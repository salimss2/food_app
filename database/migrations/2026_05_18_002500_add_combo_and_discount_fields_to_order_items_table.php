<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('meal_id')->nullable()->change();
            $table->foreignId('offer_id')->nullable()->constrained('offers')->onDelete('set null')->after('meal_id');
            $table->string('type')->default('regular_meal')->after('offer_id'); // regular_meal, discounted_meal, combo_offer
            $table->json('combo_meals')->nullable()->after('type');
            $table->string('name')->nullable()->after('combo_meals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['offer_id']);
            $table->dropColumn(['offer_id', 'type', 'combo_meals', 'name']);
            $table->foreignId('meal_id')->nullable(false)->change();
        });
    }
};
