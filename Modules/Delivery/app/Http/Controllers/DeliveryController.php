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
            // Atomic UPDATE to prevent race conditions
            $updated = Order::where('id', $id)
                ->whereNull('driver_id')
                ->where('status', 'pending_driver_acceptance')
                ->update([
                    'driver_id' => $driverId,
                    'status' => 'driver_assigned'
                ]);

            if ($updated === 0) {
                // If update failed, check why to provide a graceful error
                $order = Order::find($id);
                
                if (!$order) {
                    abort(404, 'الطلب غير موجود');
                }
                
                if ($order->driver_id !== null) {
                    abort(400, 'عذراً، قام موصل آخر بقبول هذا الطلب قبلك.');
                }
                
                if ($order->status !== 'pending_driver_acceptance') {
                    abort(400, 'الطلب ليس في حالة انتظار قبول المندوب');
                }
            }

            // Fetch the successfully claimed order
            $order = Order::find($id);

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
