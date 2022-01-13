<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObjectImage extends Model
{
    public function getImageAttribute($value)
    {
        // return $value ? cdn(config('app.fileDirectory') .'object/').$value : null;
        return env('S3_URL').'uploads/object/'.$value;
    }
}
