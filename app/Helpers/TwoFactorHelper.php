<?php

namespace App\Helpers;

use App\Mail\TransactionEmailVerification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class TwoFactorHelper
{
    public static function sendEmailVerificationCode($user, $amount = null)
    {
        $code = rand(100000, 999999);

        Cache::put('withdraw_2fa_' . $user->id, $code, now()->addMinutes(10));

        Mail::to($user->email)->send(new TransactionEmailVerification($code, $amount));
    }
}
