<?php

use App\Http\Controllers\QRController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;

Route::get('/', [QRController::class, 'index'])->name('qr.index');
Route::post('/generate', [QRController::class, 'generate'])->name('qr.generate');
Route::post('/decode', [QRController::class, 'decode'])->name('qr.decode');
Route::get('/welcome', [QRController::class, 'welcome'])->name('qr.welcome');