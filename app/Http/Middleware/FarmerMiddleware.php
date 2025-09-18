<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FarmerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated with farmer guard
        if (!Auth::guard('farmer')->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please log in as farmer.',
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login');
        }

        $farmer = Auth::guard('farmer')->user();
        
        // Check if farmer account is active
        if (!$farmer->is_active) {
            Auth::guard('farmer')->logout();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your farmer account has been deactivated.'
                ], 403);
            }
            abort(403, 'Your farmer account has been deactivated.');
        }

        return $next($request);
    }
}
