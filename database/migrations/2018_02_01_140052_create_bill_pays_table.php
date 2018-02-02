<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillPaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_pays', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_period_id')->default(0)->comment('账期ID');
            $table->integer('supplier_id')->default(0)->comment('供应商ID');
            $table->integer('payment_schedule_id')->default(0)->comment('付款计划ID');
            $table->integer('payment_type_id')->default(0)->comment('付款类型ID');
            $table->integer('payment_detail_id')->default(0)->comment('付款计划明细ID');
            $table->string('kind', 20)->default('cash')->comment('种类，cash 现金，acceptance 承兑');
            $table->date('date')->comment('付款时间')->nullable();
            $table->decimal('money', 12, 2)->default(0)->comment('金额');
            $table->string('code', 50)->default('')->comment('付款凭证');
            $table->string('company', 100)->default('')->comment('收款公司');
            $table->text('memo')->comment('备注')->nullable();
            $table->date('acceptance_date')->comment('承兑时间')->nullable();
            $table->decimal('acceptance_fee')->default(0)->comment('承兑费');

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
        Schema::dropIfExists('bill_pays');
    }
}
