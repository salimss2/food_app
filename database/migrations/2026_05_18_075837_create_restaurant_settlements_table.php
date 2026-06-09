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
        // Proactively add commission_rate to restaurants if it doesn't exist
        if (!Schema::hasColumn('restaurants', 'commission_rate')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->decimal('commission_rate', 5, 2)->default(15.00)->after('status');
            });
        }

        // Create the restaurant_settlements table
        Schema::create('restaurant_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_number')->unique();
            $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->decimal('gross_revenue', 12, 2);
            $table->decimal('system_cut', 12, 2);
            $table->decimal('net_payable', 12, 2);
            $table->timestamps();
        });

        // Link orders to restaurant_settlements
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('restaurant_settlement_id')->nullable()->constrained('restaurant_settlements')->onDelete('set null')->after('settlement_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['restaurant_settlement_id']);
            $table->dropColumn('restaurant_settlement_id');
        });

        Schema::dropIfExists('restaurant_settlements');

        if (Schema::hasColumn('restaurants', 'commission_rate')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropColumn('commission_rate');
            });
        }
    }
};
