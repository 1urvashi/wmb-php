<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Datatables;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Notifications;
use App\GenaralNotification;
use App\TraderUser;
use App\NotificationTemplate;
use Validator;
use DB;
use Auth;
use URL;
use Gate;
use App\Auction;

class NotificationTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(session()->get('selected_traders'));
        if (Gate::denies('Push-Notification-Templates_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.notifications.templates.index');
    }

    public function cancel() {
        session()->forget('selected_traders');
        return redirect('notification-templates')->with('growl', ['Notification successfully canceled.', 'success']);

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
        $datas = NotificationTemplate::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'title', 'body', 'created_at'])->orderBy('created_at', 'desc')->get();
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
                if (session()->has('selected_traders')) {
                    $a .= '<button type="button" class="btn btn-xs btn-primary" data-id="'.$datas->id.'" data-toggle="modal" data-target="#push_templates" data-href="'.url('template-send/'.$datas->id).'"><i class="fa fa-send"></i> Select</button> &nbsp; ';
                }
                else {
                    if (Gate::allows('Push-Notification-Templates_update')) {
                        $a .= '<button type="button" class="btn btn-xs btn-success" data-id="'.$datas->id.'" data-toggle="modal" data-target="#push_templates" data-href="'.url('notification-templates/'.$datas->id).'/edit"><i class="fa fa-pencil-square-o"></i> Edit</button> &nbsp; ';
                    }
                }


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
        if (Gate::denies('Push-Notification-Templates_create')) {
            return response()->json(["status" => 200, 'message' => 'You dont have sufficient privlilege to access this area']);
        }
        return view('admin.modules.notifications.templates.create');
    }

    public function send($id) {
        $data = NotificationTemplate::find($id);
        return view('admin.modules.notifications.templates.send', compact('data'));
    }

    public function sendPost(Request $request, $id) {
        $data = NotificationTemplate::find($id);
        $traders = TraderUser::whereIn('id', session()->get('selected_traders'))->get();
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();
        $notifications             = new GenaralNotification();
        $notifications->title      = $data->title;
        $notifications->body       = $data->body;
        $notifications->user_count = count($traders);
        $notifications->save();

        if(!empty($traders)) {
            foreach($traders as $trader) {
                $new_notification = new Notifications();
                $new_notification->title = $data->title;
                $new_notification->desc = $data->body;
                $new_notification->trader_id = $trader->id;
                $new_notification->notification_id = $notifications->id;
                $new_notification->save();

                // $trader = TraderUser::find($trader->id);
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
            session()->forget('selected_traders');
        }

        return response()->json(["success" => true, 'msg' => 'Notification send successfully.']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('Push-Notification-Templates_create')) {
            return response()->json(array('success' => false, 'msg' => 'You dont have sufficient privlilege to access this area'), 200);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        $exist = NotificationTemplate::where('title', $request->title)->where('body', $request->body)->first();
        if ($exist) {
            return response()->json(array('success' => false, 'msg' => 'Template alerdy exist!.'), 200);
        }

        $data = new NotificationTemplate();
        $data->title = $request->title;
        $data->body = $request->body;
        $data->save();
        return response()->json(array('success' => true, 'msg' => 'Template successfully created.'), 200);
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
        if (Gate::denies('Push-Notification-Templates_update')) {
            return response()->json(array('success' => false, 'msg' => 'You dont have sufficient privlilege to access this area'), 200);
        }
        $data = NotificationTemplate::find($id);
        return view('admin.modules.notifications.templates.create', compact('data'));

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
        if (Gate::denies('Push-Notification-Templates_update')) {
            return response()->json(array('success' => false, 'msg' => 'You dont have sufficient privlilege to access this area'), 200);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        $exist = NotificationTemplate::where('title', $request->title)->where('body', $request->body)->where('id', '!=', $id)->first();
        if ($exist) {
            return response()->json(array('success' => false, 'msg' => 'Template alerdy exist!.'), 200);
        }

        $data = NotificationTemplate::find($id);
        $data->title = $request->title;
        $data->body = $request->body;
        $data->save();
        return response()->json(array('success' => true, 'msg' => 'Template successfully updated.'), 200);
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
