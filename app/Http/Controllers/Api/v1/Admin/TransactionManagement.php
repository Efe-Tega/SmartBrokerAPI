<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionManagement extends Controller
{
    public function allTransactions()
    {
        $transactions = Transactions::latest()->get();

        return response()->json(['data' => $transactions]);
    }

    public function depositTransactions()
    {
        $depositTransactions = Transactions::where('trans_type', 'deposit')->latest()->get();

        return response()->json(['data' => $depositTransactions]);
    }

    public function withdrawTransactions()
    {
        $withdrawTransactions = Transactions::where('trans_type', 'withdraw')->latest()->get();

        return response()->json(['data' => $withdrawTransactions]);
    }

    public function transactionDetails($id)
    {
        $depositTrans = Transactions::findOrFail($id);

        return response()->json(['data' => $depositTrans]);
    }

    public function updateTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);

        $id = $request->id;

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }

        Transactions::findOrFail($id)->update([
            'status' => $request->status
        ]);

        return response()->json(['message' => 'transaction updated']);
    }

    public function deleteTransaction($id)
    {
        Transactions::findOrFail($id)->delete();

        return response()->json(['message' => 'transaction deleted']);
    }
}
