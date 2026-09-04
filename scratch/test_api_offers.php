<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Api\OfferController;
use Modules\Admin\Models\AdminOffer;
use App\Models\Restaurant;

try {
    echo "===================================================\n";
    echo "  Testing Customer Promotional Offers API Controller\n";
    echo "===================================================\n\n";

    // 1. Ensure at least one active offer exists in DB
    $createdDummy = false;
    $activeOffer = AdminOffer::where('status', 'active')
        ->where(function ($query) {
            $query->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now()->toDateString());
        })
        ->first();

    if (!$activeOffer) {
        $restaurant = Restaurant::first();
        if (!$restaurant) {
            $restaurant = Restaurant::create([
                'name' => 'مطعم الاختبار',
                'status' => 'open',
            ]);
        }

        $activeOffer = AdminOffer::create([
            'title'               => 'عرض ترويجي تجريبي متميز',
            'description'         => 'خصم حصري 30% على جميع وجبات العشاء',
            'type'                => 'banner',
            'click_action'        => 'restaurant',
            'banner_image'        => 'offers/test_banner.jpg',
            'discount_percentage' => 30.00,
            'original_price'      => 5000,
            'offer_price'         => 3500,
            'restaurant_id'       => $restaurant->id,
            'start_date'          => now()->toDateString(),
            'expiry_date'         => now()->addDays(10)->toDateString(),
            'status'              => 'active',
        ]);
        $createdDummy = true;
        echo "[INFO] Created temporary active offer (ID: {$activeOffer->id})\n\n";
    }

    // 2. Invoke banners() on App\Http\Controllers\Api\OfferController directly
    $controller = new OfferController();
    $response = $controller->banners();

    $statusCode = $response->getStatusCode();
    echo "Controller Response Status Code: {$statusCode}\n";

    if ($statusCode !== 200) {
        throw new \Exception("Expected HTTP 200, got {$statusCode}");
    }

    $json = json_decode($response->getContent(), true);

    echo "Status: " . ($json['status'] ? 'true' : 'false') . "\n";
    echo "Message: " . ($json['message'] ?? 'N/A') . "\n";
    echo "Active Banners Count: " . count($json['data'] ?? []) . "\n\n";

    // 3. Verify structure and absolute URLs
    echo "--- Sample JSON Payload ---\n";
    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n\n";

    if (!empty($json['data'])) {
        $first = $json['data'][0];

        // Check banner_image URL format
        if ($first['banner_image']) {
            $isAbsolute = str_starts_with($first['banner_image'], 'http://') || str_starts_with($first['banner_image'], 'https://');
            echo "Banner Image URL: {$first['banner_image']}\n";
            echo "Is Absolute URL: " . ($isAbsolute ? 'YES' : 'NO') . "\n";
            if (!$isAbsolute) {
                throw new \Exception("Banner image URL is not absolute: {$first['banner_image']}");
            }
        }

        // Check required fields
        $requiredKeys = [
            'id', 'title', 'description', 'banner_image', 'type', 'click_action',
            'restaurant_id', 'meal_id', 'original_price', 'offer_price',
            'discount_percentage', 'coupon_code', 'status', 'restaurant', 'meal'
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $first)) {
                throw new \Exception("Missing required key '{$key}' in offer data payload.");
            }
        }
        echo "[SUCCESS] All 15 required JSON keys are present in offer payload.\n";
    }

    // 4. Test HTTP endpoint via cURL if artisan serve is running
    echo "\n--- Testing live HTTP GET /api/v1/offers/banners ---\n";
    $ch = curl_init('http://127.0.0.1:8000/api/v1/offers/banners');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $httpResult = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        echo "Live HTTP Response Status Code: 200 OK\n";
        $httpJson = json_decode($httpResult, true);
        echo "Live HTTP JSON Status: " . ($httpJson['status'] ? 'true' : 'false') . "\n";
        echo "Live HTTP Banners Count: " . count($httpJson['data'] ?? []) . "\n";
    } else {
        echo "[NOTE] Live HTTP call returned status {$httpCode} (or dev server listening on another port/binding).\n";
    }

    // 5. Clean up temporary offer
    if ($createdDummy && isset($activeOffer)) {
        $activeOffer->delete();
        echo "\n[INFO] Cleaned up temporary offer.\n";
    }

    echo "\n===================================================\n";
    echo "  ALL VERIFICATION CHECKS PASSED SUCCESSFULLY!\n";
    echo "===================================================\n";

} catch (\Exception $e) {
    echo "\n[ERROR] Verification failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
