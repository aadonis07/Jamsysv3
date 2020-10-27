<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class JobRequest extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
	
    public function client(){
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }
    public function quotation(){
        return $this->belongsTo('App\Quotation', 'quotation_id', 'id');
    }
    public function agent(){
        return $this->belongsTo('App\Agent', 'agent_id', 'id')->with('user')->whereNull('date_end');
    }
}
