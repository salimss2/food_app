<?php

namespace Modules\Delivery\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Delivery\Models\DriverStatus;
use App\Models\User;

class DriverStatusController extends Controller
{
    public function updateStatus(Request $request)
    {
        // 1. التحقق من البيانات المرسلة من Flutter
        $request->validate([
            'is_online' => 'required|boolean',
            'user_id'   => 'nullable|integer'
        ]);

        try {
            // 2. تحديد المستخدم بأمان
            $user = null;

            if ($request->user_id) {
                $user = User::find($request->user_id);
            }

            if (!$user) {
                // نفضل استخدام auth()->id() لتجنب جلب كائن المستخدم كاملاً إذا لم نكن نحتاجه
                $user = $request->user(); 
                
                // في بيئة الإنتاج الحقيقية، لا يفضل إنشاء مستخدم عشوائي هنا إذا فشل تسجيل الدخول
                if(!$user) {
                     return response()->json([
                        'status' => false,
                        'message' => 'غير مصرح لك بإجراء هذا التعديل. الرجاء تسجيل الدخول.'
                    ], 401);
                }
            }

            // 3. التحقق من وجود طلبات نشطة قبل التحول لـ "غير متصل"
            if ($request->is_online == false) {
                $hasActiveOrders = \Modules\Delivery\Models\DeliveryTask::where('driver_id', $user->id)
                    ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                    ->exists();

                if ($hasActiveOrders) {
                    return response()->json([
                        'status' => false,
                        'message' => 'لا يمكنك التحول لوضع "غير متصل" أثناء وجود طلب نشط قيد التوصيل.'
                    ], 400);
                }
            }

            // 4. تحديث أو إنشاء الحالة في جدول driver_availability
            // لاحظ استخدام driver_id بدلاً من user_id ليتوافق مع الموديل والجدول
            $status = \Modules\Delivery\Models\DriverStatus::updateOrCreate(
                ['driver_id' => $user->id],
                [
                    'is_online' => $request->is_online,
                    'last_updated' => now() // تحديث الطابع الزمني
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث حالة السائق بنجاح',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'is_online' => (bool)$status->is_online,
                    'availability' => $status->availability
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في السيرفر: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProfile()
    {
        try {
            // جلب أول مستخدم متاح بأمان
            $user = auth()->user() ?? User::first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لا يوجد مستخدمين في النظام حالياً'
                ], 404);
            }

            // 🔥 التعديل تم هنا: استخدام driver_id بدلاً من user_id
            $status = DriverStatus::where('driver_id', $user->id)->first();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'is_online' => $status ? (bool)$status->is_online : false,
                    // قمت بإضافة هذه الخطوة تحسباً إذا كان فلاتر يحتاج قراءة الـ availability أيضاً
                    'availability' => $status ? $status->availability : 'idle' 
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}