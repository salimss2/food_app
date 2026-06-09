<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('distance_slabs', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_distance', 8, 2)->comment('من مسافة (كم)');
            $table->decimal('max_distance', 8, 2)->comment('إلى مسافة (كم)');
            $table->decimal('total_fee', 8, 2)->comment('إجمالي رسوم التوصيل');
            $table->decimal('driver_share', 8, 2)->comment('حصة الموصل');
            $table->decimal('platform_share', 8, 2)->comment('حصة المنصة');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distance_slabs');
    }
};