<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraderSpecification extends Model {

    use Uuids;

    protected $table = 'trader_specifications';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $incrementing = false;

}
