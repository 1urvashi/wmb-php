<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
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

class DRMController extends Controller
{
    public function __construct()
    {
        $user = Auth::guard('admin')->user();
        // if (Gate::denies('drmView')) {
        //     return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('DRM_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.drm.index');
    }



    public function data(Request $request)
    {
         $drm = new User();
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $drmUsers = User::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','name', 'email','mobile', 'status'])
                        // ->where('role', $drm->getDRM())
                        ->where('type', config('globalConstants.TYPE.DRM'))
                        ->orderBy('id', 'desc')->get();
        return Datatables::of($drmUsers, $user)
       ->editColumn('action', function ($drmUsers) use ($user) {
           $b = '';
           if (Gate::allows('DRM_read')) {
               $b = '<a href="drmusers/' . $drmUsers->id.'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View</a> &nbsp;';
           }
           if (Gate::allows('DRM_update')) {
               $b .= '<a href="drmusers/' . $drmUsers->id . '/edit"  class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>&nbsp;';
           }
           if (Gate::allows('DRM_delete')) {
               $b .= '<a href="drmusers/destroy/' . $drmUsers->id . '" onclick="return confirm(\'Are you sure you want to delete this DRM User?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
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
        if (Gate::denies('DRM_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.drm.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('DRM_create')) {
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
        $users = new User();
        $drmrole = $users->getDRM();
        $exist = User::where('name', $request->name)->where('email', $request->email)->where('role', $drmrole)->first();

        if($exist) {
             return redirect()->back()->with('error', 'Already Exist the user')->withInput();
        }

        $drmUser = new User();
        $drmUser->name = $request->name;
        $drmUser->email = $request->email;
        $drmUser->mobile = $request->mobile;
        $drmUser->role = $drmUser->getDRM();
        $drmUser->type = config('globalConstants.TYPE.DRM');
        $drmUser->password = bcrypt($request->password);
        $image = $request->file('image');
        $path = 'users/';
        $dir = config('app.fileDirectory') . $path;
        if($image){
             $img = Image::make($image);
             $timestamp = Date('y-m-d-H-i-s');
             $str = str_random(5);
             $name = $timestamp.'-'.$str. $image->getClientOriginalName();

             Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
             $drmUser->image = $name;

            // $timestamp = Date('y-m-d-H-i-s');
            // $str = str_random(5);
            // $name = $timestamp . $image->getClientOriginalName();
            // $drmUser->image = $name;
            // $image->move(public_path() . '/uploads/drmusers/', $name);
        }
        $data['email'] = $request->email;
        $data['name'] = $request->name;
        $data['password'] = $request->password;
        try {
            $mail =Mail::send('emails.drmusers.registration', $data, function($message) use ($data) {
                $message->to($data['email']);
                $message->subject('Admin Account Created');
            });
         }  catch (\Swift_TransportException $e){
            Log::error($e->getMessage());
         }
        $drmUser->save();
        $role_user = new RoleUser();
        $role_user->user_id =  $drmUser->id;
        $role_user->role_id =  config('globalConstants.ROLES.DRM_USER');
        $role_user->save();
        return redirect('drmusers')->with('success', 'Successfully added new DRM User');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('DRM_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
         $data = User::find($id);
        return view('admin.modules.drm.show',  compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('DRM_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $data = User::find($id);
        return view('admin.modules.drm.create',  compact('data'));
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
        if (Gate::denies('DRM_update')) {
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

       // $users = new User();
       // $drmrole = $users->getDRM();

       $exist = User::where('id', '!=', $id)->where('email', $request->email)->first();

       if($exist) {
            return redirect()->back()->with('error', 'Already Exist the user')->withInput();
       }

       $drmUser = User::find($id);
       $drmUser->name = $request->name;
       $drmUser->email = $request->email;
       $drmUser->mobile = $request->mobile;
       $drmUser->role = 9;
       if($request->password) {
            $drmUser->password = bcrypt($request->password);
       }
       $image = $request->file('image');
       $path = 'users/';
       $dir = config('app.fileDirectory') . $path;
       if($image){

            if(!empty($drmUser->getOriginal('image'))){
                 Storage::disk('s3')->delete($dir . $drmUser->getOriginal('image'));
            }

            $img = Image::make($image);
            $timestamp = Date('y-m-d-H-i-s');
            $str = str_random(5);
            $name = $timestamp.'-'.$str. $image->getClientOriginalName();

            Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
            $drmUser->image = $name;

          // $timestamp = Date('y-m-d-H-i-s');
          // $str = str_random(5);
          // $name = $timestamp . $image->getClientOriginalName();
          // $drmUser->image = $name;
          // $image->move(public_path() . '/uploads/drmusers/', $name);
       }
       $drmUser->save();
        $role_user = RoleUser::where('user_id', $drmUser->id)->first();
        if(empty($role_user))
            $role_user = new RoleUser();  {
            $role_user->user_id =  $drmUser->id;
            $role_user->role_id =  config('globalConstants.ROLES.DRM_USER');
            $role_user->save();
        }
        return redirect('drmusers')->with('success', 'Successfully updated the DRM User');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('DRM_delete')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $trader = \App\TraderUser::where('dmr_id', $id)->count();
        if($trader != 0) {
             return redirect()->back()->with('error', 'The DRM user can not delete, Because some trader related this DRM');
        }
        User::destroy($id);
        return redirect('drmusers')->with('success', 'Successfully updated the DRM User');
    }

    public function publish(Request $request, $id) {
         $attribute = User::find($request->dataId);
         $attribute->status = $request->dataValue;
         $attribute->session_id = null;
         $attribute->save();
         return;
    }

    public function export(){
        if (Gate::denies('DRM_export')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $user = new User();
        $drm = $user->getDRM();
        $fileName = 'drm_maneger_'.time();
        $dataExported = ['id', 'name','email', 'mobile'];
        $datas = User::where('id','!=',0)->where('role', $drm)->get($dataExported);
        $dataArray = [];
        $dataArray[] = $dataExported;
        foreach ($datas as $data) {
            $dataArray[] = $data->toArray();
        }
        Excel::create($fileName, function($excel) use ($dataArray) {
            $excel->setTitle('DRM');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('DRM file');
            $excel->sheet('sheet1', function($sheet) use ($dataArray) {
                $sheet->fromArray($dataArray, null, 'A1', false, false);
            });

        })->download('csv');
    }
}
