<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OTPHP\TOTP;

class TotpController extends Controller
{
    public function setupTotp()
    {
        $user = Auth::user();

        $totp = TOTP::create();
        $totp->setLabel($user->email);
        $secret = $totp->getSecret();

        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_type = 'totp';
        $user->save();

        return response()->json([
            'secret' => $secret,
            'qr_code_url' => $totp->getProvisioningUri()
        ]);
    }

    public function enableTotp(Request $request)
    {
        $request->validate(['code' => 'required']);

        $user = auth()->user();
        $secret = decrypt($user->two_factor_secret);
        $totp = TOTP::create($secret);

        if (!$totp->verify($request->code)) {
            return response()->json(['message' => 'Invalid code'], 400);
        }

        $user->two_factor_enabled = true;
        $user->save();

        return response()->json(['message' => '2FA enabled']);
    }
}
