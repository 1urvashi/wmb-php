<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use Auth;
use Validator;
use App\User;
use App\Version;
use Redirect;
use Gate;

class VersionController extends Controller
{
	public function __construct(){
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('settings_version-control')){
        //      Redirect::to('admin')->send()->with('error', 'You dont have sufficient privlilege to access this area');
        // }
     }

	public function ViewVersion() {
        if(Gate::denies('settings_version-control')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

		$ios = Version::where('type', '=', 'ios')->first();
		$android = Version::where('type', '=', 'android')->first();

		$ipad = Version::where('type', '=', 'ipad')->first();

		if(empty($ipad)){
		   $ipad = new Version();
	  	   $ipad->url = '';
	  	   $ipad->type = 'ipad';
	  	   $ipad->version_number = 1;
	  	   $ipad->status = 0;
	  	   $ipad->save();
		}

		// return view('admin.modules.manager.manager-edit', compact('edit'));
        return view('admin.modules.version.index', compact('ios', 'android','ipad'));
    }

	public function VersionUpdate(Request $request) {
        if(Gate::denies('settings_version-control')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
		$ios = Version::where('type', '=', 'ios')->first();
		$ios->url = $request->iosUrl;
		$ios->message = $request->iosMessage;
		$ios->version_number = $request->iosVersion;
		$ios->status = $request->iosVersionStatus;
        $ios->save();


		$android = Version::where('type', '=', 'android')->first();
		$android->url = $request->androidUrl;
		$android->message = $request->androidMessage;
		$android->version_number = $request->androidVersion;
		$android->status = $request->androidVersionStatus;
        $android->save();

	   $ipad = Version::where('type', '=', 'ipad')->first();
	   $ipad->url = $request->ipadUrl;
	   $ipad->message = $request->ipadMessage;
	   $ipad->version_number = $request->ipadVersion;
	   $ipad->status = $request->ipadVersionStatus;
	   $ipad->save();


		return back()->with('status', 'Successfully updated');


	}



    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //public function store(Request $request)
    //{
        //
    //}

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
