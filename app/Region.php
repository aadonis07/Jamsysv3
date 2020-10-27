<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Region extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
    public function provinces() {
        return $this->hasMany('App\Province','region_id','id');
    }
}
