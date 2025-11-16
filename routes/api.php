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

Route::post('/voice/process', [VoiceController::class, 'processVoice']);

Route::get('/tts/status/{taskId}', [VoiceController::class, 'getTtsStatus'])
    ->where('taskId', '.*');

Route::get('/tts/audio/{hash}', [VoiceController::class, 'downloadCachedAudio'])
    ->name('tts.audio');