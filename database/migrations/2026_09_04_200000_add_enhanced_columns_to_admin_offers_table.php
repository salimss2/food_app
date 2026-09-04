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
        if (Schema::hasTable('admin_offers')) {
            Schema::table('admin_offers', function (Blueprint $table) {
                if (!Schema::hasColumn('admin_offers', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
                if (!Schema::hasColumn('admin_offers', 'type')) {
                    $table->string('type')->default('banner')->after('description');
                }
                if (!Schema::hasColumn('admin_offers', 'click_action')) {
                    $table->string('click_action')->default('restaurant')->after('type');
                }
                if (!Schema::hasColumn('admin_offers', 'banner_image')) {
                    $table->string('banner_image')->nullable()->after('click_action');
                }
                if (!Schema::hasColumn('admin_offers', 'meal_id')) {
                    $table->unsignedBigInteger('meal_id')->nullable()->after('restaurant_id');
                }
                if (!Schema::hasColumn('admin_offers', 'original_price')) {
                    $table->decimal('original_price', 10, 2)->nullable()->after('discount_percentage');
                }
                if (!Schema::hasColumn('admin_offers', 'offer_price')) {
                    $table->decimal('offer_price', 10, 2)->nullable()->after('original_price');
                }
                if (!Schema::hasColumn('admin_offers', 'start_date')) {
                    $table->date('start_date')->nullable()->after('expiry_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admin_offers')) {
            Schema::table('admin_offers', function (Blueprint $table) {
                // Drop if needed
            });
        }
    }
};
