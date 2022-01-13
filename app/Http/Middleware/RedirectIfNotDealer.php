<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotDealer {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'dealer') {
        if (!Auth::guard($guard)->check()) {
            if ($request->ajax()) {
                return response()->json(["status" => "expired"], 500);
            }
            return redirect('/dealer/login');
        }

        $user = Auth::guard('dealer')->user();
        $sessionId = session()->get('dealerSessionId');

        if ((!empty($sessionId)) && ($user->session_id != $sessionId)) {
            Auth::guard('dealer')->logout();
            if ($request->ajax()) {
                return response()->json(["status" => "expired"], 500);
            }
            return redirect('/dealer/login')->with('error', trans('api.session_expire'));
        }

        return $next($request);
    }

}
