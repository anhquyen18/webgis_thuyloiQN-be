<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Department;


class AcceptedPolicies
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
        // Kiểm tra user có quyền không
        $allowUser = count(array_intersect($policies, $userPolicies)) > 0;
        // Kiểm tra department của user có quyền không

        if ($department && $department->policies != null) {
            $departmentPolicies =  $department->policies->pluck('id')->toArray();
            $allowDepartment = count(array_intersect($policies, $departmentPolicies)) > 0;
        }


        if ($allowUser || $allowDepartment) {
            // Nếu kiếm tra quyền admin, policy 2 "Toàn quyền truy cập các tổ chức" ~ admin
            if (in_array('2', $policies)) {
                $request->attributes->set('isAdmin', true);
            }
            return $next($request);
        }

        return response()->json(['caution' => 'Unauthorized', 'message' => 'Bạn không đủ thẩm quyền để thực hiện điều này'], 403);
    }
}
