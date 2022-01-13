<?php

namespace App\Http\Controllers\Admin\Auth;

use App\User;
use Auth;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Gate;
use Carbon\Carbon;
use Mail;

class AuthController extends Controller
{
    public $adminEmail = ['fc@watchmybid.com'];
    //public $adminEmail = ['niyas@mobiiworld.com', 'niyaspulath@gmail.com'];


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
     protected $redirectTo = '/admin';
     protected $guard = 'admin';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function showLoginForm()
    {
            if (Auth::guard('admin')->check())
            {
                    return redirect('/admin');
            }

            return view('admin.auth.login');
    }

    public function showRegistrationForm()
    {
            return view('admin.auth.register');
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
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $auth = Auth::guard('admin')->attempt(array("email" => $request->email,"password"=>$request->password));



        if($auth){
            $user = Auth::guard('admin')->user();
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

            $log = new \App\AdminLogHistory();
            $log->ip = $ip;
            $log->type = 'login';
            $log->time = $this->UaeDate(Carbon::now());
            $log->user_id = $user->id;
            $log->save();
               if($user->status != 1){
                    Auth::guard('admin')->logout();
                    return redirect('admin/login')->with('error', trans('api.account_blocked'));
               }

            $user->session_id = $this->generateSessionId();
            $user->save();
            session()->put('adminSessionId', $user->session_id);
            return redirect('admin');
        } else {
             return redirect('admin/login')->with('error', 'Login credentials do not match any account');
        }
    }

    public function resetPassword()
    {
            return view('admin.auth.passwords.email');
    }

    public function logout(Request $request){
            $user = Auth::guard('admin')->user();
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

            $log = new \App\AdminLogHistory();
            $log->ip = $ip;
            $log->type = 'logout';
            $log->time = $this->UaeDate(Carbon::now());
            $log->user_id = $user->id;
            $log->save();

            $sessionId = session()->get('adminSessionId');
            if($sessionId == $user->session_id){
               $user->session_id = Null;
               $user->save();
            }


            Auth::guard('admin')->logout();
            session()->forget('adminSessionId');

            return redirect('/admin/login');
    }

    public function changePassword()
    {
        if(Gate::denies('settings_password-change')){
            Redirect::to('admin')->send()->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $edit = Auth::guard('admin')->user();
        return view('admin.auth.passwords.change', compact('edit'));
    }

    public function updatePassword(Request $request)
    {
        if(Gate::denies('settings_password-change')){
            Redirect::to('admin')->send()->with('error', 'You dont have sufficient privlilege to access this area');
        }
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
        $users = User::find($id);
        $users->password = bcrypt($request->password);
        $users->save();


        $data = array('name' => $request->name, 'email' => $request->email, 'password' => $request->password);
        $email = $this->adminEmail;
            $mail = Mail::send('admin.emails.password_change', $data, function($message) use ($request, $email) {
                        $message->from(env('MAIL_FROM_ADDRESS'));
                        $message->to($email)
                                ->subject('Login credentials for WatchMyBid');
                    });
        if ($mail) {
            return redirect('admin/password')->with('status', 'Admin password updated successfully');
        } else {
            return redirect('admin/password')->with('status', 'Admin password updated successfully');
        }

    }

}
