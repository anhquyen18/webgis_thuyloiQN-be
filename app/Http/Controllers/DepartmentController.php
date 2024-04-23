<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;

class DepartmentController extends Controller
{
    public function index($organizationId)
    {
        $user = User::find(auth()->user()->id);
        $userPolicies = $user->policies()->where('policy_id', 2)->first();
        $departmentPolicies = Department::find($user->department_id)->policies()->where('policy_id', 2)->first();
        $allAccess = false;

        if ($userPolicies || $departmentPolicies) {
            // Kiểm tra xem có "Toàn quyền quản lí các tổ chức" không
            $organizationId = 'all';
            $allAccess = true;
        }

        if ($organizationId === 'all') {
            return response()->json(['allAccess' => $allAccess, 'departments'
            =>  Department::select(
                'departments.name as name',
                'departments.created_at',
                'organizations.name as organization_name'
            )->withCount('users')->withCount('policies')
                ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')->get()]);
        } else {
            return response()->json(['allAccess' => $allAccess, 'departments'
            => Department::select(
                'departments.name as name',
                'departments.created_at',
                'organizations.name as organization_name'
            )
                ->leftJoin('organizations', 'departments.organization_id', '=', 'organizations.id')
                ->where('organization_id', $organizationId)
                ->withCount('users')->withCount('policies')->get()]);
        }
    }
}
