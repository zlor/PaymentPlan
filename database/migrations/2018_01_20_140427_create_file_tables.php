<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_period_id')->default(0)->comment('账期');
            $table->integer('payment_type_id')->default(0)->comment('付款计划类型ID');
            $table->string('name', 50)->comment('文件重命名');
            $table->string('path', 100)->comment('文件路径');
            $table->string('ext', 50)->comment('文件类型');
            $table->decimal('size', 12,2)->comment('文件大小');
            $table->string('type', 50)->comment('类型，payment_schedule 付款计划，base_supplier 供应商');

            $table->boolean('is_upload_success')->default(false)->comment('是否上传成功');
            $table->boolean('is_import_success')->default(false)->comment('是否导入成功');
            $table->integer('user_id')->default(0)->comment('操作人');

            $table->string('status', 50)->default('')->comment('文件状态');

            $table->text('memo')->nullable()->comment('备注');
            $table->text('import_msg')->nullable()->comment('载入后的信息');

            $table->timestamps();
        });

        // 付款计划 < 多对多关联 > 文件, 文件导入生成多个付款计划； 一个付款计划由多个文件导入覆盖；
        Schema::create('payment_schedule_file', function(Blueprint $table){
            $table->increments('id');
            $table->integer('file_id')->default(0)->comment('文件');
            $table->integer('payment_schedule_id')->default(0)->comment('付款计划');
            $table->integer('user_id')->default(0)->comment('操作人');
            $table->integer('number')->default(0)->comment('文件中的行号');

            $table->boolean('is_success')->default(false)->comment('导入状态');
            $table->boolean('is_overwrite')->default(false)->comment('是否为覆盖');

            $table->text('import_msg')->nullable()->comment('载入时反馈的信息');
            $table->text('import_source')->nullable()->comment('记录载入时读取到的原始信息');

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
        Schema::dropIfExists('files');
        Schema::dropIfExists('payment_schedule_files');
    }
}
