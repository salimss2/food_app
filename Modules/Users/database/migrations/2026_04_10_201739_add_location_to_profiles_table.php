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
    Schema::table('profiles', function (Blueprint $table) {
        // إضافة الحقل بعد الـ address
        $table->string('location')->nullable()->after('address');
    });
}

public function down(): void
{
    Schema::table('profiles', function (Blueprint $table) {
        $table->dropColumn('location');
    });
}
};
