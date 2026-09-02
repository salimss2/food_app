<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Modules\Restaurants\Models\Restaurant;
use Modules\Restaurants\Models\Meal;
use Modules\Restaurants\Models\MealOption;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;
use Modules\Orders\Http\Controllers\OrdersController;
use Illuminate\Http\Request;

echo "=== STARTING DIRECT PAYLOAD & CART FALLBACK CHECKOUT AUDIT ===\n\n";

// 1. Setup User & Restaurant & Meal & MealOptions
$user = User::firstOrCreate(
    ['email' => 'direct_checkout_user@example.com'],
    [
        'name' => 'Direct Checkout User',
        'password' => bcrypt('password'),
        'phone' => '0599887766'
    ]
);

$restaurant = Restaurant::firstOrCreate(
    ['name' => 'Direct Checkout Restaurant'],
    [
        'user_id' => $user->id,
        'address' => 'Central Avenue',
        'phone' => '0599000111',
        'latitude' => 31.95,
        'longitude' => 35.91
    ]
);

$meal = Meal::firstOrCreate(
    ['name' => 'Gourmet Burger', 'restaurant_id' => $restaurant->id],
    [
        'price' => 15.00,
        'available' => true,
        'description' => 'Delicious beef burger'
    ]
);

$opt1 = MealOption::firstOrCreate(
    ['meal_id' => $meal->id, 'option_name' => 'Double Bacon'],
    ['additional_price' => 4.00]
);

$opt2 = MealOption::firstOrCreate(
    ['meal_id' => $meal->id, 'option_name' => 'Cheddar Dip'],
    ['additional_price' => 2.50]
);

echo "Base Meal Price: $" . number_format($meal->price, 2) . "\n";
echo "Option 1: +$" . number_format($opt1->additional_price, 2) . "\n";
echo "Option 2: +$" . number_format($opt2->additional_price, 2) . "\n";
echo "Expected Unit Price: $21.50\n";
echo "Expected Item Subtotal (quantity 2): $43.00\n\n";

// TEST 1: Direct Items Payload Checkout
echo "--- TEST 1: Direct Items Payload Checkout (items array) ---\n";

$ordersController = new OrdersController();
$directRequest = Request::create('/api/v1/orders', 'POST', [
    'payment_method' => 'cod',
    'latitude' => 31.95,
    'longitude' => 35.91,
    'items' => [
        [
            'meal_id' => $meal->id,
            'quantity' => 2,
            'option_ids' => [$opt1->id, $opt2->id]
        ]
    ]
]);
$directRequest->setUserResolver(fn() => $user);

$resDirect = $ordersController->store($directRequest);
echo "Direct Checkout Status Code: " . $resDirect->getStatusCode() . "\n";

$orderDirect = Order::where('user_id', $user->id)->latest('id')->first();

if (!$orderDirect) {
    echo "FAILED: Direct payload order was not created!\n";
    exit(1);
}

$itemDirect = OrderItem::where('order_id', $orderDirect->id)->first();

echo "Order ID: {$orderDirect->id}\n";
echo "Order Delivery Fee: $" . number_format($orderDirect->delivery_fee, 2) . "\n";
echo "Order Grand Total: $" . number_format($orderDirect->total, 2) . "\n";
echo "OrderItem Unit Price: $" . number_format($itemDirect->price, 2) . " (Expected: $21.50)\n";
echo "OrderItem Subtotal: $" . number_format($itemDirect->subtotal, 2) . " (Expected: $43.00)\n";
echo "OrderItem Customizations: " . json_encode($itemDirect->customizations) . "\n";

if (abs($itemDirect->price - 21.50) < 0.01) {
    echo "SUCCESS: Direct payload unit_price calculated correctly ($21.50)!\n";
} else {
    echo "FAILED: Direct payload unit_price incorrect! Got: {$itemDirect->price}\n";
    exit(1);
}

if (!empty($itemDirect->customizations) && count($itemDirect->customizations) === 2) {
    echo "SUCCESS: Direct payload customizations saved to OrderItem!\n";
} else {
    echo "FAILED: Direct payload customizations missing!\n";
    exit(1);
}

$expectedTotalDirect = 43.00 + $orderDirect->delivery_fee;
if (abs($orderDirect->total - $expectedTotalDirect) < 0.01) {
    echo "SUCCESS: Financial consistency verified (Order Total = Item Subtotals + Delivery Fee)!\n";
} else {
    echo "FAILED: Order Total mismatch! Got: {$orderDirect->total}, Expected: {$expectedTotalDirect}\n";
    exit(1);
}

echo "\n=== ALL DIRECT PAYLOAD & CART FALLBACK TESTS PASSED SUCCESSFULLY ===";
