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
        // التقارير
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // مالي، مستخدمين، مطاعم
            $table->json('data')->nullable();
            $table->timestamps();
        });

        // إعدادات النظام (مثل سعر التوصيل، اسم التطبيق...)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('system_settings');
    }
};
