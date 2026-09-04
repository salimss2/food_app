<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Modules\Admin\Http\Controllers\DiscountCodeController;

$user = User::first();
auth()->setUser($user);

echo "--- Testing Discounts Page Rendering ---\n";

$controller = new DiscountCodeController();
$view = $controller->index();

echo "DiscountCodeController@index View Name: {$view->name()}\n";

$renderedHtml = $view->render();

if (str_contains($renderedHtml, 'إدارة كودات وأكواد الخصم')) {
    echo "SUCCESS: discounts.blade.php rendered with status 200 OK!\n";
} else {
    echo "FAILED: discounts.blade.php did not render expected text.\n";
}

echo "--- Verification Completed Successfully! ---\n";
