<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use DB;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Validator;
use Auth;
use Gate;
use Mail;

class TestExportController extends Controller
{
    public function getDatas() {
        set_time_limit(0);
        $acutions = \App\Auction::Join('objects', 'objects.id', '=', 'auctions.object_id')
                                // ->Join('dealer_users', 'dealer_users.id', '=', 'auctions.dealer_id')
                                ->Join('makes', 'makes.id', '=', 'objects.make_id')
                                ->Join('models', 'models.id', '=', 'objects.model_id')
                                ->Join('bids', 'bids.auction_id', '=', 'auctions.id')
                                ->Join('object_attribute_values', 'object_attribute_values.object_id', '=', 'objects.id')
                                ->Join('trader_users', 'trader_users.id', '=', 'auctions.bid_owner')
                                ->LeftJoin('users', 'users.id', '=', 'trader_users.dmr_id')
                                ->groupBy('bids.auction_id')
                                ->select('makes.name as make_name', 'models.name as model_name', 
                                DB::raw('MAX(bids.price) AS price'), 
                                'object_attribute_values.color as color', 
                                'objects.vin', 
                                'objects.id as object_id', 
                                'trader_users.first_name as dealer_first_name', 'trader_users.id as dealer_id', 
                                'trader_users.last_name as dealer_last_name',
                                'users.name as DRM_name', 'auctions.type as deal_status', 'auctions.id', 
                                'auctions.updated_at as transaction_date')
                                ->where('auctions.status', 8)->get();
        $header = ['Make/Car','Model Year', 'Color', 'VIN#', 'Selling Price', 'Dealer Name',  'Dealer ID', 'DRM Name', 'Deal Status', 'Auction ID', 'Transaction date'];
        $acutionsArray = [];
        // dd($acutions);
        $acutionsArray[] = $header;
        foreach($acutions as $acution) {
            $date = $this->UaeDate($acution->transaction_date);
            $acutionsArray[] = [
                'make_name'=> $acution->make_name,
                'model_name'=> $acution->model_name,
                'color' => $this->getColor($acution->object_id), 
                'vin'=> $acution->vin ? $acution->vin : "N/A",
                'price'=>$acution->price, 
                'dealer_name'=>$acution->dealer_first_name.' '.$acution->dealer_last_name,  
                'dealer_id'=> $acution->dealer_id, 
                'DRM_name' => $acution->DRM_name ? $acution->DRM_name : 'N/A',
                'deal_status' => $acution->getAuctionType($acution->deal_status),
                'id' => $acution->id,
                'transaction_date' => date('Y-m-d', strtotime($date))
            ];
        }
        // dd($acutionsArray);
        $fileName = 'cashed_auctions_'.time();
        Excel::create($fileName, function ($excel) use ($acutionsArray) {
            $excel->setTitle('Cashed Auctions');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Traders file');
            $excel->sheet('sheet1', function ($sheet) use ($acutionsArray) {
                $sheet->fromArray($acutionsArray, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function getColor($id) {
        $color = \App\ObjectAttributeValue::where('object_id', $id)->where('attribute_id', 13)->first()->attribute_value;
        return $color;
    }

    public function getTraderDatas() {
        set_time_limit(0);
        $traders = \App\TraderUser::orderBy('created_at', 'desc')->get();
        $header = ['Trader ID', 'Trader Name','Number of Bids', 'Number of Won Bids', 'Number of Cars'];
        $tradersArray = [];
        $tradersArray[] = $header;

        foreach ($traders as $trader) {
            $tradersArray[] = [
                'dealer_id' => $trader->id,
                'name'=> $trader->first_name.' '.$trader->last_name,
                'total_bids'=> $this->allBids($trader->id) ? $this->allBids($trader->id) : "0",
                'won_bis' => $this->wonBids($trader->id) ? $this->wonBids($trader->id) : "0", 
                'cars'=>  $this->cars($trader->id) ? $this->cars($trader->id) : "0"
            ];
        }
        // dd($tradersArray);
        $fileName = 'cashed_auctions_traders'.time();
        Excel::create($fileName, function ($excel) use ($tradersArray) {
            $excel->setTitle('Cashed Auctions');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Traders file');
            $excel->sheet('sheet1', function ($sheet) use ($tradersArray) {
                $sheet->fromArray($tradersArray, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function allBids($traderId) {
        $bids = \App\Bid::where('bids.trader_id', $traderId)->count();
        return $bids;
    }

    public function wonBids($traderId) {
        $auctions = \App\Auction::where('bid_owner', $traderId)->count();
        return $auctions;
    }

    public function cars($traderId) {
        $cars = \App\Auction::where('bid_owner', $traderId)
                                ->where('status', 8)
                                ->count();
        return $cars;
    }

    public function downloadReport() {
        $user =Auth::guard('admin')->user();
        // if($user->type == config('globalConstants.TYPE.SUPER_ADMIN') || $user->type == config('globalConstants.TYPE.ADMIN')) {
        //     return view('admin.modules.reports.auction-completed');
        // }
        if (Gate::denies('auction_Export-Completed-Auction')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.reports.auction-completed');
        
    }

    public function getAuctions(Request $request) {
        if (Gate::denies('auction_Export-Completed-Auction')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        set_time_limit(0);
        $validator = Validator::make($request->all(), [
            'date' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        set_time_limit(0);
        $from_time = $this->convertTimeToUTCzone(date('Y-m-d H:i:s', strtotime($request->date."00:00:00")));
        $to_time = $this->convertTimeToUTCzone(date('Y-m-d H:i:s', strtotime($request->date."23:59:59")));
        $auctions = \App\Auction::where('status', 3)->where('end_time', ">=", $from_time)->where('end_time', "<=", $to_time)->get();
        $header = ['Id','Title', 'Start Time', 'End Time', 'Type', 'Dealer Name',  'Status', 'Bid Price', 'Negotiated Price', 'Price to selling customer'];
        $auctionsArray = [];
        $auctionsArray[] = $header;
        if (!$auctions->isEmpty()) {
            foreach ($auctions as $auction) {
                $getBidData = $this->getBidDetails($auction->id);
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
            $date = date('Y-m-d H:i:s', strtotime($this->UaeDate(Carbon::now())));
            $fileName = 'auctions_'.$date;
            Excel::create($fileName, function ($excel) use ($auctionsArray) {
                $excel->setTitle('Cashed Auctions');
                $excel->setCreator('Admin')->setCompany('Wecashanycar');
                $excel->setDescription('Traders file');
                $excel->sheet('sheet1', function ($sheet) use ($auctionsArray) {
                    $sheet->fromArray($auctionsArray, null, 'A1', false, false);
                });
            })->download('xls');            
        } else {
            return redirect('download-report')->with('error', 'No Auctions available for the day');
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

}
