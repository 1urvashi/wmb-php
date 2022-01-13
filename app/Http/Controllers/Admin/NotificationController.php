<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Validator;
use DB;
use App\Notifications;
use App\TraderUser;
use App\TraderGroup;
use App\Group;
use App\DealerUser;
use App\User;
use Auth;
use URL;
use Gate;
use App\Auction;
use App\GenaralNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(session()->get('selected_traders'));
        if (Gate::denies('Push-Notification_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        session()->forget('selected_traders');
        $dealers = DealerUser::where('branch_id', 0)->get();
        $drmsUsers = User::where('role', config('globalConstants.OLD_ROLES.DRM_USER'))->where('status', 1)->get();
        $trader_groups = Group::where('status', 1)->get();
        return view('admin.modules.notifications.index', compact('dealers', 'drmsUsers', 'trader_groups'));
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
        $traders = TraderUser::leftJoin('users', 'users.id', '=', 'trader_users.dmr_id')
                            // ->leftJoin('trader_groups', 'trader_groups.trader_id', '=', 'trader_users.id')
                            ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'trader_users.id','trader_users.first_name', 'trader_users.email', 'trader_users.last_name','trader_users.dealer_id', 'trader_users.status', 'trader_users.dmr_id', 'trader_users.last_bid', 'trader_users.deposit_amount', 'users.name', 'trader_users.device_type'])->orderBy('last_bid', 'desc')->get();
        if($request->has('group') && ($request->get('group')!=0)){
            $traders = TraderGroup::where('group_id',$request->input('group'))
                                ->leftJoin('trader_users', 'trader_users.id', '=', 'trader_groups.trader_id')
                                ->leftJoin('users', 'users.id', '=', 'trader_users.dmr_id')
                                ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'trader_users.id','trader_users.first_name', 'trader_users.email', 'trader_users.last_name','trader_users.dealer_id', 'trader_users.status', 'trader_users.dmr_id', 'trader_users.last_bid', 'trader_users.deposit_amount', 'users.name', 'trader_users.device_type', 'trader_groups.trader_id', 'trader_groups.group_id'])->orderBy('last_bid', 'desc')->get();
        }
        return Datatables::of($traders)
            ->editColumn('last_bid', function ($traders) {
                $date = new Auction();
                $now = $this->UaeDate($traders->last_bid);
                return $traders->last_bid ? date('d-m-Y h:i:s A', strtotime($now)) : null;
            })
            ->addColumn('select', function ($traders) use ($user) {
                return '<input type="checkbox" name="traders_id[]" class="trader_check" value="'.$traders->id.'"/>';
            })
             ->filter(function ($instance) use ($request) {
                 if ($request->has('dealer') && ($request->get('dealer')!=0)) {
                     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                         return ($row['dealer_id'] == $request->get('dealer')) ? true : false;
                     });
                 }
                 if ($request->has('drms') && ($request->get('drms')!=0)) {
                     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                         return ($row['dmr_id'] == $request->get('drms')) ? true : false;
                     });
                 }
                 if ($request->has('platform')) {
                    $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                        return ($row['device_type'] == $request->get('platform')) ? true : false;
                    });
                 }
                 if ($request->has('group') && ($request->get('group')!=0)) {
                    $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                        return ($row['group_id'] == $request->get('group')) ? true : false;
                    });
                 }
                 if($request->has('last_bid')) {
                    $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                        if($request->get('last_bid') != -1) {
                            return (date('Y-m-d', strtotime($row['last_bid'])) >= date('Y-m-d',strtotime("-".$request->get('last_bid')." days"))) ? true : false;
                        } else {
                            return $row['last_bid'] == null ? true : false;
                        }

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (Gate::denies('Push-Notification_create')) {
            return view('admin.modules.notifications.permission');
        }
        // session()->put('selected_traders', $request->traders);
        session()->put('selected_traders', json_decode($request->traders));
        return view('admin.modules.notifications.create');
    }

    public function dismiss() {
        session()->forget('selected_traders');
        return ;
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */

    //  public function getTraderId(Request $request) {

    //  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('Push-Notification_create')) {
            return response()->json(["status" => 200, 'message' => 'You dont have sufficient privlilege to access this area']);
        }
        $validator = Validator::make($request->all(), [
            // "title" => "required",
            "body"  => "required"
        ]);

        if ($validator->fails()) {
            $errors = array_combine($validator->errors()->keys(), $validator->errors()->all());
            return response()->json(["status" => 100, 'errors' => $errors]);
        }
        $devices['iosDevices'] = array();
        $devices['androidDevices'] = array();

        $user_count = 0;
        if ($request->user_id == 0) {
            $user_ids = TraderUser::where('status', 1)->pluck('id');
            $user_count = count($user_ids);
        } else {
            $user_ids    = explode(",", $request->user_id);
            $user_count = count($user_ids);
        }
        $notifications             = new GenaralNotification();
        $notifications->title      = 'Wecashanycar';
        $notifications->body       = $request->body;
        $notifications->user_count = $user_count;
        $notifications->save();
        if(!empty($user_ids)) {
            foreach($user_ids as $user_id) {
                $new_notification = new Notifications();
                $new_notification->title = 'Wecashanycar';
                $new_notification->desc = $request->body;
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
            session()->forget('selected_traders');
        }

        session()->forget('selected_traders');
        return response()->json(["status" => 400, 'message' => 'Notification send successfully.']);
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
