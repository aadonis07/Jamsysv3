<?php

namespace App\Http\Controllers\PurchasingSupply;

use App\Http\Controllers\Controller;
use App\SwatchGroup;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Auth;
use Session;
use Validator;
use App\Product;
use App\ProductVariant;
use App\Category;
use DB;
class ProductController extends Controller
{
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
    function showVariants(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['pid']) && !empty($data['pid'])){
            $product_id = encryptor('decrypt',$data['pid']);
            $selectQuery = Product::with('variants')->with('subCategoryWithCategory')->find($product_id);
            return view('purchasing-supply-department.products.variants')
                ->with('admin_menu','PRODUCTS')
                ->with('admin_sub_menu','LIST')
                ->with('product',$selectQuery)
                ->with('user',$user);
        }
        Session::flash('success',0);
        Session::flash('message','Unable to find product. Please try again');
        return back();
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
                return view('purchasing-supply-department.products.page-load.product-details')
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
    function showIndex(){
        $user = Auth::user();
        $selectCategoryQuery = Category::with('subCategories')->with('attributes')
            ->where('status','=','ACTIVE')
            ->orderBy('name')
            ->get();
        return view('purchasing-supply-department.products.index')
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
        return view('purchasing-supply-department.products.add-product')
            ->with('categories',$selectCategoryQuery)
            ->with('admin_menu','PRODUCTS')
            ->with('admin_sub_menu','CREATE')
            ->with('user',$user);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'declined-product-list'){
                $selectQuery = Product::with('subCategoryWithCategory')
                    ->with('createdBy')
                    ->with('updatedBy')
                    ->with('subCategoryWithCategory.category')
                    ->where('products.status','=','DECLINED')
                    ->whereIn('type',['SUPPLY'])
                    ->where('archive','=',false)
                    ->whereNull('parent_id');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery){
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<input type="hidden" id="'.$enc_product_id.'-product-name" value="'.$selectQuery->product_name.'"> ';
                        $returnValue .= '<button onClick=updateProduct("'.$enc_product_id.'") class="btn btn-primary btn-sm btn-icon" title="Edit"><span class="fas fa-edit"></span></button>&nbsp;';
                        if($selectQuery->variants->count() > 0){
                            $enc_product_id = encryptor('encrypt',$selectQuery->id);
                            $variantLink = route('purchasing-supply-product-variants',['pid' => $enc_product_id]);
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
                    ->with('createdBy')
                    ->with('updatedBy')
                    ->with('subCategoryWithCategory.category')
                    ->whereIn('type',['SUPPLY'])
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
                            $variantLink = route('purchasing-supply-product-variants',['pid' => $enc_product_id]);
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
                    ->with('createdBy')
                    ->with('updatedBy')
                    ->with('subCategoryWithCategory.category')
                    ->where('products.status','=','R-APPROVAL')
                    ->whereIn('type',['SUPPLY'])
                    ->where('archive','=',false)
                    ->whereNull('parent_id');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery){
                        $enc_product_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<input type="hidden" id="'.$enc_product_id.'-product-name" value="'.$selectQuery->product_name.'"> ';
                        //$returnValue.= '<button onClick=updateProductStatus("'.$enc_product_id.'","APPROVED") class="btn btn-success btn-icon btn-sm" title="Approved"><span class="fas fa-check"></span></button>&nbsp;';
                        //$returnValue.= '<button onClick=updateProductStatus("'.$enc_product_id.'","DECLINED") class="btn btn-danger btn-sm btn-icon" title="Archived"><span class="fas fa-times"></span></button>&nbsp;';
                        $returnValue .= '<button onClick=updateProduct("'.$enc_product_id.'") class="btn btn-info btn-sm btn-icon" title="Edit"><span class="fas fa-edit"></span></button>&nbsp;';
                        if($selectQuery->variants->count() > 0){
                            $enc_product_id = encryptor('encrypt',$selectQuery->id);
                            $variantLink = route('purchasing-supply-product-variants',['pid' => $enc_product_id]);
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
            if($postMode == 'update-product'){
                // for supply
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
                    'description'=>'required',
                ];
                if($type != 'RAW' && $type != 'SPECIAL-ITEM' ){
                    //$rules['swatches'] = 'required';
                }else{
                    $rules['base_price'] = 'Base Price';
                    $rules['base_price'] = 'required';
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
                            $selectQuery->swatches = null;
                            if(!empty($data['swatches'])){
                                $selectQuery->swatches = implode(',', $data['swatches']);
                            }
                        }else{
                            $selectQuery->base_price = $data['base_price'];
                        }
                        $selectQuery->updated_by = $user->id;
                        $selectQuery->updated_at = getDatetimeNow();
                        if($selectQuery->save()){
                            $enc_product_id = $data['key'];
                            $destination  = 'assets/img/products/'.$enc_product_id.'/';
                            $description = htmlentities($data['description']);
                            $resultCreate = toTxtFile($destination,'description','put',$description);
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
            else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
