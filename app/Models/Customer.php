<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'saldo'
    ];

    public function parkingDatas()
    {
        return $this->hashMany('App\Models\ParkingData');
    }
}
