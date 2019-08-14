<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address', 'longitude', 'latitude'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
