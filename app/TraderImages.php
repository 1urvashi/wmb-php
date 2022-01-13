<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraderImages extends Model
{
  use Uuids;

  protected $table = 'trader_images';

  /**
   * The database primary key value.
   *
   * @var string
   */
  protected $primaryKey = 'id';
  public $incrementing = false;

 

  public function getImageAttribute($value)
  {
    // dd($this->imageType);
      return $value ?  env('S3_URL').'uploads/traders/'.$this->imageType.'/'.$value : null;
      //return env('S3_URL').'uploads/traders/'.$this->imageType.'/'.$value;
  }
  
}
