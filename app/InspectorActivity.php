<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InspectorActivity extends Model
{
    use Uuids;

    protected $table = "inspector_activities";

    protected $primaryKey = 'id';
    public $incrementing = false;
}
