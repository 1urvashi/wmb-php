<?php

namespace App\Http\Controllers\Dealer\Auth;

use App\DealerUser;
use Validator;
use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller {
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

use AuthenticatesAndRegistersUsers,
    ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/dealer';
    protected $guard = 'dealer';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
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
    protected function create(array $data) {
        return DealerUser::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
        ]);
    }

    public function showLoginForm() {
        if (Auth::guard('dealer')->check()) {
            return redirect('/dealer');
        }

        return view('dealer.auth.login');
    }

    public function showRegistrationForm() {
        return view('dealer.auth.register');
    }

    public function resetPassword() {
        return view('dealer.auth.passwords.email');
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
        $auth = Auth::guard('dealer')->attempt(array("email" => $request->email, "password" => $request->password));

        if ($auth) {
            $user = Auth::guard('dealer')->user();
            $user->session_id = $this->generateSessionId();
            $user->save();

        //     if(Auth::guard('dealer')->user()->status != 1){
        //         Auth::guard('dealer')->logout();
        //         return redirect('/dealer/login')->with('error', trans('api.account_blocked'));
        //    }
        //    if(Auth::guard('dealer')->user()->is_verify_email != 1){
        //         Auth::guard('dealer')->logout();
        //         return redirect('/dealer/login')->with('error', trans('api.not_verify_email'));
        //     }

            session()->put('dealerSessionId', $user->session_id);
            return redirect('dealer');
        } else {
            return redirect('/dealer/login')->with('error', 'Login credentials do not match any account');
        }
    }

    public function logout() {
        $user = Auth::guard('dealer')->user();
        $user->session_id = Null;
        $user->save();

        Auth::guard('dealer')->logout();
        session()->forget('dealerSessionId');
        return redirect('/dealer/login');
    }

    public function changePassword() {
        $edit = Auth::guard('dealer')->user();
        return view('dealer.auth.passwords.change', compact('edit'));
    }

    public function updatePassword(Request $request) {
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
        $users = DealerUser::find($id);
        $users->password = bcrypt($request->password);
        $users->save();
        return redirect('dealer/password')->with('status', 'Dealer password updated successfully');
    }
    public function getProfile(){
        $dealer = Auth::guard('dealer')->user();
        return view('dealer.auth.update-profile', compact('dealer'));
    }
    public function updateProfile(Request $request){
        $id = $request->userId;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|max:255|unique:dealer_users,email,'.$id.',id,deleted_at,NULL',

        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $users = DealerUser::find($id);
        $users->name = $request->name;
        $users->email = $request->email;
        $users->address = $request->address;
        $users->contact = $request->contact;
        $users->save();

        return redirect('dealer/get-dealer-profile')->with('status', 'Dealer profile updated successfully');


    }

}
