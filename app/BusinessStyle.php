<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class BusinessStyle extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
}
