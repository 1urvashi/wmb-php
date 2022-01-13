<?php

namespace App;

//use Mpociot\Firebase\SyncsWithFirebase;
use App\Auction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Bid extends Model
{
    public function AuctionDetails() {
        return $this->hasOne('App\Auction','id', 'auction_id');
    }
    public function trader() {
        return $this->hasOne('App\TraderUser','id', 'trader_id')->withTrashed();
    }
   // use SyncsWithFirebase;

    /* public function getFirebaseSyncData()
    {
		 Log::error('bidding table -');
        $bid =  $this->fresh();
        $auction = Auction::where('id',$bid->auction_id)->first();
        $auction->updated_at = $bid->updated_at;
        $auction->save();
    }

	 public function save(array $options = [])
	   {

		 	 parent::save();

		    $auction = Auction::where('id',$this->auction_id)->first();
			$auction->updated_at = $this->updated_at;
			$auction->save();

	   }*/

}
