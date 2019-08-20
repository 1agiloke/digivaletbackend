<?php

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::firstOrCreate([
            'email' => 'customer@gmail.com'
        ], [
            'name'      => 'Customer',
            'password'  => Hash::make('password'),
            'phone'     => '085261538606',
            'saldo'     => '1000000',
        ]);

        Customer::firstOrCreate([
            'email' => 'agiru.zu@gmail.com'
        ], [
            'name' => 'Agil Zulkarnaen',
            'saldo' => '500000',
            'password' => Hash::make('admin2121'),
            'phone' => '081275603055'
        ]);
    }
}
