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
    Schema::table('users', function (Blueprint $table) {
        // إضافة العمود مع قيمة افتراضية لتجنب المشاكل مع المستخدمين الحاليين
        $table->enum('status', ['active', 'inactive', 'suspended', 'blocked'])->default('active');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        // حذف العمود في حال تراجعنا عن العملية
        $table->dropColumn('status');
    });
}
};
