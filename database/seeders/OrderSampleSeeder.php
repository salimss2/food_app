<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تأكد من وجود مستخدم ومطعم وسائق في قاعدة البيانات أولاً
        $userId = \App\Models\User::first()?->id ?? 1;
        $restaurantId = \Modules\Restaurants\Models\Restaurant::first()?->id ?? 1;
        $driverId = \App\Models\User::role('Driver')->first()?->id ?? 2;

        $samples = [
            [
                'user_id' => $userId,
                'restaurant_id' => $restaurantId,
                'status' => 'pending_admin_approval',
                'payment_status' => 'pending_verification', // تأكد من أنها حروف صغيرة وبدون مسافات
                'payment_method' => 'bank_transfer',
                'total' => 150.00,
            ],
            [
                'user_id' => $userId,
                'restaurant_id' => $restaurantId,
                'status' => 'pending_driver_acceptance',
                'payment_status' => 'pending_collection',   // تأكد من الحروف الصغيرة والشرطة السفلية
                'payment_method' => 'cod',
                'total' => 85.50,
            ],
            [
                'user_id' => $userId,
                'restaurant_id' => $restaurantId,
                'driver_id' => $driverId,
                'status' => 'on_the_way',
                'payment_status' => 'pending_collection',   // مطابقة للـ ENUM
                'payment_method' => 'cod',
                'total' => 200.00,
            ]
        ];

        foreach ($samples as $sample) {
            \Modules\Orders\Models\Order::create($sample + [
                'order_number' => 'ORD-' . rand(1000, 9999),
                'delivery_address' => 'عنوان تجريبي - شارع الرشيد - بناية 5', // تم تعديل الاسم هنا
                'delivery_location' => '30.0444,31.2357',                   // تم تعديل الاسم هنا
            ]);
        }
    }
}
