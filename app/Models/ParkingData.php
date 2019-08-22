<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingData extends Model
{
    const PROCESS = 'process';
    const DONE = 'done';
    const FAILED = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'police_number', 'entry_time', 'exit_time', 'price', 'status', 'customer_id', 'parking_id'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function parking()
    {
        return $this->belongsTo('App\Models\Parking');
    }
}
