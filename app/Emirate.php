<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emirate extends Model
{
     protected $table = 'emirates';

     public $fillable = ['name'];
}
