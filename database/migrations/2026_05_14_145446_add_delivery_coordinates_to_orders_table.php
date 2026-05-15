<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('delivery_lat', 10, 8)->nullable()->after('delivery_fee')->comment('خط العرض لموقع التوصيل الفعلي');
            $table->decimal('delivery_lng', 10, 8)->nullable()->after('delivery_lat')->comment('خط الطول لموقع التوصيل الفعلي');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_lat', 'delivery_lng']);
        });
    }
};