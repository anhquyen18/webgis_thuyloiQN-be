<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Exception;

class DepartmentController extends Controller
{
    public function index(Request $request, $organizationId)
    {
        $user = auth()->user();
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');

        try {
            // Kiểm tra xem có "Toàn quyền quản lí các tổ chức" không
            if ($fullAccessOrganizations) {
                return response()->json(['allAccess' => $fullAccessOrganizations, 'departments'
                =>  Department::select(
                    'departments.id',
                    'departments.name as name',
                    'departments.created_at',
                    'organizations.name as organization_name'
                )->withCount('users')->withCount('policies')
                    ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')->get()]);
            } else {
                if (!$user['organization_id'] == $organizationId)
                    return response()->json(['message' => 'Tổ chức không hợp lệ.'], 500);

                return response()->json(['allAccess' => $fullAccessOrganizations, 'departments'
                => Department::select(
                    'departments.id',
                    'departments.name as name',
                    'departments.created_at',
                    'organizations.name as organization_name'
                )
                    ->join('organizations', 'departments.organization_id', '=', 'organizations.id')
                    ->where('organization_id', $organizationId)
                    ->withCount('users')->withCount('policies')->get()]);
            }
        } catch (Exception $e) {
            // return response()->json(['message' => $e], 500);
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại. Vui lòng thử lại sau!'], 500);
        }
        return response()->json(['message' => 'Thành công!']);
    }


    public function create(Request $request)
    {
        // Nếu có gán quyền quản lí tổ chức cho department
        // mà department không thuộc tổ chức nào thì vẫn được phép gán.

        $postData = $request->json()->all();
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $user = auth()->user();

        try {
            if (!$fullAccessOrganizations) {
                $postData['info']['organizationId'] = $user['organization_id'];
            }

            $department = Department::create([
                'name' => $postData['info']['departmentName'],
                'description' => $postData['info']['description'],
                'organization_id' => $postData['info']['organizationId']
            ]);

            $policyData = [];
            foreach ($postData['policies'] as $policy) {
                $policyData[] = [
                    'department_id' => $department['id'],
                    'policy_id' => $policy,
                ];
            }
            DB::table('department_policies')->insert($policyData);
            $userData = [];
            foreach ($postData['users'] as $user) {
                $userData[] = [
                    'user_id' => $department['id'],
                    'policy_id' => $user,
                ];
            }
            DB::table('user_policies')->insert($userData);
        } catch (QueryException $e) {
            return response()->json(['caution' =>  $e, 'message' => 'Tên phòng ban đã tồn tại!'], 500);
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Tạo phòng ban thất bại. Vui lòng thử lại sau!'], 500);
        }



        return response()->json(['message' => 'Tạo phòng ban thành công.']);
    }

    public function delete(Request $request)
    {

        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $user = auth()->user();

        $departmentIds = explode(',', $request->query('id'));

        try {
            if ($fullAccessOrganizations) {
                Department::destroy($departmentIds);
            } else {
                foreach ($departmentIds as $departmentId) {
                    $department = Department::find($departmentId);
                    if ($department['organization_id'] == $user['organization_id'])  $department->delete();
                }
            }
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Xoá thất bại. Vui lòng thử lại sau!'], 500);
        }

        return response()->json(['message' => 'Xoá phòng ban thành công!']);
    }

    public function getDepartment($departmentId)
    {
        $user = auth()->user();

        try {
            $userDepartment = Department::find($user->department_id);
            $userPolicies = $user->policies;
            if ($userDepartment)
                $departmentPolicies =  $userDepartment->policies;
            else $departmentPolicies = $user->policies;

            $policies =  $userPolicies->merge($departmentPolicies)->unique('id');

            // Quyền "Quản lí toàn bộ tổ chức" và quyền "Quản lí tổ chức"
            $allAccessOrganizationsPolicy = 2;
            $accessOrganizationPolicy = 1;


            $department = Department::find($departmentId);
            if (!$department)
                return response()->json(['message' => 'Không tìm thấy phòng ban.'], 500);

            // Nếu không tồn tại quyền 2, kiểm tra quyền 1, nếu có thì id tổ chức của department phải trùng với
            // id tổ chức của user mới cho trả về
            // Còn nếu không có cả quyền 1, kiểm tra department đang request phải trùng với department mà user sở hữu
            // Có quyền 2 thì truy cập department nào cũng được
            if (!$policies->contains('id',  $allAccessOrganizationsPolicy)) {
                if ($policies->contains('id', $accessOrganizationPolicy)) {
                    if ($department['organization_id'] != $user['organization_id']) {
                        return response()->json(['message' => 'Phòng ban không thuộc quyền quản lí của người dùng.'], 500);
                    }
                } else {
                    if ($department['id'] != $user['department_id']) {
                        return response()->json(['message' => 'Phòng ban không thuộc quyền quản lí của người dùng.'], 500);
                    }
                }
            }

            $department->policies;
            $department->users =  $department->users()->select('id', 'name', 'email', 'created_at')->get();
        } catch (Exception $e) {
            return $e;
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }


        return response()->json(['department' => $department, 'message' => 'Yêu cầu thành công!']);
    }


    public function updateInfo(Request $request, $departmentId)
    {
        // Khi cập nhật bằng quyền quản lí tổ chức (policy 1) thì tổ chức của user phải trùng
        // với tổ chức của department mới cho các thao tác tiếp theo
        // Tác động đến các user khác cũng thế, đều phải nằm trong cùng tổ chức nếu chỉ có "policy 1"
        $postData = $request->json()->all();
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $user = auth()->user();


        try {
            $department = Department::find($departmentId);
            if (!$department)
                return response()->json(['message' => 'Không tìm thấy phòng ban.'], 500);

            if (!$fullAccessOrganizations && $department['organization_id'] != $user['organization_id']) {
                return response()->json(['message' => 'Phòng ban không thuộc quyền quản lí của người dùng.'], 500);
            }

            $department->name = $postData['name'];
            $department->description = $postData['description'];
            $department->save();
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }

        return response()->json(['message' => 'Yêu cầu thành công.']);
    }

    public function addUsers(Request $request, $departmentId)
    {
        $postData = $request->json()->all();
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $user = auth()->user();

        try {
            $department = Department::find($departmentId);
            if (!$department)
                return response()->json(['message' => 'Không tìm thấy phòng ban.'], 500);

            // return response()->json(['message' => !$department['organization_id'] == $user['organization_id'], 'message2' => $user['organization_id'], 'message3' => !$fullAccessOrganizations], 500);
            if (!$fullAccessOrganizations && $department['organization_id'] != $user['organization_id']) {
                return response()->json(['message' => 'Phòng ban không thuộc quyền quản lí của người dùng.'], 500);
            }

            $users = User::whereIn('id', $postData['users'])->get();

            foreach ($users as $element) {
                if (!$element->department_id) {
                    $element->department_id = $departmentId;
                    $element->save();
                }
            }
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }

        return response()->json(['message' => 'Thêm thành viên thành công.']);
    }

    public function removeUsers(Request $request, $departmentId)
    {
        $postData = $request->json()->all();
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $user = auth()->user();

        try {
            $department = Department::find($departmentId);
            if (!$department)
                return response()->json(['message' => 'Không tìm thấy phòng ban.'], 500);

            if (!$fullAccessOrganizations && $department['organization_id'] != $user['organization_id']) {
                return response()->json(['message' => 'Phòng ban không thuộc quyền quản lí của người dùng.'], 500);
            }

            $users = User::whereIn('id', $postData['users'])->get();

            foreach ($users as $element) {
                if ($element->department_id) {
                    $element->department_id = null;
                    $element->save();
                }
            }
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }

        return response()->json(['message' => 'Xoá thành viên thành công.']);
    }

    public function addPolicies(Request $request, $departmentId)
    {
        $user = auth()->user();

        try {
            $postData = $request->json()->all();
            $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
            $allAccessOrganizationsPolicy = 2;


            $department = Department::find($departmentId);
            if (!$department)
                return response()->json(['message' => 'Không tìm thấy phòng ban.'], 500);

            if (!$fullAccessOrganizations) {
                if ($department['organization_id'] != $user['organization_id']) {
                    return response()->json(['message' => 'Phòng ban không thuộc quyền quản lí của người dùng.'], 500);
                }

                // Quyền hạn được gán thêm có chứa quyền hạn cao hơn người dùng hiện có thì không được.
                if (in_array($allAccessOrganizationsPolicy, $postData['policies'])) {
                    return response()->json(['message' => 'Quyền hạn không phù hợp.'], 500);
                }
            }

            $policyData = [];
            foreach ($postData['policies'] as $policy) {
                $policyData[] = [
                    'department_id' => $department['id'],
                    'policy_id' => $policy,
                ];
            }
            DB::table('department_policies')->insert($policyData);
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }

        return response()->json(['message' => 'Thêm quyền hạn thành công.']);
    }

    public function removePolicies(Request $request, $departmentId)
    {
        $user = auth()->user();

        try {
            $postData = $request->json()->all();
            $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
            $allAccessOrganizationsPolicy = 2;

            $policyIds = explode(',', $request->query('id'));
            // Cần chuyển đổi khi explode thành string
            $policyIds = array_map('intval', $policyIds);

            $department = Department::find($departmentId);
            if (!$department)
                return response()->json(['message' => 'Không tìm thấy phòng ban.'], 500);

            if (!$fullAccessOrganizations) {
                if ($department['organization_id'] != $user['organization_id']) {
                    return response()->json(['message' => 'Phòng ban không thuộc quyền quản lí của người dùng.'], 500);
                }

                // Quyền hạn được gán thêm có chứa quyền hạn cao hơn người dùng hiện có thì không được.
                if (in_array($allAccessOrganizationsPolicy, $policyIds)) {
                    return response()->json(['message' => 'Quyền hạn không phù hợp.'], 500);
                }
            }

            $policies = DB::table('department_policies')
                ->where('department_id',  $department->id)
                ->whereIn('policy_id', $policyIds)->delete();
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }

        return response()->json(['message' => 'Xoá quyền hạn thành công.']);
    }

    public function getNoOrganizationDepartments()
    {
        return response()->json([
            'departments' => Department::select('id', 'name')->where('organization_id', null)->get()
        ]);
    }
}
