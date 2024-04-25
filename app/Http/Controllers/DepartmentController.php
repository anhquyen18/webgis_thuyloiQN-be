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

        if ($fullAccessOrganizations) {
            // Kiểm tra xem có "Toàn quyền quản lí các tổ chức" không
            $organizationId = 'all';
        }
        try {
            if ($organizationId === 'all') {
                return response()->json(['allAccess' => $fullAccessOrganizations, 'departments'
                =>  Department::select(
                    'departments.id',
                    'departments.name as name',
                    'departments.created_at',
                    'organizations.name as organization_name'
                )->withCount('users')->withCount('policies')
                    ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')->get()]);
            } else {
                return response()->json(['allAccess' => $fullAccessOrganizations, 'departments'
                => Department::select(
                    'departments.id',
                    'departments.name as name',
                    'departments.created_at',
                    'organizations.name as organization_name'
                )
                    ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')
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
            return $e;
            // return response()->json(['caution' => $e, 'message' => 'Xoá thất bại. Vui lòng thử lại sau!'], 500);
        }

        return response()->json(['message' => 'Xoá phòng ban thành công!']);
    }
}
