<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalVat extends Model
{
     protected $table = 'global_vat';

     public $fillable = ['vat', 'slug'];
}
