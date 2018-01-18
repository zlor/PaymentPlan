<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUserEnvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user_envs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->comment('代号');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->text('env')->comment('环境变量');
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
        Schema::dropIfExists('admin_user_envs');
    }
}
