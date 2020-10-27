<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class PurchaseOrder extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function products(){
        return $this->hasMany('App\PurchaseOrderDetail', 'purchase_order_id', 'id')
                        ->whereNull('parent_id')
                        ->with('details');
    }
    public function supplier(){
        return $this->belongsTo('App\Supplier', 'supplier_id', 'id')
                    ->with('industry')
                    ->with('cityProvince');
    }
    public function department(){
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
}
