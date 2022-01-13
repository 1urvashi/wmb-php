<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Validator;
use DB;
use App\DealerUser;
use App\Object;
use App\ObjectImage;
use App\ObjectAttachment;
use App\Auction;
use App\Attribute;
use Carbon\Carbon;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mail;
use App\Bank;
use PDF;
use Image;
use App\Customer;
use App\AdminNotification;
use GuzzleHttp;
use App\AttributeSet;
use Datatables;
use App\Make;
use App\Models;
use App\ObjectAttributeValue;
use Excel;
use Redirect;
use Gate;
use Storage;
use File;
use DateTime;
use DateTimeZone;

class ObjectsController extends Controller
{
     public function __construct(){
        $user = Auth::guard('admin')->user();

        // if(Gate::denies('printSoldAuction')){
        //      if(Gate::denies('vehiclesMenu')){
        //           return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        //      }
        // }

    }

     //vehiclesMenu

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Object $object)
    {
        if (Gate::denies('vehicles-under-auction_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        return view('admin.modules.object.index', compact('dealers'));
    }
    public function noIndex(Object $object)
    {
        if (Gate::denies('vehicles_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealers = DealerUser::where('branch_id', 0)->get();
        $inspector_sources = \App\InspectorSource::where('status', 1)->get();
        // dd($inspector_sources);
        return view('admin.modules.object.no-index', compact('dealers', 'inspector_sources'));
    }
    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request){
        DB::statement(DB::raw('set @rownum=0'));
        $objects = Object::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'objects.id','objects.name','objects.vin', 'objects.code', 'objects.dealer_id', 'objects.suggested_amount', 'objects.created_at'])->join('auctions','auctions.object_id','=','objects.id')->where('objects.images_uploaded',1)->orderBy('objects.created_at', 'desc')->get();
            return Datatables::of($objects)
                ->addColumn('action', function ($objects) {
                     //<a href="../objects/destroy/' . $objects->id . '" disabled onclick="return confirm(\'Are you sure you want to delete this Object?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>
                     $a = '';
                     if (Gate::allows('vehicles-under-auction_read')) {
                         $a .= '<a href="../object/detail/'.$objects->id.'" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> View Details</a>';
                     }
                     if (Gate::allows('vehicles-under-auction_submit-auction')) {
                         $a .= '<a href="" disabled class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Submit for Auction</a>';
                     }
                     if (Gate::allows('vehicles-under-auction_update')) {
                         $a .= '<a href="../objects/duplicate/' . $objects->id . '" class="btn btn-xs btn-primary duplicate-button"><i class="fa fa-pencil-square-o"></i>Edit Watch</a>';
                     }
                     return $a;

                //    return '<a href="../object/detail/'.$objects->id.'" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> View Details</a>
                //     <a href="" disabled class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Submit for Auction</a>
                //     <a href="../objects/duplicate/' . $objects->id . '" class="btn btn-xs btn-primary duplicate-button"><i class="fa fa-pencil-square-o"></i>Edit Vehicle</a>';
                })
                ->editColumn('created_at', function($objects) {
                     return date('Y-m-d h:i:s A', strtotime($this->UaeDate($objects->created_at)));
                })
                    ->filter(function ($instance) use ($request) {
                        if ($request->has('dealer') && ($request->get('dealer')!=0)) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return ($row['dealer_id'] == $request->get('dealer')) ? true : false;
                                });
                        }
                        if ($request->has('objectName') && ($request->get('objectName')!='')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return (stripos(strtolower($row['vin']),strtolower($request->get('objectName')) ) > -1)? true : false;
                                });
                        }
                    })
                ->make(true);
    }

    public function noData(Request $request) {
        DB::statement(DB::raw('set @rownum=0'));
        $auctions = Auction::select('object_id')->get()->lists('object_id');
        $objects = Object::leftJoin('inspector_users', 'inspector_users.id', '=', 'objects.inspector_id')
                                // ->leftJoin('inspector_sources', 'inspector_sources.id', '=', 'inspector_users.source_id')
                                ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'objects.id','objects.name','objects.vin', 'objects.code', 'objects.dealer_id', 'objects.suggested_amount', 'objects.created_at', 'objects.inspector_id', 'inspector_users.source_id'])
                                ->where('objects.images_uploaded',1)
                                ->whereNotIn('objects.id',$auctions)
                                ->orderBy('objects.created_at', 'desc')->get();

                                // dd($objects);
            return Datatables::of($objects)
                ->addColumn('action', function ($objects) {
                  $user = Auth::guard('admin')->user();
                  $txt = '';
                  if(Gate::allows('vehicles_read'))
                  $txt = '<a href="../object/detail/'.$objects->id.'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View Details</a>';
                  if(Gate::allows('vehicles_delete'))
                  $txt .= '<a href="../objects/destroy/' . $objects->id . '" onclick="return confirm(\'Are you sure you want to delete this Object?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>';
                  if(Gate::allows('vehicles_submit-auction'))
                  $txt .= ' <a href="../auctions/create?id=' . $objects->id . '" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Submit for Auction</a>';
                  if (Gate::allows('vehicles_update'))
                        $txt .= '<a href="../object/edit/'.$objects->id.'" class="btn btn-xs btn-success"><i class="fa fa-pencil-square-o"></i>Edit Watch</a>';

                  return $txt;
                })
                ->addColumn('inspector_source', function ($objects) {
                    // dd($objects);
                    if(!empty($objects->inspector_id)) {
                        $inspector = \App\InspectorUser::withTrashed()->where('id', $objects->inspector_id)->first();
                        $inspector_source = \App\InspectorSource::withTrashed()->where('id', $inspector->source_id)->first();
                        if(!empty($inspector) && !empty($inspector_source)) {
                            $inspector_source_name = $inspector_source->title;
                            return $inspector_source_name;
                        } else {
                            return 'N/A';
                        }
                    } else {
                        return 'N/A';
                    }

                })
                ->editColumn('created_at', function($objects) {
                     return date('Y-m-d h:i:s A', strtotime($this->UaeDate($objects->created_at)));
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
                    if ($request->has('objectName') && ($request->get('objectName')!='')) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return (stripos(strtolower($row['vin']),strtolower($request->get('objectName')) )  > -1) ? true : false;
                            });
                    }
                })
            ->make(true);
    }

    private function CloneObject($objectId){

      $object = Object::find($objectId);

      //duplicate objects
      $newObject =  new Object();
      $newObject->name = trim($object->name);
      $newObject->code = trim($object->code);
      $newObject->parentId = $object->id;
      $newObject->make_id = $object->make_id;
      $newObject->model_id = $object->model_id;
      $newObject->dealer_id = $object->dealer_id;
      $newObject->inspector_id = $object->inspector_id;
      $newObject->images_uploaded = $object->images_uploaded;

    //   $newObject->vin = $object->vin;
    //   $newObject->vehicle_registration_number = $object->vehicle_registration_number;
      $newObject->variation = $object->variation;
      $newObject->customer_name = $object->customer_name;
      $newObject->customer_mobile = $object->customer_mobile;
      $newObject->customer_email = $object->customer_email;
      $newObject->customer_reference = $object->customer_reference;
      $newObject->source_of_enquiry = $object->source_of_enquiry;
      $newObject->suggested_amount = $object->suggested_amount;
      $newObject->bank_id = $object->bank_id;
      $newObject->customer_id = $object->customer_id;
      $newObject->nationality_id = $object->nationality_id;
      $newObject->save();


      //attributes
      foreach ($object->ObjectAttributeValue as  $value) {
          $newObjectAttributeValue = new ObjectAttributeValue();
          $newObjectAttributeValue->object_id = $newObject->id;
          $newObjectAttributeValue->attribute_id = $value->attribute_id;
          $newObjectAttributeValue->attribute_value = $value->attribute_value;
          $newObjectAttributeValue->quality_level = $value->quality_level;
          $newObjectAttributeValue->color = $value->color;
          $newObjectAttributeValue->additional_text = $value->additional_text;
          $newObjectAttributeValue->save();
      }


      //images
      foreach ($object->images as  $value) {
        if(!empty($value->getOriginal('image'))){
            $newObjectImage = new ObjectImage();
            $newObjectImage->object_id = $newObject->id;
            $newObjectImage->image = $value->getOriginal('image');
            $newObjectImage->sort = $value->sort;
            $newObjectImage->save();
        }
      }

      //Attachment

      foreach ($object->attachments as  $avalue) {
        if(!empty($avalue->getOriginal('attachment'))){
            $addAttechment = new ObjectAttachment();
            $addAttechment->object_id = $newObject->id;
            $addAttechment->attachment = $avalue->getOriginal('attachment');
            $addAttechment->save();
        }
      }

    

      return $newObject->id;


    }

    public function duplicateObject(Request $request)
    {
        $this->CloneObject($request->id);
        return redirect('objects/noauction')->with('success', 'Object duplicated Successfully');
    }

    public function reopenAuction(Request $request)
    {

        $auction = Auction::find($request->id);

        $user = Auth::guard('admin')->user();
        if(Gate::denies('auction-button_reopen')){
          return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }

        if(empty($auction->object_id)){
            return redirect()->back()->with('success', 'Unable to reopen');
        }

        $objectId = $auction->object_id;
        $newObjectId = $this->CloneObject($objectId);

        return redirect('auctions/create?id='.$newObjectId); //->with('success', 'Object duplicated Successfully');
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     $Object = new Object;
    //     $bank = Bank::select('name','id')->where('status', 1)->get();
    //     $make = Make::select('name','id')->get();
	// 	$model = Models::select('name','id')->get();

    //     $sourceOfEnquiry = config('globalConstants.sourceOfEnquiry');
    //     // dd( $bank);
    //     return view('admin.modules.object.create',compact('bank','sourceOfEnquiry','make','model'));
    // }
    public function create()
    {
        $Object = new Object;
        $bank = Bank::select('name','id')->where('status', 1)->get();
        $makes = Make::orderBy('name','asc')->get();
        $attributeSet = AttributeSet::orderBy('sort','asc')->get();

        $model = Models::select('name','id')->get();

        $sourceOfEnquiry = config('globalConstants.sourceOfEnquiry');

        $dealers = DealerUser::where('branch_id', 0)->get();

        $attributes = Attribute::where('status', 1)->orderBy('sort','asc')->get();
        $data=array();
        foreach($attributeSet as $set) {
            foreach ($attributes as  $value) {
                if ($set->id == $value->attribute_set_id) {
                    $data[$set->slug][]=$value;
                }
            }
        }
        // dd($data);
        return view('admin.modules.object.create',compact('bank', 'makes','sourceOfEnquiry', 'attributeSet', 'data','model','dealers'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $exist = Object::find($request->object_id);
        $object = $request->has('object_id') ? $exist : new Object();

        $object->name = $request->name;
        $object->code =  unique_random('objects', 'code', 60);
        // $object->inspector_id = 16;
        $object->dealer_id = $request->dealer;
        $object->model_id = $request->model;
        $object->make_id = $request->make;
        $object->save();
       

        $notification = new AdminNotification();
        $notification->messages = "test created new watch";
        $notification->inspector_id = 16;
        $notification->source = 1;
        $notification->save();

        if ($request->has('attributeValue') && count($request->get('attributeValue'))) {
           
            foreach ($request->get('attributeValue') as $key=>$attributes) {

                $objectAttribute = new ObjectAttributeValue();                $objectAttribute->object_id = $object->id;
                $objectAttribute->attribute_id = $key;
                $objectAttribute->attribute_value = $attributes;
                $objectAttribute->quality_level ='No Color';
                $objectAttribute->color = 0;
                $objectAttribute->additional_text = 'test';
                $objectAttribute->save();

            }
        }

       
        $objectId = $object->id;
        $images = $request->images;
        $attachments = $request->attachment;

        // delete images if exist
         $imageExist = ObjectImage::where('object_id', $objectId)->first();
         if (!empty($imageExist)) {
             ObjectImage::where('object_id', $objectId)->delete();
         }

        $images = $request->file('images');
        $path = 'object/';
        $dir = config('app.fileDirectory') . $path;
        foreach ($images as $key => $image) {
            //dd(File::mimeType($image) != 'application/pdf');
            if ($image) {
                if (File::mimeType($image) != 'application/pdf') {
                    $img = Image::make($image);
                    // dd($img);
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $name = time() . uniqid() .'_vehicle'. '.' . $image->getClientOriginalExtension();
                   // $name = $timestamp . $key . '-' . $str . $image->getClientOriginalName();

                    Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    $data[$key] = $name;

                    $addImage = new ObjectImage();
                    $addImage->object_id = $objectId;
                    $addImage->image = $name;
                    $addImage->save();
                    /* $timestamp = Date('y-m-d-H-i-s');
                      $str = str_random(5);
                      $name = $timestamp . $key.'-'.$str. $image->getClientOriginalName();
                      $data[$key] = $name;
                      $image->move(public_path() . '/uploads/traders/images/', $name); */
                } else {
                //    dd('else' );
                    $img = $image;
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $name = time() . uniqid() .'_vehicle'. '.' . $image->getClientOriginalExtension();
                    // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    Storage::disk('s3')->put($dir . $name, file_get_contents($img->getRealPath()), 'public');
                    $data[$key] = $name;
                }
                $object = Object::find($objectId);
                $object->images_uploaded = 1;
                $object->save();
            }
        }

         // delete Attachment if exist
         $attachmentExist = ObjectAttachment::where('object_id', $objectId)->first();
         if (!empty($attachmentExist)) {
            ObjectAttachment::where('object_id', $objectId)->delete();
         }

        $attachments = $request->file('attachment');
        $path = 'attachment/';
        $dir = config('app.fileDirectory') . $path;
        foreach ($attachments as $key => $attachments) {
            //dd(File::mimeType($attachments) != 'application/pdf');
            if ($attachments) {
                if (File::mimeType($attachments) != 'application/pdf') {
                    $img = Image::make($attachments);
                    // dd($img);
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $aname = time() . uniqid() .'_document'. '.' . $attachments->getClientOriginalExtension();
                   // $name = $timestamp . $key . '-' . $str . $attachments->getClientOriginalName();

                    Storage::disk('s3')->put($dir . $aname, $img->stream()->detach(), 'public');
                    $data[$key] = $aname;

                    $addAttechment = new ObjectAttachment();
                    $addAttechment->object_id = $objectId;
                    $addAttechment->attachment = $aname;
                    $addAttechment->save();
                    /* $timestamp = Date('y-m-d-H-i-s');
                      $str = str_random(5);
                      $name = $timestamp . $key.'-'.$str. $attachments->getClientOriginalName();
                      $data[$key] = $name;
                      $attachments->move(public_path() . '/uploads/traders/images/', $name); */
                } else {
                 //   dd('else' );
                    $img = $attachments;
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $aname = time() . uniqid() .'_document'. '.' . $attachments->getClientOriginalExtension();
                    // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    Storage::disk('s3')->put($dir . $aname, file_get_contents($img->getRealPath()), 'public');
                    $data[$key] = $aname;
                }
                
            }
        }

        return redirect('objects/noauction')->with('success', 'Successfully added new watch');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Object $object)
    {
        if (Gate::denies('vehicles_read')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.object.show',  compact('object'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Object $object)
    {
        return view('admin.modules.object.create',  compact('object'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Object $object)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Object::findOrFail($id);
        $object->delete();
        return redirect()->back()->with('success', 'Object Deleted Successfully');
    }

    public function objectDetails(Request $request,$id) {
        $data=array();
        $object = Object::find($id);
// dd($object);
		$make = Make::where('id', $object->make_id)->first()->name;
		$model = Models::where('id', $object->model_id)->first()->name;

        foreach ($object->ObjectAttributeValue as  $value) {
          //  dd($value->attribute->attributeSet);
            $data[$value['attribute']['attributeSet']['slug']][]=$value;
        }
        // dd($object);
        //dd($object->ObjectAttributeValue[1]->attribute);
        $attributeSet = AttributeSet::orderBy('sort','asc')->get();
        // dd($attributeSet);
        if($request->has('type') && ($request->type == 'detail')){
            $bidAmount = $auction->bid->first()->bidding_price;
            return view('admin.trader.detail',compact('object','auction','attributeSet','bidAmount', 'make', 'model'));
        }
        //dd($attributeSet);

        return view('admin.modules.auction.auction-detail',compact('object','attributeSet','data', 'make', 'model'));
    }

    //downloadSold
    public function downloadSold($id){
         $fileName = 'auction_'.time();
         $data=array();
         $auction = Auction::find($id);

         $user = Auth::guard('admin')->user();
         if(Gate::denies('auction-button_download-and-print')){
           return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
         }
         $object = Object::find($auction->object_id);

         $make = Make::where('id', $object->make_id)->first()->name;
         $model = Models::where('id', $object->model_id)->first()->name;

         // $object = Object::leftjoin('models', 'models.id', '=', 'objects.model_id')
         //                           ->leftjoin('makes', 'makes.id', '=', 'objects.make_id')
         //                           // ->Join('object_attribute_values', 'object_attribute_values.object_id', '=', 'objects.id')
         //                           ->where('objects.id', $auction->object_id)
         //                           ->select('models.name as modelName', 'makes.name as makeName', 'objects.name', 'objects.vin', 'objects.vehicle_registration_number')->first();

         foreach ($object->ObjectAttributeValue as  $value) {
            $data[$value->attribute->attributeSet->slug][]=$value;
        }

        $attributeSet = AttributeSet::orderBy('sort','asc')->get();
        // return view('admin.modules.object.list_pdf_sold', compact('object','auction', 'attributeSet', 'data', 'make', 'model'));
         $pdf = PDF::loadView('admin.modules.object.list_pdf_sold', compact('object','auction', 'attributeSet', 'data', 'make', 'model'));
         //
         $pdf->setPaper('a4', 'portrait');

         // return view('admin.modules.object.list_pdf_sold', compact('object','auction', 'attributeSet', 'data' ,'make', 'model'));

         return $pdf->download($fileName.'.pdf');

    }

    public function download($id){
         ini_set('memory_limit','-1');
         ini_set('max_execution_time', 60000); //60000 seconds = 5 minutes
         ini_set('max_input_time ', 60000); //60000 seconds = 5 minutes
         $fileName = 'vehicles_'.time();
         $data=array();

         $object = Object::leftjoin('models', 'models.id', '=', 'objects.model_id')
                                  ->leftjoin('makes', 'makes.id', '=', 'objects.make_id')
                                  ->leftjoin('inspector_users', 'inspector_users.id', '=', 'objects.inspector_id')
                                  ->select('objects.name', 'objects.variation', 'objects.vin', 'objects.vehicle_registration_number', 'objects.customer_name', 'objects.customer_mobile', 'objects.customer_mobile',
                                   'objects.customer_email', 'objects.customer_reference', 'objects.source_of_enquiry', 'objects.created_at', 'objects.inspector_id', 'objects.dealer_id', 'objects.bank_id')
                                  ->where('objects.id', $id)->first();

         $attributeSet = AttributeSet::orderBy('sort','asc')->get();

         $obj = Object::find($id);
         $make = Make::where('id', $obj->make_id)->first()->name;
         $model = Models::where('id', $obj->model_id)->first()->name;

        //  if(!empty($obj->ObjectAttributeValue)){
        //       foreach ($obj->ObjectAttributeValue as  $value) {
        //          if(!empty($value->attribute->exportable)){
        //               $data[$value->attribute->attributeSet->slug][]=$value;
        //          }
        //       }
        //  }
        $date_time = date('Y-m-d H:i:s');
        $uae_time = $this->UaeDate($date_time);
        $format_date = date('d M Y, h:i A', strtotime($obj->created_at));
        foreach ($obj->ObjectAttributeValue as  $value) {
            $data[$value->attribute->attributeSet->slug][]=$value;
        }

        return view('admin.modules.object.list_pdf', compact('object','attributesId','objectAttributeValue','attributeSet','data', 'make', 'model', 'format_date'));
        $pdf = PDF::loadView('admin.modules.object.list_pdf', compact('object','attributesId','objectAttributeValue','attributeSet','data', 'make', 'model', 'format_date'));

        //  return view('admin.modules.object.list_pdf', compact('object','attributesId','objectAttributeValue','attributeSet','data', 'make', 'model'));
        //  $customPaper = array(0,0,567.00,283.80);
        //  $pdf->setPaper('a5','portrait');

        //  return view('admin.modules.object.list_pdf', compact('object','attributesId','objectAttributeValue','attributeSet','data', 'make', 'model'));

         return $pdf->download($fileName.'.pdf');

    }

    public function objectEdit(Request $request,$objectId) {
      
        $user = Auth::guard('admin')->user();

        // $validator = Validator::make($request->all(), [
        //     'images' => 'required|mimes:jpeg,bmp,png|size:5000',
           
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }

        
        // if(Gate::denies('objectEdit')){
        //      return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }

        $data=array();
        $object = Object::findOrFail($objectId);
        foreach ($object->ObjectAttributeValue as  $value) {
            $data[$value['attribute']['attributeSet']['slug']][]=$value;
        }
        $attributeSet = AttributeSet::orderBy('sort','asc')->get();
        $makes = Make::orderBy('name','asc')->get();
        $models = Models::where('make_id', $object->make_id)->get();
        return view('admin.modules.object.edit',compact('object','attributeSet','data', 'makes', 'models'));
    }

    public function getModels($id) {
        $models = Models::where('make_id', $id)->get();

        $html = '<option value="">Choose Model</option>';
        for ($i=0; $i < count($models) ; $i++) {
            $html .= "<option value='".$models[$i]->id."'>".$models[$i]->name."</option>";
        }
        return $html;
    }

    public function updateObject(Request $request,$objectId) {

        $data=array();
        $attribute = new Attribute();
  
        $object = Object::findOrFail($objectId);
        // dd($request->attributeValue);
        foreach ($request->attributeValue as $key => $value) {
            $objAttrValue = ObjectAttributeValue::where('object_id',$objectId)->where('attribute_id',$key)->first();
            $values = explode('#',$value);
          $objAttrValue->attribute_value = $values[0];
          if(isset($values[1])){
            $objAttrValue->quality_level = $values[1];
            $objAttrValue->color = $values[1];
          }
          $objAttrValue->save();
        }
        $object->make_id = $request->make;
        $object->model_id = $request->model;

        $object->save();

        $images = $request->file('images');
        $path = 'object/';
        $dir = config('app.fileDirectory') . $path;
        foreach ($images as $key => $image) {
            //dd(File::mimeType($image) != 'application/pdf');
            if ($image) {
                if (File::mimeType($image) != 'application/pdf') {
                    // echo 'if';die;
                    $img = Image::make($image);
                    // dd($img);
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $name = time() . uniqid() .'_vehicle'. '.' . $image->getClientOriginalExtension();
                    // $name = $timestamp . $key . '-' . $str . $image->getClientOriginalName();
                    // dd('else',$dir . $name,  $img->stream()->detach(), 'public');
                    Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    $data[$key] = $name;

                    $addImage = new ObjectImage();
                    $addImage->object_id = $objectId;
                    $addImage->image = $name;
                    $addImage->save();
                    /* $timestamp = Date('y-m-d-H-i-s');
                      $str = str_random(5);
                      $name = $timestamp . $key.'-'.$str. $image->getClientOriginalName();
                      $data[$key] = $name;
                      $image->move(public_path() . '/uploads/traders/images/', $name); */
                } else {
                 
                    $img = $image;
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $name = time() . uniqid() .'_vehicle'. '.' . $image->getClientOriginalExtension();
                    // dd('else',$dir . $name,  $img->stream()->detach(), 'public');
                    // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    Storage::disk('s3')->put($dir . $name, file_get_contents($img->getRealPath()), 'public');
                    $data[$key] = $name;

                }

               

                $object = Object::find($objectId);
                $object->images_uploaded = 1;
                $object->save();
            }
        }
        $imageids_arr = explode(",", $request->imageids_arr);
        if(count($imageids_arr) > 0){
            // Update sort position of images
            $position = 1;
            foreach($imageids_arr as $img_id){
                
                ObjectImage::where('id', $img_id)
                ->update([
                    'sort' => $position
                ]);
                $position ++;
            }
        }
        $docids_arr = explode(",", $request->docids_arr);
         if(count($docids_arr) > 0){
            // Update sort position of documents
            $pos = 1;
            foreach($docids_arr as $doc_id){

                ObjectAttachment::where('id', $doc_id)
                ->update([
                    'sort' => $pos
                    ]);
               $pos ++;
            }
         }

        $attachments = $request->file('attachment');
        $path = 'attachment/';
        $dir = config('app.fileDirectory') . $path;
        foreach ($attachments as $key => $attachments) {
            //dd(File::mimeType($attachments) != 'application/pdf');
            if ($attachments) {
                if (File::mimeType($attachments) != 'application/pdf') {
                    // dd('if');
                    $img = Image::make($attachments);
                    // dd($img);
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $aname = time() . uniqid() .'_document'. '.' . $attachments->getClientOriginalExtension();
                    // $name = $timestamp . $key . '-' . $str . $attachments->getClientOriginalName();

                    Storage::disk('s3')->put($dir . $aname, $img->stream()->detach(), 'public');
                    $data[$key] = $aname;

                    $addAttechment = new ObjectAttachment();
                    $addAttechment->object_id = $objectId;
                    $addAttechment->attachment = $aname;
                    $addAttechment->save();
                    /* $timestamp = Date('y-m-d-H-i-s');
                      $str = str_random(5);
                      $name = $timestamp . $key.'-'.$str. $attachments->getClientOriginalName();
                      $data[$key] = $name;
                      $attachments->move(public_path() . '/uploads/traders/images/', $name); */
                } else {
                    if (!empty($object->getOriginal($key))) {
                        Storage::disk('s3')->delete($dir . $object->getOriginal($key));
                    }
                   

                    $img = $attachments;
                    $timestamp = Date('y-m-d-H-i-s');
                    $str = str_random(5);
                    $aname = time() . uniqid() .'_document'. '_' . $attachments->getClientOriginalName();
                    // dd('else', $aname );
                    // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                    Storage::disk('s3')->put($dir . $aname, file_get_contents($img->getRealPath()), 'public');
                    $data[$key] = $aname;

                    $addAttechment = new ObjectAttachment();
                    $addAttechment->object_id = $objectId;
                    $addAttechment->attachment = $aname;
                    $addAttechment->save();
                }

            }
        }


        return redirect()->back()->with('success', 'Vehicle Edited Successfully');
    }
    public function remove_watch_images($type,$id)
    {
        if($type == 'images'){
            ObjectImage::where('id',$id)->delete();
            $message = "Vehicle image deleted Successfully";
        }else{
            //attachment
            ObjectAttachment::where('id',$id)->delete();
            $message = "Vehicle attachment deleted Successfully";
        }
        return redirect()->back()->with('success', $message);

    }

    public function auctionExport($dealerId){
        if (Gate::denies('vehicles-under-auction_export')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
      $dealerId = (int)$dealerId;
      $objects = Object::select(['objects.id','objects.name', 'objects.code','objects.created_at', 'objects.suggested_amount'])
        ->join('auctions','auctions.object_id','=','objects.id')->where('objects.images_uploaded',1)
        ->orderBy('objects.created_at', 'desc')->get();

      $fileName = 'vehicles_'.time();
      if($dealerId && $dealerId > 0){
        $fileName = $fileName.'-'.DealerUser::where('id',$dealerId)->first()->name;
        $objects = Object::select(['objects.id','objects.name', 'objects.code', 'objects.dealer_id'])
          ->join('auctions','auctions.object_id','=','objects.id')->where('objects.images_uploaded',1)
          ->where('objects.dealer_id',$dealerId)
          ->orderBy('objects.created_at', 'desc')->get();
      }
      $vehiclesArray = [];
      // $vehiclesArray[] = ['VehicleId','Vehicle Name','Vehicle Code','dealerId'];
      $vehiclesArray[] = ['VehicleId','Vehicle Name','Vehicle Code', 'Uploaded Date', 'Suggested Amount'];
      foreach ($objects as $object) {
          $vehiclesArray[] = $object->toArray();
      }
      Excel::create($fileName, function($excel) use ($vehiclesArray) {
          $excel->setTitle('Vehicles');
          $excel->setCreator('Admin')->setCompany('Wecashanycar');
          $excel->setDescription('Traders file');
          $excel->sheet('sheet1', function($sheet) use ($vehiclesArray) {
              $sheet->fromArray($vehiclesArray, null, 'A1', false, false);
          });

      })->download('csv');
    }

    public function noauctionExport($dealerId, $sourceId){
        // dd($sourceId);
        if (Gate::denies('vehicles_export')) {
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $dealerId = (int)$dealerId;
        $auctions = Auction::select('object_id')->get()->lists('object_id');
        // $objects = Object::select(['id','name', 'code','created_at', 'suggested_amount'])
        //     ->where('objects.images_uploaded',1)->whereNotIn('id',$auctions)
        //     ->orderBy('objects.created_at', 'desc')->get();

        $fileName = 'vehicles_'.time();
        $objects = Object::select(['id','name', 'code','created_at', 'suggested_amount'])
                            ->where('objects.images_uploaded',1)
                            ->whereNotIn('id',$auctions);
        if($dealerId && $dealerId > 0){
            $fileName = $fileName.'-'.DealerUser::where('id',$dealerId)->first()->name;
            $objects = $objects->where('objects.dealer_id',$dealerId);
        }
        if(!empty($sourceId)) {
            $inspector = \App\InspectorUser::where('source_id', $sourceId)->pluck('id')->toArray();
            $objects = $objects->whereIn('objects.inspector_id',$inspector);
        }

        $objects = $objects->orderBy('objects.created_at', 'desc')->get();

        $vehiclesArray = [];
        $vehiclesArray[] = ['VehicleId','Vehicle Name','Vehicle Code', 'Uploaded Date', 'Suggested Amount'];
        foreach ($objects as $object) {
            $vehiclesArray[] = $object->toArray();
        }
        Excel::create($fileName, function($excel) use ($vehiclesArray) {
            $excel->setTitle('Vehicles');
            $excel->setCreator('Admin')->setCompany('Wecashanycar');
            $excel->setDescription('Traders file');
            $excel->sheet('sheet1', function($sheet) use ($vehiclesArray) {
                $sheet->fromArray($vehiclesArray, null, 'A1', false, false);
            });

        })->download('csv');
        }

}
