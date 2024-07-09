<?php

namespace App\Http\Middleware;

use App\Models\LockedTime;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLockedTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $isAdmin =  $request->attributes->get('isAdmin');
            $latestLockedTime = LockedTime::orderBy('end_time', 'desc')->first();
            if ($isAdmin) {
                return $next($request);
            } else {
                if ($latestLockedTime) {
                    $endTime = Carbon::parse($latestLockedTime->end_time);
                    $startTime = Carbon::parse($latestLockedTime->end_time);
                    $now = Carbon::now();
                    if ($now->greaterThanOrEqualTo($startTime) && $now->lessThanOrEqualTo($endTime)) {
                        return response()->json(['message' => 'Không thể đăng nhập do ứng dụng đang tạm khoá.'], 403);
                        return $next($request);
                    } else {
                        return $next($request);
                    }
                } else if (is_null($latestLockedTime)) {
                    return $next($request);
                }
            }
        } catch (Exception $e) {
            return response()->json(['caution' => 'Lỗi.', 'message' => 'Không thể đăng nhập. Vui lòng thử lại sau.'], 403);
        }
        return response()->json(['caution' => 'Lỗi gì đó? cần fix.', 'message' => 'Không thể đăng nhập. Vui lòng thử lại sau.'], 403);
        // return $next($request);
    }
}
