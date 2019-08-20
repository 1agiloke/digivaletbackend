<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'name'
    ];

    public function parking()
    {
        return $this->hasOne('App\Models\Parking');
    }
}
