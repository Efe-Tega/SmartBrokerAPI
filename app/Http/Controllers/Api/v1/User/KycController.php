<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\KycRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function uploadKyc(Request $request)
    {
        $request->validate([
            'dob' => 'required',
            'address' => 'required',
            'id_type' => 'required',
            'id_number' => 'required',
            // 'id_front_path' => 'required',
            // 'id_back_path' => 'required',
            // 'selfi_path' => 'required',
            // 'poa_path' => 'required',
        ]);

        $user = Auth::user();
        $kycData = KycRequest::where('user_id', $user->id)->first();

        if ($kycData) {
            $kycData->dob = $request->dob;
            $kycData->address = $request->address;
            $kycData->id_type = $request->id_type;
            $kycData->id_number = $request->id_number;
            // $kycData->id_front_path
            // $kycData->id_back_path
            // $kycData->selfi_path
            // $kycData->selfi_path
            $kycData->save();
        } else {
            KycRequest::create([
                'user_id' => $user->id,
                'dob' => $request->dob,
                'address' => $request->address,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'created_at' => Carbon::now()
            ]);
        }

        return response()->json([
            'message' => 'Kyc Request submitted'
        ]);
    }
}
