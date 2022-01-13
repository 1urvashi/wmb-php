<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Datatables;
use DB;
use File;
use Auth;
use Illuminate\Support\Facades\Log;

use App\TraderUser;
use App\User;
use App\Auction;
use Redirect;
use Gate;

class MergeDrmTraderController extends Controller
{
     public function __construct(){
       $user = Auth::guard('admin')->user();
     //   if(Gate::denies('merge_trader')){
     //        return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
     //   }
   }

   public function index(TraderUser $trader)
   {
     if(Gate::denies('merge_trader')){
          return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
     }
       //$drms = new User();
       //$drmsUsers = User::where('role', $drms->getDRM())->get();
     
       $gType = config('globalConstants.TYPE');
       $drmsUsers = User::whereIn('type', [$gType['DRM'],$gType['HEAD_DRM']])->get();
       return view('admin.modules.merge_trader_drm.index',  compact('drmsUsers','gType'));
   }


   public function data(Request $request) {

        $drm = new User();
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $traders = TraderUser::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','first_name', 'email', 'last_name','dealer_id', 'status', 'dmr_id', 'last_bid', 'deposit_amount'])
                                            ->where('dmr_id', null)->orderBy('last_bid', 'desc');
       return Datatables::of($traders)
          ->editColumn('last_bid', function($traders) {
               $date = new Auction();
               $now = $this->UaeDate($traders->last_bid);
               return $traders->last_bid ? date('d-m-Y h:i:s A', strtotime($now)) : null;
          })
          ->addColumn('action', function ($traders) use($user) {
               $b = '';

               $b .= '<input type="checkbox" class="merge_check trader_check" name="merge[]" value="'.$traders->id.'"/>';
               return $b;
          })
          ->make(true);
   }

   public function post(Request $request) {
          // return $request->all();
          if(Gate::denies('merge_trader')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
          }
          $trader_users = $request->merge;
          $drm = $request->drm;
          if($drm && $trader_users) {
               foreach ($trader_users as $key => $value) {
                    $trader = TraderUser::where('id', $value)->first();
                    $trader->dmr_id = $drm;
                    $trader->save();
               }
               $data = "Successfully update the trader user";
               return ['status' => true, 'msg' => $data];
          } else {
               $data = "Oops something went wrong message";
               return ['status' => false, 'msg' => $data];
          }
   }

}
