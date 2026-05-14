<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
// استدعاء موديل User
use App\Models\UserR;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin::login');
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|min:8',
    //     ], [
    //         'password.min' => 'For security reasons, the password must be at least 8 characters long.',
    //     ]);

    //     $credentials = $request->only('email', 'password');
    //     $remember = $request->has('remember');

    //     if (Auth::attempt($credentials, $remember)) {
    //         $request->session()->regenerate();
    //         $user = Auth::user();

    //         // Allow access if the user has any valid administrative role or is not a regular app user
    //         if (
    //             $user->hasAnyRole(['System Admin', 'Operations Manager', 'Financial Accountant', 'Data Analyst', 'Customer Support']) ||
    //             !$user->hasAnyRole(['Customer', 'Driver', 'Restaurant Admin'])
    //         ) {
    //             return redirect()->route('admin.dashboard');
    //         }

    //         Auth::logout();
    //         $request->session()->invalidate();
    //         $request->session()->regenerateToken();

    //         return back()->with('error', 'Unauthorized access. Your account does not have admin privileges. Please log in via the appropriate portal.')->withInput($request->only('email', 'remember'));
    //     }

    //     return back()->withErrors([
    //         'email' => 'Invalid email or password. Please verify your credentials and try again.',
    //     ])->withInput($request->only('email', 'remember'));
    // }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ], [
            'password.min' => 'For security reasons, the password must be at least 8 characters long.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // 🚨 [الإضافة الجديدة]: فحص حالة الحظر قبل إنشاء الجلسة
            if ($user->status !== 'active' && !$user->hasRole('Customer')) {
                Auth::logout(); // نخرجه بهدوء

                // نرجعه لصفحة الدخول مع رسالة الخطأ باللون الأحمر
                return back()->withErrors([
                    'email' => 'تم حظر حسابك. يرجى التواصل مع الإدارة.',
                ])->withInput($request->only('email', 'remember'));
            }

            // إذا كان حسابه نشطاً، نكمل الإجراءات الطبيعية
            $request->session()->regenerate();

            // Allow access if the user has any valid administrative role or is not a regular app user
            if (
                $user->hasAnyRole(['System Admin', 'Operations Manager', 'Financial Accountant', 'Data Analyst', 'Customer Support']) ||
                !$user->hasAnyRole(['Customer', 'Driver', 'Restaurant Admin'])
            ) {
                return redirect()->route('admin.dashboard');
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('error', 'Unauthorized access. Your account does not have admin privileges. Please log in via the appropriate portal.')->withInput($request->only('email', 'remember'));
        }

        return back()->withErrors([
            'email' => 'Invalid email or password. Please verify your credentials and try again.',
        ])->withInput($request->only('email', 'remember'));
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}