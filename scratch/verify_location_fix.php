<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Users\Http\Controllers\ProfileController;
use Modules\Orders\Http\Controllers\OrdersController;
use App\Models\User;
use Modules\Restaurants\Models\Restaurant;
use Modules\Orders\Models\Cart;
use Modules\Orders\Models\CartItem;
use Modules\Restaurants\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

Event::fake();

echo "=== Verification Script Started ===\n\n";

// 1. Test Coordinate String Parsing
echo "1. Testing parseCoordinates helper:\n";
$coords1 = ProfileController::parseCoordinates("N, 49.1231° E 14.5425°");
echo "   Sample 1 ('N, 49.1231° E 14.5425°'): " . json_encode($coords1) . "\n";
assert($coords1 && $coords1['latitude'] == 49.1231 && $coords1['longitude'] == 14.5425, "Coords 1 failed");

$coords2 = ProfileController::parseCoordinates("30.0444, 31.2357");
echo "   Sample 2 ('30.0444, 31.2357'): " . json_encode($coords2) . "\n";
assert($coords2 && $coords2['latitude'] == 30.0444 && $coords2['longitude'] == 31.2357, "Coords 2 failed");

$coords3 = ProfileController::parseCoordinates("Plain text without numbers");
echo "   Sample 3 ('Plain text without numbers'): " . json_encode($coords3) . "\n\n";
assert($coords3 === null, "Coords 3 failed");

// 2. Test Customer Location Missing Validation Error
echo "2. Testing Order Checkout with missing customer location:\n";
$user = User::factory()->create();
// Create profile with null lat/long and no valid location string
$user->profile()->create([
    'location' => 'Unknown area',
    'latitude' => null,
    'longitude' => null,
]);

$controller = new OrdersController();
$reqMissing = Request::create('/api/v1/orders', 'POST', [
    'payment_method' => 'cod'
]);
$reqMissing->setUserResolver(fn() => $user);

$resMissing = $controller->store($reqMissing);
$contentMissing = json_decode($resMissing->getContent(), true);
echo "   Response Status Code: " . $resMissing->getStatusCode() . "\n";
echo "   Response Payload: " . json_encode($contentMissing, JSON_UNESCAPED_UNICODE) . "\n";
assert($resMissing->getStatusCode() == 422, "Status code should be 422");
assert(($contentMissing['message'] ?? '') === 'يرجى تحديد موقع التوصيل الخاص بك', "Error message mismatch");
echo "   => PASS: Returned exact 422 response message.\n\n";

// 3. Test Order Checkout with Missing Restaurant Coordinates (Fallback check)
echo "3. Testing Order Checkout when Restaurant coordinates are NULL:\n";
// Update user profile with valid coordinates
$user->profile->update([
    'latitude' => 30.0444,
    'longitude' => 31.2357,
]);
$user->refresh();

// Create test restaurant with null coordinates
$restaurant = Restaurant::create([
    'name' => 'Test Fallback Restaurant ' . time(),
    'user_id' => $user->id,
    'owner_id' => $user->id,
    'latitude' => null,
    'longitude' => null,
    'status' => 'open'
]);

$meal = Meal::create([
    'restaurant_id' => $restaurant->id,
    'name' => 'Test Meal',
    'price' => 25.00,
    'is_available' => true,
]);

// Put meal in cart
$cart = Cart::firstOrCreate(['user_id' => $user->id]);
CartItem::create([
    'cart_id' => $cart->id,
    'meal_id' => $meal->id,
    'quantity' => 1,
    'subtotal' => 25.00,
]);

$reqCheckout = Request::create('/api/v1/orders', 'POST', [
    'payment_method' => 'cod'
]);
$reqCheckout->setUserResolver(fn() => $user);

$resCheckout = $controller->store($reqCheckout);
$contentCheckout = json_decode($resCheckout->getContent(), true);
echo "   Response Status Code: " . $resCheckout->getStatusCode() . "\n";
echo "   Response Payload: " . json_encode($contentCheckout, JSON_UNESCAPED_UNICODE) . "\n";
assert($resCheckout->getStatusCode() == 201, "Status code should be 201 for order creation");
echo "   => PASS: Order created successfully with fallback delivery rate when restaurant coords are NULL!\n\n";

// Cleanup
CartItem::where('cart_id', $cart->id)->delete();
$cart->delete();
$meal->delete();
$restaurant->delete();
$user->profile()->delete();
$user->delete();

echo "=== All Verification Checks Passed Successfully! ===\n";
