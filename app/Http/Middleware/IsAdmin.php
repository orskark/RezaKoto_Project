<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = auth('api')->user();
            $currentRoleId = JWTAuth::parseToken()->getPayload()->get('role_id');

            if ($user && $currentRoleId == 1) {
                return $next($request);
            }

            return response()->json(['message' => 'You are not an Admin'], 403);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Unauthorized: ' . $e->getMessage()], 401);
        }
    }
}
