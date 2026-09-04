<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('admin_offers');
echo "admin_offers columns: " . implode(', ', $columns) . "\n";

$comboColumns = \Illuminate\Support\Facades\Schema::getColumnListing('restaurant_offers');
if (empty($comboColumns)) {
    $comboColumns = \Illuminate\Support\Facades\Schema::getColumnListing('offers');
}
echo "restaurant offers columns: " . implode(', ', $comboColumns) . "\n";
