<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use DateTime;
use DateTimeZone;
//use App\Object;
use App\Auction;
use App\TraderUser;
use App\InspectorUser;
use App\NotificationPreference;
use App\Notifications;
use Illuminate\Support\Facades\Log;
use App\Bid;
use App\AutomaticBid;
use Carbon\Carbon;
use App\InspectorNegaotiate;
use DB;
use App\Make;
use App\Models;
use App\ObjectAttributeValue;
use App\SmsNotification;
//use Edujugon\PushNotification\PushNotification;
use Twilio\Rest\Client;

class Controller extends BaseController {

    use AuthorizesRequests,
        AuthorizesResources,
        DispatchesJobs,
        ValidatesRequests;

    public $bcc = ['fc@watchmybid.com'];

    public function UaeDate($dts) {
        //dd($dts);
        $date = new DateTime($dts, new DateTimeZone('UTC'));
        //var_dump($date->format('Y-m-d H:i:sP'));
        // convert timezone to Asia/Dubai +4 UTC
        $date->setTimezone(new DateTimeZone('Asia/Dubai'));
        return $date->format('Y-m-d H:i:s');
    }

    public function test() {

        //$this->sendNotificationToDevice(); exit;
        $this->sendAutomaticPush(130, 15000);
    }

    public function getSalePrice($auction, $price, $other_amount){

        //$price = 800;
        $result = array();

        $result['vat'] = null;
        $result['flatValue'] = null;
        $result['percentageValue'] = null;
        $result['amount'] = null;
        $result['status'] = null;
        $result['sales_type_name'] = null;
        $result['sales_type_type'] = null;
        $result['rta_charge'] = 0;
        $result['poa_charge'] = 0;
        $result['transportation_charge'] = 0;
        $result['sales_type_id'] = null;


        if(empty($auction->sale_type_id)){
           return $result;
        }
        // $price = 800;

        $amount = 0;
        $saleType  = \App\SalesType::where('id', $auction->sale_type_id)->first();
        $vat = !empty(\App\GlobalVat::where('slug','global-vat')->first()) ? \App\GlobalVat::where('slug','global-vat')->first()->vat : 0;
        // $registered_in = '';
       // $registered_in = \App\ObjectAttributeValue::where('object_id', $auction->object_id)->where('attribute_id', 20)->first();
       // $bankLoan = \App\ObjectAttributeValue::where('object_id', $auction->object_id)->where('attribute_id', 18)->first();

        $saleTypeTransportationCharge = 0;
        $poaCharge = 0;

        if(!empty($saleType)){
            // if($bankLoan->attribute_value == 'Yes') {
            //     $poaCharge = $saleType->poa_charge;
            // }
            // if($registered_in->attribute_value == 'Abu Dhabi') {
            //     $saleTypeTransportationCharge = $saleType->transportation_charge;
            // }

            $result['sales_type_id'] = $saleType->id;
            $result['sales_type_name'] = $saleType->name;
            $result['rta_charge'] = $saleType->rta_charge;
            $result['poa_charge'] = $poaCharge;
            $result['transportation_charge'] = $saleTypeTransportationCharge;
           // $result['bank_loan'] = $bankLoan->attribute_value == 'Yes' ? true : false;
           // $result['registered_in'] = $registered_in->attribute_value == 'Abu Dhabi' ? true : false;
            //Traditional

            if($saleType->sale_type == 1) {
                $result['sales_type_type'] = $saleType->sale_type;
                if(!empty($vat)) {
                    $result['vat'] = $vat;
                    $result['status']  = 1;
                    $vAmount = ($vat / 100) * $price;
                    $extra_charge_sales_with_vat = $saleType->rta_charge + $poaCharge + $saleTypeTransportationCharge + $other_amount + $vAmount;
                    $vat_amount = $price - $extra_charge_sales_with_vat;
                } else {
                    $extra_charge_sales = $saleType->rta_charge + $poaCharge + $saleTypeTransportationCharge + $other_amount;
                    $vat_amount = $price - $extra_charge_sales;
                }

                //Profit Margin
                $profitMargin = \App\ProfitMargin::where('sales_type_id', $auction->sale_type_id)
                                                ->where('range_from', '<=', $price)
                                                ->where('range_to', '>=', $price)
                                                ->first();
                if(!empty($profitMargin)){
                    if($profitMargin->profit_status == 1){
                        $result['flatValue'] = $profitMargin->profit_amount;
                        $amount = $vat_amount - $profitMargin->profit_amount;
                        if($amount <= 0) {
                            $amount = 0;
                        }
                    }else{
                        $result['percentageValue'] = $profitMargin->profit_amount;
                        $pAmount = ($profitMargin->profit_amount / 100) * $price;
                        $amount = $vat_amount - $pAmount;
                        if($amount <= 0) {
                            $amount = 0;
                        }
                    }
                } else {
                    $profitMargin = \App\ProfitMargin::where('sales_type_id', $auction->sale_type_id)->orderBy('range_from', 'asc')->first();
                    if((!empty($profitMargin)) && ($profitMargin->range_from > $price)){
                            if($profitMargin->profit_status == 1){
                                $result['flatValue'] = $profitMargin->profit_amount;
                                $amount = $vat_amount - $profitMargin->profit_amount;
                                if($amount <= 0) {
                                    $amount = 0;
                                }
                            }else{
                                //$percentage = ($profitMargin->profit_amount*100)/$price;
                                $result['percentageValue'] = $profitMargin->profit_amount;
                                $pAmount = ($profitMargin->profit_amount / 100) * $price;
                                $amount = $vat_amount - $pAmount;
                                if($amount <= 0) {
                                    $amount = 0;
                                }
                            }
                            $result['status']  = 1;
                    } else {
                        $profitMargin = \App\ProfitMargin::where('sales_type_id', $auction->sale_type_id)->orderBy('range_to', 'desc')->first();
                        if($profitMargin->profit_status == 1){
                            $result['flatValue'] = $profitMargin->profit_amount;
                            $amount = $vat_amount - $profitMargin->profit_amount;
                            if($amount <= 0) {
                                $amount = 0;
                            }
                        }else{
                            //$percentage = ($profitMargin->profit_amount*100)/$price;
                            $result['percentageValue'] = $profitMargin->profit_amount;
                            $pAmount = ($profitMargin->profit_amount / 100) * $price;
                            $amount = $vat_amount - $pAmount;
                            if($amount <= 0) {
                                $amount = 0;
                            }
                        }
                        $result['status']  = 1;
                    }
                }
                $result['amount'] = $amount;
                return $result;
            } else {
                // dd($price);
                //Hybrid
                $result['sales_type_type'] = $saleType->sale_type;
                $extra_charge_sales = $saleType->rta_charge + $poaCharge + $saleTypeTransportationCharge + $other_amount;
                //Profit Margin
                $profitMargin = \App\ProfitMargin::where('sales_type_id', $auction->sale_type_id)
                                                ->where('range_from', '<=', $price)
                                                ->where('range_to', '>=', $price)
                                                ->first();
                if(!empty($profitMargin)){
                    if($profitMargin->profit_status == 1){
                        $result['flatValue'] = $profitMargin->profit_amount;
                        $pAmount = $profitMargin->profit_amount;
                        if(!empty($vat)) {
                            $result['vat'] = $vat;
                            $result['status']  = 1;
                            $vAmount = ($vat / 100) * $pAmount;
                            $net_margin_amount = $pAmount - $vAmount;
                        } else {
                            $net_margin_amount = $pAmount;
                        }
                        $amount = $price - ($extra_charge_sales + $net_margin_amount);
                        if($amount <= 0) {
                            $amount = 0;
                        }
                    }else{
                        $result['percentageValue'] = $profitMargin->profit_amount;
                        $pAmount = ($profitMargin->profit_amount / 100) * $price;
                        if(!empty($vat)) {
                            $result['vat'] = $vat;
                            $result['status']  = 1;
                            $vAmount = ($vat / 100) * $pAmount;
                            $net_margin_amount = $pAmount - $vAmount;
                        } else {
                            $net_margin_amount = $pAmount;
                        }
                        $amount = $price - ($extra_charge_sales + $net_margin_amount);
                        if($amount <= 0) {
                            $amount = 0;
                        }
                    }
                } else {
                    $profitMargin = \App\ProfitMargin::where('sales_type_id', $auction->sale_type_id)->orderBy('range_from', 'asc')->first();
                    if((!empty($profitMargin)) && ($profitMargin->range_from > $price)){
                            if($profitMargin->profit_status == 1){
                                $result['flatValue'] = $profitMargin->profit_amount;
                                $pAmount = $profitMargin->profit_amount;
                                if(!empty($vat)) {
                                    $result['vat'] = $vat;
                                    $result['status']  = 1;
                                    $vAmount = ($vat / 100) * $pAmount;
                                    $net_margin_amount = $pAmount - $vAmount;
                                } else {
                                    $net_margin_amount = $pAmount;
                                }
                                $amount = $price - ($extra_charge_sales + $net_margin_amount);
                                if($amount <= 0) {
                                    $amount = 0;
                                }
                            }else{
                                //$percentage = ($profitMargin->profit_amount*100)/$price;
                                $result['percentageValue'] = $profitMargin->profit_amount;
                                $pAmount = ($profitMargin->profit_amount / 100) * $price;
                                if(!empty($vat)) {
                                    $result['vat'] = $vat;
                                    $result['status']  = 1;
                                    $vAmount = ($vat / 100) * $pAmount;
                                    $net_margin_amount = $pAmount - $vAmount;
                                } else {
                                    $net_margin_amount = $pAmount;
                                }
                                $amount = $price - ($extra_charge_sales + $net_margin_amount);
                                if($amount <= 0) {
                                    $amount = 0;
                                }
                            }
                            $result['status']  = 1;
                    } else {
                        $profitMargin = \App\ProfitMargin::where('sales_type_id', $auction->sale_type_id)->orderBy('range_to', 'desc')->first();
                        if($profitMargin->profit_status == 1){
                            $result['flatValue'] = $profitMargin->profit_amount;
                            $pAmount = $profitMargin->profit_amount;
                            if(!empty($vat)) {
                                $result['vat'] = $vat;
                                $result['status']  = 1;
                                $vAmount = ($vat / 100) * $pAmount;
                                $net_margin_amount = $pAmount - $vAmount;
                            } else {
                                $net_margin_amount = $pAmount;
                            }
                            $amount = $price - ($extra_charge_sales + $net_margin_amount);
                            if($amount <= 0) {
                                $amount = 0;
                            }
                        }else{
                            $result['percentageValue'] = $profitMargin->profit_amount;
                            $pAmount = ($profitMargin->profit_amount / 100) * $price;
                            if(!empty($vat)) {
                                $result['vat'] = $vat;
                                $result['status']  = 1;
                                $vAmount = ($vat / 100) * $pAmount;
                                $net_margin_amount = $pAmount - $vAmount;
                            } else {
                                $net_margin_amount = $pAmount;
                            }
                            $amount = $price - ($extra_charge_sales + $net_margin_amount);
                            if($amount <= 0) {
                                $amount = 0;
                            }
                        }
                        $result['status']  = 1;
                    }
                }
            }
            $result['amount'] = $amount;
            return $result;
            /* Old Code
            $vat = !empty(\App\GlobalVat::where('slug','global-vat')->first()) ? \App\GlobalVat::where('slug','global-vat')->first()->vat : 0;
            if(!empty($vat)){
                $result['vat'] = $vat;
                $result['status']  = 1;
                $vAmount = ($vat / 100) * $price;
                $amount = $price - $vAmount;
              }else{
                $amount = $price;
              }

              //case 1 - in range
              $profitMargin = \App\ProfitMargin::where('sales_type_id', $auction->sale_type_id)
                                                 ->where('range_from', '<=', $price)
                                                 ->where('range_to', '>=', $price)
                                                 ->first();

              if(!empty($profitMargin)){
                 if($profitMargin->profit_status == 1){
                     $result['flatValue'] = $profitMargin->profit_amount;
                     $amount = $amount - $profitMargin->profit_amount;
                 }else{
                     //$percentage = ($profitMargin->profit_amount*100)/$price;
                     $result['percentageValue'] = $profitMargin->profit_amount;
                     $pAmount = ($profitMargin->profit_amount / 100) * $price;
                     $amount = $amount - $pAmount;
                 }
                 $result['status']  = 1;
                 $result['vat'] = $profitMargin->id;


              }else{

                      $profitMargin = \App\ProfitMargin::where('sales_type_id', $auction->sale_type_id)->orderBy('range_to', 'desc')->first();
                      if( (!empty($profitMargin)) && ($profitMargin->range_to < $price)){

                            if($profitMargin->profit_status == 1){
                                $result['flatValue'] = $profitMargin->profit_amount;
                                $amount = $amount - $profitMargin->profit_amount;
                            }else{
                                //$percentage = ($profitMargin->profit_amount*100)/$price;
                                $result['percentageValue'] = $profitMargin->profit_amount;
                                $pAmount = ($profitMargin->profit_amount / 100) * $price;
                                $amount = $amount - $pAmount;
                            }
                            $result['status']  = 1;
                      }
                      //$result['amount'] = $amount;

              }

              $result['amount'] = $amount;
              return $result;*/
        }else{
           return $result;
        }

    }

