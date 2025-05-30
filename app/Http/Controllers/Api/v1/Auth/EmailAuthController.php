<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorEmailCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailAuthController extends Controller
{
    public function setupEmail()
    {
        $user = auth()->user();

        $code = rand(100000, 999999);
        cache()->put("email_otp:{$user->id}", $code, now()->addMinutes(10));

        Mail::to($user->email)->send(new TwoFactorEmailCode($code));

        $user->two_factor_type = 'email';
        $user->save();

        return response()->json(["message" => "Verification code sent to email"]);
    }

    public function enableEmail(Request $request)
    {

        $request->validate(['code' => 'required|digits:6']);
        $user = auth()->user();

        $cached = cache()->get("email_otp:{$user->id}");

        if (!$cached || $cached != $request->code) {
            return response()->json(['message' => 'Invalid or expired code'], 400);
        }

        $user->two_factor_enabled = true;
        $user->save();
        cache()->forget("email_otp:{$user->id}");

        return response()->json(["message" => "Email-based 2FA enabled"]);
    }
}
