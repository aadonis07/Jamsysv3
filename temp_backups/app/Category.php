<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Category extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function subCategories(){
        return $this->hasMany('App\SubCategory','category_id','id');
    }
    public function attributes(){
        return $this->hasMany('App\Attribute','category_id','id')->orderBy('name');
    }
    public function subCategoryWithSwatches(){
        return $this->hasMany('App\SubCategory','category_id','id')
            ->with('swatchesGroup')
            ->orderBy('name');
    }
}
