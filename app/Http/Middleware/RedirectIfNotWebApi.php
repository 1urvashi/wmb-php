<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotWebApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = '')
	{
            $api = new ApiController();
            //if ($request->ajax() || $request->wantsJson()) {
                if($request->has('api_token') && $request->has('language')){
                    $language = $api->setApiLanguage($request->language);
                    if($request->api_token != env('API_TOKEN')){
                        return $api->errorResponse(trans('api.token_error'));
                    }
                }else {
                        return $api->errorResponse(trans('api.web_api_validate'));
                }
                return $next($request);
            /*}else {
                return $api->errorResponse('Cannot access Api');
            }*/
	}
}