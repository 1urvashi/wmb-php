<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App;
use Config;
use Validator;
class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $rules = [
        'language' => 'in:en,ar' //list of supported languages of your application.
        ];

        $language = $request->lang;

        $validator = Validator::make(compact($language),$rules);

        if($validator->passes()){
            Session::put('language',$language);
            App::setLocale($language);
        }else{
            Session::put('language','en');
        }

        return $next($request);
    }
}
