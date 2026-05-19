<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SHOW TABLES');
foreach ($tables as $t) {
    $table = current((array)$t);
    try {
        $columns = Schema::getColumnListing($table);
        foreach ($columns as $c) {
            $res = DB::table($table)->where($c, 'LIKE', '%laravel.cloud%')->first();
            if ($res) {
                echo "FOUND in table [{$table}], column [{$c}]:\n";
                print_r($res);
                echo "----------------------------------------\n";
            }
        }
    } catch (\Exception $e) {
        // Skip system/non-queryable tables
    }
}
