<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model
{
    public function attributes() {
        return $this->belongsToMany('App\Attribute','attribute_set_attributes','attribute_set_id','attribute_id');    
    }
}
