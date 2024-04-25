<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Department;

class AllowAccessOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$policies): Response
    {
        $user = auth()->user();
        $department = Department::find($user->department_id);
        $userPolicies = $user->policies->pluck('id')->toArray();
        $departmentPolicies =  $department->policies()->pluck('id')->toArray();
        // Kiểm tra xem có quyền hạn cần thiết không
        $allowUser = count(array_intersect($policies, $userPolicies)) > 0;
        $allowDepartment = count(array_intersect($policies, $departmentPolicies)) > 0;


        // return response()->json(['caution' => $allowUser]); 
        // $user = User::find(auth()->user()->id);
        // $userPolicies = $user->policies()->where('policy_id', $policies[0])->first();



        // $departmentPolicies = Department::find($user->department_id)->policies()->where('policy_id', $policies[0])->first();

        if ($allowUser || $allowDepartment) {
            // Kiểm tra xem user có toàn quyền truy cập tất cả các tổ chức không
            $fullAccessUser = $user->policies->contains('id', $policies[1]);
            $fullAccessDepartment = $department->policies->contains('id', $policies[1]);
            $fullAccess = false;
            if ($fullAccessUser || $fullAccessDepartment) {
                $fullAccess = true;
            }
            $request->attributes->set('full_access_organizations', $fullAccess);
            return $next($request);
        }

        // return response()->json(['caution' => 'Unauthorized', 'error' => 'You are not authorized to perform this action, please contact admin!'], 403);
        // return response()->json(['caution' => $user['department_id'], 'error' => $role], 403);
        return response()->json(['caution' => 'Unauthorized', 'message' => 'Bạn không đủ thẩm quyền để thực hiện điều này'], 403);
    }
}
