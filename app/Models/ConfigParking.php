<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigParking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'day', 'open_time', 'close_time', 'price', 'status', 'parking_id'
    ];

    public function parking()
    {
        return $this->belongsTo('App\Models\Parking');
    }
}
