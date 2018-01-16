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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->comment('客户名称');
            $table->string('code', 50)->comment('客户识别码');
            $table->string('address', 200)->comment('客户地址');
            $table->string('logo', 250)->comment('客户logo');
            $table->string('tel', 100)->comment('客户联系电话');
            $table->string('default_head', 100)->default('')->comment('客户抬头');
            $table->integer('default_head_id')->default(0)->comment('默认抬头ID');
            $table->string('contact')->default('')->comment('联系人');
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
    }
}
