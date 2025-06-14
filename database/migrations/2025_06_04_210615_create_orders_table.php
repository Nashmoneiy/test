<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('surname');
            $table->string('email');
            $table->string('phone');
            $table->string('street');
            $table->string('district');
            $table->string('city');
            $table->string('state');
            $table->integer('total');
            $table->string('reference');
            $table->string('status');
            $table->string('user_type');
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
        Schema::dropIfExists('orders');
    }
};
