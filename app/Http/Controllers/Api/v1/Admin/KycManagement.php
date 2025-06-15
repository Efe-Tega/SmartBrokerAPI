<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycRequest;
use Illuminate\Http\Request;

class KycManagement extends Controller
{
    public function kycRequests(Request $request)
    {
        $kycRequests = KycRequest::all();

        return response()->json(['data' => $kycRequests]);
    }

    public function kycDetails($id)
    {
        $kycDetails = KycRequest::findOrFail($id);

        return response()->json(['data' => $kycDetails]);
    }

    public function updateKyc(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required',
        ]);

        $id = $request->id;

        $kycRequest = KycRequest::findOrFail($id)->update([
            'status' => $request->status
        ]);

        return response()->json(['message' => "Kyc updated"]);
    }
}
