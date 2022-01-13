<?php

namespace App\Http\Controllers\Dealer;

use Illuminate\Http\Request;

use Validator;
use DB;
use App\DealerUser;
use App\Object;
use App\ObjectImage;
use App\ObjectAttributeValue;
use App\ObjectAttachment;
use App\Auction;
use Carbon\Carbon;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mail;
use Image;
use GuzzleHttp;
use App\Attribute;
use App\AttributeSet;
use Datatables;
use App\Make;
use App\Models;
use PDF;
use App\Bank;
use Storage;
use File;

class ObjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Object $object)
    {
        $dealers = DealerUser::all();
        return view('dealer.modules.object.index', compact('dealers'));
    }
    public function noIndex(Object $object)
    {
        $dealers = DealerUser::all();
        return view('dealer.modules.object.no-index', compact('dealers'));
    }
    /**
     * Displays model data in ajax.
     *
     * @return Datatables
     */
    public function data(Request $request)
    {
        $user = Auth::guard('dealer')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $objects = Object::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'objects.id','objects.name', 'objects.code', 'objects.suggested_amount', 'objects.created_at'])->join('auctions', 'auctions.object_id', '=', 'objects.id');
        if ($user->branch_id == 0) {
            $objects = $objects->where('objects.dealer_id', $user->id);
        } else {
            $objects = $objects->where('objects.dealer_id', $user->branch_id);
        }
        $objects = $objects->where('objects.images_uploaded', 1)->orderBy('objects.created_at', 'desc')->get();


        return Datatables::of($objects)
                ->addColumn('action', function ($objects) {
                     // <a href="../objects/destroy/' . $objects->id . '" disabled onclick="return confirm(\'Are you sure you want to delete this Object?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>
                    return '<a href="../object/detail/'.$objects->id.'" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> View Details</a>
                    <a href="" disabled class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Submit for Auction</a>
                    <a href="../objects/duplicate/' . $objects->id . '" class="btn btn-xs btn-primary duplicate-button"><i class="fa fa-pencil-square-o"></i>Edit Watch</a>';
                })
                ->editColumn('created_at', function($objects) {
                     return date('Y-m-d h:i:s A', strtotime($this->UaeDate($objects->created_at)));
                })
                    ->filter(function ($instance) use ($request) {
                        if ($request->has('objectName') && ($request->get('objectName')!='')) {
                            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                return (stripos(strtolower($row['name']), strtolower($request->get('objectName')))  > -1) ? true : false;
                            });
                        }
                    })
                ->make(true);
    }

    public function noData(Request $request)
    {
        $user = Auth::guard('dealer')->user();
        DB::statement(DB::raw('set @rownum=0'));
        $auctions = Auction::select('object_id')->get()->lists('object_id');

        
        $objects = Object::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','name', 'code', 'suggested_amount', 'created_at'])->where('objects.images_uploaded', 1)->whereNotIn('id', $auctions);
        if($user->branch_id == 0) {
             $objects = $objects->where('dealer_id', $user->id);
        } else {
             $objects = $objects->where('dealer_id', $user->branch_id);
        }
        $objects = $objects->orderBy('created_at', 'desc')->get();

        return Datatables::of($objects)
                ->addColumn('action', function ($objects) {
                    $txt = '<a href="../object/detail/'.$objects->id.'" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> View Details</a>';
                    $txt .= '<a href="../objects/destroy/' . $objects->id . '" onclick="return confirm(\'Are you sure you want to delete this Object?\');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Delete</a>'
                    .'<a href="../auctions/create?id=' . $objects->id . '" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Submit for Auction</a>'
                   .'<a href="../object/edit/'.$objects->id.'" class="btn btn-xs btn-success"><i class="fa fa-pencil-square-o"></i>Edit Watch</a>';
                    return $txt;
                })
                ->editColumn('created_at', function($objects) {
                     return date('Y-m-d h:i:s A', strtotime($this->UaeDate($objects->created_at)));
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->has('objectName') && ($request->get('objectName')!='')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return (stripos(strtolower($row['name']), strtolower($request->get('objectName')))  > -1) ? true : false;
                        });
                    }
                })
            ->make(true);
    }



    private function CloneObject($objectId)
    {
        $object = Object::find($objectId);

       $user = Auth::guard('dealer')->user();
   		 if($object->dealer_id != $user->id){
   			 return redirect()->back()->withError('You dont have previlage to disable current auction');
   		 }

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

        $newObject->vin = $object->vin;
        $newObject->vehicle_registration_number = $object->vehicle_registration_number;
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
            if (!empty($value->getOriginal('image'))) {
                $newObjectImage = new ObjectImage();
                $newObjectImage->object_id = $newObject->id;
                $newObjectImage->image = $value->getOriginal('image');
                $newObjectImage->sort = $value->sort;
                $newObjectImage->save();
            }
        }

        return $newObject->id;
    }

    public function duplicateObject(Request $request)
    {
        $user = Auth::guard('dealer')->user();
        $object = Object::find($request->id);
        if($user->branch_id == 0) {
             if ($object->dealer_id !== $user->id) {
                 return redirect('dealer')->with('error', 'Not authorized to this page');
             }
        } else {
             if ($object->dealer_id !== $user->branch_id) {
                 return redirect('dealer')->with('error', 'Not authorized to this page');
             }
        }
        $this->CloneObject($request->id);

        return redirect('dealer/objects/noauction')->with('success', 'Object duplicated Successfully');
    }

    public function reopenAuction(Request $request)
    {
        $auction = Auction::find($request->id);
        if (empty($auction->object_id)) {
            return redirect()->back()->with('success', 'Unable to reopen');
        }

        $objectId = $auction->object_id;
        $newObjectId = $this->CloneObject($objectId);

        return redirect('dealer/auctions/create?id='.$newObjectId); //->with('success', 'Object duplicated Successfully');
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

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
        return view('dealer.modules.object.create',compact('bank', 'makes','sourceOfEnquiry', 'attributeSet', 'data','model','dealers'));
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
         // $validator = Validator::make($request->all(), array('name' => 'required','code'=>'required', 'suggested_amount' => 'required'));
         // if ($validator->fails()) {
         //     return response()->json(["StatusCode" => 20000,"Status" => $validator->errors()->all()]);
         // }
         $user = Auth::guard('dealer')->user();
         $exist = Object::find($request->object_id);
         $object = $request->has('object_id') ? $exist : new Object();

         $object->name = $request->name;
         $object->code =  unique_random('objects', 'code', 60);
         // $object->inspector_id = 16;
         $object->dealer_id = $user->id;
         // $object->variation = $request->variation;
         $object->model_id = $request->model;
         $object->make_id = $request->make;
         $object->save();



         $notification = new \App\AdminNotification();
         $notification->messages = "test created new watch";
         $notification->inspector_id = 16;
         $notification->source = 1;
         $notification->save();

         if ($request->has('attributeValue') && count($request->get('attributeValue'))) {
             // if ($request->has('object_id')) {
             //     ObjectAttributeValue::where('object_id', $request->object_id)->delete();
             // }
             foreach ($request->get('attributeValue') as $key=>$attributes) {

                 $objectAttribute = new \App\ObjectAttributeValue();                $objectAttribute->object_id = $object->id;
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
 // dd($images);
         // delete images if exist
          $imageExist = \App\ObjectImage::where('object_id', $objectId)->first();
          if (!empty($imageExist)) {
              \App\ObjectImage::where('object_id', $objectId)->delete();
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

                     $addImage = new \App\ObjectImage();
                     $addImage->object_id = $objectId;
                     $addImage->image = $name;
                     $addImage->save();
                     /* $timestamp = Date('y-m-d-H-i-s');
                       $str = str_random(5);
                       $name = $timestamp . $key.'-'.$str. $image->getClientOriginalName();
                       $data[$key] = $name;
                       $image->move(public_path() . '/uploads/traders/images/', $name); */
                 } else {
                  //   dd('else' );
                     $img = $image;
                     $timestamp = Date('y-m-d-H-i-s');
                     $str = str_random(5);
                     $name = time() . uniqid() .'_vehicle'. '.' . $image->getClientOriginalExtension();
                     // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                     Storage::disk('s3')->put($dir . $name, file_get_contents($img->getRealPath()), 'public');
                     $data[$key] = $name;
                 }
                 $object = \App\Object::find($objectId);
                 $object->images_uploaded = 1;
                 $object->save();
             }
         }

          // delete Attachment if exist
          $attachmentExist = \App\ObjectAttachment::where('object_id', $objectId)->first();
          if (!empty($attachmentExist)) {
             \App\ObjectAttachment::where('object_id', $objectId)->delete();
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

                     $addAttechment = new \App\ObjectAttachment();
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

         return redirect('dealer/objects/noauction')->with('success', 'Successfully added new watch');


     }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Object $object)
    {
        return view('dealer.modules.object.show', compact('object'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Object $object)
    {
        return view('dealer.modules.object.create', compact('object'));
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
        $user = Auth::guard('dealer')->user();
        $object = Object::findOrFail($id);
        $object = Object::find($id);
        if($user->branch_id == 0) {
             if ($object->dealer_id != $user->id) {
                 return redirect('dealer')->with('error', 'Not authorized to this page');
             }
        } else {
             if ($object->dealer_id != $user->branch_id) {
                 return redirect('dealer')->with('error', 'Not authorized to this page');
             }
        }
        $object->delete();
        return redirect()->back()->with('success', 'Object Deleted Successfully');
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

    public function objectDetails(Request $request, $id)
    {
        $data=array();
        $object = Object::find($id);
        $user = Auth::guard('dealer')->user();
        if($user->branch_id == 0) {
             if ($object->dealer_id != $user->id) {
                 return redirect('dealer')->with('error', 'Not authorized to this page');
             }
        } else {
             if ($object->dealer_id != $user->branch_id) {
                return redirect('dealer')->with('error', 'Not authorized to this page');
            }
        }


        $make = Make::where('id', $object->make_id)->first()->name;
        $model = Models::where('id', $object->model_id)->first()->name;

        foreach ($object->ObjectAttributeValue as  $value) {
            //  dd($value->attribute->attributeSet);
            $data[$value->attribute->attributeSet->slug][]=$value;
        }
        //dd($data);
        //dd($object->ObjectAttributeValue[1]->attribute);
        $attributeSet = AttributeSet::orderBy('sort', 'asc')->get();
        if ($request->has('type') && ($request->type == 'detail')) {
            $bidAmount = $auction->bid->first()->bidding_price;
            return view('dealer.trader.detail', compact('object','auction', 'attributeSet', 'bidAmount', 'make', 'model'));
        }
        //dd($attributeSet);
        return view('dealer.modules.trader.auction-detail', compact('object', 'attributeSet', 'data', 'make', 'model'));
    }

    public function download($id)
    {
        $fileName = 'vehicles_'.time();
        $data=array();

        $object = Object::leftjoin('models', 'models.id', '=', 'objects.model_id')
                                  ->leftjoin('makes', 'makes.id', '=', 'objects.make_id')
                                  ->leftjoin('inspector_users', 'inspector_users.id', '=', 'objects.inspector_id')
                                  ->select('objects.name', 'objects.variation', 'objects.vin', 'objects.vehicle_registration_number', 'objects.customer_name', 'objects.customer_mobile', 'objects.customer_mobile',
                                   'objects.customer_email', 'objects.customer_reference', 'objects.source_of_enquiry', 'objects.created_at', 'objects.inspector_id', 'objects.dealer_id', 'objects.bank_id')
                                  ->where('objects.id', $id)->first();

        $attributeSet = AttributeSet::orderBy('sort', 'asc')->get();

        $obj = Object::find($id);
        $make = Make::where('id', $obj->make_id)->first()->name;
        $model = Models::where('id', $obj->model_id)->first()->name;

        if (!empty($obj->ObjectAttributeValue)) {
            foreach ($obj->ObjectAttributeValue as  $value) {
                if (!empty($value->attribute->exportable)) {
                    $data[$value->attribute->attributeSet->slug][]=$value;
                }
            }
        }

        // return view('admin.modules.object.list_pdf', compact('object','attributesId','objectAttributeValue','attributeSet','data', 'make', 'model'));
        $pdf = PDF::loadView('admin.modules.object.list_pdf', compact('object', 'attributesId', 'objectAttributeValue', 'attributeSet', 'data', 'make', 'model'));

        $pdf->setPaper('a2', 'landscape');

        // return view('admin.modules.inspector.vehicles.list_pdf', compact('headMerg', 'objects','attributesId'));

        return $pdf->download($fileName.'.pdf');
    }

    public function objectEdit(Request $request, $objectId)
    {
        $user = Auth::guard('dealer')->user();

        $data=array();
        $object = Object::findOrFail($objectId);
        if($object->dealer_id != $user->id){
          return redirect()->back()->withError('You dont have previlage to disable current auction');
        }

        foreach ($object->ObjectAttributeValue as  $value) {
            $data[$value->attribute->attributeSet->slug][]=$value;
        }
        $attributeSet = AttributeSet::orderBy('sort', 'asc')->get();
        $makes = Make::orderBy('name','asc')->get();
        $models = Models::where('make_id', $object->make_id)->get();
        return view('dealer.modules.object.edit', compact('object', 'attributeSet', 'data', 'makes', 'models'));
    }

    public function getModels($id) {
        $models = Models::where('make_id', $id)->get();

        $html = '<option value="">Choose Model</option>';
        for ($i=0; $i < count($models) ; $i++) {
            $html .= "<option value='".$models[$i]->id."'>".$models[$i]->name."</option>";
        }
        return $html;
    }

    public function updateObject(Request $request, $objectId)
    {
        $data=array();
        $attribute = new \App\Attribute();
        //return $request->all();
        $object = Object::findOrFail($objectId);

        $user = Auth::guard('dealer')->user();
        if($object->dealer_id != $user->id){
          return redirect()->back()->withError('You dont have previlage to disable current auction');
        }
        foreach ($request->attributeValue as $key => $value) {
            $objAttrValue = ObjectAttributeValue::where('object_id', $objectId)->where('attribute_id', $key)->first();
            $values = explode('#', $value);
            $objAttrValue->attribute_value = $values[0];
            if (isset($values[1])) {
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
}
