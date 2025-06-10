<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KISAPI;
use App\Services\KisMeService;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/save-commands', function (\Illuminate\Http\Request $request) {
    $data = $request->input('commands');
    
    // Zapisz do pliku JSON w katalogu public/data
    $jsonPath = public_path('data/commands.json');
    file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return response()->json(['status' => 'success']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/kis-me/devices', [KISAPI::class, 'getDevices'])->name('kis-me.devices');
    Route::get('/kis-me/devices2', [KISAPI::class, 'getDevices2'])->name('kis-me.devices2');
    Route::get('/kis-me/messenger', [KISAPI::class, 'getDevices3'])->name('kis-me.messenger');
    Route::post('/kis-me/triggers/{triggerId}', [KISAPI::class, 'triggerDevice']);
    Route::get('/kis-me/test', function (KisMeService $kisMeService) {
        return $kisMeService->testApiConnection();
    });
    Route::post('/set-led', [KISAPI::class, 'setLed'])->name('set.led');
    
});

require __DIR__.'/auth.php';
