<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Organization;
use App\Models\User;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $accessOrganizations = true;
        return response()->json([
            'accessOrganizations' => $accessOrganizations,
            'organizations' => Organization::select('id', 'name')->get()
        ]);
        // return Organization::select('id', 'name')->all();
        // $user = User::find(auth()->user()->id);
        // $userPolicies = $user->policies()->where('policy_id', 2)->first();
        // $departmentPolicies = Department::find($user->department_id)->policies()->where('policy_id', 2)->first();
        // $allAccess = false;
    }
}
