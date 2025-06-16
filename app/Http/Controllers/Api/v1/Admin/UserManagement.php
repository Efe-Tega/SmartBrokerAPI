<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserManagement extends Controller
{
    public function allUsers()
    {
        $users = User::latest()->get();

        return response()->json(["data" => $users]);
    }

    public function userDetails($id)
    {
        $user = User::findOrFail($id);

        return response()->json(["data" => $user]);
    }

    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            // 'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()]);
        }

        $id = $request->id;

        $user = User::findOrFail($id);
        $user->username = $request->username;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->save();

        return response()->json(["message" => "Information Updated"]);
    }
}
