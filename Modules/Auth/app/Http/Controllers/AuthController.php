<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // أضفنا هذا للتعامل مع قاعدة البيانات مباشرة
use Google\Client;



class AuthController extends Controller
{

    // تسجيل زبون جديد
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:6|confirmed'
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'status' => 'active'
    //     ]);

    //     // إعطاء الدور
    //     $user->assignRole('customer');

    //     // حذف التوكنات القديمة (اختياري)
    //     $user->tokens()->delete();

    //     $token = $user->createToken('customer_app')->plainTextToken;

    //     return response()->json([
    //         'status' => true,
    //         'user' => $user,
    //         'roles' => $user->getRoleNames(),
    //         'token' => $token
    //     ]);
    // الدخول 


    // تسجيل زبون جديد
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active'
            ]);

            // ملاحظة: تأكد أن مودل User يحتوي على HasRoles
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('customer');
            }

            $token = $user->createToken('customer_app')->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token,
                'message' => 'User registered successfully'
            ], 201); // نرسل 201 للتأكيد على الإنشاء

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات التحقق غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // هذا السطر سينقذك! سيخبر صديقك بالضبط ما هو الخطأ في السيرفر
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في السيرفر: ' . $e->getMessage()
            ], 500);
        }
    }


    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:6|confirmed'
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password)
    //     ]);

    //     // إعطاء الدور الافتراضي
    //     $user->assignRole('customer');

    //     $token = $user->createToken('mobile_app')->plainTextToken;

    //     return response()->json([
    //         'status' => true,
    //         'user' => $user,
    //         'role' => $user->getRoleNames()->first(),
    //         'token' => $token
    //     ]);
    // }













    //  تسجيل الدخول للكل
    // public function login(Request $request)
    // {

    //     $request->validate([
    //         'email'=>'required|email',
    //         'password'=>'required'
    //     ]);

    //     $user = User::where('email',$request->email)->first();

    //     if(!$user || !Hash::check($request->password,$user->password)){
    //         return response()->json([
    //             'status'=>false,
    //             'message'=>'Invalid credentials'
    //         ],401);
    //     }

    //     $token = $user->createToken('mobile_app')->plainTextToken;

    //     return response()->json([
    //         'status'=>true,
    //         'user'=>$user,
    //         'role'=>$user->getRoleNames()->first(),
    //         'token'=>$token
    //     ]);
    // }

    //  تسجيل الدخول للزبون
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($user->status != 'active') {
            return response()->json([
                'status' => false,
                'message' => 'تم حظر حسابك. يرجى التواصل مع الإدارة.'
            ], 403);
        }

        // فحص الصلاحية والدور المخصص للتطبيق بناءً على نوع الطلب
        $appType = $request->input('app_type');
        $isAuthorized = true;

        if ($appType === 'restaurant' || $appType === 'restaurant_admin') {
            $isAuthorized = $user->hasRole('Restaurant Admin') || $user->hasRole('restaurant_admin') || $user->hasRole('Restaurant Owner') || $user->hasRole('restaurant owner');
        } elseif ($appType === 'customer') {
            $isAuthorized = $user->hasRole('Customer') || $user->hasRole('customer');
        } elseif ($appType === 'driver') {
            $isAuthorized = $user->hasRole('Driver') || $user->hasRole('driver');
        } else {
            // إذا لم يتم تحديد نوع التطبيق، نمنع الموصل من الدخول إلى تطبيق الزبائن والمطاعم
            if (($user->hasRole('Driver') || $user->hasRole('driver')) && !$user->hasAnyRole(['Customer', 'customer', 'Restaurant Admin', 'restaurant_admin', 'Restaurant Owner', 'restaurant owner'])) {
                $isAuthorized = false;
            }
        }

        if (!$isAuthorized) {
            $user->tokens()->delete();
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بالدخول إلى هذا التطبيق. يرجى استخدام التطبيق المخصص لحسابك.'
            ], 403);
        }

        // حذف التوكنات القديمة
        $user->tokens()->delete();

        $token = $user->createToken('api_token')->plainTextToken;

        // جلب ID المطعم المرتبط بهذا المستخدم (صاحب المطعم)
        $restaurantId = \Illuminate\Support\Facades\DB::table('restaurants')
            ->where('owner_id', $user->id)
            ->value('id');

        // 5. إرسال الرد النهائي لـ Flutter
        return response()->json([
            'status' => true,
            'user' => $user,
            'roles' => $user->getRoleNames(), // لجلب الأدوار من Spatie
            'token' => $token,
            'restaurant_id' => $restaurantId, // سيستلمه مبرمج فلاتر لحفظه
        ]);
    }

    // تحديث بيانات الملف الشخصي
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'location' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096'
            ]);

            // 1. تحديث البيانات في جدول users
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            $user->save();

            // 2. تجهيز بيانات جدول profiles
            $profileData = [
                'updated_at' => now()
            ];

            if ($request->has('address')) {
                $profileData['address'] = $request->address;
            }
            if ($request->has('location')) {
                $profileData['location'] = $request->location;
            }

            // 3. معالجة رفع الصورة (avatar)
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/profiles'), $imageName);
                $profileData['avatar'] = 'uploads/profiles/' . $imageName;
            }

            // 4. تحديث البروفايل أو إنشائه إذا لم يكن موجوداً
            DB::table('profiles')->updateOrInsert(
                ['user_id' => $user->id],
                $profileData
            );

            // 5. إضافة بيانات البروفايل للرد ليقوم فلاتر بتحديث الواجهة
            $user->address = $profileData['address'] ?? $request->address;
            $user->location = $profileData['location'] ?? $request->location;
            if (isset($profileData['avatar'])) {
                $user->image = $profileData['avatar'];
            }

            // في ملف لارافل - نهاية دالة update
            return response()->json([
                'status' => true,
                'message' => 'تم تحديث البيانات بنجاح',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'location' => $user->location,
                    'image' => $user->image, // المسار الجديد للصورة
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات التحقق غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في السيرفر: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }

            // جرب جلب البيانات بدون تحميل العلاقات أولاً للتأكد
            return response()->json([
                'status' => true,
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]
            ]);
        } catch (\Exception $e) {
            // هذا السطر سيطبع الخطأ في فلاتر بدلاً من إرسال null
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        auth()->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json([
            'status' => true,
            'message' => 'FCM token updated successfully.',
        ]);
    }

    /**
     * Google Sign-In for Flutter App
     */
    public function googleSignIn(\Illuminate\Http\Request $request)
    {
        try {
            $request->validate(['idToken' => 'required|string']);

            $client = new \Google_Client(['client_id' => '780792946688-bvv2ire3ac8icbfmha80ouc23qql7l46.apps.googleusercontent.com']); // معرفك الصحيح

            // تعطيل فحص SSL محلياً كما فعلنا سابقاً
            $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
            $client->setHttpClient($guzzleClient);

            // فك تشفير التوكن عبر جوجل
            $payload = $client->verifyIdToken($request->idToken);

            if ($payload) {
                // 1. استخراج بياناتك الحقيقية من حساب جوجل
                $email = $payload['email'];
                $name = $payload['name'] ?? 'مستخدم جوجل';
                $avatar = $payload['picture'] ?? null; // صورة جوجل

                // 2. البحث عن المستخدم أو إنشاء حساب جديد له
                $user = \App\Models\User::where('email', $email)->first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'email' => $email,
                        'name' => $name,
                        'password' => \Hash::make(\Str::random(24)), // كلمة مرور عشوائية معقدة
                        'status' => 'active',
                    ]);

                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('Customer');
                    }
                }

                // فحص حالة الحظر للمستخدمين عبر جوجل
                if ($user->status !== 'active') {
                    return response()->json([
                        'status' => false,
                        'message' => 'تم حظر حسابك. يرجى التواصل مع الإدارة.'
                    ], 403);
                }

                // فحص الصلاحية والدور المخصص للتطبيق بناءً على نوع الطلب
                $appType = $request->input('app_type');
                $isAuthorized = true;

                if ($appType === 'restaurant' || $appType === 'restaurant_admin') {
                    $isAuthorized = $user->hasRole('Restaurant Admin') || $user->hasRole('restaurant_admin') || $user->hasRole('Restaurant Owner') || $user->hasRole('restaurant owner');
                } elseif ($appType === 'customer') {
                    $isAuthorized = $user->hasRole('Customer') || $user->hasRole('customer');
                } elseif ($appType === 'driver') {
                    $isAuthorized = $user->hasRole('Driver') || $user->hasRole('driver');
                } else {
                    // إذا لم يتم تحديد نوع التطبيق، نمنع الموصل من الدخول إلى تطبيق الزبائن والمطاعم
                    if (($user->hasRole('Driver') || $user->hasRole('driver')) && !$user->hasAnyRole(['Customer', 'customer', 'Restaurant Admin', 'restaurant_admin', 'Restaurant Owner', 'restaurant owner'])) {
                        $isAuthorized = false;
                    }
                }

                if (!$isAuthorized) {
                    $user->tokens()->delete();
                    \Illuminate\Support\Facades\Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return response()->json([
                        'status' => false,
                        'message' => 'غير مصرح لك بالدخول إلى هذا التطبيق. يرجى استخدام التطبيق المخصص لحسابك.'
                    ], 403);
                }

                // 3. تحديث توكن الإشعارات إذا تم إرساله من فلاتر
                if ($request->has('fcm_token')) {
                    $user->fcm_token = $request->fcm_token;
                    $user->save();
                }

                // 4. إنشاء توكن حقيقي (Sanctum)
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'تم الدخول بنجاح',
                    'token' => $token, // التوكن الحقيقي
                    'user' => $user // بياناتك الحقيقية
                ], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'توكن جوجل غير صالح'], 401);
            }
        } catch (\Exception $e) {
            \Log::error('Google Sign In Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
