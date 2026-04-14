<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Users\Models\DriverStatus;
use App\Models\User;


class DriverStatusController extends Controller
{
    public function updateStatus(Request $request)
{
    // 1. التحقق من البيانات
    $request->validate([
        'is_online' => 'required|boolean',
        'user_id'   => 'nullable|integer'
    ]);

    try {
        // 2. تحديد المعرف (ID) بأكثر طريقة آمنة ومختصرة
        // نأخذ الـ ID من الطلب، إذا لم يوجد نأخذه من التوكن، إذا لم يوجد نأخذ أول مستخدم (للتجارب فقط)
        $userId = $request->user_id ?? auth()->id() ?? User::first()?->id;

        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'لم يتم العثور على مستخدم'], 404);
        }

        // 3. التحديث في الجدول القديم driver_availability
        // لاحظ استخدمنا driver_id وليس user_id لأن هذا هو العمود في جدولك القديم
        $status = \Modules\Users\Models\DriverStatus::updateOrCreate(
            ['driver_id' => $userId], 
            [
                'is_online' => $request->is_online,
                'availability' => $request->is_online ? 'idle' : 'break',
                'last_updated' => now()
            ]
        );

        // جلب بيانات المستخدم للرد (Response)
        $user = User::find($userId);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث حالة الموصل بنجاح',
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

            $status = DriverStatus::where('user_id', $user->id)->first();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'is_online' => $status ? (bool)$status->is_online : false
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