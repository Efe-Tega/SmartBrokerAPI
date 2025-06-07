<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Helpers\TwoFactorHelper;
use App\Http\Controllers\Controller;
use App\Models\AccountInfo;
use App\Models\CryptoCurrency;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function fetchWalletAddress()
    {
        $cryptoWallets = CryptoCurrency::all();

        return response()->json($cryptoWallets);
    }

    public function cryptoDeposit(Request $request)
    {
        $request->validate(
            [
                'currency' => 'required',
                'amount' => 'required',
            ]
        );

        $userId = Auth::user()->id;
        $trans_id = "TRX" . uniqid();

        Transactions::insert([
            'user_id' => $userId,
            'currency' => $request->currency,
            'amount' => $request->amount,
            // 'crypto_value' => $request->crypto_value,
            'status' => 'pending',
            'trans_id' => $trans_id,
            'created_at' => Carbon::now()
        ]);

        $acctData = AccountInfo::where('user_id', $userId)->first();

        if ($acctData) {
            $acctData->user_id = $userId;
            $acctData->acct_bal += $request->amount;
            $acctData->save();
        } else {
            AccountInfo::insert([
                'user_id' => $userId,
                'acct_bal' => $request->amount,
                'created_at' => Carbon::now()
            ]);
        }

        return response()->json([
            "data" => $acctData ?? "New Data inserted",
            "user" => $userId,
            'message' => "Deposit successfull"
        ]);
    }

    public function requestWithdraw(Request $request)
    {
        $request->validate([
            'currency' => 'required',
            'amount' => 'required|integer'
        ]);

        $user = Auth::user();
        $kyc = $user->kycProfile;

        // if (!$kyc || $kyc->status !== "approved") {
        //     return response()->json(['message' => 'Please complete KYC before making withdraw']);
        // }

        if (!$user->two_factor_enabled) {
            return response()->json(['message' => 'Setup 2FA']);
        }

        // Check if user has enough balance
        if ($request->amount > $user->balance->acct_bal) {
            return response()->json(['message' => 'Insufficient balance for withdrawal']);
        }

        // send verification code using helper
        TwoFactorHelper::sendEmailVerificationCode($user, $request->amount);

        Cache::put('withdraw_request_' . $user->id, $request->only(['currency', 'amount', 'crypto_value']), now()->addMinutes(10));

        return response()->json(['message' => 'Verification code sent to your email']);
    }

    public function verifyWithdrawCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6'
        ]);

        $user = Auth::user();
        $code = Cache::get('withdraw_2fa_' . $user->id);

        if (!$code || $code != $request->code) {
            return response()->json(['message' => 'Invalid or expired verification code'], 400);
        }

        $withdrawData = Cache::pull('withdraw_request_' . $user->id);
        $balance = AccountInfo::where('user_id', $user->id)->first();
        if (!$withdrawData) {
            return response()->json(['message' => 'Withdrawal request expired or not found'], 400);
        } else {

            $balance->user_id = $user->id;
            $balance->acct_bal -= $withdrawData['amount'];
            $balance->save();
        }

        $transId = "trx" . uniqid();

        $withdraw = new Transactions();
        $withdraw->user_id = $user->id;
        $withdraw->currency = $withdrawData['currency'];
        $withdraw->amount = $withdrawData['amount'];
        $withdraw->crypto_value = $request->crypto_value;
        $withdraw->trans_id = $transId;
        $withdraw->trans_type = "withdraw";
        $withdraw->created_at = Carbon::now();
        $withdraw->save();

        return response()->json(['message' => 'Withdraw request submitted successfully']);
    }
}
