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
            // إضافة عمود type (ويفضل وضعه بعد user_id لترتيب الجدول)
            $table->string('type')->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            // في حال أردت التراجع عن هذا المايجريشن مستقبلاً
            $table->dropColumn('type');
        });
    }
};
