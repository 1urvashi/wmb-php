<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraderMarket extends Model {

    use Uuids;

    protected $table = 'trader_markets';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $incrementing = false;

}
