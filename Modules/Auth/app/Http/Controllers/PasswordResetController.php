<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class PasswordResetController
{
    // إرسال رابط إعادة التعيين
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return back()->with('status', __($status));
    }

    // عرض صفحة إعادة تعيين كلمة المرور
    public function showResetForm($token)
    {
        return view('auth::reset-password', ['token' => $token]);
    }

    // تحديث كلمة المرور
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required'
        ]);

        $status = Password::reset(
            $request->only(
                'email', 'password', 'password_confirmation', 'token'
            ),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success','Password changed')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
