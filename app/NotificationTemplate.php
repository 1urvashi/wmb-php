<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use Uuids;
    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $table = 'notification_templates';
}
