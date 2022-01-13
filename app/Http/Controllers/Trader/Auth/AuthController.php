<?php

namespace App\Http\Controllers\Trader\Auth;

use App\TraderUser;
use Auth;
use Validator;
use App\VerifyUser;
use App\Mail\VerifyMail;
use App\Version;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Page;
use Carbon\Carbon;
use Mail;
use Datatables;
use DB;
use File;
use Storage;
use App\DealerUser;
use App\CreditHistory;
use Illuminate\Support\Facades\Log;
use GuzzleHttp;
use App\User;
use App\Country;
use App\Emirate;
use App\Auction;
use Excel;
use Image;
use Redirect;
use Gate;
// use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
     protected $redirectTo = '/';
     protected $guard = 'trader';

     public function __construct()
     {
         $this->redirectTo = session()->get('language').'/home';
     }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
           
        ]);
    }

    public function generateSessionId() {
        return uniqid() . time();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return TraderUser::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function showLoginForm($lang = null)
    {
           if($lang == 'en') {
                $terms = Page::where('language', $lang)->where('slug', 'terms')->first();
           } else {
                $terms = Page::where('language', $lang)->where('slug', 'terms')->first();
           }
            if (Auth::guard('trader')->check())
            {
                    return redirect(session()->get('language').'/home');
            }

            return view('trader.auth.login', compact('terms'));
    }

    public function showRegistrationForm()
    {
            return view('trader.auth.register');
    }
    public function ImageUpload($image,$type,$removeDocId,$traderId ,$sortOrder){
        if (!empty($image)) {

           

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
               
                    break;
            }

          
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

               // return $this->successResponse(trans('api.document_uploaded_success'), $data);

            }
        }

    } 
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(),array(
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255|unique:trader_users',
            'password' => 'required|max:255',
            //'confirm_password' => 'min:6',
            'phone' => 'required',
            'estimated_amount' => 'required',
            'trader_images.emirates_id_front' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'trader_images.emirates_id_back' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'trader_images.passport_front' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096',
            'trader_images.passport_back' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096'

        ));
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // dd($request->all());
        $exists = TraderUser::where('email', $request->email)->first();

        if(!empty($exists)){
            redirect()->back()->with('error',trans('api.email_already_exist'))->withInput();
            
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

      


        unset($data['iosUrl']);
        unset($data['androidUrl']);
        unset($data['account']);

        $data['is_verify_email'] =0;
        $data['api_token'] = str_random(60);
        $data['password'] = bcrypt($request->password);

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

        // echo '<pre>';print_r($data);die;
        $tId =$trader->create($data);

                
        if ($request->hasFile('trader_images') || count($request->trader_images['other_doc']) > 0) {
            $traderImages = $request->trader_images;

            foreach ($traderImages as $key => $image) {
                $type = $key;
            
                $removeDocId = !empty($request->removeDocId) ? $request->removeDocId : '';
                $traderId = $tId->id;
                $sortOrder = !empty($request->sort) ? $request->sort : '';

                if($key == 'other_doc'){
                    foreach ($image as $k => $otherdoc) {
                        $this->ImageUpload($otherdoc,$type,$removeDocId,$traderId ,$sortOrder);
                    }
                }else{
                        $this->ImageUpload($image,$type,$removeDocId,$traderId ,$sortOrder);
                }
           
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
                $message->subject(' Account Created');
            });
        } catch(\Exception $e){

        }

        return redirect(session()->get('language').'/login')->with('status', trans('api.add_trader'));

    }

    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if(isset($verifyUser) ){
        $user = $verifyUser->trader;
        if(!$user->is_verify_email) {
        $verifyUser->trader->is_verify_email = 1;
        $verifyUser->trader->save();

        $deleteUser = VerifyUser::where('token', $token)->delete();

        $status = "Your e-mail is verified. You can now login.";
        } else {
        $status = "Your e-mail is already verified. You can now login.";
        }
    } else {
        return redirect(session()->get('language').'/login')->with('error', "Invalid token.");
    }
    return redirect(session()->get('language').'/login')->with('status', $status);
    }

    public function resetPassword()
    {
            return view('trader.auth.passwords.email');
    }

    public function logout(Request $request){
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = $_SERVER['REMOTE_ADDR'];
        
            if(filter_var($client, FILTER_VALIDATE_IP))
            {
                $ip = $client;
            }
            elseif(filter_var($forward, FILTER_VALIDATE_IP))
            {
                $ip = $forward;
            }
            else
            {
                $ip = $remote;
            }

            $log = new \App\TraderLogHistory();       
            $log->ip = $ip;
            $log->type = 'logout';
            $log->time = $this->UaeDate(Carbon::now());
            $log->trader_id = Auth::guard('trader')->user()->id;
            $log->save();

            Auth::guard('trader')->logout();
            session()->forget('sessionId');
            return redirect(session()->get('language').'/login');
    }

    public function changePassword()
    {
        $edit = Auth::guard('trader')->user();
        return view('trader.auth.passwords.change', compact('edit'));
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'password' => 'required|same:confirm_password|min:6',
                    'confirm_password' => 'required'
        ]);
        if ($validator->fails()) {
            return back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $id = $request->userId;
        $users = TraderUser::find($id);
        $users->password = bcrypt($request->password);
        $users->save();
        return redirect('trader/password')->with('status', 'Trader password updated successfully');
    }

    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required',
                    'g-recaptcha-response' => 'required'

        ]);
        $secretKey = env('CAPTCHA_SERVER_KEY');
        $captcha = $_POST['g-recaptcha-response'];
        // post request to server
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($captcha);
        $response = file_get_contents($url);
        $responseKeys = json_decode($response, true);

        if (empty($responseKeys["success"])) {
            return redirect()->back()->withInput($request->only('email', 'terms','g-recaptcha-response'))->with('error',trans('api.captcha_error'));
        }

        if(!$request->terms) {
             return redirect()->back()->with('error','Please accept terms and conditions')->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = TraderUser::where('email', $request->email)->first();
 
        if (empty($user)) {
                return redirect()->back()->with('error','Please enter valid email')->withInput();
        }
        $auth = Auth::guard('trader')->attempt(array("email" => $request->email,"password"=>$request->password));
        if($auth){
                $client  = @$_SERVER['HTTP_CLIENT_IP'];
                $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
                $remote  = $_SERVER['REMOTE_ADDR'];
            
                if(filter_var($client, FILTER_VALIDATE_IP))
                {
                    $ip = $client;
                }
                elseif(filter_var($forward, FILTER_VALIDATE_IP))
                {
                    $ip = $forward;
                }
                else
                {
                    $ip = $remote;
                }

                $log = new \App\TraderLogHistory();
                $log->ip = $ip;
                $log->type = 'login';
                $log->time = $this->UaeDate(Carbon::now());
                $log->trader_id = Auth::guard('trader')->user()->id;
                $log->save();
               if(Auth::guard('trader')->user()->status != 1){
                    Auth::guard('trader')->logout();
                    return redirect(session()->get('language').'/login')->with('error', trans('api.account_blocked'));
               }
               if(Auth::guard('trader')->user()->is_verify_email != 1){
                    Auth::guard('trader')->logout();
                    return redirect(session()->get('language').'/login')->with('error', trans('api.not_verify_email'));
                }
              $sessionId = $this->generateSessionId();
              $userid = Auth::guard('trader')->user()->id;
              TraderUser::where('id', $userid)->update(['session_id' => $sessionId]);
              session()->put('sessionId', $sessionId);
              return redirect(session()->get('language').'/home');
        } else {
             return redirect(session()->get('language').'/login')->with('error', 'Login credentials do not match any account');
        }
    }
}
