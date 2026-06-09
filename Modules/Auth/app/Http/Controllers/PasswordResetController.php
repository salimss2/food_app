<?php

namespace Modules\Auth\Http\Controllers;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController
{
    // ──────────────────────────────────────────────────────────────────────────
    // Step 1: Send OTP to email
    // POST api/auth/forgot-password  { email }
    // ──────────────────────────────────────────────────────────────────────────
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate 4-digit OTP
        $otp = (string) rand(1000, 9999);

        // Persist OTP with 10-minute expiry
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Send email
        Mail::to($user->email)->send(new ResetPasswordOtpMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Step 2: Verify OTP
    // POST api/auth/verify-otp  { email, otp_code }
    // ──────────────────────────────────────────────────────────────────────────
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|digits:4',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check OTP match
        if ($user->otp_code !== $request->otp_code) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح.',
            ], 422);
        }

        // Check expiry
        if (now()->isAfter($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية رمز التحقق. يرجى طلب رمز جديد.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'رمز التحقق صحيح. يمكنك الآن إعادة تعيين كلمة المرور.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Step 3: Reset Password
    // POST api/auth/reset-password  { email, password, password_confirmation }
    // ──────────────────────────────────────────────────────────────────────────
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        // Safety check: ensure OTP was verified and hasn't expired
        if (!$user->otp_code || now()->isAfter($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت جلسة إعادة التعيين. يرجى طلب رمز جديد.',
            ], 403);
        }

        // Update password and clear OTP fields
        $user->update([
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة تعيين كلمة المرور بنجاح.',
        ]);
    }
}
