<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'device_id', 'location_id', 'capacity'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }

    public function parking_datas()
    {
        return $this->hasMany('App\Models\ParkingData');
    }
}
