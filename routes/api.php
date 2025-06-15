<?php

use App\Http\Controllers\Api\v1\Admin\InvestmentManagement;
use App\Http\Controllers\Api\v1\Admin\KycManagement;
use App\Http\Controllers\Api\v1\Admin\TransactionManagement;
use App\Http\Controllers\Api\v1\Auth\AdminController;
use App\Http\Controllers\Api\v1\Auth\EmailAuthController;
use App\Http\Controllers\Api\v1\Auth\TotpController;
use App\Http\Controllers\Api\v1\Auth\UserController;
use App\Http\Controllers\Api\v1\User\InvestmentController;
use App\Http\Controllers\Api\v1\User\KycController;
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

// Admin Auth Route
Route::middleware(['auth:sanctum', 'ensure.admin'])->group(function () {
    Route::post('/admin/logout', [AdminController::class, 'logout']);

    Route::controller(TransactionManagement::class)->group(function () {
        Route::get('/transactions', 'allTransactions');
        Route::get('/deposit-transactions', 'depositTransactions');
        Route::get('/withdraw-transactions', 'withdrawTransactions');
        Route::get('/transaction/details/{id}', 'transactionDetails');
        Route::delete('/delete-transaction/{id}', 'deleteTransaction');

        Route::post('/update/transaction', 'updateTransaction');
    });

    // Investment Route
    Route::controller(InvestmentManagement::class)->group(function () {
        Route::get('/all/investment-plan', 'allInvestmentPlan');
        Route::get('/edit/investment-plan/{id}', 'editInvestmentPlan');
        Route::delete('/delete/investment-plan/{id}', 'deleteInvestmentPlan');

        Route::post('/add/investment-plan', 'addInvestmentPlan');
        Route::post('/update/investment-plan', 'updateInvestmentPlan');
    });

    // KYC Requests
    Route::controller(KycManagement::class)->group(function () {
        Route::get('/kyc-request/details/{id}', 'kycDetails');

        Route::post('/kyc-requests', 'kycRequests');
        Route::post('/update-kyc', 'updateKyc');
    });
});


// User Authentication Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('/logout',  'logout');
        Route::post('/change/password', 'changePassword');
    });

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

    // Profile Settings
    Route::controller(UserProfileController::class)->group(function () {
        Route::post('/update-profile', 'updateProfile');
    });

    // KYC 
    Route::controller(KycController::class)->group(function () {
        Route::post('/upload-kyc', 'uploadKyc');
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
