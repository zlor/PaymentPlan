<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_owner_id')->default(0)->comment('默认所有人');
            $table->string('name', 150)->default('')->comment('客户名称');
            $table->string('code', 50)->default('')->comment('客户识别码');
            $table->string('address', 200)->default('')->comment('客户地址');
            $table->string('logo', 250)->default('')->comment('客户logo');
            $table->string('tel', 100)->default('')->comment('客户联系电话');
            $table->string('head', 100)->default('')->comment('客户抬头');
            $table->string('contact')->default('')->comment('客户联系人');
            $table->text('memo')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_owners', function(Blueprint $table){
            $table->increments('id');
            $table->string('name', 50)->default('')->comment('客户所有人姓名');
            $table->string('tel', 100)->default('')->comment('客户所有人联系方式');
            $table->string('company', 100)->default('')->comment('客户所有人-主体公司');
            $table->string('code', 100)->default('')->comment('客户所有人-主体公司标识号');
            $table->text('memo')->nullable()->comment('备注');

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
        //
    }
}
