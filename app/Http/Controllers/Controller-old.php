<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

use DateTime;
use DateTimeZone;
use PushNotification;
use App\Object;
use App\Auction;
use App\TraderUser;
use App\NotificationPreference;
use App\Notifications;
use Illuminate\Support\Facades\Log;
use App\Bid;
use App\AutomaticBid;

use App\ObjectAttributeValue;


class OlddController extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
    
    public $bcc = ['fc@watchmybid.com'];
    public function UaeDate($dts) {
        //dd($dts);
        $date = new DateTime($dts, new DateTimeZone('UTC'));
        //var_dump($date->format('Y-m-d H:i:sP'));

        // convert timezone to Asia/Dubai +4 UTC
        $date->setTimezone(new DateTimeZone('Asia/Dubai'));
        return $date->format('Y-m-d H:i:s');
    }
	
	public function test(){
		
		//$this->sendNotificationToDevice(); exit;
		$this->sendAutomaticPush(130, 15000);
		
	}
	
	public function sendAutomaticPush($auctionId, $price){
		
		$maxData = AutomaticBid::where('auction_id', $auctionId)->where('amount', '<', $price)->limit(1)->orderBy('amount','desc')->first();
		
		if(!empty($maxData)){
			
			$lastBid = Bid::where('auction_id', $auctionId)->max('price');
			
			$traderId = $maxData->trader_id;
			
			$auction = Auction::find($auctionId);
			
			//$msg = 'Your maximum bid amount for '.$auction->title.' have crossed and current bid amount is '.(int) $lastBid;
			
			$msg = 'Your maximum bid amount for '.$auction->title.' has been exceeded. Increase your offer now!';
		
			$devices['iosDevices'] = array();
			$devices['androidDevices'] = array();
			
			$trader = TraderUser::find($traderId);
			$this->savePushMessage($auctionId, $trader->id, $msg, $msg);
			
			if(!empty($trader->device_type) && !empty($trader->device_id)){
						
				if($trader->device_type == 'iOS'){
					$devices['iosDevices'][] = $trader->device_id;
				}elseif($trader->device_type == 'Android'){
					$devices['androidDevices'][] = $trader->device_id;
				}
			}
			
			$this->sendPushNotification($devices, $msg);
			
		}
		
	}
	
	
	public function sendAuctionStartPush($auctionId, $msg=''){
		
		$auction = Auction::find($auctionId);
		$objectId = $auction->object_id;
		
		//$msg = 'Auction started for '.$auction->title;
		
		$msg = $auction->title.', How much would you pay for this?';
		
		
		$objectYear = ObjectAttributeValue::where('object_id', $objectId)->where('attribute_id', 1)->first()->attribute_value;
		$objectMileage = ObjectAttributeValue::where('object_id', $objectId)->where('attribute_id', 10)->first()->attribute_value;
		$objectMake = ObjectAttributeValue::where('object_id', $objectId)->where('attribute_id', 4)->first()->attribute_value;
		
		
		//$traders = TraderUser::where('dealer_id', $auction->dealer_id)->get();
		$traders = TraderUser::all();
		$devices['iosDevices'] = array();
		$devices['androidDevices'] = array();
		
		
		foreach($traders as $trader){
			$minMileage ='';
			$maxMileage ='';
			
			$minYear='';
			$maxYear='';
			$makes=array();
			
			$flag = false;
			
			/*if(empty($trader->device_type) && empty($trader->device_id)){
				continue;
			}*/
			
			$prefData = NotificationPreference::where('trader_id', $trader->id)->first();
			if(!empty($prefData)){
				
				
				$prefs = json_decode($prefData->options, true);
				
				//$minMileage =
				if(!empty($prefs['minMileage'])){
					$minMileage = $prefs['minMileage'];
				}
				
				if(!empty($prefs['maxMileage'])){
					$maxMileage = $prefs['maxMileage'];
				}
				
				if(!empty($prefs['maxYear'])){
					$maxYear = $prefs['maxYear'];
				}
				
				if(!empty($prefs['minYear'])){
					$minYear = $prefs['minYear'];
				}
				
				if(!empty($prefs['makes'])){
						foreach($prefs['makes'] as $make){
							//if($make['is_selected']){
								$makes[] = $make['attribute_value'];
							//}
						}
				}
				
				//
				
				
				
				if((!empty($minMileage)) && (!empty($maxMileage))){
					if( ($minMileage <= $objectMileage) && ($maxMileage >= $objectMileage)){
						$flag =true;
					}
				}
				
				if((!empty($minYear)) && (!empty($maxYear))){
					if( ($minYear <= $objectYear) && ($maxYear >= $objectYear)){
						$flag =true;
					}
				}
				
				
				if (!empty($makes) && in_array($objectMake, $makes)) {
					$flag =true;
				}
				
				
				if(!empty($flag)){
					// insert message
					$this->savePushMessage($auctionId, $trader->id, $msg, $msg);
					
					if(!empty($trader->device_type) && !empty($trader->device_id)){
						
						
						if($trader->device_type == 'iOS'){
							$devices['iosDevices'][] = $trader->device_id;
						}elseif($trader->device_type == 'Android'){
							$devices['androidDevices'][] = $trader->device_id;
						}
					}
					
					
				}
			} else {
			    $this->savePushMessage($auctionId, $trader->id, $msg, $msg);
					
			    if(!empty($trader->device_type) && !empty($trader->device_id)){
				    
				    
				    if($trader->device_type == 'iOS'){
					    $devices['iosDevices'][] = $trader->device_id;
				    }elseif($trader->device_type == 'Android'){
					    $devices['androidDevices'][] = $trader->device_id;
				    }
			    }
			}
			
		}
		
		//var_dump($devices); exit;
		
		
		$this->sendPushNotification($devices, $msg);
		
		return;
		
		
	}
	
	
	
	public function sendAuctionEndPush($auctionId){
		
		$auction = Auction::find($auctionId);
		
		$lastBid = Bid::where('auction_id', $auctionId)->max('price');
		
		//$msg = 'You won the auction of '.$auction->title.' for AED '.(int) $lastBid;
		
		$msg = $auction->title.', Thank you for your offer of AED '.(int) $lastBid.'. Please wait whilst we review your offer.';
		
		//Thank you for your offer of AEDX. Please wait whilst we review your offer.
		//echo $msg; exit;
		
		$devices['iosDevices'] = array();
		$devices['androidDevices'] = array();
		
		$bidOwnerId = $auction->bid_owner;
		
		$trader = TraderUser::find($auction->bid_owner);
		$this->savePushMessage($auctionId, $trader->id, $msg, $msg);
		
		if(!empty($trader->device_type) && !empty($trader->device_id)){
					
			if($trader->device_type == 'iOS'){
				$devices['iosDevices'][] = $trader->device_id;
			}elseif($trader->device_type == 'Android'){
				$devices['androidDevices'][] = $trader->device_id;
			}
		}
		
		//bid owner push
		$this->sendPushNotification($devices, $msg);
		
		//bid users push
		$devices['iosDevices'] = array();
		$devices['androidDevices'] = array();
		
		//$msg = 'Auction for '.$auction->title.' is completed and gone for AED '.(int) $lastBid;
		
		$msg = 'Auction for '.$auction->title.' has now ended.';
		
		$bidUsers = Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);
		if(!empty($bidUsers)){
			foreach($bidUsers as $bidUser){
				
				if($bidOwnerId != $bidUser->trader_id){
				
					$trader = TraderUser::find($bidUser->trader_id);
					
					$this->savePushMessage($auctionId, $trader->id, $msg, $msg);
					
						
					if(!empty($trader->device_type) && !empty($trader->device_id)){
						
						if($trader->device_type == 'iOS'){
							$devices['iosDevices'][] = $trader->device_id;
						}elseif($trader->device_type == 'Android'){
							$devices['androidDevices'][] = $trader->device_id;
						}
					}
				}
				
			}
		}
		
		$this->sendPushNotification($devices, $msg);
		
		return;
	}
	
	
	public function sendStatusPush($auctionId, $type){
		
		
		$auction = Auction::find($auctionId);
		
		$lastBid = Bid::where('auction_id', $auctionId)->orderBy('price','desc')->first();
		
		
		
		if($type == 1){
			//$msg = 'Your vehicle '.$auction->title.' has moved to security check ';
			$msg = 'Congratulations. Your bid of AED '.$lastBid->price.' has been accepted for '.$auction->title.'. Please await whilst your car clears Quality Control.';
			
		}elseif($type == 2){
			//$msg = 'Your vehicle '.$auction->title.' has passed security check ';
			
			$msg = $auction->title.' has cleared Quality Control. Please make a payment of AED '.$lastBid->price.' within 24 hours quoting '.$auction->title;
			
		}elseif($type == 3){
			$msg = 'Your watch '.$auction->title.' has failed Quality Control';
		}elseif($type == 4){
			return true;
			$msg = 'Your watch '.$auction->title.' has sold susessfully';
		}elseif($type == 5){
			//$msg = 'Your vehicle '.$auction->title.' has cashed susessfully';
			
			$msg = 'Thank You for your payment. '.$auction->title.' is ready for collection from the dealer between 9:00am and 5:00pm.';
			
		}else{
			return;	
		}
		
		$devices['iosDevices'] = array();
		$devices['androidDevices'] = array();
		
		$bidOwnerId = $auction->bid_owner;
		
		$trader = TraderUser::find($auction->bid_owner);
		$this->savePushMessage($auctionId, $trader->id, $msg, $msg);
		
		if(!empty($trader->device_type) && !empty($trader->device_id)){
					
			if($trader->device_type == 'iOS'){
				$devices['iosDevices'][] = $trader->device_id;
			}elseif($trader->device_type == 'Android'){
				$devices['androidDevices'][] = $trader->device_id;
			}
		}
		
		//bid owner push
		$this->sendPushNotification($devices, $msg);
		
		return;
		
		
	}
	
	
	
	public function sendAuctionNegotiatedPush($auctionId){
		
		$auction = Auction::find($auctionId);
		
		$lastBid = Bid::where('auction_id', $auctionId)->max('price');
		
		$msg = $auction->title.' bid negotiated and started again with AED '.(int)$lastBid;
		//echo $msg; exit;
		
		
		$devices['iosDevices'] = array();
		$devices['androidDevices'] = array();
		
		$bidUsers = Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);
		if(!empty($bidUsers)){
			foreach($bidUsers as $bidUser){
				
				$trader = TraderUser::find($bidUser->trader_id);
				
				$this->savePushMessage($auctionId, $trader->id, $msg, $msg);
				
					
				if(!empty($trader->device_type) && !empty($trader->device_id)){
					
					if($trader->device_type == 'iOS'){
						$devices['iosDevices'][] = $trader->device_id;
					}elseif($trader->device_type == 'Android'){
						$devices['androidDevices'][] = $trader->device_id;
					}
				}
			}
		}
		
		$this->sendPushNotification($devices, $msg);
		
		return;
	}
	
	
	
	
	
	public function sendBidPush($auctionId, $bidPrice=''){
		
		
		$auction = Auction::find($auctionId);
		
		//$msg = $auction->title.' bid price updated to AED '. (int) $bidPrice;
		
		$lastBid = Bid::where('auction_id', $auctionId)->where('price', $bidPrice)->first();
		
		$msg = $auction->title.', You have bid AED '. (int) $bidPrice;
		
		
		$devices['iosDevices'] = array();
		$devices['androidDevices'] = array();
		
		$bidOwnerId = $lastBid->trader_id;
		
		$trader = TraderUser::find($bidOwnerId);
		$this->savePushMessage($auctionId, $trader->id, $msg, $msg);
		
		if(!empty($trader->device_type) && !empty($trader->device_id)){
					
			if($trader->device_type == 'iOS'){
				$devices['iosDevices'][] = $trader->device_id;
			}elseif($trader->device_type == 'Android'){
				$devices['androidDevices'][] = $trader->device_id;
			}
		}
		
		//bid owner push
		$this->sendPushNotification($devices, $msg);
		
		
		
		$msg = $auction->title.', You have been Outbid! Increase your offer now!';
		
		$traders = TraderUser::all();
		$devices['iosDevices'] = array();
		$devices['androidDevices'] = array();
		
		$bidUsers = Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);
		if(!empty($bidUsers)){
			foreach($bidUsers as $bidUser){
				
				if($bidOwnerId != $bidUser->trader_id){
				
					$trader = TraderUser::find($bidUser->trader_id);
				
					$this->savePushMessage($auctionId, $trader->id, $msg, $msg);
				
					
					if(!empty($trader->device_type) && !empty($trader->device_id)){
						
						if($trader->device_type == 'iOS'){
							$devices['iosDevices'][] = $trader->device_id;
						}elseif($trader->device_type == 'Android'){
							$devices['androidDevices'][] = $trader->device_id;
						}
					}
				
				}
			}
		}
		
		$this->sendPushNotification($devices, $msg);
		
		return;
		
	}
	
	
	
	public function savePushMessage($auctionId, $traderId, $msg, $desc=''){
		
		$notification = new Notifications();
		$notification->title = $msg;
		$notification->desc = $desc;
		
		$notification->auction_id = $auctionId;
		$notification->trader_id = $traderId;
		$notification->save();
		
		return;
		
	}
	
	
	public function sendPushNotification($devices, $message) {
		
	
		if(!empty($devices['iosDevices'])){
			
			$this->sendNotificationToIos($devices['iosDevices'], $message);
		}
		
		if(!empty($devices['androidDevices'])){
			$this->sendNotificationToAndroid($devices['androidDevices'], $message);
		}
		
		return;
	}
	
	
	
	
	public function sendNotificationToAndroid($deviceIds, $message)
    {
		
		if(!empty($deviceIds)){
			foreach($deviceIds as $token){
				$deviceArray[] = PushNotification::Device($token);
			}
		}
		
		// Populate the device collection
		$devices = PushNotification::DeviceCollection($deviceArray);
		
		$message = PushNotification::Message($message, array(
			'badge'=> 1,
			'custom' => array('test' => '123'
			)
		));
		
		
		// Send the notification to all devices in the collect
		$collection = PushNotification::app('appNameAndroid')
			->to($devices)
			->send($message);
			
	}
	
	
	public function sendNotificationToIos($deviceIds, $message)
    { 
		
		if(!empty($deviceIds)){
			foreach($deviceIds as $token){
				$deviceArray[] = PushNotification::Device($token);
			}
		}
		
		
		
		
		// Populate the device collection
		$devices = PushNotification::DeviceCollection($deviceArray);
		
		$message = PushNotification::Message($message, array(
			'badge'=> 1,
			'custom' => array('test' => 123
			)
		));
		
		
		try {
				// Send the notification to all devices in the collect
				$collection = PushNotification::app('appNameIOSDev')
					->to($devices)
					->send($message);
			
			 }   catch(Exception $e) {
           		 Log::error($e->getMessage());
         	 }
			
	}
	
	
	
	 public function sendNotificationToDevice()
    {
        $deviceToken = 'ejbYC2KxYiw:APA91bEcTtG1m3qx_NiPMpKxKLeZgeQIFX10NR-myXvieKCTlv1ZlBJ2tNnIDYDdZoiTQpNzo2GUDENfxUF8LE37NhiLf7RmSdAdy30fHD94IE9Ju2Y7xkXrv-ufBtin5SaLsecpuUQh';

        $message = 'We have successfully sent a push notification!';

        // Send the notification to the device with a token of $deviceToken
        $collection = PushNotification::app('appNameAndroid')
            ->to($deviceToken)
            ->send($message);
			
			
    }
	
	
	
}
