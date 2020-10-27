<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements Auditable
{
    use Notifiable;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','username','email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function username(){
        return 'username';
    }
    public function employee(){
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }
    public function department(){
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
    public function position(){
        return $this->belongsTo('App\Position', 'position_id', 'id');
    }
    public function agent(){
        return $this->belongsTo('App\Agent', 'id', 'user_id')->whereNull('date_end');
    }
}
