<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSignupRequest;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSignupController extends Controller
{
    public function signup(UserSignupRequest $request)
    {
        $postData = $request->json()->all();
        try {

            $user = User::create([
                'username' => $postData['username'],
                'password' => Hash::make($postData['password']),
                'email' => $postData['email'],
                'status_id' => 1,
            ]);


            return response()->json(['user' => $user, 'message' => 'Tạo người dùng thành công.',]);
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Tạo người dùng thất bại. Vui lòng thử lại sau!'], 500);
        }
    }
}
