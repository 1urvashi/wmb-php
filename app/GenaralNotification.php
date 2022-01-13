<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenaralNotification extends Model
{
    use Uuids;
    protected $primaryKey = 'id';
    public $incrementing  = false;
    //genaral_notifications
    protected $table = 'genaral_notifications';
}
