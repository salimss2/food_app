<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // استيراد مودل المستخدم الأساسي
use Modules\Auth\Models\DriverProfile; // استيراد مودل السائق الفرعي
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DriverAuthController extends Controller
{
    // --- 1. تسجيل الدخول ---
    public function login(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Driver Login Payload:', $request->all());

        $request->validate([
            'phone' => 'required',
            'password' => 'required',
            'fcm_token' => 'nullable|string',
        ]);

        // البحث في جدول users وجلب بيانات البروفايل معه
        $user = User::with('driverProfile')->where('phone', $request->phone)->first();

        // التحقق من صحة البيانات
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        // Update FCM token if provided
        if ($request->has('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $user->createToken('DriverToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('driverProfile') // Ensure profile is fresh
        ], 200);
    }

    // --- 2. تحديث الصورة الشخصية (الأفاتار) ---
    public function updateAvatar(Request $request)
    {
        $user = $request->user(); // الآن هذا يعود بـ User Model

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // حد أقصى 2 ميجا
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('avatar')) {
            // جلب البروفايل أو إنشائه إن لم يكن موجوداً
            $profile = $user->driverProfile()->firstOrCreate(['user_id' => $user->id]);

            // حذف الصورة القديمة
            if ($profile->avatar_url) {
                $oldPath = str_replace(asset('storage/'), '', $profile->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            // تخزين الصورة الجديدة
            $path = $request->file('avatar')->store('avatars', 'public');
            $fullUrl = asset('storage/' . $path);

            // تحديث البروفايل
            $profile->update(['avatar_url' => $fullUrl]);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الصورة بنجاح ✅',
                'avatar_url' => $fullUrl
            ]);
        }

        return response()->json(['status' => false, 'message' => 'لم يتم إرسال ملف'], 400);
    }

    // --- 3. جلب الملف الشخصي (Flat JSON structure for Flutter) ---
    public function getProfile(Request $request)
    {
        $user = clone $request->user();
        $profile = $user->driverProfile;

        return response()->json([
            'status' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'id_number' => $profile->id_number ?? null,
                'address' => $profile->address ?? null,
                'avatar_url' => $profile->avatar_url ?? null,
                'vehicle_model' => $profile->vehicle_model ?? null,
                'vehicle_plate' => $profile->vehicle_plate ?? null,
                'vehicle_vin' => $profile->vehicle_vin ?? null,
                'latitude' => $profile->latitude ?? null,
                'longitude' => $profile->longitude ?? null,
            ]
        ], 200);
    }

    // --- 4. تحديث الملف الشخصي ---
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:users,phone,' . $user->id, // تم التعديل لجدول users
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // 1. تحديث البيانات الأساسية (User)
            $user->phone = $request->phone;
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            $user->save();

            // 2. تحديث البيانات الفرعية (DriverProfile)
            $profile = $user->driverProfile()->firstOrCreate(['user_id' => $user->id]);

            if ($request->hasFile('avatar')) {
                if ($profile->avatar_url) {
                    $oldPath = str_replace(asset('storage/'), '', $profile->avatar_url);
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $profile->avatar_url = asset('storage/' . $path);
            }

            if ($request->has('address')) {
                $profile->address = $request->address;
            }

            $profile->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث البيانات الشخصية بنجاح',
                'user' => $user->load('driverProfile') // تحميل البروفايل المحدث لإرجاعه للتطبيق
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // --- 4. تحديث بيانات المركبة ---
    public function updateVehicle(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'vehicle_model' => 'required|string',
            'plate_number' => 'required|string',
            'vehicle_vin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $profile = $user->driverProfile()->firstOrCreate(['user_id' => $user->id]);

        $profile->vehicle_model = $request->vehicle_model;
        $profile->vehicle_plate = $request->plate_number;
        $profile->vehicle_vin = $request->vehicle_vin;
        $profile->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث بيانات المركبة بنجاح',
            'user' => $user->load('driverProfile')
        ]);
    }

    // --- 5. تحديث الموقع الجغرافي (Live Location) ---
    public function updateLocation(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // تحديث أو إنشاء الموقع الجغرافي في جدول profiles بسطر واحد
        $user->driverProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => '📡 Location Sync: Success',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    }
}