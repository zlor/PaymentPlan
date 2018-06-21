<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupplierCloumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 改动供应商相关信息
        // 用于生成计划：付款周期
        Schema::table('suppliers', function (Blueprint $table) {
            $table->integer('months_pay_cycle')->default(0)->comment('月份数-付款周期');
            $table->text('terms')->notnull()->comment('供应商约定');
        });

        // 改动应付款发票相关信息
        //  识别-  付款的类别、付款的物品、付款的科目编码、付款确认人、付款关联的合同条款
        Schema::table('invoice_payments', function(Blueprint $table){
            $table->integer('payment_materiel_id')->default(0)->comment('付款的物品');
            $table->string('materiel', 50)->default(0)->comment('付款的物品名称');

            $table->text('payment_terms')->notnull()->comment('付款条款');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('months_pay_cycle');
            $table->dropColumn('contract_terms');
        });

        Schema::table('invoice_payments', function(Blueprint $table){
            $table->dropColumn('payment_materiel_id');
            $table->dropColumn('materiel');
            $table->dropColumn('payment_terms');
        });
    }
}
