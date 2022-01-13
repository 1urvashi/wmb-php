<?php

namespace App;

use DB;
//use App\Object;
use App\Sudhir\SyncsWithFirebase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\ObjectAttributeValue;
use Carbon\Carbon;
use App\Attribute;
use DateTime;
use DateTimeZone;

class Auction extends Model
{
    use SyncsWithFirebase;
    protected $guarded = ['id'];
    protected $auctionType = array(5000=>'Live', 5001=>'Inventory', 5002=>'Deals', 5003=>'Corporate');
    protected $statusArray = array(0=>'Created item', 1=>'Under auction', 2=>'Negotiated', 3=>'Auction completed',
    4=>'Waiting for Quality Control', 5=>'Failed for quality checking', 6=>'Passed quality checking', 7=>'Sold',8=>'Payment received and cashed', 9=>'stopped while under auction',
     10=>'cancelled after completion',11 => 'cancelled after security checking',12 => 'cancelled');
    protected $statusColorArray = array(0=>'#858789', 1=>'#858789', 2=>'#c97a12', 3=>'#d3c906', 4=>'#9fad0b', 5=>'#ad290c', 6=>'#6d7702', 7=>'#000',8=>'#045117', 9=>'#870514', 10=>'#870514',11 => '#870514',12 => '#870514');

    public function bids(){
        return $this->hasMany('App\Bid');
    }

    public function bid(){
        $query = $this->hasMany('App\Bid')->addSelect(DB::raw("MAX(bids.price) AS bidding_price"));
        return $query;
    }
    public function maxValue(){
        return $this->hasOne('App\Bid')->latest();
    }

    public function images() {
        return $this->hasMany('App\ObjectImage','object_id','object_id')->orderBy('sort','asc');
    }

    public function getTrader($price,$auctionId){
        return Bid::where('price',$price)->where('auction_id', $auctionId)->first() ? Bid::where('price', $price)->where('auction_id',$auctionId)->first()->trader_id : '';
    }

    public function getBidOwner($auctionId){

      $bid = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->first();
      return !empty($bid->trader_id) ? $bid->trader_id  : '';

    }

