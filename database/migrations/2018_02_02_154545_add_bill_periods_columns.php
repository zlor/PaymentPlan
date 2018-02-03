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
            $table->decimal('loan_balance', 12, 2)->default(0)->comment('贷款额度');
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
            $table->dropColumn('loan_balance');
        });
    }
}
