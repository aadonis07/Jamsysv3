<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Auth;
class Client extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function industry() {
        return $this->belongsTo('App\Industry', 'industry_id', 'id');
    }

    public function province() {
        return $this->belongsTo('App\Province', 'province_id', 'id')->with('region');
    }

    public function companyBranches() {
        $user = Auth::user();
        return $this->hasMany('App\CompanyBranch', 'client_id', 'id')->where('user_id','=',$user->id);
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id')->with('employee');
    }
}
