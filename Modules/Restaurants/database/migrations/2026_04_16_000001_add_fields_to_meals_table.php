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
        Schema::table('meals', function (Blueprint $table) {
            $table->foreignId('meal_category_id')->nullable()->after('menu_id')->constrained('meal_categories')->onDelete('set null');
            $table->string('image')->nullable()->after('description');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropForeign(['meal_category_id']);
            $table->dropColumn(['meal_category_id', 'image', 'discount_price']);
        });
    }
};
