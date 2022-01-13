<?php

namespace App\Http\Controllers\Inspector\Auth;

use App\InspectorUser;
use Validator;
use Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

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
     protected $redirectTo = '/inspector';
     protected $guard = 'inspector';

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
        return InspectorUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function showLoginForm()
    {
            if (Auth::guard('inspector')->check())
            {
                    return redirect('/inspector');
            }

            return view('inspector.auth.login');
    }

    public function showRegistrationForm()
    {
            return view('inspector.auth.register');
    }

    public function resetPassword()
    {
            return view('inspector.auth.passwords.email');
    }

    public function logout(){
            Auth::guard('inspector')->logout();
            return redirect('/inspector/login');
    }
    
    public function changePassword() 
    {
        $edit = Auth::guard('inspector')->user();
        return view('inspector.auth.passwords.change', compact('edit'));    
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
        $users = InspectorUser::find($id);
        $users->password = bcrypt($request->password);
        $users->save();
        return redirect('inspector/password')->with('status', 'Inspector password updated successfully');
    }
}
