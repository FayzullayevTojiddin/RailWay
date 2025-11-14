<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VoiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Voice AI Routes
Route::post('/voice/test', function(\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('Test endpoint hit', [
        'method' => $request->method(),
        'headers' => $request->headers->all(),
        'files' => $request->allFiles(),
        'inputs' => $request->all()
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Test endpoint works!',
        'has_file' => $request->hasFile('audio'),
        'file_info' => $request->hasFile('audio') ? [
            'size' => $request->file('audio')->getSize(),
            'mime' => $request->file('audio')->getMimeType()
        ] : null
    ]);
});
// Voice processing - audio qabul qilish va javob berish
Route::post('/voice/process', [VoiceController::class, 'processVoice']);

// TTS status tekshirish - polling uchun
Route::get('/tts/status/{taskId}', [VoiceController::class, 'getTtsStatus'])
    ->where('taskId', '.*');

// TTS audio yuklab olish
Route::get('/tts/audio/{hash}', [VoiceController::class, 'downloadCachedAudio'])
    ->name('tts.audio');