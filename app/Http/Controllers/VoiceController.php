<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Station;
use App\Models\Employee;
use App\Models\Report;
use OpenAI\Laravel\Facades\OpenAI;

class VoiceController extends Controller
{
    private $apiKey;
    private $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.uzbekvoice.api_key');
        $this->baseUrl = config('services.uzbekvoice.base_url', 'https://uzbekvoice.ai');
    }
    
    public function processVoice(Request $request)
    {
        try {
            $audioFile = $request->file('audio');
            $audioHash = md5_file($audioFile->getRealPath());
            $transcribedText = $this->getCachedTranscription($audioHash, $audioFile);
            $intent = $this->detectIntent($transcribedText);
            $responseText = $this->getResponse($intent);

            $audioResult = $this->textToSpeech($responseText);
            
            return response()->json([
                'success' => true,
                'transcribed_text' => $transcribedText,
                'intent' => $intent,
                'response_text' => $responseText,
                'audio' => $audioResult
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    protected function textToSpeech($text)
    {
        $endpoint = $this->baseUrl . '/api/v1/tts';
        $payload = [
            'text' => $text,
            'model' => '',
            'blocking' => 'true',
        ];
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, $payload);

            if (!$response->successful()) {
                \Log::error('TTS failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            $remoteUrl = $data['result']['url'] ?? null;

            if (!$remoteUrl) {
                \Log::error('TTS response missing URL');
                return null;
            }
            
            try {
                $fileData = Http::get($remoteUrl)->body();
                $fileName = 'tts_' . md5($text . microtime(true)) . '.wav';

                Storage::put("public/tts/{$fileName}", $fileData);

                return [
                    'remote_url' => $remoteUrl,
                    'local_url' => Storage::url("tts/{$fileName}")
                ];
            } catch (\Exception $e) {
                return [
                    'remote_url' => $remoteUrl,
                    'local_url' => null
                ];
            }
        }
        catch (\Exception $e) {
            \Log::error('TTS request exception', ['err' => $e->getMessage()]);
            return null;
        }
    }
    
    private function getCachedTranscription($audioHash, $audioFile)
    {
        $cacheKey = 'stt_' . $audioHash;
        return Cache::remember($cacheKey, 60 * 24 * 30, function() use ($audioFile) {
            return $this->speechToText($audioFile);
        });
    }
    
    private function speechToText($audioFile)
    {
        $audioContent = file_get_contents($audioFile->getRealPath());
        
        $response = Http::timeout(60)
            ->withHeaders(['Authorization' => $this->apiKey])
            ->attach('file', $audioContent, $audioFile->getClientOriginalName())
            ->post($this->baseUrl . '/api/v1/stt', [
                'language' => 'uz',
                'model' => 'davron-neutral',
                'blocking' => 'true'
            ]);
        
        if (!$response->successful()) {
            throw new \Exception('STT API xatosi: ' . $response->status());
        }
        
        $data = $response->json();
        return trim($data['result']['text'] ?? $data['text'] ?? '');
    }
    
    private function detectIntent(string $text): array
    {
        if (empty(trim($text))) {
            return ['type' => 'unknown', 'key' => null, 'id' => null];
        }

        $stations = Station::select('id', 'title')->get();
        $employees = Employee::select('id', 'full_name')->get();

        $stationList = $stations->map(fn($s) => "{$s->id}:{$s->title}")->implode(', ');
        $employeeList = $employees->map(fn($e) => "{$e->id}:{$e->full_name}")->implode(', ');

        $prompt = "Matn: \"{$text}\"\n\n";
        $prompt .= "Mavjud stantsiyalar (id:nom): {$stationList}\n";
        $prompt .= "Mavjud xodimlar (id:nom): {$employeeList}\n\n";
        $prompt .= "JSON formatda javob ber:\n";
        $prompt .= "{\n";
        $prompt .= '  "type": "station" | "employee" | "unknown",'."\n";
        $prompt .= '  "id": topilgan_id'."\n";
        $prompt .= "}\n\n";
        $prompt .= "Eslatma: Stantsiya turlari (big_station, small_station, bridge, enterprise) barchasi 'station' type hisoblanadi.\n";
        $prompt .= "Faqat JSON qayt.";

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Faqat JSON formatida javob ber.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.3,
            ]);

            $content = trim($response->choices[0]->message->content ?? '{}');
            $content = preg_replace('/```json|```/i', '', $content);
            $content = trim($content);
            
            $parsed = json_decode($content, true);
            
            if (!$parsed || !isset($parsed['type'])) {
                throw new \Exception('Invalid JSON');
            }

            return [
                'type' => $parsed['type'],
                'id' => $parsed['id'] ?? null
            ];

        } catch (\Exception $e) {
            return ['type' => 'unknown', 'id' => null];
        }
    }

    private function getResponse(array $intent): string
    {
        $type = $intent['type'];
        $id = $intent['id'];

        if ($type === 'station' && $id) {
            return $this->getStationInfo($id);
        }

        if ($type === 'employee' && $id) {
            return $this->getEmployeeInfo($id);
        }

        return 'Tushunmadim. Iltimos stantsiya yoki xodim nomini aniq ayting.';
    }
    
    private function getStationInfo($stationId): string
    {
        $station = Station::with('employees', 'reports')->find($stationId);

        if (!$station) {
            return 'Stantsiya topilmadi.';
        }

        // details JSON bo'lishi mumkin — qulay olish uchun data_get ishlatamiz
        $details = is_array($station->details) ? $station->details : (is_null($station->details) ? [] : (array) $station->details);

        $title = $station->title ?? 'Nom berilmagan';
        $infoParts = [];

        // 1) Sarlavha
        $infoParts[] = "{$title} stansiyasi";

        // 2) Klass
        if (!empty($details['station_class'])) {
            $infoParts[] = "{$details['station_class']}-klass stansiyasi bo'lib,";
        }

        // 3) Yo'llar haqida
        $trackParts = [];
        if (isset($details['receiving_tracks']) && $details['receiving_tracks'] !== '') {
            $trackParts[] = "{$details['receiving_tracks']} ta qabul qilib jo'natuvchi yo'li";
        }
        if (isset($details['traction_tracks']) && $details['traction_tracks'] !== '') {
            $trackParts[] = "{$details['traction_tracks']} ta traksion yo'li";
        }
        if (!empty($trackParts)) {
            $infoParts[] = implode(', ', $trackParts) . " mavjud.";
        } else {
            $infoParts[] = "Yo'llar haqida ma'lumot mavjud emas.";
        }

        // 4) Xodimlar soni va jinslar bo'yicha taqsimot
        // employees relation bilan preloaded bo'lsa ishlatamiz, aks holda query orqali olish
        try {
            $totalEmployees = $station->employees ? $station->employees->count() : $station->employees()->count();
        } catch (\Throwable $e) {
            $totalEmployees = $station->employees()->count();
        }

        if ($totalEmployees > 0) {
            // jinslar bo'yicha sanash — agar relation preloaded bo'lsa collectiondan oling, aks holda query
            if ($station->relationLoaded('employees')) {
                $maleCount = $station->employees->where('sex', 'male')->count();
                $femaleCount = $station->employees->where('sex', 'female')->count();
                $unknownSex = $totalEmployees - ($maleCount + $femaleCount);
            } else {
                $maleCount = $station->employees()->where('sex', 'male')->count();
                $femaleCount = $station->employees()->where('sex', 'female')->count();
                $unknownSex = $totalEmployees - ($maleCount + $femaleCount);
            }

            // Tuzilishi: "Stansiyada jami X ta xodim ishlaydi. Ularning Y tasi erkak, Z tasi ayol."
            $empText = "Stansiyada jami {$totalEmployees} ta xodim ishlaydi.";
            if ($maleCount > 0 || $femaleCount > 0) {
                $sexParts = [];
                if ($maleCount > 0) $sexParts[] = "{$maleCount} tasi erkak";
                if ($femaleCount > 0) $sexParts[] = "{$femaleCount} tasi ayol";
                if ($unknownSex > 0) $sexParts[] = "{$unknownSex} tasi jins ma'lum emas";
                $empText .= " Ularning " . implode(', ', $sexParts) . ".";
            } else {
                // hech qanday sex qiymati topilmagan
                $empText .= " Xodimlarning jinslari haqida ma'lumot mavjud emas.";
            }

            $infoParts[] = $empText;
        } else {
            $infoParts[] = "Stansiyada hozircha xodimlar ro'yxati bo'sh.";
        }

        // 5) Joriy oy uchun rejalashtirilgan hisobotlar (yuk tushurilishi, yuk ortilishi, pul tushumi)
        $now = \Carbon\Carbon::now();
        $year = $now->year;
        $month = $now->month;

        // reports relationship mavjud bo'lsa collectiondan filtrlash yoki query ishlatish
        $reportsQuery = null;
        if ($station->relationLoaded('reports')) {
            $reports = collect($station->reports)->filter(function ($r) use ($year, $month) {
                // r->date bo'lishi shart; mos kelmasa filtrdan tashla
                if (empty($r->date)) return false;
                try {
                    $d = \Carbon\Carbon::parse($r->date);
                } catch (\Throwable $e) {
                    return false;
                }
                return $d->year === $year && $d->month === $month;
            });
        } else {
            // relationship query — 'date' ustuni mavjud deb taxmin qilamiz
            $reports = $station->reports()
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();
        }

        // Sum values (planned_value may be numeric or string)
        $yukOrtish = (float) $reports->where('type', 'yuk_ortilishi')->sum(function ($r) {
            return (float) data_get($r, 'planned_value', 0);
        });
        $yukTushirish = (float) $reports->where('type', 'yuk_tushurilishi')->sum(function ($r) {
            return (float) data_get($r, 'planned_value', 0);
        });
        $pulTushumi = (float) $reports->where('type', 'pul_tushumi')->sum(function ($r) {
            return (float) data_get($r, 'planned_value', 0);
        });

        // Agar hech qanday reja topilmasa
        if ($yukOrtish > 0 || $yukTushirish > 0 || $pulTushumi > 0) {
            $reportParts = [];
            if ($yukTushirish > 0) $reportParts[] = "yuk tushurilishi rejasi {$yukTushirish} vagon";
            if ($yukOrtish > 0) $reportParts[] = "oylik yuk ortilishi {$yukOrtish} vagon";
            if ($pulTushumi > 0) $reportParts[] = "oylik pul tushumi {$pulTushumi} so'm";
            $infoParts[] = "Ushbu oy uchun rejalar: " . implode(', ', $reportParts) . ".";
        } else {
            $infoParts[] = "Ushbu oy uchun rejalashtirilgan yuk/pul ma'lumotlari mavjud emas.";
        }

        // Birlashtirib qaytarish
        $info = implode(' ', array_filter($infoParts));
        return trim($info);
    }      
    
    private function getEmployeeInfo($employeeId): string
    {
        $employee = Employee::with('station')->find($employeeId);

        if (!$employee) {
            return 'Xodim topilmadi.';
        }

        // Mapping
        $documentTypeMap = [
            'main'       => "Asosiy ish",
            'additional' => "Qo'shimcha ish",
        ];

        $categoryMap = [
            'higher'            => "Oliy",
            'secondary_special' => "O'rta maxsus",
            'secondary'         => "O'rta",
        ];

        $parts = [];

        //------------------------------
        // 1) Xodim haqida
        //------------------------------
        $parts[] = "{$employee->full_name} haqida ma'lumot.";

        //------------------------------
        // 2) Ishga kirgan sanasi → "2025-yil Noyabr oyidan beri"
        //------------------------------
        if ($employee->joined_at) {
            $joined = $employee->joined_at instanceof \Carbon\Carbon
                ? $employee->joined_at
                : \Carbon\Carbon::parse($employee->joined_at);

            $year  = $joined->format('Y');
            $month = $joined->locale('uz')->monthName; // Masalan: noyabr, dekabr, fevral

            $workPeriodText = "{$year}-yilning {$month} oyidan beri";

            // Stansiya + role
            $station = $employee->station->title ?? "stansiyada";

            $role = $employee->role ?? "ishchi";

            $parts[] = "U {$workPeriodText} {$station} stansiyasida {$role} bo'lib ishlaydi.";
        }

        //------------------------------
        // 3) Yoshi
        //------------------------------
        if ($employee->birth_date) {
            try {
                $age = $employee->birth_date->age;
                $parts[] = "Yoshi {$age} daa.";
            } catch (\Throwable $e) {
                $parts[] = "Yoshi ko‘rsatilmagan.";
            }
        }

        //------------------------------
        // 4) Ma'lumoti (category)
        //------------------------------
        if ($employee->category) {
            $cat = $categoryMap[$employee->category] ?? $employee->category;
            $parts[] = "Ma'lumoti {$cat}.";
        }

        //------------------------------
        // 5) Ish turi (document_type)
        //------------------------------
        if ($employee->document_type) {
            $doc = $documentTypeMap[$employee->document_type] ?? $employee->document_type;
            $parts[] = "Ish turi {$doc}.";
        }

        return implode(' ', $parts);
    }
}