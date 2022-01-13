<?php

namespace App\Http\Controllers\Api\V1\Trader;

use App\Http\Requests;
use App\VerifyUser;
use App\Version;
use Illuminate\Http\Request;
use App\TraderUser;
use App\TraderImages;
use Illuminate\Support\Facades\Log;
use Validator;
use Hash;
use App\Http\Controllers\ApiController;
use Auth;
use Twilio\Rest\Client;
use Mail;
use File;
use Storage;
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

    protected function user($request){
        $user =  TraderUser::where('api_token',$request->api_token)->first();

        if(!$user){
            return $this->errorResponse(trans('api.not_found'));
        }

		/*if(!empty($user->trade_license)){
			$user->trade_license = url('uploads/traders/images/'.$user->trade_license);
		}
		if(!empty($user->passport)){
			$user->passport = url('uploads/traders/images/'.$user->passport);
		}

		if(!empty($user->document)){
			$user->document = url('uploads/traders/images/'.$user->document);
		}

		if(!empty($user->image)){
			$user->image = url('uploads/traders/images/'.$user->image);
		}*/

        return $user;
    }


	  public function updateToken(Request $request)
    {
		$validator = Validator::make($request->all(),array('deviceId' => 'required','deviceType' => 'required'));
        if ($validator->fails()) {
			 return $this->errorResponse(trans('api.error_required_fields'));
		}

		$trader =  TraderUser::where('api_token',$request->api_token)->first();

		$trader->device_type = $request->deviceType;
		$trader->device_id = $request->deviceId;
		$trader->device_id_actual = !empty($request->deviceIdActual) ? $request->deviceIdActual : '';
        $trader->save();

		return $this->successResponse(trans('api.device_token_updated'));

	}

    public function getProfile(Request $request)
    {
           $user = $this->user($request);

         if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
             return $this->sessionExpireErrorResponse(trans('api.session_expire'));
         }

        $response = array(
            'id'=>$user->id,
            'first_name'=>$user->first_name,
            'last_name'=>$user->last_name,
            'email'=>$user->email,
            'phone'=>$user->phone,
            'is_verify_email'=>$user->is_verify_email,
            'status'=>$user->status,
            'docs'=> $this->getTraderDocs($user->id),
            'api_token'=>$user->api_token,
            'session_id'=>$user->session_id,
            'created_at'=>$user->created_at,
            'updated_at'=>$user->updated_at,
        );
        return $this->successResponse(trans('api.user_details'),$response);
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(),array('old_password' => 'required','new_password' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }
        $user = $this->user($request);
        if( (!empty($request->session_id)) && ($user->session_id != $request->session_id) ) {
          return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }
        if(!Hash::check($request->old_password, $user->password)){
            return $this->errorResponse(trans('api.password_error'));
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return $this->successResponse(trans('api.password_update'),$user);
    }

    public function traderLogout(Request $request){
        $validator = Validator::make($request->all(),array('session_id' => 'required','api_token' => 'required', 'language' => 'required'));
        if ($validator->fails()) {
			 return $this->errorResponse(trans('api.error_required_fields'));
        }
        $trader =  TraderUser::where('api_token',$request->api_token)->first();
        if( (!empty($request->session_id)) && ($trader->session_id != $request->session_id) ) {
            return $this->sessionExpireErrorResponse(trans('api.session_expire'));
        }
        if(!empty($trader->device_id)){

       
            try {
                $twilio = new Client(config('services.twilio.apiKey'), config('services.twilio.apiSecret'));
                $bindings = $twilio->notify->v1->services(config('services.twilio.serviceSid'))
                ->bindings
                ->read(["identity" => $trader->device_id]);
    
                if(!empty($bindings)){
                    foreach ($bindings as $record) {
                        $twilio->notify->v1->services(config('services.twilio.serviceSid'))
                        ->bindings($record->sid)
                        ->delete();
                    }
                }
            } catch (Exception $e) {
                Auth::guard('trader')->logout();
               // Log::error('Error creating binding: ' . $e->getMessage());
                return $this->successResponse(trans('api.success_logout'));
            }
        }
        $trader->session_id = null;
        $trader->device_id = null;
        $trader->save();
        Auth::guard('trader')->logout();
        return $this->successResponse(trans('api.success_logout'));
    }
    public function createTrader(Request $request){

        $validator = Validator::make($request->all(),array(
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
            'phone' => 'required',
            'estimated_amount' => 'required',

        ));
        if ($validator->fails()) {
            return $this->errorResponse(trans('api.error_required_fields'));
            //return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $exists = TraderUser::where('email', $request->email)->first();

        if(!empty($exists)){
            return $this->errorResponse(trans('api.email_already_exist'));
        }

        $trader = new TraderUser();
        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['estimated_amount'] = $request->estimated_amount;
        $data['account'] = 'Trader';
        $data['is_verify_email'] = 0;
        $ios = Version::where('type', '=', 'ios')->first();
        $android = Version::where('type', '=', 'android')->first();
        //dd($ios->url);
        $data['iosUrl'] = $ios->url;
        $data['androidUrl'] = $android->url;
        //$iosUrl = $ios->;
        /*try {
            $mail = Mail::send('emails.registration_traders', $data, function ($message) use ($data) {
                $message->to($data['email']);
                $message->subject($data['account'] . ' Account Created');
            });
        } catch (\Swift_TransportException $e) {
            Log::error($e->getMessage());
        }*/


        $data['is_verify_email'] =0;
        $data['api_token'] = str_random(60);
        $data['password'] = bcrypt($request->password);





        unset($data['iosUrl']);
        unset($data['androidUrl']);
        unset($data['account']);
        unset($data['business_size']);
        unset($data['kyc_credit_limit']);
        unset($data['age_of_car']);
        unset($data['target_market']);
        //unset($data['mileage']);
        unset($data['car_condition']);
        unset($data['specifications']);
        unset($data['make_cars']);
        unset($data['other_value']);
        unset($data['expiry']);

        $data['status'] = 1;
        $tId = $trader->create($data);

        //update documents
        if( !empty($request->emirates_id_front_doc_id) ){
            $traderImage = \App\TraderImages::where('id', $request->emirates_id_front_doc_id)->first();
            $traderImage->traderId = $tId->id;
            $traderImage->save();
        }

        if( !empty($request->emirates_id_back_doc_id) ){
            $traderImage = \App\TraderImages::where('id', $request->emirates_id_back_doc_id)->first();
            $traderImage->traderId = $tId->id;
            $traderImage->save();
        }

        if( !empty($request->passport_front_doc_id) ){
            $traderImage = \App\TraderImages::where('id', $request->passport_front_doc_id)->first();
            $traderImage->traderId = $tId->id;
            $traderImage->save();
        }

        if( !empty($request->passport_back_doc_id) ){
            $traderImage = \App\TraderImages::where('id', $request->passport_back_doc_id)->first();
            $traderImage->traderId = $tId->id;
            $traderImage->save();
        }

        if( !empty($request->other_doc_ids) ){
          foreach($request->other_doc_ids as $other_doc_id){
              $traderImage = \App\TraderImages::where('id', $other_doc_id)->first();
              $traderImage->traderId = $tId->id;
              $traderImage->save();
          }
        }


        $verifyUser = VerifyUser::create([
            'trader_id' => $tId->id,
            'token' => sha1(time())
        ]);
        $data['token'] = $verifyUser->token;
        $data['account'] = 'Trader';

        try {
            $mail = Mail::send('emails.verifyUser', $data, function ($message) use ($data) {
                $message->to($data['email']);
                $message->subject('Account Created');
            });
        } catch(\Exception $e){

        }


        $trader_data = TraderUser::where('id',$tId->id)->first();

        $response = array(
            'id'=>$trader_data->id,
            'first_name'=>$trader_data->first_name,
            'last_name'=>$trader_data->last_name,
            'email'=>$trader_data->email,
            'phone'=>$trader_data->phone,
            'estimated_amount'=>$trader_data->estimated_amount,
            'is_verify_email'=>$trader_data->is_verify_email,
            'status'=>$trader_data->status,
            'docs'=> $this->getTraderDocs($tId->id),
            'api_token'=>$trader_data->api_token,
            'session_id'=>$trader_data->session_id,
            'created_at'=>$trader_data->created_at,
            'updated_at'=>$trader_data->updated_at,
        );

        return $this->successResponse(trans('api.add_trader'),$response);
    }

    public function getTraderDocs($traderId){

        $data = array();

        $traderDocs = \App\TraderImages::where('traderId', $traderId)->get();


        if(!empty($traderDocs)){
            $i=0;
            foreach($traderDocs as $traderDoc){

              $path = 'traders/' . $traderDoc->imageType . '/';
              $data[$i]['type'] = $traderDoc->imageType;
              $data[$i]['docId'] = $traderDoc->id;
              $data[$i]['docUrl'] = $traderDoc->image;
              $i++;
            }
        }

        return $data;



    }

    public function uploadDocuments(Request $request) {
        // $validator = Validator::make($request->all(), array(
        //   'imageType' => 'required',
        //   'image' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:8096'
        // ));

        // if ($validator->fails()) {
        //     return $this->errorResponse(trans('api.document_uploaded_valid_error'));
        //     //return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        // }

        if ($request->hasFile('image')) {
                $type = $request->imageType;
                $removeDocId = !empty($request->removeDocId) ? $request->removeDocId : '';
                $traderId = !empty($request->traderId) ? $request->traderId : '';
                $sortOrder = !empty($request->sort) ? $request->sort : '';

                switch ($type) {
                    case 'emirates_id_front':
                        $path = 'traders/' . $type . '/';
                        break;
                    case 'emirates_id_back':
                        $path = 'traders/' . $type . '/';
                        break;

                    case 'passport_front':
                        $path = 'traders/' . $type . '/';
                        break;

                    case 'passport_back':
                        $path = 'traders/' . $type . '/';
                        break;
                    case 'other_doc':
                        $path = 'traders/' . $type . '/';
                        break;
                    default:
                        return $this->errorResponse(trans('api.error_required_fields'));
                        break;
                }

                $image = $request->file('image');
                $dir = config('app.fileDirectory') . $path;
                $uploaded = false;
                $data = array();

                if (File::mimeType($image) != 'application/pdf') {
                    $img = Image::make($image);
                    // dd($img);
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $name = $timestamp . $type . '-' . $str . $image->getClientOriginalName();
                    $uploaded = Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');

                } else {
                    $img = $image;
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $name = $timestamp . $type . '-' . $str . $image->getClientOriginalName();
                    // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    $uploaded = Storage::disk('s3')->put($dir . $name, file_get_contents($img->getRealPath()), 'public');

                }

                if ($uploaded) {

                    if(!empty($removeDocId)){
                        $removeDoc = \App\TraderImages::where('id', $removeDocId)->first();

                        if (!empty($removeDoc)) {
                            Storage::disk('s3')->delete($dir . $removeDoc->image);
                            $removeDoc->delete();
                        }
                    }

                    $traderImage = new \App\TraderImages();
                    $traderImage->image = $name;
                    $traderImage->imageType = $type;
                    if(!empty($traderId)){
                        $traderImage->traderId = $traderId;
                    }

                    if(!empty($sortOrder)){
                        $traderImage->sort = $sortOrder;
                    }

                    $traderImage->save();

                    $data['type'] = $type;
                    $data['docId'] = $traderImage->id;
                    $data['docUrl'] = env('S3_URL').'uploads/'.$path.$name;

                    return $this->successResponse(trans('api.document_uploaded_success'), $data);

                }else{
                    return $this->errorResponse(trans('api.document_uploaded_error'));
                }

            }
            return $this->errorResponse(trans('api.error_required_fields'));
    }

    public function saveTraderImage(Request $request)
    {

        $validator = Validator::make($request->all(), array('objectId' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }

        $traderId = $request->traderId;
        $images = $request->imageNames;

        //delete images if exist
        $imageExist = TraderImages::where('trader_id', $traderId)->first();
        if (!empty($imageExist)) {
            TraderImages::where('trader_id', $traderId)->delete();
        }

        if (!empty($images)) {
            foreach ($images as $image) {
                    $addImage = new TraderImages();
                    $addImage->object_id = $traderId;
                    $addImage->image = $image['name'];
                    $addImage->imageType = $image['type'];
                    $addImage->save();
              //  }
            }

            // $object = Object::find($traderId);
            // $object->images_uploaded = 1;
            // $object->save();
        }

        return $this->successResponse(trans('api.object_success'));
    }

}
