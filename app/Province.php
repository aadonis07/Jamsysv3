<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Province extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
    public function cities() {
        return $this->hasMany('App\City','province_id','id');
    }
    public function region(){
        return $this->belongsTo('App\Region','region_id','id');
    }
}
