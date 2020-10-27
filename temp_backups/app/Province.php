<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public function cities() {
        return $this->hasMany('App\City','province_code','province_code');
    }
    public function region(){
        return $this->belongsTo('App\Region','region_id','id');
    }
}
