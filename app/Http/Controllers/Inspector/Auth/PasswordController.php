<?php

namespace App\Http\Controllers\Inspector\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Password as Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    protected $linkRequestView = 'inspector.auth.passwords.email';
    protected $resetView = 'inspector.auth.passwords.reset';
    protected $redirectTo = 'inspector';
    protected $guard = 'inspector';
    protected $broker = 'inspectors';
    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function sendResetLinkEmailApi(Request $request)
    {
        $api = new ApiController();
        $validator = Validator::make($request->all(),array('email' => 'required'));
        if ($validator->fails()) {
            return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
        }
        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink(
            $this->getSendResetLinkEmailCredentials($request),
            $this->resetEmailBuilder()
        );

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $api->successResponse(trans('api.reset_email'));
            case Password::INVALID_USER:
            default:
                return $api->errorResponse(trans('api.not_found'));
        }
    }
}
