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

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['user_not_found'], 400);
            }
        } catch (TokenExpiredException $e) {
            // return 'token-1';
            return response()->json(['caution' => 'Token has expired', 'message' => 'Vui lòng đăng nhập để tiếp tục!'], 401);
        } catch (TokenInvalidException $e) {
            // return 'token-2';
            return response()->json(['caution' => 'Invalid token', 'message' => 'Vui lòng đăng nhập để tiếp tục!'], 401);
            // return $e;
        } catch (JWTException $e) {
            return response()->json(['caution' => 'No token found', 'message' => 'Vui lòng đăng nhập để tiếp tục!'], 401);
            // return 'token-3';
        }

        return $next($request);
    }
}
