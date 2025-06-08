<?php

use App\Http\Controllers\Api\v1\Admin\InvestmentManagement;
use App\Http\Controllers\Api\v1\Auth\AdminController;
use App\Http\Controllers\Api\v1\Auth\EmailAuthController;
use App\Http\Controllers\Api\v1\Auth\TotpController;
use App\Http\Controllers\Api\v1\Auth\UserController;
use App\Http\Controllers\Api\v1\User\InvestmentController;
use App\Http\Controllers\Api\v1\User\TransactionController;
use App\Http\Controllers\Api\v1\User\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::post('/admin-login', [AdminController::class, 'login']);

// User Routes
Route::post('/user-registration', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/2fa/verify', [UserController::class, 'verify2FA']);


Route::middleware(['auth:sanctum', 'ensure.admin'])->group(function () {
    Route::post('/admin/logout', [AdminController::class, 'logout']);

    // Investment Route
    Route::controller(InvestmentManagement::class)->group(function () {
        Route::post('/add/investment-plan', 'addInvestmentPlan');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    // TOTP Authentication controller
    Route::controller(TotpController::class)->group(function () {
        Route::post('/2fa/enable-totp', 'enableTotp');
        Route::post('/2fa/setup',  'setupTotp');
    });

    // Email Authentication Controller
    Route::controller(EmailAuthController::class)->group(function () {
        Route::post('/2fa/setup-email', 'setupEmail');
        Route::post('/2fa/enable-email', 'enableEmail');
    });

    // Transactions
    Route::controller(TransactionController::class)->group(function () {
        // Ajax Request
        Route::get('/fetch/wallet-address', 'fetchWalletAddress');

        // Deposit Transactions
        Route::post('/deposit', 'cryptoDeposit');

        // Withdraw Transactions
        Route::post('/withdrawal', 'requestWithdraw');
        Route::post('/withdraw/verify', 'verifyWithdrawCode');
    });

    // Investment Controller
    Route::controller(InvestmentController::class)->group(function () {
        Route::post('/investment', 'investment');
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
