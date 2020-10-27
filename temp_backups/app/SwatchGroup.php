<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SwatchGroup extends Model
{
    public function description(){
        return $this->belongsTo('App\Swatch','swatch_id','id');
    }
    public function swatches(){
        return $this->hasMany('App\SwatchGroup','parent_id','id')
            ->with('description')
            ->whereNotNull('parent_id')
            ->orderBy('order','ASC');
    }

}
