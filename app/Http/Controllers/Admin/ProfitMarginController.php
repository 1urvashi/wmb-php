<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Validator;
use DB;
use App\ProfitMargin;
use App\SalesType;
use URL;
use Gate;


class ProfitMarginController extends Controller
{
    public function formLoad()
    {
        return view('admin.modules.price_types.sales_types.form');
    }
    public function formLoadEdit($id)
    {
        $data = ProfitMargin::where('id', $id)->first();
        return view('admin.modules.price_types.sales_types.form', compact('data'));
    }
    public function manageProfit($id)
    {
        $datas = ProfitMargin::where('sales_type_id', $id)->get();
        return view('admin.modules.price_types.sales_types.manage_profit', compact('id', 'datas'));
    }
    public function data($id)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $data = ProfitMargin::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'range_from', 'range_to', 'profit_status', 'profit_amount'])->where('sales_type_id', $id)->orderBy('range_from', 'asc')->get();
        return Datatables::of($data)
                       ->editColumn('range_from', function ($data) {
                           return $data->range_from . ' AED';
                       })
                       ->editColumn('range_to', function ($data) {
                           return $data->range_to . ' AED';
                       })
                     ->editColumn('profit_status', function ($data) {
                         $val = ($data->profit_status == 1) ? 'AED'  : '%';
                         return $data->profit_amount.' '.$val;
                     })
                        ->addColumn('action', function ($data) {
                            $actions = '';
                            $actions .= '<a  data-toggle="modal" data-target="#addRange"  data-href="'. URL::route('form-load-edit', $data->id) .'" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i></a> ';
                            //$actions .= '<a href="'. URL::route('delete.profit', $data->id) .'" class="btn btn-danger destroy"><i class="fa fa-trash"></i></a>';
                            return $actions;
                        })
                        ->make(true);
    }

    public function createProfit(Request $request)
    {
        if (Gate::denies('priceType_profit-Margin-create')) {
            return response()->json(array('success' => false, 'msg' => 'You dont have sufficient privlilege to access this area'), 200);
        }
        $validator = Validator::make($request->all(), [
               'amount' => 'required|numeric',
               'profit' => 'required',
               'from' => 'required|numeric',
               'to' => 'required|numeric'
          ]);
          // dd($request->profit);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        if ($request->from >= $request->to) {
            return response()->json(array('success' => false, 'msg' => 'To Amount Must Graterthan from amount!'), 200);
        }
        if ($request->amount >= $request->to) {
            return response()->json(array('success' => false, 'msg' => 'Amount Must less than To!'), 200);
        }
        if($request->profit == 2) {
             if($request->amount >100) {
                  return response()->json(array('success' => false, 'msg' => 'The percentage must less than or equal 100!'), 200);
             }
        }
        $range_check = ProfitMargin::where(function ($query) use ($request) {
                                        $query->where('range_from', '<', $request->from);
                                        $query->where('range_to', '>', $request->from);
                                        $query->where('sales_type_id', $request->sales_type_id);
                                   }) ->orWhere(function ($query) use ($request) {
                                            $query->where('range_from', '<', $request->to);
                                            $query->where('range_to', '>', $request->to);
                                           $query->where('sales_type_id', $request->sales_type_id);
                                      })->count();
        $exist = ProfitMargin::where('range_from', $request->from)
                                        ->where('range_to', $request->to)
                                        ->where('profit_status', $request->profit)
                                        // ->where('profit_amount', $request->amount)
                                        ->where('sales_type_id', $request->sales_type_id)
                                        ->first();
        if ($exist) {
            return response()->json(array('success' => false, 'msg' => 'Profit Margin alerdy exist!.'), 200);
        }
        if ($range_check == 0) {
            $data = new ProfitMargin();
            $data->range_from = $request->from;
            $data->range_to = $request->to;
            $data->profit_status = $request->profit;
            $data->profit_amount = $request->amount;
            $data->profit_amount = $request->amount;
            $data->sales_type_id = $request->sales_type_id;
            $data->save();
            return response()->json(array('success' => true, 'msg' => 'Profit added successfully.'), 200);
        } else {
            return response()->json(array('success' => false, 'msg' => 'The Range already in profit margin!'), 200);
        }
    }

    public function updateProfit(Request $request, $id)
    {
        if (Gate::denies('priceType_profit-Margin-update')) {
            return response()->json(array('success' => false, 'msg' => 'You dont have sufficient privlilege to access this area'), 200);
        }
        $validator = Validator::make($request->all(), [
               'amount' => 'required|numeric',
               'profit' => 'required',
               'from' => 'required|numeric',
               'to' => 'required|numeric'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        if ($request->from >= $request->to) {
            return response()->json(array('success' => false, 'msg' => 'To Amount Must Graterthan from amount!'), 200);
        }
        if ($request->amount >= $request->to) {
            return response()->json(array('success' => false, 'msg' => 'To Amount Must less than To!'), 200);
        }
        if($request->profit == 2) {
            if($request->amount >100) {
                  return response()->json(array('success' => false, 'msg' => 'The percentage must less than or equal 100!'), 200);
            }
        }
        $current_datas = ProfitMargin::all();
        $exist = ProfitMargin::where('range_from', $request->from)
                                        ->where('range_to', $request->to)
                                        ->where('profit_status', $request->profit)
                                        ->where('id', '!=', $id)
                                        ->where('sales_type_id', $request->sales_type_id)
                                        ->first();
        if ($exist) {
            return response()->json(array('success' => false, 'msg' => 'Profit Margin alerdy exist!.'), 200);
        }
        $data = ProfitMargin::find($id);
        $data->range_from = $request->from;
        $data->range_to = $request->to;
        $data->profit_status = $request->profit;
        $data->profit_amount = $request->amount;
        $data->profit_amount = $request->amount;
        $data->sales_type_id = $request->sales_type_id;
        $data->save();
        return response()->json(array('success' => true, 'msg' => 'Profit updated successfully.'), 200);
    }

    public function destroy($id)
    {
        return response()->json(array('success' => true, 'msg' => 'You dont have sufficient privlilege to access this area'), 200);
        if (Gate::denies('priceType_profit-Margin-delete')) {
            return response()->json(array('success' => false, 'msg' => 'You dont have sufficient privlilege to access this area'), 200);
        }
        $data = ProfitMargin::find($id);
        $data->delete();
        return response()->json(array('success' => true, 'msg' => 'Profit deleted successfully!.'), 200);
    }
}
