<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 增加 发票属性：入账时间
        Schema::table('invoice_payments', function(Blueprint $table){
                $table->date('enter_date')->nullable()->comment('入账时间');
        });
        // 增加 付款记录属性：付款分类
        Schema::table('', function(){

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_payments', function(Blueprint $table){
                $table->dropColumn('enter_date');
        });
    }
}
