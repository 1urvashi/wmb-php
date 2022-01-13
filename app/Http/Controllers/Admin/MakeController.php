<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Validator;
use App\Make;
use DB;
use Auth;
use Redirect;
use Gate;

class MakeController extends Controller {

     public function __construct(){
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('MakeMenu')){
        //      return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if(Gate::denies('make_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.makes.index');
    }

    public function data() {
        DB::statement(DB::raw('set @rownum=0'));
        $make = Make::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'name'])->orderBy('name', 'asc')->get();
        return Datatables::of($make)
                        ->addColumn('action', function ($make) {
                            $actions = '';
                            if(Gate::allows('make_update')){
                                $actions .= '<a href="make/' . $make->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>';
                            }
                            return $actions;
                        })
                        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        if(Gate::denies('make_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.makes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if(Gate::denies('make_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $make = new Make();
        $make->name = $request->name;
        $make->save();

        return redirect('make')->with('status', 'Successfully added new Make');
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
        if(Gate::denies('make_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $edit = Make::find($id);
        return view('admin.modules.makes.edit', compact('edit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if(Gate::denies('make_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $make = Make::find($id);
        $make->name = $request->name;
        $make->save();

        return redirect('make')->with('status', 'Successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if(Gate::denies('make_delete')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $make = Make::find($id);
        $make->delete();
        return redirect()->back()->with('status', 'Successfully deleted.');
    }
}
