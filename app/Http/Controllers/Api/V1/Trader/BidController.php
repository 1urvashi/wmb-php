<?php

namespace App\Http\Controllers\Api\V1\Trader;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\TraderUser;
use App\Bid;
use Validator;
use App\Auction;
use App\AutomaticBid;
use App\ObjectImage;
use App\CreditHistory;
use Carbon\Carbon;

use Hash;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Auth;
use Illuminate\Support\Facades\Log;

class BidController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['api']);
    }


	public function getObjectDetail(Request $request){

		/*$file = $request->file('images');

		var_dump($file); exit;*/


		$validator = Validator::make($request->all(),array('objectId'=>'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => trans('api.error_required_fields')]);
        }

		$traderId = '';
		if(isset($request->api_token)){
		$user =  TraderUser::where('api_token',$request->api_token)->first();

		if(!empty($user)){
          if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
             return $this->sessionExpireErrorResponse(trans('api.session_expire'));
          }

			  $traderId = $user->id;
		  }
		}

		//echo $request->objectId; exit;
		$objectId = $request->objectId;
		$data = $this->getobjectData($objectId, $traderId);

		if(!empty($data)){
			return $this->successResponse(trans('api.object_details'), $data);
		}else{
			return $this->errorResponse(trans('api.not_found'));
		}

	}

	public function setObjectImages(Request $request){

		//$file = $request->file('images');

		 $images = $request->images;
            if(empty($images[0]) ){
                     return false;
             }


            foreach ($images as $key=>$image) {

				/*var_dump($image);
				echo $image->getClientOriginalName();
				 exit;*/

				$addImage = new ObjectImage();
				$relPath = 'uploads/object';
				$path = public_path() . '/' . $relPath;
				$filename = time() . uniqid() .'_vehicle'. '.' . $image->getClientOriginalExtension();
				$uFile = $image->move($path, $filename);

				$addImage->object_id = 3;
				$addImage->image = $filename;
				$addImage->save();



            }
			echo 44; exit;
		/*
		if ($request->file('images')->isValid()) {
			if ($request->hasFile('images')) {



               // $image = $request->file('image');

				$file = $request->file('image');

				$relPath = 'uploads/app/warranty';
				$path = public_path() . '/' . $relPath;
				$filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
				$uFile = $file->move($path, $filename);

				$data['image'] = url($relPath . '/' . $filename.'?ver='.time());

				return $this->successResponse(trans('api.image_success'), $data);
			}
		}*/

		var_dump($file); exit;

	}

  public function settleNow(Request $request){
    $validator = Validator::make($request->all(),array('auctionId' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000, "Status" => $validator->errors()->all()]);
        }

    $user =  TraderUser::where('api_token',$request->api_token)->first();
          if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
             return $this->sessionExpireErrorResponse(trans('api.session_expire'));
          }
    $traderId = $user->id;

    $auction = Auction::where('id', $request->auctionId)->first();

    $buyPrice = $auction->final_req_amount;

    $lastBid = Bid::where('auction_id', $request->auctionId)->max('price');
    $currentTime = strtotime($this->UaeDate(Carbon::now()));

    if($buyPrice >= $lastBid){
                        /*$creditLimit = CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() : 0;
                        if($auction->buy_price > $creditLimit->credit_limit){
                            return $this->errorResponse(trans('api.limit_exceed'));
                        }*/
      if((!$auction->end_time < Carbon::now()) && ($auction->status == $auction->getStatusType(1))){
          $bid = new Bid();
          $bid->price = $buyPrice;
          $bid->status = 1;
          $bid->auction_id = $request->auctionId;
          $bid->trader_id = $traderId;
          $bid->save();
      } else {
          return $this->errorResponse(trans('api.expired'));
      }

      $auction->status = $auction->getStatusType(3);
      $auction->end_time = date('Y-m-d H:i',$currentTime);
      $auction->bid_owner = $traderId;
      $auction->save();

      $auction->firebaseDelete();

      $this->sendAuctionOwnerNegotiatedEndPush($request->auctionId);

      return $this->successResponse(trans('api.bid_buy_success'));
    }

    return $this->errorResponse(trans('api.bid_buy_invalid'));
  }


	public function buyNow(Request $request){
		$validator = Validator::make($request->all(),array('auctionId' => 'required'));
    if ($validator->fails()) {
        return response()->json(["StatusCode" => 20000, "Status" => $validator->errors()->all()]);
    }

    $user =  TraderUser::where('api_token',$request->api_token)->first();

    if(empty($user->status)){
        return $this->sessionExpireErrorResponse(trans('api.session_expire'));
    }


          if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
             return $this->sessionExpireErrorResponse(trans('api.session_expire'));
          }
		$traderId = $user->id;

		$TraderEmiratesIdFront = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','emirates_id_front')->first();
		$TraderEmiratesIdBack = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','emirates_id_back')->first();
		$TraderPassportFront = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','passport_front')->first();
		$TraderPassportBack = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','passport_back')->first();
		if(empty($TraderEmiratesIdFront)){
			return $this->errorResponse(trans('api.emirates_id_front'));
		}
		if(empty($TraderEmiratesIdBack)){
			return $this->errorResponse(trans('api.emirates_id_back'));
		}
		if(empty($TraderPassportFront)){
			return $this->errorResponse(trans('api.passport_front'));
		}
		if(empty($TraderPassportBack)){
			return $this->errorResponse(trans('api.passport_back'));
		}
		

		$auction = Auction::where('id', $request->auctionId)->first();

		$buyPrice = $auction->buy_price;

		$lastBid = Bid::where('auction_id', $request->auctionId)->max('price');
		$currentTime = strtotime($this->UaeDate(Carbon::now()));

		if($buyPrice >= $lastBid){
                        /*$creditLimit = CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() : 0;
                        if($auction->buy_price > $creditLimit->credit_limit){
                            return $this->errorResponse(trans('api.limit_exceed'));
                        }*/
			if((!$auction->end_time < Carbon::now()) && ($auction->status == $auction->getStatusType(1))){
			    $bid = new Bid();
			    $bid->price = $auction->buy_price;
			    $bid->auction_id = $request->auctionId;
			    $bid->trader_id = $traderId;
			    $bid->save();
			} else {
			    return $this->errorResponse(trans('api.expired'));
			}

			$auction->status = $auction->getStatusType(3);
			$auction->end_time = date('Y-m-d H:i',$currentTime);
			$auction->bid_owner = $traderId;
            $auction->save();

            $auction->firebaseDelete();


			$this->sendAuctionEndPush($request->auctionId);

			return $this->successResponse(trans('api.bid_buy_success'));
		}

		return $this->errorResponse(trans('api.bid_buy_invalid'));
	}


	public function auctionHistory(Request $request){

		/*$validator = Validator::make($request->all(),array('auctionId' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000, "Status" => $validator->errors()->all()]);
        }*/

		$user =  TraderUser::where('api_token',$request->api_token)->first();
          if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
          }
		$traderId = $user->id;

		$auction = Auction::where('bid_owner', $traderId)
							->where('status', '>', 2)
							->where('status', '!=', 12)
							//->whereIn('status', array(1, 2, 3))
							->orderBy('id','desc')
							->get();

                                   // dd($auction);
		$data = array();

		if(!empty($auction)){

			$i=0;

			foreach($auction as $_auction){

				$lastBid = Bid::where('auction_id', $_auction->id)->orderBy('price','desc')->first();
				$bidPrice = $lastBid ? $lastBid->price : 0;
				$bidDate = $lastBid ? $lastBid->created_at->format('Y-m-d H:i:s') : '';
				//2017-04-23 18:50:23


				$data[$i]['id'] = $_auction->id;
				$data[$i]['title'] = $_auction->title;
				$data[$i]['start_time'] = $_auction->start_time;
				$data[$i]['end_time'] = $_auction->end_time;
				$data[$i]['type'] = $_auction->type;
                    $data[$i]['base_price'] = $_auction->base_price;

				$data[$i]['status_color'] = $_auction->getStatusColor($_auction->getStatusValue($_auction['status']));
				$data[$i]['buy_price'] = $_auction->buy_price;
				$data[$i]['currency'] = $_auction->currency;
				$data[$i]['dealer_id'] = $_auction->dealer_id;
				$data[$i]['is_negotiated'] = $_auction->is_negotiated;
				$data[$i]['min_increment'] = $_auction->min_increment;
				$data[$i]['object_id'] = $_auction->object_id;

				$image =  $_auction->objectImage() ? $_auction->objectImage()->toArray() : [];
                    //$imageUrl = url('uploads/object/'.$image['image']);
                    $data[$i]['image'] = $image ? $image['image'] : '';

				$data[$i]['bid_price'] = $bidPrice;
				$data[$i]['bid_date'] = $bidDate;
                    $data[$i]['status_message'] = $_auction['status'];
                    $data[$i]['status'] = $_auction->getStatusValue($_auction['status']);

				$i++;
			}

			//echo json_encode($data);
			//exit;
			return $this->successResponse(trans('api.auction_history'), $data);


		}else{
			return $this->errorResponse(trans('api.auctions_not_found'));
		}

	}

     public function auctionHistoryV2(Request $request){

		/*$validator = Validator::make($request->all(),array('auctionId' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000, "Status" => $validator->errors()->all()]);
        }*/

		$user =  TraderUser::where('api_token',$request->api_token)->first();
          if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
          }
		$traderId = $user->id;

		$auction = Auction::where('bid_owner', $traderId)
							->where('status', '>', 2)
							->where('status', '!=', 12)
							//->whereIn('status', array(1, 2, 3))
							->orderBy('id','desc')
							->get();

                                   // dd($auction);
		$data = array();

		if(!empty($auction)){

			$i=0;

			foreach($auction as $_auction){

				$lastBid = Bid::where('auction_id', $_auction->id)->orderBy('price','desc')->first();
				$bidPrice = $lastBid ? $lastBid->price : 0;
				$bidDate = $lastBid ? $lastBid->created_at->format('Y-m-d H:i:s') : '';
				//2017-04-23 18:50:23


				$data[$i]['id'] = $_auction->id;
				$data[$i]['title'] = $_auction->title;
				$data[$i]['start_time'] = strtotime($_auction->start_time);
				$data[$i]['end_time'] = strtotime($_auction->end_time);
                    // $data[$i]['start_time1'] = $_auction->start_time;
				// $data[$i]['end_time1'] = $_auction->end_time;
				$data[$i]['type'] = $_auction->type;
                    $data[$i]['base_price'] = $_auction->base_price;

				$data[$i]['status_color'] = $_auction->getStatusColor($_auction->getStatusValue($_auction['status']));
				$data[$i]['buy_price'] = $_auction->buy_price;
				$data[$i]['currency'] = $_auction->currency;
				$data[$i]['dealer_id'] = $_auction->dealer_id;
				$data[$i]['is_negotiated'] = $_auction->is_negotiated;
				$data[$i]['min_increment'] = $_auction->min_increment;
				$data[$i]['object_id'] = $_auction->object_id;

				$image =  $_auction->objectImage() ? $_auction->objectImage()->toArray() : [];
                    //$imageUrl = url('getObjectDetail'.$image['image']);
                    $data[$i]['image'] = $image ? $image['image'] : '';

				$data[$i]['bid_price'] = $bidPrice;
				$data[$i]['bid_date'] = $bidDate;
                    $data[$i]['status_message'] = $_auction['status'];
                    $data[$i]['status'] = $_auction->getStatusValue($_auction['status']);

				$i++;
			}

			//echo json_encode($data);
			//exit;
			return $this->successResponse(trans('api.auction_history'), $data);


		}else{
			return $this->errorResponse(trans('api.auctions_not_found'));
		}

	}



	public function refreshBid(Request $request){


		$validator = Validator::make($request->all(),array('auctionId' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000, "Status" => $validator->errors()->all()]);
        }


		$auction = Auction::where('id', $request->auctionId)->first();

		$auction->refreshToken = time().rand(1,1000);
		$auction->save();

		return $this->successResponse(trans('api.bid_refreshed'));


	}


  public function getFirstRoundTradersIds($auctionId){

        $data =array();
        //Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);

        //$firstRoundUsers = Bid::where('auction_id', $auctionId)->get();

        $firstRoundUsers = Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);

        if(!empty($firstRoundUsers)){
          foreach($firstRoundUsers as $user){
            $data[] = $user->trader_id;
          }
        }

        if(!empty($data)){
          return $data;
        }else{
          return '';
        }

  }

  public function checkAndPrebid($auctionId, $amount){

      $maxAmountEntry = AutomaticBid::where('auction_id', '=', $auctionId)->orderBy('amount','desc')->first();

      if(empty($maxAmountEntry->amount)){
          return false;
      }

      $maxAmount = $maxAmountEntry->amount;

      if($maxAmount != $amount){
          return false;
      }


      $topTrader = AutomaticBid::where('auction_id', '=', $auctionId)
                  ->where('amount', '=', $maxAmount)
                  ->limit(1)
                  ->orderBy('updated_at','asc')
                  ->first();

      if(empty($topTrader)){
          return false;
      }

      /*$creditLimit = CreditHistory::where('trader_id',$topTrader->trader_id)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$topTrader->trader_id)->orderBy('created_at','desc')->first() : 0;
      if($maxAmount > $creditLimit->credit_limit){
          return false;
      }*/


      $bid = new Bid();
      $bid->price = $maxAmount;
      $bid->auction_id = $auctionId;
      $bid->trader_id = $topTrader->trader_id;
      $bid->save();

      $this->setAuctionUpdate($auctionId, $bid);

      return true;

  }

	public function addBid(Request $request){

		$validator = Validator::make($request->all(),array('auctionId' => 'required','price' => 'required'));
    if ($validator->fails()) {
        return response()->json(["StatusCode" => 20000, "Status" => $validator->errors()->all()]);
    }

		$user =  TraderUser::where('api_token',$request->api_token)->first();

    if(empty($user->status)){
        return $this->sessionExpireErrorResponse(trans('api.session_expire'));
    }

    if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
       return $this->sessionExpireErrorResponse(trans('api.session_expire'));
    }


		$traderId = $user->id;

		$TraderEmiratesIdFront = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','emirates_id_front')->first();
		$TraderEmiratesIdBack = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','emirates_id_back')->first();
		$TraderPassportFront = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','passport_front')->first();
		$TraderPassportBack = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','passport_back')->first();
		if(empty($TraderEmiratesIdFront)){
			return $this->errorResponse(trans('api.emirates_id_front'));
		}
		if(empty($TraderEmiratesIdBack)){
			return $this->errorResponse(trans('api.emirates_id_back'));
		}
		if(empty($TraderPassportFront)){
			return $this->errorResponse(trans('api.passport_front'));
		}
		if(empty($TraderPassportBack)){
			return $this->errorResponse(trans('api.passport_back'));
		}

		$auction = Auction::where('id', $request->auctionId)->first();

		if( strtotime($auction->end_time) < strtotime(Carbon::now()->addSeconds(3))) {
			 //Log::error('Bid expire- 01');
			return $this->errorResponse(trans('api.expired'));
		}

    //Negotiation Bid check
    if($auction->is_negotiated == 1){
        $negTraderIds = $this->getFirstRoundTradersIds($auction->id);
        if(!empty($negTraderIds)){
            if (!in_array($traderId, $negTraderIds)) {
                return $this->errorResponse(trans('api.bid_user_invalid'));
            }
        }
    }

		 //Log::error('Bid expire- 02');

		$minAmount = $auction->min_increment;

		$lastBid = Bid::where('auction_id', $request->auctionId)->max('price');


		if(empty($lastBid)){
			$nextBidAmount = $auction->base_price;
		}else{
			$nextBidAmount = $minAmount + $lastBid;
		}

		//echo $nextBidAmount; exit;


		$price = $request->price;

		if($price >= $nextBidAmount){
      /*$creditLimit = CreditHistory::where('trader_id',$user->id)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$user->id)->orderBy('created_at','desc')->first() : 0;
      if(empty($creditLimit->credit_limit)|| $price > $creditLimit->credit_limit){
          return $this->errorResponse(trans('api.limit_exceed'));
      }*/
			//check automatic bid exists
			$automaticTrader = AutomaticBid::where('auction_id', '=', $request->auctionId)
									->where('trader_id', '=', $traderId)
									->first();


			if(!empty($automaticTrader) && ($request->price <= $automaticTrader->amount)){
				return $this->errorResponse(trans('api.automatic_bid_amount_exist'));
			}


			/*$endTime = strtotime($auction->end_time);

			$currentTime = time();

			$diff =  round((abs(strtotime($endTime) - $currentTime)/60), 2 );

			echo $diff;
			exit;*/

			//if((!$auction->end_time < Carbon::now()) && ($auction->status == $auction->getStatusType(1))){

			if((!(strtotime($auction->end_time) < strtotime(Carbon::now()->addSeconds(3)) ) ) && ($auction->status == $auction->getStatusType(1))){

          //check whether automatic bid exist for the given value - done for Autobid2
          /*$preAutoBidPalcedStatus = $this->setAutomaticBid($request->auctionId, $traderId, '','', true);


          if($preAutoBidPalcedStatus == 'PreBidded'){
             return $this->errorResponse(trans('api.bid_amount_invalid'));
          }*/

          $preAutoBidPalcedStatus = $this->checkAndPrebid($request->auctionId, $request->price);
          if($preAutoBidPalcedStatus){
             return $this->errorResponse(trans('api.bid_amount_automatic_exist'));
          }


			    $bid = new Bid();
			    $bid->price = $request->price;
			    $bid->auction_id = $request->auctionId;
			    $bid->trader_id = $traderId;
			    $bid->save();
			} else {
			    return $this->errorResponse(trans('api.expired'));
			}

			//$this->setAuctionUpdate($request->auctionId, $bid->updated_at);

			//$this->bidTimeUpdate($auction);

			/*$myBid = new Bid();
			$myBid->price = $request->price + 1000;
			$myBid->auction_id = $request->auctionId;
			$myBid->trader_id = 5;
			$myBid->save();*/

			//$this->setAuctionUpdate($request->auctionId, $myBid->updated_at);
      $user->last_bid = date('Y-m-d H:i:s');
      $user->save();
			$this->setAutomaticBid($request->auctionId, $traderId, $bid);

      $lastBidTrader = Bid::where('auction_id', $request->auctionId)->orderBy('price','desc')->first();
      if( (!empty($lastBidTrader->trader_id)) && ($lastBidTrader->trader_id != $traderId)){
          return $this->errorResponse(trans('api.bid_amount_invalid'));
      }else{
          return $this->successResponse(trans('api.bid_success'));
      }

		}else{

			//sync db value to firebase to avoid mismatch



			return $this->errorResponse(trans('api.bid_amount_invalid'));
		}


	}


	private function getTimeDifference($id){
		$auction = Auction::where('id',$id)->first();



	}



	private function setAuctionUpdate($id, $bid){
    if(empty($bid)){
        return;
    }

		$auction = Auction::where('id',$id)->first();

		$this->sendAutomaticPush($id, $bid->price);

		$endTime = strtotime($auction->end_time);

		$currentTime = time();

		$diff =  round((abs(strtotime($endTime) - $currentTime)/60), 2 );
		//round(abs($endTime - $currentTime) / 60,2)

		$minutes =1;

		//$date = '2017-04-20 19:59:16';

		//echo $date.'<br>';

		$nextendTime = strtotime("+".$minutes." minutes", strtotime($endTime));

		$nextendTimeDate = date("Y-m-d H:i:s",$nextendTime );


		if($diff <= 1){
			$nextendTime = '';
			//$auction->end_time = $nextendTimeDate;
		}


		$auction->refreshToken = time().rand(1,1000);

		//$auction->updated_at = $bid->updated_at;
		$auction->save();

		//echo $bid->updated_at; exit;

		$this->sendBidPush($id, $bid->price);

		return true;

	}


	public function bidTimeUpdate($auction=''){



		$endTime = strtotime($auction->end_time);
		//$endTime = date("Y-m-d H:i:s", strtotime('+5 hours'));
		$currentTime = time();

		$diff =  round((abs(strtotime($endTime) - $currentTime)/60), 2 );
		//round(abs($endTime - $currentTime) / 60,2)

		$minutes =1;

		//$date = '2017-04-20 19:59:16';

		//echo $date.'<br>';

		$nextendTime = strtotime("+".$minutes." minutes", strtotime($endTime));

		$nextendTimeDate = date("Y-m-d H:i:s",$nextendTime );


		if($diff <= 1){
			$nextendTime = '';
			$auction->end_time = $nextendTimeDate;
            $auction->save();
		}

		return true;


	}


	public function setAutomaticBid($auctionId, $currentTraderId, $bid, $flag=1, $isPreBid=false){
   //Log::error('Autobid reached - 1');
		//Find the automatic bid count
		$count = AutomaticBid::where('auction_id', '=', $auctionId)->count();

		if(empty($count)){
			$this->setAuctionUpdate($auctionId, $bid);
			return;
		}
		$status='';

		$auction = Auction::where('id', $auctionId)->first();
		$minAmount = $auction->min_increment;

		$lastBid = Bid::where('auction_id', $auctionId)->max('price');

		$nextBidAmount = $lastBid + $minAmount;

		//Log::error('Bid - automatic');
		//echo $nextBidAmount; exit;

		$maxAmount = AutomaticBid::where('auction_id', '=', $auctionId)->orderBy('amount','desc')->first();



		if($lastBid >= $maxAmount->amount){
			$this->setAuctionUpdate($auctionId, $bid);
			return false;
		}



		if($count > 1){

			 //Log::error('Bid - 01');
			//same amount for two users



			$topTrader = AutomaticBid::where('auction_id', '=', $auctionId)
										->where('amount', '=', $maxAmount->amount)
										->count();

			if($topTrader > 1){
				//Log::error('Bid - 02');

				$topTrader = AutomaticBid::where('auction_id', '=', $auctionId)
										->where('amount', '=', $maxAmount->amount)
										->limit(1)
										//->orderBy('amount','desc')
										->orderBy('updated_at','asc')
										->first();

										//return $topTrader;

				/*$creditLimit = CreditHistory::where('trader_id',$topTrader->trader_id)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$topTrader->trader_id)->orderBy('created_at','desc')->first() : 0;
          if($maxAmount->amount > $creditLimit->credit_limit){
              return $this->errorResponse(trans('api.limit_exceed'));
          }*/


				$bid1 = new Bid();
				$bid1->price = $maxAmount->amount;
				$bid1->auction_id = $auctionId;
				$bid1->trader_id = $topTrader->trader_id;
				$bid1->save();

				$this->setAuctionUpdate($auctionId, $bid1);
        //Log::error('Top trader - 1');
				return;
			}


      //Log::error('auctionId - '.$auctionId);
			$trader = AutomaticBid::where('auction_id', '=', $auctionId)
									->offset(1)->limit(1)
									->groupBy('amount')
									->orderBy('amount','desc')
									->count();

			if(empty($trader))	{
        //Log::error('status  - loop ');
				$status = 1;
			}

		}

		//Log::error('Bid - 03');
    //Log::error('count - '.$count);
    //Log::error('status - '.$status);
    //Log::error('NextBidAmount - '.$nextBidAmount);

		//count equal 1
		if(($count == 1) || ($status == 1)){

			$trader = AutomaticBid::where('auction_id', '=', $auctionId)->orderBy('amount','desc')->first();
			$maxBidAmount = $trader->amount;
			$traderId = $trader->trader_id;

			 //Log::error('Bid - 1');
			if($currentTraderId == $traderId){
				$this->setAuctionUpdate($auctionId, $bid);
				return;
			}


			 //Log::error('Bid - 2');
			if($nextBidAmount < $maxBidAmount){
        //Autobid2
        if($isPreBid){
            return false;
        }


				 //Log::error('Bid - 3');
      /*  $creditLimit = CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() : 0;
        if($nextBidAmount > $creditLimit->credit_limit){
            return $this->errorResponse(trans('api.limit_exceed'));
        }*/

				$bid1 = new Bid();
				$bid1->price = $nextBidAmount;
				$bid1->auction_id = $auctionId;
				$bid1->trader_id = $traderId;
				$bid1->save();

				$this->setAuctionUpdate($auctionId, $bid1);
			}else{


        //second higest automatic bidder is less than next bid amount Autobid2
        $bid1 = new Bid();
				$bid1->price = $maxBidAmount;
				$bid1->auction_id = $auctionId;
				$bid1->trader_id = $traderId;
				$bid1->save();
        //$nextBidAmount = $maxBidAmount;
				$this->setAuctionUpdate($auctionId, $bid1);
        if($isPreBid){
            return 'PreBidded';
        }
			}


		}else{
			//Log::error('Bid - 4');
			//count greater than 2
			//$nextBidAmount='';

			//second higest bid

      //Log::error('second higest bid - 1');



			$trader = AutomaticBid::where('auction_id', '=', $auctionId)
									->offset(1)->limit(1)
									->groupBy('amount')
									->orderBy('amount','desc')
									->get();

			$secondmaxBidAmount = $trader[0]->amount;
			$traderId = $trader[0]->trader_id;



			$secondnextBidAmount = $secondmaxBidAmount + $minAmount;

			$tempNextBid = $nextBidAmount;

			if($secondnextBidAmount > $nextBidAmount){
				//Log::error('Bid - 13');
				 $nextBidAmount = $secondnextBidAmount;
			}


			//higest person
			$firstTrader = AutomaticBid::where('auction_id', '=', $auctionId)
									->orderBy('amount','desc')
									->first();

			//var_dump($firstTrader); exit;

			$maxBidAmount = $firstTrader->amount;

			if($secondmaxBidAmount == $maxBidAmount){


			}



			$traderId = $firstTrader->trader_id;

			if($flag){
				//Log::error('Bid - flag');
				if($currentTraderId == $traderId){
					$this->setAuctionUpdate($auctionId, $bid);
					return;
				}
			}
			//\in normal case where second last automatic bidder price + min amount is less than firt automatic bidder
			if($nextBidAmount <= $maxBidAmount ){
        //Log::error('second higest bid - 5');
				//Log::error('Bid - 8');
        /*$creditLimit = CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() : 0;
        if($nextBidAmount > $creditLimit->credit_limit){
            return $this->errorResponse(trans('api.limit_exceed'));
        }*/
				$bid1 = new Bid();
				$bid1->price = $nextBidAmount;
				$bid1->auction_id = $auctionId;
				$bid1->trader_id = $traderId;
				$bid1->save();

				$this->setAuctionUpdate($auctionId, $bid1);


			}else{
        if($isPreBid){
          return false;
        }
				//Log::error('Bid - 9');
				//first and second have same value
				$diff = $nextBidAmount - $maxBidAmount;
				if($diff  == $minAmount){
                                        /*$creditLimit = CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() : 0;
                                        if($nextBidAmount > $creditLimit->credit_limit){
                                            return $this->errorResponse(trans('api.limit_exceed'));
                                        }*/


					if($nextBidAmount > $maxBidAmount){
						//Log::error('Bid - 12');
						$this->setAuctionUpdate($auctionId, $bid);
						return;
					}


					$bid1 = new Bid();
					$bid1->price = $nextBidAmount;
					$bid1->auction_id = $auctionId;
					$bid1->trader_id = $traderId;
					$bid1->save();
					//Log::error('Bid - 10');
					$this->setAuctionUpdate($auctionId, $bid1);

				}else{

					//if the difrence is less than minuum amount but both are not same. it will go to the higest automatic bid..
					$bid1 = new Bid();
					$bid1->price = $maxBidAmount;
					$bid1->auction_id = $auctionId;
					$bid1->trader_id = $traderId;
					$bid1->save();
					$this->setAuctionUpdate($auctionId, $bid1);


					//Log::error('Bid - 11');
					//$this->setAuctionUpdate($auctionId, $bid);
				}

			}


		}

    if($isPreBid){
        return 'PreBidded';
    }

    return true;

	}


	public function setAutomaticBidAmount(Request $request){


		$validator = Validator::make($request->all(),array('auctionId' => 'required', 'amount' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000, "Status" => $validator->errors()->all()]);
        }


		$key = 123456;
		$max = 1;
		$permissions = 0666;
		$autoRelease = 1;

		//Open a new or get an existing semaphore
		// $semaphore = sem_get($key, $max, $permissions, $autoRelease);
		// 	if(!$semaphore) {
		// 		return $this->errorResponse('semaphore issue');
		// 	}

		// 	sem_acquire($semaphore);

			$user =  TraderUser::where('api_token',$request->api_token)->first();

      if(empty($user->status)) {
          return $this->sessionExpireErrorResponse(trans('api.session_expire'));
      }

       if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
           return $this->sessionExpireErrorResponse(trans('api.session_expire'));
       }


			$auction = Auction::where('id', $request->auctionId)->first();
      //Negotiation Bid check
      if($auction->is_negotiated == 1){
          $negTraderIds = $this->getFirstRoundTradersIds($auction->id);
          if(!empty($negTraderIds)){
              if (!in_array($user->id, $negTraderIds)) {
                  return $this->errorResponse(trans('api.bid_user_invalid'));
              }
          }
      }
			$minAmount = $auction->min_increment;

			$lastBid = Bid::where('auction_id', $request->auctionId)->orderBy('price','desc')->first();

			//if($lastBid->)
			//$topAutomatic = AutomaticBid::where('auction_id', '=', $request->auctionId)->order->first();

			//return $topAutomatic;

			if( (!empty($lastBid)) && ($request->amount <= $lastBid->price)){
				return $this->errorResponse(trans('api.invalid_max_amount'));
			}


			if(empty($lastBid)){
				$nextBidAmount = $auction->base_price;
			}else{
				$nextBidAmount = $minAmount + $lastBid->price;
			}

				//Log::error('auction - 3');

			if($nextBidAmount >= $request->amount){
				return $this->errorResponse(trans('api.invalid_max_amount'));
			}


			$traderId = $user->id;

			$TraderEmiratesIdFront = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','emirates_id_front')->first();
			$TraderEmiratesIdBack = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','emirates_id_back')->first();
			$TraderPassportFront = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','passport_front')->first();
			$TraderPassportBack = \App\TraderImages::where('traderId', $traderId)->where('imageType','=','passport_back')->first();
			if(empty($TraderEmiratesIdFront)){
				return $this->errorResponse(trans('api.emirates_id_front'));
			}
			if(empty($TraderEmiratesIdBack)){
				return $this->errorResponse(trans('api.emirates_id_back'));
			}
			if(empty($TraderPassportFront)){
				return $this->errorResponse(trans('api.passport_front'));
			}
			if(empty($TraderPassportBack)){
				return $this->errorResponse(trans('api.passport_back'));
			}


			$exists = AutomaticBid::where('trader_id', '=', $traderId)
									->where('auction_id', '=', $request->auctionId)->first();
		/*	$creditLimit = CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() : 0;
			if($request->amount > $creditLimit->credit_limit){
			   return $this->errorResponse(trans('api.limit_exceed'));
			}*/

			//if((!$auction->end_time < Carbon::now()) && ($auction->status == $auction->getStatusType(1))){

			if((!(strtotime($auction->end_time) < strtotime(Carbon::now()->addSeconds(3)) ) ) && ($auction->status == $auction->getStatusType(1))){

			if (!$exists) {
				$bid = new AutomaticBid();
				$bid->amount = $request->amount;
				$bid->auction_id = $request->auctionId;
				$bid->trader_id = $traderId;
				$bid->save();
			}else{

				$bid = AutomaticBid::find($exists['id']);
				$bid->amount = $request->amount;
				$bid->save();
			}
						} else {
							return $this->errorResponse(trans('api.expired'));
						}
			//Log::error('auction - 3');


			if(!empty($lastBid)){
					//Log::error('auction - 1');

				$data['amount'] = (int) $request->amount;

				if($lastBid->trader_id == $traderId){
					return $this->successResponse(trans('api.bid_max_set_success'), $data);
				}
				$this->setAutomaticBid($request->auctionId, $lastBid->trader_id, $lastBid, 0);

			}else{

				//Log::error('auction - 2');
							/*$creditLimit = CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() ? CreditHistory::where('trader_id',$traderId)->orderBy('created_at','desc')->first() : 0;
							if($auction->base_price > $creditLimit->credit_limit){
								return $this->errorResponse(trans('api.limit_exceed'));
							}*/
				$bid = new Bid();
				$bid->price = $auction->base_price;
				$bid->auction_id = $request->auctionId;
				$bid->trader_id = $traderId;
				$bid->save();

				$this->setAuctionUpdate($request->auctionId, $bid);

				//echo 343; exit;
			}
			//Log::error('auction - 3');
			$data['amount'] = (int) $request->amount;


			// sem_release($semaphore);
			return $this->successResponse(trans('api.bid_max_set_success'), $data);

	}





    protected function user($request){
        $user =  TraderUser::where('api_token',$request->api_token)->first();
        if(!$user){
            return $this->errorResponse(trans('api.not_found'));
        }
        return $user;
    }

    public function getProfile(Request $request)
    {
        return $this->successResponse(trans('api.user_details'),$this->user($request));
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(),array('old_password' => 'required','new_password' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }
        $user = $this->user($request);
        if(!Hash::check($request->old_password, $user->password)){
            return $this->errorResponse(trans('api.password_error'));
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return $this->successResponse(trans('api.password_update'),$user);
    }

}
