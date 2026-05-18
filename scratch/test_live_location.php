<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Modules\Auth\Models\DriverProfile;
use Illuminate\Support\Facades\Schema;

try {
    echo "--- 1. Checking Database Columns in driver_profiles table ---\n";
    $columns = Schema::getColumnListing('driver_profiles');
    $hasLat = in_array('latitude', $columns);
    $hasLong = in_array('longitude', $columns);

    echo "Latitude column exists: " . ($hasLat ? 'Yes' : 'No') . "\n";
    echo "Longitude column exists: " . ($hasLong ? 'Yes' : 'No') . "\n";

    echo "\n--- 2. Simulating Driver Profile update logic ---\n";
    // Find or create a driver user
    $driver = User::role('Driver')->first();
    if (!$driver) {
        $driver = User::first();
        if ($driver) {
            $driver->assignRole('Driver');
        }
    }

    if ($driver) {
        echo "Driver found: " . $driver->name . " (ID: " . $driver->id . ")\n";

        // Create driver profile if missing
        $profile = DriverProfile::updateOrCreate(
            ['user_id' => $driver->id],
            [
                'vehicle_model' => 'Simulated Camry',
                'vehicle_plate' => 'GPS-1234',
            ]
        );

        echo "DriverProfile exists: Yes\n";

        // Test coordinates to update
        $lat = 24.7136;
        $long = 46.6753;

        // Perform the update exactly like our controller does:
        $profile = DriverProfile::where('user_id', $driver->id)->first();
        $profile->update([
            'latitude' => $lat,
            'longitude' => $long,
        ]);

        $updatedProfile = DriverProfile::where('user_id', $driver->id)->first();
        echo "Updated Latitude: " . $updatedProfile->latitude . "\n";
        echo "Updated Longitude: " . $updatedProfile->longitude . "\n";

        if (abs($updatedProfile->latitude - $lat) < 0.0001 && abs($updatedProfile->longitude - $long) < 0.0001) {
            echo "Verification SUCCESS!\n";
        } else {
            echo "Verification FAILED: Updated values do not match simulated inputs.\n";
        }
    } else {
        echo "No user exists in the database to test with.\n";
    }
} catch (\Exception $e) {
    echo "Error during verification: " . $e->getMessage() . "\n";
}
