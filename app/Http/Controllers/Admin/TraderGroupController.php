<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Storage;
use App\DealerUser;
use App\TraderUser;
use App\Group;
use App\TraderGroup;
use Validator;
use Datatables;
use Gate;
use DB;

class TraderGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('traders-group_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.trader_group.index', compact('dealers', 'drmsUsers'));
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request)
    {
        $datas = Group::all();
        return Datatables::of($datas)
            ->addColumn('actions', function ($datas) {
                $a = '';

                if (Gate::allows('traders-group_update')) {
                    $a .= '<a href="traders-group/' . $datas->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> '  ;
                }
                if (Gate::allows('traders-group_delete')) {
                    $a .= '<a href="traders-group/destroy/' . $datas->id . '" onclick="return confirm(\'Are you sure you want to delete this Trader?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                }
                return $a; 
            })
            ->addColumn('user_count', function ($datas) {
                $total = TraderGroup::where('group_id', $datas->id)->count();
                return $total;
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
        if (Gate::denies('traders-group_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $drmsUsers = User::where('role', config('globalConstants.OLD_ROLES.DRM_USER'))->where('status', 1)->get();
        return view('admin.modules.trader_group.create', compact('drmsUsers'));
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function createData(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $traders = TraderUser::leftJoin('users', 'users.id', '=', 'trader_users.dmr_id')
                            ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'trader_users.id','trader_users.first_name', 'trader_users.email', 'trader_users.last_name','trader_users.dealer_id', 'trader_users.status', 'trader_users.dmr_id', 'users.name'])->get();
        return Datatables::of($traders)
            ->addColumn('select', function ($traders) {
                return '<input type="checkbox" name="traders_id[]" class="trader_check" value="'.$traders->id.'"/>'; 
            })
            ->filter(function ($instance) use ($request) {
                 if ($request->has('drms') && ($request->get('drms')!=0)) {
                     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                         return ($row['dmr_id'] == $request->get('drms')) ? true : false;
                     });
                 }
                 if ($request->has('search') && ($request->get('search')!='')) {
                     $needle = strtolower($request->get('search'));
                     $instance->collection = $instance->collection->filter(function ($row) use ($request,$needle) {
                         $row = $row->toArray();
                         $result = 0;
                         foreach ($row as $key => $value) {
                             if (strpos(strtolower($value), $needle) > -1) {
                                 $result = 1;
                             }
                         }
                         return $result ? true : false;
                     });
                 }
             })
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('traders-group_create')) {
            return response()->json(["status" => 200, 'message' => 'You dont have sufficient privlilege to access this area']);
        }

        if(empty($request->name)) {
            return response()->json(["status" => 200, 'message' => 'The name field is required']);
        }

        if(empty($request->trader)) {
            return response()->json(["status" => 200, 'message' => 'Please select a trader']);
        }

        $exsit = Group::where('name', $request->name)->first();
        if($exsit) {
            return response()->json(["status" => 200, 'message' => 'The same group already exist!!']);
        }

        $data        = new Group();
        $data->name  = $request->name;
        $data->save();

        $traders = array_unique($request->trader);
        if(!empty($traders)) {
            foreach($traders as $value) {
                $trader_group = new TraderGroup();
                $trader_group->trader_id = $value;
                $trader_group->group_id = $data->id;
                $trader_group->save();
            }
        }
        return response()->json(["status" => 100, 'message' => 'New trader group successfully created']);
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
        if (Gate::denies('traders-group_create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $edit = Group::find($id);
        $drmsUsers = User::where('role', config('globalConstants.OLD_ROLES.DRM_USER'))->where('status', 1)->get();
        $selectedUser = TraderGroup::where('group_id', $id)->pluck('trader_id')->toArray();
        // dd($selectedUser);
        $newArray = implode('","', $selectedUser);
        return view('admin.modules.trader_group.create', compact('edit', 'drmsUsers', 'selectedUser', 'newArray'));
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function editData(Request $request, $id)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $traders = TraderUser::leftJoin('users', 'users.id', '=', 'trader_users.dmr_id')
                            ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'trader_users.id','trader_users.first_name', 'trader_users.email', 'trader_users.last_name','trader_users.dealer_id', 'trader_users.status', 'trader_users.dmr_id', 'users.name'])->get();
        $trader_groups = TraderGroup::where('group_id', $id)->pluck('trader_id')->toArray();
        return Datatables::of($traders)
            ->addColumn('select', function ($traders) use ($trader_groups) {
                    if(in_array($traders->id, array_values($trader_groups))) {
                        return '<input type="checkbox" name="traders_id[]" checked class="trader_check" value="'.$traders->id.'"/>';
                    } else {
                        return '<input type="checkbox" name="traders_id[]" class="trader_check" value="'.$traders->id.'"/>';
                    }
                    
            })
             ->filter(function ($instance) use ($request) {
                 if ($request->has('drms') && ($request->get('drms')!=0)) {
                     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                         return ($row['dmr_id'] == $request->get('drms')) ? true : false;
                     });
                 }
                 if ($request->has('search') && ($request->get('search')!='')) {
                     $needle = strtolower($request->get('search'));
                     $instance->collection = $instance->collection->filter(function ($row) use ($request,$needle) {
                         $row = $row->toArray();
                         $result = 0;
                         foreach ($row as $key => $value) {
                             if (strpos(strtolower($value), $needle) > -1) {
                                 $result = 1;
                             }
                         }
                         return $result ? true : false;
                     });
                 }
             })
            ->make(true);
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
        if (Gate::denies('traders-group_update')) {
            return response()->json(["status" => 200, 'message' => 'You dont have sufficient privlilege to access this area']);
        }

        if(empty($request->name)) {
            return response()->json(["status" => 200, 'message' => 'The name field is required']);
        }

        if(empty($request->trader)) {
            return response()->json(["status" => 200, 'message' => 'Please select a trader']);
        }

        $exsit = Group::where('name', $request->name)->where('id', '!=', $id)->first();
        if($exsit) {
            return response()->json(["status" => 200, 'message' => 'The same group already exist!!']);
        }

        $data        = Group::find($id);
        $data->name  = $request->name;
        $data->save();

        $traders = array_unique($request->trader);
        // dd($traders);
        TraderGroup::where('group_id', $id)->delete();
        if(!empty($traders)) {
            foreach($traders as $value) {
                $trader_group = new TraderGroup();
                $trader_group->trader_id = $value;
                $trader_group->group_id = $data->id;
                $trader_group->save();
            }
        }
        return response()->json(["status" => 100, 'message' => 'Trader group successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('traders-group_delete')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        TraderGroup::where('group_id', $id)->delete();
        Group::destroy($id);
        return redirect('admin/traders-group')->with('status', 'Role deleted successfully...');
    }
}
