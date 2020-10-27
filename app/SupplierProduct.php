<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class SupplierProduct extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;

    public function variant(){ // for supply type
    	return $this->belongsTo('App\Product', 'product_id', 'id')
                ->with('parent')
                ->whereNotNull('parent_id');
    }
    public function product(){ // for raw type
        return $this->belongsTo('App\Product', 'product_id', 'id')
            ->whereNull('parent_id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
}
