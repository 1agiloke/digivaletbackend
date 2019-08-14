<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingData extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'police_number', 'date', 'day', 'time_in', 'time_out', 'price', 'status', 'customer_id'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}