	public function getFirstRoundTraders($auctionId){

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
			return implode(',', $data);
		}else{
			return '';
		}

    }



    public function vehiclesHistory() {
        return $this->hasOne('App\Object','id', 'object_id');
    }

    public function tradersBid() {
        return $this->hasOne('App\TraderUser','id', 'bid_owner');
    }

    public function lastBid() {
        $lastBid = Bid::where('auction_id', $this->id)->orderBy('price', 'desc')->first();
        return $lastBid ? $lastBid->price : '';
    }

    public function lastBidDate() {
        $lastBidDate = Bid::where('auction_id', $this->id)->orderBy('created_at', 'desc')->first();
        return $lastBidDate ? $lastBidDate->created_at : '';
    }

    public function AuctionBid() {
        return $this->hasMany('App\Bid','id', 'auction_id');
    }


    public function objectImage()
    {
        $object = Object::find($this->object_id);
        return $object->images()->select('image')->first();
    }

    public function objectList() {
        return $this->hasOne('App\Object','id', 'object_id');
    }

    public function firebaseDelete() {
        $this->saveToFirebase('delete');
    }

    public function getFirebaseSyncData()
    {
        if ($fresh = $this->fresh()) {

			//getStatusType

			if( ($fresh->status == $fresh->getStatusType(4))  || ($fresh->status == $fresh->getStatusType(10))   || ($fresh->status == $fresh->getStatusType(6))  ||
       ($fresh->status == $fresh->getStatusType(7))  || ($fresh->status == $fresh->getStatusType(5)) || ($fresh->status == $fresh->getStatusType(8))){
				 return [];
			}

                if(($fresh->created_at != $fresh->updated_at) || (($fresh->created_at == $fresh->updated_at) && ($fresh->status == $fresh->getStatusType(1)))){

                    $result = $fresh->toArray();
                    $result = array_merge($result,$fresh->bid()->first()->toArray());
                    $image =  $fresh->objectImage() ? $fresh->objectImage()->toArray() : [];

					//$imageUrl = url('uploads/object/'.$image['image']);
                    $result['image'] = $image ? str_replace("/object/","/object/",$image['image']) : '';
					$result['image'] = $result['image'] ? str_replace("localhost","dealers.wecashanycar.com",$result['image']) : '';

                    if($result['bidding_price']){
                        $result['bid_trader_id'] = (string) $fresh->getTrader($result['bidding_price'],$result['id']);
						$result['bidding_price'] =  (int) $result['bidding_price'];
                    }

					//$result['currentTime'] = 'Firebase.database.ServerValue.TIMESTAMP';

                    $result['start_time'] = strtotime($result['start_time']);


                    $result['end_time'] = $this->_timeDifference($fresh);

                    $result['created_at'] = strtotime($result['created_at']);
                    $result['type'] = (int) $result['type'];
                    $result['base_price'] = (int) $result['base_price'];
                    $result['buy_price'] = (int) $result['buy_price'];
                    $result['min_increment'] = (int) $result['min_increment'];
                    $result['is_negotiated'] = (int) $result['is_negotiated'];

                    $result['final_req_amount'] = (int) $result['final_req_amount'];

                    if(!empty($result['final_req_amount'])){
                        $result['negotiated_traders'] = (string) $fresh->getBidOwner($result['id']);
                    }else{
                        if(!empty($result['is_negotiated'])){
              						$result['negotiated_traders'] = (string) $fresh->getFirstRoundTraders($result['id']);
              					}
                    }

                    $result['negotiated_amount'] = (int) $result['negotiated_amount'];

                    $result['status'] = $this->getStatusValue($result['status']);
		    return $result;
                }
        }
        return [];
    }

    public function _timeDifference($auction){
        $time = strtotime($auction->end_time);
        //if($auction->type == '5000'){
            $end = Carbon::parse($auction->end_time);
            $diff = Carbon::now()->diffInSeconds($end);



			if( strtotime($auction->end_time) < strtotime(Carbon::now()->addSeconds(3))) {
				//Log::error('Bid expire- 04');
				return $time;
			}

            if(($diff <= 60) && ($auction->status == $auction->getStatusType(1)) ){
                // Log::error('Last minut Bid');
                if($auction->is_negotiated == 1){
                    $time = strtotime('+30 seconds',strtotime($auction->end_time));
                    $auction->end_time = $this->UaeDate(Carbon::createFromTimestamp($time)->toDateTimeString());
                    $auction->save();
                }
            }
      //  }
        return $time;
    }

   function getAuctionType($index){
       return $this->auctionType[$index];
   }

   function getAuctionTypes(){
       return $this->auctionType;
   }

   function getAuctionValue($value){
      return array_search($value, $this->auctionType);
   }

   function getStatusType($index){
       return $this->statusArray[$index];
   }


   function getStatusTypes(){
       return $this->statusArray;
   }

   function getStatusValue($value){
      return array_search($value, $this->statusArray);
   }

   function getStatusColor($index){
      return $this->statusColorArray[$index];
   }

    /*public function setTypeAttribute($value)
    {
        $this->attributes['type'] = $this->getAuctionValue($value);
    }

    public function getTypeAttribute($value)
    {
        return $this->getAuctionType($value);
    }*/

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $this->getStatusValue($value);
    }

	public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = $this->convertTimeToUTCzone($value);
    }

	public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = $this->convertTimeToUTCzone($value);
    }

	public function convertTimeToUTCzone($str, $userTimezone='Asia/Dubai', $format = 'Y-m-d H:i:s'){
		//dd($str->format('Y-m-d H:i:s'));
		$date = new DateTime($str, new DateTimeZone('Asia/Dubai'));
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format('Y-m-d H:i:s');

		/*
    	$new_str = new DateTime($str, new DateTimeZone(  $userTimezone  ) );
   	 	$new_str->setTimeZone(new DateTimeZone('UTC'));
    	return $new_str->format( $format);*/
	}

    public function getStatusAttribute($value)
    {
        return $this->getStatusType($value);
    }

    public function getObjectValue($attributeSet, $objectId) {
        $attributeId = Attribute::where('attribute_set_id',$attributeSet)->where('invisible_to_trader',0)->orderBy('sort','asc')->lists('id');
        return ObjectAttributeValue::whereIn('attribute_id',$attributeId)->where('object_id',$objectId)->with('attribute')->get();
    }

    public function getColorValue($_color) {
        $color = ['green'=>'good','yellow'=>'medium','red'=>'bad'];
        return $color[$_color];
    }
    public function getObjectAttribute($id,$object) {
        $attribute = Attribute::where('id',$id)->first();
        if(!$attribute){
            return '';
        }
        $objAttribute = ObjectAttributeValue::where('attribute_id',$attribute->id)->where('object_id',$object)->first();
        return $objAttribute ? $objAttribute : '';
    }

    public function UaeDate($dts) {
        //dd($dts);
        $date = new DateTime($dts, new DateTimeZone('UTC'));
        //var_dump($date->format('Y-m-d H:i:sP'));

        // convert timezone to Asia/Dubai +4 UTC
        $date->setTimezone(new DateTimeZone('Asia/Dubai'));
        return $date->format('Y-m-d H:i:s');
    }
}
