<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AdminLogHistory;
use Gate;
use Redirect;
use DB;
use Auth;
use Datatables;
use Location;

class AuditReportController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if (Gate::denies('audit_report')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
		        
        return view('admin.modules.audit-report.index');
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request) {
        $user = Auth::guard('admin')->user();

        //$users = AdminLogHistory::join('users', 'users.id', '=', 'admin_log_histories.user_id')
                //->select(['users.id', 'users.email', 'admin_log_histories.ip', DB::raw('MAX(admin_log_histories.time) as time')])
                //->where('admin_log_histories.type', 'login')
                //->groupBy('users.id')
                //->orderBy('admin_log_histories.created_at')
                //->get();
		$sub = DB::raw('(SELECT user_id, Max(created_at) AS `max_date` FROM admin_log_histories where `type` = "login" GROUP BY user_id) AS max_table');
		
		$users = AdminLogHistory::join($sub, function($join) { $join->on('max_table.user_id', '=', 'admin_log_histories.user_id')->on('max_table.max_date', '=', 'admin_log_histories.created_at');})->orderBy('admin_log_histories.created_at', 'desc')->get();

        return Datatables::of($users)
                        ->addColumn('action', function ($users) {
                            $a = '';
                            $a .= '<a href="audit-report/' . $users->user_id . '" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View</a>';
                            return $a;
                        })
						->addColumn('email', function ($users) {
                            $email = \App\User::withTrashed()->where('id', $users->user_id)->first()->email;
							return $email;
						})
                        ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = \App\User::find($id);
        return view('admin.modules.audit-report.show', compact('user'));
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function showData(Request $request) {
        $users = AdminLogHistory::join('users', 'users.id', '=', 'admin_log_histories.user_id')
                ->select(['admin_log_histories.ip', 'admin_log_histories.time'])
                ->where('admin_log_histories.type', 'login')
                ->where('admin_log_histories.user_id', $request->userId)
                ->orderBy('admin_log_histories.time', 'DESC')
                ->get();

        return Datatables::of($users)
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('from') && ($request->has('to'))) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return (($row['time'] >= $request->get('from') . ' 00:00:00') && ($row['time'] <= $request->get('to') . ' 23:59:59')) ? true : false;
                                });
                            } elseif ($request->has('from')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return (($row['time'] >= $request->get('from') . ' 00:00:00')) ? true : false;
                                });
                            } elseif ($request->has('to')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return (($row['time'] <= $request->get('to') . ' 23:59:59')) ? true : false;
                                });
                            }
                        })
                        ->make(true);
    }

}
