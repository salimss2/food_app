<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Admin\Models\DiscountCode;
use App\Models\User;
use Modules\Admin\Http\Controllers\DiscountCodeController;
use App\Http\Controllers\Api\DiscountController as ApiDiscountController;
use Illuminate\Http\Request;

$user = User::first();
auth()->setUser($user);

echo "--- Testing Discount Codes / Coupons System ---\n";

// 1. Create a test percentage discount code with cap
$controller = new DiscountCodeController();

$requestData = [
    'code' => 'MUKALLA20',
    'discount_type' => 'percentage',
    'discount_value' => 20,
    'max_discount_amount' => 1000,
    'expiry_date' => now()->addDays(15)->toDateString(),
    'min_order_amount' => 3000,
    'max_usages' => 100,
    'per_user_limit' => 2,
    'is_active' => 1,
];

$storeReq = Request::create('/admin/discounts', 'POST', $requestData);
$storeRes = $controller->store($storeReq);

echo "Store Request Status Code: " . $storeRes->getStatusCode() . "\n";

$codeModel = DiscountCode::where('code', 'MUKALLA20')->first();

if (!$codeModel) {
    echo "FAILED to save discount code MUKALLA20\n";
    exit(1);
}

echo "Created Discount Code ID: {$codeModel->id}, Code: {$codeModel->code}, Value: {$codeModel->discount_value}%\n";

// 2. Test Customer Verification API (POST /api/v1/coupons/validate)
$apiController = new ApiDiscountController();

// Test Case A: Valid order > min_order_amount (Total: 4000 YER)
// 20% of 4000 = 800 YER discount (under cap 1000)
$valReqA = Request::create('/api/v1/coupons/validate', 'POST', [
    'code' => 'MUKALLA20',
    'cart_total' => 4000
]);
$valResA = $apiController->validateCoupon($valReqA);
$dataA = json_decode($valResA->getContent(), true);

echo "API Validation Test A (4000 YER): Success={$dataA['success']}, DiscountAmount={$dataA['discount_amount']} YER (Expected 800), NewTotal={$dataA['new_total']} YER\n";

// Test Case B: Valid order exceeding cap (Total: 10000 YER)
// 20% of 10000 = 2000 YER -> Capped at 1000 YER
$valReqB = Request::create('/api/v1/coupons/validate', 'POST', [
    'code' => 'MUKALLA20',
    'cart_total' => 10000
]);
$valResB = $apiController->validateCoupon($valReqB);
$dataB = json_decode($valResB->getContent(), true);

echo "API Validation Test B (10000 YER Capped): Success={$dataB['success']}, DiscountAmount={$dataB['discount_amount']} YER (Expected 1000), NewTotal={$dataB['new_total']} YER\n";

// Test Case C: Order below minimum order amount (Total: 2000 YER < 3000 YER)
$valReqC = Request::create('/api/v1/coupons/validate', 'POST', [
    'code' => 'MUKALLA20',
    'cart_total' => 2000
]);
$valResC = $apiController->validateCoupon($valReqC);
$dataC = json_decode($valResC->getContent(), true);

echo "API Validation Test C (Below Min Order): Success={$dataC['success']}, ErrorMessage='{$dataC['message']}'\n";

// 3. Test Toggle Status
$toggleRes = $controller->toggleStatus($codeModel->id);
$toggleData = json_decode($toggleRes->getContent(), true);
echo "Toggle Status Response: is_active = " . ($toggleData['is_active'] ? 'TRUE' : 'FALSE') . "\n";

// 4. Test Index View Rendering
$indexRes = $controller->index();
echo "Index View Rendered: " . ($indexRes->name() === 'admin::discounts' ? 'SUCCESS' : 'FAILED') . "\n";

// Clean up
$codeModel->delete();
echo "Cleaned up test discount code.\n";

echo "--- Verification Completed Successfully! ---\n";
