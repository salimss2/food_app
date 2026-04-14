<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Modules\Auth\Models\Driver;
use Modules\Auth\Models\DriverProfile;
use App\Models\User; // أو مسار موديل المستخدم عندك
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // 🔥 لإدارة تخزين الملفات

class DriverAuthController extends Controller
{
    // --- 1. تسجيل الدخول ---
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'phone'    => 'required',
    //         'password' => 'required',
    //     ]);

    //     $driver = Driver::where('phone', $request->phone)->first();

    //     if (!$driver || !Hash::check($request->password, $driver->password)) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'بيانات الدخول غير صحيحة'
    //         ], 401);
    //     }

    //     $token = $driver->createToken('DriverToken')->plainTextToken;

    //     return response()->json([
    //         'token' => $token,
    //         'user'  => $driver 
    //     ], 200);
    // }


    public function login(Request $request)
    {
        // 1. التحقق من البيانات المدخلة
        $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        // 2. البحث عن المستخدم في جدول users الأساسي عبر رقم الهاتف
        $user = User::where('phone', $request->phone)->first();

        // 3. التحقق من وجود المستخدم، صحة كلمة المرور، وأنه يمتلك دور "موصل"
        // ملاحظة: استخدمنا $user->hasRole('Driver') للتأكد أن الزبون لا يمكنه الدخول من تطبيق السائقين
        if (!$user || !Hash::check($request->password, $user->password) || !$user->hasRole('Driver')) {
            return response()->json([
                'status'  => false,
                'message' => 'بيانات الدخول غير صحيحة أو ليس لديك صلاحية الوصول'
            ], 401);
        }

        // 4. التحقق من حالة الحساب (اختياري ولكن مهم)
        if ($user->status !== 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'هذا الحساب معطل، يرجى التواصل مع الإدارة'
            ], 403);
        }

        // 5. إنشاء التوكن (Sanctum)
        $token = $user->createToken('DriverToken')->plainTextToken;

        // 6. إرجاع البيانات مع ملف الموصل (المركبة وغيرها)
        return response()->json([
            'status' => true,
            'token'  => $token,
            'user'   => $user->load('driverProfile') // تحميل بيانات المركبة مع المستخدم
        ], 200);
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {

            $profile = $user->driverProfile;

            // حذف الصورة القديمة إذا موجودة
            if ($profile && $profile->avatar_url) {
                $oldPath = str_replace(asset('storage/'), '', $profile->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $fullUrl = asset('storage/' . $path);

            // ✅ الحل: إنشاء أو تحديث البروفايل
            $user->driverProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['avatar_url' => $fullUrl]
            );

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الصورة بنجاح ✅',
                'avatar_url' => $fullUrl
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'لم يتم إرسال ملف'
        ], 400);
    }
   public function getProfile(Request $request)
{
    $user = $request->user();

    // تحميل البروفايل
    $user->load('driverProfile');

    return response()->json([
        'status' => true,
        'data' => [
            'id'        => $user->id,
            'name'      => $user->name,
            'phone'     => $user->phone,
            'email'     => $user->email,

            // 👇 هذه أهم نقطة
            'id_number' => optional($user->driverProfile)->id_number,
            'address'   => optional($user->driverProfile)->address,
        ]
    ]);
}

    public function updateVehicle(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'vehicle_model' => 'required|string',
            'vehicle_plate' => 'required|string',
            'vehicle_vin'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ الحل: إنشاء أو تحديث driver_profile
        $user->driverProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['vehicle_model', 'vehicle_plate', 'vehicle_vin'])
        );

        $user->load('driverProfile');

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث بيانات المركبة بنجاح',
            'data' => [
                'vehicle_model' => optional($user->driverProfile)->vehicle_model,
                'vehicle_plate' => optional($user->driverProfile)->vehicle_plate,
                'vehicle_vin' => optional($user->driverProfile)->vehicle_vin,
            ]
        ]);
    }
    // // --- 2. تحديث الصورة الشخصية (الأفاتار) ---
    // public function updateAvatar(Request $request)
    // {
    //     $driver = $request->user();

    //     $validator = Validator::make($request->all(), [
    //         'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // حد أقصى 2 ميجا
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     if ($request->hasFile('avatar')) {
    //         // حذف الصورة القديمة إذا وجدت لتوفير مساحة السيرفر
    //         if ($driver->avatar_url) {
    //             // استخراج اسم الملف من الرابط وحذفه
    //             $oldPath = str_replace(asset('storage/'), '', $driver->avatar_url);
    //             Storage::disk('public')->delete($oldPath);
    //         }

    //         // تخزين الصورة الجديدة في مجلد avatars داخل storage/app/public
    //         $path = $request->file('avatar')->store('avatars', 'public');

    //         // توليد الرابط الكامل للصورة
    //         $fullUrl = asset('storage/' . $path);

    //         // تحديث الحقل في قاعدة البيانات
    //         $driver->update(['avatar_url' => $fullUrl]);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'تم تحديث الصورة بنجاح ✅',
    //             'avatar_url' => $fullUrl // هذا هو الحقل الذي ينتظره Flutter
    //         ]);
    //     }

    //     return response()->json(['status' => false, 'message' => 'لم يتم إرسال ملف'], 400);
    // }

    // // --- 3. تحديث الملف الشخصي ---
    // public function updateProfile(Request $request)
    // {
    //     $driver = $request->user();

    //     $validator = Validator::make($request->all(), [
    //         'phone'   => 'required|unique:drivers,phone,' . $driver->id,
    //         'email'   => 'nullable|email|unique:drivers,email,' . $driver->id,
    //         'address' => 'nullable|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     $driver->update($request->only(['phone', 'email', 'address']));

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'تم تحديث البيانات الشخصية بنجاح',
    //         'user' => $driver
    //     ]);
    // }

    // // --- 4. تحديث بيانات المركبة ---
    // public function updateVehicle(Request $request)
    // {
    //     $driver = $request->user();

    //     $validator = Validator::make($request->all(), [
    //         'vehicle_model' => 'required|string',
    //         'vehicle_plate' => 'required|string',
    //         'vehicle_vin'   => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     $driver->update($request->only(['vehicle_model', 'vehicle_plate', 'vehicle_vin']));

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'تم تحديث بيانات المركبة بنجاح',
    //         'user' => $driver
    //     ]);
    // }
}
