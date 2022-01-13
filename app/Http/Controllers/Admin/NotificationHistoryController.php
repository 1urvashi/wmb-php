<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Validator;
use DB;
use App\GenaralNotification;
use App\Notifications;
use Auth;
use App\Auction;
use App\TraderUser;

class NotificationHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.modules.notifications.history.index');
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request)
    {
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $datas = GenaralNotification::orderBy('created_at', 'desc')->get();
        return Datatables::of($datas)
            ->editColumn('created_at', function ($datas) {
                $date = new Auction();
                $now = $this->UaeDate($datas->created_at);
                return $datas->created_at ? date('Y-m-d h:i A', strtotime($now)) : null;
            })
            ->editColumn('body', function($datas) {
                return strip_tags(\Illuminate\Support\Str::words($datas->body, 20,''));
            })
            ->addColumn('action', function ($datas) {
                $a = '';
                $a .= '<button type="button" class="btn btn-xs btn-primary" data-id="'.$datas->id.'" data-toggle="modal" data-target="#push_information" data-href="'.url('notification-history/'.$datas->id).'"><i class="fa fa-eye"></i> View</button> &nbsp; ';

                $a .= '<button type="button" class="btn btn-xs btn-success" data-id="'.$datas->id.'" data-toggle="modal" data-target="#push_information" data-href="'.url('notification-resend/'.$datas->id).'"><i class="fa fa-bell"></i> Resend</button> &nbsp; ';

                return $a;
            })
            ->make(true);
    }

    public function traderData($id) {
        $notifications = Notifications::where('notification_id', $id)->pluck('trader_id');
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $datas = TraderUser::whereIn('id', $notifications)->orderBy('created_at', 'desc')->get();
        return Datatables::of($datas)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $show = GenaralNotification::where('id', $id)->first();
        $notifications = Notifications::where('notification_id', $id)->pluck('trader_id');
        $count_sent = count($notifications);
        $traders = TraderUser::whereIn('id', $notifications)->get();
        return view('admin.modules.notifications.history.show', compact('show', 'count_sent', 'traders'));
    }

    public function reSend($id)
    {
        $show = GenaralNotification::where('id', $id)->first();
        $notifications = Notifications::where('notification_id', $id)->pluck('trader_id');
        // dd($notifications);
        $count_sent = count($notifications);
        $traders = TraderUser::whereIn('id', $notifications)->get();
        return view('admin.modules.notifications.history.resend', compact('show', 'count_sent', 'traders'));
    }

    public function reSendPost($id)
    {
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        $show = GenaralNotification::where('id', $id)->first();
        $notifications_old = Notifications::where('notification_id', $id)->pluck('trader_id');
        $notifications             = new GenaralNotification();
        $notifications->title      = 'Wecashanycar';
        $notifications->body       = $show->body;
        $notifications->user_count = count($notifications_old);
        $notifications->save();
        if(!empty($notifications_old)) {
            foreach($notifications_old as $user_id) {
                $new_notification = new Notifications();
                $new_notification->title = 'Wecashanycar';
                $new_notification->desc = $show->body;
                $new_notification->trader_id = $user_id;
                $new_notification->notification_id = $notifications->id;
                $new_notification->save();

                $trader = TraderUser::find($user_id);
                if (!empty($trader->device_type) && !empty($trader->device_id)) {
                    if ($trader->device_type == 'iOS') {
                        $devices['iosDevices'][] = $trader->device_id;
                    } elseif ($trader->device_type == 'Android') {
                        $devices['androidDevices'][] = $trader->device_id;
                    }
                }
            }
            if(!empty($devices)) {
                $this->sendGeneralPushNotification($devices, $request->body);
            }
        }

        return response()->json(["status" => 400, 'message' => 'Notification send successfully.']);
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
