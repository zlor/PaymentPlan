<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentMaterielsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_materiels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('物料名称');
            $table->string('icon', 250)->default('')->comment('物料标识');
            $table->string('code', 50)->default('')->comment('物料编号');
            $table->text('memo')->comment('物料备注')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('payment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('类型名称');
            $table->string('icon', 250)->default('')->comment('类型标识');
            $table->string('code', 50)->default('')->comment('类型编号');
            $table->text('memo')->comment('类型备注')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('payment_materiels');
        Schema::dropIfExists('payment_types');

    }
}
