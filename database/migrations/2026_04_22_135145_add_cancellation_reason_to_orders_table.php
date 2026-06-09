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
        Schema::table('orders', function (Blueprint $table) {
            // إضافة العمود كنص، ويُسمح بأن يكون فارغاً (nullable) لأن أغلب الطلبات ستنجح
            $table->text('cancellation_reason')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // في حال أردنا التراجع عن المايجريشن مستقبلاً
            $table->dropColumn('cancellation_reason');
        });
    }
};
