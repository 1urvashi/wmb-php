<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Validator;
use Datatables;
use DB;
use File;
use App\DealerUser;
use App\TraderUser;
use App\CreditHistory;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mail;
use GuzzleHttp;
use App\Version;
use App\Customer;
use Excel;
use Auth;
use Redirect;
use Gate;

class CustomerController extends Controller
{
     public function __construct(){
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('customersMenu')){
        //      return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Customer $trader)
    {
        if(Gate::denies('customers_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $customers = Customer::all();
        return view('admin.modules.customer.index',  compact('customers'));
    }
    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request) {
        DB::statement(DB::raw('set @rownum=0'));
        $traders = Customer::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id', 'customer_name', 'email', 'mobile','customer_reference_number','created_at'])->orderBy('id', 'desc')->get();
        return Datatables::of($traders)
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Gate::denies('customers_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        return view('admin.modules.trader.create',compact('dealers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Gate::denies('customers_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
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
	//$iosUrl = $ios->;
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
        $data['api_token'] = str_random(60);
        $data['password'] = bcrypt($request->password);
        $trader->create($data);
        return redirect('traders')->with('success', 'Successfully added new Trader');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TraderUser $trader)
    {
        if(Gate::denies('customers_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.trader.show',  compact('trader'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TraderUser $trader)
    {
        if(Gate::denies('customers_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        return view('admin.modules.trader.create',  compact('trader','dealers'));
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
        if(Gate::denies('customers_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
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
            unset($data['images']);
        }
        if($request->has('password')){
            $data['account'] = 'Trader';
	    $ios = Version::where('type', '=', 'ios')->first();
	    $android = Version::where('type', '=', 'android')->first();
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
        if($request->isMethod('patch') && $request->has('credit_limit')){
            $creditHistory = new CreditHistory();
            $creditHistory->trader_id = $trader->id;
            $creditHistory->credit_limit = $request->credit_limit;
            $creditHistory->save();
        }
        $trader->update($data);
        return $request->isMethod('patch') ? redirect()->back()->with('success', 'Successfully updated Trader') : redirect('traders')->with('success', 'Successfully updated Trader');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Gate::denies('customers_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $trader = TraderUser::findOrFail($id);
        $trader->delete();
        return redirect('traders')->with('success', 'Trader Deleted Successfully');
    }
    public function creditHistory($id){
        if(Gate::denies('customers_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $credits = CreditHistory::where('trader_id',$id)->orderBy('created_at','desc')->get();
        return view('admin.modules.trader.credits',  compact('credits'));
    }

    public function export($dealerId){
        if(Gate::denies('customers_export')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealerId = (int)$dealerId;
        $fileName = 'traders';
        $dataExported = ['first_name','last_name','email'];
        $traders = TraderUser::where('id','!=',0)->get($dataExported);
        if($dealerId && $dealerId > 0){
            $fileName = $fileName.'-'.DealerUser::where('id',$dealerId)->first()->name;
            $traders = TraderUser::where('dealer_id',$dealerId)->get($dataExported);
        }
        //dd($traders);
        $tradersArray = [];
        $tradersArray[] = $dataExported;
        foreach ($traders as $trader) {
            $tradersArray[] = $trader->toArray();
        }
        Excel::create($fileName, function($excel) use ($tradersArray) {
            $excel->setTitle('Traders');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
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
