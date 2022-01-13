<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use Datatables;
use DB;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
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

class UsersController extends Controller {

    public function __construct() {
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('usersMenu')){
        //      Redirect::to('admin')->send()->with('danger', 'You dont have sufficient privlilege to access this area');
        // }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user) {
        if (Gate::denies('users_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.user.index');
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data() {
        //$drm = new User();
        $gType = config('globalConstants.TYPE');

        DB::statement(DB::raw('set @rownum=0'));
        $users = User::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'name', 'email', 'role', 'login_status'])
                        // ->where('role','!=',null)
                        // ->where('role','!=',0)
                        // ->where('role', '!=', $drm->getDRM())
                        ->whereIn('type', [$gType['USERS'], $gType['HEAD_DRM']])
                        ->orderBy('id', 'desc')->get();
        return Datatables::of($users)
                        ->addColumn('action', function ($users) {
                            /* return '<a href="users/' . $users->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>
                              <a href="users/destroy/' . $users->id . '" onclick="return confirm(\'Are you sure you want to delete this User?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                             */

                            $a = '';
                            if (Gate::allows('users_update')) {
                                $a .= '<a href="users/' . $users->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> &nbsp;';
                            }
                            if (Gate::allows('users_delete')) {
                                $a .= '<a href="users/destroy/' . $users->id . '" onclick="return confirm(\'Are you sure you want to delete this User?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                            }
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
                            //return $user->getRole($user->role);
                        })
                        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        if (Gate::denies('users_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        // $roles = Role::where('name', '!=', 'drm_user')->get();
        $roles = Role::where('name', '!=', 'drm_user')->where('name', '!=', 'admin')->where('name', '!=', 'onboarder')->get();
        return view('admin.modules.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (Gate::denies('users_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required|min:6',
                    'role' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $gType = config('globalConstants.TYPE');

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->type = ($request->role == config('globalConstants.ROLES.HEAD_DRM')) ? $gType['HEAD_DRM'] : $gType['USERS'];
        $user->save();
        // dd($request->all());
        // $data = $request->all();
        // $data['account'] = 'User';
        // $data['password'] = bcrypt($request->password);
        // $data['type'] = config('globalConstants.TYPE.USERS');
        // $user->create($data);
        // dd($data);
        $role_user = new RoleUser();
        $role_user->user_id = $user->id;
        $role_user->role_id = $request->role;
        $role_user->save();
        return redirect('users')->with('success', 'Successfully added new User');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {
        if (Gate::denies('users_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user) {
        if (Gate::denies('users_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $roles = Role::where('name', '!=', 'drm_user')->where('name', '!=', 'admin')->where('name', '!=', 'onboarder')->get();
        $selected_role = RoleUser::where('user_id', $user->id)->first();
        return view('admin.modules.user.create', compact('user', 'roles', 'selected_role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {
        if (Gate::denies('users_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|max:255',
                    'role' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $exist = User::where('email', $request->email)->where('id', '!=', $user->id)->first();

        if ($exist) {
            return redirect()->back()->with('error', 'The admin user alredy exist!!');
        }

        $gType = config('globalConstants.TYPE');
        $roleHeadId = config('globalConstants.ROLES.HEAD_DRM');

        if ($user->type == $gType['HEAD_DRM'] && $request->role != $roleHeadId) {
            $tradeUserExist = \App\TraderUser::where('dmr_id', $user->id)->first();
            if ($tradeUserExist) {
                return redirect('users')->with('error', 'Can not update the role.This user is merged with trader.');
            }
        }

        $data = $request->all();
        $data['type'] = ($request->role == $roleHeadId) ? $gType['HEAD_DRM'] : $gType['USERS'];
        $data['role'] = null;
        if ($request->has('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        $role_user = RoleUser::where('user_id', $user->id)->first();
        if (empty($role_user)) {
            $data = new RoleUser();
            $data->user_id = $user->id;
            $data->role_id = $request->role;
            $data->save();
        } else {
            $role_user->role_id = $request->role;
            $role_user->save();
            //RoleUser::where('user_id', $user->id)->update(['role_id' => $request->role]);
        }
        return redirect('users')->with('success', 'Successfully updated User');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (Gate::denies('users_delete')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $user = User::findOrFail($id);
        if ($user->delete()) {
            \DB::table('users')->where('id', $user->id)->update(['session_id' => Null]);
            return redirect('users')->with('success', 'User Deleted Successfully');
        }
        return redirect('users')->with('error', 'Something went wrong.Tray Again.');
    }

    public function export() {
        if (Gate::denies('users_export')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $user = new User();
        $drm = $user->getDRM();
        $fileName = 'admin_users_' . time();
        $dataExported = ['id', 'name', 'email', 'role'];
        $datas = User::where('role', '!=', $drm)->get($dataExported);
        $dataArray = [];
        $dataArray[] = $dataExported;
        foreach ($datas as $data) {
            $dataArray[] = ['id' => $data->id, 'name' => $data->name, 'email' => $data->email, 'role' => $user->getRole($data->role)];
        }
        Excel::create($fileName, function($excel) use ($dataArray) {
            $excel->setTitle('Admin Users');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Admin Users file');
            $excel->sheet('sheet1', function($sheet) use ($dataArray) {
                $sheet->fromArray($dataArray, null, 'A1', false, false);
            });
        })->download('csv');
    }

}
