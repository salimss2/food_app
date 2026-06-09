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
        Schema::table('driver_profiles', function (Blueprint $table) {
            // نستخدم نوع decimal لأنه الأفضل والأدق لتخزين الإحداثيات الجغرافية
            // جعلناها nullable حتى لا يحدث خطأ مع السائقين المسجلين مسبقاً
            $table->decimal('latitude', 10, 8)->nullable()->after('user_id'); 
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_profiles', function (Blueprint $table) {
            // حذف الحقول في حال التراجع عن التهجير
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};