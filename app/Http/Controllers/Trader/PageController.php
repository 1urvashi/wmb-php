<?php

namespace App\Http\Controllers\Trader;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Carbon\Carbon;
use App\Page;
use Auth;

class PageController extends Controller
{
    public function about()
    {
         $user = Auth::guard('trader')->user();
         $sessionId = session()->get('sessionId');
         if( (!empty($sessionId)) && ($user->session_id != $sessionId) ) {
            Auth::guard('trader')->logout();
            return redirect(session()->get('language').'/login')->with('error', trans('api.session_expire'));
         }
        $dataen = Page::where('slug', '=', 'about')->where('language', '=', 'en')->first();

        $dataar = Page::where('slug', '=', 'about')->where('language', '=', 'ar')->first();

        if (session()->get('language') != 'ar') {
            $title = $dataen['title'];
            $content =$dataen['body'];
        } else {
            $title = $dataar['title'];
            $content =$dataar['body'];
        }

        return view('trader.pages.about', compact('content', 'title'));
    }
    public function contact()
    {
         $user = Auth::guard('trader')->user();
         $sessionId = session()->get('sessionId');
         if( (!empty($sessionId)) && ($user->session_id != $sessionId) ) {
            Auth::guard('trader')->logout();
            return redirect(session()->get('language').'/login')->with('error', trans('api.session_expire'));
         }
        //return 1;
        $dataen = Page::where('slug', '=', 'contact')->where('language', '=', 'en')->first();

        $dataar = Page::where('slug', '=', 'contact')->where('language', '=', 'ar')->first();

        if (session()->get('language') != 'ar') {
            $title = $dataen['title'];
            $content =$dataen['body'];
        } else {
            $title = $dataar['title'];
            $content =$dataar['body'];
        }

        return view('trader.pages.contact', compact('content', 'title'));
    }
    public function faq()
    {
         $user = Auth::guard('trader')->user();
         $sessionId = session()->get('sessionId');
         if( (!empty($sessionId)) && ($user->session_id != $sessionId) ) {
            Auth::guard('trader')->logout();
            return redirect(session()->get('language').'/login')->with('error', trans('api.session_expire'));
         }
        //return 1;
        $faqen = Page::where('slug', '=', 'faq')->where('language', '=', 'en')->first();

        $faqar = Page::where('slug', '=', 'faq')->where('language', '=', 'ar')->first();

        if (session()->get('language') != 'ar') {
            $title = $faqen['title'];
            $content =$faqen['body'];
        } else {
            $title = $faqar['title'];
            $content =$faqar['body'];
        }

        return view('trader.pages.faq', compact('content', 'title'));
    }
    public function privacyPolicy()
    {
         $user = Auth::guard('trader')->user();
         $sessionId = session()->get('sessionId');
         if( (!empty($sessionId)) && ($user->session_id != $sessionId) ) {
            Auth::guard('trader')->logout();
            return redirect(session()->get('language').'/login')->with('error', trans('api.session_expire'));
         }
        $dataen = Page::where('slug', '=', 'privacy')->where('language', '=', 'en')->first();

        $dataar = Page::where('slug', '=', 'privacy')->where('language', '=', 'ar')->first();

        if (session()->get('language') != 'ar') {
            $title = $dataen['title'];
            $content =$dataen['body'];
        } else {
            $title = $dataar['title'];
            $content =$dataar['body'];
        }

        return view('trader.pages.privacy_policy', compact('content', 'title'));
    }
    public function termsService()
    {
         $user = Auth::guard('trader')->user();
         $sessionId = session()->get('sessionId');
         if( (!empty($sessionId)) && ($user->session_id != $sessionId) ) {
            Auth::guard('trader')->logout();
            return redirect(session()->get('language').'/login')->with('error', trans('api.session_expire'));
         }
        //return 1;
        $faqen = Page::where('slug', '=', 'terms')->where('language', '=', 'en')->first();

        $faqar = Page::where('slug', '=', 'terms')->where('language', '=', 'ar')->first();

        if (session()->get('language') != 'ar') {
            $title = $faqen['title'];
            $content =$faqen['body'];
        } else {
            $title = $faqar['title'];
            $content =$faqar['body'];
        }

        return view('trader.pages.terms_service', compact('content', 'title'));
        // return view('trader.pages.terms_service');
    }
}
