<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\CryptoCurrency;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $transaction = Transactions::insert([
            'user_id' => $userId,
            'currency' => $request->currency,
            'amount' => $request->amount,
            // 'crypto_value' => $request->crypto_value,
            'status' => 'pending',
            'trans_id' => $trans_id,
            'trans_type' => 'deposit',
        ]);

        return response()->json([
            "data" => $transaction,
            "user" => $userId,
            'message' => "Deposit successfull"
        ]);
    }
}
