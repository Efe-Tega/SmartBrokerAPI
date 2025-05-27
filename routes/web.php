<?php

use App\Http\Controllers\User\TotpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/showqrcode', [TotpController::class, 'showQrCode']);
