<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdminOrDealer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
	{
		if (!(Auth::guard('admin')->check() || Auth::guard('dealer')->check())) {                    
			return redirect('/admin/login');
		}
                
		return $next($request);
	}
}