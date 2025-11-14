<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Station;
use App\Models\Employee;

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
            
            if (!$audioFile) {
                return response()->json([
                    'success' => false,
                    'error' => 'Audio topilmadi'
                ], 400);
            }
            
            // Audio faylni hash qilib keshlash uchun
            $audioHash = md5_file($audioFile->getRealPath());
            $transcribedText = $this->getCachedTranscription($audioHash, $audioFile);
            
            $response = $this->processIntent($transcribedText);
            
            $taskId = $this->getCachedTtsAudio($response);
            
            return response()->json([
                'success' => true,
                'transcribed_text' => $transcribedText,
                'response' => $response,
                'task_id' => $taskId,
                'timestamp' => now()->toIso8601String(),
                'from_cache' => true
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Audio faylni matnга aylantirish (kesh bilan)
     */
    private function getCachedTranscription($audioHash, $audioFile)
    {
        $cacheKey = 'stt_' . $audioHash;
        
        // Keshdan tekshirish
        $cached = Cache::get($cacheKey);
        if ($cached) {
            \Log::info("STT keshdan olindi: {$audioHash}");
            return $cached;
        }
        
        // Keshda yo'q bo'lsa, API orqali olish
        $transcription = $this->speechToText($audioFile);
        
        // Keshga saqlash (30 kun)
        Cache::put($cacheKey, $transcription, 60 * 24 * 30);
        
        \Log::info("STT APIdan olindi va keshlandi: {$audioHash}");
        
        return $transcription;
    }
    
    /**
     * Matnni ovozга aylantirish (kesh bilan)
     */
    private function getCachedTtsAudio($text)
    {
        $textHash = md5(trim($text));
        $cacheKey = 'tts_' . $textHash;
        
        // Keshdan tekshirish
        $cached = Cache::get($cacheKey);
        if ($cached) {
            \Log::info("TTS keshdan olindi: {$textHash}");
            
            // Agar audio URL mavjud bo'lsa
            if (isset($cached['audio_url'])) {
                return $cacheKey;
            }
            
            // Agar lokal fayl mavjud bo'lsa
            if (isset($cached['local_path']) && Storage::exists($cached['local_path'])) {
                return $cacheKey;
            }
        }
        
        // Keshda yo'q bo'lsa, API orqali yaratish
        $taskId = $this->createTtsTask($text, $textHash);
        
        \Log::info("TTS APIdan olindi va keshlandi: {$textHash}");
        
        return $taskId;
    }
    
    private function speechToText($audioFile)
    {
        try {
            $audioContent = file_get_contents($audioFile->getRealPath());
            
            $response = Http::timeout(120)
                ->connectTimeout(30)
                ->withHeaders(['Authorization' => $this->apiKey])
                ->attach('file', $audioContent, $audioFile->getClientOriginalName())
                ->post($this->baseUrl . '/api/v1/stt', [
                    'return_offsets' => false,
                    'run_diarization' => false,
                    'language' => 'uz',
                    'blocking' => 'true'
                ]);
            
            if (!$response->successful()) {
                throw new \Exception('STT API xatosi: ' . $response->status() . ' - ' . $response->body());
            }
            
            $data = $response->json();
            
            if (isset($data['result']['text'])) {
                return trim($data['result']['text']);
            }
            
            if (isset($data['text'])) {
                return trim($data['text']);
            }
            
            if (isset($data['id'])) {
                return $this->waitForSttResult($data['id']);
            }
            
            throw new \Exception('STT natijasida matn topilmadi: ' . json_encode($data));
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    private function waitForSttResult($taskId, $maxAttempts = 60)
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep(1);
            
            try {
                $response = Http::timeout(30)
                    ->withHeaders(['Authorization' => $this->apiKey])
                    ->get($this->baseUrl . '/api/v1/tasks', ['id' => $taskId]);
                
                if (!$response->successful()) {
                    continue;
                }
                
                $data = $response->json();
                $state = $data['state'] ?? $data['status'] ?? null;
                
                if ($state === 'SUCCESS' || $state === 'COMPLETED') {
                    $text = $data['result']['text'] ?? $data['text'] ?? '';
                    
                    if (!empty($text)) {
                        return trim($text);
                    }
                }
                
                if ($state === 'FAILED' || $state === 'ERROR') {
                    throw new \Exception('STT task failed: ' . ($data['error'] ?? 'Unknown'));
                }
                
            } catch (\Exception $e) {
                // Continue polling
            }
        }
        
        throw new \Exception('STT timeout - ' . $maxAttempts . ' soniya kutildi');
    }
    
    private function processIntent($text)
    {
        $originalText = $text;
        $text = strtolower(trim($text));
        
        if (empty($text)) {
            return 'Tushunmadim. Iltimos qaytadan gapiring.';
        }
        
        // Salom
        if (strpos($text, 'salom') !== false || strpos($text, 'hello') !== false) {
            return 'Assalomu alaykum! Men sizga stantsiyalar va xodimlar haqida ma\'lumot beraman.';
        }
        
        // Stantsiya intent
        $hasStationWord = strpos($text, 'stantsiya') !== false || 
                         strpos($text, 'stansiya') !== false ||
                         strpos($text, 'stansiyasi') !== false;
        
        if ($hasStationWord) {
            return $this->handleStationQuery($text);
        }
        
        // Xodim intent
        $hasEmployeeWord = strpos($text, 'xodim') !== false || 
                          strpos($text, 'hodim') !== false || 
                          strpos($text, 'ishchi') !== false || 
                          strpos($text, 'employee') !== false;
        
        if ($hasEmployeeWord) {
            return $this->handleEmployeeQuery($text);
        }
        
        // Stantsiya nomini to'g'ridan-to'g'ri qidirish
        $station = $this->findStationInText($text);
        if ($station) {
            return $this->getStationInfo($station);
        }
        
        // Xodim nomini to'g'ridan-to'g'ri qidirish
        $employee = $this->findEmployeeInText($text);
        if ($employee) {
            return $this->getEmployeeInfo($employee);
        }
        
        return 'Tushunmadim. Stantsiya yoki xodim nomini aniq ayting.';
    }
    
    private function handleStationQuery($text)
    {
        $station = $this->findStationInText($text);
        
        if (!$station) {
            $count = Station::count();
            return "Jami {$count} ta stantsiya mavjud. Qaysi stantsiya haqida ma'lumot kerak?";
        }
        
        return $this->getStationInfo($station);
    }
    
    private function handleEmployeeQuery($text)
    {
        $employee = $this->findEmployeeInText($text);
        
        if (!$employee) {
            $count = Employee::count();
            return "Jami {$count} nafar xodim ishlaydi. Qaysi xodim haqida ma'lumot kerak?";
        }
        
        return $this->getEmployeeInfo($employee);
    }
    
    private function findStationInText($text)
    {
        $stations = Station::all();
        
        // 1. To'liq nom bilan
        foreach ($stations as $station) {
            $stationName = strtolower($station->title);
            if (stripos($text, $stationName) !== false) {
                return $station;
            }
        }
        
        // 2. Asosiy so'z bilan (3+ harf)
        foreach ($stations as $station) {
            $stationName = strtolower($station->title);
            $words = explode(' ', $stationName);
            
            foreach ($words as $word) {
                if (strlen($word) > 3 && stripos($text, $word) !== false) {
                    return $station;
                }
            }
        }
        
        // 3. O'xshash nom (fuzzy) - harflarni solishtirish
        foreach ($stations as $station) {
            $stationName = strtolower($station->title);
            $stationWords = explode(' ', $stationName);
            $textWords = explode(' ', $text);
            
            foreach ($stationWords as $stationWord) {
                if (strlen($stationWord) < 4) continue;
                
                foreach ($textWords as $textWord) {
                    if (strlen($textWord) < 3) continue;
                    
                    // O'xshashlik (70%+)
                    similar_text($stationWord, $textWord, $percent);
                    if ($percent >= 70) {
                        return $station;
                    }
                }
            }
        }
        
        return null;
    }
    
    private function findEmployeeInText($text)
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            $fullName = strtolower($employee->first_name . ' ' . $employee->last_name);
            $firstName = strtolower($employee->first_name);
            $lastName = strtolower($employee->last_name);
            
            if (stripos($text, $fullName) !== false || 
                stripos($text, $firstName) !== false || 
                stripos($text, $lastName) !== false) {
                return $employee;
            }
        }
        
        return null;
    }
    
    private function getStationInfo($station)
    {
        // Asosiy ma'lumot
        $info = "{$station->title} stantsiyasi haqida ma'lumot. ";
        
        // Tavsif
        if ($station->description) {
            $info .= $station->description . ". ";
        }
        
        // Xodimlar
        $employeeCount = $station->employees()->count();
        if ($employeeCount > 0) {
            $info .= "Ushbu stantsiyada {$employeeCount} nafar xodim ishlaydi. ";
            
            $femaleCount = $station->employees()->where('sex', 'female')->count();
            $maleCount = $station->employees()->where('sex', 'male')->count();
            
            if ($femaleCount > 0 && $maleCount > 0) {
                $info .= "Ulardan {$femaleCount} nafari ayol, {$maleCount} nafari erkak xodimlar. ";
            } elseif ($femaleCount > 0) {
                $info .= "Ularning barchasi ayol xodimlar. ";
            } elseif ($maleCount > 0) {
                $info .= "Ularning barchasi erkak xodimlar. ";
            }
        } else {
            $info .= "Hozircha xodimlar ma'lumoti kiritilmagan. ";
        }
        
        // Yo'llar
        $branchCount = $station->branchRailways()->count();
        $mainCount = $station->mainRailways()->count();
        $totalTracks = $branchCount + $mainCount;
        
        if ($totalTracks > 0) {
            $info .= "Mavjud yo'llarning soni {$totalTracks} ta. ";
            
            if ($branchCount > 0 && $mainCount > 0) {
                $info .= "Ulardan {$branchCount} tasi shaxobcha yo'llari, {$mainCount} tasi esa asosiy temir yo'ldir. ";
            } elseif ($branchCount > 0) {
                $info .= "Ularning barchasi shaxobcha yo'llari. ";
            } elseif ($mainCount > 0) {
                $info .= "Ularning barchasi asosiy temir yo'llar. ";
            }
        } else {
            $info .= "Yo'llar ma'lumoti hali kiritilmagan. ";
        }
        
        // Yer maydoni (Cadastre)
        $cadastres = $station->cadastres;
        if ($cadastres->count() > 0) {
            $totalArea = $cadastres->sum('area');
            
            if ($totalArea > 0) {
                if ($totalArea >= 10000) {
                    $hectares = round($totalArea / 10000, 2);
                    $info .= "Umumiy yer maydoni {$hectares} gektar. ";
                } else {
                    $info .= "Umumiy yer maydoni {$totalArea} kvadrat metr. ";
                }
            }
        }
        
        return $info;
    }
    
    private function getEmployeeInfo($employee)
    {
        $info = "{$employee->full_name} haqida ma'lumot. ";
        
        if ($employee->position) {
            $info .= "Lavozimi: {$employee->position}. ";
        }
        
        if ($employee->station) {
            $info .= "{$employee->station->title} stantsiyasida ishlaydi. ";
        }
        
        $sex = $employee->sex === 'female' ? 'Ayol' : 'Erkak';
        $info .= "Jinsi: {$sex}. ";
        
        if ($employee->birth_date) {
            $age = now()->diffInYears($employee->birth_date);
            $info .= "Yoshi: {$age}. ";
        }
        
        return $info;
    }
    
    private function createTtsTask($text, $textHash)
    {
        try {
            $response = Http::timeout(120)
                ->connectTimeout(30)
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/api/v1/tts', [
                    'text' => $text,
                    'model' => 'davron-neutral',
                    'blocking' => 'true'
                ]);
            
            if (!$response->successful()) {
                throw new \Exception('TTS API xatosi: ' . $response->status());
            }
            
            $data = $response->json();
            $taskId = $data['id'] ?? null;
            
            if (!$taskId) {
                throw new \Exception('TTS task ID topilmadi');
            }
            
            $cacheKey = 'tts_' . $textHash;
            
            if (isset($data['result']['url']) && $data['status'] === 'SUCCESS') {
                // Audio URL ni keshga saqlash
                $audioUrl = $data['result']['url'];
                
                // Audio faylni yuklab olish va lokal saqlash
                $localPath = $this->downloadAndStoreAudio($audioUrl, $textHash);
                
                Cache::put($cacheKey, [
                    'status' => 'SUCCESS',
                    'audio_url' => $audioUrl,
                    'local_path' => $localPath,
                    'original_task_id' => $taskId,
                    'created_at' => now()->toIso8601String()
                ], 60 * 24 * 30); // 30 kun
            } else {
                Cache::put($cacheKey, [
                    'status' => 'PENDING',
                    'original_task_id' => $taskId,
                    'created_at' => now()->toIso8601String()
                ], 600);
                
                dispatch(function() use ($taskId, $cacheKey, $textHash) {
                    $this->pollTtsResult($taskId, $cacheKey, $textHash);
                })->afterResponse();
            }
            
            return $cacheKey;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Audio faylni yuklab olish va lokal saqlash
     */
    private function downloadAndStoreAudio($url, $hash)
    {
        try {
            $audioContent = Http::timeout(60)->get($url)->body();
            
            $filename = 'tts_audio/' . $hash . '.mp3';
            Storage::put($filename, $audioContent);
            
            return $filename;
        } catch (\Exception $e) {
            \Log::error("Audio yuklab olishda xatolik: " . $e->getMessage());
            return null;
        }
    }
    
    private function pollTtsResult($taskId, $cacheKey, $textHash)
    {
        $maxAttempts = 60;
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep(1);
            
            try {
                $response = Http::timeout(30)
                    ->withHeaders(['Authorization' => $this->apiKey])
                    ->get($this->baseUrl . '/api/v1/tasks', ['id' => $taskId]);
                
                if (!$response->successful()) {
                    continue;
                }
                
                $data = $response->json();
                $status = $data['status'] ?? $data['state'] ?? null;
                
                if ($status === 'SUCCESS' || $status === 'COMPLETED') {
                    $audioUrl = $data['result']['url'] ?? null;
                    
                    if ($audioUrl) {
                        // Audio faylni yuklab olish va lokal saqlash
                        $localPath = $this->downloadAndStoreAudio($audioUrl, $textHash);
                        
                        Cache::put($cacheKey, [
                            'status' => 'SUCCESS',
                            'audio_url' => $audioUrl,
                            'local_path' => $localPath,
                            'updated_at' => now()->toIso8601String()
                        ], 60 * 24 * 30); // 30 kun
                        
                        return;
                    }
                }
                
                if ($status === 'FAILED' || $status === 'ERROR') {
                    Cache::put($cacheKey, [
                        'status' => 'FAILED',
                        'error' => $data['error'] ?? 'Unknown'
                    ], 600);
                    
                    return;
                }
                
            } catch (\Exception $e) {
                // Continue polling
            }
        }
        
        Cache::put($cacheKey, ['status' => 'TIMEOUT'], 600);
    }
    
    public function getTtsStatus($taskId)
    {
        $status = Cache::get($taskId);
        
        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Task topilmadi'
            ], 404);
        }
        
        // Agar lokal fayl mavjud bo'lsa, uni yuborish
        if (isset($status['local_path']) && Storage::exists($status['local_path'])) {
            $hash = basename($status['local_path'], '.mp3');
            return response()->json([
                'success' => true,
                'task_id' => $taskId,
                'status' => $status['status'] ?? 'PENDING',
                'audio_url' => url('/api/tts/audio/' . $hash),
                'from_cache' => true,
                'created_at' => $status['created_at'] ?? null,
                'updated_at' => $status['updated_at'] ?? null
            ]);
        }
        
        return response()->json([
            'success' => true,
            'task_id' => $taskId,
            'status' => $status['status'] ?? 'PENDING',
            'audio_url' => $status['audio_url'] ?? null,
            'from_cache' => false,
            'created_at' => $status['created_at'] ?? null,
            'updated_at' => $status['updated_at'] ?? null
        ]);
    }
    
    /**
     * Keshlangan audio faylni yuklash
     */
    public function downloadCachedAudio($hash)
    {
        $filename = 'tts_audio/' . $hash . '.mp3';
        
        if (!Storage::exists($filename)) {
            abort(404, 'Audio fayl topilmadi');
        }
        
        return Storage::download($filename, $hash . '.mp3', [
            'Content-Type' => 'audio/mpeg'
        ]);
    }
    
    /**
     * Keshni tozalash (ixtiyoriy - admin uchun)
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all'); // 'stt', 'tts', 'all'
        
        if ($type === 'stt' || $type === 'all') {
            Cache::flush(); // yoki Cache::tags(['stt'])->flush()
        }
        
        if ($type === 'tts' || $type === 'all') {
            // TTS audio fayllarni o'chirish
            Storage::deleteDirectory('tts_audio');
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Kesh tozalandi'
        ]);
    }
}