<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Auction;
use Carbon\Carbon;
use App\DealerUser;
use DateTime;
use DateTimeZone;
use Mail;
use App\Bid;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AuctionCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Log::error('Cron run at - '.Carbon::now());
        $controller = new Controller();
        $auction = Auction::where('start_time','<=', Carbon::now())->where('end_time','>=',Carbon::now())->get();
        if(count($auction)){
            foreach($auction as $_auction){
                if($_auction->status == $_auction->getStatusType(0)){
                    $_auction->status = $_auction->getStatusType(1);
                    $_auction->save();
                    $controller->sendAuctionStartPush($_auction->id);

					/*Log::error('Debug-carbon now - '.Carbon::now());

					Log::error('Debug-Auction Id - '.$_auction->id);
					Log::error('Debug-Auction start time - '.$_auction->start_time);
					Log::error('Debug-Auction end time - '.$_auction->end_time);*/

                }
            }
        }
    
      
        $auction =  Auction::where('end_time','<=',Carbon::now())->where('status',1)->get();

        $currentTime = strtotime($controller->UaeDate(Carbon::now()));
        if(count($auction)){
            foreach ($auction as $_auction) {
                $lastBid = Bid::where('auction_id', $_auction->id)->orderBy('price','desc')->first();
                /*if(empty($lastBid)){
                        $_auction->status = $_auction->getStatusType(12);
                        $_auction->end_time = date('Y-m-d H:i',$currentTime);
                        $_auction->save();
                        $_auction->firebaseDelete();
                        continue;
                }*/

				/*Log::error('Debug- End Auction Id - '.$_auction->id);
				Log::error('Debug- End carbon - '.Carbon::now());
				Log::error('Debug- End carbon end time - '.$_auction->end_time);*/

                if(empty($lastBid)){
                        $_auction->status = $_auction->getStatusType(12);
                        // $controller->sendAuctionCompleteMail($_auction->id);
                }else{
                        $bidPrice = $lastBid ? $lastBid->price : 0;

                        $traderId = $lastBid->trader_id;
                        $_auction->bid_owner = $traderId;
                        $_auction->buy_price = $bidPrice;
                        $_auction->status = $_auction->getStatusType(3);



                        //Sale type value
                        // $saleType = $controller->getSalePrice($_auction->sale_type_id, $bidPrice, $_auction->other_amount);
                        $saleType = $controller->getSalePrice($_auction, $bidPrice, $_auction->other_amount);
                        // $controller->sendAuctionCompleteMail($_auction->id);
                        
                        if(!empty($saleType['status'])  && !empty($saleType['amount'])){
                            $saleData = json_encode($saleType);
                            $_auction->deducted_amount = round($saleType['amount']);
                            $_auction->deducted_details = $saleData;
                        }

                        /*else {
                            $saleData = json_encode($saleType);
                            $_auction->deducted_amount = round($saleType['amount']);
                            $_auction->deducted_details = $saleData;
                        }*/
                        //Sale type value
                }


                $_auction->end_time = date('Y-m-d H:i',$currentTime);
                $_auction->save();
                $_auction->firebaseDelete();
                if(!empty($lastBid)){
                        $controller->sendAuctionEndPush($_auction->id);
                        
                        $controller->sendBidOwnerSms($_auction->id);
                        $controller->sendBulkSms($_auction->id);
                }
            }
        }


       $auction =  Auction::where('status','>',1)
		   ->where('end_time','>', Carbon::now()->subMinutes(20))
		   ->where('end_time','<', Carbon::now())->get();
        if(count($auction)){
            foreach ($auction as $_auction) {

				//Log::error('Debug- RR End Auction Id - '.$_auction->id);
				//Log::error('Debug- RR carbon remove - '.Carbon::now());
				//Log::error('Debug- RR End carbon end time - '.$_auction->end_time);
               $_auction->firebaseDelete();
            }
        }
    }

  
    
}
