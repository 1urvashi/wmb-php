<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attribute;

class ObjectAttributeValue extends Model
{
    protected $fillable = [
        'object_id', 'attribute_id', 'attribute_value','quality_level','color','additional_text','invisible_to_trader'
    ];

    public function setColorAttribute($value)
    {
        $attribute = new Attribute();
        $this->attributes['color'] = $attribute->getColorValue($value);
    }
    public function getColorAttribute($value)
    {
        $attribute = new Attribute();
        return $attribute->getColor($value);
    }

    public function attribute(){
        $query =  $this->hasOne('App\Attribute','id','attribute_id');//->with('attributeSet');
        return $query;
    }
}
