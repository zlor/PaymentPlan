<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillPeriodsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('bill_periods', function (Blueprint $table) {
            $table->decimal('cash_collected', 12, 2)->default(0)->comment('已收款现金额度');
            $table->decimal('acceptance_collected', 12, 2)->default(0)->comment('已收款承兑额度');
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
            $table->dropColumn('cash_collected');
            $table->dropColumn('acceptance_collected');
        });
    }
}
