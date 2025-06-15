<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TotpController extends Controller
{
    public function showQrCode()
    {
        $otpauthurl = 'otpauth://totp/Smart%20Broker:user%40gmail.com?secret=NOYLHYMC7FVFH3P7&issuer=Smart%20Broker&algorithm=SHA1&digits=6&period=30';

        // Generate QR code data
        // $qrCode = QrCode::size(250)->generate($otpauthurl);

        // return view('welcome', compact('qrCode'));
    }
}
