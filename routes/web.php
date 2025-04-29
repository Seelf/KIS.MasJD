<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KISAPI;
use App\Services\KisMeService;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/kis-me/devices', [KISAPI::class, 'getDevices'])->name('kis-me.devices');
    Route::post('/kis-me/triggers/{triggerId}', [KISAPI::class, 'triggerDevice']);
    Route::get('/kis-me/test', function (KisMeService $kisMeService) {
        return $kisMeService->testApiConnection();
    });
    Route::post('/set-led', [KISAPI::class, 'setLed'])->name('set.led');
});

require __DIR__.'/auth.php';
