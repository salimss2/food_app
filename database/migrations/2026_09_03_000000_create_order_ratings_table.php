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
        if (!Schema::hasTable('order_ratings')) {
            Schema::create('order_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');
                $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->tinyInteger('meals_rating');
                $table->tinyInteger('driver_rating')->nullable();
                $table->tinyInteger('restaurant_rating');
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }

        // Add helper aggregate columns to restaurants table if missing
        if (Schema::hasTable('restaurants')) {
            Schema::table('restaurants', function (Blueprint $table) {
                if (!Schema::hasColumn('restaurants', 'rating_avg')) {
                    $table->decimal('rating_avg', 3, 2)->default(5.00)->after('status');
                }
                if (!Schema::hasColumn('restaurants', 'rating_count')) {
                    $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
                }
            });
        }

        // Add helper aggregate columns to driver_profiles table if missing
        if (Schema::hasTable('driver_profiles')) {
            Schema::table('driver_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('driver_profiles', 'rating_avg')) {
                    $table->decimal('rating_avg', 3, 2)->default(5.00);
                }
                if (!Schema::hasColumn('driver_profiles', 'rating_count')) {
                    $table->unsignedInteger('rating_count')->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_ratings');

        if (Schema::hasTable('restaurants')) {
            Schema::table('restaurants', function (Blueprint $table) {
                if (Schema::hasColumn('restaurants', 'rating_avg')) {
                    $table->dropColumn('rating_avg');
                }
            });
        }

        if (Schema::hasTable('driver_profiles')) {
            Schema::table('driver_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('driver_profiles', 'rating_avg')) {
                    $table->dropColumn('rating_avg');
                }
            });
        }
    }
};
