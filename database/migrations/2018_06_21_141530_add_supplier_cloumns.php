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
            $table->integer('payment_type_id')->default(0)->comment('付款类型ID');
            $table->integer('payment_materiel_id')->default(0)->comment('物料ID');
            $table->string('charge_man', 50)->nullable()->default('')->comment('供应商负责人');
            $table->string('address', 200)->nullable()->default('')->comment('供应商地址')->change();
            $table->string('logo', 250)->nullable()->default('')->comment('供应商logo')->change();
            $table->string('tel', 100)->nullable()->default('')->comment('供应商联系电话')->change();
            $table->string('head', 100)->nullable()->default('')->comment('供应商抬头')->change();
            $table->string('contact')->nullable()->default('')->comment('供应商联系人')->change();
            $table->text('terms')->nullable()->comment('供应商约定');
        });

        // 改动应付款发票相关信息
        //  识别-  付款的类别、付款的物品、付款的科目编码、付款确认人、付款关联的合同条款
        Schema::table('invoice_payments', function(Blueprint $table){
            $table->integer('payment_materiel_id')->default(0)->comment('付款的物品');
            $table->string('materiel', 50)->nullable()->default('')->comment('付款的物品名称');
            $table->text('payment_terms')->nullable()->comment('付款条款');
            $table->string('code', 50)->nullable()->default('')->comment('发票凭证')->change();
            $table->string('title', 50)->nullable()->default('')->comment('发票抬头')->change();
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
            $table->dropColumn('payment_type_id');
            $table->dropColumn('payment_materiel_id');
            $table->dropColumn('terms');
            $table->dropColumn('charge_man');
        });

        Schema::table('invoice_payments', function(Blueprint $table){
            $table->dropColumn('payment_materiel_id');
            $table->dropColumn('materiel');
            $table->dropColumn('payment_terms');
        });
    }
}
