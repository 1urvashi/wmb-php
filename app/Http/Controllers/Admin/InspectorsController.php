<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Validator;
use Datatables;
use DB;
use Auth;
use App\InspectorUser;
use App\InspectorActivity;
use App\DealerUser;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mail;
use GuzzleHttp;
use App\Object;
use Excel;
use App\ObjectAttributeValue;
use PDF;
use Illuminate\Support\Str;
use Redirect;
use Gate;
use DateTime;
use DateTimeZone;
use App\InspectorSource;

class InspectorsController extends Controller
{
     public function __construct(){
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('inspectorsMenu')){
        //      return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InspectorUser $inspector)
    {
        if(Gate::denies('inspectors_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        $inspector_sources = InspectorSource::where('status', 1)->get();
        $user = Auth::guard('admin')->user();
        return view('admin.modules.inspector.index', compact('dealers', 'user', 'inspector_sources'));
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
        $inspectors = InspectorUser::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','name', 'email','dealer_id', 'source_id'])->orderBy('id', 'desc')->get();
        return Datatables::of($inspectors, $user)
            ->addColumn('action', function ($inspectors) use($user) {
                 $b = '';
                 if (Gate::allows('inspectors_update')) {
                     $b .= '<a href="inspectors/' . $inspectors->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a> ';
                 }
                 if (Gate::allows('inspectors_read')) {
                    $b .= '<a href="inspector-activity/' . $inspectors->id .'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> Activity</a> ';
                 }
                 if (Gate::allows('inspectors_delete')) {
                     $b .= '<a href="inspectors/destroy/' . $inspectors->id . '" onclick="return confirm(\'Are you sure you want to delete this Inspector User?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                 }
                 if (Gate::allows('vehicles_read')) {
                     $b .= '&nbsp; <a href="inspectors/vehicle/'.$inspectors->id.'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View Vehicles</a>';
                 }
                return $b;
            })
            ->editColumn('source_id', function ($inspectors) use($user) {
                $inspector_source = InspectorSource::withTrashed()->where('id', $inspectors->source_id)->first();
                return !empty($inspector_source) ? $inspector_source->title : 'N/A';
            })
             ->filter(function ($instance) use ($request) {
                 if ($request->has('dealer') && ($request->get('dealer')!=0)) {
                     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                         return ($row['dealer_id'] == $request->get('dealer')) ? true : false;
                     });
                 }
                 if ($request->has('inspector_sources') && ($request->get('inspector_sources')!='0')) {
                     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                         return ($row['source_id'] == $request->get('inspector_sources')) ? true : false;
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
    public function create()
    {
        if(Gate::denies('inspectors_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        $inspector_sources = InspectorSource::where('status', 1)->get();
        return view('admin.modules.inspector.create', compact('dealers', 'inspector_sources'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        if(Gate::denies('inspectors_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'dealer_id'=>'required',
            'source_id'=>'required',
            'email' => 'required|email|max:255|unique:inspector_users',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $inspector_sources_name = InspectorSource::where('id', $request->source_id)->first()->title;
        
        // Need to update the name checking for future
        if($inspector_sources_name == 'Wecashanycar (Internal)') {
            if(empty($request->dealer_id)) {
                return redirect()->back()->with('error', 'Please select the branche')->withInput();
            }
        }
        $inspector = new InspectorUser();
        $data = $request->all();
        $data['account'] = 'Inspector';
        try {
            $mail =Mail::send('emails.registration_inspector', $data, function ($message) use ($data) {
                $message->to($data['email']);
                $message->subject($data['account'].' Account Created');
            });
        } catch (\Swift_TransportException $e) {
            Log::error($e->getMessage());
        }
        unset($data['account']);
        $data['password'] = bcrypt($request->password);
        $data['api_token'] = str_random(60);
        $inspector->create($data);
        return redirect('inspectors')->with('success', 'Successfully added new Inspector');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(InspectorUser $inspector)
    {
        if(Gate::denies('inspectors_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.inspector.show', compact('inspector'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectorUser $inspector)
    {
        if(Gate::denies('inspectors_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        $inspector_sources = InspectorSource::where('status', 1)->get();
        return view('admin.modules.inspector.create', compact('inspector', 'dealers', 'inspector_sources'));
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
        if(Gate::denies('inspectors_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'dealer_id'=>'required',
            'source_id'=>'required',
            'email' => 'required|email|max:255',
        ]);
        $exist = InspectorUser::where('id', '!=', $inspector->id)->where('email', $request->email)->first();
        if($exist) {
             return redirect()->back()->with('error', 'The inspector user already exist!!')->withInput();
        }
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $inspector_sources_name = InspectorSource::where('id', $request->source_id)->first()->title;
        
        // Need to update the name checking for future
        if($inspector_sources_name == 'Wecashanycar (Internal)') {
            if(empty($request->dealer_id)) {
                return redirect()->back()->with('error', 'Please select the branche')->withInput();
            }
        }
        $data = $request->all();
        if ($request->has('password')) {
            $data['account'] = 'Inspector';
            try {
                $mail =Mail::send('emails.registration_edit', $data, function ($message) use ($data) {
                    $message->to($data['email']);
                    $message->subject($data['account'].' Account Updated');
                });
            } catch (\Swift_TransportException $e) {
                Log::error($e->getMessage());
            }
            unset($data['account']);
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }
        $inspector->update($data);
        return redirect('inspectors')->with('success', 'Successfully updated Inspector');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Gate::denies('inspectors_delete')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $inspector = InspectorUser::findOrFail($id);
        $inspector->session_id = null;
        $inspector->save();

        $inspector->delete();
        return redirect('inspectors')->with('success', 'Inspector Deleted Successfully');
    }

    public function vehicle($id)
    {
        if(Gate::denies('vehicles_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.inspector.vehicles.index', compact('id'));
    }

    public function vehicleData(Request $request, $id = null)
    {
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $objects = \App\Object::where('inspector_id', $id)
                                   ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'objects.id','objects.name', 'objects.code', 'objects.dealer_id',
                                   'objects.customer_name', 'objects.customer_mobile','objects.customer_email','objects.customer_reference','objects.source_of_enquiry','makes.name as makeName',
                                   'models.name as modelName', 'objects.vin', 'inspector_users.name as inspectorName', 'objects.created_at'])
                                   ->leftjoin('auctions', 'auctions.object_id', '=', 'objects.id')
                                   ->leftjoin('models', 'models.id', '=', 'objects.model_id')
                                   ->leftjoin('makes', 'makes.id', '=', 'objects.make_id')
                                   ->leftjoin('inspector_users', 'inspector_users.id', '=', 'objects.inspector_id')
                                   ->where('objects.images_uploaded', 1)
                                   ->groupBy('objects.id')
                                   ->orderBy('objects.created_at', 'desc')->get();
        return Datatables::of($objects, $user)
                ->addColumn('action', function ($objects) use($user) {
                     $b = '';
                     if(Gate::allows('vehicles_delete')){
                         $b .= '<a href="../../objects/destroy/' . $objects->id . '" disabled onclick="return confirm(\'Are you sure you want to delete this Object?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                     }
                     if (Gate::allows('vehicles_read')) {
                         $b .= '&nbsp; <a href="../../object/detail/'.$objects->id.'" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> View Details</a>';
                     }
                    return $b;
                    // return '<a href="../../object/detail/'.$objects->id.'" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> View Details</a>
                    // <a href="../../objects/destroy/' . $objects->id . '" disabled onclick="return confirm(\'Are you sure you want to delete this Object?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                })
                ->addColumn('created_at', function ($objects) {
                     return date('Y-m-d h:i:s A', strtotime($this->UaeDate($objects->created_at)));
                    //return date('d/M/Y', strtotime($objects->created_at));
                })
                ->filter(function ($instance) use ($request) {


                     if ($request->has('searchTitle')) {
                          $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                              return Str::contains($row['vin'], $request->get('searchTitle')) ? true : false;
                          });
                     }

                     if ($request->has('from') && ($request->has('to'))) {
                         $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                             return (($row['created_at']  >= $request->get('from').' 00:00:00') && ($row['created_at']  <= $request->get('to').' 23:59:59'))? true : false;
                         });
                     }

                    if ($request->has('from') && ($request->has('to'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return (($row['created_at']  >= $request->get('from').' 00:00:00') && ($row['created_at']  <= $request->get('to').' 23:59:59'))? true : false;
                        });
                    } elseif ($request->has('from')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return (($row['created_at']  >= $request->get('from').' 00:00:00'))? true : false;
                        });
                    } elseif ($request->has('to')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return (($row['created_at']  <= $request->get('to').' 23:59:59'))? true : false;
                        });
                    }
               })
                ->make(true);
    }


    public function exportCsvVehicle(Request $request, $inspectorId)
    {
        if(Gate::denies('vehicles_export')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $fileName = 'vehicles_'.time();
        if ($request->exportSubmit == "Export CSV") {
            Excel::create($fileName, function ($excel) use ($inspectorId, $request) {
                $excel->sheet('vehicles', function ($sheet) use ($inspectorId, $request) {
                    $dataExported = ['objects.created_at as date','objects.code','objects.customer_name','objects.customer_mobile','objects.customer_reference','objects.source_of_enquiry',
                                              'objects.customer_email', 'makes.name as makeName', 'models.name as modelName','objects.vin', 'inspector_users.email as inspectorEmail',
                                              'inspector_users.id as inspectorId','objects.id as vehicleId', 'object_attribute_values.attribute_value','object_attribute_values.color'];


                    $objects = Object::leftjoin('models', 'models.id', '=', 'objects.model_id')
                                             ->leftjoin('makes', 'makes.id', '=', 'objects.make_id')
                                             ->leftjoin('object_attribute_values', 'object_attribute_values.object_id', '=', 'objects.id')
                                             ->leftjoin('attributes', 'attributes.id', '=', 'object_attribute_values.attribute_id')
                                             ->leftjoin('inspector_users', 'inspector_users.id', '=', 'objects.inspector_id')
                                             ->where('attributes.exportable', 1)
                                             ->where('inspector_users.id', $inspectorId)
                                             ->where('objects.images_uploaded', 1)
                                             ->where('attributes.exportable', 1)
                                             ->groupBy('objects.id')->orderBy('objects.created_at', 'desc');

                    if ($request->has('from_date')) {
                        $objects->where('objects.created_at', '>=', $request->get('from_date').' 00:00:00');
                    }
                    if ($request->has('to_date')) {
                        $objects->where('objects.created_at', '<=', $request->get('to_date').' 23:59:59');
                    }

                    if ($request->has('searchTitle')) {
                        $objects->where('objects.vin', 'like', '%' . $request->get('searchTitle') . '%');
                        //$objects->where('objects.vin', '=', $request->get('searchTitle'));
                    }

                    // dd($objects);
                    $attributesId = \App\Attribute::where('attributes.exportable', 1)->orderBy('attributes.id', 'asc')->pluck('id')->toArray();

                    // dd($objects-);
                    $attributesNames = \App\Attribute::where('attributes.exportable', 1)->orderBy('attributes.id', 'asc')->pluck('name')->toArray();
                    $headers = ['Date', 'Inspection number','Name of customer','Mobile number','Customer ref number','Source of enquiry','Email', 'Make','Model','VIN','Inspector email','Inspector number'];
                    $merg = array_merge($headers, $attributesNames);

                    // var_dump($merg); exit;

                    $sheet->fromArray(array($merg), null, 'A1', false, false);

                    if (!empty($objects->get($dataExported))) {
                        foreach ($objects->get($dataExported) as $value) {
                            $objectAttributeValue = ObjectAttributeValue::whereIn('attribute_id', $attributesId)->where('object_id', $value->vehicleId)->pluck('attribute_value')->toArray();
                            // dd($objectAttributeValue);
                            $data = [$value->date, $value->vehicleId, $value->customer_name, $value->customer_mobile, $value->customer_reference,
                                            $value->source_of_enquiry, $value->customer_email, $value->makeName, $value->modelName, $value->vin, $value->inspectorEmail, $value->inspectorId];
                            // dd($objectAttributeValue);
                            $merg = array_merge($data, $objectAttributeValue);
                            // dd($merg);
                            $test = $sheet->fromArray(array($merg), null, 'A1', false, false);
                        }
                    }
                });
            })->download('csv');
        } else {
            $dataExported = ['objects.created_at as date','objects.code','objects.customer_name','objects.customer_mobile','objects.customer_reference','objects.source_of_enquiry',
                                      'objects.customer_email', 'makes.name as makeName', 'models.name as modelName','objects.vin', 'inspector_users.email as inspectorEmail',
                                      'inspector_users.id as inspectorId','objects.id as vehicleId', 'object_attribute_values.attribute_value','object_attribute_values.color'];

            $objectList = Object::leftjoin('models', 'models.id', '=', 'objects.model_id')
                                     ->leftjoin('makes', 'makes.id', '=', 'objects.make_id')
                                     ->leftjoin('object_attribute_values', 'object_attribute_values.object_id', '=', 'objects.id')
                                     ->leftjoin('attributes', 'attributes.id', '=', 'object_attribute_values.attribute_id')
                                     ->leftjoin('inspector_users', 'inspector_users.id', '=', 'objects.inspector_id')
                                     ->where('attributes.exportable', 1)
                                     ->where('inspector_users.id', $inspectorId)
                                     ->where('objects.images_uploaded', 1)
                                     ->where('attributes.exportable', 1)
                                     ->groupBy('objects.id')->orderBy('objects.created_at', 'desc');

            if ($request->has('from_date')){
                $objectList->where('objects.created_at', '>=', $request->get('from_date').' 00:00:00');
            }
            if ($request->has('to_date')) {
                $objectList->where('objects.created_at', '<=', $request->get('to_date').' 23:59:59');
            }

            $objects =  $objectList->get($dataExported);
            // dd($objects);
            $attributesId = \App\Attribute::where('attributes.exportable', 1)->orderBy('attributes.id', 'asc')->pluck('id')->toArray();


            $attributesNames = \App\Attribute::where('attributes.exportable', 1)->orderBy('attributes.id', 'asc')->pluck('name')->toArray();
            $headers = ['Date', 'Inspection number','Name of customer','Mobile number','Customer ref number','Source of enquiry','Email', 'Make','Model','VIN','Inspector email','Inspector number'];

            $headMerg = array_merge($headers, $attributesNames);
            // return $merg;

            $pdf = PDF::loadView('admin.modules.inspector.vehicles.list_pdf', compact('headMerg', 'objects', 'attributesId'));

            $pdf->setPaper('a2', 'landscape');

            // return view('admin.modules.inspector.vehicles.list_pdf', compact('headMerg', 'objects','attributesId'));

            return $pdf->download($fileName.'.pdf');
        }
    }

    public function trashed() {
        if(Gate::denies('inspectors_trashed-read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        $inspector_sources = InspectorSource::where('status', 1)->get();
        $user = Auth::guard('admin')->user();
        return view('admin.modules.inspector.index-trashed', compact('dealers', 'user', 'inspector_sources'));
    }

    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function trashedData(Request $request)
    {
        $user = Auth::guard('admin')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $inspectors = InspectorUser::onlyTrashed()->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','name', 'email','dealer_id', 'source_id'])->orderBy('id', 'desc')->get();
        return Datatables::of($inspectors, $user)
            ->addColumn('action', function ($inspectors) use($user) {
                 $b = '';
                 if (Gate::allows('inspectors_trashed-restore')) {
                     $b .= '<a href="../inspectors-restrore/' . $inspectors->id . '" class="btn btn-xs btn-success"><i class="fa fa-pencil-square-o"></i> Restrore</a> ';
                 }
                return $b;
            })
            ->editColumn('source_id', function ($inspectors) use($user) {
                $inspector_source = InspectorSource::withTrashed()->where('id', $inspectors->source_id)->first();
                return !empty($inspector_source) ? $inspector_source->title : 'N/A';
            })
             ->filter(function ($instance) use ($request) {
                 if ($request->has('dealer') && ($request->get('dealer')!=0)) {
                     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                         return ($row['dealer_id'] == $request->get('dealer')) ? true : false;
                     });
                 }
                 if ($request->has('inspector_sources') && ($request->get('inspector_sources')!='0')) {
                     $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                         return ($row['source_id'] == $request->get('inspector_sources')) ? true : false;
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

    public function restrore($id) {
        if(Gate::denies('inspectors_trashed-restore')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $inspector = InspectorUser::onlyTrashed()->where('id', $id)->first();
        $inspector->deleted_at =null;
        $inspector->save();
        return redirect('inspectors')->with('success', 'Successfully restored inspector');
    }

/*
    public function exportPdfVehicle($inspectorId)
    {
        $dataExported = ['objects.created_at as date','objects.code','objects.customer_name','objects.customer_mobile','objects.customer_reference','objects.source_of_enquiry',
                                  'objects.customer_email', 'makes.name as makeName', 'models.name as modelName','objects.vin', 'inspector_users.name as inspectorName',
                                  'inspector_users.id as inspectorId','objects.id as vehicleId', 'object_attribute_values.attribute_value','object_attribute_values.color'];

        $objects = Object::leftjoin('models', 'models.id', '=', 'objects.model_id')
                                 ->leftjoin('makes', 'makes.id', '=', 'objects.make_id')
                                 ->leftjoin('object_attribute_values', 'object_attribute_values.object_id', '=', 'objects.id')
                                 ->leftjoin('attributes', 'attributes.id', '=', 'object_attribute_values.attribute_id')
                                 ->leftjoin('inspector_users', 'inspector_users.id', '=', 'objects.inspector_id')
                                 ->where('attributes.exportable', 1)
                                 ->where('inspector_users.id', $inspectorId)
                                 ->where('objects.images_uploaded', 1)
                                 ->where('attributes.exportable', 1)
                                 ->groupBy('objects.id')
                                 ->get($dataExported);
        // dd($objects);
        $attributesId = \App\Attribute::where('attributes.exportable', 1)->orderBy('attributes.id', 'asc')->pluck('id')->toArray();


        $attributesNames = \App\Attribute::where('attributes.exportable', 1)->orderBy('attributes.id', 'asc')->pluck('name')->toArray();
        $headers = ['Date', 'Inspection number','Name of customer','Mobile number','Customer ref number','Source of enquiry','Email', 'Make','Model','VIN','Inspector name','Inspector number'];

        $headMerg = array_merge($headers, $attributesNames);
        // return $merg;

        $pdf = PDF::loadView('admin.modules.inspector.vehicles.list_pdf', compact('headMerg', 'objects', 'attributesId'));

        $pdf->setPaper('a2', 'landscape');

        // return view('admin.modules.inspector.vehicles.list_pdf', compact('headMerg', 'objects','attributesId'));

        return $pdf->download('vehicles.pdf');
}*/


    public function export(){
        if(Gate::denies('inspectors_export')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $fileName = 'inspectors_'.time();
        $dataExported = ['id', 'name','email'];
        $datas = InspectorUser::where('id','!=',0)->get($dataExported);

        $dataArray = [];
        $dataArray[] = $dataExported;
        foreach ($datas as $data) {
            $dataArray[] = $data->toArray();
        }
        Excel::create($fileName, function($excel) use ($dataArray) {
            $excel->setTitle('Inspectors');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Inspectors file');
            $excel->sheet('sheet1', function($sheet) use ($dataArray) {
                $sheet->fromArray($dataArray, null, 'A1', false, false);
            });

        })->download('csv');
    }

    public function activity($id) {
        $inspector_id = $id;
        $activities = InspectorActivity::where('inspector_id', $id)->get();
        return view('admin.modules.inspector.activity.index', compact('inspector_id'));


        // dd($activities);
    }

    public function activityData($id) {
        $datas = InspectorActivity::where('inspector_id', $id)->groupBy('object_id')->pluck('object_id')->toArray();
        $objects = Object::whereIn('id', $datas)->get();
        return Datatables::of($objects)
                            ->addColumn('action', function ($objects) {
                            return '<a href="../inspector-activity-object/' . $objects->id . '" class="btn btn-xs btn-primary"><i class="fa fa-clock-o"></i> Timeline</a> ';

                        })->make(true);

    }

    public function objectActivity($id) {
        $object_id = $id;
        $object_name = Object::where('id', $object_id)->first()->name;
        return view('admin.modules.inspector.activity.object.index', compact('object_id', 'object_name'));
    }

    public function activityObjectData($id) {
        $activities = InspectorActivity::where('object_id', $id)->orderBy('created_at', 'desc')->get();
        return Datatables::of($activities)
                        ->editColumn('start_time', function ($activities) {
                            $start_time = $activities->start_time;
                            return $start_time;

                        })
                        ->editColumn('end_time', function ($activities) {
                            $end_time = $activities->end_time;
                            return $end_time;

                        })
                        ->editColumn('session_start_time', function ($activities) {
                            $session_start_time = $activities->session_start_time;
                            return date('Y-m-d h:i:s A', strtotime($session_start_time));

                        })
                        ->editColumn('spending_time', function ($activities) {
                            $datetime1 = new DateTime($activities->start_time);//start time
                            $datetime2 = new DateTime($activities->end_time);//end time
                            $interval = $datetime1->diff($datetime2);
                            // $spend_time = $interval->format('%Y years %m months %d days %H hours %i minutes %s seconds');//00 years 0 months 0 days 08 hours 0 minutes 0 seconds
                            $spend_time = $interval->format('%d days %H hours %i minutes %s seconds');//00 years 0 months 0 days 08 hours 0 minutes 0 seconds
                            return $spend_time;

                        })->make(true);

    }

    public function UaeDate($dts) {
        $date = new DateTime($dts, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Asia/Dubai'));
        return $date->format('Y-m-d H:i:s');
    }


}
