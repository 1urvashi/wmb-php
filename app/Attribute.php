<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = "attributes";
    protected $color = array(0=>'No Color',1=>'red', 2=>'yellow', 3=>'green');
    protected $inputType = array(0=>'text', 1=>'radio', 2=>'checkbox', 3=>'select', 4=>'multiselect', 5=>'date', 6=>'file', 7=>'multiplefiles',8=>'email', 9=>'number', 10=>'textarea',11 => 'year');
    function attributeValues() {
        return $this->hasMany('App\AttributeValue');
    }

    function qualityValues() {
        return $this->hasMany('App\AttributeValue')->where('quality_level','!=','');
    }

    function attributeSet(){
        return $this->hasOne('App\AttributeSet','id','attribute_set_id');
    }

   function getInputType($index){
       return $this->inputType[$index];
   }

   function getInputTypes(){
       return $this->inputType;
   }

   function getColor($index){
       return $this->color[$index];
   }

   function getColors(){
       return $this->color;
   }

   function getColorValue($value){
      return array_search($value, $this->color);
   }

   function getInputValue($value){
      return array_search($value, $this->inputType);
   }
    public function setInputTypeAttribute($value)
    {
        $this->attributes['input_type'] = $this->getInputValue($value);
    }
    public function getInputTypeAttribute($value)
    {
        return $this->getInputType($value);
    }
}
