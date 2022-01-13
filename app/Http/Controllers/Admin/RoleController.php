<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use App\Role;
use Datatables;
use App\Permission;
use App\User;
use Gate;
use DB;
use Auth;
use URL;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         if (Gate::denies('roles_read')) {
             return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
         }
         return view('admin.modules.roles.index');
    }

    public function datatable() {
        DB::statement(DB::raw('set @rownum=0'));
        $roles = Role::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'label'])->orderBy('id', 'asc')->get();
        return Datatables::of($roles)
                                   ->addColumn('action', function ($roles) {
                                   $a = '';
                                  if (Gate::allows('roles_update')) {
                                        $a .= '<a href="roles/' . $roles->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> &nbsp;';
                                   }
                                //    if (Gate::allows('roles_delete')) {
                                //         $a .='<a href="roles/destroy/' . $roles->id . '" onclick="return confirm(\'Are you sure you want to delete this role?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                                //    }
                                   return $a;
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
         if (Gate::denies('roles_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $permissions = Permission::pluck('id', 'name')->toArray();
        $target = [];
        foreach ($permissions as $k => $v) {
            $keys = explode("_", $k);
            if (count($keys) > 1) {
                $target[$keys[0]][$v] = $keys[1];
            } else {
                $target[$k] = $v;
            }
        }

        $permissions = $target;
        return view('admin.modules.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         if (Gate::denies('roles_create')) {
           return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
         $validator = Validator::make($request->all(), [
                    'name' => 'required',
        ]);
        if ($validator->fails()) {
            return back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $permissions = $request->permission;
        if (empty($permissions)) {
            return redirect()->back()->with('error', 'Please select atleast one permission to create a role');
        }

        $role = new Role();
        $role->name = str_slug($request->name, "_");
        $role->label = $request->name;
        $role->save();

        foreach ($permissions as $permission) {
            $permission = Permission::find($permission);
            $role->givePermissionTo($permission);
        }
        return redirect('roles')->with('status', 'New role added successfully...');
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
         if (Gate::denies('roles_update')) {
             return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
         }
         $edit = Role::findOrFail($id);
         $select_permissions = Role::find($id)->permissions()->get();
         $permissions = Permission::pluck('id', 'name')->toArray();
         $all_permissions = Permission::all();

        $target = [];
        foreach ($permissions as $k => $v) {
            $keys = explode("_", $k);
            if (count($keys) > 1) {
                $target[$keys[0]][$v] = $keys[1];
            } else {
                $target[$k] = $v;
            }
        }
        $permissions = $target;
        return view('admin.modules.roles.edit', compact('edit', 'permissions', 'select_permissions', 'all_permissions'));
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
         if (Gate::denies('roles_update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
         $validator = Validator::make($request->all(), [
                    // 'name' => 'required',
        ]);
        if ($validator->fails()) {
            return back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $permissions = $request->permission;
        // dd($permissions);
        if (empty($permissions)) {
            return redirect()->back()->with('error', 'Please select atleast one permission to edit a role');
        }
        $role = Role::find($id);
        $role->permissions()->sync($request->permission);
        return redirect('roles')->with('status', 'Role updated successfully...');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         if (Gate::denies('roles_delete')) {
           return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
       }
        Role::destroy($id);
        return redirect('roles')->with('status', 'Role deleted successfully...');
    }
}
