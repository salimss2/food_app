<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderReview;
use Modules\Restaurants\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get or ensure baseline User, Restaurant, and Driver
        $user = User::first() ?? User::create([
            'name' => 'Default Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'phone' => '0500000000',
        ]);

        $driver = User::role('Driver')->first() ?? User::first();

        $restaurants = Restaurant::all();
        if ($restaurants->isEmpty()) {
            $restaurant = Restaurant::create([
                'name' => 'مطعم الياسمين الشامي',
                'location' => 'الرياض - حي الملقا',
                'status' => 'open',
                'category' => 'شرقي',
                'rating' => 4.80,
                'rating_count' => 15,
            ]);
            $restaurants = collect([$restaurant]);
        }

        // Generate 6 months of historical orders if orders count is low
        if (Order::count() < 15) {
            $paymentMethods = ['cash', 'bank_transfer'];
            $statuses = ['delivered', 'delivered', 'delivered', 'delivered', 'delivered', 'delivered', 'canceled', 'accepted'];
            $comments = [
                'طعام ممتاز وتوصيل في الوقت المحدد!',
                'الوجبة ساخنة والخدمة رائعة جداً.',
                'التوصيل سريع والسائق محترم للغاية.',
                'جودة الوجبات ممتازة شكراً لكم.',
                'تجربة رائعة وسأكرر الطلب بالتأكيد.',
            ];

            for ($i = 5; $i >= 0; $i--) {
                $monthDate = Carbon::now()->subMonths($i);
                $daysInMonth = $monthDate->daysInMonth;

                // Create 8-12 orders per month
                $ordersCount = rand(8, 12);
                for ($j = 0; $j < $ordersCount; $j++) {
                    $day = rand(1, min(28, $daysInMonth));
                    $hour = rand(11, 23); // Peak hours around lunch/dinner
                    $minute = rand(0, 59);

                    $createdAt = Carbon::create($monthDate->year, $monthDate->month, $day, $hour, $minute, 0);
                    $acceptedAt = (clone $createdAt)->addMinutes(rand(2, 5));
                    $confirmedAt = (clone $acceptedAt);
                    $prepMinutes = rand(12, 25);
                    $readyAt = (clone $confirmedAt)->addMinutes($prepMinutes);
                    $deliveryMinutes = rand(15, 35);
                    $deliveredAt = (clone $readyAt)->addMinutes($deliveryMinutes);

                    $status = $statuses[array_rand($statuses)];
                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                    $total = rand(45, 280) + (rand(0, 99) / 100);
                    $restaurant = $restaurants->random();

                    $order = Order::create([
                        'order_number' => 'ORD-' . $createdAt->format('ymd') . '-' . rand(1000, 9999),
                        'user_id' => $user->id,
                        'restaurant_id' => $restaurant->id,
                        'driver_id' => $driver ? $driver->id : null,
                        'payment_method' => $paymentMethod,
                        'status' => $status,
                        'payment_status' => $status === 'delivered' ? 'completed' : ($status === 'canceled' ? 'canceled' : 'pending_verification'),
                        'total' => $total,
                        'total_price' => $total,
                        'delivery_fee' => rand(10, 25),
                        'driver_earning' => rand(8, 20),
                        'platform_commission' => $total * 0.10,
                        'accepted_at' => $status !== 'pending' ? $acceptedAt : null,
                        'confirmed_at' => $status !== 'pending' ? $confirmedAt : null,
                        'ready_at' => in_array($status, ['delivered', 'on_the_way']) ? $readyAt : null,
                        'delivered_at' => $status === 'delivered' ? $deliveredAt : null,
                        'created_at' => $createdAt,
                        'updated_at' => $deliveredAt,
                    ]);

                    // Add review for delivered orders
                    if ($status === 'delivered') {
                        $restaurantRating = rand(4, 5);
                        $driverRating = rand(4, 5);
                        $mealsRating = rand(4, 5);

                        OrderReview::create([
                            'order_id' => $order->id,
                            'user_id' => $user->id,
                            'restaurant_id' => $restaurant->id,
                            'driver_id' => $driver ? $driver->id : null,
                            'meals_rating' => $mealsRating,
                            'driver_rating' => $driverRating,
                            'restaurant_rating' => $restaurantRating,
                            'comment' => $comments[array_rand($comments)],
                            'created_at' => $deliveredAt,
                            'updated_at' => $deliveredAt,
                        ]);
                    }
                }
            }
        }
    }
}
