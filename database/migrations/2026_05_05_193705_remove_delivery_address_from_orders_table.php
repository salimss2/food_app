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
            // أمر حذف العمود
            $table->dropColumn('delivery_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // إعادة إنشاء العمود في حال قمت بعمل تراجع (rollback) للمايجريشن
            // افترضت أنه من نوع string، يمكنك تغييره إذا كان text
            $table->string('delivery_address')->nullable(); 
        });
    }
};