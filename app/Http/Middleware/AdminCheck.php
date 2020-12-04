<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    public function handle($request, Closure $next)
    {
        if ($request->path() == 'app/admin_login') {
            return $next($request);
        }
        // \Log::info('hgqasdgaudg aysu');
        if (!Auth::check()) {
            return response()->json([
                'msg' => 'You are not allowed to access this routes...',
                'url' => $request->path(),
            ],403);
        }
        if (Auth::user()->role->isAdmin == 0) {
            return response()->json([
                'msg' => 'You are not allowed to access this routes...'
            ],403);
        }
        return $next($request);
    }
}