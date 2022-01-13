<?php

namespace App\Http\Controllers\Trader;

use App\TraderUser;
use Auth;
use Validator;
use App\Http\Requests;
use App\Auction;
use App\Attribute;
use App\AttributeSet;
use App\ObjectAttributeValue;
use App\Notifications;
use App\AutomaticBid;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Make;
use App\Models;
use App\Object;

class IndexController extends Controller
{
    public function auctionDetails(Request $request,$lang,$id) {
        $data=array();
         $user = Auth::guard('trader')->user();
         $sessionId = session()->get('sessionId');
         if( (!empty($sessionId)) && ($user->session_id != $sessionId) ) {
            Auth::guard('trader')->logout();
            return redirect(session()->get('language').'/login')->with('error', trans('api.session_expire'));
         }
         $auction = Auction::find($id);
        //   dd($auction);

		$object =   Object::where('id', $auction->object_id)->first();

        foreach ($object->ObjectAttributeValue as  $value) {
            //  dd($value->attribute->attributeSet);
              $data[$value['attribute']['attributeSet']['slug']][]=$value;
          }

		$make = Make::where('id', $object->make_id)->first()->name;
		$model = Models::where('id', $object->model_id)->first()->name;
// dd($object);
          $user = Auth::guard('trader')->user();
          if($user->session_id != session()->get('sessionId')) {
               return redirect(session()->get('language').'/logout')->with('error', trans('api.session_expire'));
          }
        $attributeSet = AttributeSet::orderBy('sort','asc')->get();
        // dd($data);
        if($request->has('type') && ($request->type == 'detail')){
            $bidAmount = $auction->bid->first()->bidding_price;
            return view('trader.detail',compact('auction','attributeSet','bidAmount', 'make', 'model','object','data'));
        }
        $automaticBid = AutomaticBid::where('trader_id',Auth::guard('trader')->user()->id)->where('auction_id',$id)->first();



        return view('trader.auction-detail',compact('auction','attributeSet','automaticBid', 'make', 'model','object','data'));
    }

    public function getProfile($lang) {
         $user = Auth::guard('trader')->user();

        //  dd($user->traderImages);
          $sessionId = session()->get('sessionId');
          if( (!empty($sessionId)) && ($user->session_id != $sessionId) ) {
               Auth::guard('trader')->logout();
               return redirect(session()->get('language').'/login')->with('error', trans('api.session_expire'));
          }
        return view('trader.profile',compact('user'));
    }

    public function getHistory($lang) {
         $user = Auth::guard('trader')->user();
         if($user->session_id != session()->get('sessionId')) {
              return redirect(session()->get('language').'/logout')->with('error', trans('api.session_expire'));
         }
        $auctions = Auction::where('bid_owner', Auth::guard('trader')->user()->id)
							//->whereIn('id', array(1, 2, 3))
							->where('status', '!=', 12)
							->where('status', '>', 2)->with('bid')->orderBy('id','desc')->paginate(12);
        return view('trader.history',compact('auctions'));
    }

    public function getNotification($lang) {
        $auction = new Auction();
        Carbon::setLocale($lang);
        $notifications = Notifications::where('trader_id', Auth::guard('trader')->user()->id)->orderBy('id', 'desc')->paginate(10);
        return view('trader.notification',compact('notifications','auction'));
    }

    public function getNotificationPreference($lang) {

        $notificationPage=1;
        return view('trader.notification-preference', compact("notificationPage"));
    }

    public function setNotificationPreference(Request $request, $lang) {

    }
}
