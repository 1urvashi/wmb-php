<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraderCarCondition extends Model {

    use Uuids;

    protected $table = 'trader_car_condition';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $incrementing = false;

}
