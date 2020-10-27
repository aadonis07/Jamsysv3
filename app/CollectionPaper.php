<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CollectionPaper extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function document(){
        return $this->belongsTo('App\AccountingPaper', 'accounting_paper_id', 'id');
    }
}
