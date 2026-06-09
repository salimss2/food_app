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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->after('total')->nullable();
            $table->text('customer_notes')->nullable()->after('scheduled_at');
            // Update status enum logic is usually handled by modifying the column or using string
            // For simplicity and safety in existing DBs, we'll ensure it's a string or enum if possible
            // $table->enum('status', ['pending', 'processing', 'ready', 'delivered', 'cancelled'])->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('quantity')->nullable();
            $table->json('customizations')->nullable()->after('price');
            $table->text('special_instructions')->nullable()->after('customizations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['price', 'customizations', 'special_instructions']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_price', 'customer_notes']);
        });
    }
};
