<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentScheduleColumns extends Migration
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
            $table->decimal('invoice_m_1', 12, 2)->default(0)->comment('1月应付款发票');
            $table->decimal('invoice_m_2', 12, 2)->default(0)->comment('2月应付款发票');
            $table->decimal('invoice_m_3', 12, 2)->default(0)->comment('3月应付款发票');
            $table->decimal('invoice_m_4', 12, 2)->default(0)->comment('4月应付款发票');
            $table->decimal('invoice_m_5', 12, 2)->default(0)->comment('5月应付款发票');
            $table->decimal('invoice_m_6', 12, 2)->default(0)->comment('6月应付款发票');
            $table->decimal('invoice_m_7', 12, 2)->default(0)->comment('7月应付款发票');
            $table->decimal('invoice_m_8', 12, 2)->default(0)->comment('8月应付款发票');
            $table->decimal('invoice_m_9', 12, 2)->default(0)->comment('9月应付款发票');
            $table->decimal('invoice_m_10', 12, 2)->default(0)->comment('10月应付款发票');
            $table->decimal('invoice_m_11', 12, 2)->default(0)->comment('11月应付款发票');
            $table->decimal('invoice_m_12', 12, 2)->default(0)->comment('12月应付款发票');
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
            $table->dropColumn('invoice_m_1');
            $table->dropColumn('invoice_m_2');
            $table->dropColumn('invoice_m_3');
            $table->dropColumn('invoice_m_4');
            $table->dropColumn('invoice_m_5');
            $table->dropColumn('invoice_m_6');
            $table->dropColumn('invoice_m_7');
            $table->dropColumn('invoice_m_8');
            $table->dropColumn('invoice_m_9');
            $table->dropColumn('invoice_m_10');
            $table->dropColumn('invoice_m_11');
            $table->dropColumn('invoice_m_12');
        });
    }
}
