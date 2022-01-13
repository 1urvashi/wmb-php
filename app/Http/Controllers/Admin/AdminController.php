<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AdminNotification;
use Gate;
use Redirect;

class AdminController extends Controller
{
    public function getNewVehicle(Request $request)
    {

         $time = date("Y-m-d H:i:s", time() - 5);
         // dd($time);
         // return $time;
         // $afterfive = date('Y-m-d h:i:s A', strtotime("-5 seconds"));
     //     $notifications = AdminNotification::where('read_status', 0)->where('source', 1)->orderBy('id', 'desc')->get();
         $notifications = AdminNotification::leftJoin('inspector_users', 'inspector_users.id', '=', 'admin_notifications.inspector_id')
                                                  ->where('admin_notifications.read_status', 0)->where('admin_notifications.source', 1)->orderBy('admin_notifications.id', 'desc')->get();
         $notificationCount = AdminNotification::where('read_status', 0)->where('source', 1)->where('created_at','>', $time)->count();
         // return $notificationCount;
         return response()->json(['notifications'=>$notifications,  'count'=> $notificationCount]);
    }

    public function notificationStatus($id) {
         $notifications = AdminNotification::where('read_status', 0)->where('source', 1)->get();
         foreach ($notifications as $key => $notification) {
              $notification->read_status = 1;
              $notification->save();
         }
         return;
    }

    public function getOtherNewVehicle(Request $request)
    {
         $time = date("Y-m-d H:i:s", time() - 5);
         $notifications = AdminNotification::leftJoin('inspector_users', 'inspector_users.id', '=', 'admin_notifications.inspector_id')
                                        ->where('admin_notifications.read_status', 0)->where('admin_notifications.source', 2)->orderBy('admin_notifications.id', 'desc')->get();
         $notificationCount = AdminNotification::where('read_status', 0)->where('source', 2)->where('created_at','>', $time)->count();
         // return $notificationCount;
         return response()->json(['notifications1'=>$notifications,  'count1'=> $notificationCount]);
    }

    public function notificationOtherStatus($id) {
         $notifications = AdminNotification::where('read_status', 0)->where('source', 2)->get();
         foreach ($notifications as $key => $notification) {
              $notification->read_status = 1;
              $notification->save();
         }
         return;
    }
}
