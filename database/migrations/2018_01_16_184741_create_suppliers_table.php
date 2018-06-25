<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('supplier_owners', function(Blueprint $table){
            $table->increments('id');
            $table->string('name', 50)->default('')->comment('供应商所有人姓名');
            $table->string('tel', 100)->default('')->comment('供应商所有人联系方式');
            $table->string('company', 100)->default('')->comment('供应商所有人-主体公司');
            $table->string('code', 100)->default('')->comment('供应商所有人-主体公司标识号');
            $table->text('memo')->nullable()->comment('备注');

            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('supplier_owner_id')->default(0)->comment('默认所有人');
            $table->string('name', 150)->default('')->comment('供应商名称');
            $table->string('code', 50)->nullable()->default('')->comment('供应商识别码');
            $table->string('address', 200)->nullable()->default('')->comment('供应商地址');
            $table->string('logo', 250)->nullable()->default('')->comment('供应商logo');
            $table->string('tel', 100)->nullable()->default('')->comment('供应商联系电话');
            $table->string('head', 100)->nullable()->default('')->comment('供应商抬头');
            $table->string('contact')->nullable()->default('')->comment('供应商联系人');
            $table->text('memo')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('supplier_owners');
    }
}
