<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtPolicyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$policy): Response
    {
        $user = auth()->user();

        if ($user['department_id'] ===  (int)$policy[0]) {
            return $next($request);
        }

        // return response()->json(['caution' => 'Unauthorized', 'error' => 'You are not authorized to perform this action, please contact admin!'], 403);
        // return response()->json(['caution' => $user['department_id'], 'error' => $role], 403);
        return response()->json(['caution' => 'Unauthorized', 'message' => 'Bạn không đủ thẩm quyền để thực hiện điều này'], 403);
    }
}
