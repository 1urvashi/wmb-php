<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Excel;
use App\Auction;
use Mail;
use DB;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use App\Bid;
use App\TraderUser;

class CompletedAuctionReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crone for completed auctions email';

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
        // dd('hi');
        // $date = date('Y-m-d', strtotime($this->UaeDate(Carbon::now())));
        $emails = ['mohammed.nasser@dubizzle.com', 'amrita.bhadrannavar@dubizzle.com'];
        $date = date('Y-m-d', strtotime($this->UaeDate(Carbon::now())."-1 days"));
        $from_time = $this->convertTimeToUTCzone(date('Y-m-d H:i:s', strtotime($date."00:00:00")));
        $to_time = $this->convertTimeToUTCzone(date('Y-m-d H:i:s', strtotime($date."23:59:59")));

        $auctions = \App\Auction::where('status', 3)->where('end_time', ">=", $from_time)->where('end_time', "<=", $to_time)->get();
        // dd( $auctions);
        $header = ['Id','Title', 'Start Time', 'End Time', 'Type', 'Dealer Name',  'Status', 'Bid Price', 'Negotiated Price', 'Price to selling customer'];
        $auctionsArray = [];
        $auctionsArray[] = $header;
        if (!$auctions->isEmpty()) {
            foreach ($auctions as $auction) {
                $getBidData = $this->getBidDetails($auction->id);
                // $sendMail = $this->sendAuctionCompleteMail($auction->id);

              

                // dd($getBidData);
                $auctionsArray[] = [
                    'id'=> $auction->id,
                    'title'=> $auction->title,
                    'start_time' => $this->UaeDate($auction->start_time),
                    'end_time' => $this->UaeDate($auction->end_time),
                    'type' => $auction->getAuctionType($auction->type),
                    'dealer_name'=> $getBidData['dealer_name'] ? $getBidData['dealer_name'] : "N/A",
                    'status'=> $auction->status,
                    'bid_price'=> $getBidData['bid_amount'] ? $getBidData['bid_amount'] : "0",
                    'negotiated_price'=>$auction->negotiated_amount,
                    'customer_price'=>$auction->deducted_amount,
                ];
            }
            $date = date('Y-m-d', strtotime($this->UaeDate(Carbon::now())));
            $fileName = 'Auction Report '.$date;
            $exfie = Excel::create($fileName, function ($excel) use ($auctionsArray) {
                $excel->setTitle('Cashed Auctions');
                $excel->setCreator('Admin')->setCompany('Wecashanycar');
                $excel->setDescription('Traders file');
                $excel->sheet('sheet1', function ($sheet) use ($auctionsArray) {
                    $sheet->fromArray($auctionsArray, null, 'A1', false, false);
                });
                // })->download('xls');
            });
            \Mail::send([], [], function ($m) use ($exfie, $fileName) {
                $m->to($emails)->subject($fileName);
                $m->attach($exfie->store("xls", false, true)['full']);
            });
            unlink(storage_path('exports/'.$fileName.'.xls'));
        }
        
    }

   

    public function getBidDetails($auctionId) {
        $bid = \App\Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->first();
        $trader = \App\TraderUser::where('id', $bid->trader_id)->first();

        $array = ['dealer_name' => $trader->first_name.' '.$trader->last_name, 'bid_amount' => $bid->price];
        return $array;
    }

    public function convertTimeToUTCzone($str, $userTimezone='Asia/Dubai', $format = 'Y-m-d H:i:s'){
		$date = new DateTime($str, new DateTimeZone('Asia/Dubai'));
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format('Y-m-d H:i:s');
    }
    
    public function UaeDate($dts) {
        $date = new DateTime($dts, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Asia/Dubai'));
        return $date->format('Y-m-d H:i:s');
    }
}
