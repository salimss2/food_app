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
        // 1. Add individual item discount fields to meals table
        Schema::table('meals', function (Blueprint $table) {
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->after('price');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->dateTime('discount_start')->nullable()->after('discount_value');
            $table->dateTime('discount_end')->nullable()->after('discount_start');
        });

        // 2. Modify offers table (rename discount to combo_price, add image)
        Schema::table('offers', function (Blueprint $table) {
            $table->renameColumn('discount', 'combo_price');
            $table->string('image')->nullable()->after('description');
        });

        // 3. Create pivot table meal_offer
        Schema::create('meal_offer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_offer');

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->renameColumn('combo_price', 'discount');
        });

        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_start', 'discount_end']);
        });
    }
};
