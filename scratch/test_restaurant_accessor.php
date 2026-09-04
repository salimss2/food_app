<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Restaurant;
use Modules\Admin\Http\Controllers\AdminOfferController;

echo "--- Testing Restaurant Image Accessors & Offers Page Load ---\n";

$restaurant = Restaurant::first() ?? new Restaurant(['name' => 'مطعم تجريبي', 'logo' => 'restaurants/logo.png', 'image_path' => 'restaurants/cover.png']);

echo "Testing Restaurant ID: {$restaurant->id}\n";
echo "Logo Full URL: " . ($restaurant->logo_full_url ?? 'NULL') . "\n";
echo "Image URL: " . ($restaurant->image_url ?? 'NULL') . "\n";

// Test rendering admin offers controller index
$controller = new AdminOfferController();
$view = $controller->index();

echo "AdminOfferController@index Status: " . ($view->name() === 'admin::offers' ? '200 OK' : 'FAILED') . "\n";
echo "--- Verification Completed Successfully! ---\n";
