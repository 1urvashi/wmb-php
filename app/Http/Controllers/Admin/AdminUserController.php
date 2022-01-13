<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Datatables;
use DB;
use App\User;
use Illuminate\Support\Facades\Log;
use Mail;
use File;
use GuzzleHttp;
use Auth;
use Redirect;
use Excel;
use Gate;
use App\Role;
use App\RoleUser;

class AdminUserController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user = Auth::guard('admin')->user();
        $super_admin = \App\User::where('type', config('globalConstants.TYPE.SUPER_ADMIN'))->where('id', $user->id)->first();
        if (!empty($super_admin)) {
            return view('admin.modules.admin-user.index');
        } else {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data() {
        DB::statement(DB::raw('set @rownum=0'));
        $users = User::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'name', 'email', 'role'])
                        ->where('type', config('globalConstants.TYPE.ADMIN'))
                        ->orderBy('id', 'desc')->get();
        return Datatables::of($users)
                        ->addColumn('action', function ($users) {
                            $a = '';
                            $a .= '<a href="admin-user/' . $users->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> &nbsp;';
                            $a .= '<a href="admin-user/destroy/' . $users->id . '" onclick="return confirm(\'Are you sure you want to delete this Admin?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                            return $a;
                        })
                        ->editColumn('role', function ($user) {
                            $role_id = RoleUser::where('user_id', $user->id)->first();
                            if (!empty($role_id)) {
                                $role_name = Role::where('id', $role_id->role_id)->first()->label;
                                return $role_name;
                            } else {
                                return 'NA';
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
        $user = Auth::guard('admin')->user();
        $super_admin = \App\User::where('type', config('globalConstants.TYPE.SUPER_ADMIN'))->where('id', $user->id)->first();
        if (!empty($super_admin)) {
            return view('admin.modules.admin-user.create');
        } else {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
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
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->type = config('globalConstants.TYPE.ADMIN');
        $user->save();
        $role_user = new RoleUser();
        $role_user->user_id = $user->id;
        $role_user->role_id = config('globalConstants.ROLES.ADMIN');
        $role_user->save();
        return redirect('admin-user')->with('success', 'Successfully added new Admin');
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
    public function edit(User $admin_user) {
        $user = Auth::guard('admin')->user();
        $super_admin = \App\User::where('type', config('globalConstants.TYPE.SUPER_ADMIN'))->where('id', $user->id)->first();
        if (!empty($super_admin)) {
            return view('admin.modules.admin-user.create', compact('admin_user'));
        } else {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
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
                    'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $exist = User::where('email', $request->email)->where('id', '!=', $id)->first();

        if ($exist) {
            return redirect()->back()->with('error', 'The admin user alredy exist!!');
        }
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->type = config('globalConstants.TYPE.ADMIN');
        $user->save();
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->type = config('globalConstants.TYPE.ADMIN');
        $user->save();

        $role_user = RoleUser::where('user_id', $user->id)->first();
        if (empty($role_user)) {
            $data = new RoleUser();
            $data->user_id = $user->id;
            $data->role_id = $request->role;
            $data->save();
        } else {
            RoleUser::where('user_id', $user->id)->update(['role_id' => config('globalConstants.ROLES.ADMIN')]);
        }

        return redirect('admin-user')->with('success', 'Successfully updated Admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = Auth::guard('admin')->user();
        $super_admin = \App\User::where('type', config('globalConstants.TYPE.SUPER_ADMIN'))->where('id', $user->id)->first();
        if (!empty($super_admin)) {
            $user = User::findOrFail($id);
            if ($user->delete()) {
                \DB::table('users')->where('id', $user->id)->update(['session_id' => Null]);
                return redirect('admin-user')->with('success', 'Admin Deleted Successfully');
            }
            return redirect('admin-user')->with('error', 'Something went wrong.Tray Again.');
        } else {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
    }

}
