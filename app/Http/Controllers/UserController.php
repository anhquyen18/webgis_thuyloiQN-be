<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Hash;

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
            'users.id',
            'users.name',
            'users.username',
            'avatar',
            'phone_number',
            'birthday',
            'gender',
            'users.department_id',
            'users.organization_id',
            'users.status_id',
            'users.email',
            'organizations.name as organization_name',
            'departments.name as department_name',
        )
            ->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('users.username', '=', $credentials['username'])->first();

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng. Vui lòng thử lại sau.'], 500);
        }

        $avatar = '';
        if ($user['avatar']) {
            if (strrchr($user['avatar'], ".") === '.jpg')
                $avatar = 'data:image/jpg;base64,' . base64_encode(file_get_contents(resource_path('assets/avatar/' .   $user['avatar'])));
            else if (strrchr($user['avatar'], ".") === '.png')
                $avatar = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('assets/avatar/' .   $user['avatar'])));
        }

        $policies = $user->policies;

        $department = Department::find($user->department_id);
        if ($department) {
            $policies = $policies->merge($department->policies);
        }

        $user->allPolicies = $policies;
        $user->avatar_base64 = $avatar;

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
        $user = User::select(
            'users.id',
            'users.name',
            'users.username',
            'avatar',
            'phone_number',
            'birthday',
            'gender',
            'users.department_id',
            'users.organization_id',
            'users.status_id',
            'users.email',
            'organizations.name as organization_name',
            'departments.name as department_name',
        )
            ->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('users.id', '=',  auth()->user()->id)->first();

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng. Vui lòng thử lại sau.'], 500);
        }



        $avatar = '';
        if ($user['avatar'] !== '') {
            if (strrchr($user['avatar'], ".") === '.jpg')
                $avatar = 'data:image/jpg;base64,' . base64_encode(file_get_contents(resource_path('assets/avatar/' .   $user['avatar'])));
            else if (strrchr($user['avatar'], ".") === '.png')
                $avatar = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('assets/avatar/' .   $user['avatar'])));
        };

        $policies = $user->policies;

        $department = Department::find($user->department_id);
        if ($department) {
            $policies = $policies->merge($department->policies);
        }

        $user->allPolicies = $policies;
        $user->avatar_base64 = $avatar;



        return response()->json(compact('user'));
    }

    public function updateUserInfo(Request $request, $id)
    {
        $postData = $request->json()->all();
        try {
            $user = User::find($id);
            if (!$user)
                return response()->json(['caution' => '', 'message' => 'Không tìm thấy người dùng.'], 500);
            $user->name = $postData['name'];
            $user->email = $postData['email'];
            $user->phone_number = $postData['phone_number'];
            $user->gender = $postData['gender'];
            $user->birthday = $postData['birthday'];
            $user->save();

            return response()->json(['message' => 'Cập nhật hồ sơ người dùng thành công',]);
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Cập nhật hồ sơ người dùng không thành công. Vui lòng thử lại sau.'], 500);
        }
    }

    public function uploadUserAvatar(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Ảnh có định dạng jpeg, png, jpg hoặc gif, dung lượng tối đa 2MB
        ]);

        if ($request->has('file')) {
            $user = User::find($id);

            $file = $request->file('file');
            $fileName = 'user' . '_' . $id . '_' . 'avatar.jpg';
            $file->move(resource_path('assets/avatar'),  $fileName);
            $user->avatar = $fileName;
            $user->save();

            $avatar = 'data:image/jpg;base64,' . base64_encode(file_get_contents(resource_path('assets/avatar/' .   $fileName)));
            return response()->json(['message' => 'Cập nhật ảnh đại diện thành công', 'avatar' => $avatar]);
        } else {
            return response()->json(['message' => 'Cập nhật ảnh đại diện không thành công. Vui lòng thử lại sau.'], 500);
        }
    }

    public function updateUserPassword(Request $request, $id)
    {
        $postData = $request->json()->all();

        try {
            $user = User::find($id);
            if (!$user)
                return response()->json(['caution' => '', 'message' => 'Không tìm thấy người dùng.'], 500);


            if (Hash::check($postData['currentPass'], $user['password'])) {
                $user['password'] = Hash::make($postData['checkPass']);
                $user->save();

                return response()->json(['message' => 'Đổi mật khẩu thành công.']);
            }
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Cập nhật mật khẩu không thành công. Vui lòng thử lại sau.'], 500);
        }

        return response()->json(['message' => 'Sai mật khẩu.'], 500);
    }

    public function getNoDepartmentUser(Request $request)
    {
        $postData = $request->json()->all();

        // $users = User::select('id', 'name', 'created_at')->where('department_id', null)->paginate($postData['pageSize']);
        $users = User::select('id', 'name', 'created_at')->where('department_id', null)->get();
        return response()->json(['users' => $users]);
    }


    public function testSomethings(Request $request)
    {

        // $postData = $request->json()->all();

    }
}
