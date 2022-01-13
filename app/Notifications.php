<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTimeZone;
use DateTime;

class Notifications extends Model
{
     protected $table = "notifications";
    protected $dates = [
        'created_at', 
        'updated_at'
    ];

    public $fillable = ['title', 'title', 'trader_id', 'auction_id', 'type'];


     public function getCreatedAtAttribute($value)
    {
		return new DateTime($value, new DateTimeZone('Asia/Dubai'));
    }
	
	
}
