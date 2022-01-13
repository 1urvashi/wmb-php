<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    protected $guarded = [];
 
    public function trader()
    {
        return $this->belongsTo('App\TraderUser', 'trader_id');
    }
}
