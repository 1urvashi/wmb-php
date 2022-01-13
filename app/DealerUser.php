<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DealerUser extends Authenticatable
{
     use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    //protected $fillable = ['name', 'email', 'password',];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getImageAttribute($value)
    {
         return $value ? cdn(config('app.fileDirectory') .'dealers/').$value : null;
        // return url('uploads/dealers/'.$value);
    }

    public function getLicenseImageAttribute($value)
   {
       return $value ? cdn(config('app.fileDirectory') .'dealers_license/').$value : null;
       //return url('uploads/traders/images/'.$value);
   }
}
