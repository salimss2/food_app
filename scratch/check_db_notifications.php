<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \Illuminate\Support\Facades\DB::table('notifications')
    ->take(10)
    ->get();

echo "Existing Notifications in DB:\n";
foreach ($rows as $row) {
    echo "ID: {$row->id} | Type: {$row->type}\n";
    echo "Data: {$row->data}\n";
    echo "---------------------------\n";
}
