<?php

namespace App\Http\Controllers\Dealer;

use Illuminate\Http\Request;

use Validator;
use Datatables;
use DB;
use File;
use App\DealerUser;
use App\TraderUser;
use App\CreditHistory;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mail;
use GuzzleHttp;
use App\Version;
use Excel;

class TradersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TraderUser $trader)
    {
        return view('dealer.modules.trader.index');
    }
    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data() {
        DB::statement(DB::raw('set @rownum=0'));
        $traders = TraderUser::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','first_name', 'email', 'last_name','dealer_id', 'status'])->where('dealer_id',Auth::guard('dealer')->user()->id)->orderBy('last_bid', 'desc')->get();
        return Datatables::of($traders)
            ->addColumn('action', function ($traders) {
                return '<a href="traders/' . $traders->id.'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View</a>
               <a href="traders/' . $traders->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>
                    <a href="history?trader=' . $traders->id . '" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View History</a>
			   ';  /*
               <a href="traders/destroy/' . $traders->id . '" onclick="return confirm(\'Are you sure you want to delete this Trader?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';   */
            })
           ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dealer.modules.trader.create');
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255|unique:trader_users',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $request->all();
        $images = $request->file('images');
        foreach ($images as $key=>$image)
        {
            if($image){
               $timestamp = Date('y-m-d-H-i-s');
               $str = str_random(5);
               $name = $timestamp . $key.'-'.$str. $image->getClientOriginalName();
               $data[$key] = $name;
               $image->move(public_path() . '/uploads/traders/images/', $name);
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
        try {
            $mail =Mail::send('emails.registration_traders', $data, function($message) use ($data) {
                $message->to($data['email']);
                $message->subject($data['account'].' Account Created');
            });
         }  catch (\Swift_TransportException $e){
            Log::error($e->getMessage());
         }
	unset($data['iosUrl']);
	unset($data['androidUrl']);
        unset($data['account']);
        $data['password'] = bcrypt($request->password);
        $data['api_token'] = str_random(60);
        $data['dealer_id'] =  Auth::guard('dealer')->user()->id;
        $trader->create($data);
        return redirect('dealer/traders')->with('success', 'Successfully added new Trader');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TraderUser $trader)
    {
        if($trader->dealer_id != Auth::guard('dealer')->user()->id){
            return redirect('dealer')->with('error', 'Not authorized to this page');
        }
        return view('dealer.modules.trader.show',  compact('trader'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TraderUser $trader)
    {
        if($trader->dealer_id != Auth::guard('dealer')->user()->id){
            return redirect()->back()->with('error', 'Not authorized to this page');
        }
        $dealers = DealerUser::all();
        return view('dealer.modules.trader.create',  compact('trader','dealers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TraderUser $trader)
    {
        $data = $request->all();
        if(!$request->isMethod('patch')){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|max:255',
            ]);
            if ($validator->fails()) {
                return redirect('traders')->withErrors($validator)->withInput();
            }
            $images = $request->file('images');
            foreach ($images as $key=>$image)
            {
                if($image){
                   File::delete('uploads/traders/'.$key.'/'. $trader[$key]);
                   $timestamp = Date('y-m-d-H-i-s');
                   $str = str_random(5);
                   $name = $timestamp . $key.'-'.$str. $image->getClientOriginalName();
                   $data[$key] = $name;
                   $image->move(public_path() . '/uploads/traders/images/', $name);
                }
            }
        if($request->has('password')){
            $data['account'] = 'Trader';
	    $ios = Version::where('type', '=', 'ios')->first();
	    $android = Version::where('type', '=', 'android')->first();
	    //dd($ios->url);
	    $data['iosUrl'] = $ios->url;
	    $data['androidUrl'] = $android->url;
            try {
                $mail =Mail::send('emails.registration_edit_trader', $data, function($message) use ($data) {
                    $message->to($data['email']);
                    //$message->bcc($this->bcc);
                    $message->subject($data['account'].' Account Updated');
                });
             }  catch (\Swift_TransportException $e){
                Log::error($e->getMessage());
             }
	    unset($data['iosUrl']);
	    unset($data['androidUrl']);
            unset($data['account']);
            $data['password'] = bcrypt($request->password);
        }else{
            unset($data['password']);
        }
            unset($data['images']);
        }
        if($request->isMethod('patch') && $request->has('credit_limit')){
            $creditHistory = new CreditHistory();
            $creditHistory->trader_id = $trader->id;
            $creditHistory->credit_limit = $request->credit_limit;
            $creditHistory->save();
        }
        $trader->update($data);
        return $request->isMethod('patch') ? redirect()->back()->with('success', 'Successfully updated Trader') : redirect('dealer/traders')->with('success', 'Successfully updated Trader');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trader = TraderUser::findOrFail($id);
        $trader->delete();
        return redirect('dealer/traders')->with('success', 'Trader Deleted Successfully');
    }

    public function creditHistory($id){
        $credits = CreditHistory::where('trader_id',$id)->orderBy('created_at','desc')->get();
        return view('dealer.modules.trader.credits',  compact('credits'));
    }

    public function export(){
      $fileName = 'traders';
      $dataExported = ['first_name','last_name','email'];
      $traders = TraderUser::where('dealer_id',Auth::guard('dealer')->user()->id)->get($dataExported);
      $tradersArray = [];
      $tradersArray[] = $dataExported;
      foreach ($traders as $trader) {
          $tradersArray[] = $trader->toArray();
      }
      Excel::create($fileName, function($excel) use ($tradersArray) {
          $excel->setTitle('Traders');
          $excel->setCreator('Dealer')->setCompany('Wecashanycar');
          $excel->setDescription('Traders file');
          $excel->sheet('sheet1', function($sheet) use ($tradersArray) {
              $sheet->fromArray($tradersArray, null, 'A1', false, false);
          });

      })->download('csv');
    }

    public function publish(Request $request, $id) {
         $attribute = TraderUser::find($request->dataId);
         $attribute->status = $request->dataValue;
         $attribute->session_id = '';
         $attribute->save();
         return;
    }
}
