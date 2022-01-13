<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;

class Authenticate
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
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson() || $guard == "api" || $guard == "trader_api") {
                $api = new ApiController();
                return $api->sessionExpireErrorResponse('Unauthorized.');
                // return $api->errorResponse('Unauthorized.');
            }
        }

        return $next($request);
    }
}
