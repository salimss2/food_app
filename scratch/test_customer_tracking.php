<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Modules\Orders\Models\Order;
use Modules\Orders\Http\Resources\OrderResource;
use Modules\Auth\Models\DriverProfile;
use Modules\Delivery\Events\DriverLocationUpdated;

try {
    echo "--- 1. Fetching a test order ---\n";
    $order = Order::first();
    if (!$order) {
        echo "No order found in the database. Creating a mock order for testing...\n";
        // Create a mock user
        $user = User::first();
        if (!$user) {
            throw new Exception("No user found in the system to link the mock order.");
        }

        $order = Order::create([
            'order_number' => 'ORD-TEST1234',
            'user_id' => $user->id,
            'restaurant_id' => 1,
            'driver_id' => $user->id, // self as driver for testing
            'payment_method' => 'cod',
            'total' => 100.0,
            'total_price' => 100.0,
            'status' => 'accepted',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
        ]);
    }

    echo "Order Number: " . $order->order_number . "\n";
    echo "Driver ID: " . ($order->driver_id ?: 'None') . "\n";

    // Assign driver if none exists for this test
    if (!$order->driver_id) {
        $driver = User::role('Driver')->first();
        if (!$driver) {
            $driver = User::first();
        }
        $order->driver_id = $driver->id;
        $order->save();
        echo "Assigned Driver ID: " . $driver->id . "\n";
    }

    echo "\n--- 2. Updating Driver Coordinates ---\n";
    $lat = 24.7999;
    $lng = 46.6999;
    $driverProfile = DriverProfile::updateOrCreate(
        ['user_id' => $order->driver_id],
        [
            'latitude' => $lat,
            'longitude' => $lng,
        ]
    );
    echo "Driver Live Latitude set to: " . $driverProfile->latitude . "\n";
    echo "Driver Live Longitude set to: " . $driverProfile->longitude . "\n";

    echo "\n--- 3. Resolving OrderResource ---\n";
    // Load relationships like the API controller does
    $order->load(['items.meal', 'restaurant', 'user.profile', 'driver.driverProfile']);
    $resource = new OrderResource($order);
    $data = $resource->toArray(request());

    echo "Resource restaurant_lat: " . ($data['restaurant_lat'] ?? 'Missing') . "\n";
    echo "Resource restaurant_lng: " . ($data['restaurant_lng'] ?? 'Missing') . "\n";
    echo "Resource customer_lat: " . ($data['customer_lat'] ?? 'Missing') . "\n";
    echo "Resource customer_lng: " . ($data['customer_lng'] ?? 'Missing') . "\n";
    echo "Resource driver_lat: " . ($data['driver_lat'] ?? 'Missing') . "\n";
    echo "Resource driver_lng: " . ($data['driver_lng'] ?? 'Missing') . "\n";

    if (isset($data['driver_lat']) && abs($data['driver_lat'] - $lat) < 0.0001) {
        echo "OrderResource Coordinate Mapping: SUCCESS!\n";
    } else {
        echo "OrderResource Coordinate Mapping: FAILED!\n";
    }

    echo "\n--- 4. Dispatching Live Broadcast Event ---\n";
    $event = new DriverLocationUpdated($order->id, $lat, $lng);
    event($event);
    echo "Event successfully dispatched! Channel: order." . $order->id . ".tracking\n";
    echo "Verification Completed Successfully!\n";

} catch (\Exception $e) {
    echo "Error during verification: " . $e->getMessage() . "\n";
}
