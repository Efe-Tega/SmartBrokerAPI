<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingsManagement extends Controller
{
    public function toggleUserStatus($id)
    {
        $user = Settings::where('user_id', $id)->first();

        if (!$user) {
            Settings::insert([
                'user_id' => $id,
                'user_status' => 1,
                'created_at' => Carbon::now(),
            ]);
        }

        $user->user_status = $user->user_status == 1 ? 0 : 1;
        $user->save();

        return response()->json([
            'message' => 'Status updated'
        ]);
    }
}
