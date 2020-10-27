<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function provinces() {
        return $this->hasMany('App\Province','region_id','id');
    }
}
