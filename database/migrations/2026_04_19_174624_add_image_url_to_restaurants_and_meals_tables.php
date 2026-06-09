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
        // إضافة الحقل لجدول المطاعم
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('category');
        });

        // إضافة الحقل لجدول الوجبات
        Schema::table('meals', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة الحقل من جدول المطاعم في حال التراجع
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        // إزالة الحقل من جدول الوجبات في حال التراجع
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};