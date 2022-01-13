<?php

namespace App\Http\Controllers\Api\V1\Inspector;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\InspectorUser;
use App\Object;
use App\ObjectImage;
use App\Auction;

use App\ObjectAttributeValue;
use Validator;
use App\AttributeSet;
use App\Attribute;
use App\Customer;
use App\Http\Controllers\ApiController;
use Auth;
use App\Make;
use App\Models;
use App\Bank;
use App\InspectorNegaotiate;
use DB;
use Twilio\Rest\Client;
use App\AdminNotification;
use Carbon\Carbon;
use App\InspectorActivity;

use App\Http\Controllers\Controller as SentPush;

use Image;

class UserController extends ApiController
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

    public function sessionValid($request) {
        if(empty($request->api_token) || empty($request->session_id)) {
            return true;
        }

        $user =  InspectorUser::where('api_token', $request->api_token)->first();


        if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
            return false;
        }
        return true;
    }

    protected function user($request)
    {
        $user =  InspectorUser::where('api_token', $request->api_token)->first();
        if (!$user) {
            return $this->errorResponse(trans('api.not_found'));
        }
        return $user;
    }

    public function updateToken(Request $request){

        $validator = Validator::make($request->all(), array('deviceId' => 'required','deviceType' => 'required'));
        if ($validator->fails()) {
            return $this->errorResponse(trans('api.error_required_fields'));
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        $inspector =  InspectorUser::where('api_token', $request->api_token)->first();

        $inspector->device_type = $request->deviceType;
        $inspector->device_id = $request->deviceId;
        $inspector->device_id_actual = !empty($request->deviceIdActual) ? $request->deviceIdActual : '';
        $inspector->save();

        return $this->successResponse(trans('api.device_token_updated'));
    }

    public function twilioRegister(Request $request)
    {
        $validator = Validator::make($request->all(), array('identity' => 'required', 'BindingType' => 'required', 'Address' => 'required'));
        if ($validator->fails()) {
            return $this->errorResponse(trans('api.error_required_fields'));
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        $client = new Client(config('services.twilio.apiKey'), config('services.twilio.apiSecret'), config('services.twilio.inpectorServiceSid'));

        $service = $client->notify->v1->services(config('services.twilio.inpectorServiceSid'));

        // Create a binding
        try {
            $binding = $service->bindings->create(
                    $request->identity,
                $request->BindingType,
                $request->Address
            );

            return $this->successResponse(trans('api.bindingCreated'));
        } catch (Exception $e) {
            Log::error('Error creating binding: ' . $e->getMessage());
            return $this->errorResponse(trans('api.bindingFalied'));
        }
    }

    public function getProfile(Request $request)
    {
        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }
        return $this->successResponse(trans('api.user_details'), $this->user($request));
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), array('old_password' => 'required','new_password' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }
        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        $user = $this->user($request);
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->errorResponse(trans('api.password_error'));
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return $this->successResponse(trans('api.password_update'), $user);
    }

    public function getAttributeSet()
    {
        $attributeSet = $this->getAttributeSetList();
        if (count($attributeSet)) {
            return $this->successResponse(trans('api.attributeset_success'), $attributeSet);
        }
        return $this->errorResponse(trans('api.not_found'));
    }

    public function getMakes()
    {
        $data = [];
        $data['makes'] = Make::with('models')->get();
        $data['banks'] = Bank::where('status', 1)->get();
        $data['nationalities'] = \App\Country::all();

        $source[] = 'Dubizzle';
        $source[] = 'Instagram';
        $source[] = 'Facebook';
        $source[] = 'Referral';

        $source[] = 'Billboards';
        $source[] = 'Electronics Media';
        $source[] = 'Youtube Ads';

        $source[] = 'Radio';
        $source[] = 'TV ADS';
        $source[] = 'Others';

        $data['enquirySources'] = $source;

        if (count($data)) {
            return $this->successResponse(trans('api.attributeset_success'), $data);
        }
        return $this->errorResponse(trans('api.not_found'));
    }

    public function getBanks()
    {
         $data = [];
         $data['bank'] = Bank::where('status', 1)->get();
         $data['nationalities'] = \App\Country::all();

        if (count($data)) {
            return $this->successResponse(trans('api.attributeset_success'), $data);
        }
        return $this->errorResponse(trans('api.not_found'));
    }


    public function saveImageNames(Request $request)
    {

        //ini_set('memory_limit','256M');
        //ini_set('max_input_vars', 2500);

        $validator = Validator::make($request->all(), array('objectId' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }


        $objectId = $request->objectId;
        $images = $request->imageNames;

        //delete images if exist
        $imageExist = ObjectImage::where('object_id', $objectId)->first();
        if (!empty($imageExist)) {
            ObjectImage::where('object_id', $objectId)->delete();
        }

        if (!empty($images)) {
            foreach ($images as $image) {
                $addImage = new ObjectImage();
                $addImage->object_id = $objectId;
                $addImage->image = $image;
                $addImage->save();
            }

            $object = Object::find($objectId);
            $object->images_uploaded = 1;
            $object->save();
        }

        return $this->successResponse(trans('api.object_success'));
    }

    public function saveImageNamesV2(Request $request)
    {

        //ini_set('memory_limit','256M');
        //ini_set('max_input_vars', 2500);

        $validator = Validator::make($request->all(), array('objectId' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }


        $objectId = $request->objectId;
        $images = $request->imageNames;

        //delete images if exist
        $imageExist = ObjectImage::where('object_id', $objectId)->first();
        if (!empty($imageExist)) {
            ObjectImage::where('object_id', $objectId)->delete();
        }

        if (!empty($images)) {
            foreach ($images as $image) {
                /*$imgExistChk = ObjectImage::where('object_id',$objectId)->where('imageType', $image['type'])->first();
                if(!empty($imgExistChk)){
                    $imgExistChk->image = $image['name'];
                    $imgExistChk->save();
                }else{*/
                    $addImage = new ObjectImage();
                    $addImage->object_id = $objectId;
                    $addImage->image = $image['name'];
                    $addImage->imageType = $image['type'];
                    $addImage->save();
              //  }
            }

            $object = Object::find($objectId);
            $object->images_uploaded = 1;
            $object->save();
        }

        return $this->successResponse(trans('api.object_success'));
    }


    public function saveImage(Request $request)
    {

        //ini_set('memory_limit','256M');
        //ini_set('max_input_vars', 2500);

        $validator = Validator::make($request->all(), array('objectId' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }


        $objectId = $request->objectId;
        $images = $request->images;
        /*
            if(empty($images[0]) ){
                     return false;
             }*/

        if (!empty($images[0])) {
            foreach ($images as $key=>$image) {

                /*var_dump($image);
                echo $image->getClientOriginalName();
                 exit;*/
                $addImage = new ObjectImage();
                $relPath = 'uploads/object';
                $path = public_path() . '/' . $relPath;
                $filename = time() . uniqid() .'_vehicle'. '.' . $image->getClientOriginalExtension();

                //thumb
                $destinationPath = public_path('/uploads/object/thumb/');
                $img = Image::make($image->getRealPath());
                $img->fit(400, 400, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath . '/' . $filename);
                //thumb

                $uFile = $image->move($path, $filename);

                $addImage->object_id = $objectId;
                $addImage->image = $filename;
                $addImage->save();
            }

            $object = Object::find($objectId);
            $object->images_uploaded = 1;
            $object->save();
        }


        return $this->successResponse(trans('api.object_success'));
    }


    public function saveObjectBackup(Request $request)
    {
        $validator = Validator::make($request->all(), array('name' => 'required','code'=>'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        $user = $this->user($request);
        $object = $request->has('object_id') ? Object::find($request->object_id) : new Object();
        $object->name = $request->name;
        $object->code = $request->code;
        $object->inspector_id = $user->id;
        $object->dealer_id = $user->dealer_id;
        $object->model_id = $request->model;
        $object->make_id = $request->make;
        $object->model_id = $request->model;
        $object->make_id = $request->make;

        $object->save();



        if ($request->has('attributes') && count($request->get('attributes'))) {
            if ($request->has('object_id')) {
                ObjectAttributeValue::where('object_id', $request->object_id)->delete();
            }
            foreach ($request->get('attributes') as $attributes) {
                $attributes['object_id'] = $object->id;
                $objectAttribute = new ObjectAttributeValue();
                $objectAttribute->create($attributes);
            }
        }



        $objectId = $object->id;
        $data = $object->toArray();
        $data['attributes'] = $object->values;

        $data['images'] = !empty($object->images) ? $object->images : '';

        $objectId = $object->id;

        /*
     	$images = $request->images;


     	if(!empty($images[0])){

           foreach ($images as $key=>$image) {


     			$addImage = new ObjectImage();
     			$relPath = 'uploads/object';
     			$path = public_path() . '/' . $relPath;
     			$filename = time() . uniqid() .'_vehicle'. '.' . $image->getClientOriginalExtension();
     			$uFile = $image->move($path, $filename);

     			$addImage->object_id = $objectId;
     			$addImage->image = $filename;
     			$addImage->save();

           }

     	}*/
        //$objects =   Object::where('id', $id)->with('values','images')->get();



        $data = $object->toArray();
        $data['attributes'] = $object->values;

        $data['images'] = !empty($object->images) ? $object->images : '';

        //var_dump($data); exit;
        //echo json_encode($data); exit;

        return $this->successResponse(trans('api.object_success'), $data);
    }

    public function saveObject(Request $request)
    {
        $validator = Validator::make($request->all(), array('name' => 'required','code'=>'required', 'suggested_amount' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        $user = $this->user($request);
        $exist = Object::find($request->object_id);
        $object = $request->has('object_id') ? $exist : new Object();
        $objectId = !empty($request->object_id) ? $request->object_id : '';
        $restrictStatus = $this->objectRestrictUpdateStatus($objectId);

        if($restrictStatus){
             return $this->errorResponse(trans('api.vehilce_under_auction'));
        }

        $source = \App\InspectorSource::where('id', $user->source_id)->first();
        $notification_source = !empty($source) ? ($source->title=='Wecashanycar (Internal)') ? 1 : 2 : 2;
        // dd($notification_source);
        $object->name = $request->name;
        $object->code = $request->code;
        $object->inspector_id = $user->id;
        $object->dealer_id = $user->dealer_id;
        $object->variation = $request->variation;
        // $object->vin = $request->vin;
        // $object->vehicle_registration_number = $request->vehicleRegistrationNumber;
        $object->model_id = $request->model;
        $object->make_id = $request->make;
        $object->customer_name = $request->customerName;
        $object->customer_mobile = $request->customerMobile;
        $object->customer_email = $request->customerEmail;
        $object->customer_reference = $request->customerReference;
        $object->suggested_amount = $request->suggested_amount;
        $object->nationality_id = $request->nationality_id;
        $object->bank_id = $request->bank;
        $object->source_of_enquiry = $request->sourceOfEnquiry;
        $object->save();

        $customer = Customer::where('mobile', $request->customerMobile)->first();
        if(!empty($customer)){
             $customer->customer_name = $request->customerName;
             $customer->email = $request->customerEmail;
             $customer->customer_reference_number = $request->customerReference;
             $customer->nationality_id = $request->nationality_id;
             $customer->save();
        }else{
             $customer = new Customer();
             $customer->customer_name = $request->customerName;
             $customer->mobile = $request->customerMobile;
             $customer->email = $request->customerEmail;
             $customer->customer_reference_number = $request->customerReference;
             $customer->nationality_id = $request->nationality_id;
             $customer->save();
        }

        $notification = new AdminNotification();
        $notification->messages = $user->name." created new watch";
        $notification->inspector_id = $user->id;
        $notification->source = $notification_source;
        $notification->save();

        if ($request->has('attributes') && count($request->get('attributes'))) {
            if ($request->has('object_id')) {
                ObjectAttributeValue::where('object_id', $request->object_id)->delete();
            }
            foreach ($request->get('attributes') as $attributes) {
                $attributes['object_id'] = $object->id;
                $objectAttribute = new ObjectAttributeValue();
                $objectAttribute->create($attributes);
            }
        }

        $objectId = $object->id;
        $data = $object->toArray();
        $data['attributes'] = $object->values;

        $data['images'] = !empty($object->images) ? $object->images : '';


        return $this->successResponse(trans('api.object_success'), $data);
    }

    public function getObject(Request $request)
    {
        $validator = Validator::make($request->all(), array('inspectorId' => 'required', 'language' => 'required', 'api_token' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        //$auctions = Auction::select('object_id')->get()->lists('object_id');
        $vehicles = Object::join('makes', 'makes.id', '=', 'objects.make_id')
                                        ->join('models', 'models.id', '=', 'objects.model_id')
                                        ->select('objects.id', 'models.name as modelName', 'makes.name as makeName', 'objects.name as vehicleName', 'objects.code', 'objects.created_at as objectCreated', 'objects.suggested_amount')
                                        //->whereNotIn('objects.id',$auctions)
                                        ->where('inspector_id', $request->inspectorId);
        $keyword = $request->keyword;
        if ($keyword){
            $vehicles->where(function ($query) use ($keyword) {
                                       $query->where('objects.name', 'LIKE', '%'.$keyword.'%')
                                             ->orWhere('objects.vehicle_registration_number', 'LIKE', '%'.$keyword.'%')
                                             ->orWhere('objects.vin', 'LIKE', '%'.$keyword.'%');
                                   });
             //->where('objects.name', 'LIKE', '%'.$keyword.'%')->orWhere('objects.code', 'LIKE', '%'.$keyword.'%');
        }

        $vehicles = $vehicles->orderBy('objects.updated_at', 'desc')->limit(100)->get();

        $data = [];
        $i=0;

        if (!empty($vehicles)) {
            foreach ($vehicles as $vehicle) {
                $vehicleImage = ObjectImage::where('object_id', $vehicle->id)->first();
                $vehicleInAuction = Auction::where('object_id', $vehicle->id)->first();

                $data[$i]['vehicleId'] = $vehicle->id;
                $data[$i]['vehicleName'] = $vehicle->vehicleName;
                $data[$i]['suggested_amount'] = $vehicle->suggested_amount;
                $data[$i]['code'] = $vehicle->code;
                $data[$i]['modelName'] = $vehicle->modelName;
                $data[$i]['makeName'] = $vehicle->makeName;
                $data[$i]['vehicleImage'] = $vehicleImage ? $vehicleImage->image : null;
                $data[$i]['vehicleStatus'] = $vehicleInAuction ? 1 : 0;
                $data[$i]['auctionStatus'] = !empty($vehicleInAuction->status) ? $vehicleInAuction->status : 'New';
                $data[$i]['restrictStatus'] = $this->objectRestrictUpdateStatus($vehicle->id);

                $data[$i]['created_at'] = $vehicle->objectCreated;
                $i++;
            }
        }

        return $this->successResponse(trans('api.vehicleDetails'), $data);
    }

    public function getObjectDetail(Request $request)
    {
        if(empty($request->vinNumber)) {
             $validator = Validator::make($request->all(), array('objectId'=>'required', 'language' => 'required', 'api_token' => 'required'));
        }else{
             $validator = Validator::make($request->all(), array('vinNumber'=>'required', 'language' => 'required', 'api_token' => 'required'));
        }

        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => trans('api.error_required_fields')]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        $user =  InspectorUser::where('api_token', $request->api_token)->first();
        $userId = $user->id;
        $objectId = $request->objectId;
        $vinNumber = $request->vinNumber;

        if(!empty($objectId)){
           $data = $this->getobjectDataInspector($objectId);
        }else{
           $data = $this->getobjectDataInspector('', $vinNumber);
        }

        if (!empty($data)) {
            return $this->successResponse(trans('api.object_not_found'), $data);
        } else {
            return $this->errorResponse(trans('api.object_not_found'));
        }
    }




    public function getClosedAuctions(Request $request)
    {

        $validator = Validator::make($request->all(), array('inspectorId' => 'required', 'language' => 'required', 'api_token' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        //$auction = new Auction();/
        //echo $auction->getStatusType(3); exit;

        $closedAuctions = Auction::join('objects', 'objects.id', '=', 'auctions.object_id')
                                   ->join('makes', 'makes.id', '=', 'objects.make_id')
                                   ->join('models', 'models.id', '=', 'objects.model_id')
                                   ->select('auctions.status', 'auctions.sale_type_id', 'auctions.deducted_amount', 'auctions.object_id', 'objects.name', 'auctions.id as auctionId',
                                   'objects.id as vehicleId', 'objects.name as vehicleName',
                                     'models.name as modelName', 'makes.name as makeName', 'objects.code')
                                   ->where('objects.inspector_id', $request->inspectorId)
                                   ->where('auctions.status', 3)
                                   ->whereNotNull('auctions.deducted_amount')
                                   ->orderBy('auctions.created_at', 'desc')
                                   ->get();


        /*$inspectorNegaotiates = InspectorNegaotiate::join('auctions', 'auctions.id', '=', 'inspector_negaotiates.auction_id')
                                                                           ->join('objects', 'objects.id', '=', 'auctions.object_id')
                                                                           ->join('makes', 'makes.id', '=', 'objects.make_id')
                                                                           ->join('models', 'models.id', '=', 'objects.model_id')
                                                                           ->join(DB::raw('(select max(id) as id from inspector_negaotiates group by auction_id) lastAuction'), function ($join) {
                                                                               $join->on('inspector_negaotiates.id', '=', 'lastAuction.id');
                                                                           })
                                                                           ->select('inspector_negaotiates.id', 'objects.id as vehicleId', 'objects.name as vehicleName',
                                                                             'models.name as modelName', 'makes.name as makeName', 'objects.code',
                                                                              'inspector_negaotiates.override_amount', 'auctions.id as auctionId', 'auctions.object_id',
                                                                              'inspector_negaotiates.customer_amount')
                                                                             ->where('inspector_negaotiates.inspector_id', $request->inspectorId)
                                                                             ->where('inspector_negaotiates.customer_amount', '=', 0)
                                                                           ->orderBy('inspector_negaotiates.created_at', 'desc')
                                                                           ->get();*/
        // dd($inspectorNegaotiates);
        if ($closedAuctions) {
            $data = [];
            $i = 0;
            foreach ($closedAuctions as $closedAuction) {

                if(!empty($closedAuction->deducted_amount)){

                      $vehicle = ObjectImage::where('object_id', $closedAuction->object_id)->first();

                      $data[$i]['vehicleId'] = $closedAuction->vehicleId;
                      $data[$i]['vehicleName'] = $closedAuction->vehicleName;
                      $data[$i]['vehicleImage'] = $vehicle->image;
                      $data[$i]['modelName'] = $closedAuction->modelName;
                      $data[$i]['makeName'] = $closedAuction->makeName;
                      $data[$i]['code'] = $closedAuction->code;

                      $lastBid = \App\Bid::where('auction_id', $closedAuction->auctionId)->orderBy('price', 'desc')->first();
                      $bidPrice = $lastBid ? $lastBid->price : 0;

                      $data[$i]['salePrice'] = $closedAuction->deducted_amount;

                      $data[$i]['auctionId'] = $closedAuction->auctionId;

                      $i++;
              }
            }
            return $this->successResponse(trans('api.vehicleDetails'), $data);
        } else {
            return $this->errorResponse(trans('api.inspector_not_found'));
        }
    }




    public function getNegotiate(Request $request)
    {
        $validator = Validator::make($request->all(), array('inspectorId' => 'required', 'language' => 'required', 'api_token' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        $inspectorNegaotiates = InspectorNegaotiate::join('auctions', 'auctions.id', '=', 'inspector_negaotiates.auction_id')
                                                                           ->join('objects', 'objects.id', '=', 'auctions.object_id')
                                                                           ->join('makes', 'makes.id', '=', 'objects.make_id')
                                                                           ->join('models', 'models.id', '=', 'objects.model_id')
                                                                           ->join(DB::raw('(select max(id) as id from inspector_negaotiates group by auction_id) lastAuction'), function ($join) {
                                                                               $join->on('inspector_negaotiates.id', '=', 'lastAuction.id');
                                                                           })
                                                                           ->select('inspector_negaotiates.id', 'objects.id as vehicleId', 'objects.name as vehicleName',
                                                                             'models.name as modelName', 'makes.name as makeName', 'objects.code',
                                                                              'inspector_negaotiates.override_amount', 'auctions.id as auctionId', 'auctions.object_id',
                                                                              'inspector_negaotiates.customer_amount')
                                                                             ->where('inspector_negaotiates.inspector_id', $request->inspectorId)
                                                                             ->where('inspector_negaotiates.customer_amount', '=', 0)
                                                                           ->orderBy('inspector_negaotiates.created_at', 'desc')
                                                                           ->get();
        // dd($inspectorNegaotiates);
        if ($inspectorNegaotiates) {
            $data = [];
            $i = 0;
            foreach ($inspectorNegaotiates as $inspectorNegaotiate) {
                $vehicle = ObjectImage::where('object_id', $inspectorNegaotiate->object_id)->first();

                $data[$i]['negaotiateId'] = $inspectorNegaotiate->id;
                $data[$i]['vehicleId'] = $inspectorNegaotiate->vehicleId;
                $data[$i]['vehicleName'] = $inspectorNegaotiate->vehicleName;
                $data[$i]['vehicleImage'] = $vehicle->image;
                $data[$i]['modelName'] = $inspectorNegaotiate->modelName;
                $data[$i]['makeName'] = $inspectorNegaotiate->makeName;
                $data[$i]['code'] = $inspectorNegaotiate->code;
                $data[$i]['overrideAmount'] = $inspectorNegaotiate->override_amount;
                $data[$i]['auctionId'] = $inspectorNegaotiate->auctionId;
                $data[$i]['customerAmount'] = $inspectorNegaotiate->customer_amount;
                $i++;
            }
            return $this->successResponse(trans('api.vehicleDetails'), $data);
        } else {
            return $this->errorResponse(trans('api.inspector_not_found'));
        }
    }

    public function saveCustomerNegotiateAmount(Request $request)
    {
        $validator = Validator::make($request->all(), array('auctionId' => 'required', 'language' => 'required', 'api_token' => 'required', 'customerAmount' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }

        $id = $request->auctionId;

        $auction = Auction::find($id);
        $object = Object::find($auction->object_id);

        $inspectorAuction = new InspectorNegaotiate;
        $inspectorAuction->auction_id = $id;
        $inspectorAuction->inspector_id = $object->inspector_id;
        $inspectorAuction->customer_amount = $request->customerAmount;
        $inspectorAuction->save();

        /*$inspectorNegaotiate = InspectorNegaotiate::find($request->negaotiateId);
        $inspectorNegaotiate->customer_amount = $request->customerAmount;
        $inspectorNegaotiate->save();*/
        return $this->successResponse(trans('api.customerAmountUpdate'));
    }

    public function negotiateSave(Request $request) {
          $validator = Validator::make($request->all(), array('negotiatePrice' => 'required', 'auctionId' => 'required', 'language' => 'required', 'api_token' => 'required'));
          if ($validator->fails()) {
               return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
          }

          $session_valid = $this->sessionValid($request);
          if($session_valid == false) {
              return $this->sessionExpireErrorResponse(trans('api.session_expire'));
          }

         $auction = Auction::find($request->auctionId);

         $lastBid = \App\Bid::where('auction_id', $request->auctionId)->orderBy('price', 'desc')->first();
         $bidPrice = $lastBid ? $lastBid->price : 0;

         if ($auction->status != $auction->getStatusType(3)) {
             return $this->errorResponse(trans('api.unable_negotiate'));
         }

         $deductedAmount = $bidPrice;

         if(!empty($auction->deducted_amount)){
            $deductedAmount = $auction->deducted_amount;
         }

         $diffAmount = $request->negotiatePrice - $deductedAmount;
         $actualAmount = $bidPrice + $diffAmount;

         if ($request->negotiatePrice <  $deductedAmount) {
            return $this->errorResponse(trans('api.negotaite_greater_last_bid_price'));
         }

         $currentTime = strtotime($this->UaeDate(Carbon::now()));

         if ($auction->status != $auction->getStatusType(3)) {
             return $this->errorResponse(trans('api.unable_negotiate'));
         }

         $now = $this->UaeDate(Carbon::now());
         $newTime = date('Y-m-d H:i:s', strtotime($now." +5 minutes"));
         $auction->status = $auction->getStatusType(1);
         $auction->negotiated_amount = $actualAmount;
         $auction->negotiatedTime = Carbon::now();
         $auction->is_negotiated = 1;
         $auction->end_time = $newTime;
         $auction->save();
         SentPush::sendAuctionNegotiatedPush($request->auctionId);
         return $this->successResponse(trans('api.inspector_successfully_negotiated'));
    }

    public function saveTracker(Request $request) {
        $validator = Validator::make($request->all(), array('inspectorId' => 'required', 'objectId' => 'required', 'language' => 'required', 'api_token' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }
        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }
        $tracker = $request->tracker;
        foreach($tracker as $_tracker) {
            $inspector_tracker = new InspectorActivity();
            $inspector_tracker->inspector_id = $request->inspectorId;
            $inspector_tracker->object_id = $request->objectId;
            $inspector_tracker->session_start_time = $request->sessionStartTime;
            $inspector_tracker->start_time = date('Y-m-d H:i:s', strtotime($_tracker['start']));
            $inspector_tracker->end_time = date('Y-m-d H:i:s', strtotime($_tracker['end']));
            $inspector_tracker->type = $_tracker['type'];
            $inspector_tracker->save();
        }
        return $this->successResponse(trans('api.inspector_tracker_successfully_added'));
    }

    public function pdfGenerate(Request $request) {
        $validator = Validator::make($request->all(), array('language' => 'required', 'api_token' => 'required', 'session_id' => 'required', 'objectId' => 'required|integer'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $session_valid = $this->sessionValid($request);
        if($session_valid == false) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }
        $objectId = $request->objectId;
        $object = \App\Object::where('id', $objectId)->first();
        if(!empty($object)) {
            $data['url'] = url('inspector-generatePdf/'. encrypt($objectId));
            return $this->successResponse(trans('api.successfully_get_url'), $data);
        } else {
            return '<h2 style="text-align: center">oops something went wrong</h2>';
        }

        return $this->errorResponse(trans('api.no_data_found'));
    }
}
