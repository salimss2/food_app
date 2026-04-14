<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');
$user = App\Models\User::find(1); // Assuming ID 1 exists and is admin
if ($user) {
    App::make('auth')->login($user);
}
try {
    $response = $kernel->handle($request);
    echo "STATUS: " . $response->status() . "\n";
    if ($response->exception) {
        echo "EXCEPTION: " . $response->exception->getMessage() . "\n";
        echo "FILE: " . $response->exception->getFile() . ":" . $response->exception->getLine() . "\n";
    }
} catch (\Throwable $e) {
    echo "FATAL: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
