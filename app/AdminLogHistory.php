<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminLogHistory extends Model
{
    use Uuids;

    protected $table = "admin_log_histories";

    protected $primaryKey = 'id';
    public $incrementing = false;
}
