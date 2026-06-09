<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // إضافة الأعمدة بعد حقل الـ total
            $table->text('delivery_address')->nullable()->after('total')->comment('العنوان التفصيلي');
            $table->string('delivery_location')->nullable()->after('delivery_address')->comment('إحداثيات الخريطة');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_address', 'delivery_location']);
        });
    }
};