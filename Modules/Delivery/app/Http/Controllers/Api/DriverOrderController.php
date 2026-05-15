<?php

namespace Modules\Delivery\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Orders\Models\Order;
use Modules\Delivery\Models\DeliveryTask;
use Carbon\Carbon;
use Modules\Delivery\Services\OrderEarningService; // 1. تم تعديل اسم الـ Service هنا

class DriverOrderController extends Controller
{
    protected $earningService; // 2. تغيير اسم المتغير ليكون منطقياً

    // 3. حقن الـ Service بالاسم الجديد
    public function __construct(OrderEarningService $earningService)
    {
        $this->earningService = $earningService;
    }

    public function getAvailableOrders()
    {
        $orders = Order::with(['user', 'restaurant', 'items.meal', 'payment'])
            ->where('status', 'pending_driver_acceptance')
            ->whereDoesntHave('deliveryTask') // تأكيد عدم وجود سائق لهذا الطلب (driver_id == null)
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. استخدام الدالة الجديدة
        $orders = $this->earningService->mapOrdersWithEarning($orders);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function getOrderDetails($id)
    {
        $order = Order::with(['user', 'restaurant', 'items.meal', 'items.options.mealOption', 'payment'])
            ->findOrFail($id);

        // 5. استخدام الدالة الجديدة
        $order = $this->earningService->mapOrdersWithEarning($order);

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    public function acceptOrder(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                // 1. Fetch order with pessimistic lock to prevent race conditions
                $order = Order::lockForUpdate()->find($id);

                if (!$order) {
                    return response()->json([
                        'status' => false,
                        'message' => 'عذراً، لم يتم العثور على هذا الطلب!'
                    ], 404);
                }

                // 2. Validation: Ensure order is still in 'pending_driver_acceptance' state
                if ($order->status !== 'pending_driver_acceptance' || !is_null($order->driver_id)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'عذراً، لقد سبقك موصل آخر وقبل هذا الطلب!'
                    ], 400);
                }

                $driverId = Auth::id() ?? 1; // Fallback for testing if needed

                // 3. Update Order Status to 'accepted'
                $order->update([
                    'status' => 'accepted',
                    'driver_id' => $driverId
                ]);

                // 4. Create Delivery Task
                DeliveryTask::create([
                    'order_id' => $order->id,
                    'driver_id' => $driverId,
                    'status' => 'on_way',
                    'pickup_time' => now(),
                ]);

                // 5. Log Tracking Event
                DB::table('order_tracking')->insert([
                    'order_id' => $order->id,
                    'status' => 'accepted_by_driver',
                    'updated_at' => now(),
                ]);

                // 6. Update Driver Status to 'busy' (now safe in ENUM)
                \Modules\Users\Models\DriverStatus::updateOrCreate(
                    ['driver_id' => $driverId],
                    ['availability' => 'busy']
                );

                // 7. Standardized API Response
                return response()->json([
                    'status' => true,
                    'message' => 'تم قبول الطلب بنجاح، بالتوفيق في رحلتك!',
                    'data' => [
                        'order_id' => (int) $order->id,
                        'new_status' => 'accepted',
                        'driver_status' => 'busy'
                    ]
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ فني أثناء قبول الطلب، يرجى المحاولة لاحقاً',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function completeOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $task = DeliveryTask::where('order_id', $id)
            ->where('status', '!=', 'delivered')
            ->first();

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'عذراً، لم يتم العثور على مهمة توصيل نشطة لهذا الطلب'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // 1. تحديث حالة الطلب الأساسي
            $order->update(['status' => 'delivered']);

            // 2. تحديث حالة مهمة التوصيل
            $task->update([
                'status' => 'delivered',
                'delivery_time' => now()
            ]);

            // 3. إضافة سجل التتبع
            DB::table('order_tracking')->insert([
                'order_id' => $order->id,
                'status' => 'delivered',
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسليم الطلب بنجاح، عمل رائع!',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إتمام عملية التسليم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory()
    {
        $history = DeliveryTask::where('driver_id', Auth::id())
            ->where('status', 'delivered')
            ->with(['order.user', 'order.restaurant', 'order.payment'])
            ->orderBy('delivery_time', 'desc')
            ->get();

        // 6. استخدام الدالة الجديدة داخل الـ Loop
        $history->transform(function ($task) {
            if ($task->order) {
                $task->order = $this->earningService->mapOrdersWithEarning($task->order);
            }
            return $task;
        });

        return response()->json([
            'status' => true,
            'data' => $history
        ]);
    }
}