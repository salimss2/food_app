<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('users::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('users::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
    }

    /**
     * تحديث موقع المستخدم (Latitude & Longitude)
     */
    // public function updateLocation(\Illuminate\Http\Request $request)
    // {
    //     $request->validate([
    //         'latitude' => 'required|numeric',
    //         'longitude' => 'required|numeric',
    //     ]);

    //     $user = $request->user();

    //     // Update the profile if it exists, or create a new one with the location data
    //     $user->profile()->updateOrCreate(
    //         ['user_id' => $user->id],
    //         [
    //             'latitude' => $request->latitude,
    //             'longitude' => $request->longitude,
    //         ]
    //     );

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'تم تحديث الموقع بنجاح'
    //     ]);
    // }

    public function updateLocation(\Illuminate\Http\Request $request)
    {
        // 1. التحقق من صحة البيانات القادمة من فلاتر
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();

        // 2. تحديث الإحداثيات في جدول profiles
        // نستخدم updateOrCreate لكي نتفادى أي خطأ في حال كان المستخدم ليس لديه صف في جدول profiles بعد
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id], // ابحث عن بروفايل هذا المستخدم
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ] // قم بتحديث أو إضافة هذه الإحداثيات
        );

        // 3. إرسال رد النجاح لتطبيق فلاتر
        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الموقع في الملف الشخصي بنجاح'
        ]);
    }
}
