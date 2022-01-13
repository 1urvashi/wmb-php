<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotApiValidated
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
                if($request->has('language')){
                    $language = $api->setApiLanguage($request->language);
                }else {
                        return $api->errorResponse(trans('api.api_validate'));
                }
                return $next($request);
            /*}else {
                return $api->errorResponse('Cannot access Api');
            }*/
	}
}