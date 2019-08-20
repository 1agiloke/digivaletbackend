<?php

use App\Models\ParkingData;
use Illuminate\Database\Seeder;

class ParkingDatasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ParkingData::firstOrCreate([
        //     'police_number' => 'BK 5533 XBB'
        // ], [
        //     'date'          => date("Y-m-d"),
        //     'day'           => '1',
        //     'time_in'       => date("H:i"),
        //     'time_out'      => date("H:i"),
        //     'price'         => '10000',
        //     'status'        => 'done',
        //     'customer_id'   => 1
        // ]);

        // ParkingData::firstOrCreate([
        //     'police_number' => 'BK 1144 ASD'
        // ], [
        //     'date'          => date("Y-m-d"),
        //     'day'           => '2',
        //     'time_in'       => date("H:i"),
        //     'time_out'      => date("H:i"),
        //     'price'         => '3000',
        //     'status'        => 'process',
        //     'customer_id'   => 1
        // ]);

        // ParkingData::firstOrCreate([
        //     'police_number' => 'BK 8800 ZXC'
        // ], [
        //     'date'          => date("Y-m-d"),
        //     'day'           => '3',
        //     'time_in'       => date("H:i"),
        //     'time_out'      => date("H:i"),
        //     'price'         => '25000',
        //     'status'        => 'failed',
        //     'customer_id'   => 1
        // ]);
    }
}
