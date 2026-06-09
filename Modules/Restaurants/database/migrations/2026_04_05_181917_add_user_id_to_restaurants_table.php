<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // إضافة الحقل وربطه بجدول المستخدمين
            // استخدمنا after('id') ليظهر العمود بعد الـ id مباشرة في القاعدة
            $table->foreignId('user_id')->after('id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // حذف الربط ثم حذف العمود عند التراجع
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};