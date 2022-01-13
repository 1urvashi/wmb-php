<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TraderUser extends Authenticatable
{
     use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    public function verifyUser()
    {
    return $this->hasOne('App\VerifyUser');
    }
    public function traderImages()
    {
    return $this->hasMany('App\TraderImages','traderId', 'id');
    }

   
	public function getTradeLicenseAttribute($value)
    {
        return $value ? cdn(config('app.fileDirectory') .'traders/images/').$value : null;
        //return url('uploads/traders/images/'.$value);
    }

    public function getKycAttribute($value)
   {
       return $value ? cdn(config('app.fileDirectory') .'traders/images/').$value : null;
       //return url('uploads/traders/images/'.$value);
   }
   public function getPaymentReceiptAttribute($value)
  {
      return $value ? cdn(config('app.fileDirectory') .'traders/images/').$value : null;
      //return url('uploads/traders/images/'.$value);
  }

	public function getPassportAttribute($value)
    {
        return $value ? cdn(config('app.fileDirectory') .'traders/images/').$value : null;
        //return url('uploads/traders/images/'.$value);
    }

	public function getDocumentAttribute($value)
    {
        return $value ? cdn(config('app.fileDirectory') .'traders/images/').$value : null;
        //return url('uploads/traders/images/'.$value);
    }

	public function getImageAttribute($value)
    {
        return $value ? cdn(config('app.fileDirectory') .'traders/images/').$value : null;
        //return $value ? url('uploads/traders/images/'.$value) : null;
    }

    //protected $fillable = array('first_name','last_name','email','password');

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
