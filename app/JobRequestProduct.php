<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class JobRequestProduct extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function jr_product(){
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }
    public function jr_quotation_product(){
        return $this->belongsTo('App\QuotationProduct', 'quotation_product_id', 'id');
    }
    public function jr_type(){
        return $this->belongsTo('App\JobRequestType', 'job_request_type_id', 'id');
    }
    public function jr_revisions(){
        return $this->hasMany('App\JobRequestProduct', 'parent_id', 'id')->whereNull('date_cancelled')->orWhereNotNull('designer_name');
    }
    public function revisions(){
        return $this->hasMany('App\JobRequestProduct', 'parent_id', 'id')->with('jr_type')->whereNull('date_cancelled');
    }
    public function jr_quote_product_with_quotation(){
        return $this->belongsTo('App\QuotationProduct', 'quotation_product_id', 'id')->with('quotation');
    }
}
