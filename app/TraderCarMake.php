<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraderCarMake extends Model {

    use Uuids;

    protected $table = 'trader_car_makes';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $incrementing = false;

}
