<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Api\DeliveryCalculationController;
use Illuminate\Http\Request;
use App\Models\DistanceSlab;

echo "=== STARTING DISTANCE CALCULATION & COVERAGE FIX VERIFICATION ===\n\n";

// Ensure at least one DistanceSlab exists for testing
$slab = DistanceSlab::firstOrCreate(
    ['min_distance' => 0.00, 'max_distance' => 5.00],
    ['total_fee' => 10.00, 'driver_share' => 7.00, 'platform_share' => 3.00]
);

$controller = new DeliveryCalculationController();

// Test 1: Null Coordinates Fallback
echo "Test 1: Testing null coordinates fallback...\n";
$reqNull = Request::create('/api/delivery/calculate-fee', 'POST', [
    'restaurant_lat' => null,
    'restaurant_lng' => null,
    'customer_lat' => null,
    'customer_lng' => null,
]);

$resNull = $controller->calculate($reqNull);
$dataNull = json_decode($resNull->getContent(), true);

echo "Status Code: " . $resNull->getStatusCode() . "\n";
echo "Response JSON: " . json_encode($dataNull) . "\n";

if ($resNull->getStatusCode() === 200 && ($dataNull['status'] ?? false) === true && ($dataNull['distance_km'] ?? 0) == 2.0) {
    echo "SUCCESS: Null coordinates fallback to 2.0 km base distance passed!\n\n";
} else {
    echo "FAILED: Null coordinates fallback failed!\n\n";
    exit(1);
}

// Test 2: Exact Boundary Match (0 km)
echo "Test 2: Testing exact 0 km boundary match...\n";
$reqZero = Request::create('/api/delivery/calculate-fee', 'POST', [
    'restaurant_lat' => 31.95,
    'restaurant_lng' => 35.91,
    'customer_lat' => 31.95,
    'customer_lng' => 35.91,
]);

$resZero = $controller->calculate($reqZero);
$dataZero = json_decode($resZero->getContent(), true);

echo "Status Code: " . $resZero->getStatusCode() . "\n";
echo "Response JSON: " . json_encode($dataZero) . "\n";

if ($resZero->getStatusCode() === 200 && ($dataZero['status'] ?? false) === true) {
    echo "SUCCESS: 0 km boundary match passed!\n\n";
} else {
    echo "FAILED: 0 km boundary match failed!\n\n";
    exit(1);
}

// Test 3: Extreme Distance (Fallback Slab)
echo "Test 3: Testing extreme distance out-of-bounds (1000 km)...\n";
$reqExtreme = Request::create('/api/delivery/calculate-fee', 'POST', [
    'restaurant_lat' => 31.95,
    'restaurant_lng' => 35.91,
    'customer_lat' => 40.71,
    'customer_lng' => -74.00,
]);

$resExtreme = $controller->calculate($reqExtreme);
$dataExtreme = json_decode($resExtreme->getContent(), true);

echo "Status Code: " . $resExtreme->getStatusCode() . "\n";
echo "Response JSON: " . json_encode($dataExtreme) . "\n";

if ($resExtreme->getStatusCode() === 200 && ($dataExtreme['status'] ?? false) === true) {
    echo "SUCCESS: Extreme distance fallback passed (never throws coverage error)!\n\n";
} else {
    echo "FAILED: Extreme distance fallback failed!\n\n";
    exit(1);
}

echo "=== ALL DISTANCE CALCULATION TESTS PASSED SUCCESSFULLY ===\n";
