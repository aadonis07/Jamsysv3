<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Supplier extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function purchaseOrders(){
        return $this->hasMany('App\PurchaseOrder', 'supplier_id', 'id')
                    ->with('products');
    }
    public function inProgressPurchaseOrder(){
        return $this->hasOne('App\PurchaseOrder', 'supplier_id', 'id')
                    ->with('products');
    }
    public function supplyProducts(){ // for supply
    	return $this->hasMany('App\SupplierProduct', 'supplier_id', 'id')->with('variant');
    }
    public function rawProducts(){ // for raw products special-item included
    	return $this->hasMany('App\SupplierProduct', 'supplier_id', 'id')->with('product');
    }
    public function cityProvince(){
        return $this->belongsTo('App\City', 'city_id', 'id')->with('province');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function industry(){
        return $this->belongsTo('App\Industry', 'industry_id', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
	public function department(){
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
}
