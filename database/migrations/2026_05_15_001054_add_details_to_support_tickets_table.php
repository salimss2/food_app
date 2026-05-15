<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            // إضافة عمود details من نوع text لأنه سيحتوي على رسالة طويلة
            $table->text('details')->after('subject');
        });
    }

    public function down()
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn('details');
        });
    }
};
