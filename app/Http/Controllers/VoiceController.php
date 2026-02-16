<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Station;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class VoiceController extends Controller
{
    public function processVoice(Request $request)
    {
        try {
            $audioFile = $request->file('audio');
            $transcribedText = $this->speechToText($audioFile);
            $intent = $this->detectEntity($transcribedText);
            $responseData = $this->getResponse($intent);
            $audioResult = $this->textToSpeech($responseData['text']);

            return response()->json([
                'success' => true,
                'transcribed_text' => $transcribedText,
                'intent' => $intent,
                'response_text' => $responseData['text'],
                'images' => $responseData['images'],
                'audio' => $audioResult,
            ]);
        } catch (\Exception $e) {
            Log::error('processVoice error', ['err' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function speechToText($audioFile): string
    {
        $extension = $audioFile->getClientOriginalExtension() ?: 'webm';
        $tempPath = storage_path('app/temp_' . uniqid() . '.' . $extension);
        copy($audioFile->getRealPath(), $tempPath);

        try {
            $response = OpenAI::audio()->transcribe([
                'model'           => 'whisper-1',
                'file'            => fopen($tempPath, 'r'),
                'response_format' => 'json',
            ]);

            $text = trim($response->text ?? '');

            if ($text === '') {
                throw new \Exception('Whisper: empty transcription result');
            }

            return $text;
        } finally {
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    protected function textToSpeech(string $text): ?array
    {
        try {
            $response = OpenAI::audio()->speech([
                'model'           => 'tts-1',
                'voice'           => 'nova',
                'input'           => $text,
                'response_format' => 'mp3',
            ]);

            $fileName = 'tts_' . md5($text . microtime(true)) . '.mp3';
            Storage::put("public/tts/{$fileName}", $response);

            return [
                'local_url' => Storage::url("tts/{$fileName}"),
            ];
        } catch (\Exception $e) {
            Log::error('TTS error', ['err' => $e->getMessage()]);
            return null;
        }
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
            ->map(fn($s) => "{$s->id}:{$s->title}")
            ->implode(', ');

        $prompt = "Текст: \"{$text}\"\n\n"
            . "Доступные станции (id:название):\n{$entities}\n\n"
            . "Правила:\n"
            . "- Текст может быть любым типом предложения (сообщение, запрос, команда, вопрос)\n"
            . "- Цель предложения не важна, нужно ТОЛЬКО определить название станции внутри текста\n"
            . "- Поиск не зависит от регистра\n"
            . "- Если в тексте встречается полное или частичное название станции\n"
            . "  (орфографическая ошибка, дополнительные слова, слово \"станция\" и т.д.),\n"
            . "  выбери наиболее подходящую станцию\n"
            . "- Если совпадение слабое или ненадёжное — id и title должны быть null\n"
            . "- Выбери только одну станцию\n"
            . "- Верни только JSON, без комментариев\n\n"
            . "JSON:\n{\n  \"id\": number|null,\n  \"title\": string|null\n}";

        try {
            $response = OpenAI::chat()->create([
                'model'       => 'gpt-4o-mini',
                'temperature' => 0,
                'messages'    => [
                    ['role' => 'system', 'content' => 'Верни только JSON.'],
                    ['role' => 'user',   'content' => $prompt],
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

    private function getResponse(?array $intent): array
    {
        if (empty($intent['title']) || empty($intent['id'])) {
            return [
                'text'   => 'К сожалению, информация по запрашиваемой станции не найдена.',
                'images' => [],
            ];
        }

        return $this->getResponseWithID((int) $intent['id']);
    }

    private function getResponseWithID(int $id): array
    {
        try {
            $station = Station::findOrFail($id);
            $images = [];

            if (is_array($station->images)) {
                $images = collect($station->images)
                    ->map(function ($path) {
                        $filename = basename($path);
                        return '/station-images/stations/' . $filename;
                    })
                    ->values()
                    ->toArray();
            }

            return [
                'text'   => $station->ai_response ?: 'Информация пока недоступна.',
                'images' => $images,
            ];
        } catch (Exception $e) {
            return [
                'text'   => 'Произошла ошибка в системе. Извините, сейчас не могу ответить.',
                'images' => [],
            ];
        }
    }
}