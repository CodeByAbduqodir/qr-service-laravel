<?php

use App\Http\Controllers\TelegramController;

Route::post('/webhook', [TelegramController::class, 'handleWebhook']);