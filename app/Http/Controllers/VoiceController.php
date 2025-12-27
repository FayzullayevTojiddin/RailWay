<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Station;
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
            $intent = $this->detectEntity($transcribedText);
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
            'model' => 'lola',
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
                'model' => 'lola',
                'blocking' => 'true'
            ]);
        
        if (!$response->successful()) {
            throw new \Exception('STT API xatosi: ' . $response->status());
        }
        
        $data = $response->json();
        return trim($data['result']['text'] ?? $data['text'] ?? '');
    }
    
    private function detectEntity(string $text): ?array
{
    $text = trim($text);

    if ($text === '') {
        return null;
    }

    $entities = Station::query()
        ->select('id', 'title')
        ->get()
        ->map(fn ($s) => "{$s->id}:{$s->title}")
        ->implode(', ');

    $prompt = <<<PROMPT
Matn: "{$text}"

Mavjud stansiyalar (id:nom):
{$entities}

Qoidalar:
- Matn ichidan stansiya nomini qidir
- Qidiruv katta-kichik harflarga bog‘liq emas
- Agar matndagi so‘z ro‘yxatdagi stansiya nomiga juda yaqin bo‘lsa
  (masalan: kichik imlo xatosi, qo‘shimcha so‘zlar, "stansiyasi" kabi),
  eng mos keladigan stansiyani tanla
- Agar moslik juda kuchsiz yoki ishonchsiz bo‘lsa — id va title null bo‘lsin
- Eng ko‘pi bilan bitta stansiyani tanla
- Taxminni haddan oshirma
- Faqat JSON qaytar

JSON:
{
  "id": number|null,
  "title": string|null
}
PROMPT;

    try {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'temperature' => 0,
            'messages' => [
                ['role' => 'system', 'content' => 'Faqat JSON qaytar.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $content = preg_replace('/```json|```/i', '', $response->choices[0]->message->content ?? '');
        $data = json_decode(trim($content), true);

        if (!isset($data['id'], $data['title'])) {
            throw new \RuntimeException();
        }

        return [
            'id'    => $data['id'] !== null ? (int) $data['id'] : null,
            'title' => $data['title'] ?? null,
        ];

    } catch (\Throwable) {
        return [
            'id'    => null,
            'title' => null,
        ];
    }
}


    private function getResponse(array $intent): string
    {
        if (empty($intent['title']) || empty($intent['id'])) {
            return "Siz so'ragan korxona yoki stansiya haqida ma'lumot topilmadi.";
        }

        return $this->getResponseWithID(
            (int) $intent['id']
        );
    }

    private function getResponseWithID(int $id): string
    {
        try {
            $data = Station::findOrFail($id);
            $ai_response = $data->ai_response;
            return $data->ai_response ?: "Hozircha ma'lumotlar mavjud emas";
        } catch(Exception $error) {
            return "Tizimda xatolik. Kechirasiz sizga hozir javob bera olmayman";
        }
    }
}