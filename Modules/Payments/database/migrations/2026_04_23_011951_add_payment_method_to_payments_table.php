<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cod', 'bank_transfer'])->after('order_id')->nullable();
        });

        // 1. Convert any existing unsupported data to default status
        \Illuminate\Support\Facades\DB::statement("
            UPDATE payments 
            SET payment_status = 'pending_verification' 
            WHERE payment_status NOT IN (
                'pending_verification', 
                'pending_collection', 
                'completed', 
                'rejected',
                'canceled',
                'pending_refund',
                'refunded'
            )
        ");

        // 2. Apply new ENUM values
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE payments 
            MODIFY COLUMN payment_status ENUM(
                'pending_verification', 
                'pending_collection', 
                'completed', 
                'rejected',
                'canceled',
                'pending_refund',
                'refunded'
            ) NOT NULL DEFAULT 'pending_verification'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE payments 
            MODIFY COLUMN payment_status ENUM(
                'pending',
                'confirmed',
                'rejected',
                'cash_pending',
                'paid'
            ) NOT NULL DEFAULT 'pending'
        ");

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
