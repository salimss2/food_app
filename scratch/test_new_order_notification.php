<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Modules\Orders\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderAdminNotification;

try {
    echo "--- 1. Querying an Admin User ---\n";
    $admin = User::whereHas('roles', function ($q) {
        $q->where('name', 'Admin');
    })->first() ?: User::first();

    if (!$admin) {
        throw new Exception("No admin found in database.");
    }
    echo "Admin found: " . $admin->name . "\n";

    echo "\n--- 2. Fetching/Creating an Order ---\n";
    $order = Order::with('user')->first();
    if (!$order) {
        // Create a dummy order
        $order = Order::create([
            'order_number' => 'ORD-TEST-99',
            'group_id' => 'group-test',
            'user_id' => $admin->id,
            'restaurant_id' => 1,
            'total' => 250.00,
            'status' => 'pending_driver_acceptance',
            'payment_status' => 'pending_collection',
        ]);
        $order->load('user');
        echo "Created dummy order for testing.\n";
    }
    echo "Order found: Number " . ($order->order_number ?? $order->id) . " | Placed by: " . ($order->user->name ?? 'N/A') . "\n";

    echo "\n--- 3. Triggering NewOrderAdminNotification ---\n";
    $admin->notify(new NewOrderAdminNotification($order));
    echo "Notification triggered successfully!\n";

    echo "\n--- 4. Verifying the Database Record ---\n";
    $latestNotification = $admin->notifications()->first();
    if ($latestNotification) {
        $data = $latestNotification->data;
        echo "Notification ID: " . $latestNotification->id . "\n";
        echo "Title: " . ($data['title'] ?? 'N/A') . "\n";
        echo "Body: " . ($data['body'] ?? 'N/A') . "\n";
        echo "Source: " . ($data['source'] ?? 'N/A') . "\n";
        echo "Action URL: " . ($data['action_url'] ?? 'N/A') . "\n";

        if (
            ($data['title'] === 'طلب جديد من زبون! 🍔') &&
            ($data['source'] === 'Customer') &&
            isset($data['action_url'])
        ) {
            echo "\nDatabase Notification Seeding: SUCCESS!\n";
        } else {
            echo "\nDatabase Notification Seeding: FAILED!\n";
        }
    } else {
        echo "No notification record found in database.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
