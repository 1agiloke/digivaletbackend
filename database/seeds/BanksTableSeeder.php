<?php

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bank::updateOrCreate([
            'name' => 'Mandiri',
        ], [
            'owner' => 'PT Digivalet',
            'number' => '10700012345678'
        ]);
        Bank::updateOrCreate([
            'name' => 'BNI',
        ], [
            'owner' => 'PT Digivalet',
            'number' => '0999123910'
        ]);
    }
}
