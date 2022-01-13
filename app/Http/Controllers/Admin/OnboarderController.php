<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mail;
use GuzzleHttp;
use Excel;
use Auth;
use Redirect;
use Validator;
use Datatables;
use DB;
use File;
use App\User;
use URL;
use Image;
use Storage;
use Gate;
use App\RoleUser;
use App\Role;

class OnboarderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('Onboarder_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.onboarder.index');
    }

    public function data(Request $request)
    {
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $datas = User::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','name', 'email','mobile', 'status'])
                        ->where('type', config('globalConstants.TYPE.ONBOARDER'))
                        ->orderBy('id', 'desc')->get();
        return Datatables::of($datas, $user)
       ->editColumn('action', function ($datas) use ($user) {
           $b = '';
           if (Gate::allows('Onboarder_update')) {
               $b .= '<a href="onboarder-users/' . $datas->id . '/edit"  class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>&nbsp;';
           }
           if (Gate::allows('Onboarder_delete')) {
               $b .= '<a href="onboarder-users/destroy/' . $datas->id . '" onclick="return confirm(\'Are you sure you want to delete this Onboarder User?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
           }
           return $b;
       })->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('Onboarder_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.onboarder.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('Onboarder_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mobile' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $exist = User::where('name', $request->name)->where('email', $request->email)->first();

        if ($exist) {
            return redirect()->back()->with('error', 'Already exist the user')->withInput();
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->role = null;
        $user->type = config('globalConstants.TYPE.ONBOARDER');
        $user->password = bcrypt($request->password);
        $image = $request->file('image');
        $path = 'users/';
        $dir = config('app.fileDirectory') . $path;
        if ($image) {
            $img = Image::make($image);
            $timestamp = Date('y-m-d-H-i-s');
            $str = str_random(5);
            $name = $timestamp.'-'.$str. $image->getClientOriginalName();

            Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
            $user->image = $name;
        }
        $data['email'] = $request->email;
        $data['name'] = $request->name;
        $data['password'] = $request->password;

        if ($request->has('password')) {
            try {
                $mail =Mail::send('emails.onboarder.registration', $data, function ($message) use ($data) {
                    $message->to($data['email']);
                    $message->subject('Admin account created');
                });
            } catch (\Swift_TransportException $e) {
                Log::error($e->getMessage());
            }
        }

        $user->save();
        $role_user = new RoleUser();
        $role_user->user_id =  $user->id;
        $role_user->role_id =  config('globalConstants.ROLES.ONBOARDER');
        $role_user->save();
        return redirect('onboarder-users')->with('success', 'Successfully added new onboarder');
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
        if (Gate::denies('Onboarder_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $data = User::find($id);
        return view('admin.modules.onboarder.create',  compact('data'));
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
        if (Gate::denies('Onboarder_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
 
        $exist = User::where('id', '!=', $id)->where('email', $request->email)->first();
 
        if($exist) {
            return redirect()->back()->with('error', 'Already exist the user')->withInput();
        }
 
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->role = null;
        $user->type = config('globalConstants.TYPE.ONBOARDER');
        if($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        $image = $request->file('image');
        $path = 'users/';
        $dir = config('app.fileDirectory') . $path;

        if($image){
 
            if(!empty($user->getOriginal('image'))){
                Storage::disk('s3')->delete($dir . $user->getOriginal('image'));
            }

            $img = Image::make($image);
            $timestamp = Date('y-m-d-H-i-s');
            $str = str_random(5);
            $name = $timestamp.'-'.$str. $image->getClientOriginalName();

            Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
            $user->image = $name;
        }

        $user->save();
        $role_user = RoleUser::where('user_id', $user->id)->first();
        if(empty($role_user))
             $role_user = new RoleUser();  {
             $role_user->user_id =  $user->id;
             $role_user->role_id =  config('globalConstants.ROLES.ONBOARDER');
             $role_user->save();
        }
        return redirect('onboarder-users')->with('success', 'Successfully updated the onboarder user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('Onboarder_delete')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $trader = \App\TraderUser::where('onboarder_id', $id)->count();
        if($trader != 0) {
             return redirect()->back()->with('error', 'The onboarder user can not delete, Because some trader related this onboarder');
        }
        User::destroy($id);
        return redirect('onboarder-users')->with('success', 'Successfully updated the onboarder');
    }

    public function publish(Request $request, $id) {
        $attribute = User::find($request->dataId);
        $attribute->status = $request->dataValue;
        $attribute->session_id = null;
        $attribute->save();
        return;
    }

    public function export(){
        if (Gate::denies('Onboarder_export')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $fileName = 'onboarder_'.time();
        $dataExported = ['id', 'name','email', 'mobile'];
        $datas = User::where('id','!=',0)->where('type', config('globalConstants.TYPE.ONBOARDER'))->where('status', 1)->get($dataExported);
        $dataArray = [];
        $dataArray[] = $dataExported;
        foreach ($datas as $data) {
            $dataArray[] = $data->toArray();
        }
        Excel::create($fileName, function($excel) use ($dataArray) {
            $excel->setTitle('Onboarder');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Onboarder file');
            $excel->sheet('sheet1', function($sheet) use ($dataArray) {
                $sheet->fromArray($dataArray, null, 'A1', false, false);
            });

        })->download('csv');
    }
}
