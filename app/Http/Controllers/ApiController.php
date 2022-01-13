<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\InspectorUser;
use App\TraderUser;
use App\Object;
use App\ObjectAttributeValue;
use Validator;
use App\AttributeSet;
use App\Attribute;
use App\AutomaticBid;
use App\Auction;
use App\Version;
use App\Make;
use App\Models;
use Illuminate\Support\Facades\Log;
use Auth;
use App;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['web_api']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return Auth::guard('api')->user();
    }
    public function indexTrader(){
        return Auth::guard('trader_api')->user();
    }

    public function generateSessionId() {
        return uniqid() . time();
    }

    public function inspectorLogin(Request $request){
        $validator = Validator::make($request->all(),array('email' => 'required|email','password' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => trans('api.error_required_fields')]);
        }
        $input = $request->json()->all();
        $auth = Auth::guard('inspector')->attempt(array("email" => $input['email'],"password"=>$input['password']));
        if($auth){
            $sessionId = $this->generateSessionId();
            $inspector = Auth::guard('inspector')->user();
            $inspector->session_id =$sessionId;
            $inspector->save();

            return $this->successResponse(trans('api.success_login'), InspectorUser::where('email',$input['email'])->first());
        } else {
            return $this->errorResponse(trans('api.not_found'));
        }
    }

    public function traderLogin(Request $request){
        $validator = Validator::make($request->all(),array('email' => 'required|email','password' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => trans('api.error_required_fields')]);
        }
        $input = $request->json()->all();
        $auth = Auth::guard('trader')->attempt(array("email" => $input['email'],"password"=>$input['password']));
        if($auth){
                  if(Auth::guard('trader')->user()->status != 1){
                       Auth::guard('trader')->logout();
                       return $this->errorResponse(trans('api.account_blocked'));
                  }
                  if(Auth::guard('trader')->user()->is_verify_email != 1){
                      Auth::guard('trader')->logout();
                      return $this->errorResponse(trans('api.not_verify_email'));
                  }
			//update device token
			//if(!empty($request->deviceType) && !empty($request->deviceId)){

			    $deviceId = !empty($request->deviceId) ? $request->deviceId : '';
				//$deviceType = !empty($request->deviceType) ? $request->deviceType : '';
			    //$deviceIdActual =  !empty($request->deviceIdActual) ? $request->deviceIdActual : '';
                   $sessionId = $this->generateSessionId();

				$userid = Auth::guard('trader')->user()->id;

				TraderUser::where('id', $userid)
							->update(['device_id_actual' => $deviceId, 'session_id' => $sessionId]);
			//}

            return $this->successResponse(trans('api.success_login'), TraderUser::where('email',$input['email'])->first());
        } else {
            return $this->errorResponse(trans('api.not_found'));
        }
    }

    public function objectRestrictUpdateStatus($objectId=''){
         if(!empty($objectId)){
              $vehicleInAuction = Auction::where('object_id', $objectId)->first();
              // $statusArray = array(3, 4, 5,6,7,8,9,10,11,12);
              $statusArray = array(5,6,7,8,11);
              //if ( (!empty($vehicleInAuction)) && (in_array($vehicleInAuction->getOriginal('status'), $statusArray))) {
              if ( !empty($vehicleInAuction)) {
                   return true;
              }
        }
        return false;
    }


	 public function versionCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'os' => 'required',
					'version' => 'required'
        ]);

		if ($validator->fails()) {
            $status = trans('api.invalid_data');
            return $this->errorResponse($status);
        }



		$data = array(
					'hasUpdate' => false,
          'isHasUpdate' => false,
					'url' => ''
				);


		if($request->os == 'iOS'){
			$ios = Version::where('type', '=', 'ios')->first();

			if(($ios->status) && ($ios->version_number > $request->version)){

				$data = array(
					'hasUpdate' => true,
					'url' => $ios->url,
					'message' => $ios->message
				);

			}

		}elseif($request->os == 'Android'){
			$android = Version::where('type', '=', 'android')->first();
			if(($android->status) && ($android->version_number > $request->version)){

				$data = array(
					'hasUpdate' => true,
          'isHasUpdate' => true,
					'url' => $android->url,
					'message' => $android->message
				);

			}

		}elseif($request->os == 'ipad'){
			$ipad = Version::where('type', '=', 'ipad')->first();
			if(($ipad->status) && ($ipad->version_number > $request->version)){

				$data = array(
					'hasUpdate' => true,
					'url' => $ipad->url,
					'message' => $ipad->message
				);

			}

		}else{
			$status = trans('api.invalid_data');
            return $this->errorResponse($status);
		}

		//trans('api.mail_success')


		return $this->successResponse('', $data);

    }



	public function getObj(){



		return $this->getobjectData(56);


	}



	public function getobjectData($id, $traderId=''){

	 $objects =   Object::where('id', $id)->with('values','images','attachments')->first();
     //->toArray();
//dd($objects->name,empty($objects),$objects == null,$objects);

     if(empty($objects))
     {
        return $this->errorResponse(trans('api.not_found'));
    }else{
    //  echo '<pre>';print_r($results);die;
    $results = $objects;
	 //return $results;

	 $data = array();

	 $data['name'] = $results->name;

	 $data['code'] = $results->code;

	 $data['inspector_id'] = $results->inspector_id;

	 $data['dealer_id'] = $results->dealer_id;

	 $data['images'] = $results->images;
	 $data['attachments'] = $results->attachments;

	 $maxamount='';

	 if(!empty($traderId)){

	 	$auction = Auction::where('object_id', $id)->first();

	 	$automaticTrader = AutomaticBid::where('auction_id', '=', $auction['id'])
									->where('trader_id', '=', $traderId)
									->first();

		if(!empty($automaticTrader)){
			$maxamount = $automaticTrader->amount;
		}


	 }

	 $data['amount'] = (int) $maxamount;




		//
		$data['atrributes'][0]['catId'] = '1';
		$data['atrributes'][0]['catName'] = 'Watch Details';
		$data['atrributes'][0]['catSlug'] = 'test';

	 	$data['atrributes'][0]['attribute_id'] = $results->make_id;

		$data['atrributes'][0]['attribute_name'] = 'Make';
		$data['atrributes'][0]['attribute_type'] = 'select';


		$data['atrributes'][0]['attribute_value'] = Make::where('id', $results->make_id)->first()->name;

		$data['atrributes'][0]['quality_level'] = "No Color";
		$data['atrributes'][0]['color'] = "No Color";

		$data['atrributes'][0]['additional_text'] = "";
		$data['atrributes'][0]['color'] =  "No Color";




		$data['atrributes'][1]['catId'] = '1';
		$data['atrributes'][1]['catName'] = 'Watch Details';
		$data['atrributes'][1]['catSlug'] = 'test';

	 	$data['atrributes'][1]['attribute_id'] =  $results->model_id;

		$data['atrributes'][1]['attribute_name'] = 'Model';
		$data['atrributes'][1]['attribute_type'] = 'select';


		$data['atrributes'][1]['attribute_value'] = Models::where('id', $results->model_id)->first()->name;

		$data['atrributes'][1]['quality_level'] = "No Color";
		$data['atrributes'][1]['color'] = "No Color";

		$data['atrributes'][1]['additional_text'] = "";
		$data['atrributes'][1]['color'] =  "No Color";





	 $i=2;

	 foreach($results['values'] as $result){

           $attributeCheck = Attribute::where('id', $result['attribute_id'])->first();

        //    if($attributeCheck->invisible_to_trader != 1 ){
                //$result['attribute_id']

		//$category = Attribute::where('id', $id)->with('attribute')->get();
		//echo $result['attribute_id']; exit;

        $category = AttributeSet::join('attributes', 'attributes.attribute_set_id', '=', 'attribute_sets.id')
        ->select(['attribute_sets.id',
            'attribute_sets.name',
                  'attribute_sets.slug'

        ])
        ->where('attributes.id', '=', $result['attribute_id'])
        ->first();



		if(!empty($category)){

			//var_dump($category->id); exit;


			$data['atrributes'][$i]['catId'] = $category->id;
			$data['atrributes'][$i]['catName'] = $category->name;
			$data['atrributes'][$i]['catSlug'] = $category->slug;
		}

		$data['atrributes'][$i]['attribute_id'] = $result['attribute_id'];

		$attribute = Attribute::where('id', '=', $result['attribute_id'])
									->first();

// dd($attribute->name);
		$data['atrributes'][$i]['attribute_name'] = $attribute['name'];
		$data['atrributes'][$i]['attribute_type'] = $attribute['input_type'];


		$data['atrributes'][$i]['attribute_value'] = $result['attribute_value'];

		$data['atrributes'][$i]['quality_level'] = $result['quality_level'];
		$data['atrributes'][$i]['color'] = $result['color'];

		$data['atrributes'][$i]['additional_text'] = $result['additional_text'];
		$data['atrributes'][$i]['color'] = $result['color'];

		$i++;
    //  }

	 }

	  return $data;
    }

	}

     public function getobjectDataInspector($id, $vinNumber=''){

          if(!empty($id)){
               $objects =   Object::where('id', $id)->with('values','images', 'make', 'model', 'bank')->orderBy('id','desc')->first();
          }else{
               $objects =   Object::where('vin', $vinNumber)->with('values','images', 'make', 'model', 'bank')->orderBy('id','desc')->first();
          }

          // return $objects;
          $results = $objects;

          //return $results;

          $data = array();

          if(empty($results)){
               return $data;
          }

          $data['objectId'] = $results->id;

          $vehicleInAuction = Auction::where('object_id', $results->id)->first();

          $data['vehicleAuctionStatus'] = $vehicleInAuction ? 1 : 0;

          $data['name'] = $results->name;

          $data['code'] = $results->code;

          $data['restrictStatus'] = $this->objectRestrictUpdateStatus($results->id);

          $data['inspector_id'] = $results->inspector_id;

          $data['nationality_id'] = $results->nationality_id ? $results->nationality_id : null;

          $data['dealer_id'] = $results->dealer_id;

          $data['images'] = $results->images;
          $data['variation'] = $results->variation;
          $data['suggested_amount'] = $results->suggested_amount;
          $data['vin'] = $results->vin;
          $data['vehicle_registration_number'] = $results->vehicle_registration_number;
          $data['customer_name'] = $results->customer_name;
          $data['customer_mobile'] = $results->customer_mobile;
          $data['customer_email'] = $results->customer_email;
          $data['customer_reference'] = $results->customer_reference;
          $data['source_of_enquiry'] = $results->source_of_enquiry;

          $data['make'][0]['id'] = $results->make ? $results->make->id : null;
          $data['make'][0]['name'] = $results->make ? $results->make->name : null;

          $data['model'][0]['id'] = $results->model ? $results->model->id : null;
          $data['model'][0]['name'] = $results->model ? $results->model->name : null;

          $data['bank'][0]['id'] = $results->bank ? $results->bank->id : null;
          $data['bank'][0]['name'] = $results->bank ? $results->bank->name : null;




          //
          $data['atrributes'][0]['catId'] = '1';
          $data['atrributes'][0]['catName'] = 'Car Details';
          $data['atrributes'][0]['catSlug'] = 'car-details';

          $data['atrributes'][0]['attribute_id'] = 4;

          $data['atrributes'][0]['attribute_name'] = 'Make';
          $data['atrributes'][0]['attribute_type'] = 'select';


          $data['atrributes'][0]['attribute_value'] = Make::where('id', $results->make_id)->first()->name;

          $data['atrributes'][0]['quality_level'] = "No Color";
          $data['atrributes'][0]['color'] = "No Color";

          $data['atrributes'][0]['additional_text'] = "";
          $data['atrributes'][0]['color'] =  "No Color";




          $data['atrributes'][1]['catId'] = '1';
          $data['atrributes'][1]['catName'] = 'Car Details';
          $data['atrributes'][1]['catSlug'] = 'car-details';

          $data['atrributes'][1]['attribute_id'] = 5;

          $data['atrributes'][1]['attribute_name'] = 'Model';
          $data['atrributes'][1]['attribute_type'] = 'select';


          $data['atrributes'][1]['attribute_value'] = Models::where('id', $results->model_id)->first()->name;

          $data['atrributes'][1]['quality_level'] = "No Color";
          $data['atrributes'][1]['color'] = "No Color";

          $data['atrributes'][1]['additional_text'] = "";
          $data['atrributes'][1]['color'] =  "No Color";


          $i=2;

          foreach($results['values'] as $result){

               //$category = Attribute::where('id', $id)->with('attribute')->get();
               //echo $result['attribute_id']; exit;

               $category = AttributeSet::join('attributes', 'attributes.attribute_set_id', '=', 'attribute_sets.id')
               ->select(['attribute_sets.id',
               'attribute_sets.name',
               'attribute_sets.slug'

               ])
               ->where('attributes.id', '=', $result['attribute_id'])
               ->first();



               if(!empty($category)){

                    //var_dump($category->id); exit;


                    $data['atrributes'][$i]['catId'] = $category->id;
                    $data['atrributes'][$i]['catName'] = $category->name;
                    $data['atrributes'][$i]['catSlug'] = $category->slug;
               }

               $data['atrributes'][$i]['attribute_id'] = $result['attribute_id'];

               $attribute = Attribute::where('id', '=', $result['attribute_id'])
               ->first();


               $data['atrributes'][$i]['attribute_name'] = $attribute->name;
               $data['atrributes'][$i]['attribute_type'] = $attribute->input_type;


               $data['atrributes'][$i]['attribute_value'] = $result['attribute_value'];

               $data['atrributes'][$i]['quality_level'] = $result['quality_level'];
               $data['atrributes'][$i]['color'] = $result['color'];

               $data['atrributes'][$i]['additional_text'] = $result['additional_text'];
               $data['atrributes'][$i]['color'] = $result['color'];

               $i++;

          }

          return $data;

	}


    public function getAttributeSetList() {

        $attributeSet = AttributeSet::all();

		return $attributeSet;
        if(count($attributeSet)){
            return $this->successResponse(trans('api.attributeset_success'),$attributeSet);
        }
        return $this->errorResponse(trans('api.not_found'));
    }

    public function getAttributes(Request $request) {
       $attribute = Attribute::where('status', 1)->with('attributeValues')->orderBy('sort','asc')->get();
       return $this->successResponse(trans('api.attribute_success'), $attribute);
    }


    public function setApiLanguage($param) {
        $language = 'en';
        if ($param == 'english') {
            $language = 'en';
        } elseif ($param == 'arabic') {
            $language = 'ar';
        }

        App::setLocale($language);

        return $language;
    }

    public function getCurrentLanguage() {

        if (session()->has('language')) {
            if (session()->get('language') == 'en') {
                return 'English';
            } else {
                return 'Arabic';
            }
        } else {
            return 'English';
        }
    }

    public function successResponse($status, $data='', $count='', $pageLimit='') {
		$page = null;
		//if(!empty($count)){
			$page['totalCount'] =$count;
			$page['limit'] =$pageLimit;
		//}
        if (!empty($data)) {
            return response()->json([
                        "StatusCode" => 10000,
                        "Status" => $status,
                        "Data" => $data,
						"Page" => $page,
            ]);
        } else {
            return response()->json([
                        "StatusCode" => 10000,
                        "Status" => $status
            ]);
        }
    }

    public function errorResponse($status) {
        return response()->json([
                    "StatusCode" => 20000,
                    "Status" => $status
        ]);
    }

    public function sessionExpireErrorResponse($status) {
        return response()->json([
                    "StatusCode" => 30000,
                    "Status" => $status
        ]);
    }
}
