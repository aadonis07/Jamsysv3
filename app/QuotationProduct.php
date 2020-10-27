<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
class QuotationProduct extends Model
{
    public function product(){
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }
    public function qproduct(){
        return $this->belongsTo('App\QuotationProduct', 'id', 'id');
    }
    public function fitout_products(){
        $user = Auth::user();
        if($user->department_id==19){
            return $this->hasMany('App\QuotationProduct', 'parent_id', 'id')->whereNull('cancelled_date')->whereIn('type',['SUPPLY','FIT-OUT']);
        }elseif($user->department_id==7){
            return $this->hasMany('App\QuotationProduct', 'parent_id', 'id')->whereNull('cancelled_date')->whereIn('type',['COMBINATION','CUSTOMIZED','FIT-OUT']);
        }else{
            return $this->hasMany('App\QuotationProduct', 'parent_id', 'id')->with('job_request_product')->whereNull('cancelled_date');
        }
    }
    public function fitout_jr_items(){
        return $this->hasMany('App\QuotationProduct', 'parent_id', 'id')->with('job_request_product')->whereNull('cancelled_date')->where('is_jr','=',1);
    }
    public function update_fitout_products(){
        return $this->hasMany('App\QuotationProduct', 'parent_id', 'id');
    }
    public function job_request_product(){
        return $this->belongsTo('App\JobRequestProduct', 'id', 'quotation_product_id')->with('revisions')->whereNull('parent_id');
    }
    public function purchase_order_product(){
        return $this->belongsTo('App\PurchaseOrderDetail', 'id', 'product_id')->whereNotNull('parent_id')->with('purchaseOrder');
    }
    public function quotation(){
        return $this->belongsTo('App\Quotation', 'quotation_id', 'id');
    }
}
