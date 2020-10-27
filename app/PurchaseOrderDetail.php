<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class PurchaseOrderDetail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function details(){
        return $this->hasMany('App\PurchaseOrderDetail', 'parent_id', 'id')
            ->whereNotNull('parent_id');
    }
    public function supplierProduct(){
        // yung product id sa purchase order details, supplier_product_id data nun.
        return $this->belongsTo('App\SupplierProduct', 'product_id', 'id')->with('variant');
    }
    public function supplierRawProduct(){
        // yung product id sa purchase order details, supplier_product_id data nun.
        return $this->belongsTo('App\SupplierProduct', 'product_id', 'id')->with('product');
    }	
    public function purchaseOrder(){
        return $this->belongsTo('App\PurchaseOrder', 'purchase_order_id', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
}
