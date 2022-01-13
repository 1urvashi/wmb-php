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

class BranchManagerController extends Controller {

    public function __construct() {
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('branchManagerMenu')){
        //      return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Gate::denies('branch-managers_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $branches = DealerUser::where('branch_id', 0)->get();
        return view('admin.modules.branch_managers.index', compact('branches'));
    }

    public function data(Request $request) {
        DB::statement(DB::raw('set @rownum=0'));
        $dealers = DealerUser::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'name', 'email', 'contact', 'address', 'branch_id'])->where('branch_id', '!=', 0)->orderBy('id', 'desc')->get();
        return Datatables::of($dealers)
                        ->addColumn('action', function ($dealers) {
                            $a = '';
                            if (Gate::allows('branch-managers_update')) {
                                $a .= '<a href="branch-managers/' . $dealers->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> &nbsp;';
                            }
                            if (Gate::allows('branch-managers_delete')) {
                                $a .= '<a href="branch-managers/destroy/' . $dealers->id . '" onclick="return confirm(\'Are you sure you want to delete this Dealer?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                            }
                            return $a;
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
        if (Gate::denies('branch-managers_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $branches = DealerUser::where('branch_id', 0)->get();
        return view('admin.modules.branch_managers.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Gate::denies('branch-managers_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|max:255|unique:dealer_users',
                    'password' => 'required|min:6',
                    'branch' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $manager = new DealerUser();
        $manager->branch_id = $request->branch;
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
            $mail = Mail::send('emails.registration_dealer', $data, function($message) use ($data) {
                        $message->to($data['email']);
                        $message->subject('Branch Manager Account Created');
                    });
        } catch (\Swift_TransportException $e) {
            Log::error($e->getMessage());
        }
        return redirect('branch-managers')->with('success', 'Successfully added new Branch Manager');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (Gate::denies('branch-managers_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.branch_managers.show', compact('dealer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if (Gate::denies('branch-managers_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $edit = DealerUser::find($id);
        $branches = DealerUser::where('branch_id', 0)->get();
        return view('admin.modules.branch_managers.create', compact('edit', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (Gate::denies('branch-managers_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
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
        $manager->branch_id = $request->branch;
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
                $mail = Mail::send('emails.registration_edit', $data, function($message) use ($data) {
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
        return redirect('branch-managers')->with('success', 'Successfully updated Branch Manager');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Gate::denies('branch-managers_delete')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealer = DealerUser::findOrFail($id);

        if ($dealer->delete()) {
            \DB::table('dealer_users')->where('id', $dealer->id)->update(['session_id' => Null]);
            return redirect('branch-managers')->with('success', 'Branch Manager Deleted Successfully');
        }
        return redirect('branch-managers')->with('error', 'Something went wrong.Tray Again.');
    }

    public function export() {
        if (Gate::denies('branch-managers_export')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $fileName = 'manegers_' . time();
        $dataExported = ['id', 'name', 'email', 'branch_id', 'address', 'contact'];
        $header = ['ID', 'Name', 'Email', 'Branch Name', 'Address', 'Contact'];
        $maneges = DealerUser::where('id', '!=', 0)->where('branch_id', '!=', 0)->get($dataExported);
        $manegesArray = [];
        $manegesArray[] = $header;
        foreach ($maneges as $manege) {
            // $dataArray[] = ['id'=> $data->id,'name'=>$data->name, 'email' => $data->email, 'role'=>$user->getRole($data->role)];
            // $manegesArray[] = $manege->toArray();
            $managerName = DealerUser::where('id', $manege->branch_id)->first()->name;
            $manegesArray[] = ['id' => $manege->id, 'name' => $manege->name, 'email' => $manege->email, 'branch_id' => $managerName, 'address' => $manege->address, 'contact' => $manege->contact];
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
