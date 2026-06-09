<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Modules\Orders\Models\Order;
use Modules\Orders\Http\Controllers\OrdersController;

try {
    echo "--- 1. Fetching a test order ---\n";
    $order = Order::first();
    if (!$order) {
        throw new Exception("No order exists in the database to test.");
    }
    echo "Using Order ID: " . $order->id . " (Order Number: " . $order->order_number . ")\n";

    echo "\n--- 2. Invoking the track() controller method ---\n";
    $controller = new OrdersController();
    $response = $controller->track($order->id);

    echo "Response status code: " . $response->getStatusCode() . "\n";
    $content = json_decode($response->getContent(), true);

    echo "\n--- 3. Verifying exact JSON payload structure ---\n";
    echo json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

    // Structural checks
    if (
        isset($content['status']) && 
        $content['status'] === true &&
        isset($content['data']) &&
        isset($content['data']['order_id']) &&
        isset($content['data']['status']) &&
        array_key_exists('restaurant_lat', $content['data']) &&
        array_key_exists('restaurant_lng', $content['data']) &&
        array_key_exists('customer_lat', $content['data']) &&
        array_key_exists('customer_lng', $content['data']) &&
        array_key_exists('driver', $content['data'])
    ) {
        echo "\nJSON Structure Verification: SUCCESS!\n";
    } else {
        echo "\nJSON Structure Verification: FAILED! One or more required fields are missing.\n";
    }

} catch (\Exception $e) {
    echo "Error during verification: " . $e->getMessage() . "\n";
}
