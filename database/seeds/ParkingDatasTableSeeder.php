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
        ParkingData::firstOrCreate([
            "code" => '12345'
        ], [
            'police_number' => 'BK 5533 XBB',
            'entry_time'    => date("Y-m-d H:i:s"),
            'exit_time'     => date("Y-m-d H:i:s"),
            'price'         => '10000',
            'status'        => 'done',
            'customer_id'   => 1,
            'parking_id'    => 1
        ]);

        ParkingData::firstOrCreate([
            "code" => '123456'
        ], [
            'police_number' => 'BK 8800 ZXC',
            'entry_time'    => date("Y-m-d H:i:s"),
            'exit_time'     => null,
            'price'         => '0',
            'status'        => 'process',
            'customer_id'   => 2,
            'parking_id'    => 1
        ]);

        ParkingData::firstOrCreate([
            "code" => '1234567'
        ], [
            'police_number' => 'BK 1144 ASD',
            'entry_time'    => date("Y-m-d H:i:s"),
            'exit_time'     => null,
            'price'         => '0',
            'status'        => 'process',
            'customer_id'   => 2,
            'parking_id'    => 1
        ]);
    }
}
