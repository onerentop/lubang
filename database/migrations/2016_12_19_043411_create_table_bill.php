<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin');
            $table->string('endPoint');
            $table->string('time');
            $table->unsignedInteger('money');
            $table->unsignedInteger('status');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('driver_id');
            $table->timestamps();

            $table->foreign('user_id')->reference('id')->on('users');
            $table->foreign('driver_id')->reference('id')->on('driver_user');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill');
    }
}
