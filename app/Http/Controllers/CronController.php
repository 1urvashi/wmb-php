<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Auction;
use App\Bid;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class CronController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest');
    }

    public function index()
    {
        Log::error('Cron '.Carbon::now());
        $auction = Auction::where('start_time','<=', Carbon::now())->where('end_time','>=',Carbon::now())->get();
        if(count($auction)){
            foreach($auction as $_auction){
                if($_auction->status == $_auction->getStatusType(0)){
                    $_auction->status = $_auction->getStatusType(1);
                    $_auction->save();

					$this->sendAuctionStartPush($_auction->id);
                }
            }
        }




        $auction =  Auction::where('end_time','<=',Carbon::now())->where('status',1)->get();
		$currentTime = strtotime($this->UaeDate(Carbon::now()));
        if(count($auction)){
            foreach ($auction as $_auction) {

				//echo $_auction->id; exit;

				$lastBid = Bid::where('auction_id', $_auction->id)->orderBy('price','desc')->first();

				/*if(empty($lastBid)){
					$_auction->status = $_auction->getStatusType(12);
			 		$_auction->end_time = date('Y-m-d H:i',$currentTime);
					$_auction->save();
			 		$_auction->firebaseDelete();
					continue;
				}*/


                Log::error('Auction Status '.$_auction->status);
				if(empty($lastBid)){
					$_auction->status = $_auction->getStatusType(12);
				}else{
					$traderId = $lastBid->trader_id;
					$_auction->bid_owner = $traderId;
					$_auction->status = $_auction->getStatusType(3);
				}

				$_auction->end_time = date('Y-m-d H:i',$currentTime);
                $_auction->save();
                $_auction->firebaseDelete();

				if(!empty($lastBid)){
					$this->sendAuctionEndPush($_auction->id);
				}
            }
        }





	/////// status != 1 delete fire base

	$auction =  Auction::where('status','!=',1)->get();

        if(count($auction)){
            foreach ($auction as $_auction) {

                $_auction->firebaseDelete();

            }
        }


    }

}
