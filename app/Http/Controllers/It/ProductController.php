<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;
use Auth;
use Session;
use Validator;
use App\Product;
use App\ProductVariant;
use App\Category;
use App\SwatchGroup;
use DB;
use Cart;
class ProductController extends Controller
{
    function showAddproductFitOut(){
        $user = Auth::user();
        $selectCategoryQuery = Category::with('subCategories')
            ->where('status','=','ACTIVE')
            ->orderBy('name')
            ->get();
        return view('it-department.products.add-product-fitout')
            ->with('admin_menu','PRODUCTS')
            ->with('admin_sub_menu','CREATE')
            ->with('categories',$selectCategoryQuery)
            ->with('user',$user);
    }
    function showSwatchDetails(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';
        if(isset($data['swatch']) && !empty($data['swatch'])){
            $swatch_group_name = encryptor('decrypt',$data['swatch']);
            $selectQuery = SwatchGroup::with('swatches')->where('name','=',$swatch_group_name)->first();
            $destination  = 'assets/img/swatches/';
            foreach($selectQuery->swatches as $index=>$swatch){
                $swatch_id = encryptor('encrypt',$swatch->swatch_id);
                $basePath = '//via.placeholder.com/300x300';
                $filename = $swatch_id;
                $path = imagePath($destination.''.$filename,$basePath);
                $resultHtml = $resultHtml.'
                        <div class="col-md-3 p-2">
                            <input class="form-control form-control-sm" readonly value="'.$swatch->swatch.'"/>
                            <img src="'.$path.'" alt="'.$swatch->swatch.'"  title="'.$swatch->swatch.'" class="img-fluid mt-0"/>
                        </div>
                    ';
            }
        }
        else{
            $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find swatch. Please try again
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showUpdateproduct(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';
        if(isset($data['pid']) && !empty($data['pid'])){
            $product_id = encryptor('decrypt',$data['pid']);
            $selectQuery = Product::with('variants')
                ->where('id','=',$product_id)
                ->first();
            if($selectQuery){
                $selectCategoryQuery = Category::with('subCategoryWithSwatches')
                    ->where('status','=','ACTIVE')
                    ->orderBy('name')
                    ->get();
                return view('it-department.products.page-load.product-details')
                    ->with('categories',$selectCategoryQuery)
                    ->with('product',$selectQuery);
            }else{
                $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find product details. Please try again.
                    </div>
                </div>';
            }
        }else{
            $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find product details. Please try again.
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showVariants(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['pid']) && !empty($data['pid'])){
            $product_id = encryptor('decrypt',$data['pid']);
            $selectQuery = Product::with('variants')->with('subCategoryWithCategory')->find($product_id);
            return view('it-department.products.variants')
                ->with('admin_menu','PRODUCTS')
                ->with('admin_sub_menu','LIST')
                ->with('product',$selectQuery)
                ->with('user',$user);
        }
        Session::flash('success',0);
        Session::flash('message','Unable to find product. Please try again');
        return back();
    }
    function showIndex(){
        $user = Auth::user();
        $selectCategoryQuery = Category::with('subCategories')->with('attributes')
            ->where('status','=','ACTIVE')
            ->orderBy('name')
            ->get();
        return view('it-department.products.index')
            ->with('admin_menu','PRODUCTS')
            ->with('admin_sub_menu','LIST')
            ->with('categories',$selectCategoryQuery)
            ->with('user',$user);
    }
    function showAddproduct(){
        $user = Auth::user();
        $selectCategoryQuery = Category::with('subCategoryWithSwatches')->with('attributes')
            ->where('status','=','ACTIVE')
            ->orderBy('name')
            ->get();
        return view('it-department.products.add-product')
            ->with('categories',$selectCategoryQuery)
            ->with('admin_menu','PRODUCTS')
            ->with('admin_sub_menu','CREATE')
            ->with('user',$user);
    }
    function showAddproductRaw(){
        $user = Auth::user();
        $selectCategoryQuery = Category::with('subCategoryWithSwatches')->with('attributes')
            ->where('status','=','ACTIVE')
            ->orderBy('name')
            ->get();
        return view('it-department.products.add-product-raw')
            ->with('categories',$selectCategoryQuery)
            ->with('admin_menu','PRODUCTS')
            ->with('admin_sub_menu','CREATE')
            ->with('user',$user);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'remove-to-fit-out-list'){
                Cart::remove($data['key']);
                $cart = Cart::getContent();
                $subTotal = Cart::getSubTotal();
                return array(
                    'success' => 1,
                    'message' =>'Product Removed.',
                    'cart_total' => $cart->count(),
                    'sub_total' => $subTotal,
                );
            }
            elseif($postMode == 'add-to-fit-out-list'){
                $attributes = [
                    'key' => 'Product',
                    'type' => 'Type',
                ];
                $rules = [
                    'key' => 'required',
                    'type' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $types = array(
                      'SPECIAL-ITEM',
                      'SUPPLY',
                      'RAW'
                    );
                    $type = $data['type'];
                    if(!in_array($type,$types)){ //not in array
                        return array('success' => 0, 'message' => 'Undefined type. Please try again');
                    }else{
                        $enc_product_id = $data['key'];
                        $product_id = encryptor('decrypt',$data['key']);
                        $resultHtml = '';
                        $selectQuery = array();
                        if(Cart::get($data['key'])) {
                            return array('success' => 0, 'message' => 'Already Added');
                        }else{
                            if($type == 'RAW' || $type == 'SPECIAL-ITEM'){
                                $selectQuery = Product::with('subCategoryWithCategory')
                                    ->whereNull('parent_id')
                                    ->whereIn('type',['RAW','SPECIAL-ITEM'])
                                    ->where('id','=',$product_id)
                                    ->first();
                                if($selectQuery){
                                    //cart setup
                                    $product = array(
                                        'id' => $enc_product_id, // standard key
                                        'name' => $selectQuery->product_name,
                                        'price' => $selectQuery->base_price, // standard key
                                        'quantity' => 1, // standard key
                                        'attributes' => array(
                                            'type' => $selectQuery->type,
                                            'product_name' => $selectQuery->product_name,
                                            'variant_name' => '', // ignored if RAW || SPECIAL-ITEM
                                            'base_price' => $selectQuery->base_price,
                                        )
                                    );
                                    $resultHtml = '
                                        <tr id="row-'.$enc_product_id.'">
                                            <td>
                                                <input type="hidden" name="types[]" value="'.$selectQuery->type.'"/>
                                                <input type="hidden" name="keys[]" value="'.$enc_product_id.'"/>
                                                <input type="hidden" name="names[]" value="'.$selectQuery->product_name.'"/>
                                                '.$selectQuery->product_name.'
                                                <hr class="m-0">
                                                <text class="text-primary small"><b>TYPE: '.$selectQuery->type.'</b></text>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text text-dark">
                                                            &#8369;
                                                        </div>
                                                    </div>
                                                    <input  name="prices[]" required value="'.$selectQuery->base_price.'" type="number" step=".01" class="form-control form-control-sm"/>
                                                </div>
                                            </td>
                                            <td>
                                                <button id="removebtn-'.$enc_product_id.'" onClick=removeinRow("'.$enc_product_id.'",this.id) class="btn btn-xs btn-danger">Remove</button>
                                            </td>
                                        </tr>
                                    ';
                                }
                            }
                            elseif($type == 'SUPPLY'){
                                $selectQuery = Product::with('parentWithCategory')
                                    ->whereNotNull('parent_id')
                                    ->whereIn('type',['SUPPLY'])
                                    ->where('id','=',$product_id)
                                    ->first();
                                if($selectQuery){
                                    //cart setup
                                    $product = array(
                                        'id' => $enc_product_id, // standard key
                                        'name' => $selectQuery->parentWithCategory->product_name.' v: '.$selectQuery->product_name, // name v: variant
                                        'price' => $selectQuery->base_price, // standard key
                                        'quantity' => 1, // standard key
                                        'attributes' => array(
                                            'type' => $selectQuery->type,
                                            'product_name' => $selectQuery->parentWithCategory->product_name,
                                            'variant_name' => $selectQuery->product_name,
                                            'base_price' => $selectQuery->base_price,
                                        )
                                    );
                                    $resultHtml = '
                                        <tr id="row-'.$enc_product_id.'">
                                            <td>
                                                <input type="hidden" name="types[]" value="'.$selectQuery->type.'"/>
                                                <input type="hidden" name="keys[]" value="'.$enc_product_id.'"/>
                                                <input type="hidden" name="names[]" value="'.$selectQuery->parent->product_name.' v: '.$selectQuery->product_name.'"/>
                                                '.$selectQuery->parent->product_name.' v: '.$selectQuery->product_name.'
                                                <hr class="m-0">
                                                <text class="text-primary small"><b>TYPE: '.$selectQuery->parent->type.'</b></text>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text text-dark">
                                                            &#8369;
                                                        </div>
                                                    </div>
                                                    <input name="prices[]" required value="'.$selectQuery->base_price.'" type="number" step=".01" class="form-control form-control-sm"/>
                                                </div>
                                            </td>
                                            <td>
                                                <button id="removebtn-'.$enc_product_id.'" onClick=removeinRow("'.$enc_product_id.'",this.id) class="btn btn-xs btn-danger">Remove</button>
                                            </td>
                                        </tr>
                                    ';
                                }
                            }
                            if($selectQuery){
                                Cart::add($product);
                                $cart = Cart::getContent();
                                $subTotal = Cart::getSubTotal();
                                return array('success' => 1,
                                    'message' => 'Product Identified',
                                    'data' => $resultHtml,
                                    'cart_total' => $cart->count(),
                                    'sub_total' =>  $subTotal,
                                );
                            }else{
                                return array('success' => 0, 'message' => 'Unable to find product. Please try again');
                            }
                        }
                    }
                }
            }
            elseif($postMode == 'raw-product-list') {
                $selectQuery = Product::with('subCategoryWithCategory')
                    ->with('subCategoryWithCategory.category')
                    ->whereNull('parent_id')
                    ->whereIn('type',['RAW','SPECIAL-ITEM'])
                    ->where('status','=','APPROVED')
                    ->where('archive','=',false)
                    ->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('actions', function($selectQuery) use($data) {
                        $returnValue = '';
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        if(Cart::get($enc_product_id)) {
                            $returnValue = '<text class="text-success">Already Added</text>';
                        }else{
                            if(isset($data['parent'])){
                                $parent_id = encryptor('decrypt',$data['parent']);
                                $raw_name = $selectQuery->product_name;
                                $checkifExist = Product::where('parent_id','=',$parent_id)
                                                        ->where('type','=','FIT-OUT')
                                                        ->where('product_name','=',$raw_name)
                                                        ->first();
                                if($checkifExist){
                                    $returnValue = '<text class="text-success">Already Added</text>';
                                }else{
                                    $returnValue .= '
                                        <input type="hidden" id="raw-product-name-'.$enc_product_id.'" value="'.$selectQuery->product_name.'"/>
                                        <input type="hidden" id="product-type-'.$enc_product_id.'" value="'.$selectQuery->type.'"/>
                                        <input type="hidden" id="raw-product-base-price-'.$enc_product_id.'" value="'.$selectQuery->base_price.'"/>
                                    ';
                                    $returnValue .= '<button onClick=addRawToFitOutProduct("'.$enc_product_id.'") class="btn btn-info btn-xs small" >Add to list</a>&nbsp;';
                                }
                            }else{
                                $returnValue .= '
                                        <input type="hidden" id="raw-product-name-'.$enc_product_id.'" value="'.$selectQuery->product_name.'"/>
                                        <input type="hidden" id="product-type-'.$enc_product_id.'" value="'.$selectQuery->type.'"/>
                                        <input type="hidden" id="raw-product-base-price-'.$enc_product_id.'" value="'.$selectQuery->base_price.'"/>
                                    ';
                                $returnValue .= '<button onClick=addRawToFitOutProduct("'.$enc_product_id.'") class="btn btn-info btn-xs small" >Add to list</a>&nbsp;';
                            }
                        }
                        return $returnValue;
                    })
                    ->editColumn('product_name', function($selectQuery) {
                        $returnValue = '';
                        $returnValue .= $selectQuery->product_name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">TYPE: '.$selectQuery->type.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('sub_category_with_category.name', function($selectQuery) {
                        $returnValue = '';
                        $returnValue .= $selectQuery->subCategoryWithCategory->name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">CATEGORY: '.$selectQuery->subCategoryWithCategory->category->name.'</text>';
                        return $returnValue;
                    })
                    ->setRowId(function ($selectQuery) {
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        return 'raw-product-row-'.$enc_product_id;
                    })
                    ->setRowClass(function ($user) {
                        return 'raw-products';
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'supply-product-list'){
                $selectQuery = Product::with('subCategoryWithCategory')
                    ->with('subCategoryWithCategory.category')
                    ->whereNull('parent_id')
                    ->where('type','=','SUPPLY')
                    ->where('status','=','APPROVED')
                    ->where('archive','=',false)
                    ->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('actions', function($selectQuery) {
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<input type="hidden" id="product-name-'.$enc_product_id.'" value="'.$selectQuery->product_name.'">';
                        $returnValue .= '<button onClick=supplyProductVariants("'.$enc_product_id.'") class="btn btn-info btn-sm btn-icon" title="Variants"><span class="fas fa-tags"></span></a>&nbsp;';
                        return $returnValue;
                    })
                    ->editColumn('product_name', function($selectQuery) {
                        $returnValue = '';
                        $returnValue .= $selectQuery->product_name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">TYPE: '.$selectQuery->type.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('sub_category_with_category.name', function($selectQuery) {
                        $returnValue = '';
                        $returnValue .= $selectQuery->subCategoryWithCategory->name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">CATEGORY: '.$selectQuery->subCategoryWithCategory->category->name.'</text>';
                        return $returnValue;
                    })
                    ->setRowId(function ($selectQuery) {
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        return 'product-row-'.$enc_product_id;
                    })
                    ->setRowClass(function ($user) {
                        return 'products';
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'supply-product-variant-list'){
                $product_id =  encryptor('decrypt',$data['product']);
                $selectQuery = Product::with('supplierProduct')->with('parent')
                    ->whereNotNull('parent_id')
                    ->where('parent_id','=',$product_id)
                    ->where('type','=','SUPPLY')
                    ->where('status','=','APPROVED')
                    ->where('archive','=',false)
                    ->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery) use ($data){
                        $returnValue = '';
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        if(Cart::get($enc_product_id)) {
                            $returnValue = '<text class="text-success">Already Added</text>';
                        }else{
                            if(isset($data['parent'])){
                                $parent_id = encryptor('decrypt',$data['parent']);
                                $variant_name = $selectQuery->parent->product_name.' v: '.$selectQuery->product_name;
                                $checkifExist = Product::where('product_name','=',$variant_name)
                                                        ->where('type','=','FIT-OUT')
                                                        ->where('parent_id','=',$parent_id)
                                                        ->first();
                                if($checkifExist){
                                    $returnValue = '<text class="text-success">Already Added</text>';
                                }else{
                                    $returnValue = '<button id="addtolist-'.$enc_product_id.'-btn" onClick=addSupplyToFitOutProduct("'.$enc_product_id.'") class="btn btn-info btn-xs " title="Add to Fit-Out">Add to list</a>&nbsp;';
                                }
                            }else{
                                $returnValue = '<button id="addtolist-'.$enc_product_id.'-btn" onClick=addSupplyToFitOutProduct("'.$enc_product_id.'") class="btn btn-info btn-xs " title="Add to Fit-Out">Add to list</a>&nbsp;';
                            }
                        }
                        return $returnValue;
                    })
                    ->editColumn('product_name', function($selectQuery){
                        $enc_product_id = encryptor('encrypt',$selectQuery->id); //variant
                        $returnValue = '
                                <input type="hidden" id="variant-name-'.$enc_product_id.'" value="'.$selectQuery->product_name.'"/>
                                <input type="hidden" id="product-name-'.$enc_product_id.'" value="'.$selectQuery->parent->product_name.'"/>
                                <input type="hidden" id="product-type-'.$enc_product_id.'" value="'.$selectQuery->parent->type.'"/>
                                <input type="hidden" id="base-price-'.$enc_product_id.'" value="'.$selectQuery->base_price.'"/>
                            ';
                        $returnValue .= $selectQuery->product_name;
                        return $returnValue;
                    })
                    ->setRowId(function ($selectQuery) {
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        return 'variant-row-'.$enc_product_id;
                    })
                    ->setRowClass(function ($user) {
                        return 'variants';
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'declined-product-list'){
                $selectQuery = Product::with('subCategoryWithCategory')
                    ->with('variants')
                    ->with('createdBy')
                    ->with('updatedBy')
                    ->with('subCategoryWithCategory.category')
                    ->where('products.status','=','DECLINED')
                    ->where('archive','=',false)
                    ->whereNull('parent_id');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery){
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<input type="hidden" id="'.$enc_product_id.'-product-name" value="'.$selectQuery->product_name.'"> ';
                        $returnValue .= '<button onClick=updateProduct("'.$enc_product_id.'") class="btn btn-primary btn-sm btn-icon" title="Edit"><span class="fas fa-edit"></span></button>&nbsp;';
                        if($selectQuery->variants->count() > 0){
                            $enc_product_id = encryptor('encrypt',$selectQuery->id);
                            $variantLink = route('product-variants',['pid' => $enc_product_id]);
                            $returnValue .= '<a href="'.$variantLink.'" class="btn btn-info btn-sm btn-icon" title="Variants"><span class="fas fa-tags"></span></a>&nbsp;';
                        }
                        return $returnValue;
                    })

                    ->editColumn('created_by.username', function($selectQuery) {
                        $returnValue = $selectQuery->createdBy->username;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Last Update By: '.$selectQuery->updatedBy->username.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('swatches', function($selectQuery) {
                        $returnValue = '- - - - ';
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        if(!empty($selectQuery->swatches)){
                            $returnValue = '';
                            $swatches = explode(',',$selectQuery->swatches);
                            foreach($swatches as $index=>$swatch){
                                $tempkey = 'swatch-'.$enc_product_id;
                                $tempkey = $tempkey.'-'.$index;
                                $enc_swatch = encryptor('encrypt',$swatch);
                                $returnValue .= '<input type="hidden" value="'.$enc_swatch.'" id="'.$tempkey.'"/>';
                                $returnValue .= '<a onClick=viewSwatch("'.$tempkey.'") href="javascript:;"><span class="badge badge-primary">'.$swatch.'</span></a>&nbsp;';
                            }
                        }
                        return $returnValue;
                    })
                    ->editColumn('sub_category_with_category.name', function($selectQuery) {
                        $returnValue = '';
                        $returnValue .= $selectQuery->subCategoryWithCategory->name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Category: '.$selectQuery->subCategoryWithCategory->category->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('product_name', function($selectQuery) {
                        $returnValue ='<span class="badge badge-primary">'.$selectQuery->type.'</span> | ';
                        $returnValue .= $selectQuery->product_name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Added At: '.readableDate($selectQuery->created_at).'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'approved-product-list'){
                $selectQuery = Product::with('subCategoryWithCategory')
                    ->with('variants')
                    ->with('createdBy')
                    ->with('updatedBy')
                    ->with('subCategoryWithCategory.category')
                    ->where('products.status','=','APPROVED')
                    ->where('archive','=',false)
                    ->whereNull('parent_id');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery){
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<input type="hidden" id="'.$enc_product_id.'-product-name" value="'.$selectQuery->product_name.'"> ';
                        $returnValue .= '<button onClick=updateProduct("'.$enc_product_id.'") class="btn btn-info btn-sm btn-icon" title="Edit"><span class="fas fa-edit"></span></button>&nbsp;';
                        if($selectQuery->variants->count() > 0){
                            $enc_product_id = encryptor('encrypt',$selectQuery->id);
                            $variantLink = route('product-variants',['pid' => $enc_product_id]);
                            $returnValue .= '<a href="'.$variantLink.'" class="btn btn-info btn-sm btn-icon" title="Variants"><span class="fas fa-tags"></span></a>&nbsp;';
                        }
                        return $returnValue;
                    })
                    ->editColumn('created_by.username', function($selectQuery) {
                        $returnValue = $selectQuery->createdBy->username;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Last Update By: '.$selectQuery->updatedBy->username.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('swatches', function($selectQuery) {
                        $returnValue = '- - - - ';
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        if(!empty($selectQuery->swatches)){
                            $returnValue = '';
                            $swatches = explode(',',$selectQuery->swatches);
                            foreach($swatches as $index=>$swatch){
                                $tempkey = 'swatch-'.$enc_product_id;
                                $tempkey = $tempkey.'-'.$index;
                                $enc_swatch = encryptor('encrypt',$swatch);
                                $returnValue .= '<input type="hidden" value="'.$enc_swatch.'" id="'.$tempkey.'"/>';
                                $returnValue .= '<a onClick=viewSwatch("'.$tempkey.'") href="javascript:;"><span class="badge badge-primary">'.$swatch.'</span></a>&nbsp;';
                            }
                        }
                        return $returnValue;
                    })
                    ->editColumn('sub_category_with_category.name', function($selectQuery) {
                        $returnValue = '';
                        $returnValue .= $selectQuery->subCategoryWithCategory->name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Category: '.$selectQuery->subCategoryWithCategory->category->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('product_name', function($selectQuery) {
                        $returnValue ='<span class="badge badge-primary">'.$selectQuery->type.'</span> | ';
                        $returnValue .= $selectQuery->product_name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Added At: '.readableDate($selectQuery->created_at).'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'forapproval-product-list'){
                $selectQuery = Product::with('subCategoryWithCategory')
                    ->with('variants')
                    ->with('createdBy')
                    ->with('updatedBy')
                    ->with('subCategoryWithCategory.category')
                    ->where('products.status','=','R-APPROVAL')
                    ->where('archive','=',false)
                    ->whereNull('parent_id');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery){
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<input type="hidden" id="'.$enc_product_id.'-product-name" value="'.$selectQuery->product_name.'"> ';
                        $returnValue.= '<button onClick=updateProductStatus("'.$enc_product_id.'","APPROVED") class="btn btn-success btn-icon btn-sm" title="Approved"><span class="fas fa-check"></span></button>&nbsp;';
                        $returnValue.= '<button onClick=updateProductStatus("'.$enc_product_id.'","DECLINED") class="btn btn-danger btn-sm btn-icon" title="Archived"><span class="fas fa-times"></span></button>&nbsp;';
                        $returnValue .= '<button onClick=updateProduct("'.$enc_product_id.'") class="btn btn-info btn-sm btn-icon" title="Edit"><span class="fas fa-edit"></span></button>&nbsp;';
                        if($selectQuery->variants->count() > 0){
                            $enc_product_id = encryptor('encrypt',$selectQuery->id);
                            $variantLink = route('product-variants',['pid' => $enc_product_id]);
                            $returnValue .= '<a href="'.$variantLink.'" class="btn btn-info btn-sm btn-icon" title="Variants"><span class="fas fa-tags"></span></a>&nbsp;';
                        }
                        return $returnValue;
                    })
                    ->editColumn('created_by.username', function($selectQuery) {
                        $returnValue = $selectQuery->createdBy->username;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Last Update By: '.$selectQuery->updatedBy->username.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('swatches', function($selectQuery) {
                        $returnValue = '- - - - ';
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        if(!empty($selectQuery->swatches)){
                            $returnValue = '';
                            $swatches = explode(',',$selectQuery->swatches);
                            foreach($swatches as $index=>$swatch){
                                $tempkey = 'swatch-'.$enc_product_id;
                                $tempkey = $tempkey.'-'.$index;
                                $enc_swatch = encryptor('encrypt',$swatch);
                                $returnValue .= '<input type="hidden" value="'.$enc_swatch.'" id="'.$tempkey.'"/>';
                                $returnValue .= '<a onClick=viewSwatch("'.$tempkey.'") href="javascript:;"><span class="badge badge-primary">'.$swatch.'</span></a>&nbsp;';
                            }
                        }
                        return $returnValue;
                    })
                    ->editColumn('sub_category_with_category.name', function($selectQuery) {
                        $returnValue = '';
                        $returnValue .= $selectQuery->subCategoryWithCategory->name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Category: '.$selectQuery->subCategoryWithCategory->category->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('product_name', function($selectQuery) {
                        $returnValue ='<span class="badge badge-primary">'.$selectQuery->type.'</span> | ';
                        $returnValue .= $selectQuery->product_name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text class="small text-primary">Added At: '.readableDate($selectQuery->created_at).'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'create-product-with-variants'){
                $product_id = '';
                $productvariants = array();
                $type = 'SUPPLY';
                $department_id = 19; //supply purchasing
                $attributes = [
                    'img' => 'Product Image',
                    'product_name' => 'Product Name',
                    'swatches' => 'Swatches',
                    'category' => 'Category',
                    'sub_category' => 'Sub Category',
                    'variants' => 'Product Combinations',
                    'is_default' => 'Default display',
                ];
                $rules = [
                    'img' => 'required|image|mimes:jpeg,png|max:1024',
                    'product_name' => 'required|unique:products,product_name,NULL,id,sub_category_id,'.$data['sub_category'].',archive,0|max:100',
                    'category'=>'required',
                    'sub_category'=>'required',
                  //  'swatches' => 'required',
                    'variants' => 'required',
                    'is_default' => 'required',
                ];
                if(isset($data['variants'])){
                    $defaults = $data['is_default'];
                    foreach($data['variants'] as $index=>$variant){
                        $variant = json_decode($variant);
                        $productdescription = array();
                        $productdescription['parent_id'] = 0;
                        $productdescription['descriptions'] = array();
                        $productdescription['variant'] = '';
                        $productdescription['base_price'] = $data['base_price'][$index];
                        $productdescription['is_default'] = filter_var($defaults[$index],FILTER_VALIDATE_BOOLEAN);
                        $variant_description = ' ';
                        for($i=0;$i < count($variant); $i++) {
                            if( $i > 0 && $i < count($variant)){
                                $variant_description .= ' | ';
                            }
                            $attributeandvalue = array();
                            $attributeandvalue['attribute_name'] = str_replace('--', ' ',$variant[$i]->attribute_key);
                            $attributeandvalue['attribute_value'] =str_replace('--', ' ',$variant[$i]->attribute_value);
                            array_push($productdescription['descriptions'],$attributeandvalue);
                            $variant_description = $variant_description.''.strtoupper(str_replace('--', ' ',$variant[$i]->attribute_description)).':'.strtoupper(str_replace('--', ' ',$variant[$i]->attribute_value)).'';
                        }
                        $productdescription['variant'] = $variant_description;
                        array_push($productvariants,$productdescription);
                        $attributes['base_price.'.$index] = $variant_description.' base price';
                        $rules['base_price.'.$index] = 'required|min:1';
                    }
                }
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $selected_swatches = '';
                    if(!empty($data['swatches'])){
                        $selected_swatches = implode(',',$data['swatches']);
                    }
                    $parentColumn = array(
                        'product_name'=>trim($data['product_name']),
                        'swatches'=> $selected_swatches,
                        'category_id'=>$data['category'],
                        'sub_category_id'=>$data['sub_category'],
                        'department_id'=>$department_id,
                        'type'=>$type,
                        'created_by'=>$user->id,
                        'updated_by'=>$user->id,
                        'created_at'=>getDatetimeNow(),
                        'updated_at'=>getDatetimeNow()
                    );
                    DB::beginTransaction();
                    try {
                        DB::transaction(function () use ($data, $user,$parentColumn,$productvariants,$department_id) {
                            $insertQuery = Product::create($parentColumn);
                            $parent_id = $insertQuery->id;
                            $enc_product_id = encryptor('encrypt',$insertQuery->id); // parent_id
                            $destination  = 'assets/img/products/'.$enc_product_id.'/';
                            $description = htmlentities($data['description']);
                            //variants
                            foreach($productvariants as $index=>$productvariant){
                                $columns = array(
                                    'parent_id' =>$parent_id,
                                    'product_name' =>trim($productvariant['variant']),
                                    'is_default' =>$productvariant['is_default'],
                                    'base_price' =>$productvariant['base_price'],
                                    'created_by'=>$user->id,
                                    'updated_by'=>$user->id,
                                    'created_at'=>getDatetimeNow(),
                                    'updated_at'=>getDatetimeNow()
                                );
                                $insertVariantQuery = Product::create($columns);
                                $variant_id =  $insertVariantQuery->id; // product info padin pero may parent id
                                $variants = array();
                                foreach($productvariant['descriptions'] as $properties ){
                                    $column = array();
                                    $column['product_id'] = $variant_id;
                                    $column['attribute_name'] = trim($properties['attribute_name']);
                                    $column['attribute_value'] = trim($properties['attribute_value']);
                                    $column['created_at'] = getDatetimeNow();
                                    $column['updated_at'] = getDatetimeNow();
                                    array_push($variants,$column);
                                }
                                ProductVariant::insert($variants);
                            }
                            // Binaba ko talaga to para kapag nag error yung query, hindi agad mag uupload ng files
                            $filename= $enc_product_id;
                            if(isset($data['img'])) {
                                $file = $data['img'];
                                $isExist = isExistFile($destination . '' . $filename);
                                if ($isExist['is_exist'] == true){
                                    unlink($isExist['path']);
                                }
                                fileStorageUpload($file, $destination, $filename, 'resize', 685, 888);
                            }
                            $resultCreate = toTxtFile($destination,'description','put',$description);

                            DB::commit();
                        });
                        return array('success' => 1, 'message' => 'Product Added ( FOR APPROVAL )');
                    }catch (QueryException $exception) {
                        DB::rollback();
                        return array('success' => 0, 'message' =>$exception->errorInfo[2]);
                        return back()->withInput($data);
                    }
                }
            }
            elseif($postMode == 'generate-combination'){
                $attributes = array();
                $attributes_description = array();
                $combinations = array();
                $htmlCombo = '';
                $keys = explode(',', $data['keys']);
                $description = explode(',', $data['description']);
                $values = explode(',', $data['values']);
                foreach ($keys as $index => $value) {
                    $attributes_description[$value] = $description[$index];
                    $merge = $value . '**' . $attributes_description[$value] . '**' . $values[$index];
                    if (array_key_exists($value, $attributes) == true) {
                        if (is_array($attributes[$value])) {
                            array_push($attributes[$value], $merge);
                        } else {
                            $attributes[$value] = array($attributes[$value], $merge);
                        }
                    } else {
                        $attributes[$value] = array($merge);
                    }
                }
                $combos = generate_combinations($attributes);
                // create html
                foreach ($combos as $index=>$attributes) {
                    $productvariants = array();
                    $variant_description = '';
                    $htmlCombo = '';
                    $htmlCombo = '
                        <tr style="font-size: 12px;">
                            <td> |
                    ';
                    foreach ($attributes as $attribute) {
                        $productvariant = array();
                        $description = explode('**', $attribute);
                        $productvariant['attribute_key'] = preg_replace('/\s+/', '--', $description[0]);
                        $productvariant['attribute_value'] = preg_replace('/\s+/', '--', $description[2]);
                        $productvariant['attribute_description'] = preg_replace('/\s+/', '--', $description[1]);
                        array_push($productvariants, $productvariant);
                        $variant_description = $variant_description . '' . strtoupper($description[1]) . ' : ' . strtoupper($description[2]) . ' |';
                        // [0] key
                        // [1] description
                        // [2] value
                        $htmlCombo = $htmlCombo . '
                                    ' . strtoupper($description[1]) . ' : ' . strtoupper($description[2]) . ' |
                            ';
                    }
                    $htmlCombo = $htmlCombo . '
                            <input type="hidden" class="form-control" name="variants[]" value='.json_encode($productvariants).'>
                            </td>
                            <td>
                                <input class="form-control form-control-sm" name="base_price[]"  min="1" type="number" step=".01">
                            </td>
                            <td>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="variant-'.$index.'" value="false" name="is_default[]">
                                    <label class="custom-control-label" for="variant-'.$index.'"></label>
                                </div>
                            </td>
                        </tr>
                    ';
                    array_push($combinations, $htmlCombo);
                }
                return array('success' => 1, 'message' => 'Generate success', 'data' => $combinations);
            }
            else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode == 'add-to-fitout-product'){
                $attributes = [
                    'key' => 'Product',
                    'type' => 'type',
                ];
                $rules = [
                    'key'=>'required',
                    'type'=>'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                    return back();
                }else {
                    $type = $data['type'];
                    $product_id = encryptor('decrypt',$data['key']);
                    $parent_id = encryptor('decrypt',$data['parent']);
                    $product_name = '';
                    $selectQuery = array();
                    $parentQuery = Product::whereNull('parent_id')
                                        ->where('type','=','FIT-OUT')
                                        ->where('id','=',$parent_id)
                                        ->first();
                    if($type == 'RAW' || $type == 'SPECIAL-ITEM' ){
                        $selectQuery = Product::whereNull('parent_id')
                                                ->where('type','=',$type)
                                                ->where('id','=',$product_id)
                                                ->first();
                        $product_name = $selectQuery->product_name;
                    }
                    elseif($type == 'SUPPLY'){
                        $selectQuery = Product::with('parent')
                                ->whereNotNull('parent_id')
                                ->where('type','=',$type)
                                ->where('id','=',$product_id)
                                ->first();
                        $product_name = $selectQuery->parent->product_name.' v: '.$selectQuery->product_name;
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to find product. Please try again');
                        return back();
                    }
                    $insertQuery = new Product();
                    $insertQuery->product_name = $product_name;
                    $insertQuery->parent_id = $parent_id;
                    $insertQuery->type = $selectQuery->type;
                    $insertQuery->base_price = $data['price'];
                    $insertQuery->status = $parentQuery->status;
                    $insertQuery->remarks = 'PREVIOUS: '.$selectQuery->type;
                    $insertQuery->updated_by = $user->id;
                    $insertQuery->created_by = $user->id;
                    $insertQuery->created_at = getDatetimeNow();
                    $insertQuery->updated_at = getDatetimeNow();
                    if($insertQuery->save()){
                        $parentQuery->base_price += $data['price'];
                        if($parentQuery->save()){
                            Session::flash('success',1);
                            Session::flash('message','Product Added');
                            return back();
                        }else{
                            Session::flash('success',0);
                            Session::flash('message','Unable to update fit-out base price. Please try again');
                            return back();
                        }
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to save product. Please try again');
                        return back();
                    }
                }
            }
            elseif($postMode == 'update-product'){
                $type = $data['type'];
                $product_id = encryptor('decrypt',$data['key']);
                $attributes = [
                    'img' => 'Product Image',
                    'product_name' => 'Product Name',
                    'swatches' => 'Swatches',
                    'category' => 'Category',
                    'sub_category' => 'Sub Category',
                    'description' => 'Description',
                ];
                $rules = [
                    'img' => 'nullable|image|mimes:jpeg,png|max:1024',
                    'product_name' => 'required|unique:products,product_name,'.$product_id.',id,sub_category_id,'.$data['sub_category'].',archive,0|max:100',
                    'category'=>'required',
                    'sub_category'=>'required',
                    //'description'=>'required',
                ];
                if($type != 'RAW' && $type != 'SPECIAL-ITEM' && $type != 'FIT-OUT' ){
                    $rules['description'] = 'required';
                    //$rules['swatches'] = 'required';
                }else{
                    if($type != 'FIT-OUT'){
                        $rules['description'] = 'required';
                        $rules['base_price'] = 'Base Price';
                        $rules['base_price'] = 'required';
                    }
                }
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                    Session::flash('update-product',$data['key']);
                    return back();
                }else {
                    $selectQuery = Product::find($product_id);
                    if($selectQuery){
                        $selectQuery->product_name = trim($data['product_name']);
                        $selectQuery->category_id = $data['category'];
                        $selectQuery->sub_category_id = $data['sub_category'];
                        if($type != 'RAW' && $type != 'SPECIAL-ITEM' ) {
                            if(!empty($data['swatches'])){
                                $selectQuery->swatches = implode(',', $data['swatches']);
                            }
                        }else{
                            if($type != 'FIT-OUT'){
                                $selectQuery->base_price = $data['base_price'];
                            }
                        }
                        $selectQuery->updated_by = $user->id;
                        $selectQuery->updated_at = getDatetimeNow();
                        if($selectQuery->save()){
                            $enc_product_id = $data['key'];
                            $destination  = 'assets/img/products/'.$enc_product_id.'/';
                            if($type != 'FIT-OUT') {
                                $description = htmlentities($data['description']);
                                $resultCreate = toTxtFile($destination, 'description', 'put', $description);
                            }
                            $filename= $enc_product_id;
                            if(isset($data['img'])) {
                                $file = $data['img'];
                                $isExist = isExistFile($destination . '' . $filename);
                                if ($isExist['is_exist'] == true){
                                    unlink($isExist['path']);
                                }
                                fileStorageUpload($file, $destination, $filename, 'resize', 685, 888);
                            }
                            Session::flash('success',1);
                            Session::flash('message','Product Updated');
                        }else{
                            Session::flash('success',0);
                            Session::flash('message','Unable to update product. Please try again');
                        }
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to find product. Please try again');
                    }
                    return back();
                }
            }
            elseif($postMode == 'update-product-status'){
                $product_id = encryptor('decrypt',$data['product_key']);
                $selectQuery = Product::with('variants')->find($product_id);
                if($selectQuery){
                    $selectQuery->status = trim($data['product_status']);
                    $selectQuery->remarks = trim($data['remarks']);
                    $selectQuery->updated_by =$user->id;
                    $selectQuery->updated_at = getDatetimeNow();
                    if($selectQuery->save()){
                        $selectQuery->variants()->update(['status' => $selectQuery->status]);
                        Session::flash('success',1);
                        Session::flash('message','Product '.$selectQuery->product_name.' '.strToTitle($selectQuery->status));
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to update product status. Please try again');
                    }
                }else{
                    Session::flash('success',0);
                    Session::flash('message','Unable to find product please try again');
                }
                return back();
            }
            elseif($postMode == 'update-variant-price'){
                $variant_id = encryptor('decrypt',$data['variant']);
                $product_id = encryptor('decrypt',$data['product']);
                $selectQuery = Product::find($variant_id);
                if($selectQuery){
                    $selectQuery->base_price = $data['base_price'];
                    $selectQuery->updated_at = getDatetimeNow();
                    if($selectQuery->save()){
                        Session::flash('success',1);
                        Session::flash('message','Variant '.$selectQuery->name.' base price updated');
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to update variant price. Please try again');
                    }
                }else{
                    Session::flash('success',0);
                    Session::flash('message','Unable to find product variant. Please try again');
                }
                return back();
            }
            elseif($postMode == 'variant-default'){
                $variant_id = encryptor('decrypt',$data['variant']);
                $product_id = encryptor('decrypt',$data['product']);
                $selectQuery = Product::where('parent_id','=',$product_id)->get();
                foreach($selectQuery as $variant){
                    $variant->is_default = false;
                    if($variant_id == $variant->id){
                        $variant->is_default = true;
                    }
                    $variant->save();
                }
                Session::flash('success',1);
                Session::flash('message','Default variant updated.');
                return back();
            }
            elseif($postMode == 'add-product-fitout'){
                $attributes = [
                    'img' => 'Product Image',
                    'keys' => 'Fit Out Products',
                    'product_name' => 'Product Name',
                    'category' => 'Category',
                    'sub_category' => 'Sub Category',
                   // 'base_price' => 'Base price',
                ];
                $rules = [
                    'img' => 'required|image|mimes:jpeg,png|max:1024',
                    'keys' => 'required',
                    'product_name' => 'required|unique:products,product_name,NULL,id,sub_category_id,'.$data['sub_category'].',archive,0|max:100',
                    'category'=>'required',
                    'sub_category'=>'required',
                   // 'base_price' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                    return back()->withInput($data);
                }else{
                    $type = 'FIT-OUT';
                    $department_id = 6; // Fit out
                    $total_base_price = 0;
                    foreach($data['prices'] as $price){
                        $total_base_price += $price;
                    }
                    $parentColumn = array(
                        'product_name'=>trim($data['product_name']),
                        'category_id'=>$data['category'],
                        'sub_category_id'=>$data['sub_category'],
                        'department_id'=>$department_id,
                        'base_price'=>$total_base_price,
                        'type'=> $type,
                        'created_by'=>$user->id,
                        'updated_by'=>$user->id,
                        'created_at'=>getDatetimeNow(),
                        'updated_at'=>getDatetimeNow()
                    );
                    DB::beginTransaction();
                    try {
                        DB::transaction(function () use ($user,$parentColumn,$type,$data,$department_id) {
                            $insertQuery = Product::create($parentColumn);
                            $enc_product_id = encryptor('encrypt',$insertQuery->id); // parent_id
                            $destination  = 'assets/img/products/'.$enc_product_id.'/';
                            $fitoutproducts = array();
                            foreach($data['keys'] as $index=>$product){
                                $fitoutproduct = array();
                                $fitoutproduct['parent_id'] = $insertQuery->id;
                                $fitoutproduct['product_name'] = $data['names'][$index];
                                $fitoutproduct['type'] = $data['types'][$index];
                                $fitoutproduct['base_price'] = $data['prices'][$index];
                                $fitoutproduct['remarks'] = 'PREVIOUS: '.$data['types'][$index];
                                $fitoutproduct['created_by'] = $user->id;
                                $fitoutproduct['updated_by'] = $user->id;
                                $fitoutproduct['created_at'] = getDatetimeNow();
                                $fitoutproduct['updated_at'] = getDatetimeNow();
                                array_push($fitoutproducts,$fitoutproduct);
                            }
                            Product::insert($fitoutproducts);
                            $filename= $enc_product_id;
                            if(isset($data['img'])) {
                                $file = $data['img'];
                                $isExist = isExistFile($destination . '' . $filename);
                                if ($isExist['is_exist'] == true){
                                    unlink($isExist['path']);
                                }
                                fileStorageUpload($file, $destination, $filename, 'resize', 685, 888);
                            }
                            DB::commit();
                        });
                        Cart::clear();
                        Session::flash('success',1);
                        Session::flash('message','FIT-OUT Product Added ( FOR APPROVAL )');
                        return back();
                    }catch (QueryException $exception) {
                        DB::rollback();
                        Session::flash('success',1);
                        Session::flash('message',$exception->errorInfo[2]);
                        return back();
                    }
                }
            }
            elseif($postMode == 'add-product-raw'){
                $product_id = '';
                $productvariants = array();
                $type = 'RAW';
                $department_id = 7; //raw purchasing
                $attributes = [
                    'img' => 'Product Image',
                    'product_name' => 'Product Name',
                    'swatches' => 'Swatches',
                    'category' => 'Category',
                    'sub_category' => 'Sub Category',
                    'base_price' => 'Base price',
                    'description' => 'Description',
                ];
                $rules = [
                    'img' => 'required|image|mimes:jpeg,png|max:1024',
                    'product_name' => 'required|unique:products,product_name,NULL,id,sub_category_id,'.$data['sub_category'].',archive,0|max:100',
                    'category'=>'required',
                    'sub_category'=>'required',
                    'base_price' => 'required',
                    'description'=>'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                    return back()->withInput($data);
                }else{
                    $insertQuery = new Product();
                    $insertQuery->product_name = trim($data['product_name']);
                    $insertQuery->sub_category_id = $data['sub_category'];
                    $insertQuery->category_id = $data['category'];
                    $insertQuery->base_price = $data['base_price'];
                    $insertQuery->department_id = $department_id;
                    $insertQuery->type = $type;
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    $insertQuery->created_at = getDatetimeNow();
                    $insertQuery->updated_at = getDatetimeNow();
                    if($insertQuery->save()){
                        $enc_product_id = encryptor('encrypt',$insertQuery->id);
                        $destination  = 'assets/img/products/'.$enc_product_id.'/';
                        $description = htmlentities($data['description']);
                        $filename= $enc_product_id;
                        if(isset($data['img'])) {
                            $file = $data['img'];
                            $isExist = isExistFile($destination . '' . $filename);
                            if ($isExist['is_exist'] == true){
                                unlink($isExist['path']);
                            }
                            fileStorageUpload($file, $destination, $filename, 'resize', 685, 888);
                        }
                        $resultCreate = toTxtFile($destination,'description','put',$description);
                        Session::flash('success',1);
                        Session::flash('message','Product [ RAW ] Added ( FOR APPROVAL )');
                    }
                    else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to create product [ RAW ]. Please try again');
                    }
                    return back();
                }
            }
            else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
