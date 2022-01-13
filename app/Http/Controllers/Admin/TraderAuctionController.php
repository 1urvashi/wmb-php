<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Datatables;
use DB;
use App\Auction;
use App\Bid;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Log;
use App\TraderUser;
use URL;
use GuzzleHttp;
use DateTime;
use DateTimeZone;
use Redirect;
use Gate;
use App\Object;
use Excel;

class TraderAuctionController extends Controller
{
    // public function __construct()
    // {
    //     $user = Auth::guard('admin')->user();
    //     if ($user->type != config('globalConstants.TYPE.ADMIN') || $user->type != config('globalConstants.TYPE.SUPER_ADMIN') || $user->type != config('globalConstants.TYPE.DRM')) {
    //         return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
    //     }
    // }
    public function index() {
        $latestBidPrice = Auction::where('status', 3)->max('buy_price');
        $drm_users = \App\User::where('type', config('globalConstants.TYPE.DRM'))->get();
        $user = Auth::guard('admin')->user();
        return view('admin.modules.trader_auction.index', compact('drm_users', 'latestBidPrice'));
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));

        $user = Auth::guard('admin')->user();
        $aStatus = 3;

        if($user->type == config('globalConstants.TYPE.ADMIN') || $user->type == config('globalConstants.TYPE.SUPER_ADMIN')) {
            $auctions = Auction::Join('trader_users', 'trader_users.id', '=', 'auctions.bid_owner')
                                // ->Join('users', 'users.id', '=', 'trader_users.dmr_id')
                                ->Join('bids', 'bids.auction_id', '=', 'auctions.id')
                                ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'auctions.id','auctions.title', 'auctions.object_id', 'auctions.start_time', 'auctions.end_time','auctions.min_increment','auctions.type','auctions.status','auctions.base_price','auctions.is_negotiated','auctions.buy_price', 'auctions.final_req_amount', 'auctions.bid_owner',
                                // 'users.name as userName', 'users.id as userId', 'trader_users.dmr_id',
                                 'trader_users.id as trader_id', DB::raw('MAX(bids.price) AS price')])
                                ->where('auctions.status', $aStatus)
                                ->orderBy('auctions.updated_at', 'desc')
                                ->groupBy('bids.auction_id')
                                ->get();
        }
         elseif($user->type == config('globalConstants.TYPE.DRM')) {
            $auctions = Auction::Join('trader_users', 'trader_users.id', '=', 'auctions.bid_owner')
                                ->Join('bids', 'bids.auction_id', '=', 'auctions.id')
                                ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'auctions.id','auctions.title', 'auctions.object_id', 'auctions.start_time', 'auctions.end_time','auctions.min_increment','auctions.type','auctions.status','auctions.base_price','auctions.is_negotiated','auctions.buy_price', 'auctions.final_req_amount', 'auctions.bid_owner', 'trader_users.dmr_id', 'trader_users.id as trader_id', DB::raw('MAX(bids.price) AS price')])
                                ->where('auctions.status', $aStatus)
                                ->where('trader_users.dmr_id', $user->id)
                                ->orderBy('auctions.updated_at', 'desc')
                                ->groupBy('bids.auction_id')
                                ->get();
        }
         else {
            $auctions = Auction::Join('trader_users', 'trader_users.id', '=', 'auctions.bid_owner')
                                ->Join('bids', 'bids.auction_id', '=', 'auctions.id')
                                ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'auctions.id','auctions.title', 'auctions.object_id', 'auctions.start_time', 'auctions.end_time','auctions.min_increment','auctions.type','auctions.status','auctions.base_price','auctions.is_negotiated','auctions.buy_price', 'auctions.final_req_amount', 'auctions.bid_owner', 'trader_users.dmr_id', 'trader_users.id as trader_id', DB::raw('MAX(bids.price) AS price')])
                                ->where('auctions.status', $aStatus)
                                ->orderBy('auctions.updated_at', 'desc')
                                ->groupBy('bids.auction_id')
                                ->get();
        }

        return Datatables::of($auctions)
                ->addColumn('customerAmount', function ($auctions) use ($request,$user) {
                    $inspectorNegaotiate = \App\InspectorNegaotiate::where('auction_id', $auctions->id)->orderBy('created_at', 'desc')->first();
                    if ($inspectorNegaotiate) {
                        return $inspectorNegaotiate->customer_amount;
                    } else {
                        return "0.00";
                    }
                })
                ->addColumn('id', function ($auctions) use ($request) {
                    return '<a href="auctions/view/' . $auctions->id . '">'.$auctions->id.'</a>';

                })
                ->addColumn('bid_owner', function ($auctions) use ($request) {
                    $trader = \App\TraderUser::where('id', $auctions->bid_owner)->first();

                    // print_r($trader);
                    $trader_name = !empty($trader) ? $trader->first_name.' '.$trader->last_name : '';
                    if(!empty($trader_name) ){

                        return '<a href="traders/' . $trader->id . '" class="btn btn-primary btn-sm">'.$trader_name.'</a>';
                    }
                    // else{

                    //     return '<a href="traders" class="btn btn-primary btn-sm">'.$trader_name.'</a>';
                    // }

                })
                ->editColumn('base_price', function ($auctions) use ($user) {
                    return $auctions->base_price;
                })
                ->editColumn('start_time', function ($auction) {
                    return $this->UaeDate($auction->start_time);
                })
                ->editColumn('end_time', function ($auction) {
                    return $this->UaeDate($auction->end_time);
                })
                ->editColumn('type', function ($auction) {
                    return $auction->getAuctionType($auction->type);
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->has('drm') && ($request->get('drm')!=0)) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return ($row['dmr_id'] == $request->get('drm')) ? true : false;
                        });
                    }
                    if ($request->has('traders') && ($request->get('traders')!=0)) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return ($row['trader_id'] == $request->get('traders')) ? true : false;
                        });
                    }
                    if ($request->has('type') && ($request->get('type')!=0)) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return ($row['type'] == $request->get('type')) ? true : false;
                        });
                    }
                    if ($request->has('bid_price')) {
                        $bidRange = explode(',', $request->bid_price);
                        $instance->collection = $instance->collection->filter(function ($row) use ($request, $bidRange) {
                            return ((int)$row['price'] >= (int)$bidRange[0] && (int)$row['price'] <= (int)$bidRange[1]) ? true : false;
                        });
                    }
                    if ($request->has('from') && ($request->get('from')!=0)) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return (date('Y-m-d', strtotime($row['start_time'])) >= date('Y-m-d', strtotime($request->get('from')))) ? true : false;
                        });
                    }
                    if ($request->has('to') && ($request->get('to')!=0)) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return (date('Y-m-d', strtotime($row['end_time'])) <= date('Y-m-d', strtotime($request->get('to')))) ? true : false;
                        });
                    }
                    if ($request->has('search') && ($request->get('search')!='')) {
                        $needle = strtolower($request->get('search'));
                        $instance->collection = $instance->collection->filter(function ($row) use ($request,$needle) {
                            $row = $row->toArray();
                            $result = 0;
                            foreach ($row as $key => $value) {
                                if (strpos(strtolower($value), $needle) > -1) {
                                    $result = 1;
                                }
                            }
                            return $result ? true : false;
                        });
                    }
                })
                ->make(true);
    }

    public function getTraders(Request $request) {
        $user = Auth::guard('admin')->user();
        // $traders = TraderUser::where('dmr_id', $request->drm_id)->get();
        $traders = TraderUser::Join('auctions','auctions.bid_owner','=','trader_users.id')
        ->select('trader_users.id','trader_users.first_name','trader_users.last_name')
        ->where('auctions.status', 3)
        ->distinct()
        ->get();

        $html = '<option value="0">All</option>';
        foreach ($traders as $trader) {
            $html .= "<option value='" . $trader->id . "'>" . $trader->first_name . '' . $trader->last_name . "</option>";
        }
        return $html;
    }

    public function getDatas() {
        $acutions = \App\Auction::Join('objects', 'objects.id', '=', 'auctions.object_id')
                                ->Join('dealer_users', 'dealer_users.id', '=', 'auctions.dealer_id')
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
                                'dealer_users.name as dealer_name', 'dealer_users.id as dealer_id',
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
                'dealer_name'=>$acution->dealer_name,
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
}
