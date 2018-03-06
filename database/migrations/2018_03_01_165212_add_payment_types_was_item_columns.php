<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentTypesWasItemColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('payment_types', function (Blueprint $table) {
            $table->boolean('is_plan')->default(false)->comment('是否为计划类型');
            $table->boolean('is_closed')->default(false)->comment('是否关闭');
            $table->integer('parent_id')->default(0)->comment('父节点ID');
        });
    }

    /**.
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropColumn('is_plan');
            $table->dropColumn('is_closed') ;
            $table->dropColumn('parent_id');
        });
    }
}
