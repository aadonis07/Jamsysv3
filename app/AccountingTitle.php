<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AccountingTitle extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function particulars(){
        return $this->hasMany('App\AccountTitleParticular', 'account_title_id', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
}
