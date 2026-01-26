<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MapController;

Route::get('', function() {
    return redirect('/super');
});

Route::get('/station-images/{filename}', function ($filename) {
    $path = $filename;
    
    if (!Storage::disk('private')->exists($path)) {
        abort(404);
    }
    
    $file = Storage::disk('private')->get($path);
    $mimeType = Storage::disk('private')->mimeType($path);
    
    return response($file, 200)->header('Content-Type', $mimeType);
})->where('filename', '.*')->name('station.image');

Route::get('/employees/{id}/download', [EmployeeController::class, 'download'])
    ->middleware('auth')
    ->name('employees.download');

Route::get('/map', [MapController::class, 'index'])->name('map.index');

Route::prefix('api')->group(function () {
    Route::get('/stations', [MapController::class, 'getStations'])->name('api.stations');
    Route::get('/stations/{id}', [MapController::class, 'show'])->name('api.stations.show');
});