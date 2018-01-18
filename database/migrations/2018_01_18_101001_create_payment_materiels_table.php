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
            $table->string('name')->comment('物料名称');
            $table->string('code')->comment('物料编号');
            $table->text('memo')->comment('物料备注')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('payment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('类型名称');
            $table->string('code')->comment('类型编号');
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
