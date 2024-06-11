<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\JWTManager as JWT;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Models\Department;

class jwtInUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$policies): Response
    {
        $token = $request->query('token');
        if (!$token) {
            return response()->json(['caution' => 'No token found', 'message' => 'Không tìm thầy token. Vui lòng đăng nhập để tiếp tục!'], 401);
        }

        try {
            JWTAuth::setToken($token);
            if (!JWTAuth::check()) {
                return response()->json(['caution' => 'Invalid token', 'message' => 'Phiên đăng nhập không hợp lệ. Vui lòng đăng nhập để tiếp tục!'], 401);
            }
            $user = JWTAuth::authenticate();

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
                return $next($request);
            }
        } catch (JWTException $e) {
            return response()->json(['caution' => 'Token has expired', 'message' => 'Phiên đăng nhập hết hạn. Vui lòng đăng nhập để tiếp tục!'], 401);
        }

        return response()->json(['caution' => 'Unauthorized', 'message' => 'Bạn không đủ thẩm quyền để thực hiện điều này'], 403);
    }
}
