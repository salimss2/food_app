<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE orders 
            MODIFY COLUMN status ENUM(
                'pending_admin_approval', 
                'pending_driver_acceptance', 
                'accepted',
                'driver_assigned', 
                'ready_for_pickup', 
                'on_the_way', 
                'delivered', 
                'canceled'
            ) NOT NULL DEFAULT 'pending_admin_approval'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE orders 
            MODIFY COLUMN status ENUM(
                'pending_admin_approval', 
                'pending_driver_acceptance', 
                'driver_assigned', 
                'ready_for_pickup', 
                'on_the_way', 
                'delivered', 
                'canceled'
            ) NOT NULL DEFAULT 'pending_admin_approval'
        ");
    }
};
