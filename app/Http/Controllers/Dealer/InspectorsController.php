<?php

namespace App\Http\Controllers\Dealer;

use Illuminate\Http\Request;

use Validator;
use Datatables;
use DB;
use Auth;
use App\InspectorUser;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mail;
use GuzzleHttp;

class InspectorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InspectorUser $inspector)
    {
        return view('dealer.modules.inspector.index');
    }
    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data() {
        DB::statement(DB::raw('set @rownum=0'));
        $user = Auth::guard('dealer')->user();
        $inspectors = InspectorUser::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','name', 'email']);
        if($user->branch_id == 0) {
             $inspectors = $inspectors->where('dealer_id',$user->id);
        } else {
             $inspectors = $inspectors->where('dealer_id',$user->branch_id);
        }
        $inspectors = $inspectors->orderBy('id', 'desc')->get();
        return Datatables::of($inspectors)
            ->addColumn('action', function ($inspectors) {
                return '<a href="inspectors/' . $inspectors->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>'
                .'&nbsp;<a href="inspectors/vehicle/'.$inspectors->id.'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View Watches</a>

                    <a href="inspectors/destroy/' . $inspectors->id . '" onclick="return confirm(\'Are you sure you want to delete this Inspector?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
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
        return view('dealer.modules.inspector.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|max:255|unique:inspector_users',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = Auth::guard('dealer')->user();
        $inspector = new InspectorUser();
        $data = $request->all();

		$data['account'] = 'Inspector';
        try {
            $mail =Mail::send('emails.registration_inspector', $data, function($message) use ($data) {
                $message->to($data['email']);
                $message->subject($data['account'].' Account Created');
            });
         }  catch (\Swift_TransportException $e){
            Log::error($e->getMessage());
         }
        unset($data['account']);

        $data['password'] = bcrypt($request->password);
        $data['api_token'] = str_random(60);

        $data['dealer_id'] = $user->branch_id == 0 ? $user->id : $user->branch_id;

        $inspector->create($data);
        return redirect('dealer/inspectors')->with('success', 'Successfully added new Inspector');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(InspectorUser $inspector)
    {
        if($inspector->dealer_id != Auth::guard('dealer')->user()->id){
            return redirect()->back()->with('error', 'Not authorized to this page');
        }
        return view('dealer.modules.inspector.show',  compact('inspector'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectorUser $inspector)
    {
        if($inspector->dealer_id != Auth::guard('dealer')->user()->id){
            return redirect()->back()->with('error', 'Not authorized to this page');
        }
        return view('dealer.modules.inspector.create',  compact('inspector'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InspectorUser $inspector)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = Auth::guard('dealer')->user();
        $data = $request->all();
        if($request->has('password')){
            $data['password'] = bcrypt($request->password);
        }else{
            unset($data['password']);
        }
        $inspector->update($data);
        return redirect('dealer/inspectors')->with('success', 'Successfully updated Inspector');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inspector = InspectorUser::findOrFail($id);
        $inspector->session_id = null;
        $inspector->save();

        $inspector->delete();
        return redirect('dealer/inspectors')->with('success', 'Inspector Deleted Successfully');
    }

    public function vehicle($id) {
         return view('dealer.modules.inspector.vehicles.index', compact('id'));
    }

    public function vehicleData(Request $request, $id = null){
        DB::statement(DB::raw('set @rownum=0'));
        $objects = \App\Object::where('inspector_id', $id)
                                   ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'objects.id','objects.name', 'objects.code', 'objects.dealer_id', 'objects.created_at'])->leftjoin('auctions','auctions.object_id','=','objects.id')->where('objects.images_uploaded',1)->orderBy('objects.created_at', 'desc')->get();
            return Datatables::of($objects)
                ->addColumn('action', function ($objects) {
                   return '<a href="../../object/detail/'.$objects->id.'" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> View Details</a>
                    <a href="../../objects/destroy/' . $objects->id . '" disabled onclick="return confirm(\'Are you sure you want to delete this Object?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                })
                ->editColumn('created_at', function($objects) {
                     return date('Y-m-d h:i:s A', strtotime($this->UaeDate($objects->created_at)));
                })
                ->make(true);
    }
}
