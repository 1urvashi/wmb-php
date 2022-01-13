<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use File;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Page;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use Redirect;
use Gate;

class PagesController extends Controller
{
     public function __construct(){
        $user = Auth::guard('admin')->user();
     //    if(Gate::denies('termsMenu') && Gate::denies('faqMenu')){
     //         return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
     //    }
    }

   public function termsPage(){
     if(Gate::denies('page_terms-read')){
          return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
     }

	     $termsen = Page::where('slug', '=', 'terms')
						->where('language', '=', 'en')->first();

		$termsar = Page::where('slug', '=', 'terms')
						->where('language', '=', 'ar')->first();

        return view('admin.modules.pages.terms', compact('termsen', 'termsar'));
    }


    public function setTermsPage(Request $request) {
          if(Gate::denies('page_terms-update')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
		$page = Page::where('slug', '=', 'terms')->where('language', '=', 'en')->first();
          if(empty($page)){
               $page = new Page();
               $page->slug = 'terms';
               $page->language = 'en';
          }
		$page->title = $request->title;
		$page->body = $request->content;
		$page->save();

		$page = Page::where('slug', '=', 'terms')->where('language', '=', 'ar')->first();
          if(empty($page)){
               $page = new Page();
               $page->slug = 'terms';
               $page->language = 'ar';
          }
		$page->title = $request->title_ar;
		$page->body = $request->content_ar;
		$page->save();

        //return 1;
        return redirect('admin/terms')->with('status', 'Successfully updated content.');

	}

     public function faqPage(){
          if(Gate::denies('page_faq-read')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
  	     $termsen = Page::where('slug', '=', 'faq')
  						->where('language', '=', 'en')->first();

  		$termsar = Page::where('slug', '=', 'faq')
  						->where('language', '=', 'ar')->first();

          return view('admin.modules.pages.faq', compact('termsen', 'termsar'));
      }

      public function setFaqPage(Request $request) {
          if(Gate::denies('page_faq-update')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
               // return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
 		$page = Page::where('slug', '=', 'faq')->where('language', '=', 'en')->first();
           if(empty($page)){
                $page = new Page();
                $page->slug = 'faq';
                $page->language = 'en';
           }
 		$page->title = $request->title;
 		$page->body = $request->content;
 		$page->save();

 		$page = Page::where('slug', '=', 'faq')->where('language', '=', 'ar')->first();
           if(empty($page)){
                $page = new Page();
                $page->slug = 'faq';
                $page->language = 'ar';
           }
 		$page->title = $request->title_ar;
 		$page->body = $request->content_ar;
 		$page->save();

         //return 1;
         return redirect('admin/faq')->with('status', 'Successfully updated content.');

 	}


     public function about(){
          if(Gate::denies('page_about-read')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
  	     $dataen = Page::where('slug', '=', 'about')
  						->where('language', '=', 'en')->first();

  		$dataar = Page::where('slug', '=', 'about')
  						->where('language', '=', 'ar')->first();

          return view('admin.modules.pages.about', compact('dataen', 'dataar'));
      }

      public function setAboutPage(Request $request) {
          if(Gate::denies('page_about-update')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
 		$page = Page::where('slug', '=', 'about')->where('language', '=', 'en')->first();
           if(empty($page)){
                $page = new Page();
                $page->slug = 'about';
                $page->language = 'en';
           }
 		$page->title = $request->title;
 		$page->body = $request->content;
 		$page->save();

 		$page = Page::where('slug', '=', 'about')->where('language', '=', 'ar')->first();
           if(empty($page)){
                $page = new Page();
                $page->slug = 'about';
                $page->language = 'ar';
           }
 		$page->title = $request->title_ar;
 		$page->body = $request->content_ar;
 		$page->save();

         //return 1;
         return redirect('admin/about')->with('status', 'Successfully updated content.');

 	}



     public function privacyPolicy(){
          if(Gate::denies('page_privacy-read')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
  	     $dataen = Page::where('slug', '=', 'privacy')
  						->where('language', '=', 'en')->first();

  		$dataar = Page::where('slug', '=', 'privacy')
  						->where('language', '=', 'ar')->first();

          return view('admin.modules.pages.privacy', compact('dataen', 'dataar'));
      }

      public function setPrivacyPolicyPage(Request $request) {
          if(Gate::denies('page_privacy-update')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
 		$page = Page::where('slug', '=', 'privacy')->where('language', '=', 'en')->first();
           if(empty($page)){
                $page = new Page();
                $page->slug = 'privacy';
                $page->language = 'en';
           }
 		$page->title = $request->title;
 		$page->body = $request->content;
 		$page->save();

 		$page = Page::where('slug', '=', 'privacy')->where('language', '=', 'ar')->first();
           if(empty($page)){
                $page = new Page();
                $page->slug = 'privacy';
                $page->language = 'ar';
           }
 		$page->title = $request->title_ar;
 		$page->body = $request->content_ar;
 		$page->save();

         //return 1;
         return redirect('admin/privacy_policy')->with('status', 'Successfully updated content.');

 	}


     public function contact(){
          if(Gate::denies('page_contact-read')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
  	     $dataen = Page::where('slug', '=', 'contact')
  						->where('language', '=', 'en')->first();

  		$dataar = Page::where('slug', '=', 'contact')
  						->where('language', '=', 'ar')->first();

          return view('admin.modules.pages.contact', compact('dataen', 'dataar'));
      }

      public function setContactPage(Request $request) {
          if(Gate::denies('page_contact-update')){
               return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area.');
          }
 		$page = Page::where('slug', '=', 'contact')->where('language', '=', 'en')->first();
           if(empty($page)){
                $page = new Page();
                $page->slug = 'contact';
                $page->language = 'en';
           }
 		$page->title = $request->title;
 		$page->body = $request->content;
 		$page->save();

 		$page = Page::where('slug', '=', 'contact')->where('language', '=', 'ar')->first();
           if(empty($page)){
                $page = new Page();
                $page->slug = 'contact';
                $page->language = 'ar';
           }
 		$page->title = $request->title_ar;
 		$page->body = $request->content_ar;
 		$page->save();

         //return 1;
         return redirect('admin/contact')->with('status', 'Successfully updated content.');

 	}





}
