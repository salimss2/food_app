<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Admin\Models\AdminOffer;
use App\Models\User;
use Modules\Admin\Http\Controllers\AdminOfferController;

$user = User::first();
auth()->setUser($user);

echo "--- Testing Promotional Offers & Combo Meals Module ---\n";

// 1. Create temporary banner offer
$offer = AdminOffer::create([
    'title' => 'عرض ترويجي مؤقت للاختبار',
    'description' => 'خصم 30% على وجبات السحور والعشاء',
    'type' => 'banner',
    'click_action' => 'restaurant',
    'discount_percentage' => 30.00,
    'original_price' => 4000,
    'offer_price' => 2800,
    'expiry_date' => now()->addDays(5)->toDateString(),
    'start_date' => now()->toDateString(),
    'status' => 'active',
]);

echo "Created Offer ID: {$offer->id}, Title: {$offer->title}\n";

// 2. Test AJAX Toggle Status
$controller = new AdminOfferController();
$toggleRes = $controller->toggleStatus($offer->id);
$toggleData = json_decode($toggleRes->getContent(), true);

echo "Toggle Status Response: New Status = {$toggleData['new_status']}\n";

// 3. Test Index View Rendering
$indexRes = $controller->index();
echo "Index View Rendered: " . ($indexRes->name() === 'admin::offers' ? 'SUCCESS' : 'FAILED') . "\n";

// 4. Clean up test offer
$offer->delete();
echo "Cleaned up test offer.\n";

echo "--- Verification Completed Successfully! ---\n";
