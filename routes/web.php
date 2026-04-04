<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MapController;
use App\Exports\AvtomobilTemplateExport;
use App\Exports\MikrosxemaTemplateExport;
use App\Imports\AvtomobilImport;
use App\Imports\MikrosxemaImport;
use Maatwebsite\Excel\Facades\Excel;

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

Route::middleware('auth')->group(function () {
    Route::get('/avtomobillar/shablon', function () {
        return Excel::download(new AvtomobilTemplateExport, 'avtomobillar_shablon.xlsx');
    })->name('avtomobillar.template');

    Route::post('/avtomobillar/import/{station}', function (\App\Models\Station $station, \Illuminate\Http\Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new AvtomobilImport($station->id), $request->file('file'));
        return back()->with('success', 'Avtomobillar muvaffaqiyatli import qilindi!');
    })->name('avtomobillar.import');

    Route::get('/mikrosxemalar/shablon', function () {
        return Excel::download(new MikrosxemaTemplateExport, 'kichik_mexanizmlar_shablon.xlsx');
    })->name('mikrosxemalar.template');

    Route::post('/mikrosxemalar/import/{station}', function (\App\Models\Station $station, \Illuminate\Http\Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new MikrosxemaImport($station->id), $request->file('file'));
        return back()->with('success', 'Kichik mexanizmlar muvaffaqiyatli import qilindi!');
    })->name('mikrosxemalar.import');
});

Route::prefix('api')->group(function () {
    Route::get('/stations', [MapController::class, 'getStations'])->name('api.stations');
    Route::get('/stations/{id}', [MapController::class, 'show'])->name('api.stations.show');
});