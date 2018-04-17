<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentCyclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_cycles', function (Blueprint $table) {

            $table->increments('id');

            $table->string('slug', 80)->default('')->comment('来源');

            $table->string('reg_ex', 40)->default('')->comment('匹配表达式');

            $table->integer('month_num')->default(0)->comment('识别出的延迟月份数');

            $table->integer('day_num')->default(0)->comment('识别出的延迟天数');

            $table->integer('default')->default(3)->comment('默认延迟月份数');

            $table->integer('supplier_id')->default(0)->comment('对应供应商');

            $table->integer('bill_period_id')->default(0)->comment('来源账期');

            $table->integer('payment_schedule_id')->default(0)->comment('来源计划');

            $table->integer('payment_type_id')->default(0)->comment('来源计划类型');

            $table->boolean('is_checked')->default(true)->comment('是否验证过');

            $table->boolean('is_closed')->default(false)->comment('是否关闭');

            $table->text('memo')->nullable()->comment('备注');

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
        Schema::dropIfExists('payment_cycles');
    }
}
