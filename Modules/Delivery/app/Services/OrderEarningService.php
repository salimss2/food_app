<?php

namespace Modules\Delivery\Services;

use Modules\Orders\Models\Order;

class OrderEarningService // قمنا بتغيير اسم الكلاس ليكون منطقياً أكثر
{
    /**
     * Calculate and return the driver earning for an order.
     * If the earning is null, calculate it as 10% of the total.
     *
     * @param Order $order
     * @return float
     */
    public function calculateDriverEarning(Order $order): float
    {
        // التحقق من الحقل الجديد driver_earning بدلاً من commission
        if (!is_null($order->driver_earning)) {
            return (float) $order->driver_earning;
        }

        // إذا كان فارغاً، احسب الأجرة افتراضياً (مثلاً 10% من الإجمالي)
        return round((float) $order->total * 0.10, 2);
    }

    /**
     * Map a collection of orders or a single order to include the dynamically calculated driver earning.
     *
     * @param mixed $orders
     * @return mixed
     */
    public function mapOrdersWithEarning($orders)
    {
        if ($orders instanceof \Illuminate\Support\Collection) {
            return $orders->map(function ($order) {
                // تعيين القيمة للحقل الجديد
                $order->driver_earning = $this->calculateDriverEarning($order);
                return $order;
            });
        }

        if ($orders instanceof Order) {
            // تعيين القيمة للحقل الجديد
            $orders->driver_earning = $this->calculateDriverEarning($orders);
        }

        return $orders;
    }
}