<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Modules\Users\Models\Profile;

try {
    // 1. Check if columns exist
    $columns = Schema::getColumnListing('profiles');
    $hasLat = in_array('latitude', $columns);
    $hasLong = in_array('longitude', $columns);

    echo "Latitude column exists: " . ($hasLat ? 'Yes' : 'No') . "\n";
    echo "Longitude column exists: " . ($hasLong ? 'Yes' : 'No') . "\n";

    // 2. Check relationship
    $user = User::first();
    if ($user) {
        echo "User found: " . $user->name . "\n";
        $profile = $user->profile;
        echo "Profile exists: " . ($profile ? 'Yes' : 'No') . "\n";

        // 3. Test updateLocation logic (simulated)
        $lat = 24.7136;
        $long = 46.6753;

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'latitude' => $lat,
                'longitude' => $long,
            ]
        );

        $updatedProfile = $user->fresh()->profile;
        echo "Updated Latitude: " . $updatedProfile->latitude . "\n";
        echo "Updated Longitude: " . $updatedProfile->longitude . "\n";

        if ($updatedProfile->latitude == $lat && $updatedProfile->longitude == $long) {
            echo "Verification SUCCESS!\n";
        } else {
            echo "Verification FAILED: Values don't match.\n";
        }
    } else {
        echo "No user found to test relationship.\n";
    }
} catch (\Exception $e) {
    echo "Error during verification: " . $e->getMessage() . "\n";
}
