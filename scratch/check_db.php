<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "tables:\n";
foreach (['restaurants', 'meal_restaurants', 'driver_profiles', 'drivers', 'users', 'order_ratings', 'order_reviews', 'orders'] as $tbl) {
    if (Schema::hasTable($tbl)) {
        echo "Table $tbl EXISTS. Columns: " . implode(', ', Schema::getColumnListing($tbl)) . "\n";
    } else {
        echo "Table $tbl DOES NOT exist.\n";
    }
}
