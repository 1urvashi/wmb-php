<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Models extends Model {

    protected $table = 'models';
    
    public $fillable = ['make_id','name'];
    
    public function make() {
        return $this->hasOne(Make, 'make_id');
    }

}
