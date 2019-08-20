<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterParkingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parking_data', function (Blueprint $table) {
            $table->unsignedBigInteger('parking_id');
            $table->foreign('parking_id')->references('id')->on('parkings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parking_data', function (Blueprint $table) {
            $table->dropForeign('parking_data_parking_id_foreign');
            $table->dropColumn('parking_id');
        });
    }
}
