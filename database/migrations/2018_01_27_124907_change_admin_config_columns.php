<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAdminConfigColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_config', function (Blueprint $table) {
            $table->text('value')->nullable()->comment('值')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_config', function (Blueprint $table) {
            $table->string('value', 255)->default('')->comment('值')->change();
        });
    }
}
