<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Modules\Admin\Http\Controllers\DiscountCodeController;

$user = User::first();
auth()->setUser($user);

$controller = new DiscountCodeController();
$view = $controller->index();
$html = $view->render();

if (str_contains($html, 'generateRandomPromoCode()') && str_contains($html, 'توليد تلقائي')) {
    echo "VERIFIED: Promo Code Generator button rendered in discountModal!\n";
} else {
    echo "FAILED: Promo Code Generator button NOT found!\n";
}
