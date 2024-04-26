<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Department;

class AllowAccessOrganizations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$policies): Response
    {
        // Quyền truy cập toàn bộ các tổ chức hiện có

        $user = User::find(auth()->user()->id);
        $userPolicies = $user->policies()->where('policy_id', $policies[0])->first();
        $departmentPolicies = Department::find($user->department_id)->policies()->where('policy_id', $policies[0])->first();

        if ($userPolicies || $departmentPolicies) {
            return $next($request);
        }

        return response()->json(['caution' => 'Unauthorized', 'message' => 'Bạn không đủ thẩm quyền để thực hiện điều này'], 403);
    }
}