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
            $table->string('name', 50)->default('')->comment('计划名称');
            $table->string('supplier_name', 100)->default('')->comment('供应商名称');
            $table->decimal('supplier_balance', 12, 2)->default(0)->comment('供应商账户余额(前期未付清余额)');
            $table->decimal('supplier_lpu_balance', 12, 2)->default(0)->comment('上期未付清余额');
            $table->integer('payment_type_id')->default(0)->comment('分类ID');
            $table->integer('payment_materiel_id')->default(0)->comment('物料ID');
            $table->string('materiel_name')->default('')->comment('物料导入名称');
            $table->string('pay_cycle')->default('')->comment('付款周期');
            $table->string('charge_man',50)->default('')->comment('付款确认人');
            $table->integer('batch')->default(0)->comment('导入批次');
            $table->decimal('suggest_due_money', 12, 2)->default(0)->comment('本期建议应付款');

            $table->date('plan_time')->comment('计划时间')->nullable();
            $table->decimal('plan_due_money', 12, 2)->default(0)->comment('本期计划应付款');
            $table->string('plan_man', 50)->default('')->comment('计划人');

            $table->date('audit_time')->comment('审核时间')->nullable();
            $table->decimal('audit_due_money', 12, 2)->default(0)->comment('本期审核应付款');
            $table->string('audit_man', 50)->default('')->comment('审核人');

            $table->date('final_time')->comment('最终编辑时间')->nullable();
            $table->decimal('final_due_money', 12, 2)->default(0)->comment('本期最终应付款');
            $table->string('final_man', 50)->default('')->comment('最终编辑人');

            // 实际操作应付款
            $table->decimal('due_money', 12, 2)->default(0)->comment('本期应付款');

            $table->decimal('cash_paid', 12, 2)->default(0)->comment('本期已付现金');
            $table->decimal('acceptance_paid', 12, 2)->default(0)->comment('本期已付承兑');

            $table->string('status', 30)->default('init')->comment('状态, init 初始化,import_init 导入创建，web_init Web创建, check_audit 审核人检查,check_final boss检查, checked 已审核,  paying 付款中,  lock 锁定');

            $table->text('memo')->comment('备注')->nullable();
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
