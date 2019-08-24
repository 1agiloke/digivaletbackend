<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nominal', 'unique_code', 'status', 'user_id', 'customer_id', 'bank_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank');
    }
}
