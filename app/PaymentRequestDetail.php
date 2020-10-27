<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentRequestDetail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function client(){
        return $this->belongsTo('App\Client', 'client_id', 'id')->with('industry');
    }
    public function supplier(){
        return $this->belongsTo('App\Supplier', 'supplier_id', 'id')->with('department');
    }
    public function purchaseOrder(){
        return $this->belongsTo('App\PurchaseOrder', 'name', 'po_number');
    }
    public function quotation(){
        return $this->belongsTo('App\Quotation', 'name', 'quote_number');
    }
    public function request(){
        return $this->belongsTo('App\PaymentRequest', 'payment_request_id', 'id');
    }
}
