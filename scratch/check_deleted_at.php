<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

var_dump(Illuminate\Support\Facades\Schema::hasColumn('support_tickets', 'deleted_at'));
