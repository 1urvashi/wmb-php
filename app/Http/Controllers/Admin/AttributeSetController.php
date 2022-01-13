<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\AttributeSet;
use App\Attribute;
use App\AttributeSetAttributes;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Validator;
use File;
use Auth;
use Redirect;
use Gate;

class AttributeSetController extends Controller
{
     public function __construct(){
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('AttributeSetMenu')){
        //      return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Gate::denies('attributeSet_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.attributeset.index');
    }

    public function data() {
       // return 1;
        $attributesets = AttributeSet::select(['id', 'name'])->orderBy('id', 'asc')->get();
        return Datatables::of($attributesets)
            ->addColumn('action', function ($attributesets) {
                $a = '';
                if (Gate::allows('attributeSet_update')) {
                    $a .='<a href="attributeset/' . $attributesets->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>';
                }
                if (Gate::allows('attributeSet_delete')) {
                    $a .='<a href="attributeset/destroy/' . $attributesets->id . '" onclick="return confirm(\'Are you sure you want to delete this AttributeSet?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                }
                return $a;
                //<a href="attributeset/destroy/' . $attributesets->id . '" onclick="return confirm(\'Are you sure you want to delete this AttributeSet?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
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
        if(Gate::denies('attributeSet_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $attributes = Attribute::all();
        return view('admin.modules.attributeset.create',  compact('attributes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Gate::denies('attributeSet_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        //return $request->get('attributes');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $attributesets = new AttributeSet;
        $attributesets->name = $request->name;
        $attributesets->sort = $request->sort;
        $attributesets->slug = str_slug($request->name, '-');
        $attributesets->save();
        if ($request->has('attributes')) {
            AttributeSet::find($attributesets->id)->attributes()->attach($request->get('attributes'));
        }
        return redirect('attributeset')->with('status', 'Successfully added new AttributeSet');
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
        if(Gate::denies('attributeSet_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $edit = AttributeSet::find($id);
        $attributes = Attribute::all();
        $selectedAttributes = AttributeSetAttributes::where('attribute_set_id',$id)->lists('attribute_id')->toArray();
        return view('admin.modules.attributeset.create', compact('edit','attributes','selectedAttributes'));
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
        if(Gate::denies('attributeSet_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $attributesets = AttributeSet::find($id);
        $attributesets->name = $request->name;
        $attributesets->sort = $request->sort;
        $attributesets->slug = ($attributesets->slug) ? $attributesets->slug : str_slug($request->name, '-');
        $attributesets->save();
        if ($request->has('attributes')) {
            AttributeSet::find($attributesets->id)->attributes()->sync($request->get('attributes'));
        } else{
            AttributeSetAttributes::where('attribute_set_id',$id)->delete();
        }
        return redirect('attributeset')->with('success', 'Successfully updated AttributeSet');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Gate::denies('attributeSet_delete')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $attributesets = AttributeSet::find($id);
        AttributeSetAttributes::where('attribute_set_id',$id)->delete();
        $attributesets->delete();
        return redirect('attributeset')->with('success', 'AttributeSet Deleted Successfully');
    }
}
