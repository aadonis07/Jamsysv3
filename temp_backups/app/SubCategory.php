<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class SubCategory extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function category(){
        return $this->belongsTo('App\Category', 'category_id', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    public function swatchesGroup(){
        return $this->hasMany('App\SwatchGroup','sub_category_id','id')
            ->with('swatches')
            ->whereNull('parent_id');
    }
}
