<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Auth;
use Validator;
use Datatables;
use DB;
use App\DealerUser;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mail;
use File;
use Excel;
use GuzzleHttp;
use Redirect;
use Storage;
use Image;
use Gate;

class DealersController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $user = Auth::guard('admin')->user();
        //     if(Gate::denies('branches_read')){
        //          return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        //     } elseif(Gate::denies('branches_create')) {
        //         return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        //    }
    }

    public function index() {
        if (Gate::denies('branches_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.dealer.index');
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data() {
        DB::statement(DB::raw('set @rownum=0'));
        $dealers = DealerUser::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'name', 'email', 'contact', 'address'])->where('branch_id', 0)->orderBy('id', 'desc')->get();
        return Datatables::of($dealers)
                        ->addColumn('action', function ($dealers) {
                            $a = '';
                            if (Gate::allows('branches_update')) {
                                $a .= '<a href="dealers/' . $dealers->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> &nbsp;';
                            }
                            if (Gate::allows('branches_delete')) {
                                $a .= '<a href="dealers/destroy/' . $dealers->id . '" onclick="return confirm(\'Are you sure you want to delete this Dealer?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                            }
                            return $a;
                        })
                        ->make(true);
    }

    public function publish(Request $request, $id) {

        $attribute = DealerUser::find($request->dataId);
        if($request->data_action_type == "status"){
            $attribute->status = $request->dataValue;
        }else{
            $attribute->is_verify_email = $request->dataValue;
        }

        $attribute->session_id = '';
        $attribute->save();
        return;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        if (Gate::denies('branches_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.dealer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Gate::denies('branches_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|max:255|unique:dealer_users',
                    'password' => 'required|min:6'
                        // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        // 'license_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        // 'license' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $dealer = new DealerUser();
        $data = $request->all();
        $data['account'] = 'Dealer';
        $image = $request->file('image');
        $path = 'dealers/';
        $dir = config('app.fileDirectory') . $path;
        if ($image) {

            $img = Image::make($image);
            $timestamp = Date('y-m-d-H-i-s');
            $str = str_random(5);
            $name = $timestamp . '-' . $str . $image->getClientOriginalName();

            Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
            $data['image'] = $name;

            // $timestamp = Date('y-m-d-H-i-s');
            // $str = str_random(5);
            // $name = $timestamp . $image->getClientOriginalName();
            // $data['image'] = $name;
            // $image->move(public_path() . '/uploads/dealers/', $name);
        }

        $image = $request->file('license_image');
        $path = 'dealers_license/';
        $dir = config('app.fileDirectory') . $path;
        if ($image) {
            $img = Image::make($image);
            $timestamp = Date('y-m-d-H-i-s');
            $str = str_random(5);
            $name = $timestamp . '-' . $str . $image->getClientOriginalName();
            Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
            $data['license_image'] = $name;

            // $timestamp = Date('y-m-d-H-i-s');
            // $str = str_random(5);
            // $name = $timestamp . $image->getClientOriginalName();
            // $data['license_image'] = $name;
            // $image->move(public_path() . '/uploads/dealers/license_image/', $name);
        }

        try {
            $mail = Mail::send('emails.registration_dealer', $data, function($message) use ($data) {
                        $message->to($data['email']);
                        $message->subject($data['account'] . ' Account Created');
                    });
        } catch (\Swift_TransportException $e) {
            Log::error($e->getMessage());
        }
        unset($data['account']);
        $data['password'] = bcrypt($request->password);
        $dealer->create($data);
        return redirect('dealers')->with('success', 'Successfully added new Branch');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DealerUser $dealer) {
        if (Gate::denies('branches_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.dealer.show', compact('dealer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(DealerUser $dealer) {
        if (Gate::denies('branches_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.dealer.create', compact('dealer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DealerUser $dealer) {
        if (Gate::denies('branches_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|max:255',
                        // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        // 'license_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $exist = DealerUser::where('email', $request->email)->where('id', '!=', $dealer->id)->first();
        if ($exist) {
            return redirect()->back()->with('error', 'This manager alredy exist!!');
        }
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $request->all();
        $image = $request->file('image');
        $path = 'dealers/';
        $dir = config('app.fileDirectory') . $path;
        if ($image) {

            if (!empty($dealer->getOriginal('image'))) {
                Storage::disk('s3')->delete($dir . $dealer->getOriginal('image'));
            }

            $img = Image::make($image);
            $timestamp = Date('y-m-d-H-i-s');
            $str = str_random(5);
            $name = $timestamp . '-' . $str . $image->getClientOriginalName();

            Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
            $data['image'] = $name;
            // File::delete('uploads/dealers/'. $dealer->image);
            // $timestamp = Date('y-m-d-H-i-s');
            // $str = str_random(5);
            // $name = $timestamp . $image->getClientOriginalName();
            // $data['image'] = $name;
            // $image->move(public_path() . '/uploads/dealers/', $name);
        }
        $image = $request->file('license_image');
        $path = 'dealers_license/';
        $dir = config('app.fileDirectory') . $path;
        if ($image) {

            if (!empty($dealer->getOriginal('license_image'))) {
                Storage::disk('s3')->delete($dir . $dealer->getOriginal('license_image'));
            }

            $img = Image::make($image);
            $timestamp = Date('y-m-d-H-i-s');
            $str = str_random(5);
            $name = $timestamp . '-' . $str . $image->getClientOriginalName();

            Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
            $data['license_image'] = $name;

            // $timestamp = Date('y-m-d-H-i-s');
            // $str = str_random(5);
            // $name = $timestamp . $image->getClientOriginalName();
            // $data['license_image'] = $name;
            // $image->move(public_path() . '/uploads/dealers/license_image/', $name);
        }
        if ($request->has('password')) {
            $data['account'] = 'Dealer';
            $data['password'] = bcrypt($request->password);
            try {
                $mail = Mail::send('emails.registration_edit', $data, function($message) use ($data) {
                            $message->to($data['email']);
                            $message->subject($data['account'] . ' Account Updated');
                        });
            } catch (\Swift_TransportException $e) {
                Log::error($e->getMessage());
            }
            unset($data['account']);
       
        } else {
           // unset($data['password']);
        }
        $dealer->update($data);
        return redirect('dealers')->with('success', 'Successfully updated Dealer');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Gate::denies('branches_delete')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealer = DealerUser::findOrFail($id);
        $inspector = \App\InspectorUser::where('dealer_id', $id)->count();
        $manager = DealerUser::where('branch_id', $id)->count();
        // if ($manager != 0 || $inspector != 0) {
        //     return redirect()->back()->with('error', 'You cant delete the branch because, inspector and manager related this branch');
        // }

        if ($dealer->delete()) {
            \DB::table('dealer_users')->where('id', $dealer->id)->update(['session_id' => Null]);
            return redirect('dealers')->with('success', 'Dealer Deleted Successfully');
        }
        return redirect('dealers')->with('error', 'Something went wrong.Tray Again.');
    }

    public function export() {
        if (Gate::denies('branches_export')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $fileName = 'dealers_' . time();
        $dataExported = ['id', 'name', 'email', 'address', 'contact'];
        $maneges = DealerUser::where('id', '!=', 0)->get($dataExported);
        // if($dealerId && $dealerId > 0){
        //   $fileName = $fileName.'-'.DealerUser::where('id',$dealerId)->first()->name;
        //   $traders = TraderUser::where('dealer_id',$dealerId)->get($dataExported);
        // }
        //dd($traders);
        $manegesArray = [];
        $manegesArray[] = $dataExported;
        foreach ($maneges as $manege) {
            $manegesArray[] = $manege->toArray();
        }
        Excel::create($fileName, function($excel) use ($manegesArray) {
            $excel->setTitle('Maneges');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Maneges file');
            $excel->sheet('sheet1', function($sheet) use ($manegesArray) {
                $sheet->fromArray($manegesArray, null, 'A1', false, false);
            });
        })->download('csv');
    }

}
