<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Page;

class CmsController extends Controller
{

	public function getTerms()
    {

		$faqen = Page::where('slug', '=', 'terms')->where('language', '=', 'en')->first();

		$faqar = Page::where('slug', '=', 'terms')->where('language', '=', 'ar')->first();

		 if(session()->get('language') != 'ar'){
		   		$title = $faqen['title'];
				$content =$faqen['body'];
		   }else{
			   $title = $faqar['title'];
			   $content =$faqar['body'];
		   }

		return view('webviews.pages.terms', compact('content', 'title'));
    }

    public function getFaq()
   {

	    $faqen = Page::where('slug', '=', 'faq')->where('language', '=', 'en')->first();

	    $faqar = Page::where('slug', '=', 'faq')->where('language', '=', 'ar')->first();

		if(session()->get('language') != 'ar'){
			    $title = $faqen['title'];
			    $content =$faqen['body'];
		  }else{
			  $title = $faqar['title'];
			  $content =$faqar['body'];
		  }

	    return view('webviews.pages.faq', compact('content', 'title'));
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
