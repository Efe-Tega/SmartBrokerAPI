<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OTPHP\TOTP;
use PragmaRX\Google2FA\Google2FA;

class TotpController extends Controller
{
    public function setupTotp()
    {
        $user = Auth::user();

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_type = 'totp';
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl
        ]);
    }

    public function enableTotp(Request $request)
    {
        $request->validate(['code' => 'required']);

        $user = auth()->user();
        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return response()->json(['message' => 'Invalid code'], 400);
        }

        $user->two_factor_enabled = true;
        $user->save();

        return response()->json(['message' => '2FA enabled']);
    }
}
