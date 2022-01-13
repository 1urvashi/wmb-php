<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraderLogHistory extends Model
{
    use Uuids;

    protected $table = "trader_log_histories";

    protected $primaryKey = 'id';
    public $incrementing = false;
}
