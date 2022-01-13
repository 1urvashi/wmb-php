<?php

namespace App\Http\Controllers;

use Auth;
use App\Auction;

use App\Http\Requests;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function traderIndex()
    {
         $user = Auth::guard('trader')->user();
         $sessionId = session()->get('sessionId');
         if( (!empty($sessionId)) && ($user->session_id != $sessionId) ) {
            Auth::guard('trader')->logout();
            return redirect(session()->get('language').'/login')->with('error', trans('api.session_expire'));
         }
        return view('trader.dashboard');
    }
    public function dealerIndex()
    {
		$data['live']=  Auction::where('status',1)->where('dealer_id',Auth::guard('dealer')->user()->id)->count();

		$data['sold']=  Auction::where('status',7)->where('dealer_id',Auth::guard('dealer')->user()->id)->count();
		$data['cashed']=  Auction::where('status',8)->where('dealer_id',Auth::guard('dealer')->user()->id)->count();



		$data['carsLive']=  Auction::where('status',1)->where('dealer_id',Auth::guard('dealer')->user()->id)->where('type',5000)->count();
		$data['carsInventory']=  Auction::where('status',1)->where('dealer_id',Auth::guard('dealer')->user()->id)->where('type',5001)->count();
		$data['carsDeals']=  Auction::where('status',1)->where('dealer_id',Auth::guard('dealer')->user()->id)->where('type',5002)->count();

        return view('dealer.dashboard', compact('data'));
    }
    public function inspectorIndex()
    {
        return view('inspector.dashboard');
    }
    public function adminIndex()
    {

		$data['live']=  Auction::where('status',1)->count();

		$data['sold']=  Auction::where('status',7)->count();
		$data['cashed']=  Auction::where('status',8)->count();

		$data['liveBranches'] =  Auction::distinct('dealer_id')->where('status',1)->count('dealer_id');


		$data['carsLive']=  Auction::where('status',1)->where('type',5000)->count();
		$data['carsInventory']=  Auction::where('status',1)->where('type',5001)->count();
		$data['carsDeals']=  Auction::where('status',1)->where('type',5002)->count();

    $auctionModel = new Auction();
    $startDate = $auctionModel->convertTimeToUTCzone(date('Y-m-d 00:00:00', time()));
    $endDate = $auctionModel->convertTimeToUTCzone(date('Y-m-d 23:59:59', time()));


    $data['closedToday'] =  Auction::where('end_time', '>=', $startDate)
                                    ->where('end_time', '<=', $endDate)
                                    ->where('status', 3)->count();


        return view('admin.dashboard', compact('data'));
    }

}
