<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterParkingDataTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parking_data', function (Blueprint $table) {
            if (!Schema::hasColumn('parking_data', 'code')) {
                $table->string('code')->nullable()->after('id');
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
            if (Schema::hasColumn('parking_data', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
}
