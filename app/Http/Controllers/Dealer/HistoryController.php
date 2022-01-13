<?php

namespace App\Http\Controllers\Dealer;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\DealerUser;
use App\TraderUser;
use DB;
use App\Auction;
use Datatables;
use Carbon\Carbon;
use Auth;

class HistoryController extends Controller
{
    public function History() {
        $traders = TraderUser::where('dealer_id',Auth::guard('dealer')->user()->id)->get();
        return view('dealer.modules.history.index',compact('traders'));
    }

    public function data(Request $request) {
      DB::statement(DB::raw('set @rownum=0'));
      $auction = Auction::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','title', 'start_time','dealer_id','end_time','base_price','min_increment','type','bid_owner','object_id','status'])
      ->where('dealer_id', Auth::guard('dealer')->user()->id)
      ->where('status','>=',3)->orderBy('id', 'desc')->get();
      // return $auction;
      return Datatables::of($auction)
              ->addColumn('bid_owners', function ($auction) {
                if($auction->getOriginal('status') == 7 || $auction->getOriginal('status') == 8){
                    return $auction->tradersBid ? '<a href="traders/' . $auction->tradersBid->id .'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> '. $auction->tradersBid->first_name .'</a>' : '';
                }else{
                    return '';
                }

              })
              ->addColumn('objects_id', function ($auction) {
                      return '<a href="object/detail/'. $auction->vehiclesHistory->id .'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> '. $auction->vehiclesHistory->name .'</a>';


              })
              ->addColumn('auction_detail', function ($auction) {

                      return '<a href="auctions/view/'. $auction->id .'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View details</a>';


              })
              ->editColumn('type', function ($auction) {
                  return $auction->getAuctionType($auction->type);
              })

              ->editColumn('start_time', function ($auction) {
                  return $this->UaeDate($auction->start_time);
              })
              ->editColumn('end_time', function ($auction) {
                  return $this->UaeDate($auction->end_time);
              })
              ->addColumn('bid_price', function ($auction) {
                  return $auction->lastBid();
              })
              ->addColumn('last_bid_date', function ($auction) {
                  return $this->UaeDate($auction->lastBidDate());
              })
              ->filter(function ($instance) use ($request) {

                  if ($request->has('status')) {
                          $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                              if($row->getOriginal('status') && $row->getOriginal('status') >= 3){
                                  return ($row->getOriginal('status') == $request->get('status')) ? true : false;
                              }
                          });
                  }
                  if ($request->has('start_time') && ($request->get('start_time')!=0)) {
                      $date = Carbon::parse($request->get('start_time'))->format('Y-m-d H:i:s');
                          $instance->collection = $instance->collection->filter(function ($row) use ($request,$date) {
                              $rowDate = Carbon::parse($this->UaeDate($row['start_time']))->format('Y-m-d H:i:s');
                              return ($rowDate >= $date) ? true : false;
                          });
                  }
                  if ($request->has('end_time') && ($request->get('end_time')!=0)) {
                      $date = Carbon::parse($request->get('end_time'))->format('Y-m-d H:i:s');
                          $instance->collection = $instance->collection->filter(function ($row) use ($request,$date) {
                              $rowDate = Carbon::parse($this->UaeDate($row['end_time']))->format('Y-m-d H:i:s');
                              return ($rowDate <= $date) ? true : false;
                          });
                  }
                  if ($request->has('search') && ($request->get('search')!='')) {
                        $needle = strtolower($request->get('search'));
                        $instance->collection = $instance->collection->filter(function ($row) use ($request,$needle) {
                          $row = $row->toArray();
                          $result = 0;
                          foreach ($row as $key => $value) {
                            if(strpos(strtolower($value), $needle) > -1) {
                              $result = 1;
                            }
                          }
                          return $result ? true : false;
                        });
                  }
            })
          ->make(true);
    }
}
