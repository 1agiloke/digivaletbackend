<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigParkingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_parkings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('day', ['0', '1', '2', '3', '4', '5', '6'])->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->string('price')->nullable();
            $table->enum('status', ['open', 'close'])->default('close');

            $table->unsignedBigInteger('parking_id')->nullable();
            $table->foreign('parking_id')->references('id')->on('parkings')->onDelete('cascade');

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
        Schema::dropIfExists('config_parkings');
    }
}
