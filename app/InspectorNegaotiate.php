<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InspectorNegaotiate extends Model
{
     protected $table = 'inspector_negaotiates';
     
     public $fillable = ['auction_id','inspector_id','override_amount'];
}
