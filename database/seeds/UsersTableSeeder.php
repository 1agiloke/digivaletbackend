<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::where('email', 'developer@gmail.com')->first() === null) {
            $user               = new User();
            $user->name         = 'Developer';
            $user->email        = 'developer@gmail.com';
            $user->phone        = '085261538606';
            $user->password     = Hash::make('password');
            $user->save();
        }
        User::firstOrCreate([
            'email'     => 'cold.lipz69@gmail.com',
            'phone'     => '085360867334',
        ], [
            'name'      => 'Biring (Bibir Kering)',
            'phone'     => '085360867334',
            'password'  => Hash::make('admin2121'),
        ]);
        User::firstOrCreate([
            'email'     => 'itishardto.breathewithoutyou@gmail.com',
            'phone'     => '081275603055',
        ], [
            'name'      => 'Lebay (Lele Jablay)',
            'phone'     => '081275603055',
            'password'  => Hash::make('admin2121'),
        ]);
        User::firstOrCreate([
            'email'     => 'hidupadalahmencintai@gmail.com',
            'phone'     => '081234567890'
        ], [
            'name'      => 'Jonas (Jomblo Ngenas)',
            'phone'     => '081234567890',
            'password'  => Hash::make('admin2121'),
        ]);
    }
}
