<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TotpController extends Controller
{
    public function showQrCode()
    {
        $otpauthurl = 'otpauth://totp/efetega%40gmail.com?secret=76ZHJNBSCUIPKV22SSN6UG42WNVK4KSANIQFBNPQCJNMG6Q6XX3USLAJQBABOZFJ3RGUQKOWEP35TB4P63IFKFNLVY7NJADUDATMGMQ';

        // Generate QR code data
        $qrCode = QrCode::size(250)->generate($otpauthurl);

        return view('welcome', compact('qrCode'));
    }
}
