<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentRequestPartial extends Model
{
    public function paymentRequest(){
        return $this->belongsTo('App\PaymentRequest', 'payment_request_id', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
}
