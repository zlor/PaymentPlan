<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierFlowTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 供应商相关的金额流
        Schema::create('supplier_balance_flows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('supplier_id')->default(0)->comment('供应商ID');
            $table->integer('bill_pay_id')->default(0)->comment('账期付款ID');
            $table->integer('payment_detail_id')->default(0)->comment('付款明细ID');
            $table->integer('invoice_payment_id')->default(0)->comment('应付款发票ID');

            $table->integer('payment_type_id')->default(0)->comment('付款类型ID');
            $table->integer('payment_schedule_id')->default(0)->comment('付款计划ID');


            $table->integer('year')->default(0)->comment('年份');
            $table->integer('month')->default(0)->comment('月份');

            $table->decimal('money', 12,2)->default(0)->comment('发生数额');

            $table->string('type', 20)->default('init')->comment('类型, init 初始化的应付款, pay 实际付款，invoice 应付款发票');

            $table->string('kind', 20)->default('cash')->comment('种类，cash 现金，acceptance 承兑');

            $table->date('date')->comment('发生时间')->nullable();

            $table->text('memo')->comment('备注')->nullable();

            $table->integer('user_id')->default(0)->comment('用户ID');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_balance_flows');
    }
}
