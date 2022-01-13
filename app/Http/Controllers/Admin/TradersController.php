<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use Datatables;
use DB;
use File;
use Storage;
use App\DealerUser;
use App\TraderUser;
use App\CreditHistory;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mail;
use GuzzleHttp;
use App\Version;
use App\User;
use App\Country;
use App\Emirate;
use App\Auction;
use Carbon\Carbon;
use Excel;
use Image;
use Auth;
use Redirect;
use Gate;

class TradersController extends Controller {

    public function __construct() {
        $user = Auth::guard('admin')->user();
        // if (Gate::denies('tradersMenu')) {
        //     return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TraderUser $trader) {

        if (Gate::denies('traders_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        //$drms = new User();
        //$drmsUsers = User::where('role', $drms->getDRM())->where('status', 1)->get();

        $gType = config('globalConstants.TYPE');
        //$drmsUsers = User::whereIn('type', [$gType['DRM'], $gType['HEAD_DRM']])->get();

        //$onboarders = User::where('type', config('globalConstants.TYPE.ONBOARDER'))->where('status', 1)->get();

        return view('admin.modules.trader.index', compact('dealers', 'gType'));
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request) {
        $drm = new User();
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $traders = TraderUser::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'trader_users.id', 'trader_users.onboarder_id', 'trader_users.first_name', 'trader_users.email', 'trader_users.last_name', 'trader_users.dealer_id', 'trader_users.status', 'trader_users.dmr_id', 'trader_users.last_bid', 'trader_users.deposit_amount','trader_users.is_verify_email']);
        /*if ($user->role == $drm->getDRM()) {
            $traders = $traders->where('dmr_id', $user->id)->orderBy('last_bid', 'desc')->get();
        } elseif ($user->type == config('globalConstants.TYPE.ONBOARDER')) {
            $traders = $traders->where('onboarder_id', $user->id)->orderBy('last_bid', 'desc')->get();
        } else {*/
            // $traders = $traders->orderBy('last_bid', 'desc')->get();
            $traders = $traders->orderBy('id', 'desc')->get();
        //}
        return Datatables::of($traders)
                        ->editColumn('last_bid', function ($traders) {
                            $date = new Auction();
                            $now = $this->UaeDate($traders->last_bid);
                            return $traders->last_bid ? date('d-m-Y h:i:s A', strtotime($now)) : null;
                            //return date('D/M/Y h:i:s A', strtotime($traders->last_bid));
                        })
                        ->addColumn('cashed', function ($traders) use ($user) {
                            $count_cashed = Auction::where('bid_owner', $traders->id)->where('status', 8)->count();

                            return $count_cashed ? $count_cashed : 0;
                        })
                        ->addColumn('cashed_date', function ($traders) use ($user) {
                            $cashed_date = Auction::where('bid_owner', $traders->id)->where('status', 8)->orderBy('updated_at', 'desc')->first();

                            return $cashed_date ? date('Y-m-d h:i:s A', strtotime($this->UaeDate($cashed_date->updated_at))) : '';
                            // return $cashed_date ? $cashed_date->updated_at : '';
                        })
                        ->addColumn('action', function ($traders) use ($user) {
                            $b = '';
                            if (Gate::allows('traders_read')) {
                                $b = '<a href="traders/' . $traders->id . '" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View</a> &nbsp;';
                            }
                            if (Gate::allows('traders_update')) {
                                $b .= '<a href="traders/' . $traders->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> &nbsp;';
                            }
                            if (Gate::allows('history_read')) {
                                $b .= '<a href="history?trader=' . $traders->id . '" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View History</a>';
                            }
                            if (Gate::allows('traders_delete')) {
                                $b .= '<a href="traders/destroy/' . $traders->id . '" onclick="return confirm(\'Are you sure you want to delete this Trader?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                            }
                            return $b;
                            // return '<a href="traders/' . $traders->id.'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View</a>
                            // <a href="traders/' . $traders->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>
                            //     <a href="history?trader=' . $traders->id . '" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View History</a>	';
                            /*
                              <a href="traders/destroy/' . $traders->id . '" onclick="return confirm(\'Are you sure you want to delete this Trader?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>'; */
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('dealer') && ($request->get('dealer') != 0)) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return ($row['dealer_id'] == $request->get('dealer')) ? true : false;
                                });
                            }
                            if ($request->has('drms') && ($request->get('drms') != 0)) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return ($row['dmr_id'] == $request->get('drms')) ? true : false;
                                });
                            }
                            if ($request->has('onboarder') && ($request->get('onboarder') != 0)) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return ($row['onboarder_id'] == $request->get('onboarder')) ? true : false;
                                });
                            }
                            if ($request->has('search') && ($request->get('search') != '')) {
                                $needle = strtolower($request->get('search'));
                                $instance->collection = $instance->collection->filter(function ($row) use ($request, $needle) {
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

    public function viewDeleted(){

      if (Gate::denies('traders_View-Deleted')) {
          return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
      }

      return view('admin.modules.trader.deleted');
    }

  


    public function deletedData(Request $request) {
        $drm = new User();
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $traders = TraderUser::onlyTrashed()
                  ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'onboarder_id', 'first_name', 'email', 'last_name', 'dealer_id',
                  'status', 'dmr_id', 'last_bid', 'deposit_amount','deleted_at'])
                  ->get();

        return Datatables::of($traders)
                        ->editColumn('last_bid', function ($traders) {
                            $date = new Auction();
                            $now = $this->UaeDate($traders->last_bid);
                            return $traders->last_bid ? date('d-m-Y h:i:s A', strtotime($now)) : null;
                            //return date('D/M/Y h:i:s A', strtotime($traders->last_bid));
                        })
                        ->editColumn('deleted_at', function ($traders) {
                            $date = new Auction();
                            $now = $this->UaeDate($traders->deleted_at);
                            return $traders->deleted_at ? date('d-m-Y h:i:s A', strtotime($now)) : null;
                            //return date('D/M/Y h:i:s A', strtotime($traders->last_bid));
                        })
                        ->editColumn('dmr_id', function ($traders) {
                            return  '';
                            //return $traders->last_bid ? date('d-m-Y h:i:s A', strtotime($now)) : null;
                            //return date('D/M/Y h:i:s A', strtotime($traders->last_bid));
                        })
                        ->editColumn('dealer_id', function ($traders) {
                            return  '';
                            //return $traders->last_bid ? date('d-m-Y h:i:s A', strtotime($now)) : null;
                            //return date('D/M/Y h:i:s A', strtotime($traders->last_bid));
                        })
                        ->addColumn('action', function ($traders) use ($user) {
                            $b = '<a href="traders-restore/' . $traders->id . '" onclick="return confirm(\'Are you sure you want to restore this Trader?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Restore</a>';

                            return $b;
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('search') && ($request->get('search') != '')) {
                                $needle = strtolower($request->get('search'));
                                $instance->collection = $instance->collection->filter(function ($row) use ($request, $needle) {
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

    public function restoreTrader($id){
        if (Gate::denies('traders_View-Deleted')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $trader = TraderUser::onlyTrashed()
                ->where('id', $id)->restore();
        return redirect('traders')->with('success', 'Trader restored Successfully');

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        if (Gate::denies('traders_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $user = Auth::guard('admin')->user();
        $dealers = DealerUser::where('branch_id', 0)->get();
        $emirates = Emirate::all();
        // $drms = User::where('role', $user->getDRM())->get();
        //$drms = User::where('role', $user->getDRM())->where('status', 1)->get();
        $gType = config('globalConstants.TYPE');
        $drms = User::whereIn('type', [$gType['DRM'], $gType['HEAD_DRM']])->where('status', 1)->get();
        $onboarders = User::where('type', config('globalConstants.TYPE.ONBOARDER'))->where('status', 1)->get();
        $countries = Country::all();

        $markets = \App\Market::all();
        $carConditions = \App\CarCondition::all();
        $carMakes = \App\CarMake::all();
        $specifications = \App\Specification::all();

        return view('admin.modules.trader.create', compact('dealers', 'drms', 'gType', 'countries', 'emirates', 'onboarders','markets','carConditions','carMakes','specifications'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function ImageUpload($image,$type,$removeDocId,$traderId ,$sortOrder){
        if (!empty($image)) {

           

            switch ($type) {
                case 'emirates_id_front':
                    $path = 'traders/' . $type . '/';
                    break;
                case 'emirates_id_back':
                    $path = 'traders/' . $type . '/';
                    break;

                case 'passport_front':
                    $path = 'traders/' . $type . '/';
                    break;

                case 'passport_back':
                    $path = 'traders/' . $type . '/';
                    break;
                case 'other_doc':
                    $path = 'traders/' . $type . '/';
                    break;
                default:
               
                    break;
            }

          
            $dir = config('app.fileDirectory') . $path;
            $uploaded = false;
            $data = array();

            if (File::mimeType($image) != 'application/pdf') {
                $img = Image::make($image);
                // dd($img);
                $timestamp = Date('y-m-d-H-i-s');
                $str = str_random(5);
                $name = $timestamp . $type . '-' . $str . $image->getClientOriginalName();
                $uploaded = Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');

            } else {
                $img = $image;
                $timestamp = Date('y-m-d-H-i-s');
                $str = str_random(5);
                $name = $timestamp . $type . '-' . $str . $image->getClientOriginalName();
                // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                $uploaded = Storage::disk('s3')->put($dir . $name, file_get_contents($img->getRealPath()), 'public');

            }

            if ($uploaded) {

                if(!empty($removeDocId)){
                    $removeDoc = \App\TraderImages::where('id', $removeDocId)->first();

                    if (!empty($removeDoc)) {
                        Storage::disk('s3')->delete($dir . $removeDoc->image);
                        $removeDoc->delete();
                    }
                }

                $traderImage = new \App\TraderImages();
                $traderImage->image = $name;
                $traderImage->imageType = $type;
                if(!empty($traderId)){
                    $traderImage->traderId = $traderId;
                }

                if(!empty($sortOrder)){
                    $traderImage->sort = $sortOrder;
                }

                $traderImage->save();

                $data['type'] = $type;
                $data['docId'] = $traderImage->id;
                $data['docUrl'] = env('S3_URL').'uploads/'.$path.$name;

               // return $this->successResponse(trans('api.document_uploaded_success'), $data);

            }
        }

    } 
    public function remove_trader_images($type,$id)
    {
        if($type == 'emirates_id_front' || $type == 'emirates_id_back' ||$type == 'passport_front' ||$type == 'passport_back' ||$type == 'other_doc' ){
            \App\TraderImages::where('id',$id)->delete();
            $message = "Trader image deleted Successfully";
        }
        // else{
        //     //attachment
        //     ObjectAttachment::where('id',$id)->delete();
        //     $message = "Vehicle attachment deleted Successfully";
        // }
        return redirect()->back()->with('success', $message);

    }

    public function store(Request $request) {
        if (Gate::denies('traders_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email|max:255|unique:trader_users',
                    'password' => 'required|min:6',
                    'phone' => 'required',
                    'estimated_amount' => 'required',
                    'trader_images.emirates_id_front' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'trader_images.emirates_id_back' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'trader_images.passport_front' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'trader_images.passport_back' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096'
                    //'dmr_id' => 'required',
                    //'onboarder_id' => 'required',
                    /*'country_id' => 'required',
                    'emirate_id' => 'required',
                    'post_code' => 'required',
                    'images.image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
                    'images.passport' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
                    'images.trade_license' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
                    'images.kyc' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
                    'images.payment_receipt' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
                    'images.document' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
                    'company_name' => 'required',
                    'trade_license_no' => 'required',
                    'tax_registration_no' => 'required',
                    'emirates_id' => 'required',
                    'expiry' => 'required|date|date_format:Y-m-d',
                    'tax_registration_no' => 'required',

                    'business_size' => 'required',
                    'kyc_credit_limit' => 'required',
                    'age_of_car' => 'required',
                    //'mileage' => 'required',
                    'target_market' => 'required',
                    'car_condition' => 'required',
                    'specifications' => 'required',
                    'make_cars' => 'required'
                        ], [
                    //"onboarder_id.required" => "Onboarder field is required",
                    //"dmr_id.required" => "DRM field is required",
                      'make_cars.required' => 'Make of watch field is required.',
                      'car_condition.required' => 'Watch condition field is required.',*/

        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $businessSizeArray = explode('-',$request->business_size);

        $target_market =$request->target_market;
        $car_condition =$request->car_condition;
        $specifications =$request->specifications;
        $make_cars =$request->make_cars;
        $other_value = $request->other_value;

        $drm = new User();
        $user = Auth::guard('admin')->user();
        // $data = $request->all();
        // $data = [
        //   'first_name'=> $request->first_name,
        //   'last_name'=> $request->last_name,
        //   'email'=> $request->email,
        //   'phone'=> $request->phone,
        //   'account'=> 'Trader',
        //   'estimated_amount'=> $request->estimated_amount,
        //   'estimated_amount'=> $request->estimated_amount,
        //   'estimated_amount'=> $request->estimated_amount,
        //   'estimated_amount'=> $request->estimated_amount,
        //   'estimated_amount'=> $request->estimated_amount,
        // ];

        // $data = json_encode([]);
        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['estimated_amount'] = $request->estimated_amount;
        $data['is_verify_email'] = 1;
        $data['api_token'] = str_random(60);
        $data['password'] = bcrypt($request->password);
        $data['user_password'] = $request->password;
        $data['onboarder_id'] = $request->onboarder_id;


        $images = $request->file('images');
        $path = 'traders/images/';
        $dir = config('app.fileDirectory') . $path;
        foreach ($images as $key => $image) {
            if ($image) {
                if (File::mimeType($image) != 'application/pdf') {
                    $img = Image::make($image);
                    // dd($img);
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $name = $timestamp . $key . '-' . $str . $image->getClientOriginalName();

                    Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    $data[$key] = $name;

                    /* $timestamp = Date('y-m-d-H-i-s');
                      $str = str_random(5);
                      $name = $timestamp . $key.'-'.$str. $image->getClientOriginalName();
                      $data[$key] = $name;
                      $image->move(public_path() . '/uploads/traders/images/', $name); */
                } else {
                    $img = $image;
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $name = $timestamp . $key . '-' . $str . $image->getClientOriginalName();
                    // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    Storage::disk('s3')->put($dir . $name, file_get_contents($img->getRealPath()), 'public');
                    $data[$key] = $name;
                }
            }
        }
        unset($data['images']);
        $trader = new TraderUser();
        $data['account'] = 'Trader';
        $ios = Version::where('type', '=', 'ios')->first();
        $android = Version::where('type', '=', 'android')->first();
        //dd($ios->url);
        $data['iosUrl'] = $ios->url;
        $data['androidUrl'] = $android->url;
        //$iosUrl = $ios->;
        try {
            $mail = Mail::send('emails.registration_traders', $data, function ($message) use ($data) {
                        $message->to($data['email']);
                        $message->subject($data['account'] . ' Account Created');
                    });
            // $mail = Mail::send('emails.verifyUser', $data, function ($message) use ($data) {
            //     $message->to($data['email']);
            //     $message->subject(' Account Created');
            // });
        } catch (\Swift_TransportException $e) {
            Log::error($e->getMessage());
        }
        unset($data['iosUrl']);
        unset($data['androidUrl']);
        unset($data['account']);



 

       // $data['kycBusinessLowSize'] = $businessSizeArray[0];
       // $data['kycBusinessUpSize'] = $businessSizeArray[1];


        $data['kycCreditLimit'] = $request->kyc_credit_limit;
        //$data['kycMileage'] = $request->mileage;
        $data['kycCarAge'] = $request->age_of_car;
        $data['emiratesIdExpiry'] = $request->expiry;

        unset($data['business_size']);
        unset($data['kyc_credit_limit']);
        unset($data['age_of_car']);
        unset($data['target_market']);
        //unset($data['mileage']);
        unset($data['car_condition']);
        unset($data['specifications']);
        unset($data['make_cars']);
        unset($data['other_value']);
        unset($data['expiry']);
        unset($data['user_password']);
        // unset($data['trader_images']);

        

        $data['status'] = $user->role == $drm->getDRM() ? 0 : 1;

        // return $data;

        $tId =$trader->create($data);
        
        if ($request->hasFile('trader_images') || count($request->trader_images['other_doc']) > 0) {
            $traderImages = $request->trader_images;

            foreach ($traderImages as $key => $image) {
                $type = $key;
            
                $removeDocId = !empty($request->removeDocId) ? $request->removeDocId : '';
                $traderId = $tId->id;
                $sortOrder = !empty($request->sort) ? $request->sort : '';

                if($key == 'other_doc'){
                    foreach ($image as $k => $otherdoc) {
                        $this->ImageUpload($otherdoc,$type,$removeDocId,$traderId ,$sortOrder);
                    }
                }else{
                        $this->ImageUpload($image,$type,$removeDocId,$traderId ,$sortOrder);
                }
           
            }
        }


        //$request->target_market
        if(!empty($target_market)){
             foreach($target_market as $target){
                 if(!empty($target)){
                    $market = new \App\TraderMarket();
                    //$market = \App\TraderMarket::firstOrNew(['marketId' => $target, 'traderId' =>  $tId->id]);
                    $market->marketId = $target;
                    $market->traderId = $tId->id;
                    $market->save();
                 }
             }
        }

        if(!empty($car_condition)){
             foreach($car_condition as $carCondition){
                 if(!empty($carCondition)){
                    $condition = new \App\TraderCarCondition();
                    //$condition = \App\TraderCarCondition::firstOrNew(['carConditionId' => $carCondition, 'traderId' =>  $tId->id]);
                    $condition->carConditionId = $carCondition;
                    $condition->traderId = $tId->id;
                    $condition->save();
                 }
             }
        }

        if(!empty($specifications)){
             foreach($specifications as $specification){
                 if(!empty($specification)){
                    $tSpecification = new \App\TraderSpecification();
                    //$tSpecification = \App\TraderSpecification::firstOrNew(['specificationId' => $specification, 'traderId' =>  $tId->id]);
                    $tSpecification->specificationId = $specification;
                    $tSpecification->traderId = $tId->id;
                    $tSpecification->save();
                 }
             }
        }

        if(!empty($make_cars)){
             foreach($make_cars as $makeCar){
                 if(!empty($makeCar)){
                    $tMakeCar = new \App\TraderCarMake();
                    //$tMakeCar =  \App\TraderCarMake::firstOrNew(['carMakeId' => $makeCar, 'traderId' =>  $tId->id]);
                    $tMakeCar->carMakeId = $makeCar;
                    if(!empty($other_value)){
                        $tMakeCar->otherTitle = $other_value;
                    }
                    $tMakeCar->traderId = $tId->id;
                    $tMakeCar->save();
                 }
             }
        }

        return redirect('traders')->with('success', 'Successfully added new Trader');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TraderUser $trader) {
        if (Gate::denies('traders_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        // dd($trader);
        return view('admin.modules.trader.show', compact('trader'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TraderUser $trader) {
        // dd($trader->traderImages);
        if (Gate::denies('traders_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $user = Auth::guard('admin')->user();
        $dealers = DealerUser::where('branch_id', 0)->get();
        // $drms = User::where('role', $user->getDRM())->get();
        //$drms = User::where('role', $user->getDRM())->where('status', 1)->get();
        $gType = config('globalConstants.TYPE');
        $drms = User::whereIn('type', [$gType['DRM'], $gType['HEAD_DRM']])->where('status', 1)->get();
        $countries = Country::all();
        $emirates = Emirate::all();
        $onboarders = User::where('type', config('globalConstants.TYPE.ONBOARDER'))->where('status', 1)->get();
        $markets = \App\Market::all();
        $carConditions = \App\CarCondition::all();
        $carMakes = \App\CarMake::all();
        $specifications = \App\Specification::all();

        $marketsArray = \App\TraderMarket::where('traderId',$trader->id)->pluck('marketId')->toArray();

        // $traderimageArray = \App\TraderImages::where('traderId',$trader->id)->get()->toArray();

        $carConditionIdArray = \App\TraderCarCondition::where('traderId',$trader->id)->pluck('carConditionId')->toArray();
        $specificationIdArray = \App\TraderSpecification::where('traderId',$trader->id)->pluck('specificationId')->toArray();
        $carMakeIdArray = \App\TraderCarMake::where('traderId',$trader->id)->pluck('carMakeId')->toArray();
        $otherValue = \App\TraderCarMake::join("car_makes as ck", "ck.id", "=", "trader_car_makes.carMakeId")
                                          ->where('trader_car_makes.traderId',$trader->id)->where('ck.otherStatus',1)->select('otherTitle')->first();

                                        //   dd($trader->traderImages);

        return view('admin.modules.trader.create', compact('trader', 'dealers', 'drms','gType', 'countries', 'emirates',
         'onboarders','markets','carConditions','carMakes','specifications','marketsArray','carConditionIdArray','specificationIdArray','carMakeIdArray','otherValue'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TraderUser $trader) {
        // dd($request->all());
        if (Gate::denies('traders_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255|unique:trader_users,email,'.$trader->id,
            // 'password' => 'required|min:6',
            'phone' => 'required',
            'estimated_amount' => 'required',
            // 'trader_images.emirates_id_front' => 'required|array|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            // 'trader_images.emirates_id_back' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            // 'trader_images.passport_front' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            // 'trader_images.passport_back' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096'
            //'dmr_id' => 'required',
            //'onboarder_id' => 'required',
            /*'country_id' => 'required',
            'emirate_id' => 'required',
            'post_code' => 'required',
            'images.image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'images.passport' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'images.trade_license' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'images.kyc' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
            'images.payment_receipt' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
            'images.document' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'company_name' => 'required',
            'trade_license_no' => 'required',
            'tax_registration_no' => 'required',
            'emirates_id' => 'required',
            'expiry' => 'required|date|date_format:Y-m-d',
            'tax_registration_no' => 'required',

            'business_size' => 'required',
            'kyc_credit_limit' => 'required',
            'age_of_car' => 'required',
            //'mileage' => 'required',
            'target_market' => 'required',
            'car_condition' => 'required',
            'specifications' => 'required',
            'make_cars' => 'required'
                ], [
            //"onboarder_id.required" => "Onboarder field is required",
            //"dmr_id.required" => "DRM field is required",
              'make_cars.required' => 'Make of watch field is required.',
              'car_condition.required' => 'Watch condition field is required.',*/

    ]);
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
        // $data = $request->all();

        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['estimated_amount'] = $request->estimated_amount;
        $data['is_verify_email'] = 1;
        $data['api_token'] = str_random(60);
        // $data['password'] = bcrypt($request->password);
        $data['onboarder_id'] = $request->onboarder_id;

        if (!$request->isMethod('patch')) {
            $validator = Validator::make($request->all(), [
                        'first_name' => 'required',
                        'last_name' => 'required',
                        'email' => 'required|email|max:255',
                        'phone' => 'required',
                        'estimated_amount' => 'required',
                        //'dmr_id' => 'required',
                        //'onboarder_id' => 'required',
                        /*'country_id' => 'required',
                        'emirate_id' => 'required',
                        'post_code' => 'required',
                        'images.image' => 'image|mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
                        'images.passport' => 'mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
                        'images.trade_license' => 'mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
                        'images.kyc' => 'mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
                        'images.payment_receipt' => 'mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',
                        'images.document' => 'mimes:jpeg,png,jpg,gif,svg,pdf|max:4096',

                        'company_name' => 'required',
                        'trade_license_no' => 'required',
                        'tax_registration_no' => 'required',
                        
                        'expiry' => 'required|date|date_format:Y-m-d',
                        'tax_registration_no' => 'required',

                        'business_size' => 'required',
                        'kyc_credit_limit' => 'required',
                        'age_of_car' => 'required',
                        //'mileage' => 'required',
                        'target_market' => 'required',
                        'car_condition' => 'required',
                        'specifications' => 'required',
                        'make_cars' => 'required'
                            ], [
                        //"onboarder_id.required" => "Onboarder field is required",
                        //"dmr_id.required" => "DRM field is required",
                        'make_cars.required' => 'Make of watch field is required.',
                        'car_condition.required' => 'Watch condition field is required.',*/
            ]);


            $exist = TraderUser::where('email', $request->email)->where('id', '!=', $trader->id)->first();

            if ($exist) {
                return redirect()->back()->with('error', 'This Trader alredy exist!!');
            }
            // dd($request->all());
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $businessSizeArray = explode('-',$request->business_size);

            $target_market =$request->target_market;
            $car_condition =$request->car_condition;
            $specifications =$request->specifications;
            $make_cars =$request->make_cars;
            $other_value = $request->other_value;

            $images = $request->file('images');
            $path = 'traders/images/';
            $dir = config('app.fileDirectory') . $path;
// dd($dir);
            /*  $dir = config('app.fileDirectory') . $path;
              $img = Image::make($myImage);
              $imageName =  rand(1, time()) . '.' . $myImage->getClientOriginalExtension();
              Storage::disk('s3')->put($dir . $imageName, $img->stream()->detach(), 'public');

              exit; */
            foreach ($images as $key => $image) {
                if ($image) {
                    if (File::mimeType($image) != 'application/pdf') {
                        if (!empty($trader->getOriginal($key))) {
                            Storage::disk('s3')->delete($dir . $trader->getOriginal($key));
                        }

                        $img = Image::make($image);
                        $timestamp = Date('y-m-d-H-i-s');
                        $str = str_random(5);
                        $name = $timestamp . $key . '-' . $str . $image->getClientOriginalName();



                        Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                        $data[$key] = $name;

                        /* File::delete('uploads/traders/'.$key.'/'. $trader[$key]);
                          $timestamp = Date('y-m-d-H-i-s');
                          $str = str_random(5);
                          $name = $timestamp . $key.'-'.$str. $image->getClientOriginalName();
                          $data[$key] = $name;
                          $image->move(public_path() . '/uploads/traders/images/', $name); */
                    } else {
                        if (!empty($trader->getOriginal($key))) {
                            Storage::disk('s3')->delete($dir . $trader->getOriginal($key));
                        }
                        $img = $image;
                        $timestamp = Date('y-m-d-H-i-s');
                        $str = str_random(5);
                        $name = $timestamp . $key . '-' . $str . $image->getClientOriginalName();
                        // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                        Storage::disk('s3')->put($dir . $name, file_get_contents($img->getRealPath()), 'public');
                        $data[$key] = $name;
                    }
                }
            }
            unset($data['images']);
        }
        if ($request->has('password')) {
            $data['account'] = 'Trader';
            $ios = Version::where('type', '=', 'ios')->first();
            $android = Version::where('type', '=', 'android')->first();
            $data['iosUrl'] = $ios->url;
            $data['androidUrl'] = $android->url;
            $data['user_password'] = $request->password;
            try {
                $mail = Mail::send('emails.registration_edit_trader', $data, function ($message) use ($data) {
                            $message->to($data['email']);
                            $message->subject($data['account'] . ' Account Updated');
                        });
            } catch (\Swift_TransportException $e) {
                Log::error($e->getMessage());
            }
            unset($data['iosUrl']);
            unset($data['androidUrl']);
            unset($data['account']);
            unset($data['user_password']);
           
        } else {
            unset($data['user_password']);
        }
        $data['password'] = bcrypt($request->password);
        //$data['onboarder_id'] = $request->onboarder_id;
        /*if ($request->isMethod('patch') && $request->has('credit_limit')) {
            $creditHistory = new CreditHistory();
            $creditHistory->trader_id = $trader->id;
            $creditHistory->credit_limit = $request->credit_limit;
            $creditHistory->save();
        }*/

        if (!$request->isMethod('patch')) {

         // $data['kycBusinessLowSize'] = $businessSizeArray[0];
         // $data['kycBusinessUpSize'] = $businessSizeArray[1];


          $data['kycCreditLimit'] = $request->kyc_credit_limit;
          //$data['kycMileage'] = $request->mileage;
          $data['kycCarAge'] = $request->age_of_car;
          $data['emiratesIdExpiry'] = $request->expiry;

          unset($data['business_size']);
          unset($data['kyc_credit_limit']);
          unset($data['age_of_car']);
          unset($data['target_market']);
          //unset($data['mileage']);
          unset($data['car_condition']);
          unset($data['specifications']);
          unset($data['make_cars']);
          unset($data['other_value']);
          unset($data['expiry']);
      }

      if (empty($request->credit_limit)) {
          unset($data['credit_limit']);
      }
      if (empty($request->deposit_amount)) {
          unset($data['deposit_amount']);
      }

        $trader->update($data);

        // dd($request->trader_images);
        if ($request->hasFile('trader_images') || count($request->trader_images['other_doc']) > 0) {
            $traderImages = $request->trader_images;
            
            foreach ($traderImages as $key => $image) {
                $type = $key;
            
                $removeDocId = !empty($request->removeDocId) ? $request->removeDocId : '';
                $traderId = $trader->id;
                $sortOrder = !empty($request->sort) ? $request->sort : '';

                if($key == 'other_doc'){
                    foreach ($image as $k => $otherdoc) {
                        $this->ImageUpload($otherdoc,$type,$removeDocId,$traderId ,$sortOrder);
                    }
                }else{
                        $this->ImageUpload($image,$type,$removeDocId,$traderId ,$sortOrder);
                }
           
            }
        }


        //$request->target_market
        if(!empty($target_market)){
              \App\TraderMarket::where('traderId',$trader->id)->delete();
             foreach($target_market as $target){
                 if(!empty($target)){
                    $market = new \App\TraderMarket();
                    $market->marketId = $target;
                    $market->traderId = $trader->id;
                    $market->save();
                 }
             }
        }

        if(!empty($car_condition)){
             \App\TraderCarCondition::where('traderId',$trader->id)->delete();
             foreach($car_condition as $carCondition){
                 if(!empty($carCondition)){
                    $condition = new \App\TraderCarCondition();
                    $condition->carConditionId = $carCondition;
                    $condition->traderId = $trader->id;
                    $condition->save();
                 }
             }
        }

        if(!empty($specifications)){
              \App\TraderSpecification::where('traderId',$trader->id)->delete();
             foreach($specifications as $specification){
                 if(!empty($specification)){
                    $tSpecification = new \App\TraderSpecification();
                    $tSpecification->specificationId = $specification;
                    $tSpecification->traderId = $trader->id;
                    $tSpecification->save();
                 }
             }
        }

        if(!empty($make_cars)){
              \App\TraderCarMake::where('traderId',$trader->id)->delete();
             foreach($make_cars as $makeCar){
                 if(!empty($makeCar)){
                    $tMakeCar = new \App\TraderCarMake();
                    $tMakeCar->carMakeId = $makeCar;
                    if(!empty($other_value)){
                        $tMakeCar->otherTitle = $other_value;
                    }
                    $tMakeCar->traderId = $trader->id;
                    $tMakeCar->save();
                 }
             }
        }

        return $request->isMethod('patch') ? redirect()->back()->with('success', 'Successfully updated Trader') : redirect('traders')->with('success', 'Successfully updated Trader');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Gate::denies('traders_delete')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $trader = TraderUser::findOrFail($id);
        $trader->session_id = '';
        $trader->save();

        $trader->delete();
        return redirect('traders')->with('success', 'Trader Deleted Successfully');
    }


    public function creditHistory($id) {
        if (Gate::denies('traders_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $credits = CreditHistory::where('trader_id', $id)->orderBy('created_at', 'desc')->get();
        return view('admin.modules.trader.credits', compact('credits'));
    }

    public function export($dealerId) {

        if (Gate::denies('traders_export')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealerId = (int) $dealerId;
        $fileName = 'traders_' . time();
        $header = ['Id', 'First Name', 'Last Name', 'Email', 'Deposit Amount', 'Last Bid', 'Cashed Count', 'Cashed Date'];
        $dataExported = ['trader_users.id', 'trader_users.first_name', 'trader_users.last_name', 'trader_users.email', 'trader_users.deposit_amount', 'trader_users.last_bid', 'users.name'];
        $traders = TraderUser::leftJoin('users', 'users.id', '=', 'trader_users.dmr_id')->where('trader_users.dealer_id', '!=', 0)->select($dataExported)->get();
        if ($dealerId && $dealerId > 0) {
            $fileName = $fileName . '-' . DealerUser::where('id', $dealerId)->first()->name;
            // $dataExported = ['trader_users.id','trader_users.first_name','trader_users.last_name','trader_users.email', 'users.name', 'auctions.bid_owner'];
            $dataExported = ['trader_users.id', 'trader_users.first_name', 'trader_users.last_name', 'trader_users.email', 'trader_users.deposit_amount', 'trader_users.last_bid', 'users.name'];
            $traders = TraderUser::leftJoin('users', 'users.id', '=', 'trader_users.dmr_id')->where('trader_users.dealer_id', '=', $dealerId)->select($dataExported)->get();
            //->leftJoin('auctions', 'auctions.object_id', '=', 'objects.id')
        }
        // dd($traders);
        $tradersArray = [];
        $tradersArray[] = $header;
        foreach ($traders as $trader) {
            $count_cashed = Auction::where('bid_owner', $trader->id)->where('status', 8)->count();
            $cashed_date = Auction::where('bid_owner', $trader->id)->where('status', 8)->orderBy('updated_at', 'desc')->first();
            $date = $cashed_date ? date('Y-m-d H:i:s', strtotime($this->UaeDate($cashed_date->updated_at))) : '';
            // dd($date);
            // $cashed = $count_cashed != 0 ? $count_cashed : 0;
            $tradersArray[] = ['id' => $trader->id, 'first_name' => $trader->first_name, 'last_name' => $trader->last_name, 'email' => $trader->email, 'deposit_amount' => $trader->deposit_amount, 'trader_users' => $trader->last_bid, /*'name' => $trader->name,*/ 'cashed' => $count_cashed, 'bid_owner' => $date];
        }
        // dd($tradersArray);
        Excel::create($fileName, function ($excel) use ($tradersArray) {
            $excel->setTitle('Traders');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Traders file');
            $excel->sheet('sheet1', function ($sheet) use ($tradersArray) {
                $sheet->fromArray($tradersArray, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function publish(Request $request, $id) {

        $attribute = TraderUser::find($request->dataId);
        if($request->data_action_type == "status"){
            $attribute->status = $request->dataValue;
        }else{
            $attribute->is_verify_email = $request->dataValue;
        }

        $attribute->session_id = '';
        $attribute->save();
        return;
    }

}
