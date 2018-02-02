<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillPeriodFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_period_flows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_period_id')->default(0)->comment('账期ID');
            $table->integer('supplier_id')->default(0)->comment('供应商ID');
            $table->integer('payment_type_id')->default(0)->comment('付款计划类型ID');
            $table->integer('payment_schedule_id')->default(0)->comment('付款计划ID');

            $table->integer('pay_id')->default(0)->comment('付款记录ID');
            $table->integer('collect_id')->default(0)->comment('收款记录ID');

            $table->string('type', 20)->default('')->comment('类型, pay 付款，collect 收款');
            $table->string('kind', 20)->default('cash')->comment('种类，cash 现金，acceptance 承兑');
            $table->date('date')->comment('发生时间')->nullable();
            $table->decimal('money', 12,2)->default(0)->comment('发生金额');

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
        Schema::dropIfExists('bill_period_flows');
    }
}
