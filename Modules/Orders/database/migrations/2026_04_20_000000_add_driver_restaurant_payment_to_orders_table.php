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
            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('cascade')->after('user_id');
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->string('payment_method')->default('cod')->after('total');
            $table->string('group_id')->nullable()->after('id')->comment('For grouping split checkout orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['restaurant_id', 'driver_id', 'payment_method', 'group_id']);
        });
    }
};
