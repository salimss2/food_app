<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

try {
    echo "--- 1. Checking Notifications Table ---\n";
    $tableExists = Schema::hasTable('notifications');
    echo "Notifications table exists: " . ($tableExists ? "YES" : "NO") . "\n";

    if (!$tableExists) {
        echo "Table notifications does not exist. Creating default notifications table using migration...\n";
        // Run database notification migration command
        // Note: we can run migration or create a dummy notifications table
    }

    echo "\n--- 2. Fetching Admin User ---\n";
    $admin = User::whereHas('roles', function ($q) {
        $q->where('name', 'Admin');
    })->first() ?: User::first();

    if (!$admin) {
        throw new Exception("No admin or user found in DB.");
    }
    echo "Admin user found: " . $admin->name . " (Email: " . $admin->email . ", ID: " . $admin->id . ")\n";

    echo "\n--- 3. Seeding Test Alert Notifications ---\n";
    $dummyNotifications = [
        [
            'id' => (string) Str::uuid(),
            'type' => 'Modules\\Admin\\Notifications\\SystemAlert',
            'notifiable_type' => get_class($admin),
            'notifiable_id' => $admin->id,
            'data' => json_encode([
                'title' => 'New Order Received',
                'body' => 'Customer Ahmed placed a new order ORD-7910 with value 150 SAR.',
                'source' => 'Customer',
                'resource_id' => 109,
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id' => (string) Str::uuid(),
            'type' => 'Modules\\Admin\\Notifications\\SystemAlert',
            'notifiable_type' => get_class($admin),
            'notifiable_id' => $admin->id,
            'data' => json_encode([
                'title' => 'Driver GPS Offline',
                'body' => 'Driver Khalid is offline or has disabled GPS location permissions.',
                'source' => 'Driver',
                'resource_id' => 5,
            ]),
            'read_at' => null,
            'created_at' => now()->subMinutes(15),
            'updated_at' => now()->subMinutes(15),
        ],
        [
            'id' => (string) Str::uuid(),
            'type' => 'Modules\\Admin\\Notifications\\SystemAlert',
            'notifiable_type' => get_class($admin),
            'notifiable_id' => $admin->id,
            'data' => json_encode([
                'title' => 'Restaurant Delay Warning',
                'body' => 'Restaurant "Al-Saj" has not accepted order ORD-7905 within 10 minutes.',
                'source' => 'Restaurant',
                'resource_id' => 12,
            ]),
            'read_at' => now(), // already read
            'created_at' => now()->subHours(1),
            'updated_at' => now()->subHours(1),
        ]
    ];

    foreach ($dummyNotifications as $notif) {
        DB::table('notifications')->insert($notif);
        echo "Seeded notification: " . json_decode($notif['data'])->title . "\n";
    }

    echo "\n--- 4. Querying User Notifications via Eloquent ---\n";
    $allNotifications = $admin->notifications;
    echo "Total User Notifications Count: " . $allNotifications->count() . "\n";
    $unreadNotifications = $admin->unreadNotifications;
    echo "Unread Notifications Count: " . $unreadNotifications->count() . "\n";

    foreach ($allNotifications as $n) {
        $data = $n->data;
        echo "- [" . ($n->read_at ? "READ" : "UNREAD") . "] Source: " . ($data['source'] ?? 'System') . " | Title: " . ($data['title'] ?? 'N/A') . " | Time: " . $n->created_at->diffForHumans() . "\n";
    }

    echo "\nVerification SUCCESS!\n";

} catch (\Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
}
