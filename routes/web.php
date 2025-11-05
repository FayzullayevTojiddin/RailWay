<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MapController;

Route::get('/employees/{id}/download', [EmployeeController::class, 'download'])
    ->middleware('auth')
    ->name('employees.download');

Route::get('/map', [MapController::class, 'index'])->name('map.index');

Route::prefix('api')->group(function () {
    Route::get('/stations', [MapController::class, 'getStations'])->name('api.stations');
    Route::get('/stations/{id}', [MapController::class, 'show'])->name('api.stations.show');
});