<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('police_number');
            $table->date('date');
            $table->enum('day', ['1', '2', '3', '4', '5', '6', '7']);
            $table->time('time_in');
            $table->time('time_out');
            $table->string('price');
            $table->enum('status', ['process', 'done', 'failed']);

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

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
        Schema::dropIfExists('parking_data');
    }
}
