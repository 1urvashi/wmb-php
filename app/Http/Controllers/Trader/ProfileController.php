<?php

namespace App\Http\Controllers\Trader;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Storage;
use Auth;
use File;
use Image;
class ProfileController extends Controller
{
    public function upload(Request $request){
    	$messages = [
            'file.mimes' => trans('api.document_uploaded_valid_error')
        ];
    	$rules =  [ 
            'file' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:4096'
        ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            $errorMessage = $validator->messages();

            return [
	        	'status' => 'failed',
	        	'message' => ($errorMessage->first('file')) ?  $errorMessage->first('file') : trans('api.document_uploaded_error'),
	        ];
        }
        $response = $this->imageUpload($request->file,$request->type);
    	return response()->json($response);
    }

    public function imageUpload($image,$type ){
    	$traderId = Auth::guard('trader')->user()->id;
        if (!empty($image)) {
            switch ($type) {
                case 'emirates_id_front':
                    $path = 'traders/' . $type . '/';
                    break;
                case 'emirates_id_back':
                    $path = 'traders/' . $type . '/';
                    break;

                case 'passport_front':
                    $path = 'traders/' . $type . '/';
                    break;

                case 'passport_back':
                    $path = 'traders/' . $type . '/';
                    break;
                case 'other_doc':
                    $path = 'traders/' . $type . '/';
                    break;
                default:
               
                    break;
            }

          
            $dir = config('app.fileDirectory') . $path;
            $uploaded = false;
            $data = array();
            $docType = '';
            if (File::mimeType($image) != 'application/pdf') {
            	$docType = 'image';
                $img = Image::make($image);
                // dd($img);
                $timestamp = Date('y-m-d-H-i-s');
                $str = str_random(5);
                $name = $timestamp . $type . '-' . $str . $image->getClientOriginalName();
                $uploaded = Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');

            } else {
            	
            	$docType = 'pdf';
                $img = $image;
                $timestamp = Date('y-m-d-H-i-s');
                $str = str_random(5);
                $name = $timestamp . $type . '-' . $str . $image->getClientOriginalName();
                // Storage::disk('s3')->put($dir . $name, $img->stream()->detach(), 'public');
                $uploaded = Storage::disk('s3')->put($dir . $name, file_get_contents($img->getRealPath()), 'public');

            }

            if ($uploaded) {
            	if($type != 'other_doc'){
            		$removeDoc = \App\TraderImages::where('traderId', $traderId)->where('imageType',$type)->first();
	                if(!empty($removeDoc)){
	                    $this->deleteDoc($removeDoc->id,$dir);
	                }
            	}

                $traderImage = new \App\TraderImages();
                $traderImage->image = $name;
                $traderImage->imageType = $type;
                if(!empty($traderId)){
                    $traderImage->traderId = $traderId;
                }

                // if(!empty($sortOrder)){
                //     $traderImage->sort = $sortOrder;
                // }

                $traderImage->save();

                $data['type'] = $type;
                $data['docType'] = $docType;
                $data['docId'] = $traderImage->id;
                
                $data['docUrl'] = env('S3_URL').'uploads/'.$path.$name;
                
                return [
			        	'status' => 'success',
			        	'message' => trans('api.document_uploaded_success'),
			        	'data' => $data
			        ];

            }
        }

        return [
	        	'status' => 'failed',
	        	'message' => trans('api.document_uploaded_error'),
	        ];

    } 

    function deleteDoc($id){
    	$removeDoc = \App\TraderImages::where('id', $id)->first();
    	$type = $removeDoc->imageType;
    	switch ($type) {
            case 'emirates_id_front':
                $path = 'traders/' . $type . '/';
                break;
            case 'emirates_id_back':
                $path = 'traders/' . $type . '/';
                break;

            case 'passport_front':
                $path = 'traders/' . $type . '/';
                break;

            case 'passport_back':
                $path = 'traders/' . $type . '/';
                break;
            case 'other_doc':
                $path = 'traders/' . $type . '/';
                break;
            default:
           
                break;
        }

      
        $dir = config('app.fileDirectory') . $path;
        if (!empty($removeDoc)) {
            Storage::disk('s3')->delete($dir . $removeDoc->image);
            $removeDoc->delete();
        }
    }

    public function remove(Request $request){

    	$this->deleteDoc($request->id);

    	return [
	        	'status' => 'success',
	        	'message' =>  trans('frontend.delete_success'),
	        ];
    }
}
