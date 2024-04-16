<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;

use App\Models\Department;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            "username" => "required",
            "password" => "required",
        ], [
            "username.required" => "Vui lòng nhập tên tài khoản.",
            "password.required" => "Vui lòng nhập mật khẩu.",
        ]);
        $credentials = $request->except('rememberme');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response(['message' => 'Sai tài khoản hoặc mật khẩu'], 400);
            }
        } catch (JWTException $e) {
            // return $e;
            return response()->json(['message' => 'could_not_create_token'], 500);
        }

        $user = User::select(
            'id',
            'name',
            'username',
            'department_id',
            'status_id',
            'email',
        )->where('users.username', '=', $credentials['username'])->first();

        //Set cookie tạm bằng js
        return response()->json(compact('token', 'user'));

        // return response()->cookie('accessToken', $token, 720)->cookie('user', $user, 720);
        // Set cookie từ server nhưng chỉ thấy trên http chứ không thấy trên application
        // Có lẽ không có https nên nó không set trên application
        // Nhưng thử ở trên postman thì vẫn có cookie
        // Do cors ??
        // $response = response('Đăng nhập thành công.');
        // $response->withCookie(cookie('accessToken', $token, 720, null, null, false, true));
        // $response->withCookie(cookie('user', $user, 720, null, null, false, false));
        // return $response;
    }

    public function getAuthenticatedUser(Request $request)
    {
        $postData = $request->json()->all();
        $user = auth()->user();
        // $userData = Arr::except(
        //     $user,
        //     [
        //         'avatar', 'email_verified_at', 'login_at',
        //         'change_password_at', 'delete_at', 'created_at', 'updated_at',
        //     ]
        // );

        // if ($data['accountId']) {
        //     if ($data['accountId'] == $user['id']) {
        //         return response()->json(compact('user'));
        //     } else {
        //         return response()->json([
        //             'error' => 'Url not found.'
        //         ], 404);
        //     }
        // }

        return response()->json(compact('user'));
    }

    public function testSomethings()
    {
        // $postData = $request->json()->all();

        // $url = asset('assets/avatar/cat.png');
        // return $url;
        // $path = resource_path('assets/avatar/' . 'cat.png');

        // if (!file_exists($path)) {
        //     abort(404);
        // }

        // return response()->file($path);
        $department = Department::find(3);
        $policies = $department->policies;
        // return response()->json(['policies' => $policies]);
        return $policies;
        // return response()->json($policies);
    }
}
