<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_period_id')->default(0)->comment('账期ID');
            $table->integer('supplier_id')->default(0)->comment('供应商ID');
            $table->integer('payment_schedule_id')->default(0)->comment('付款计划ID');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->string('pay_type', 30)->default('cash')->comment('付款类型,cash 现金，acceptance 承兑');
            $table->date('time')->comment('付款时间')->nullable();
            $table->decimal('money', 12, 2)->default(0)->comment('付款金额');
            $table->string('collecting_company', 100)->default('')->comment('收款公司');
            $table->text('collecting_proof')->comment('收款凭据')->nullable();
            $table->text('payment_proof')->comment('付款凭据')->nullable();
            $table->string('code', 50)->default('')->comment('流水号');
            $table->text('memo')->comment('备注')->nullable();
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
        Schema::dropIfExists('payment_details');
    }
}