    public function sendAutomaticPush($auctionId, $price) {


        /* $automaticBids = AutomaticBid::where('auction_id', $auctionId)->count();


          if($automaticBids < 2){
          return;
          } */


        $bids = Bid::where('auction_id', $auctionId)->count();


        if ($bids < 2) {
            return;
        }

        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();



        $lastBid = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->limit(1)->first();

        $lastBidAmount = $lastBid->price;
        $bidOwnerId = $lastBid->trader_id;


        $secondLastBid = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->offset(1)->limit(1)->first();

        $secondLastBidAmount = $secondLastBid->price;


        $usersInRange = AutomaticBid::where('auction_id', $auctionId)
                ->where('amount', '<', $lastBidAmount)
                ->where('amount', '>=', $secondLastBidAmount)
                ->get();


        $auction = Auction::find($auctionId);


        $msg = 'Your maximum bid amount for ' . $auction->title . ' has been exceeded. Increase your offer now!';

        if (!empty($usersInRange)) {

            foreach ($usersInRange as $userInRange) {


                $traderId = $userInRange->trader_id;

                if ($bidOwnerId == $traderId) {
                    continue;
                }

                $trader = TraderUser::find($traderId);
                $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

                if (!empty($trader->device_type) && !empty($trader->device_id)) {

                    if ($trader->device_type == 'iOS') {
                        $devices['iosDevices'][] = $trader->device_id;
                    } elseif ($trader->device_type == 'Android') {
                        $devices['androidDevices'][] = $trader->device_id;
                    }
                }
            }

            $this->sendPushNotification($devices, $msg, $auctionId);
        }


        return;



        $maxData = AutomaticBid::where('auction_id', $auctionId)->where('amount', '<', $price)->limit(1)->orderBy('amount', 'desc')->first();

        if (!empty($maxData)) {

            $traderId = $maxData->trader_id;


            $lastBid = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->limit(1)->first();
            //$lastBid = Bid::where('auction_id', $auctionId)->where('price', $bidPrice)->first();

            if (!empty($lastBid)) {

                $bidOwnerId = $lastBid->trader_id;

                if ($bidOwnerId == $traderId) {
                    return;
                }
            }


            $auction = Auction::find($auctionId);

            //$previousBidOwner =
            //$msg = 'Your maximum bid amount for '.$auction->title.' have crossed and current bid amount is '.(int) $lastBid;

            $msg = 'Your maximum bid amount for ' . $auction->title . ' has been exceeded. Increase your offer now!';

            $devices['iosDevices'] = array();
            $devices['androidDevices'] = array();

            $trader = TraderUser::find($traderId);
            $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

            if (!empty($trader->device_type) && !empty($trader->device_id)) {

                if ($trader->device_type == 'iOS') {
                    $devices['iosDevices'][] = $trader->device_id;
                } elseif ($trader->device_type == 'Android') {
                    $devices['androidDevices'][] = $trader->device_id;
                }
            }

            $this->sendPushNotification($devices, $msg, $auctionId);
        }
    }

