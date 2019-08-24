<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'owner', 'number'
    ];

    public function deposit()
    {
        return $this->hasOne('App\Models\Deposit');
    }
}
