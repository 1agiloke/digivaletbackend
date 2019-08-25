<?php

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\Location;
use App\Models\Parking;
use App\Models\User;
use App\Models\ConfigParking;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchant = User::where('email', '=', 'cold.lipz69@gmail.com')->first();

        $device1 = Device::firstOrCreate([
            'key' => 'r45aY4n6p3rN4h4dA',
            'name' => 'biring_device_1'
        ]);

        $location1 = Location::firstOrCreate([
            'latitude' => '3.627762',
            'longitude' => '98.670403'
        ], [
            'address' => 'Kl yos sudarso komplek: brayan one stop square 27-30, Pulo Brayan Kota, Kec. Medan Bar., Kota Medan, Sumatera Utara 20116'
        ]);

        $parking1 = Parking::firstOrCreate([
            'user_id' => $merchant->id,
            'device_id' => $device1->id,
            'location_id' => $location1->id
        ], [
            'capacity' => 10
        ]);

        for ($i=0; $i < 7; $i++) {
            $configParking = new ConfigParking();
            $configParking->day = strval($i);
            $configParking->parking_id = $parking1->id;
            $configParking->save();
        }

        // ----------------------------------------------------------------------------------------------------------------------------------

        $device2 = Device::firstOrCreate([
            'key' => 'j4nC0kT4cH1lH053mv4',
            'name' => 'biring_device_2'
        ]);

        $location2 = Location::firstOrCreate([
            'latitude' => '3.586600',
            'longitude' => '98.703097'
        ], [
            'address' => 'Komplek Asia Mega Mas, Jl. Asia Raya No.19-20, Sukaramai II, Kec. Medan Area, Kota Medan, Sumatera Utara 20222'
        ]);

        $parking2 = Parking::firstOrCreate([
            'user_id' => $merchant->id,
            'device_id' => $device2->id,
            'location_id' => $location2->id
        ], [
            'capacity' => 20
        ]);

        for ($i = 0; $i < 7; $i++) {
            $configParking = new ConfigParking();
            $configParking->day = strval($i);
            $configParking->parking_id = $parking2->id;
            $configParking->save();
        }
    }
}
