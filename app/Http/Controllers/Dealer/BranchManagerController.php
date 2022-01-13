<?php

namespace App\Http\Controllers\Dealer;

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

class BranchManagerController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('dealer.modules.branch_managers.index', compact('branches'));
    }

    public function data(Request $request) {
        $user = Auth::guard('dealer')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $dealers = DealerUser::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'name', 'email', 'contact', 'address', 'branch_id'])->where('branch_id', $user->id)->orderBy('id', 'desc')->get();
        return Datatables::of($dealers)
                        ->addColumn('action', function ($dealers) {
                            return '<a href="branch-managers/' . $dealers->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>
                   <a href="branch-managers/destroy/' . $dealers->id . '" onclick="return confirm(\'Are you sure you want to delete this Manager?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                        })
                        ->addColumn('branchName', function ($dealers) {
                            $branch = DealerUser::where('id', $dealers->branch_id)->first()->name;
                            return $branch;
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('dealer') && ($request->get('dealer') != 0)) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return ($row['branch_id'] == $request->get('dealer')) ? true : false;
                                });
                            }
                        })
                        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('dealer.modules.branch_managers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|max:255|unique:dealer_users',
                    'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = Auth::guard('dealer')->user();
        $manager = new DealerUser();
        $manager->branch_id = $user->id;
        $manager->name = $request->name;
        $manager->email = $request->email;
        $manager->password = bcrypt($request->password);
        $manager->address = $request->address;
        $manager->contact = $request->contact;
        $manager->save();
        $data = [];
        $data['account'] = "Branch Manager";
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = $request->password;
        try {
            $mail = Mail::send('emails.registration_dealer', $data, function ($message) use ($data) {
                        $message->to($data['email']);
                        $message->subject('Branch Manager Account Created');
                    });
        } catch (\Swift_TransportException $e) {
            Log::error($e->getMessage());
        }
        return redirect('dealer/branch-managers')->with('success', 'Successfully added new Branch Manager');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $edit = DealerUser::find($id);
        return view('dealer.modules.branch_managers.create', compact('edit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|max:255',
                        // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        // 'license_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $exist = DealerUser::where('email', $request->email)->where('id', '!=', $id)->first();
        if ($exist) {
            return redirect()->back()->with('error', 'This manager alredy exist!!');
        }
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $manager = DealerUser::find($id);
        // $manager->branch_id = $request->branch;
        $manager->name = $request->name;
        $manager->email = $request->email;
        if ($request->has('password')) {
            $manager->password = bcrypt($request->password);
        }
        $manager->address = $request->address;
        $manager->contact = $request->contact;
        $data = [];
        $data['account'] = "Branch Manager";
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = $request->password;
        if ($request->has('password')) {
            $data['account'] = 'Branch Manager';
            try {
                $mail = Mail::send('emails.registration_edit', $data, function ($message) use ($data) {
                            $message->to($data['email']);
                            $message->subject($data['account'] . ' Account Updated');
                        });
            } catch (\Swift_TransportException $e) {
                Log::error($e->getMessage());
            }
            unset($data['account']);
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }
        $manager->save();
        return redirect('dealer/branch-managers')->with('success', 'Successfully updated Branch Manager');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $dealer = DealerUser::findOrFail($id);

        if ($dealer->delete()) {
            \DB::table('dealer_users')->where('id', $dealer->id)->update(['session_id' => Null]);
            return redirect('dealer/branch-managers')->with('success', 'Branch Manager Deleted Successfully');
        }
        return redirect('dealer/branch-managers')->with('error', 'Something went wrong.Tray Again.');
    }

    public function export() {
        $user = Auth::guard('dealer')->user();
        $fileName = 'manegers';
        $dataExported = ['id', 'name', 'email', 'address', 'contact'];
        $maneges = DealerUser::where('id', '!=', 0)->where('branch_id', $user->id)->get($dataExported);
        $manegesArray = [];
        $manegesArray[] = $dataExported;
        foreach ($maneges as $manege) {
            $manegesArray[] = $manege->toArray();
        }
        Excel::create($fileName, function ($excel) use ($manegesArray) {
            $excel->setTitle('Maneges');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Maneges file');
            $excel->sheet('sheet1', function ($sheet) use ($manegesArray) {
                $sheet->fromArray($manegesArray, null, 'A1', false, false);
            });
        })->download('csv');
    }

}
