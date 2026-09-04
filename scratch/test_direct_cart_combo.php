<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Admin\Models\AdminOffer;
use App\Models\User;
use Modules\Restaurants\Models\Meal;
use Modules\Admin\Http\Controllers\AdminOfferController;
use Illuminate\Http\Request;

$user = User::first();
auth()->setUser($user);

echo "--- Testing Direct Cart Offer Submission with Missing Expiry Date & Offer Price ---\n";

// Fetch or create sample meal
$meal = Meal::first();
if (!$meal) {
    echo "No meal found in DB.\n";
    exit(1);
}

echo "Testing with Meal ID: {$meal->id}, Meal Name: {$meal->name}, Meal Restaurant ID: {$meal->restaurant_id}, Meal Price: {$meal->price}\n";

// 1. Simulate POST request without expiry_date, without offer_price, without restaurant_id
$requestData = [
    'title' => 'عرض وجبة كومبو تضاف للسلة مباشرة',
    'description' => 'تذوق أشهى المأكولات بسعر مخفض',
    'type' => 'direct_cart',
    'click_action' => 'cart',
    'discount_percentage' => 20,
    'original_price' => $meal->price,
    // expiry_date, offer_price, restaurant_id left intentionally blank
    'meal_id' => $meal->id,
];

$request = Request::create('/admin/offers', 'POST', $requestData);
$controller = new AdminOfferController();

$response = $controller->store($request);

echo "Store Request Status Code: " . $response->getStatusCode() . "\n";

// 2. Fetch created offer
$createdOffer = AdminOffer::where('title', 'عرض وجبة كومبو تضاف للسلة مباشرة')->latest()->first();

if ($createdOffer) {
    echo "SUCCESS: Offer saved cleanly!\n";
    echo "ID: {$createdOffer->id}\n";
    echo "Title: {$createdOffer->title}\n";
    echo "Type: {$createdOffer->type}\n";
    echo "Inferred Restaurant ID: {$createdOffer->restaurant_id} (Expected: {$meal->restaurant_id})\n";
    echo "Original Price: {$createdOffer->original_price}\n";
    echo "Discount Percentage: {$createdOffer->discount_percentage}%\n";
    echo "Calculated Offer Price: {$createdOffer->offer_price}\n";
    echo "Calculated Expiry Date: {$createdOffer->expiry_date}\n";
    echo "Start Date: {$createdOffer->start_date}\n";

    // Clean up
    $createdOffer->delete();
    echo "Cleaned up test offer.\n";
} else {
    echo "FAILURE: Offer was not saved.\n";
}

echo "--- Test Completed Successfully! ---\n";
