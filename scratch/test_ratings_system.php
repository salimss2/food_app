<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OrderRating;
use App\Models\User;
use Modules\Restaurants\Models\Restaurant;
use Modules\Auth\Models\DriverProfile;
use Modules\Orders\Models\Order;

echo "--- Testing Ratings & Reviews System ---\n";

// Find or create test order
$order = Order::first();
if (!$order) {
    echo "No order found in database.\n";
    exit;
}

echo "Testing with Order #{$order->id} (User: {$order->user_id}, Restaurant: {$order->restaurant_id}, Driver: " . ($order->driver_id ?? 'None') . ")\n";

// Cleanup existing ratings for this order if any
OrderRating::where('order_id', $order->id)->delete();

// Submit rating
$rating = OrderRating::create([
    'order_id' => $order->id,
    'user_id' => $order->user_id,
    'restaurant_id' => $order->restaurant_id,
    'driver_id' => $order->driver_id,
    'meals_rating' => 5,
    'driver_rating' => 4,
    'restaurant_rating' => 5,
    'comment' => 'Outstanding food and super prompt delivery!',
]);

echo "Created OrderRating ID #{$rating->id}\n";

// Check Restaurant Aggregates
$restaurant = Restaurant::find($order->restaurant_id);
if ($restaurant) {
    echo "Restaurant ID #{$restaurant->id} => rating_avg: {$restaurant->rating_avg}, rating_count: {$restaurant->rating_count}\n";
}

// Check Driver Aggregates
if ($order->driver_id) {
    $driverProfile = DriverProfile::where('user_id', $order->driver_id)->first();
    if ($driverProfile) {
        echo "Driver User ID #{$order->driver_id} => rating_avg: {$driverProfile->rating_avg}, rating_count: {$driverProfile->rating_count}\n";
    }
}

// Test Analytics API Controller
$analyticsController = new \App\Http\Controllers\Admin\RatingManagementController();
$analyticsResponse = $analyticsController->analytics();
echo "Analytics Output: " . json_encode($analyticsResponse->getData()) . "\n";

// Test Index API Controller
$request = new \Illuminate\Http\Request();
$indexResponse = $analyticsController->index($request);
echo "Listing Output Count: " . count($indexResponse->getData()->data) . "\n";

echo "--- Verification Completed Successfully ---\n";
