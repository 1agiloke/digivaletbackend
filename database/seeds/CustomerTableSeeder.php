<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::firstOrCreate([
            'email' => 'agiru.zu@gmail.com'
        ], [
            'name' => 'Agil Zulkarnaen',
            'saldo' => 500000,
            'password' => Hash::make('admin2121'),
            'phone' => '081275603055'
        ]);
    }
}
