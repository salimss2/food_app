<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email', // أزلنا unique مؤقتاً للتجربة
            'password' => 'required'
        ]);

        // 2. إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password) // استخدم Hash::make بدلاً من bcrypt
        ]);

        // 3. إنشاء التوكن
        $token = $user->createToken('mobile_app')->plainTextToken;

        // 4. الرد السريع
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * تسجيل دخول مستخدم موجود مسبقاً.
     */
    public function login(Request $request)
    {
        // 1. التحقق من البيانات المرسلة
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. البحث عن المستخدم في قاعدة البيانات عبر البريد الإلكتروني
        $user = User::where('email', $request->email)->first();

        // 3. التحقق من وجود المستخدم ومطابقة كلمة المرور المشفرة
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة، يرجى المحاولة مرة أخرى.'
            ], 401);
        }

        // 4. إنشاء توكن جديد لجلسة الدخول الحالية
        $token = $user->createToken('mobile_app')->plainTextToken;

        // 5. الرد بنجاح العملية مع إرجاع بيانات المستخدم والتوكن
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * تسجيل خروج المستخدم وحذف التوكن الحالي.
     */
    public function logout(Request $request)
    {
        // مسح التوكن الحالي الذي استخدمه المستخدم للدخول
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح.'
        ], 200);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('auth::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('auth::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
