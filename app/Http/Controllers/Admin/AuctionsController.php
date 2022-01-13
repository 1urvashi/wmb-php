<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Validator;
use Datatables;
use DB;
use App\Object;
use App\DealerUser;
use App\Auction;
use App\Bid;

use Carbon\Carbon;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\AutomaticBid;
use App\TraderUser;
use URL;
use Mail;
use Image;
use GuzzleHttp;
use DateTime;
use DateTimeZone;
use App\AttributeSet;
use App\InspectorNegaotiate;
use Redirect;
use Gate;

class AuctionsController extends Controller
{
    public function __construct()
    {
        $user = Auth::guard('admin')->user();
        // if (Gate::denies('auctionsMenu')) {
        //     return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Auction $auction, $type = '')
    {
        $user = Auth::guard('admin')->user();

        if ($type == 'ongoing') {
            if (Gate::denies('auction_ongoing')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'closed') {
            if (Gate::denies('auction_closed')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'scheduled') {
            if (Gate::denies('auction_scheduled')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'qualitycheck') {
            if (Gate::denies('auction_qualitycheck')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'cancel-closed') {
            if (Gate::denies('auction_cancel-closed')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'canceled') {
            if (Gate::denies('auction_canceled')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'passcheck') {
            if (Gate::denies('auction_passcheck')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'failcheck') {
            if (Gate::denies('auction_failcheck')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'sold') {
            if (Gate::denies('auction_sold')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        } elseif ($type == 'cash') {
            if (Gate::denies('auction_cash')) {
                return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
            }
        }




        $auctionModel = new Auction();
        if ($type == 'completed') {
            return view('admin.modules.auction.index', compact('type'));
        } elseif ($type == 'ongoing') {
            return view('admin.modules.auction.ongoing', compact('auctionModel'));
        } elseif ($type == 'canceled') {
            return view('admin.modules.auction.canceled', compact('type'));
        } elseif ($type == 'closed') {
            $drm_users = \App\User::where('type', config('globalConstants.TYPE.DRM'))->get();
            return view('admin.modules.auction.closed', compact('type', 'drm_users'));
        } else {
            return view('admin.modules.auction.completed', compact('type'));
        }
    }
    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        //[DB::raw('@rownum  := @rownum  + 1 AS rownum'),

        if ($request->type == 'ongoing') {
            $aStatus = 1;
        } elseif ($request->type == 'closed') {
          $aStatus = 3;
        } elseif ($request->type == 'scheduled') {
            $aStatus = 0;
        } elseif ($request->type == 'qualitycheck') {
            $aStatus = 4;
        } elseif ($request->type == 'cancel-closed') {
            $aStatus = 10;
        } elseif ($request->type == 'canceled') {
            $aStatus = 12;
        } elseif ($request->type == 'passcheck') {
            $aStatus = 6;
        } elseif ($request->type == 'failcheck') {
            $aStatus = 5;
        } elseif ($request->type == 'sold') {
            $aStatus = 7;
        } elseif ($request->type == 'cash') {
            $aStatus = 8;
        }

        // leftJoin('objects', 'objects.id', '=', 'auctions.object_id')
        $user = Auth::guard('admin')->user();
        if($request->type == 'closed') {
            $auctions = Auction::Join('trader_users', 'trader_users.id', '=', 'auctions.bid_owner')
                                ///->Join('users', 'users.id', '=', 'trader_users.dmr_id')
                                ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'auctions.id','auctions.title', 'auctions.object_id', 'auctions.start_time', 'auctions.end_time','auctions.min_increment','auctions.type','auctions.status','auctions.base_price','auctions.is_negotiated','auctions.buy_price', 'auctions.final_req_amount', 'auctions.deducted_amount',  'auctions.bid_owner','auctions.isAccept'])
                                ->where('auctions.status', $aStatus)
                                ->orderBy('auctions.id', 'desc')->get();
        } else {
            $auctions = Auction::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','title', 'object_id', 'start_time', 'end_time','min_increment','type','status','base_price','is_negotiated','buy_price', 'final_req_amount', 'bid_owner', 'deducted_amount','isAccept'])
                                ->where('status', $aStatus)
                                ->orderBy('id', 'desc')->get();
        }

        return Datatables::of($auctions)
               ->addColumn('customerAmount', function ($auctions) use ($request,$user) {
                //    $inspectorNegaotiate = \App\InspectorNegaotiate::where('auction_id', $auctions->id)->orderBy('created_at', 'desc')->first();
                //    if ($inspectorNegaotiate) {
                //        return $inspectorNegaotiate->customer_amount;
                //    } else {
                //        return "0.00";
                //    }
                return $auctions->deducted_amount;
               })
               ->addColumn('bid_owner', function ($auctions) use ($request) {

                $trader = \App\TraderUser::where('id', $auctions->bid_owner)->first();
                $trader_name = !empty($trader) ? $trader->first_name.' '.$trader->last_name : '';
                return $trader_name;

               })
               ->addColumn('suggestedAmount', function ($auctions) use ($request) {
                   $object = Object::where('id', $auctions->object_id)->first();
                   if ($request->type == 'closed') {
                       return $object ? $object->suggested_amount : '';
                   } else {
                       return '';
                   }
               })
               ->editColumn('base_price', function ($auctions) use ($user) {
                   if (Gate::allows('auction-column_base-price-read')) {
                       return $auctions->base_price;
                   } else {
                       return '';
                   }
               })
               ->editColumn('isAccept', function ($auctions) use ($user) {
                   if ($auctions->isAccept == 0) {
                       return 'Accept';
                   } else {
                       return 'Accepted';
                   }
               })

            ->addColumn('action', function ($auctions) use ($request) {
                if ($request->type == 'ongoing') {
                    return '<a href="view/' . $auctions->id . '" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-eye"></i> View</a>
					<a href="stop/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to stop this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Stop</a>
						<a href="cancel/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to cancel this Auction?\');" class="btn btn-margin btn-xs btn-danger"><i class="fa fa-trash-o"></i> Cancel</a>';
                } elseif ($request->type == 'closed') {
                    $user = Auth::guard('admin')->user();

                    if (($auctions->type == 5000)) {
                         $inspector_nego = \App\InspectorNegaotiate::where('auction_id', $auctions->id)->count();
                        $txt = '';
                        if ($user && !is_null($user) && Gate::allows('auction-button_reopen')) {
                            $txt .= '<a href="reopen/' . $auctions->id . '" class="duplicate-button btn btn-margin btn-xs btn-warning" onclick="return confirm(\'Are you sure you want to reopen this Auction?\');"><i class="fa fa-history"></i> Reopen</a>';
                        }
                      /*  if ($user && !is_null($user) && Gate::allows('auction-button_negotiate') && ($auctions->is_negotiated == 0)) {
                            $txt .= '<a href="negotiate/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to negotiate this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Negotiate</a>';
                        }*/
                        if ($user && !is_null($user) && Gate::allows('auction-button_view')) {
                            $txt .= '<a href="view/' . $auctions->id . '" class="btn btn-margin btn-xs btn-success"><i class="fa fa-eye"></i> View</a>';
                        }
                        if ($user && !is_null($user) && Gate::allows('auction-button_cash')) {
                            if ($auctions->isAccept == 1) {
                              $txt .= '<a href="cashed/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to cash this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-money"></i> Cash</a>';
                            }else{
                                $txt .= '<a href="javascript:void(0);" onclick="return confirm(\'Dealer has not accepted the Auction please wait.\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-money"></i> Cash </a>';
                            }
                        }
                        /*if ($user && !is_null($user) && $user->isAllowed('closeAuctionNegotiateAction') && (!empty($auctions->final_req_amount)) && ($inspector_nego == 0)) {*/
                        /*if ($user && !is_null($user) && Gate::allows('auction-button_inspector-negotiate')) {
                            $txt .= '<a href="inspector-negotiate/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to negotiate this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Inspector Negotiate</a>';
                        }
                        if ($user && !is_null($user) && Gate::allows('auction-button_override-bid-amount')) {
                            $txt .= '<a href="override_bid_amount/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to Override Bid Amount this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Override Bid Amount</a>';
                        }*/
                        /*if ($user && !is_null($user) && Gate::allows('auction-button_negotiate-with-bid-owner') && ($auctions->is_negotiated == 1) && (strtotime($auctions->end_time) < strtotime(date('Y-m-d H:i:s'))) && (empty($auctions->final_req_amount))) {
                            $txt .= '<a href="owner_negototiate/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to negotiate with bid owner?\');" class="btn btn-margin btn-xs btn-success"><i class="fa fa-pencil-square-o"></i> Negotiate with Bid Owner</a>';
                        }*/
                        if ($user && !is_null($user) && Gate::allows('auction-button_cancel')) {
                            $txt .= '<a href="cancel-closed/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to cancel this Auction?\');" class="btn btn-margin btn-xs btn-danger"><i class="fa fa-trash-o"></i> Cancel</a>';
                        }
                        return $txt;
                    } else {
                        $txt = '';
                        if ($user && !is_null($user) && Gate::allows('auction-button_reopen')) {
                            $txt .= '<a href="reopen/' . $auctions->id . '" class="duplicate-button btn btn-margin btn-xs btn-warning" onclick="return confirm(\'Are you sure you want to reopen this Auction?\');"><i class="fa fa-history"></i> Reopen</a>';
                        }
                        if ($user && !is_null($user) && Gate::allows('auction-button_view')) {
                            $txt .= '<a href="view/' . $auctions->id . '" class="btn btn-margin btn-xs btn-success"><i class="fa fa-eye"></i> View</a>';
                        }
                      /*  if ($user && !is_null($user) && Gate::allows('auction-button_inspector-negotiate')) {
                            $txt .= '<a href="inspector-negotiate/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to negotiate this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Inspector Negotiate</a>';
                        }*/
                        
                        if ($user && !is_null($user) && Gate::allows('auction-button_cash')) {
                            if ($auctions->isAccept == 1) {
                            $txt .= '<a href="cashed/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to cash this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-money"></i> Cash </a>';
                            }else{
                                $txt .= '<a href="javascript:void(0);" onclick="return confirm(\'Dealer has not accepted the Auction please wait.\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-money"></i> Cash </a>';
                            }
                        }
                        if ($user && !is_null($user) && Gate::allows('auction-button_cancel')) {
                            $txt .= '<a href="cancel-closed/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to cancel this Auction?\');" class="btn btn-margin btn-xs btn-danger"><i class="fa fa-trash-o"></i> Cancel</a>';
                        }
                        return $txt;
                    }
                }  elseif ($request->type == 'cash') {
                    $user = Auth::guard('admin')->user();
                    $txt = '';
                    if ($user && !is_null($user) && Gate::allows('auction-button_reopen')) {
                        $txt .= '<a href="reopen/' . $auctions->id . '" class="duplicate-button btn btn-margin btn-xs btn-warning" onclick="return confirm(\'Are you sure you want to reopen this Auction?\');"><i class="fa fa-history"></i> Reopen</a>';
                    }
                    /*if ($user && !is_null($user) && Gate::allows('auction-button_send-reminder')) {
                        $txt .= '<a href="sendreminder/' . $auctions->id . '"  class="btn btn-margin btn-xs btn-danger"><i class="fa fa-shopping-cart"></i> Send Reminder</a>';
                    }*/

                    /*if ($user && !is_null($user) && Gate::allows('auction-button_download-and-print')) {
                        //route('shows_home')
                        $txt .= '<a target="_blank" href="' . URL::route('object-sold-download', $auctions->id). '" class="btn btn-margin btn-xs btn-warning"><i class="fa fa-eye"></i>Download & Print</a>';
                    }*/

                    if ($user && !is_null($user) && Gate::allows('auction-button_view')) {
                        $txt .= '<a href="view/' . $auctions->id . '" class="btn btn-margin btn-xs btn-success"><i class="fa fa-eye"></i> View</a>';
                    }

                    if ($user && !is_null($user) && Gate::allows('auction-button_ready-to-sale')) {
                        $txt .= '<a href="readysale/' . $auctions->id . '" onclick="return confirm(\'Are you sure?\');" class="btn btn-margin btn-xs btn-danger"><i class="fa fa-shopping-cart"></i>Sold</a>';
                    }
                    if ($user && !is_null($user) && Gate::allows('auction-button_cancel')) {
                        $txt .= '<a href="cancel-closed/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to cancel this Auction?\');" class="btn btn-margin btn-xs btn-danger"><i class="fa fa-trash-o"></i> Cancel</a>';
                    }
                    //'<a href="destroy/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to delete this Auction?\');" class="btn btn-margin btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                    return $txt;
                } elseif ($request->type == 'canceled' || $request->type == 'cancel-closed') {
                    $user = Auth::guard('admin')->user();
                    $txt = '';
                    if ($user && !is_null($user) && Gate::allows('auction-button_reopen')) {
                        $txt .= '<a href="reopen/' . $auctions->id . '" class="duplicate-button btn btn-margin btn-xs btn-warning" onclick="return confirm(\'Are you sure you want to reopen this Auction?\');"><i class="fa fa-history"></i> Reopen</a>';
                    }
                    if ($user && !is_null($user) && Gate::allows('auction-button_view')) {
                        $txt .= '<a href="view/' . $auctions->id . '" class="btn btn-margin btn-xs btn-success"><i class="fa fa-eye"></i> View</a>';
                    }
                    return $txt;
                //'<a href="' . $auctions->id . '/edit" onclick="return confirm(\'Are you sure you want to resubmit this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Resubmit Auction</a>';
                } elseif ($request->type == 'scheduled') {
                    $user = Auth::guard('admin')->user();
                    $txt = '';
                    if ($user && !is_null($user) && Gate::allows('auction-button_view')) {
                        $txt .= '<a href="view/' . $auctions->id . '" class="btn btn-margin btn-xs btn-success"><i class="fa fa-eye"></i> View</a>';
                    }
                    return $txt;
                //'<a href="' . $auctions->id . '/edit" onclick="return confirm(\'Are you sure you want to resubmit this Auction?\');" class="btn btn-margin btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Resubmit Auction</a>';
                } else {
                    $user = Auth::guard('admin')->user();
                    $txt = '';
                    if ($user && !is_null($user) && Gate::allows('auction-button_reopen')) {
                        $txt .= '<a href="reopen/' . $auctions->id . '" class="duplicate-button btn btn-margin btn-xs btn-warning" onclick="return confirm(\'Are you sure you want to reopen this Auction?\');"><i class="fa fa-history"></i> Reopen</a>';
                    }
                    if ($user && !is_null($user) && Gate::allows('auction-button_view')) {
                        $txt .= '<a href="view/' . $auctions->id . '" class="btn btn-margin btn-xs btn-success"><i class="fa fa-eye"></i> View</a>';
                    }
                    if ($user && !is_null($user) && Gate::allows('auction-button_cancel')) {
                        $txt .= '<a href="cancel-closed/' . $auctions->id . '" onclick="return confirm(\'Are you sure you want to cancel this Auction?\');" class="btn btn-margin btn-xs btn-danger"><i class="fa fa-trash-o"></i> Cancel</a>';
                    }
                    return $txt;
                }
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

        // ->editColumn('buy_price', function ($auction) use ($request) {
        // 	if (!$request->type == 'canceled' || $request->type == 'cash' || $request->type == 'passcheck' || $request->type =='sold' || $request->type == 'qualitycheck' || $request->type == 'closed' || $request->type == 'failcheck' ) {
         //           return $auction->bids->max()->price;
        // }
         //        })

         ->editColumn('buy_price', function ($auction) use ($request, $user) {
            //  if (Gate::denies('buyPriceView')) {
            //      return '';
            //  }
             if (!$request->type == 'canceled' || $request->type == 'cash' || $request->type == 'passcheck' || $request->type =='sold' || $request->type == 'qualitycheck' || $request->type == 'closed' || $request->type == 'cancel-closed' || $request->type == 'failcheck') {
                 // return 1;
                 $bidPrice = \App\Bid::where('auction_id', $auction->id)->orderBy('price', 'desc')->first();
                 if ($bidPrice) {
                     return $bidPrice->price;
                 } else {
                     return "0.00";
                 }
             }
         })

             ->filter(function ($instance) use ($request) {
                //  if ($request->has('type')) {
                //      $instance->collection = $instance->collection->filter(function ($row) use ($request) { //var_dump($row); exit;
                //          return $this->_matchRecord($row, $request->type);
                //      });
                //  }
                if ($request->has('drm') && ($request->get('drm')!=0)) {
                    $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                        return ($row['dmr_id'] == $request->get('drm')) ? true : false;
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



    public function getDeduct(Request $request, $id) {
        $auction = Auction::find($id);
        $lastBid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        // $bidPrice = $lastBid ? $lastBid->price : 0;
        $bidPrice = $request->nAmount ? $request->nAmount : 0;

        // if ((!empty($bidPrice)) && ($bidPrice <  $lastBid->price)) {
        //     return response()->json(["status" => 100,'message' => 'Negotaite Price should be greater than last bid Price']);
        // }
        $saleType = [];
        $saleType = $this->getSalePrice($auction, $bidPrice, $auction->other_amount);

        $margin = 0;
        // $bidPrice = 105000;
        $vat = 0;
        $margin_amount = 0;

        if($saleType['sales_type_type'] == 1) {
            if(empty($saleType['flatValue'])) {
                $vat = $bidPrice * ($saleType['vat'] / 100);
                $margin = $bidPrice * ($saleType['percentageValue'] / 100);
                $margin_amount = $margin;
            } else {
                $vat = $bidPrice * ($saleType['vat'] / 100);
                $margin = $saleType['flatValue'];
                $margin_amount = $margin;
            }
        } else {
            if(empty($saleType['flatValue'])) {
                $margin = $bidPrice * ($saleType['percentageValue'] / 100);
                $vat = $margin * ($saleType['vat'] / 100);
                $margin_amount = $margin - $vat;
            } else {
                $margin = $saleType['flatValue'];
                $vat = $margin * ($saleType['vat'] / 100);
                $margin_amount = $margin - $vat;
            }
        }

        $other_amount = $auction->other_amount;

        return view('admin.modules.auction.get-deduction', compact('auction', 'saleType', 'other_amount', 'margin_amount', 'vat', 'bidPrice', 'lastBid'));
    }



    public function negotiateCreate($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_negotiate')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $object = Object::find($auction->object_id);
        //$auction = new Auction();
        // $types = $auction->getAuctionTypes();

        //var_dump($object->code); exit;
        $lastBid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        $bidPrice = $lastBid ? $lastBid->price : 0;
        return view('admin.modules.auction.negaotiate', compact('object', 'auction', 'bidPrice'));
    }


    public function negotiateStore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'negotiate_price' => 'required'
            // 'end_time' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $auction = Auction::find($id);

        $lastBid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        $bidPrice = $lastBid ? $lastBid->price : 0;


        if ((!empty($request->negotiate_price)) && ($request->negotiate_price <  $bidPrice)) {
            return redirect()->back()->withError('Negotaite Price should be greater than last bid Price')->withInput();
        }

        $currentTime = strtotime($this->UaeDate(Carbon::now()));
        // $endTime = strtotime($this->UaeDate($request->end_time));

        // if (!$request->formin) {
        //     if ($endTime <= $currentTime) {
        //         return redirect()->back()->withError('End time should be greater than current time')->withInput();
        //     }
        // }

        // $end = Carbon::parse($auction->end_time);
        // $diff = Carbon::now()->diffInSeconds($end);


        // if (!empty($diff) && ($diff < 120)) {
        //     return redirect()->back()->withError('Please wait for 2 minutes before submitting for negotiation')->withInput();
        // }

        if ($auction->status != $auction->getStatusType(3)) {
            return redirect()->back()->withError('Unable to negotiate current auction');
        }

        $now = $this->UaeDate(Carbon::now());
        $newTime = date('Y-m-d H:i:s', strtotime($now." +5 minutes"));
        $auction->status = $auction->getStatusType(1);
        $auction->negotiated_amount = $request->negotiate_price;
        $auction->negotiatedTime = Carbon::now();
        $auction->is_negotiated = 1;

        // $auction->end_time = $request->formin ? $newTime : date('Y-m-d H:i', strtotime($request->end_time));
        $auction->end_time = $newTime;
        $auction->save();

        $this->sendAuctionNegotiatedPush($id);

        return redirect('auctions/ongoing?xYvrW='.time().rand())->with('success', 'Successfully negotiated Auction');
    }

    public function inspectorNegotiateCreate($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_inspector-negotiate')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        $object = Object::find($auction->object_id);
        $lastBid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        $bidPrice = $lastBid ? $lastBid->price : 0;
        return view('admin.modules.auction.inspector-negaotiate', compact('object', 'auction', 'bidPrice'));
    }

    public function inspectorNegotiateStore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
               'negotiate_price' => 'required'
          ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $auction = Auction::find($id);
        $object = Object::find($auction->object_id);

        $inspectorAuction = new InspectorNegaotiate;
        $inspectorAuction->auction_id = $id;
        $inspectorAuction->inspector_id = $object->inspector_id;
        $inspectorAuction->override_amount = $request->negotiate_price;
        $inspectorAuction->save();

        $this->sendAuctionInspectorNegotiatedPush($id);

        return redirect('auctions/closed')->with('success', 'Inspector successfully negotiated Auction');
    }


    public function stopAuction($id)
    {
        $auction = Auction::find($id);

        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_stop')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }


        if ($auction->status != $auction->getStatusType(1)) {
            return redirect()->back()->withError('Unable to disable current auction');
        }

        $lastBid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        $bidPrice = $lastBid ? $lastBid->price : 0;
// dd($lastBid );
        $currentTime = strtotime($this->UaeDate(Carbon::now()));

        if (empty($lastBid)) {
            $auction->status = $auction->getStatusType(12);
            $auction->save();
            $auction->end_time = date('Y-m-d H:i', $currentTime);
            $auction->firebaseDelete();

            return redirect('auctions/canceled')->with('success', 'Successfully cancelled auction');
        } else {

            //Sale type value
            $saleType = $this->getSalePrice($auction, $bidPrice, $auction->other_amount);
            if(!empty($saleType['status'])  && !empty($saleType['amount'])){
                $saleData = json_encode($saleType);
                $auction->deducted_amount = round($saleType['amount']);
                $auction->deducted_details = $saleData;
            }
            // dd($saleData);
            //Sale type value

            $traderId = $lastBid->trader_id;
            $auction->bid_owner = $traderId;
            $auction->buy_price = $bidPrice;
            $auction->status = $auction->getStatusType(3);
            $auction->end_time = date('Y-m-d H:i', $currentTime);
            $auction->save();
            $auction->firebaseDelete();

            $this->sendAuctionEndPush($id);
            $this->sendBidOwnerSms($id);
            $this->sendBulkSms($id);
        }

        // return redirect('auctions/sold')->with('success', 'Successfully moved to sold');
        return redirect('auctions/closed')->with('success', 'Successfully stopped auction');
    }

    public function cancelClosedAuction($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_cancel-closed')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }


        if ($auction->status != $auction->getStatusType(1)) {
            //return redirect()->back()->withError('Unable to disable current auction');
        }

        $currentTime = strtotime($this->UaeDate(Carbon::now()));

        $auction->status = $auction->getStatusType(10);
        $auction->end_time = date('Y-m-d H:i', $currentTime);
        $auction->save();

        return redirect('auctions/cancel-closed')->with('success', 'Successfully cancelled auction');
    }

    public function cancelAuction($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_cancel')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        if ($auction->status != $auction->getStatusType(1)) {
            return redirect()->back()->withError('Unable to disable current auction');
        }

        $currentTime = strtotime($this->UaeDate(Carbon::now()));

        $auction->status = $auction->getStatusType(12);
        $auction->end_time = date('Y-m-d H:i', $currentTime);
        $auction->save();
        $auction->firebaseDelete();

        $this->sendAuctionCancelPush($id);

        return redirect('auctions/canceled')->with('success', 'Successfully cancelled auction');
    }

    public function qualityAuction($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_qualitycheck')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        if ($auction->status != $auction->getStatusType(3)) {
            return redirect()->back()->withError('Unable to quality check current auction');
        }

        $auction->status = $auction->getStatusType(4);
        $auction->save();

        $this->sendStatusPush($id, 1);
        $this->sendSms($id, 1);
        return redirect('auctions/qualitycheck')->with('success', 'Successfully moved to quality check');
    }


    public function passCheck($id)
    {
        $auction = Auction::find($id);

        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_pass')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        if ($auction->status != $auction->getStatusType(4)) {
            return redirect()->back()->withError('Unable to pass auction');
        }

        $auction->status = $auction->getStatusType(6);
        $auction->save();

        $this->sendStatusPush($id, 2);
        $this->sendSms($id, 2);

        return redirect('auctions/passcheck')->with('success', 'Successfully passed quality check');
    }


    public function failCheck($id)
    {
        $auction = Auction::find($id);

        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_fail')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        if ($auction->status != $auction->getStatusType(4)) {
            return redirect()->back()->withError('Unable to fail auction');
        }

        $auction->status = $auction->getStatusType(5);
        $auction->save();

        $this->sendStatusPush($id, 3);
        $this->sendSms($id, 3);

        return redirect('auctions/failcheck')->with('success', 'Successfully moved to failed quality check');
    }

    public function readySale($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_ready-to-sale')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        if ($auction->status != $auction->getStatusType(8)) {
            return redirect()->back()->withError('Unable to sale auction');
        }

        $auction->status = $auction->getStatusType(7);
        $auction->save();

        $this->sendStatusPush($id, 4);
        $this->sendSms($id, 4);

        return redirect('auctions/sold')->with('success', 'Successfully moved to sold');
    }

    public function cashOut($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_cash')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        if ($auction->status != $auction->getStatusType(3)) {
            return redirect()->back()->withError('Unable to cash unsold auction');
        }

        $auction->status = $auction->getStatusType(8);
        $auction->save();

        $this->sendStatusPush($id, 5);
        $this->sendSms($id, 5);

        // //send mail to dealer
        // $DealerUser = DealerUser::where('id', $auction->dealer_id)->first();
        // $data = [];
        // $data['subject'] = 'Auction moved to cashed';
        // $data['email'] = $DealerUser->email;
        // $data['content'] ='Auction for ' . $auction->title . ' is cashed, Please contact the winner';
            

        //     try {
        //         $mail = Mail::send('emails.auction_completed', $data, function($message) use ($data) {
        //                     $message->to($data['email']);
        //                     $message->subject($data['subject']);
        //                 });
        //     } catch (\Swift_TransportException $e) {
        //         Log::error($e->getMessage());
        //     }


        return redirect('auctions/cash')->with('success', 'Successfully moved to cashed');
    }


    public function sendreminder($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_send-reminder')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        if ($auction->status != $auction->getStatusType(7)) {
            return redirect()->back()->withError('Unable to cash unsold auction');
        }


        $this->sendStatusPush($id, 6);
        $this->sendSms($id, 6);

        return redirect()->back()->with('success', 'Successfully sent reminder');
    }






    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $object = Object::find($request->id);
        $auction = new Auction();
        $types = $auction->getAuctionTypes();
        $salesTypes = \App\SalesType::where('status', 1)->get();

        //reopen
        $parentAuction ='';
        if (!empty($object->parentId)) {
            $parentAuction = Auction::where('object_id', $object->parentId)->first();
        }

        return view('admin.modules.auction.create', compact('object', 'types', 'parentAuction', 'salesTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'base_price' => 'required',
            'type' => 'required',
            'sale_type_id' => 'required',
            // 'start_time' => 'required',
            'min_increment' =>'required',
            'other_amount' => 'regex:/^\d*(\.\d{1,2})?$/'
        ],[
            "sale_type_id.required" => "The sale type field is required",
        ]);
        // $now = $this->UaeDate(Carbon::now());
        // dd($now);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $saleType = \App\SalesType::where('id', $request->sale_type_id)->first();
        $profitMargin = \App\ProfitMargin::where('sales_type_id', $request->sale_type_id)->count();
        if($profitMargin == 0) {
            return redirect()->back()->withError('There is no profit margin available for this sale type')->withInput();
        }
        $user = Auth::guard('admin')->user();

        /*if( !empty($request->min_increment) && ($request->min_increment >=  $request->base_price)){
             return redirect()->back()->withError('Start Price should be greater than Minimum Increment')->withInput();
        }*/

        if (!empty($request->buy_price) && ($request->base_price >=  $request->buy_price)) {
            return redirect()->back()->withError('Buy Now Price should be greater than Start Price')->withInput();
        }
        /*
        echo date('Y-m-d H:i',strtotime($request->start_time)); echo '<br>';

        echo $this->convertTimeToUTCzone(date('Y-m-d H:i',strtotime($request->start_time)), 'Asia/Dubai'); exit;

        echo date('Y-m-d H:i',strtotime($request->start_time));
        exit;

        */

        //
        if (empty($request->immediate)) {
            if (strtotime($request->start_time) < strtotime($this->UaeDate(Carbon::now()))) {
                return redirect()->back()->withError('Please enter start time properly')->withInput();
            }

            if (empty($request->formin)) {
                if ((strtotime($request->start_time)  >= strtotime($request->end_time))) {
                    return redirect()->back()->withError('Please enter start time and end time properly')->withInput();
                }
            }
        }
        if (empty($request->formin) || $request->formin == 0) {
            if (strtotime($request->end_time) < strtotime($this->UaeDate(Carbon::now()))) {
                return redirect()->back()->withError('Please enter end time properly')->withInput();
            }
        }



        if ($request->has('immediate')) {
            $now = $this->UaeDate(Carbon::now());

            $newTime = date('Y-m-d H:i:s', strtotime($now." +".$request->formin." minutes"));
        } else {
            $newTime = date('Y-m-d H:i:s', strtotime($request->start_time." +".$request->formin." minutes"));
        }

        $date = date('Y-m-d H:i:s', strtotime($request->start_time));
        $auction = new Auction();
        $data = $request->all();
        $data['dealer_id'] = Object::find($request->object_id) ? Object::find($request->object_id)->dealer_id  : '';
        $data['start_time'] = ($request->has('immediate')) ? $now : $date;
        if ($request->formin && $request->formin != 0) {
            $data['end_time'] = $newTime;
        } else {
            $data['end_time'] = date('Y-m-d H:i:s', strtotime($request->end_time));
        }
        $data['status'] = ($request->has('immediate')) ? $auction->getStatusType(1) : $auction->getStatusType(0);
        unset($data['immediate']);
        unset($data['formin']);
        // dd($data);
        $auctions = $auction->create($data);
        // dd($request->immediate);
        if (!empty($request->immediate)) {
         //   $this->sendAuctionStartPush($auctions->id);
        }
        if (Gate::allows('auction_ongoing')) {
            return redirect('auctions/ongoing?xYvrW='.time().rand())->with('success', 'Successfully added new Auction');
        } else {
            return redirect('objects/noauction')->with('success', 'Successfully added new Auction');
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Auction $auction)
    {
        return view('admin.modules.auction.show', compact('auction'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Auction $auction)
    {
        $id = $auction->id;
        $objectId = $auction->object_id;
        $auction = Auction::findOrFail($id);
        //if($auction->status != $auction->getStatusType(0)){
        //    return redirect('dealer/auctions')->with('error', 'Auction cannot be deleted');
        //}
        $auction->delete();
        return redirect('auctions/create?id=' . $objectId)->with('success', '');
        //$types = $auction->getAuctionTypes();
        //return view('admin.modules.auction.create',  compact('auction','types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Auction $auction)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'base_price' => 'required',
            'type' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'sale_type_id' => 'required',
            'other_charge' => 'regex:/^\d*(\.\d{1,2})?$/'
        ],[
            "sale_type_id.required" => "The sale type field is required",
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $request->all();
        $data['status'] = $auction->getStatusType(0);
        $data['start_time'] = date('Y-m-d H:i', strtotime($request->start_time));
        $data['end_time'] = date('Y-m-d H:i', strtotime($request->end_time));
        $auction->update($data);
        return redirect('auctions/ongoing?xYvrW='.time().rand())->with('success', 'Successfully edited Auction');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $auction = Auction::findOrFail($id);
        switch ($auction->status) {
          case $auction->getStatusType(0):
          case $auction->getStatusType(7):
            $auction->delete();
            return redirect()->back()->with('success', 'Auction Deleted Successfully');
            break;
          default:
            return redirect()->back()->with('error', 'Auction cannot be deleted');
            break;
        }
    }

    public function _matchRecord($data, $type)
    {
        $auction = new Auction();
        if ($type == 'ongoing') {
            //return Carbon::now()->between(Carbon::parse($data['start_time']), Carbon::parse($data['end_time'])) ? true : false;
            return ($data['status'] == $auction->getStatusType(1)) ? true : false;
        } elseif ($type == 'closed') {
            return ($data['status'] == $auction->getStatusType(3)) ? true : false;
        } elseif ($type == 'scheduled') {
            return ($data['status'] == $auction->getStatusType(0)) ? true : false;
        } elseif ($type == 'qualitycheck') {
            return ($data['status'] == $auction->getStatusType(4)) ? true : false;
        } elseif ($type == 'cancel-closed') {
            return ($data['status'] == $auction->getStatusType(10)) ? true : false;
        } elseif ($type == 'canceled') {
            return ($data['status'] == $auction->getStatusType(12)) ? true : false;
        } elseif ($type == 'passcheck') {
            return ($data['status'] == $auction->getStatusType(6)) ? true : false;
        } elseif ($type == 'failcheck') {
            return ($data['status'] == $auction->getStatusType(5)) ? true : false;
        } elseif ($type == 'sold') {
            return ($data['status'] == $auction->getStatusType(7)) ? true : false;
        } elseif ($type == 'cash') {
            return ($data['status'] == $auction->getStatusType(8)) ? true : false;
        } else {
            return false;
        }
    }

    public function auctionDetails(Request $request, $id)
    {
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_view')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        // dd($id);

        $auction = Auction::find($id);
        // dd($auction);
        $saleType =  !empty($auction->sale_type_id) ? \App\SalesType::find($auction->sale_type_id)->name : '';


        $bidHistory = Bid::where('auction_id', $auction->id)->with('trader')->orderBy('price', 'desc')->get();


        $automaticBids = AutomaticBid::where('auction_id', $auction->id)->orderBy('amount', 'desc')->get();

        $inspectorNegaotiate = InspectorNegaotiate::where('auction_id', $id)->orderBy('created_at', 'desc')->first();

        $overrideBid = Bid::where('status', 2)->where('auction_id', $id)->orderBy('created_at', 'desc')->first();
        $aBids = array();

        if (!empty($automaticBids)) {
            $i=0;

            foreach ($automaticBids as $automaticBid) {
                $aBids[$i]['trader_id'] = TraderUser::where('id', $automaticBid->trader_id)->withTrashed()->first()->first_name;
                $aBids[$i]['amount'] = (int) $automaticBid->amount;
                $aBids[$i]['updated_at'] =  $this->UaeDate($automaticBid->updated_at);
                $i++;
            }
        }

        $sale_type_details = \App\SalesType::where('id', $auction->sale_type_id)->first();

        $global_vat = \App\GlobalVat::where('slug', 'global-vat')->first();

        //var_dump($aBids); exit;

        //dd($aBids);
        /*$attributeSet = AttributeSet::orderBy('sort','asc')->get();
        if($request->has('type') && ($request->type == 'detail')){
            $bidAmount = $auction->bid->first()->bidding_price;
            return view('trader.detail',compact('auction','attributeSet','bidAmount'));
        }*/
        $automaticBidHistory = \App\AutomaticBid::where('auction_id', $auction->id)->with('trader')->orderBy('amount', 'desc')->get();
        $lastBid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        $bidPrice = $lastBid ? $lastBid->price : 0;

        $saleType = [];
        // dd($auction, $bidPrice, $auction->other_amount);
        $saleType = $this->getSalePrice($auction, $bidPrice, $auction->other_amount);
        $margin = 0;
        // $bidPrice = 105000;
        $vat = 0;
        $margin_amount = 0;

      //  echo $auction->status; exit;

        if ($auction->status >= $auction->getStatusType(3)) {
          $saleType = json_decode($auction['deducted_details'], true);
                if(!empty($saleType['sales_type_type'])){
                  $saleType['sales_type_name'] = \App\SalesType::where('id', $auction->sale_type_id)->first()->name;
                    if($saleType['sales_type_type'] == 1) {
                        if(empty($saleType['flatValue'])) {
                            $vat = $bidPrice * ($saleType['vat'] / 100);
                            $margin = $bidPrice * ($saleType['percentageValue'] / 100);
                            $margin_amount = $margin;
                        } else {
                            $vat = $bidPrice * ($saleType['vat'] / 100);
                            $margin = $saleType['flatValue'];
                            $margin_amount = $margin;
                        }
                    } else {
                        if(empty($saleType['flatValue'])) {
                            $margin = $bidPrice * ($saleType['percentageValue'] / 100);
                            $vat = $margin * ($saleType['vat'] / 100);
                            $margin_amount = $margin - $vat;
                        } else {
                            $margin = $saleType['flatValue'];
                            $vat = $margin * ($saleType['vat'] / 100);
                            $margin_amount = $margin - $vat;
                        }
                    }
              }

        }else{

          if($saleType['sales_type_type'] == 1) {
              if(empty($saleType['flatValue'])) {
                  $vat = $bidPrice * ($saleType['vat'] / 100);
                  $margin = $bidPrice * ($saleType['percentageValue'] / 100);
                  $margin_amount = $margin;
              } else {
                  $vat = $bidPrice * ($saleType['vat'] / 100);
                  $margin = $saleType['flatValue'];
                  $margin_amount = $margin;
              }
          } else {
              if(empty($saleType['flatValue'])) {
                  $margin = $bidPrice * ($saleType['percentageValue'] / 100);
                  $vat = $margin * ($saleType['vat'] / 100);
                  $margin_amount = $margin - $vat;
              } else {
                  $margin = $saleType['flatValue'];
                  $vat = $margin * ($saleType['vat'] / 100);
                  $margin_amount = $margin - $vat;
              }
          }
      }

        $other_amount = $auction->other_amount;
        return view('admin.modules.auction.auction-details', compact('auction', 'bidHistory', 'aBids' ,'inspectorNegaotiate', 'overrideBid','saleType', 'sale_type_details', 'global_vat', 'automaticBidHistory', 'other_amount', 'vat', 'margin_amount', 'saleType', 'bidPrice'));
    }

    public function auctionDetailsAjax(Request $request, $id)
    {
        $auction = Auction::find($id);
        $bidHistory = Bid::where('auction_id', $auction->id)->with('trader')->orderBy('price', 'desc')->get();
        $view = view('includes.ajax-auction-details', compact('auction', 'bidHistory', 'aBids'))->render();
        return json_encode(array('status'=>'success','view'=>$view));
    }
    //Automatic Bid
    public function auctionAutomaticAjax(Request $request, $id)
    {
        $auction = Auction::find($id);
        $automaticBidHistory = \App\AutomaticBid::where('auction_id', $auction->id)->with('trader')->orderBy('amount', 'desc')->get();
        return view('includes.ajax-automatic-bid-details', compact('auction', 'automaticBidHistory', 'aBids'))->render();
        // return json_encode(array('status'=>'success','view'=>$view));
    }


    public function override($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_override-bid-amount')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        $bid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        return view('admin.modules.auction.override_bid_amount', compact('auction', 'bid'));
    }

    public function overridePost(Request $request, $id)
    {
        if (Gate::denies('auction-button_override-bid-amount')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
             'override_bid_amount' => 'required'
         ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if ($request->override_bid_amount < $request->current_bid_price) {
            return redirect()->back()->with('error', 'Price should be greater than last bid Price')->withInput();
        }

        $bid = new Bid();
        $bid->trader_id = $request->trader_id;
        $bid->auction_id = $id;
        $bid->status = 2;
        $bid->price = $request->override_bid_amount;
        $bid->save();
        return redirect('auctions/closed')->with('success', 'Successfully override the bid amount');
    }


    public function ownerNegotiate($id)
    {
        $auction = Auction::find($id);
        $user = Auth::guard('admin')->user();
        if (Gate::denies('auction-button_negotiate-with-bid-owner')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        $bid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        return view('admin.modules.auction.negotiate_with_owner', compact('auction', 'bid'));
    }



    public function ownerNegotiatePost(Request $request, $id)
    {
        if (Gate::denies('auction-button_negotiate-with-bid-owner')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
             'override_bid_amount' => 'required'
         ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if ($request->override_bid_amount < $request->current_bid_price) {
            return redirect()->back()->with('error', 'Price should be greater than last bid Price')->withInput();
        }

        $now = $this->UaeDate(Carbon::now());
        $newTime = date('Y-m-d H:i:s', strtotime($now." +10 minutes"));

        $auction = Auction::find($id);

        $auction->status = $auction->getStatusType(1);
        $auction->final_req_amount = $request->override_bid_amount;
        $auction->ownerNegotiatedTime = Carbon::now();
        $auction->end_time = $newTime;
        $auction->save();

        $this->sendAuctionOwnerNegotiatedPush($id);

        return redirect('auctions/ongoing?xYvrW='.time().rand())->with('success', 'Successfully requested for settlement amount and auction restarted');
    }

    public function getSalesTypes($id) {
        $salesTypes = \App\SalesType::Join('profit_margins', 'profit_margins.sales_type_id', '=', 'sales_types.id')->where('sales_types.status', 1)
                                    ->select('sales_types.id', 'sales_types.name')->groupBy('sales_types.id')->get();
        $auction = Auction::find($id);
        return view('admin.modules.auction.edit-sales-type', compact('salesTypes', 'auction'));
    }

    public function updateSalesTypes(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'sales_type_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        // $profitMargin = \App\ProfitMargin::where('sales_type_id', $request->sales_type_id)->count();
        // if ($profitMargin == 0) {
        //     return response()->json(array('success' => true, 'msg' => 'The selected sales type no profit margin found'), 200);
        // }
        $auction = Auction::find($id);
        $auction->sale_type_id = $request->sales_type_id;
        $auction->save();

        $lastBid = Bid::where('auction_id', $id)->orderBy('price', 'desc')->first();
        $bidPrice = $lastBid ? $lastBid->price : 0;

        $saleType = [];
        $saleType = $this->getSalePrice($auction, $bidPrice, $auction->other_amount);

        $margin = 0;
        $vat = 0;
        $margin_amount = 0;

        if($saleType['sales_type_type'] == 1) {
            if(empty($saleType['flatValue'])) {
                $vat = $bidPrice * ($saleType['vat'] / 100);
                $margin = $bidPrice * ($saleType['percentageValue'] / 100);
                $margin_amount = $margin;
            } else {
                $vat = $bidPrice * ($saleType['vat'] / 100);
                $margin = $saleType['flatValue'];
                $margin_amount = $margin;
            }
        } else {
            if(empty($saleType['flatValue'])) {
                $margin = $bidPrice * ($saleType['percentageValue'] / 100);
                $vat = $margin * ($saleType['vat'] / 100);
                $margin_amount = $margin - $vat;
            } else {
                $margin = $saleType['flatValue'];
                $vat = $margin * ($saleType['vat'] / 100);
                $margin_amount = $margin - $vat;
            }
        }

        $other_amount = $auction->other_amount;

        $registered_in = \App\ObjectAttributeValue::where('object_id', $auction->object_id)->where('attribute_id', 20)->first();
        $bankLoan = \App\ObjectAttributeValue::where('object_id', $auction->object_id)->where('attribute_id', 18)->first();
        return view('admin.modules.auction.update-sales-type', compact('auction', 'saleType', 'other_amount', 'margin_amount', 'vat', 'bidPrice', 'lastBid', 'registered_in', 'bankLoan'));
    }
}
