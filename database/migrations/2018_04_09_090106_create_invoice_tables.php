<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 发票-应付
         */
        Schema::create('invoice_payments', function(Blueprint $table){
            $table->increments('id');
            $table->integer('supplier_id')->default(0)->comment('供应商ID');
            $table->integer('payment_type_id')->default(0)->comment('付款分类ID');
            $table->integer('user_id')->default(0)->comment('操作用户ID');
            $table->integer('payment_detail_id')->default(0)->comment('实际付款明细ID');

            $table->decimal('money', 12, 2)->default(0)->comment('发票金额');
            $table->string('code', 50)->nullable()->default('')->comment('发票凭证');
            $table->string('title', 50)->nullable()->default('')->comment('发票抬头');
            $table->date('date')->nullable()->comment('日期');
            $table->integer('year')->default(0)->comment('年份');
            $table->integer('month')->default(0)->comment('月份');
            $table->integer('lay_month')->default(0)->comment('延迟付款月份');
            $table->text('memo')->nullable()->comment('备注');
            $table->decimal('money_paid')->default(0)->comment('已支付金额数');

            $table->timestamps();
        });

        /**
         *  发票月度汇总快照
         */
        Schema::create('invoice_gather_shoots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('year');
            $table->integer('month');
            $table->date('date')->nullable();
            $table->integer('supplier_id')->default(0)->comment('供应商ID');
            $table->integer('payment_schedule_id')->default(0)->comment('计划ID');
            $table->decimal('money', 12, 2)->default(0)->comment('金额');

            $table->timestamps();
        });

        /**
         *  发票月度汇总快照变更记录
         */
        Schema::create('invoice_gather_shoot_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->date('change_date')->nullable();
            $table->integer('payment_schedule_id')->default(0)->comment('计划ID');
            $table->decimal('money', 12, 2)->default(0)->comment('金额');
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
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_gather_shoots');
        Schema::dropIfExists('invoice_gather_shoot_changes');
    }
}
