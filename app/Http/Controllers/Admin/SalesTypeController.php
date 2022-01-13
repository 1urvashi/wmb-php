<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Datatables;
use DB;
use Auth;
use Validator;
use App\SalesType;
use Gate;
use Redirect;

class SalesTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('priceType_salestype-read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.price_types.sales_types.index');
    }

    public function data() {
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $data = SalesType::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'name', 'status'])->orderBy('name', 'asc')->get();
        return Datatables::of($data)
                        ->addColumn('action', function ($data) use($user) {
                             $actions = '';
                             if (Gate::allows('priceType_salestype-update')) {
                                  $actions .= '<a href="sales-types/' . $data->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> '  ;
                             }
                             if (Gate::allows('priceType_profit-Margin-read')) {
                                  $actions .= '<a href="profit-management/' . $data->id . '" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Manage Profit</a> ';
                             }
                             if (Gate::allows('priceType_salestype-create')) {
                                $actions .= '<a class="btn btn-xs btn-success" data-toggle="modal" data-target="#duplicate-model" data-href="'.url("duplicate-form/").'/'.$data->id.'"><i class="fa fa-pencil-square-o"></i> Duplicate</a>';
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
        if (Gate::denies('priceType_salestype-create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.price_types.sales_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('priceType_salestype-create')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
            'sale_type' => 'required',
            'name' => 'required',
            'rta_charge' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'poa_charge' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'transportation_charge' => 'required|regex:/^\d*(\.\d{1,2})?$/'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = new SalesType();
        $data->sale_type = $request->sale_type;
        $data->name = $request->name;
        $data->rta_charge = $request->rta_charge;
        $data->poa_charge = $request->poa_charge;
        $data->transportation_charge = $request->transportation_charge;
        $data->save();
        return redirect('sales-types')->with('success', 'Successfully added new sales types');
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
        if (Gate::denies('priceType_salestype-update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $edit = SalesType::find($id);
        return view('admin.modules.price_types.sales_types.edit', compact('edit'));
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
        if (Gate::denies('priceType_salestype-update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
         $validator = Validator::make($request->all(), [
            'sale_type' => 'required',
            'name' => 'required',
            'rta_charge' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'poa_charge' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'transportation_charge' => 'required|regex:/^\d*(\.\d{1,2})?$/'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = SalesType::find($id);
        $data->sale_type = $request->sale_type;
        $data->name = $request->name;
        $data->rta_charge = $request->rta_charge;
        $data->poa_charge = $request->poa_charge;
        $data->transportation_charge = $request->transportation_charge;
        $data->save();
        return redirect('sales-types')->with('success', 'Successfully updated sales types');
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

    public function publish(Request $request, $id)
    {
        $attribute = SalesType::find($request->dataId);
        $attribute->status = $request->dataValue;
        $attribute->save();
        return;
    }

    public function vat() {
        if (Gate::denies('priceType_vat-update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
         $vat = \App\GlobalVat::where('slug', '=', 'global-vat')->first();
         return view('admin.modules.price_types.vat.index', compact('vat'));
    }

    public function vatPost(Request $request) {
        if (Gate::denies('priceType_vat-update')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
         $validator = Validator::make($request->all(), [
                    'vat' => 'required|numeric|between:0,99.99'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

         $page = \App\GlobalVat::where('slug', '=', 'global-vat')->first();
         if(empty($page)){
              $page = new \App\GlobalVat();
              $page->slug = 'global-vat';
         }
         $page->vat = $request->vat;
         $page->save();
         return redirect()->back()->with('status', 'VAT Successfully updated');
    }

    public function formLoad($id) {
        $sale_type_id = $id;
        return view('admin.modules.price_types.sales_types.duplicate-form', compact('sale_type_id'));
    }

    public function duplicate(Request $request) {
        if (Gate::denies('priceType_salestype-create')) {
            return response()->json(array('success' => false, 'msg' => 'You dont have sufficient privlilege to access this area'), 200);
        }
        $validator = Validator::make($request->all(), [
               'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        if(empty($request->type_id)) {
            return response()->json(array('success' => false, 'msg' => 'Oops something went wrong. Please reload the page.'), 200);
        }

        $saleType = SalesType::where('id', $request->type_id)->first();
        $proftMargins = \App\ProfitMargin::where('sales_type_id', $saleType->id)->get();
        $newType = new SalesType();
        $newType->name = $request->name;
        $newType->sale_type = $saleType->sale_type;

        $newType->rta_charge = $saleType->rta_charge;
        $newType->poa_charge = $saleType->poa_charge;
        $newType->transportation_charge = $saleType->transportation_charge;
        $newType->save();

        if (!empty($proftMargins)) {
            foreach ($proftMargins as $proftMargin) {
                $newMargin = new \App\ProfitMargin();
                $newMargin->range_from = $proftMargin->range_from;

                $newMargin->range_to = $proftMargin->range_to;
                $newMargin->profit_status = $proftMargin->profit_status;
                $newMargin->profit_amount = $proftMargin->profit_amount;
                $newMargin->sales_type_id = $newType->id;
                $newMargin->save();
            }
        }

        return response()->json(array('success' => true, 'msg' => 'Duplicate added successfully.'), 200);
    }
}
