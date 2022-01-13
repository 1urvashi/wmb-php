<?php

namespace App;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Object extends Model
{

     // use SoftDeletes;
     protected $table = "objects";
    protected $dates = ['deleted_at'];

    public function values() {
        return $this->hasMany('App\ObjectAttributeValue');//->with('attribute');
    }

    public function images(){
        return $this->hasMany('App\ObjectImage')->orderBy('sort','asc');
    }

    public function attachments(){
        return $this->hasMany('App\ObjectAttachment')->orderBy('sort','asc');
    }

    public function ObjectAttributeValue(){
        return $this->hasMany('App\ObjectAttributeValue');
    }

    public function inspectorDetails() {
        return $this->hasOne('App\InspectorUser','id', 'inspector_id');
    }

    public function DealerDetails() {
        return $this->hasOne('App\DealerUser','id', 'dealer_id');
    }

    public function getObjectValue($attributeSet, $objectId) {
        $attributeId = Attribute::where('attribute_set_id',$attributeSet)->orderBy('sort','asc')->lists('id');
        return ObjectAttributeValue::whereIn('attribute_id',$attributeId)->where('object_id',$objectId)->with('attribute')->get();
    }

    public function make() {
        return $this->hasOne('App\Make','id', 'make_id');
    }

    public function model() {
        return $this->hasOne('App\Models','id', 'model_id');
    }

    public function bank() {
        return $this->hasOne('App\Bank','id', 'bank_id');
    }
    // function bankValues() {
    //     return $this->hasMany('App\Bank');
    // }
	/*
	public function bids(){
        return $this->hasMany('App\bid')->orderBy('sort','desc');
    }*/

}
