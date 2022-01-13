<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attribute;

class AttributeValue extends Model
{
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
}
