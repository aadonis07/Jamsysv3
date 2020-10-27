<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class City extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
    public function region(){
        return $this->belongsTo('App\Region','region_id','id');
    }

    public function province(){
        return $this->belongsTo('App\Province','province_id','id');
    }

    public function barangays() {
        return $this->hasMany('App\Barangay','city_id','id');
    }
}
