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
        // 1. Create order_reviews table
        Schema::create('order_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('meals_rating');
            $table->integer('driver_rating')->nullable();
            $table->integer('restaurant_rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // 2. Add rating_count to restaurants table
        if (Schema::hasTable('restaurants')) {
            Schema::table('restaurants', function (Blueprint $table) {
                if (!Schema::hasColumn('restaurants', 'rating_count')) {
                    $table->integer('rating_count')->default(0)->after('rating');
                }
            });
        }

        // 3. Add rating and rating_count to driver_profiles table
        if (Schema::hasTable('driver_profiles')) {
            Schema::table('driver_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('driver_profiles', 'rating')) {
                    $table->decimal('rating', 3, 2)->default(0.00)->after('vehicle_vin');
                }
                if (!Schema::hasColumn('driver_profiles', 'rating_count')) {
                    $table->integer('rating_count')->default(0)->after('rating');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_reviews');

        if (Schema::hasTable('restaurants')) {
            Schema::table('restaurants', function (Blueprint $table) {
                if (Schema::hasColumn('restaurants', 'rating_count')) {
                    $table->dropColumn('rating_count');
                }
            });
        }

        if (Schema::hasTable('driver_profiles')) {
            Schema::table('driver_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('driver_profiles', 'rating')) {
                    $table->dropColumn('rating');
                }
                if (Schema::hasColumn('driver_profiles', 'rating_count')) {
                    $table->dropColumn('rating_count');
                }
            });
        }
    }
};
