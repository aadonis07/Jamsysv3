<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Auth;
class Quotation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function agent(){
        return $this->belongsTo('App\Agent', 'user_id', 'user_id')->whereNull('date_end')->with('user');
    }
    public function sales_agent(){
        return $this->belongsTo('App\User', 'user_id', 'id')->with('employee');
    }
    public function client(){
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }
    public function products(){
        $user = Auth::user();
        if($user->department_id==19){
            return $this->hasMany('App\QuotationProduct', 'quotation_id', 'id')->with('product')->with('fitout_products')->whereNull('parent_id')->whereNull('cancelled_date')->whereIn('type',['SUPPLY','FIT-OUT'])->orderBy('order','ASC');
        }elseif($user->department_id==7){
            return $this->hasMany('App\QuotationProduct', 'quotation_id', 'id')->with('product')->with('fitout_products')->whereNull('parent_id')->whereNull('cancelled_date')->whereIn('type',['COMBINATION','CUSTOMIZED','FIT-OUT'])->orderBy('order','ASC');
        }else{
            return $this->hasMany('App\QuotationProduct', 'quotation_id', 'id')->with('product')->with('fitout_products')->whereNull('parent_id')->whereNull('cancelled_date')->orderBy('order','ASC');
        }
    }
    public function province(){
        return $this->belongsTo('App\Province', 'province_id', 'id');
    }
    public function city(){
        return $this->belongsTo('App\City', 'city_id', 'id')->with('region');
    }
    public function barangay(){
        return $this->belongsTo('App\Barangay', 'barangay_id', 'id');
    }
    public function terms(){
        return $this->belongsTo('App\QuotationTerm', 'quotation_term_id', 'id');
    }
    public function update_products(){
        return $this->hasMany('App\QuotationProduct', 'quotation_id', 'id')->with('product')->with('update_fitout_products')->whereNull('parent_id')->where('remarks','!=','DELETED');
    }
    public function temporary(){
        return $this->hasMany('App\QuotationProduct', 'quotation_id', 'id')->whereNull('parent_id')->whereIn('remarks',['DELETED','TEMPORARY']);
    }
    public function job_request(){
        return $this->belongsTo('App\JobRequest', 'id', 'quotation_id');
    }
    public function collection(){
        return $this->belongsTo('App\Collection', 'id', 'quotation_id')->with('invoice');
    }
	public function userWithEmployeeDetails(){
        return $this->belongsTo('App\User', 'user_id', 'id')->with('employee');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
