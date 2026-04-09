<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
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

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('customer');
            }

            DB::table('profiles')->insert([
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $token = $user->createToken('customer_app')->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
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
                    'message' => 'Account not active'
                ], 403);
            }

            $user->tokens()->delete();
            $token = $user->createToken('api_token')->plainTextToken;

            $profile = DB::table('profiles')->where('user_id', $user->id)->first();

            if ($profile) {
                $user->address = $profile->address;
                $user->location = $profile->location;
                $user->image = $profile->avatar;
            }

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    // ✅ التعديل المهم هنا
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

            // ✅ 1. تحديث user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone
            ]);

            // ✅ 2. تجهيز profile بدون شروط (الحل الأقوى)
            $profileData = [
                'address' => $request->address,
                'location' => $request->location,
                'updated_at' => now()
            ];

            // ✅ 3. رفع الصورة
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/profiles'), $imageName);

                $profileData['avatar'] = 'uploads/profiles/' . $imageName;
            }

            // ✅ 4. تحديث أو إنشاء profile
            DB::table('profiles')->updateOrInsert(
                ['user_id' => $user->id],
                $profileData
            );

            // ✅ 5. جلب البيانات النهائية
            $profile = DB::table('profiles')->where('user_id', $user->id)->first();

            $user->address = $profile->address ?? null;
            $user->location = $profile->location ?? null;
            $user->image = $profile->avatar ?? null;

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث البيانات بنجاح',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function googleSignIn(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->idToken);

            if (!$payload) {
                return response()->json(['status' => false], 401);
            }

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $picture = $payload['picture'] ?? null;

            $user = User::where('email', $email)->orWhere('google_id', $googleId)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'status' => 'active'
                ]);

                DB::table('profiles')->insert([
                    'user_id' => $user->id,
                    'avatar' => $picture,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $user->tokens()->delete();
            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}