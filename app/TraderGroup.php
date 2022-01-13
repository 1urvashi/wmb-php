<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraderGroup extends Model
{
    //
    use Uuids;
    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $table = 'trader_groups';
}
