<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DealerUser;
use App\TraderUser;
use DB;
use App\Auction;
use Carbon\Carbon;
use Datatables;
use Auth;
use Redirect;
use Excel;
use App\Bid;
use Gate;

class HistoryController extends Controller
{
     public function __construct(){
        $user = Auth::guard('admin')->user();
    }

    public function History() {
        if(Gate::denies('history_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        $traders = TraderUser::all();
        return view('admin.modules.history.index', compact('dealers','traders'));
    }

    public function data(Request $request) {
        DB::statement(DB::raw('set @rownum=0'));
        $auction = Auction::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','title', 'start_time','dealer_id','end_time','base_price','min_increment','type','bid_owner','object_id','status'])->where('status','>=',3)->orderBy('id', 'desc')->get();
        // return $auction;
        return Datatables::of($auction)
                ->addColumn('bid_owners', function ($auction) {
                    if (Gate::allows('traders_read')) {
                        return $auction->tradersBid ? '<a href="traders/' . $auction->tradersBid->id .'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> '. $auction->tradersBid->first_name .'</a>' : '';
                    }

                })
                ->addColumn('objects_id', function ($auction) {
                    if (Gate::allows('vehicles_read')) {
                        return '<a href="object/detail/'. $auction->vehiclesHistory->id .'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> '. $auction->vehiclesHistory->name .'</a>';
                    }

                })
                ->addColumn('auction_detail', function ($auction) {
                    if (Gate::allows('vehicles_read')) {
                        return '<a href="auctions/view/'. $auction->id .'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View details</a>';
                    }

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
                    if ($request->has('dealer') && ($request->get('dealer')!=0)) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return ($row['dealer_id'] == $request->get('dealer')) ? true : false;
                            });
                    }
                    if ($request->has('trader') && ($request->get('trader')!=0)) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                if($row['bid_owner'] > 0){
                                    return ($row['bid_owner'] == $request->get('trader')) ? true : false;
                                }
                            });
                    }
                    if ($request->has('status')) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                if($row->getOriginal('status') && $row->getOriginal('status') >= 3){
                                    return ($row->getOriginal('status') == $request->get('status')) ? true : false;
                                }
                            });
                    }

                    if ($request->has('start_time') && ($request->get('start_time')!=0)) {
                        $date = Carbon::parse($request->get('start_time'))->format('Y-m-d');
                            $instance->collection = $instance->collection->filter(function ($row) use ($request,$date) {

                                return (date('Y-m-d', strtotime($row['start_time'])) >= date('Y-m-d', strtotime($request->get('start_time')))) ? true : false;

                                // $rowDate = Carbon::parse($this->UaeDate($row['start_time']))->format('Y-m-d');
                                // return ($rowDate >= $date) ? true : false;
                            });
                    }
                    if ($request->has('end_time') && ($request->get('end_time')!=0)) {
                        $date = Carbon::parse($request->get('end_time'))->format('Y-m-d');
                            $instance->collection = $instance->collection->filter(function ($row) use ($request,$date) {
                                // $rowDate = Carbon::parse($this->UaeDate($row['end_time']))->format('Y-m-d');
                                // return ($rowDate <= $date) ? true : false;

                                return (date('Y-m-d', strtotime($row['end_time'])) <= date('Y-m-d', strtotime($request->get('end_time')))) ? true : false;
                            });
                    }
                    // if (($request->get('start_time')!=0) && ($request->get('end_time')!=0)) {
                    //     $start_date  = Carbon::parse($request->get('start_time'))->format('Y-m-d H:i:s');
                    //     $end_date  = Carbon::parse($request->get('end_time'))->format('Y-m-d H:i:s');

                    //     $instance->collection = $instance->collection->filter(function ($row) use ($request,$start_date,$end_date) {

                    //         $rowSDate = Carbon::parse($this->UaeDate($row['start_time']))->format('Y-m-d H:i:s');
                    //         $rowEDate = Carbon::parse($this->UaeDate($row['end_time']))->format('Y-m-d H:i:s');

                    //         if($rowSDate >= $start_date && $rowEDate <= $end_date) {
                    //             return true;
                    //         }else{
                    //             return false;
                    //         }
                    //     });
                    // }
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
    public function traderList(Request $request){
        if($request->has('dealer')){
            $traders = TraderUser::where('dealer_id',$request->dealer)->select('id','first_name','last_name')->get();
            if($traders->count()){
                return json_encode(array('status'=>'success','data'=>$traders));
            }
            return json_encode(array('status'=>'error','message'=>'No traders under this dealer'));
        }
    }


    public function export(Request $request){
        if(Gate::denies('history_export')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
       }
         ini_set('memory_limit','-1');
        ini_set('max_execution_time', 60000); //60000 seconds = 5 minutes
        ini_set('max_input_time ', 60000); //60000 seconds = 5 minutes
         // dd($request->all());
     $fileName = 'auctions_history_'.time();
     Excel::create($fileName, function ($excel) use($request) {
          $excel->sheet('auctions', function ($sheet) use($request) {
               $dataExported = ['auctions.id','auctions.title', 'auctions.start_time','objects.name as vehicle_name','auctions.end_time','auctions.base_price','auctions.min_increment','auctions.type','auctions.bid_owner','auctions.status', 'objects.id as vehicle_id', 'objects.make_id as make_id', 'objects.model_id as model_id'];

               $datas = Auction::Join('objects', 'objects.id', '=', 'auctions.object_id')
                                      ->where('auctions.status','>=',3)->orderBy('auctions.id', 'desc');
               if($request->has('dealer') && $request->get('dealer') != 0) {
                    $datas = $datas->where('auctions.dealer_id', $request->get('dealer'));
               }

               if($request->has('trader') && $request->get('trader') != 0) {
                    $datas = $datas->where('auctions.bid_owner', $request->get('trader'));
               }

               if($request->has('status') && $request->get('status') != '') {
                    $datas = $datas->where('auctions.status', $request->get('status'));
               }
               if($request->has('start_time')) {
                    $datas = $datas->where('auctions.start_time', '>=', date('Y-m-d H:i:s', strtotime($request->get('start_time'))));
               }
               if($request->has('end_time')) {
                    $datas = $datas->where('auctions.end_time', '<=', date('Y-m-d H:i:s', strtotime($request->get('end_time'))));
               }

               $headers = ['Auction ID','Auction Name','Auction Start Time', 'Watch Name','Auction End Time','Base Price','Min Increment',
                    'Auction Type', 'Bid Amount', 'Bid Owner','Brand', 'Model','Status'];

               $sheet->fromArray(array($headers), null, 'A1', false, false);

               if (!empty($datas->get($dataExported))) {
                    foreach ($datas->get($dataExported) as $value) {
                         switch ($value->type) {
                              case '5000':
                                   $type = 'Live';
                                   break;
                              case '5001':
                                   $type = 'Inventory';
                                   break;
                              case '5002':
                                   $type = 'Deals';
                                   break;
                              default:
                                   $type =  $value->type;
                                   break;
                         }
                         if($value->bid_owner != 0) {
                              $trader = TraderUser::where('id', $value->bid_owner)->first();
                         }

                         $name = isset($trader) ? $trader->first_name : '';

                         $start_time = $this->UaeDate($value->start_time);
                         $end_time = $this->UaeDate($value->end_time);

                         $bid = Bid::where('auction_id', $value->id)->orderBy('created_at', 'desc')->first();

                         $bidPice = $bid ? $bid->price : null;

                         $make = \App\Make::where('id', $value->make_id)->first()->name;
                         $model = \App\Models::where('id', $value->model_id)->first()->name;

                         //$objectAttributeValue = \App\ObjectAttributeValue::Join('attributes','attributes.id','=','object_attribute_values.attribute_id')
                                                                                                   // ->select('object_attribute_values.attribute_value', 'attributes.name')->where('object_id', $value->vehicle_id)->get();

                       //  $year = $objectAttributeValue->where('attributes.name', 'Year')->first() ? $objectAttributeValue->where('attributes.name', 'Year')->first()->attribute_value : '';
                       //  $km = $objectAttributeValue->where('attributes.name', 'KM')->first() ? $objectAttributeValue->where('attributes.name', 'KM')->first()->attribute_value : '';

                        // $exteriourColor = $objectAttributeValue->where('attributes.name', 'Exterior Colour')->first() ? $objectAttributeValue->where('attributes.name', 'Exterior Colour')->first()->attribute_value : '';
                        // $interiourColor = $objectAttributeValue->where('attributes.name', 'Interior Colour')->first() ? $objectAttributeValue->where('attributes.name', 'Interior Colour')->first()->attribute_value : '';
                         // dd($km);
                        // $regional_specs = $objectAttributeValue->where('attributes.name', 'Regional specs')->first() ? $objectAttributeValue->where('attributes.name', 'Regional specs')->first()->attribute_value : '';
                       //  $trim_level = $objectAttributeValue->where('attributes.name', 'TRIM level')->first() ? $objectAttributeValue->where('attributes.name', 'TRIM level')->first()->attribute_value : '';
                        // $acident_history = $objectAttributeValue->where('attributes.name', 'Accident History')->first() ? $objectAttributeValue->where('attributes.name', 'Accident History')->first()->attribute_value : '';
                       //  $service_history = $objectAttributeValue->where('attributes.name', 'Service History')->first() ? $objectAttributeValue->where('attributes.name', 'Service History')->first()->attribute_value : '';


                         // $trim_level = $objectAttributeValue->where('attributes.name', 'TRIM level')->first() ? $objectAttributeValue->where('attributes.name', 'TRIM level')->first()->attribute_value : '';

                         //$headers = ['Auction ID','Auction Name','Auction Start Time', 'Vehicle Name','Auction End Time','Base Price','Min Increment',
                         //     'Auction Type', 'Bid Amount', 'Bid Owner','Make', 'Model', 'KM', 'Exterior Colour', 'Interior Colour', 'Regional specs', 'TRIM level', 'Year',  'Status'];
                         $data = [$value->id, $value->title, $start_time, $value->vehicle_name, $end_time,
                                         $value->base_price, $value->min_increment, $type, $bidPice, $name, $make, $model,
//                             $km, $exteriourColor, $interiourColor, $regional_specs,$trim_level, $year, $value->inspector_name, $acident_history, $service_history,
                             $value->status];

                         $result = $sheet->fromArray(array($data), null, 'A1', false, false);
                    }

               }
          });
     })->download('csv');
    }
}
