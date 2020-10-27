<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class CompanyBranch extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function province() {
        return $this->belongsTo('App\Province', 'province_id', 'id')->with('region');
    }
}
