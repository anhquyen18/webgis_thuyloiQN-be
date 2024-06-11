<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\Department;
use App\Models\User;
use Exception;

class PolicyController extends Controller
{
    public function index(Request $request)
    {
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');

        // Nếu có policy 2 "Toàn quyền truy cập tổ chức" thì được get về tất cả các quyền
        // Còn không loại 2 đi
        try {
            if ($fullAccessOrganizations) {
                $policies = Policy::all();
            } else {
                $policies = Policy::whereNot('id', 2)->get();
            }
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }

        return response()->json(['policies' => $policies, 'message' => 'Yêu cầu thành công.']);
    }

    public function getPoliciesNotInDepartment(Request $request, $departmentId)
    {
        // Nếu có policy 2 "Toàn quyền truy cập tổ chức" thì được get về tất cả các quyền
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');
        $user = auth()->user();
        try {
            if ($fullAccessOrganizations) {
                $allPolicies = Policy::all();
            } else {
                $allPolicies = Policy::whereNot('id', 2)->get();
            }

            $department = Department::find($departmentId);
            if (!$department) {
                return response()->json(['message' => 'Không tìm thấy phòng ban.'], 500);
            }
            if (!$fullAccessOrganizations && $department['organization_id'] != $user->organization->id) {
                return response()->json(['message' => 'Phòng ban không thuộc quyền quản lí của người dùng.'], 500);
            }

            $policies = $allPolicies->diff($department->policies);
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }
        return response()->json(['policies' => $policies, 'message' => 'Yêu cầu thành công.']);
    }
}
