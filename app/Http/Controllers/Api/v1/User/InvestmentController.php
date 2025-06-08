<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\AccountInfo;
use App\Models\InvestmentPlan;
use App\Models\PackagePlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    public function investment(Request $request)
    {
        $request->validate([
            'package_plan_id' => 'required',
            'amount' => 'required',
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $planId = $request->package_plan_id;

        $investmentPlan = PackagePlan::where('id', $planId)->first();
        $balance = AccountInfo::where('user_id', $user->id)->first();

        $dailyProfit = ($amount / 100) * $investmentPlan->roi;

        // if ($amount > $balance->acct_bal) {
        //     return response()->json(["message" => "Insufficient Balance"]);
        // }

        if ($investmentPlan->min_amount > $amount) {
            return response()->json(['message' => 'Amount is small for this plan. Please increase amount']);
        } elseif ($amount > $investmentPlan->max_amount) {
            return response()->json(['message' => 'Amount for this plan exceeded']);
        } else {
            $balance->acct_bal -= $amount;
            $balance->save();
        }

        $investment = new InvestmentPlan();
        $investment->user_id = $user->id;
        $investment->package_plan_id = $planId;
        $investment->plan_amount = $amount;
        $investment->plan_profit = $dailyProfit;
        $investment->created_at = Carbon::now();
        $investment->save();

        return response()->json([
            'message' => 'Investment Activated'
        ]);
    }
}
