<?php

namespace App;

use App\Auction;
use Illuminate\Database\Eloquent\Model;

class AutomaticBid extends Model
{
     protected $table = "automatic_bid";

     public function AuctionDetails() {
          return $this->hasOne('App\Auction','id', 'auction_id');
      }
      public function trader() {
          return $this->hasOne('App\TraderUser','id', 'trader_id');
      }
}
