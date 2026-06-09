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
        Schema::table('users', function (Blueprint $table) {
            // إضافة العمود بعد عمود الإيميل (أو الباسوورد) ويكون يقبل القيم الفارغة
            $table->string('image_path')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // لحذف العمود في حال التراجع
            $table->dropColumn('image_path');
        });
    }
};
