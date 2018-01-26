<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('账期名称');
            $table->string('month',50)->comment('账期');
            $table->date('time_begin')->nullable()->comment('开始时间');
            $table->date('time_end')->nullable()->comment('结束时间');
            $table->decimal('cash_balance', 12, 2)->default(0)->comment('现金账户余额');
            $table->decimal('invoice_balance', 12, 2)->default(0)->comment('确认计划收款(已收发票额度)');
            $table->decimal('except_balance', 12, 2)->default(0)->comment('预期收款额度');
            $table->decimal('acceptance_line', 12, 2)->default(0)->comment('银行承兑额度');
            $table->decimal('cash_paid', 12, 2)->default(0)->comment('已付现金');
            $table->decimal('acceptance_paid', 12, 2)->default(0)->comment('已付承兑额');
            $table->string('charge_man')->default('')->comment('负责人');
            $table->integer('user_id')->default(0)->comment('创建用户');
            $table->string('status')->default('standby')->comment('状态, standby: 待机准备, active 激活，lock 快照审核，close 关闭归档');
            $table->boolean('is_actived')->default(false)->comment('是否激活');
            $table->boolean('is_locked')->default(false)->comment('是否审核');
            $table->boolean('is_close')->default(false)->comment('是否核定关闭');
            $table->timestamps();
        });

        // // 记录资金池的金额变动
        // Schema::create('bill_period_moneys', function(Blueprint $table){
        //     $table->increments('id');
        //     $table->integer('bill_period_id')->default(0)->comment('账期');
        //     $table->integer('payment_schedule_id')->default(0)->comment('付款计划');
        //     $table->decimal('money', 12, 2)->default(0)->comment('变动金额');
        //     $table->string('pay_type', 25)->default('')->comment('付款类型');
        //     $table->string('memo');
        //
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_periods');
    }
}
