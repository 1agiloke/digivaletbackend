<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterParkingDataTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parking_data', function (Blueprint $table) {
            if(!Schema::hasColumn('parking_data', 'exit_time')){
                $table->dateTime('exit_time')->nullable()->after('police_number');
            }
            if(!Schema::hasColumn('parking_data', 'entry_time')){
                $table->dateTime('entry_time')->after('police_number');
            }
            if (Schema::hasColumn('parking_data', 'date')) {
                $table->dropColumn('date');
            }
            if (Schema::hasColumn('parking_data', 'day')) {
                $table->dropColumn('day');
            }
            if (Schema::hasColumn('parking_data', 'time_in')) {
                $table->dropColumn('time_in');
            }
            if (Schema::hasColumn('parking_data', 'time_out')) {
                $table->dropColumn('time_out');
            }
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
            if (Schema::hasColumn('parking_data', 'exit_time')) {
                $table->dropColumn('exit_time');
            }
            if (Schema::hasColumn('parking_data', 'entry_time')) {
                $table->dropColumn('entry_time');
            }
        });
    }
}
