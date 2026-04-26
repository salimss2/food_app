<?php

namespace Modules\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Models\Order;

class DeliveryController extends Controller
{
    /**
     * Driver accepts an order using pessimistic locking.
     * POST /api/v1/deliveries/orders/{id}/accept
     */
    public function acceptOrder(Request $request, $id)
    {
        $driverId = $request->user()->id; // Assuming user auth is driver

        try {
            $order = DB::transaction(function () use ($id, $driverId) {
                // Fetch with pessimistic locking to prevent race conditions
                $lockedOrder = Order::where('id', $id)->lockForUpdate()->first();

                if (!$lockedOrder) {
                    abort(404, 'الطلب غير موجود');
                }

                if ($lockedOrder->driver_id !== null) {
                    // Order is already taken
                    abort(400, 'عذرًا، تم قبول هذا الطلب من قبل مندوب آخر');
                }

                if ($lockedOrder->status !== 'pending_driver_acceptance') {
                    abort(400, 'الطلب ليس في حالة انتظار قبول المندوب');
                }

                // Update order driver and status
                $lockedOrder->driver_id = $driverId;
                $lockedOrder->status = 'driver_assigned'; 
                $lockedOrder->save();

                return $lockedOrder;
            });

            return response()->json([
                'message' => 'تم تعيين الطلب لك بنجاح',
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            return response()->json([
                'message' => $e->getMessage() ?: 'حدث خطأ أثناء قبول الطلب',
            ], $statusCode);
        }
    }
}
