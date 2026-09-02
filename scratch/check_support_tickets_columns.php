<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\DB::statement("ALTER TABLE support_tickets MODIFY status ENUM('pending', 'in_progress', 'resolved', 'rejected', 'closed') NOT NULL DEFAULT 'pending';");
Illuminate\Support\Facades\DB::statement("ALTER TABLE support_tickets MODIFY type ENUM('complaint', 'inquiry') NOT NULL DEFAULT 'inquiry';");

echo "Altered status and type ENUM columns successfully.\n";
