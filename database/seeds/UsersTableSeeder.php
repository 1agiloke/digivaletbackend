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
            $user->phone        = null;
            $user->password     = Hash::make('password');
            $user->save();
        }
    }
}
