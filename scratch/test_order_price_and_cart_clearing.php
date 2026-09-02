<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Modules\Restaurants\Models\Restaurant;
use Modules\Restaurants\Models\Meal;
use Modules\Restaurants\Models\MealOption;
use Modules\Orders\Models\Cart;
use Modules\Orders\Models\CartItem;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;
use Modules\Orders\Http\Controllers\CartController;
use Modules\Orders\Http\Controllers\OrdersController;
use Illuminate\Http\Request;

echo "=== STARTING ORDER CREATION & CART CLEARING VERIFICATION ===\n\n";

// 1. Setup Test User
$user = User::firstOrCreate(
    ['email' => 'checkout_audit_user@example.com'],
    [
        'name' => 'Checkout Audit User',
        'password' => bcrypt('password'),
        'phone' => '0599112233'
    ]
);

// 2. Setup Test Restaurant & Meal & Options
$restaurant = Restaurant::firstOrCreate(
    ['name' => 'Checkout Audit Restaurant'],
    [
        'user_id' => $user->id,
        'address' => 'Main Street',
        'phone' => '0599111222',
        'latitude' => 31.95,
        'longitude' => 35.91
    ]
);

$meal = Meal::firstOrCreate(
    ['name' => 'Customized Pizza', 'restaurant_id' => $restaurant->id],
    [
        'price' => 12.00,
        'available' => true,
        'description' => 'Pizza with custom toppings'
    ]
);

$option1 = MealOption::firstOrCreate(
    ['meal_id' => $meal->id, 'option_name' => 'Extra Cheese'],
    ['additional_price' => 3.00]
);

$option2 = MealOption::firstOrCreate(
    ['meal_id' => $meal->id, 'option_name' => 'Olives'],
    ['additional_price' => 2.00]
);

echo "Base Meal Price: $" . number_format($meal->price, 2) . "\n";
echo "Option 1: +$" . number_format($option1->additional_price, 2) . "\n";
echo "Option 2: +$" . number_format($option2->additional_price, 2) . "\n";
echo "Expected Unit Price: $17.00\n";
echo "Expected Item Subtotal (qty 2): $34.00\n\n";

// 3. Add to Cart via CartController
$cartController = new CartController();
$addRequest = Request::create('/api/v1/cart/add', 'POST', [
    'meal_id' => $meal->id,
    'quantity' => 2,
    'option_ids' => [$option1->id, $option2->id]
]);
$addRequest->setUserResolver(fn() => $user);
$cartController->add($addRequest);

$cart = Cart::where('user_id', $user->id)->first();
$itemsCountBefore = CartItem::where('cart_id', $cart->id)->count();
echo "Cart Items before checkout: {$itemsCountBefore}\n";

// 4. Perform Order Creation via OrdersController@store
$ordersController = new OrdersController();
$orderRequest = Request::create('/api/v1/orders', 'POST', [
    'payment_method' => 'cod',
    'latitude' => 31.95,
    'longitude' => 35.91,
]);
$orderRequest->setUserResolver(fn() => $user);

$orderResponse = $ordersController->store($orderRequest);
$orderContent = json_decode($orderResponse->getContent(), true);

echo "Order Store Status Code: " . $orderResponse->getStatusCode() . "\n";

// 5. Verify Created Order and OrderItems in DB
$latestOrder = Order::where('user_id', $user->id)->latest('id')->first();

if (!$latestOrder) {
    echo "FAILED: Order was not created in DB!\n";
    exit(1);
}

echo "\n--- DB Order Verification ---\n";
echo "Order ID: {$latestOrder->id}\n";
echo "Order Number: {$latestOrder->order_number}\n";
echo "Delivery Fee: $" . number_format($latestOrder->delivery_fee, 2) . "\n";
echo "Order Grand Total: $" . number_format($latestOrder->total, 2) . " (Expected: $34.00 + $" . number_format($latestOrder->delivery_fee, 2) . " = $" . number_format(34.00 + $latestOrder->delivery_fee, 2) . ")\n";

$orderItem = OrderItem::where('order_id', $latestOrder->id)->first();
if (!$orderItem) {
    echo "FAILED: OrderItem record missing!\n";
    exit(1);
}

echo "OrderItem ID: {$orderItem->id}\n";
echo "OrderItem Unit Price: $" . number_format($orderItem->price, 2) . " (Expected: $17.00)\n";
echo "OrderItem Subtotal: $" . number_format($orderItem->subtotal, 2) . " (Expected: $34.00)\n";
echo "OrderItem Customizations: " . json_encode($orderItem->customizations) . "\n";

// Assertions on Order & OrderItem
if (abs($orderItem->price - 17.00) < 0.01) {
    echo "SUCCESS: OrderItem unit_price correctly preserved ($17.00)!\n";
} else {
    echo "FAILED: OrderItem unit_price incorrect! Got: {$orderItem->price}\n";
}

if (!empty($orderItem->customizations) && count($orderItem->customizations) === 2) {
    echo "SUCCESS: Customizations explicitly copied to OrderItem!\n";
} else {
    echo "FAILED: Customizations missing on OrderItem!\n";
}

$expectedGrandTotal = 34.00 + $latestOrder->delivery_fee;
if (abs($latestOrder->total - $expectedGrandTotal) < 0.01) {
    echo "SUCCESS: Order Grand Total recalculated strictly (subtotal + delivery_fee)!\n";
} else {
    echo "FAILED: Order Grand Total mismatch! Got: {$latestOrder->total}, Expected: {$expectedGrandTotal}\n";
}

// 6. Verify Cart Cleanup
$itemsCountAfter = CartItem::where('cart_id', $cart->id)->count();
$cartAfter = Cart::find($cart->id);

echo "\n--- Cart Cleanup Verification ---\n";
echo "Cart Items after checkout: {$itemsCountAfter}\n";
echo "Cart Total after checkout: $" . number_format($cartAfter->total, 2) . "\n";

if ($itemsCountAfter === 0 && (float) $cartAfter->total === 0.0) {
    echo "SUCCESS: Cart items cleared and cart total reset to 0!\n";
} else {
    echo "FAILED: Cart was not cleared properly after checkout!\n";
    exit(1);
}

echo "\n=== ALL CHECKS PASSED SUCCESSFULLY ===";
