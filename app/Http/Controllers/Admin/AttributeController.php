<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use File;
use App\Attribute;
use App\AttributeSet;
use App\AttributeValue;
use Validator;
use Yajra\Datatables\Datatables;
use Redirect;
use Gate;

class AttributeController extends Controller
{
     public function __construct(){
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('AttributeMenu')){
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
        if(Gate::denies('attribute_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
       }
        $attributesets = AttributeSet::all();
        return view('admin.modules.attribute.index',compact('attributesets'));
    }

    public function data(Request $request) {
        $attribute = Attribute::select(['id','name','input_type','is_required','status','attribute_set_id', 'invisible_to_trader', 'exportable'])->orderBy('name', 'asc')->get();
        return Datatables::of($attribute)
            ->addColumn('action', function ($attribute) {
                $a = '';
                if (Gate::allows('attribute_update')) {
                    $a .= '<a href="attribute/' . $attribute->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>';
                }
                return $a;
                //<a href="attribute/destroy/' . $attribute->id . '" onclick="return confirm(\'Are you sure you want to delete this attribute?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
            })
            ->filter(function ($instance) use ($request) {
                    if ($request->has('attributeset') && ($request->get('attributeset')!=0)) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return ($row['attribute_set_id'] == $request->get('attributeset')) ? true : false;
                            });
                    }
                    if ($request->has('search') && ($request->get('search')!='')) {
                          $needle = strtolower($request->get('search'));
                          $instance->collection = $instance->collection->filter(function ($row) use ($request,$needle) {
                            $row = $row->toArray();
                            $result = 0;
                            foreach ($row as $key => $value) {
                              if(strpos(strtolower($value), $needle) > -1) {
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*$attributes = Attribute::all();
        foreach($attributes as $_attribute){
            $set = AttributeSetAttributes::where('attribute_id',$_attribute->id)->first();
            if($set){
                $_attribute->attribute_set_id = $set->attribute_set_id;
            }
            $_attribute->save();
        }*/
        if(Gate::denies('attribute_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
       }
        $attribute = new Attribute;
        $attributeSet = AttributeSet::all();
        return view('admin.modules.attribute.create',compact('attribute','attributeSet'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        if(Gate::denies('attribute_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
       }
       //return $request->request->all();
       $validator = Validator::make($request->all(), [
           'name' => 'required',
           ]);
           if ($validator->fails()) {
               return redirect()->back()->withErrors($validator)->withInput();
            }
            // dd('d');
        $attribute = new Attribute;
        $attribute->name = $request->name;
        $attribute->input_type = $request->input_type;
        $attribute->attribute_set_id = ($request->attribute_set_id == 0) ? null : $request->attribute_set_id;
        $attribute->is_required = $request->has('is_required') ? 1 : 0;
        $attribute->has_additional_text = $request->has('has_additional_text') ? 1 : 0;
        $attribute->sort = $request->sort;
        //$attribute->option = $request->has('option') ? $attribute->getRenderValue($request->option) : '';
        $attribute->save();
        // dd($attribute);
        if($request->has('attributes')){
            $attributeValues = $request->get('attributes');

            foreach($attributeValues as $key=>$value){
                if(!empty($value['name'])){
                    $dynamicAttributes = new AttributeValue;
                    $dynamicAttributes->attribute_value = $value['name'];
                    $dynamicAttributes->color = $value['color'];
                    $dynamicAttributes->attribute_id = $attribute->id;
                    $dynamicAttributes->save();
                }
            }
        }
        return redirect('attribute')->with('success', 'Successfully added new attribute');
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
        if(Gate::denies('attribute_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $edit = Attribute::find($id);
        $attributeSet = AttributeSet::all();
        return view('admin.modules.attribute.create',compact('edit','attributeSet'));
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
        if(Gate::denies('attribute_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        //return $request->request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $attribute = Attribute::find($id);
        $attribute->name = $request->name;
        $attribute->input_type = $request->input_type;
        $attribute->attribute_set_id = ($request->attribute_set_id == 0) ? null : $request->attribute_set_id;
        $attribute->is_required = $request->has('is_required') ? 1 : 0;
        $attribute->has_additional_text = $request->has('has_additional_text') ? 1 : 0;
        $attribute->sort = $request->sort;
        //$attribute->option = $request->has('option') ? $attribute->getRenderValue($request->option) : '';
        $attribute->save();
        if($request->has('attributes')){
            $attributeValues = $request->get('attributes');

            if(isset($attributeValues['name'])){
            
                Attribute::find($id)->attributeValues()->delete();
                foreach($attributeValues['name'] as $key=>$value){
                $dynamicAttributes = new AttributeValue;
                $dynamicAttributes->attribute_value = $value;
                $dynamicAttributes->color = $attributeValues['color'][$key];
                $dynamicAttributes->attribute_id = $attribute->id;
                $dynamicAttributes->save();
                }
            }
        }

        return redirect('attribute')->with('success', 'Successfully updated attribute');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Gate::denies('attribute_delete')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $attribute = Attribute::find($id);
        Attribute::find($id)->attributeValues()->delete();
        $attribute->delete();
        return redirect('attribute')->with('success', 'Attribute Deleted Successfully');
    }

    public function publish(Request $request, $id) {
         $attribute = Attribute::find($request->dataId);
         $attribute->status = $request->dataValue;
         $attribute->save();
         return;
    }

    public function invisibleToTrader(Request $request, $id) {
         $attribute = Attribute::find($request->dataId);
         $attribute->invisible_to_trader = $request->dataValue;
         $attribute->save();
         return;
    }

    public function exportable(Request $request, $id) {
         $attribute = Attribute::find($request->dataId);
         $attribute->exportable = $request->dataValue;
         $attribute->save();
         return;
    }

}
