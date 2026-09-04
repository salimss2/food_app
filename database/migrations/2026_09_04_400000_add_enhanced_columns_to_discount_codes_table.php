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
        Schema::table('discount_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('discount_codes', 'max_discount_amount')) {
                $table->decimal('max_discount_amount', 10, 2)->nullable()->after('discount_value');
            }
            if (!Schema::hasColumn('discount_codes', 'restaurant_id')) {
                $table->unsignedBigInteger('restaurant_id')->nullable()->after('max_discount_amount');
            }
            if (!Schema::hasColumn('discount_codes', 'per_user_limit')) {
                $table->integer('per_user_limit')->default(1)->after('max_usages');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            if (Schema::hasColumn('discount_codes', 'max_discount_amount')) {
                $table->dropColumn('max_discount_amount');
            }
            if (Schema::hasColumn('discount_codes', 'restaurant_id')) {
                $table->dropColumn('restaurant_id');
            }
            if (Schema::hasColumn('discount_codes', 'per_user_limit')) {
                $table->dropColumn('per_user_limit');
            }
        });
    }
};
