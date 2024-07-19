<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\LockedTime;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        try {
            $user = User::select(
                'users.id',
                'users.name',
                'users.username',
                'avatar',
                'phone_number',
                'birthday',
                'gender',
                'users.department_id',
                'departments.organization_id',
                'users.status_id',
                'users.email',
                'organizations.name as organization_name',
                'departments.name as department_name',
            )
                ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')
                ->where('users.username', '=', $credentials['username'])->first();

            if (!$user) {
                return response()->json(['message' => 'Không tìm thấy người dùng. Vui lòng thử lại sau.'], 404);
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

            // Kiểm tra khả năng đăng nhập lúc ứng dụng đang tạm khoá
            // Nếu kiếm tra quyền admin trả về có phải admin không, policy 2 "Toàn quyền truy cập các tổ chức" ~ admin
            $adminPolicy = 2;

            if (!in_array($adminPolicy, $user->allPolicies->pluck('id')->toArray())) {
                $latestLockedTime = LockedTime::orderBy('id', 'desc')->first();

                if ($latestLockedTime) {
                    $startTime = Carbon::parse($latestLockedTime->start_time);
                    $endTime = Carbon::parse($latestLockedTime->end_time);
                    $now = Carbon::now();
                    if ($now->greaterThanOrEqualTo($startTime) && $now->lessThanOrEqualTo($endTime)) {
                        return response()->json(['message' => 'Không thể đăng nhập do ứng dụng đang tạm khoá.'], 403);
                    }
                }
            }

            $user->avatar_base64 = $avatar;
        } catch (Exception $e) {
            return response()->json(['message' => 'Đăng nhập không thành công.'], 500);
        }


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


        try {
            $user = User::select(
                'users.id',
                'users.name',
                'users.username',
                'avatar',
                'phone_number',
                'birthday',
                'gender',
                'users.department_id',
                'departments.organization_id',
                'users.status_id',
                'users.email',
                'organizations.name as organization_name',
                'departments.name as department_name',
            )
                ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')
                ->where('users.id', '=',  auth()->user()->id)->first();

            if (!$user) {
                return response()->json(['message' => 'Không tìm thấy người dùng. Vui lòng thử lại sau.'], 404);
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
        } catch (Exception $e) {
            // return $e;
            return response()->json(['message' => 'Đăng nhập không thành công.'], 500);
        }

        return response()->json(compact('user'));
    }

    function updateUserPersonalInfo($user, $data)
    {
        $user->name =  $data['name'];
        $user->email =  $data['email'];
        $user->phone_number =  $data['phone_number'];
        $user->gender =  $data['gender'];
        $user->birthday = $data['birthday'];
        $user->save();

        return "Cập nhật thôn tin cá nhân người dùng thành công";
    }

    public function updateUserInfo(Request $request, $id)
    {
        // Thiếu validation phía server
        $postData = $request->json()->all();
        try {
            $user = User::find($id);
            if (!$user)
                return response()->json(['caution' => '', 'message' => 'Không tìm thấy người dùng.'], 404);

            $this->updateUserPersonalInfo($user, $postData);


            return response()->json(['message' => 'Cập nhật hồ sơ người dùng thành công',]);
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Cập nhật hồ sơ người dùng không thành công. Vui lòng thử lại sau.'], 500);
        }
    }

    public function updateUserProfile(Request $request, $id)
    {
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $postData = $request->json()->all();
        $myUser = auth()->user();

        // "Toàn quyền truy cập các tổ chức"
        $allAccessOrganizationsPolicy = 2;

        try {

            $requestedUser = User::find($id);
            if (!$requestedUser)
                return response()->json(['caution' => '', 'message' => 'Không tìm thấy người dùng.'], 404);

            if (!$fullAccessOrganizations) {

                // Quyền hạn được gán thêm có chứa quyền hạn cao hơn người dùng hiện có thì không được.
                if (
                    in_array($allAccessOrganizationsPolicy, $postData['addPolicies'])
                    || in_array($allAccessOrganizationsPolicy, $postData['removePolicies'])
                ) {
                    return response()->json(['message' => 'Quyền hạn không phù hợp.'], 500);
                }
            }

            $addPolicyData = [];
            foreach ($postData['addPolicies'] as $policy) {
                $addPolicyData[] = [
                    'user_id' => $requestedUser->id,
                    'policy_id' => $policy,
                ];
            }

            $removePolicyData = [];
            foreach ($postData['removePolicies'] as $policy) {
                $removePolicyData[] = [
                    'user_id' => $requestedUser->id,
                    'policy_id' => $policy,
                ];
            }

            $this->updateUserPersonalInfo($requestedUser, $postData);
            DB::table('user_policies')->insert($addPolicyData);
            $policies = DB::table('user_policies')
                ->where('user_id',  $requestedUser->id)
                ->whereIn('policy_id', $postData['removePolicies'])->delete();
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Cập nhật hồ sơ người dùng không thành công. Vui lòng thử lại sau.'], 500);
        }


        return response()->json(['message' => 'Cập nhật hồ sơ người dùng thành công',]);
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

    public function updateUserPassword(PasswordRequest $passwordRequest, $id)
    {
        $validatedPassword = $passwordRequest->validated();
        try {
            $user = User::find($id);
            if (!$user)
                return response()->json(['caution' => '', 'message' => 'Không tìm thấy người dùng.'], 404);


            if (Hash::check($passwordRequest['currentPass'], $user['password'])) {
                $user['password'] = Hash::make($validatedPassword['newPass']);
                $user->save();

                return response()->json(['message' => 'Đổi mật khẩu thành công.']);
            }
        } catch (Exception $e) {
            Log::channel('test')->error('Có lỗi xảy ra: ' . $e->getMessage());
            return response()->json(['caution' => $e, 'message' => 'Cập nhật mật khẩu không thành công. Vui lòng thử lại sau.'], 500);
        }

        return response()->json(['message' => 'Sai mật khẩu.'], 403);
    }

    public function getNoDepartmentUser(Request $request)
    {
        $postData = $request->json()->all();

        // $users = User::select('id', 'name', 'created_at')->where('department_id', null)->paginate($postData['pageSize']);
        $users = User::select('id', 'name', 'created_at')->where('department_id', null)->get();
        return response()->json(['users' => $users]);
    }

    public function getUsers(Request $request)
    {
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $user = auth()->user();

        try {
            $users = User::with('department')->select(
                'users.id',
                'users.name',
                'username',
                'users.created_at',
                'users.department_id',
            )
                ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')
                ->get();
            $policies = Policy::all();
            if (!$fullAccessOrganizations) {;

                // User không có quyền "Truy cập các tổ chức" thì chỉ lấy được user trong tổ chức của chính user
                if ($user->organization->id != null) {
                    $users = User::with('department')->select(
                        'users.id',
                        'users.name',
                        'username',
                        'users.created_at',
                        'users.department_id',
                    )
                        ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                        ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')
                        ->where('departments.organization_id', $user->organization->id)->get();
                } else {
                    $users = null;
                }
            }
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }

        return response()->json(['users' => $users, 'message' => 'Yêu cầu thành công.']);
    }

    public function getUserById(Request $request, $id)
    {
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $myUser = auth()->user();
        try {
            $requestedUser = User::with('policies')->with('department')->find($id);

            if (!$requestedUser)
                return response()->json(['caution' => '', 'message' => 'Không tìm thấy người dùng.'], 404);

            $userPolicies = $requestedUser->policies()->pluck('policy_id');
            $policies = Policy::all();
            $allAccessOrganizationsPolicy = 2;

            if (!$fullAccessOrganizations) {
                // User không có quyền "Truy cập các tổ chức" thì chỉ lấy được user trong tổ chức của chính user
                $policies = Policy::whereNot('id', $allAccessOrganizationsPolicy)->get();

                if ($myUser->organization->id == null || $myUser->organization->id != $requestedUser->organization->id) {
                    return response()->json(['caution' => '', 'message' => 'Không tìm thấy người dùng.'], 404);
                }
            }

            $otherPolicies = $policies->reject(function ($policy) use ($userPolicies) {
                return $userPolicies->contains($policy->id);
            });

            $requestedUser->noPolicies = $otherPolicies->values()->all();
            $requestedUser->organization = Department::with('organization')->find($myUser->department_id)->organization;
        } catch (Exception $e) {
            return $e;
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }


        return response()->json(['user' => $requestedUser, 'message' => 'Yêu cầu thành công.']);
    }

    public function getLogs(Request $request)
    {
        // $validator = $request->validate([
        //     "type" => ["string", 'regex:/^[a-zA-Z0-9\s]+$/'],
        // ], []);

        $type = $request->query('type');

        try {
            $logs = UserLog::with('user')->where('log_type', $type)->get();
            return response()->json(['logs' => $logs, 'message' => 'Yêu cầu thành công.']);
        } catch (Exception $e) {
            return $e;
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }
    }


    public function testSomethings(Request $request)
    {

        // $postData = $request->json()->all();
        $user = auth()->user();
        return $user;
    }
}
