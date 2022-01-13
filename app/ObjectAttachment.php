<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObjectAttachment extends Model
{
    protected  $table='object_attachments';

    public function getAttachmentAttribute($value)
    {
        // return $value ? cdn(config('app.fileDirectory') .'object/').$value : null;
        return env('S3_URL').'uploads/attachment/'.$value;
    }

   
}
