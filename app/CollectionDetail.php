<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CollectionDetail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function bank(){
        return $this->belongsTo('App\Bank', 'bank_id', 'id');
    }
    public function collector(){
        return $this->belongsTo('App\User', 'collector_user_id', 'id')->with('employee');
    }
}
