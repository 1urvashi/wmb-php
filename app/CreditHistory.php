<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;

class CreditHistory extends Model
{
    protected $table = "credit_history";
    
    public function trader() {
        return $this->hasOne('App\TraderUser','id','trader_id');
    }
    
    public function getCreatedAtAttribute($value)
    {
        $controller = new Controller();
        return $controller->UaeDate($value);
    }
}
