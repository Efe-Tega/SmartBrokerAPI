<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;
use Str;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => Carbon::now()
        ]);



        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->two_factor_enabled) {
            $google2fa = new Google2FA();
            $twoFaToken = encrypt($google2fa->generateSecretKey());
            cache()->put("2fa:$twoFaToken", $user->id, now()->addMinutes(5));

            if ($user->two_factor_type === 'totp') {
                return response()->json([
                    '2fa_required' => true,
                    'type' => 'totp',
                    '2fa_token' => $twoFaToken,
                    // 'redirect_url' => route('2fa.totp.form')
                ]);
            }

            if ($user->two_factor_type === 'email') {
                $code = rand(100000, 999999);
                $user->two_factor_email_code = $code;
                $user->two_factor_expires_at = now()->addMinutes(5);
                $user->save();

                // Send email
                Mail::to($user->email)->send(new \App\Mail\TwoFactorEmailCode($code));

                return response()->json([
                    '2fa_required' => true,
                    'type' => $user->two_factor_type,
                    '2fa_token' => $twoFaToken
                ]);
            }
        }

        // No 2Fa, issue token
        $token = $user->createToken('user-token', ['user'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            '2fa_token' => 'required|string',
            'code' => 'required|string'
        ]);

        $userId = cache("2fa:{$request->input('2fa_token')}");

        if (!$userId) {
            return response()->json(['message' => "Invalid or expired 2FA token"], 403);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->two_factor_type === 'totp') {
            $google2fa = new Google2FA();
            $secret = decrypt($user->two_factor_secret);

            if (!$google2fa->verifyKey($secret, $request->code)) {
                return response()->json(['message' => 'Invalid authenticator code'], 401);
            }
        } elseif ($user->two_factor_type === 'email') {
            if ($user->two_factor_email_code !== $request->code || now()->gt($user->two_factor_expires_at)) {
                return response()->json(['message' => 'Invalid or expired email']);
            }

            // Clear code
            $user->two_factor_email_code = null;
            $user->two_factor_expires_at = null;
            $user->save();
        }

        cache()->forget("2fa:{$request->input('2fa_token')}");

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'User Logged out']);
    }
}
