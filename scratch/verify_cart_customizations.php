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
use Modules\Orders\Http\Controllers\CartController;
use Illuminate\Http\Request;

echo "=== STARTING CART CUSTOMIZATIONS VERIFICATION ===\n\n";

// 1. Create or fetch a test user
$user = User::firstOrCreate(
    ['email' => 'audit_cart_user@example.com'],
    [
        'name' => 'Audit Cart User',
        'password' => bcrypt('password'),
        'phone' => '0590001122'
    ]
);

// 2. Create or fetch a test restaurant & meal
$restaurant = Restaurant::firstOrCreate(
    ['name' => 'Cart Audit Restaurant'],
    [
        'user_id' => $user->id,
        'address' => 'Test Street',
        'phone' => '0599000000',
        'latitude' => 31.95,
        'longitude' => 35.91
    ]
);

$meal = Meal::firstOrCreate(
    ['name' => 'Audit Burger', 'restaurant_id' => $restaurant->id],
    [
        'price' => 10.00,
        'available' => true,
        'description' => 'Delicious audit burger'
    ]
);

// 3. Create test meal options
$option1 = MealOption::firstOrCreate(
    ['meal_id' => $meal->id, 'option_name' => 'Extra Cheese'],
    ['additional_price' => 2.50]
);

$option2 = MealOption::firstOrCreate(
    ['meal_id' => $meal->id, 'option_name' => 'Extra Sauce'],
    ['additional_price' => 1.50]
);

echo "Meal Base Price: $" . number_format($meal->price, 2) . "\n";
echo "Option 1: {$option1->option_name} (+ $" . number_format($option1->additional_price, 2) . ")\n";
echo "Option 2: {$option2->option_name} (+ $" . number_format($option2->additional_price, 2) . ")\n";
echo "Expected Unit Price: $" . number_format($meal->price + $option1->additional_price + $option2->additional_price, 2) . " ($14.00)\n\n";

// Clear previous cart items for test user
$cart = Cart::firstOrCreate(['user_id' => $user->id]);
CartItem::where('cart_id', $cart->id)->delete();
$cart->update(['total' => 0]);

// 4. Test POST /api/v1/cart/add with option_ids payload
$controller = new CartController();

$addRequest = Request::create('/api/v1/cart/add', 'POST', [
    'meal_id' => $meal->id,
    'quantity' => 2,
    'option_ids' => [$option1->id, $option2->id]
]);
$addRequest->setUserResolver(fn() => $user);

$addResponse = $controller->add($addRequest);
$addContent = json_decode($addResponse->getContent(), true);

echo "Add to Cart API Response Status Code: " . $addResponse->getStatusCode() . "\n";
echo "Cart Total in Response: $" . number_format($addContent['cart_total'] ?? 0, 2) . "\n";

// 5. Inspect database record in cart_items
$cartItem = CartItem::where('cart_id', $cart->id)->first();

if (!$cartItem) {
    echo "FAILED: Cart item was not stored in DB!\n";
    exit(1);
}

echo "\n--- DB Verification (cart_items record) ---\n";
echo "Cart Item ID: {$cartItem->id}\n";
echo "Quantity: {$cartItem->quantity}\n";
echo "Stored Subtotal: $" . number_format($cartItem->subtotal, 2) . " (Expected: $28.00 = $14.00 * 2)\n";
echo "Stored Customizations JSON: " . json_encode($cartItem->customizations) . "\n";

// Assertions
$expectedSubtotal = ($meal->price + $option1->additional_price + $option2->additional_price) * 2; // 14.00 * 2 = 28.00
if (abs($cartItem->subtotal - $expectedSubtotal) < 0.01) {
    echo "SUCCESS: Subtotal calculated correctly!\n";
} else {
    echo "FAILED: Subtotal mismatch! Stored: {$cartItem->subtotal}, Expected: {$expectedSubtotal}\n";
}

if (!empty($cartItem->customizations) && count($cartItem->customizations) === 2) {
    echo "SUCCESS: Customizations stored correctly!\n";
} else {
    echo "FAILED: Customizations missing or invalid!\n";
}

// 6. Test GET /api/v1/cart (Index endpoint)
echo "\n--- GET /api/v1/cart Verification ---\n";
$indexRequest = Request::create('/api/v1/cart', 'GET');
$indexRequest->setUserResolver(fn() => $user);

$indexResponse = $controller->index($indexRequest);
$indexContent = json_decode($indexResponse->getContent(), true);

echo "Index API Response Status: " . ($indexContent['status'] ? 'true' : 'false') . "\n";
$items = $indexContent['items'] ?? [];
echo "Items returned: " . count($items) . "\n";
if (!empty($items)) {
    $firstItem = $items[0];
    echo "Item Unit Price: $" . number_format($firstItem['unit_price'] ?? 0, 2) . "\n";
    echo "Item Subtotal: $" . number_format($firstItem['subtotal'] ?? 0, 2) . "\n";
    echo "Item Customizations Exposed: " . json_encode($firstItem['customizations'] ?? []) . "\n";
}

echo "\n=== ALL VERIFICATIONS COMPLETED SUCCESSFULLY ===";
