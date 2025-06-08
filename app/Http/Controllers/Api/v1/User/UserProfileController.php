<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OTPHP\TOTP;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserProfileController extends Controller
{
    public function profileUpdate(Request $request)
    {
        $request->validate([
            "username" => 'required|string',
            "first_name" => 'required|string',
            "last_name" => 'required|string',
            "email" => 'required|email',
            "phone" => 'required'
        ]);

        $user = Auth::user();

        User::findOrFail($user->id)->update([
            "username" => $request->username,
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "email" => $request->email,
            "phone" => $request->phone,
        ]);

        return response()->json(["message" => "Profile updated!"]);
    }

    public function changePassword(Request $request)
    {

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(["message" => "old password is incorrect"]);
        }

        User::whereId($user->id)->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(["message" => "Password changed"]);
    }
}
