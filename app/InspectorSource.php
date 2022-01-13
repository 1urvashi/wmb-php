<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class InspectorSource extends Model
{
    use SoftDeletes;
    use Uuids;
    
    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $table = 'inspector_sources';

    public $fillable = ['title', 'status'];
}
