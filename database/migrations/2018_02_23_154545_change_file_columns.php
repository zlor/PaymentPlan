<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFileColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->longText('import_msg')->nullable()->comment('载入后的信息')->change();
        });

        // 付款计划 < 多对多关联 > 文件, 文件导入生成多个付款计划； 一个付款计划由多个文件导入覆盖；
        Schema::table('payment_schedule_files', function(Blueprint $table){
            $table->longText('import_msg')->nullable()->comment('载入时反馈的信息')->change();
            $table->longText('import_source')->nullable()->comment('记录载入时读取到的原始信息')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->longText('import_msg')->nullable()->comment('载入后的信息')->change();
        });

        // 付款计划 < 多对多关联 > 文件, 文件导入生成多个付款计划； 一个付款计划由多个文件导入覆盖；
        Schema::table('payment_schedule_files', function(Blueprint $table){
            $table->longText('import_msg')->nullable()->comment('载入时反馈的信息')->change();
            $table->longText('import_source')->nullable()->comment('记录载入时读取到的原始信息')->change();
        });
    }
}