    public function sendAuctionStartPush($auctionId, $msg = '') {

        $auction = Auction::find($auctionId);
        // dd("auctionId",$auction);
        $objectId = $auction->object_id;
        $object = Object::where('id', $objectId)->first();
        //$msg = 'Auction started for '.$auction->title;

        $msg = $auction->title . ', How much would you pay for this?';


        $objectYear = ObjectAttributeValue::where('object_id', $objectId)->where('attribute_id', 14)->first()->attribute_value;

        // $objectMileage = ObjectAttributeValue::where('object_id', $objectId)->where('attribute_id', 10)->first()->attribute_value;

        $objectMileage = null;

        //$objectMake = ObjectAttributeValue::where('object_id', $objectId)->where('attribute_id', 4)->first()->attribute_value;
        $objectMake = Make::where('id', $object->make_id)->first()->name;



        //$traders = TraderUser::where('dealer_id', $auction->dealer_id)->get();
        $traders = TraderUser::all();
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();


        foreach ($traders as $trader) {
            $minMileage = '';
            $maxMileage = '';

            $minYear = '';
            $maxYear = '';
            $makes = array();

            $flag = false;

            $mileageFlag = false;
            $yearFlag = false;
            $makesFlag = false;


            /* if(empty($trader->device_type) && empty($trader->device_id)){
              continue;
              } */

            $prefData = NotificationPreference::where('trader_id', $trader->id)->first();
            if (!empty($prefData)) {


                $prefs = json_decode($prefData->options, true);

                //$minMileage =
                if (!empty($prefs['minMileage'])) {
                    $minMileage = $prefs['minMileage'];
                }

                if (!empty($prefs['maxMileage'])) {
                    $maxMileage = $prefs['maxMileage'];
                }

                if (!empty($prefs['maxYear'])) {
                    $maxYear = $prefs['maxYear'];
                }

                if (!empty($prefs['minYear'])) {
                    $minYear = $prefs['minYear'];
                }

                if (!empty($prefs['makes'])) {
                    foreach ($prefs['makes'] as $make) {
                        //if($make['is_selected']){
                        $makes[] = $make['attribute_value'];
                        //}
                    }
                }

                //



                if ((!empty($minMileage)) && (!empty($maxMileage))) {
                    if (($minMileage <= $objectMileage) && ($maxMileage >= $objectMileage)) {
                        $mileageFlag = true;
                    }
                }

                if ((!empty($minYear)) && (!empty($maxYear))) {
                    if (($minYear <= $objectYear) && ($maxYear >= $objectYear)) {
                        $yearFlag = true;
                    }
                }


                if (!empty($makes) && in_array($objectMake, $makes)) {
                    $makesFlag = true;
                }


                if (!empty($mileageFlag) && !empty($yearFlag) && !empty($makesFlag)) {
                    // insert message
                    $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

                    if (!empty($trader->device_type) && !empty($trader->device_id)) {


                        if ($trader->device_type == 'iOS') {
                            $devices['iosDevices'][] = $trader->device_id;
                        } elseif ($trader->device_type == 'Android') {
                            $devices['androidDevices'][] = $trader->device_id;
                        }
                    }
                }
            } else {


                $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

                if (!empty($trader->device_type) && !empty($trader->device_id)) {


                    if ($trader->device_type == 'iOS') {
                        $devices['iosDevices'][] = $trader->device_id;
                    } elseif ($trader->device_type == 'Android') {
                        $devices['androidDevices'][] = $trader->device_id;
                    }
                }
            }
        }

        //echo(json_encode($devices)); exit;


        $this->sendPushNotification($devices, $msg, $auctionId);

        return;
    }

