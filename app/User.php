<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    const VERIFIED_USER = "1";
    const UNVERIFIED_USER = "0";

    const ADMIN_USER = "true";
    const REGULAR_USER = "false";

    protected $dates = ["deleted_at"];
    protected $table ="users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', "verified", "verification_token", "admin",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', "verification_token"
//        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function setNameAttribute($name){
        $this->attributes["name"] = strtolower($name);
    }
    public function getNameAttribute($name){
        return ucwords($name);
    }
    public function setEmailAttribute($email){
        $this->attributes["email"] = strtolower($email);
    }

    public function isVerified(){
        return $this->verified == User::VERIFIED_USER;
    }

    public function isAdmin(){
        return $this->admin == User::ADMIN_USER;
    }

    public function generateVerificationCode(){
        return Str::random(40);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

}
