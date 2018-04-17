<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceFileTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('invoice_files', function(Blueprint $table){
             $table->increments('id');

             $table->integer('file_id');
             $table->integer('invoice_paymemt_id');
             $table->integer('user_id');
             $table->integer('number');

             $table->boolean('is_success')->default(false)->comment('导入状态');
             $table->boolean('is_overwrite')->default(false)->comment('是否为覆盖');

             $table->text('import_msg')->nullable()->comment('载入时反馈的信息');
             $table->text('import_source')->nullable()->comment('记录载入时读取到的原始信息');

             $table->timestamps();
         });

         Schema::table('files', function(){

         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_files');
    }
}
