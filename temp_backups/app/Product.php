<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Product extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'product_name',
        'category_id',
        'sub_category_id',
        'parent_id',
        'department_id',
        'swatches',
        'status',
        'supply',
        'is_default',
        'remarks',
        'created_by',
        'updated_by',
        'archive',
    ];
    public function category(){
        return $this->belongsTo('App\Category','category_id','id');
    }
    public function variants(){
        return $this->hasMany('App\Product','parent_id','id')->with('variantDescription');
    }
    public function subCategoryWithCategory(){
        return $this->belongsTo('App\SubCategory','sub_category_id','id')->with('category');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    public function variantDescription(){
        return $this->hasMany('App\ProductVariant','product_id','id');
    }
    
}
