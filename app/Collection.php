<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Collection extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function invoice(){
        return $this->belongsTo('App\CollectionPaper', 'collection_id', 'id')->where('accounting_paper_id','=',6)->where('archive','=',0);
    }
    public function quotation(){
        return $this->belongsTo('App\Quotation', 'quotation_id', 'id')->where('status','!=','PENDING')->whereNotNull('date_moved');
    }
    public function client(){
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }
    public function agent(){
        return $this->belongsTo('App\Agent', 'agent_id', 'id')->whereNull('date_end')->with('user');
    }
    public function collection_details(){
        return $this->hasMany('App\CollectionDetail', 'collection_id', 'id')->whereIn('status',['UNVERIFIED','VERIFIED']);
    }
    public function verifieds(){
        return $this->hasMany('App\CollectionDetail', 'collection_id', 'id')->where('status','=','VERIFIED');
    }
}
