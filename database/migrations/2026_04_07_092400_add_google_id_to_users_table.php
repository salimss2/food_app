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
            // إضافة حقل google_id
            $table->string('google_id')->nullable()->after('email');
            
            // جعل حقل كلمة المرور يقبل الفراغ (لأن الدخول بجوجل لا يحتاج كلمة مرور)
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_id');
            // إعادة كلمة المرور لتكون إجبارية في حال التراجع
            $table->string('password')->nullable(false)->change();
        });
    }
};