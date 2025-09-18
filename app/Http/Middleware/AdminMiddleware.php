<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated with admin guard
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please log in as admin.',
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login');
        }

        $admin = Auth::guard('admin')->user();
        
        // Check if admin account is active
        if (!$admin->is_active) {
            Auth::guard('admin')->logout();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your admin account has been deactivated.'
                ], 403);
            }
            abort(403, 'Your admin account has been deactivated.');
        }

        return $next($request);
    }
}
