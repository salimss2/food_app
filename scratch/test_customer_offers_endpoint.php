<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Modules\Restaurants\Http\Controllers\Api\CustomerOfferController;
use Modules\Restaurants\Models\Offer;
use Modules\Restaurants\Models\Restaurant;
use Illuminate\Support\Facades\DB;

try {
    echo "--- 1. Making sure at least one offer exists ---\n";
    $restaurant = Restaurant::first();
    if (!$restaurant) {
        // Create test restaurant if none exists
        $restaurant = Restaurant::create([
            'name' => 'مطعم السدة',
            'location' => 'Riyadh',
            'status' => 'open',
            'category' => 'برجر',
        ]);
    }

    $offer = Offer::first();
    if (!$offer) {
        $offer = Offer::create([
            'restaurant_id' => $restaurant->id,
            'title' => 'عرض الغداء المدمر',
            'description' => 'خصم 40% على جميع وجبات البرجر',
            'combo_price' => 45.00,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(5),
            'image' => 'restaurants/offers/test_banner.jpg',
        ]);
        echo "Created dummy offer for validation.\n";
    }

    echo "\n--- 2. Invoking index() on CustomerOfferController ---\n";
    $controller = new CustomerOfferController();
    $response = $controller->index();

    echo "Response status code: " . $response->getStatusCode() . "\n";
    $content = json_decode($response->getContent(), true);

    echo "\n--- 3. Formatted JSON Payload ---\n";
    echo json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";

    // Validate keys and format structure
    if (
        isset($content['status']) &&
        $content['status'] === true &&
        isset($content['data']) &&
        is_array($content['data']) &&
        (count($content['data']) === 0 || (
            isset($content['data'][0]['id']) &&
            isset($content['data'][0]['title']) &&
            array_key_exists('description', $content['data'][0]) &&
            isset($content['data'][0]['combo_price']) &&
            array_key_exists('image', $content['data'][0]) &&
            array_key_exists('restaurant', $content['data'][0])
        ))
    ) {
        echo "\nJSON Output Format Check: SUCCESS!\n";
    } else {
        echo "\nJSON Output Format Check: FAILED!\n";
    }

} catch (\Exception $e) {
    echo "Error checking endpoint: " . $e->getMessage() . "\n";
}
