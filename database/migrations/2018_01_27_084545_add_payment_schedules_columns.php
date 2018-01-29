<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentSchedulesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->boolean('is_froze')->comment('是否冻结付款')->default(0);
            $table->text('memo_froze')->comment('冻结付款备注')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropColumn('is_froze');
            $table->dropColumn('memo_froze');
        });
    }
}
