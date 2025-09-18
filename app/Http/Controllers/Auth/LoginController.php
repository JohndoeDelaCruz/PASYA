<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use App\Models\Farmer;

class LoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'user_type' => 'required|string|in:admin,farmer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('username', 'password');
        $userType = $request->input('user_type');

        // Try to authenticate based on user type
        if ($userType === 'admin') {
            // Check if admin exists and is active
            $admin = Admin::where('username', $credentials['username'])->where('is_active', true)->first();
            
            if ($admin && Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
            
            // Check if admin exists but is inactive
            $inactiveAdmin = Admin::where('username', $credentials['username'])->first();
            if ($inactiveAdmin && !$inactiveAdmin->is_active) {
                return redirect()->back()
                    ->withErrors([
                        'username' => 'Your admin account has been deactivated. Please contact a system administrator.',
                    ])
                    ->withInput();
            }
        } elseif ($userType === 'farmer') {
            // Check if farmer exists and is active
            $farmer = Farmer::where('username', $credentials['username'])->where('is_active', true)->first();
            
            if ($farmer && Auth::guard('farmer')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('farmer.dashboard');
            }
            
            // Check if farmer exists but is inactive
            $inactiveFarmer = Farmer::where('username', $credentials['username'])->first();
            if ($inactiveFarmer && !$inactiveFarmer->is_active) {
                return redirect()->back()
                    ->withErrors([
                        'username' => 'Your farmer account has been deactivated. Please contact an administrator.',
                    ])
                    ->withInput();
            }
        }

        return redirect()->back()
            ->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])
            ->withInput();
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Logout from all guards
        Auth::guard('admin')->logout();
        Auth::guard('farmer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}