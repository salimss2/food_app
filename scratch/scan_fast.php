<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking restaurants logo:\n";
$res = DB::table('restaurants')->where('logo', 'LIKE', '%http%')->get();
print_r($res->toArray());

echo "\nChecking orders receipt_image:\n";
$ord = DB::table('orders')->where('receipt_image', 'LIKE', '%http%')->get();
print_r($ord->toArray());

echo "\nChecking users profile_picture:\n";
$usr = DB::table('users')->where('profile_picture', 'LIKE', '%http%')->get();
print_r($usr->toArray());

echo "\nDone!\n";
