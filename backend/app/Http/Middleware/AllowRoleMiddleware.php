<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowRolesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user(); 
        if (!$user) {
            return response()->json(['message' => 'Unauthorized access'], 401);
        }   
        if ($user->role === 1) {
            return $next($request);
        }
        if (!empty($roles) && !in_array($user->role, $roles, true)) {
            return response()->json([
                'message' => 'Access denied! You do not have permission.'
            ], 403);
        }
    
        return $next($request);
    }
    
}
