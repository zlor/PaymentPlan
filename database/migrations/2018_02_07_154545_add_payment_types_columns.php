<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentTypesColumns extends Migration
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
            $table->boolean('map_sheet')->default(false)->comment('是否于导入文件的sheet产生映射');
            $table->string('sheet_slug')->default('')->comment('导入的汇总Excel文件中的sheet名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_periods', function (Blueprint $table) {
            $table->dropColumn('sheet_slug');
            $table->dropColumn('map_sheet');
        });
    }
}