    public function sendAuctionOwnerNegotiatedEndPush($auctionId){

        $auction = Auction::find($auctionId);

        $lastBid = Bid::where('auction_id', $auctionId)->max('price');

        //$msg = 'You won the auction of '.$auction->title.' for AED '.(int) $lastBid;

        $msg = $auction->title . ', Thank you for your offer of AED ' . (int) $lastBid . '. Please wait whilst we review your offer.';

        //Thank you for your offer of AEDX. Please wait whilst we review your offer.
        //echo $msg; exit;

        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        $bidOwnerId = $auction->bid_owner;

        $trader = TraderUser::find($auction->bid_owner);
        $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

        if (!empty($trader->device_type) && !empty($trader->device_id)) {

            if ($trader->device_type == 'iOS') {
                $devices['iosDevices'][] = $trader->device_id;
            } elseif ($trader->device_type == 'Android') {
                $devices['androidDevices'][] = $trader->device_id;
            }
        }

        //bid owner push
        $this->sendPushNotification($devices, $msg, $auctionId);

    }

    public function sendAuctionEndPush($auctionId) {

        $auction = Auction::find($auctionId);

        $lastBid = Bid::where('auction_id', $auctionId)->max('price');

        //$msg = 'You won the auction of '.$auction->title.' for AED '.(int) $lastBid;

        $msg = $auction->title . ', Thank you for your offer of AED ' . (int) $lastBid . '. Please wait whilst we review your offer.';

        //Thank you for your offer of AEDX. Please wait whilst we review your offer.
        //echo $msg; exit;

        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        $bidOwnerId = $auction->bid_owner;

        $trader = TraderUser::find($auction->bid_owner);
        $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

        if (!empty($trader->device_type) && !empty($trader->device_id)) {

            if ($trader->device_type == 'iOS') {
                $devices['iosDevices'][] = $trader->device_id;
            } elseif ($trader->device_type == 'Android') {
                $devices['androidDevices'][] = $trader->device_id;
            }
        }

        //bid owner push
        $this->sendPushNotification($devices, $msg, $auctionId);

        //bid users push
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        //$msg = 'Auction for '.$auction->title.' is completed and gone for AED '.(int) $lastBid;

        $msg = 'Auction for ' . $auction->title . ' has now ended.';

        $bidUsers = Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);
        if (!empty($bidUsers)) {
            foreach ($bidUsers as $bidUser) {

                if ($bidOwnerId != $bidUser->trader_id) {

                    $trader = TraderUser::find($bidUser->trader_id);

                    $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);


                    if (!empty($trader->device_type) && !empty($trader->device_id)) {

                        if ($trader->device_type == 'iOS') {
                            $devices['iosDevices'][] = $trader->device_id;
                        } elseif ($trader->device_type == 'Android') {
                            $devices['androidDevices'][] = $trader->device_id;
                        }
                    }
                }
            }
        }

        $this->sendPushNotification($devices, $msg, $auctionId);

        return;
    }

    public function sendAuctionCancelPush($auctionId) {

        $auction = Auction::find($auctionId);

        $lastBid = Bid::where('auction_id', $auctionId)->max('price');

        if (empty($lastBid)) {
            return;
        }


        //bid users push
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        //$msg = 'Auction for '.$auction->title.' is completed and gone for AED '.(int) $lastBid;

        $msg = 'Auction for ' . $auction->title . ' has been canceled.';

        $bidUsers = Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);
        if (!empty($bidUsers)) {
            foreach ($bidUsers as $bidUser) {


                $trader = TraderUser::find($bidUser->trader_id);

                $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);


                if (!empty($trader->device_type) && !empty($trader->device_id)) {

                    if ($trader->device_type == 'iOS') {
                        $devices['iosDevices'][] = $trader->device_id;
                    } elseif ($trader->device_type == 'Android') {
                        $devices['androidDevices'][] = $trader->device_id;
                    }
                }
            }
        }

        $this->sendPushNotification($devices, $msg, $auctionId);

        return;
    }


    public function sendSms($auctionId, $type) {
        return true;
         try {
              $auction = Auction::find($auctionId);
              $lastBid = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->first();
              $trader = TraderUser::where('id', $lastBid->trader_id)->first();
              $first = $trader->mobile[0];
              if($first == 0) {
                   $mobile = "971".ltrim ($trader->mobile,'0');
              } else {
                   $mobile = "971".$trader->mobile;
              }

              if ($type == 1) {
                   $msg = 'Congratulations. Your bid of AED ' . $lastBid->price . ' has been accepted for ' . $auction->title . '. Please await whilst your car clears Quality Control.';
              } elseif ($type == 2) {
                   $msg = $auction->title . ' has cleared Quality Control. Please make a payment of AED ' . $lastBid->price . ' within 24 hours quoting ' . $auction->title;
              } elseif ($type == 3) {
                   $msg = 'Your watch ' . $auction->title . ' has failed Quality Control';
              } elseif ($type == 4) {
                   // return true;
                   $msg = 'Your watch ' . $auction->title . ' has sold susessfully';
              } elseif ($type == 5) {
                   $msg = 'Thank You for your payment. ' . $auction->title . ' is ready for collection from the dealer between 9:00am and 5:00pm.';
              } elseif ($type == 6) {
                   $msg = $auction->title . ' has cleared Quality Control. Please make a payment of AED ' . $lastBid->price . ' within 24 hours quoting ' . $auction->title;
              } else {
              return;
              }

              if($msg) {
                   $sms = new SmsNotification();
                   $sms->trader_id = $trader->id;
                   $sms->mobile = $mobile;
                   $sms->message = $msg;


                   // $fields = array('user' => "mobiworld",'pass' => "mobi1234",'sid' => "102234",'mno' => $mobile,'text' => $msg,'type' => "1");
                   $fields = array('user' => env('SMS_USER'),'pass' => env('SMS_PASS'),'sid' =>env('SMS_SID'),'mno' => $mobile, 'text' => $msg, 'type' => env('SMS_TYPE'));

                   //Prepare parameter string
                   $url = env('SMS_URL')."?".http_build_query($fields);
                    //echo $url; die;

                   //prepare connection
                   $ch = curl_init($url);
                   curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
                   curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);

                   //reading response
                   $body = curl_exec($ch);

                   //finally close connection
                   curl_close($ch);
                   $sms->response = $body;
                   $sms->save();

              }
              return;

         } catch (\Exception $e) {
              Log::info('SMS Notification Errors: '.$e);
         }
    }

    public function sendBidOwnerSms($auctionId) {
      return;
         try {
              $auction = Auction::find($auctionId);
              $lastBid = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->first();
              $trader = TraderUser::where('id', $lastBid->trader_id)->first();
              $first = $trader->mobile[0];
              if(!empty($trader->mobile)){
                  if($first == 0) {
                       $mobile = "971".ltrim ($trader->mobile,'0');
                  } else {
                       $mobile = "971".$trader->mobile;
                  }
                  $msg = $auction->title . ', Thank you for your offer of AED ' . (int) $lastBid->price . '. Please wait whilst we review your offer.';
                  // $msg = 'Congratulations. Your bid of AED ' . $lastBid->price . ' has been accepted for ' . $auction->title . '. Please await whilst your car clears Quality Control.';

                  if($msg) {
                       $sms = new SmsNotification();
                       $sms->trader_id = $trader->id;
                       $sms->mobile = $mobile;
                       $sms->message = $msg;


                       // $fields = array('user' => "mobiworld",'pass' => "mobi1234",'sid' => "102234",'mno' => $mobile, 'text' => $msg, 'type' => "1");
                       $fields = array('user' => env('SMS_USER'),'pass' => env('SMS_PASS'),'sid' =>env('SMS_SID'),'mno' => $mobile, 'text' => $msg, 'type' => env('SMS_TYPE'));

                       //Prepare parameter string
                       $url = env('SMS_URL')."?".http_build_query($fields);
                       // echo $url; die;

                       //prepare connection
                       $ch = curl_init($url);
                       curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
                       curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);

                       //reading response
                       $body = curl_exec($ch);

                       //finally close connection
                       curl_close($ch);
                       $sms->response = $body;
                       $sms->save();

                  }

              }


              return;

         } catch (\Exception $e) {
              Log::info('SMS Notification Errors: '.$e);
         }
    }

    public function sendBulkSms($auctionId) {
      return;
         try {
              $auction = Auction::find($auctionId);
              $lastBid = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->first();
              $bidUsers = Bid::where('id', '!=', $lastBid->id)->where('auction_id', $auctionId)->distinct()->get(['trader_id']);

              $mobileArray = array();

              foreach ($bidUsers as $key => $bidUser) {
                   $trader = TraderUser::where('id', $bidUser->trader_id)->first();
                   $first = $trader->mobile[0];

                   if(!empty($trader->mobile)){
                       if($first == 0) {
                            $mobile = "971".ltrim ($trader->mobile,'0');
                       } else {
                            $mobile = "971".$trader->mobile;
                       }

                       $msg = 'Auction for ' . $auction->title . ' has now ended.';

                       if($msg) {
                            $sms = new SmsNotification();
                            $sms->trader_id = $trader->id;
                            $sms->mobile = $mobile;
                            $sms->message = $msg;
                            $sms->save();
                       }

                       $mobileArray[] = $mobile;
                 }

             }


            if(!empty($mobileArray)){

                $comma_separated = implode(",", $mobileArray);

                 //send SMS
                 // $fields = array('user' => "mobiworld",'pass' => "mobi1234",'sid' => "102234",'mno' => $mobile, 'text' => $msg, 'type' => "1");
                 $fields = array('user' => env('SMS_USER'),'pass' => env('SMS_PASS'),'sid' =>env('SMS_SID'),'mno' => $comma_separated, 'text' => $msg, 'type' => env('SMS_TYPE'));

                 //Prepare parameter string
                 $url = env('SMS_URL')."?".http_build_query($fields);

                 //prepare connection
                 $ch = curl_init($url);
                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
                 curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);

                 //reading response
                 $body = curl_exec($ch);

                 //finally close connection
                 curl_close($ch);
            }

            return;

         } catch (\Exception $e) {
              Log::info('SMS Notification Errors: '.$e);
         }
    }

    public function sendStatusPush($auctionId, $type) {


        $auction = Auction::find($auctionId);

        $lastBid = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->first();

        if ($type == 1) {
            //$msg = 'Your vehicle '.$auction->title.' has moved to security check ';
            $msg = 'Congratulations. Your bid of AED ' . $lastBid->price . ' has been accepted for ' . $auction->title . '. Please await whilst your car clears Quality Control.';
        } elseif ($type == 2) {
            //$msg = 'Your vehicle '.$auction->title.' has passed security check ';

            $msg = $auction->title . ' has cleared Quality Control. Please make a payment of AED ' . $lastBid->price . ' within 24 hours quoting ' . $auction->title;
        } elseif ($type == 3) {
            $msg = 'Your watch ' . $auction->title . ' has failed Quality Control';
        } elseif ($type == 4) {
            return true;
            $msg = 'Your watch ' . $auction->title . ' has sold susessfully';
        } elseif ($type == 5) {
            //$msg = 'Your vehicle '.$auction->title.' has cashed susessfully';

            $msg = 'Thank You for your payment. ' . $auction->title . ' is ready for collection from the dealer between 9:00am and 5:00pm.';
        } elseif ($type == 6) {
            //$msg = 'Your vehicle '.$auction->title.' has cashed susessfully';

            $msg = $auction->title . ' has cleared Quality Control. Please make a payment of AED ' . $lastBid->price . ' within 24 hours quoting ' . $auction->title;
        } else {
            return;
        }

        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        $bidOwnerId = $auction->bid_owner;

        $trader = TraderUser::find($auction->bid_owner);
        $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

        if (!empty($trader->device_type) && !empty($trader->device_id)) {

            if ($trader->device_type == 'iOS') {
                $devices['iosDevices'][] = $trader->device_id;
            } elseif ($trader->device_type == 'Android') {
                $devices['androidDevices'][] = $trader->device_id;
            }
        }

        //bid owner push
        $this->sendPushNotification($devices, $msg, $auctionId);

        return;
    }

    public function sendAuctionOwnerNegotiatedPush($auctionId) {

        $auction = Auction::find($auctionId);

        $lastBid = Bid::where('auction_id', $auctionId)->max('price');

        $msg = $auction->title . ' request you to bid for AED ' . (int) $auction->final_req_amount;
        //echo $msg; exit;
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        $bidOwnerId = $auction->bid_owner;

        $trader = TraderUser::find($auction->bid_owner);
        $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

        if (!empty($trader->device_type) && !empty($trader->device_id)) {

            if ($trader->device_type == 'iOS') {
                $devices['iosDevices'][] = $trader->device_id;
            } elseif ($trader->device_type == 'Android') {
                $devices['androidDevices'][] = $trader->device_id;
            }
        }

        if(empty($devices['iosDevices'])){
          return;
        }

        if(empty($devices['androidDevices'])){
          return;
        }

        //bid owner push
        $this->sendPushNotification($devices, $msg, $auctionId);

        return;
    }

    //sendAuctionInspectorNegotiatedPush
    public function sendAuctionInspectorNegotiatedPush($auctionId) {

        $auction = Auction::find($auctionId);

        $msg = $auction->title . ' - have one negotation request';
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();


        $inspectorNegotoated = InspectorNegaotiate::where('auction_id', $auctionId)->orderBy('id', 'desc')->first();
        $inspector = InspectorUser::find($inspectorNegotoated->inspector_id);

        if (!empty($inspector->device_type) && !empty($inspector->device_id)) {

            if ($inspector->device_type == 'iOS') {
                $devices['iosDevices'][] = $inspector->device_id;
            }
        }

        if(empty($devices['iosDevices'])){
          return;
        }

        //bid owner push
        $this->sendPushNotification($devices, $msg, $auctionId, true);

        return;
    }


    public function sendAuctionNegotiatedPush($auctionId) {

        $auction = Auction::find($auctionId);

        $lastBid = Bid::where('auction_id', $auctionId)->max('price');

        $msg = $auction->title . ' bid negotiated and started again with AED ' . (int) $lastBid;
        //echo $msg; exit;


        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        $bidUsers = Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);
        if (!empty($bidUsers)) {
            foreach ($bidUsers as $bidUser) {

                $trader = TraderUser::find($bidUser->trader_id);

                $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);


                if (!empty($trader->device_type) && !empty($trader->device_id)) {

                    if ($trader->device_type == 'iOS') {
                        $devices['iosDevices'][] = $trader->device_id;
                    } elseif ($trader->device_type == 'Android') {
                        $devices['androidDevices'][] = $trader->device_id;
                    }
                }
            }
        }

        $this->sendPushNotification($devices, $msg, $auctionId);

        return;
    }

    public function sendBidPush($auctionId, $bidPrice = '') {



        $auction = Auction::find($auctionId);
        //$msg = $auction->title.' bid price updated to AED '. (int) $bidPrice;
        
        $lastBid = Bid::where('auction_id', $auctionId)
        // ->where('price', $bidPrice)
        ->first();
       

        $msg = $auction->title . ', You have bid AED ' . (int) $bidPrice;


        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        $bidOwnerId = $lastBid->trader_id;

        /*
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
          $this->sendPushNotification($devices, $msg); */

        //previous bid owner




        $msg = $auction->title . ', You have been Outbid! Increase your offer now!';

        $traders = TraderUser::all();
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();


        $count = Bid::where('auction_id', '=', $auctionId)->count();

        if (empty($count)) {
            return;
        }

        if ($count > 1) {

            $bidUsers = Bid::where('auction_id', $auctionId)->orderBy('price', 'desc')->offset(1)->limit(1)->get();

            if (!empty($bidUsers[0]->trader_id)) {
                $previousBidOwner = $bidUsers[0]->trader_id;

                if ($bidOwnerId != $previousBidOwner) {
                    $trader = TraderUser::find($previousBidOwner);

                    $this->savePushMessage($auctionId, $trader->id, $auction->title, $msg);

                    if (!empty($trader->device_type) && !empty($trader->device_id)) {

                        if ($trader->device_type == 'iOS') {
                            $devices['iosDevices'][] = $trader->device_id;
                        } elseif ($trader->device_type == 'Android') {
                            $devices['androidDevices'][] = $trader->device_id;
                        }
                    }
                }
            }
        }
        /* var_dump($bidUsers); exit;


          $bidUsers = Bid::where('auction_id', $auctionId)->distinct()->get(['trader_id']);


          if(!empty($bidUsers)){
          foreach($bidUsers as $bidUser){

          if($bidOwnerId != $bidUser->trader_id){

          echo $bidUser->trader_id; exit;

          $trader = TraderUser::find($bidUser->trader_id);

          $this->savePushMessage($auctionId, $trader->id, $msg, $msg);


          if(!empty($trader->device_type) && !empty($trader->device_id)){

          if($trader->device_type == 'iOS'){
          $devices['iosDevices'][] = $trader->device_id;
          }elseif($trader->device_type == 'Android'){
          $devices['androidDevices'][] = $trader->device_id;
          }
          }


          //return;
          }
          }
          } */

        $this->sendPushNotification($devices, $msg, $auctionId);

        return;
    }

    public function savePushMessage($auctionId, $traderId, $msg, $desc = '') {

        $notification = new Notifications();
        $notification->title = $msg;
        $notification->desc = $desc;

        $notification->auction_id = $auctionId;
        $notification->trader_id = $traderId;
        $notification->save();

        return;
    }

    public function sendPushNotification($devices, $message, $auctionId, $inspectorPush = false) {

        /* if (!empty($devices['iosDevices'])) {

          $this->sendNotificationToIos($devices['iosDevices'], $message, $auctionId);
          }

          if (!empty($devices['androidDevices'])) {
          $this->sendNotificationToAndroid($devices['androidDevices'], $message, $auctionId);
          }
          return; */
        $identity = array_merge($devices['iosDevices'], $devices['androidDevices']);
        $identities = array_chunk($identity, 20, true);

        if (!empty($identity)) {
             $client = new Client(config('services.twilio.accountSid'), config('services.twilio.authToken'));

             if($inspectorPush){
                  $serviceId =  config('services.twilio.inpectorServiceSid');
             }else{
                  $serviceId =  config('services.twilio.serviceSid');
             }

		   $message = trim(preg_replace('/\s+/', ' ', $message));

            // Create a notification
            try {
                foreach ($identities as $identity) {
                    $notification = $client
                            ->notify->services($serviceId)
                            ->notifications->create([
                        "identity" => $identity,
                        "body" => $message,
                        "sound" => 'default',
                        "data" => '{"message":"' . $message . '","auctionId":"' . $auctionId . '"}',
                        //"fcm" => '{"notification":{"body":"' . $message . '"}}',
                        //"fcm" => '{"data":{"FRAGMENT_ID":"9","FRAGMENT_HEADER_NAME":"Notifications"},"notification":{"body":"' . $message . '","click_action":".activity.MenuActivity"}}',
                        "apn" => '{"aps":{"alert":{"body":"' . $message . '"}}}'
                    ]);
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        return;
    }

    public function sendGeneralPushNotification($devices, $message) {

        $identity = array_merge($devices['iosDevices'], $devices['androidDevices']);
        $identity = array_unique($identity);

        if (!empty($identity)) {
             $client = new Client(config('services.twilio.accountSid'), config('services.twilio.authToken'));

            //  if($inspectorPush){
            //       $serviceId =  config('services.twilio.inpectorServiceSid');
            //  }else{
            //       $serviceId =  config('services.twilio.serviceSid');
            //  }
            $serviceId =  config('services.twilio.serviceSid');

		    $message = trim(preg_replace('/\s+/', ' ', $message));

            // Create a notification
            try {
                // foreach ($identities as $identity) {
                    $notification = $client
                            ->notify->services($serviceId)
                            ->notifications->create([
                        "identity" => $identity,
                        "body" => $message,
                        "sound" => 'default',
                        "data" => '{"message":"' . $message . '"}',
                        "apn" => '{"aps":{"alert":{"body":"' . $message . '"}}}'
                    ]);
                // }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        return;
    }

    public function sendNotificationToAndroid($deviceIds, $message, $auctionId) {
        if (empty($deviceIds)) {
            return;
        }

        try {
            $push = new PushNotification('gcm');
            $push->setMessage([
                        'notificationdata' => [
                            //'title'=>$message,
                            'sound' => 'default'
                        ],
                        'data' => [
                            'message' => $message,
                            'auctionId' => $auctionId
                        ]
                    ])
                    ->setDevicesToken($deviceIds)
                    ->send();

            //var_dump($push->getFeedback()); exit;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        /*
          if(!empty($deviceIds)){
          foreach($deviceIds as $token){
          $deviceArray[] = PushNotification::Device($token);
          }
          }

          // Populate the device collection
          $devices = PushNotification::DeviceCollection($deviceArray);

          $message = PushNotification::Message($message, array(
          'badge'=> 1,
          'custom' => array('auctionId' => $auctionId
          )
          ));

          try {
          // Send the notification to all devices in the collect
          $collection = PushNotification::app('appNameAndroid')
          ->to($devices)
          ->send($message);
          }   catch(Exception $e) {
          Log::error($e->getMessage());
          }
         */
    }

    public function sendNotificationToIos($deviceIds, $message, $auctionId) {

        if (empty($deviceIds)) {
            return;
        }

        $push = new PushNotification('apn');

        try {
            $push->setMessage([
                        'aps' => [
                            'alert' => [
                                //'title' => 'Wecash',
                                'body' => $message
                            ],
                            'sound' => 'default'
                        ],
                        'extraPayLoad' => [
                            'auctionId' => $auctionId,
                        ]
                    ])
                    ->setDevicesToken($deviceIds)->send();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        /*

          if(!empty($deviceIds)){
          foreach($deviceIds as $token){
          $deviceArray[] = PushNotification::Device($token);
          }
          }

          // Populate the device collection
          $devices = PushNotification::DeviceCollection($deviceArray);

          $message = PushNotification::Message($message, array(
          'badge'=> 0,
          'custom' => array('auctionId' => $auctionId)
          ));

          try {
          // Send the notification to all devices in the collect
          $collection = PushNotification::app('appNameIOSProd')
          ->to($devices)
          ->send($message);

          }   catch(Exception $e) {
          Log::error($e->getMessage());
          } */
    }

    public function sendNotificationToDevice(Request $request) {

       
        $tId = $request->tId;
        $type = $request->type;

        if($type == 'ios'){
        $identityVar = 'TraderIOS'.$tId;
        }

        if($type == 'android'){
        $identityVar = 'TraderAndroid'.$tId;
        }
        //$identity = array_merge($devices['iosDevices'], $devices['androidDevices']);
        // $identity = ['TraderIOS26', 'TraderIOS50', 'TraderAndroid50'];
        $identity = [$identityVar];
// dd($identity);
        if (!empty($identity)) {
            $client = new Client(config('services.twilio.accountSid'), config('services.twilio.authToken'));


			///$myMsg = "2011 . Nissan Tiida . Sedan . GCC . 65229km	";

			$myMsg = "2011 . Nissan Tiida . Sedan . GCC . 65229km	?";

			$myMsg = trim(preg_replace('/\s+/', ' ', $myMsg));

            // Create a notification
            try {
                $notification = $client
                        ->notify->services(config('services.twilio.serviceSid'))
                        ->notifications->create([
                    "identity" => $identity,
                    "body" => 'Hi, this is test message body',
                    "data" => '{"message":"'.$myMsg.'","auctionId":"33"}',
                    //"fcm" => '{"notification":{"body":"Hi, this is test message body"}}',
                    "fcm" => '{"data":{"FRAGMENT_ID":"9","FRAGMENT_HEADER_NAME":"Notifications"},"notification":{"body":"Hi, this is test message body","click_action":".activity.MenuActivity"}}',
                    "apn" => '{"aps":{"alert":{"body":"Hi, this is test message body"}}}'
                ]);

                echo 'message send';
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        return;


        /* $this->sendAuctionStartPush(96);
          echo 'start...!!';
          exit;

          echo Carbon::now()->addSeconds(20);

          echo '<br>';

          echo Carbon::now(); exit;


          $this->sendBidPush(48, 8600);
          echo 'pushed...!!';
          exit;

          $deviceIds[] = '9ef20837a8d1776a15bbf8ec5de840ba174f2c52d7c7ca3c565203a273a5b2bf';
          $deviceIds[] ='9ef20837a8d1776a15bbf8ec5de840ba174f2c52d7c7ca3c565203a273a5b2be';


          $this->sendNotificationToIos($deviceIds, 'Hello abhi', 34); exit; */

        // $deviceIds[] = 'cjb6eqM1W2I:APA91bE3ltGahsyOIb0tZwQDEND7wkNKANSp7MkuIz-WT1srHh0O-6GHk-2HrtRr2wE7wjFhFbVf7H2wbzjCVjQkEoEPenpn8WMRaTRU76Uvx0SX2lVWZJsPlpaneLRMQFz8qfp4HKuo';
        //$deviceIds[] ='f4rKx9780uM:APA91bEQdO0USp53AqVaPh_PuH7JzGjy8u5KKdH4lxK5aACYYpjWCW9kYTJ6PHl1Nj4u1fTT2ZZFUUbjlCyOEmY4LJcqqBKQRorgP7-JxXIqRnPTwB5gQmYpLpqGTlE5gFZXEFV4qnkf';
        //var_dump($deviceIds); exit;

        /* $this->sendNotificationToAndroid($deviceIds, 'Hello dgfdgdgd', 34);

          exit;

          $deviceArray[] = '9ef20837a8d1776a15bbf8ec5de840ba174f2c52d7c7ca3c565203a273a5b2be';


          $devices = PushNotification::DeviceCollection($deviceArray);

          var_dump($devices);
          exit;


          $message = PushNotification::Message('Greetings', array(
          'badge' => 0,
          'custom' => array('auctionId' => 23)
          ));

          $collection = PushNotification::app('appNameIOSProd')
          ->to($devices)
          ->send($message);


          exit;


          $deviceToken = '9ef20837a8d1776a15bbf8ec5de840ba174f2c52d7c7ca3c565203a273a5b2be';

          $message = 'Hello, greetings from we cash!';

          // Send the notification to the device with a token of $deviceToken
          $collection = PushNotification::app('appNameIOSProd')
          ->to($deviceToken)
          ->send($message); */
    }

    public function generateSessionId() {
        return uniqid() . time();
    }

    public function generatePdf($id){
        // dd(encrypt(963258));
        $decrypt_id = decrypt($id);
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 60000); //60000 seconds = 5 minutes
        ini_set('max_input_time ', 60000); //60000 seconds = 5 minutes
        // $fileName = 'vehicles_'.time();
        $data=array();

        $object = \App\Object::leftjoin('models', 'models.id', '=', 'objects.model_id')
                                 ->leftjoin('makes', 'makes.id', '=', 'objects.make_id')
                                 ->leftjoin('inspector_users', 'inspector_users.id', '=', 'objects.inspector_id')
                                 ->select('objects.name', 'objects.variation', 'objects.vin', 'objects.vehicle_registration_number', 'objects.customer_name', 'objects.customer_mobile', 'objects.customer_mobile',
                                  'objects.customer_email', 'objects.customer_reference', 'objects.source_of_enquiry', 'objects.created_at', 'objects.inspector_id', 'objects.dealer_id', 'objects.bank_id')
                                 ->where('objects.id', $decrypt_id)->first();

        if(!empty($object)) {
            $attributeSet = \App\AttributeSet::orderBy('sort','asc')->get();

            $obj = \App\Object::find($decrypt_id);
            $make = \App\Make::where('id', $obj->make_id)->first()->name;
            $model = \App\Models::where('id', $obj->model_id)->first()->name;


            foreach ($obj->ObjectAttributeValue as  $value) {
                $data[$value->attribute->attributeSet->slug][]=$value;
            }
            return view('admin.modules.object.list_pdf_inspector', compact('object','attributesId','objectAttributeValue','attributeSet','data', 'make', 'model'));
        }
   }
}
