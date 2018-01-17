<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_period_id')->default(0)->comment('账期ID');
            $table->integer('supplier_id')->default(0)->comment('供应商ID');
            $table->string('name')->default('')->comment('计划名称');
            $table->string('supplier_name')->default('')->comment('供应商名称');
            $table->decimal('supplier_balance')->default(0)->comment('供应商账户余额(未付款总额)');
            $table->decimal('due_money')->default(0)->comment('本期应付款');
            $table->decimal('cash_paid')->default(0)->comment('本期已付现金');
            $table->decimal('acceptance_paid')->default(0)->comment('本期已付承兑');
            $table->date('plan_time')->comment('计划时间')->nullable();
            $table->integer('batch')->default(0)->comment('导入批次');
            $table->string('status')->default('init')->comment('状态, init 初始化,import_init 导入创建，web_init Web创建, checked 已审核,  paying 付款中,  lock 锁定');
            $table->boolean('is_checked')->default(false)->comment('是否已审核');
            $table->boolean('is_locked')->default(false)->comment('是否已锁定');
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
        Schema::dropIfExists('payment_schedules');
    }
}
