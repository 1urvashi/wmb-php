<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admin') {
        if ($request->ajax() && !Auth::guard($guard)->check()) {
            return response()->json(["status" => "expired"], 500);
        }
        if (!Auth::guard($guard)->check()) {
            return redirect('/admin/login');
        }

        $user = Auth::guard('admin')->user();
        $sessionId = session()->get('adminSessionId');

        if ((!empty($sessionId)) && ($user->session_id != $sessionId)) {
            Auth::guard('admin')->logout();
            if ($request->ajax()) {
                return response()->json(["status" => "expired"], 500);
            }
            return redirect('/admin/login')->with('error', trans('api.session_expire'));
        }

        return $next($request);
    }

}
