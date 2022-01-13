<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use Validator;
use App\Bank;
use Redirect;
use Gate;

class BanksController extends Controller
{
     public function __construct(){
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('bankMenu')){
        //      return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
     }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if(Gate::denies('bank_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.bank.index');
    }

    public function data() {
        DB::statement(DB::raw('set @rownum=0'));
        $bank = Bank::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'name'])->orderBy('name', 'asc')->get();
        return Datatables::of($bank)
                        ->addColumn('action', function ($bank) {
                            if (Gate::allows('bank_update')) {
                                $actions = '<a href="bank/' . $bank->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>';
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
    public function create()
    {
        if(Gate::denies('bank_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.bank.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Gate::denies('bank_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
         $validator = Validator::make($request->all(), [
                    'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $exist = Bank::where('name', $request->name)->first();
        if($exist) {
             return redirect('bank')->with('error', 'The bank is already exist!');
        }

        $bank = new Bank();
        $bank->name = $request->name;
        $bank->address = $request->address;
        $bank->save();

        return redirect('bank')->with('status', 'Successfully added new Bank');
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
        if(Gate::denies('bank_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
         $edit = Bank::find($id);
         return view('admin.modules.bank.edit', compact('edit'));
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
        if(Gate::denies('bank_update')){
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

        $exist = Bank::where('name', $request->name)->where('id', '!=', $id)->first();
        if($exist) {
             return redirect('bank')->with('error', 'The bank is already exist!');
        }

        $bank = Bank::find($id);
        $bank->name = $request->name;
        $bank->address = $request->address;
        $bank->save();
        return redirect('bank')->with('status', 'Successfully updated.');
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
