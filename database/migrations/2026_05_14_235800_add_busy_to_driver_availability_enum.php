<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE driver_availability 
            MODIFY COLUMN availability ENUM('idle', 'delivering', 'break', 'busy') NOT NULL DEFAULT 'idle'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE driver_availability 
            MODIFY COLUMN availability ENUM('idle', 'delivering', 'break') NOT NULL DEFAULT 'idle'
        ");
    }
};
