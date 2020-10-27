<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentRequest extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function payee(){
        return $this->belongsTo('App\Payee', 'payee_id', 'id');
    }
    public function details(){
        return $this->hasMany('App\PaymentRequestDetail', 'payment_request_id', 'id')
                        ->with('client')
                        ->with('quotation')
                        ->with('purchaseOrder')
                        ->with('supplier');
    }
    public function partials(){
        return $this->hasMany('App\PaymentRequestPartial', 'payment_request_id', 'id');
    }
    public function liquidations(){
        return $this->hasMany('App\Liquidation', 'payment_request_id', 'id');
    }
    public function accountTitle(){
        return $this->belongsTo('App\AccountingTitle', 'account_title_id', 'id');
    }
    public function accountTitleParticular(){
        return $this->belongsTo('App\AccountTitleParticular', 'particular_id', 'id');
    }
    public function employee(){
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
}
