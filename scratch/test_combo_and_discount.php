<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Modules\Restaurants\Models\Offer;
use Modules\Restaurants\Models\Meal;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;
use Illuminate\Support\Facades\DB;

// Ensure we have a customer user
$customer = User::where('email', 'customer@gmail.com')->first();
if (!$customer) {
    // Or just grab any user
    $customer = User::first();
}

if (!$customer) {
    echo "No customer found\n";
    exit(1);
}

// Make sure the customer has latitude and longitude in their profile
$profile = $customer->profile;
if (!$profile) {
    $profile = $customer->profile()->create([
        'latitude' => 33.5138,
        'longitude' => 36.2913,
        'address' => 'Damascus'
    ]);
} else {
    $profile->update([
        'latitude' => 33.5138,
        'longitude' => 36.2913
    ]);
}

// Fetch or create a restaurant
$restaurant = \App\Models\Restaurant::first();
if (!$restaurant) {
    $restaurant = \App\Models\Restaurant::create([
        'name' => 'Test Restaurant',
        'latitude' => 33.5100,
        'longitude' => 36.2900,
        'status' => 'active'
    ]);
}

// Ensure there is a DistanceSlab
$slab = \App\Models\DistanceSlab::first();
if (!$slab) {
    \App\Models\DistanceSlab::create([
        'min_distance' => 0.00,
        'max_distance' => 10.00,
        'total_fee' => 1000.00,
        'driver_share' => 800.00,
        'platform_share' => 200.00
    ]);
}

// Ensure we have an active Offer
$offer = Offer::first();
if (!$offer) {
    $offer = Offer::create([
        'restaurant_id' => $restaurant->id,
        'title' => 'عرض نهاية الأسبوع',
        'description' => 'شاورما مع بروست',
        'combo_price' => 7000.00,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
    ]);
} else {
    $offer->update([
        'combo_price' => 7000.00,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
    ]);
}

// Ensure the offer has some meals in the pivot table
$meal1 = Meal::first();
if (!$meal1) {
    $meal1 = Meal::create([
        'restaurant_id' => $restaurant->id,
        'name' => 'شاورما',
        'price' => 4000.00,
    ]);
}
$meal2 = Meal::skip(1)->first();
if (!$meal2) {
    $meal2 = Meal::create([
        'restaurant_id' => $restaurant->id,
        'name' => 'بروست',
        'price' => 4500.00,
    ]);
}

DB::table('meal_offer')->updateOrInsert(
    ['offer_id' => $offer->id, 'meal_id' => $meal1->id],
    ['quantity' => 1]
);
DB::table('meal_offer')->updateOrInsert(
    ['offer_id' => $offer->id, 'meal_id' => $meal2->id],
    ['quantity' => 2]
);

echo "Simulating order creation for a Combo Offer...\n";
// Let's create a Request object and send it to our store method
$request = Illuminate\Http\Request::create('/v1/orders', 'POST', [
    'payment_method' => 'cod',
    'offer_id' => $offer->id,
    'quantity' => 2
]);
$request->setUserResolver(function () use ($customer) {
    return $customer;
});

$controller = app(\Modules\Orders\Http\Controllers\OrdersController::class);
$response = $controller->store($request);

echo "Response Status Code: " . $response->getStatusCode() . "\n";
$responseData = json_decode($response->getContent(), true);
echo "Response Message: " . ($responseData['message'] ?? 'None') . "\n";

if ($response->getStatusCode() === 201) {
    echo "Combo Order created successfully!\n";
    $orders = $responseData['orders'] ?? [];
    if (!empty($orders)) {
        $firstOrder = $orders[0];
        echo "Order Total: " . $firstOrder['total'] . "\n";
        $items = $firstOrder['items'] ?? [];
        foreach ($items as $item) {
            echo "Item Name: " . $item['name'] . "\n";
            echo "Item Price: " . $item['price'] . "\n";
            echo "Item Type: " . $item['type'] . "\n";
            echo "Item Combo Meals:\n";
            print_r($item['combo_meals']);
        }
    }
} else {
    echo "Failed to create order: " . $response->getContent() . "\n";
}
