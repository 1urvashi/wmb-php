<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Role;
use Validator;
use Datatables;
use App\Permission;
use Gate;
use DB;
use Auth;
use URL;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('permission_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.permissions.index');
    }


    public function datatable() {
        DB::statement(DB::raw('set @rownum=0'));
        $permissions = Permission::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'label', 'name'])->orderBy('id', 'desc')->get();
        return Datatables::of($permissions)
                            ->addColumn('roles', function ($permissions) {
                              $role = DB::table('permission_role')->where('permission_id', $permissions->id)->pluck('role_id');
                              // dd($role);
                              if(!empty($role)) {
                                $roleName = Role::whereIn('id', $role)->pluck('label');
                                $data = implode(", ",$roleName->toArray());
                                return $data;
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
    public function create()
    {
        if (Gate::denies('permission_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // $roles = Role::all();
        // return view('admin.modules.permissions.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('permission_create')) {
          return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
          'name' => 'required',
        ]);
        if ($validator->fails()) {
          return back()->withErrors($validator)->withInput();
        }
        $roles = $request->roles;
        $permission = new Permission();
        $permission->name = str_slug($request->name, "_");
        $permission->label = $request->name;
        $permission->save();


        foreach ($roles as $role) {
          DB::table('permission_role')->insert(
              ['permission_id' => $permission->id, 'role_id' => $role]
          );
        }
        return redirect('permissions')->with('status', 'New Permission added successfully...');
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
