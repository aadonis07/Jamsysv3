<?php

namespace App\Http\Controllers\PurchasingRaw;

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
class ProductController extends Controller
{
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
                return view('purchasing-raw-department.products.page-load.product-details')
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
        return view('purchasing-raw-department.products.index')
            ->with('admin_menu','PRODUCTS')
            ->with('admin_sub_menu','LIST')
            ->with('categories',$selectCategoryQuery)
            ->with('user',$user);
    }
    function showAddproductRaw(){
        $user = Auth::user();
        $selectCategoryQuery = Category::with('subCategoryWithSwatches')->with('attributes')
            ->where('status','=','ACTIVE')
            ->orderBy('name')
            ->get();
        return view('purchasing-raw-department.products.add-product-raw')
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
                    ->whereIn('type',['RAW','SPECIAL-ITEM'])
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
                    ->whereIn('type',['RAW','SPECIAL-ITEM'])
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
                    ->whereIn('type',['RAW','SPECIAL-ITEM'])
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
            else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode == 'update-product'){
                // for product raw and special item update-product.
                $type = $data['type'];
                $product_id = encryptor('decrypt',$data['key']);
                $attributes = [
                    'img' => 'Product Image',
                    'product_name' => 'Product Name',
                    'type' => 'type',
                    'base_price' => 'Base Price',
                    'category' => 'Category',
                    'sub_category' => 'Sub Category',
                    'description' => 'Description',
                ];
                $rules = [
                    'img' => 'nullable|image|mimes:jpeg,png|max:1024',
                    'product_name' => 'required|unique:products,product_name,'.$product_id.',id,sub_category_id,'.$data['sub_category'].',archive,0|max:100',
                    'type'=>'required',
                    'base_price'=>'required',
                    'category'=>'required',
                    'sub_category'=>'required',
                    'description'=>'required',
                ];
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
                        $selectQuery->type = $type;
                        $selectQuery->category_id = $data['category'];
                        $selectQuery->sub_category_id = $data['sub_category'];
                        $selectQuery->base_price = $data['base_price'];
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
            elseif($postMode == 'add-product-raw'){
                $product_id = '';
                $productvariants = array();
                //$type = 'RAW';
                $department_id = 7; //raw purchasing
                $attributes = [
                    'img' => 'Product Image',
                    'type' => 'Type',
                    'product_name' => 'Product Name',
                    'swatches' => 'Swatches',
                    'category' => 'Category',
                    'sub_category' => 'Sub Category',
                    'base_price' => 'Base price',
                    'description' => 'Description',
                ];
                $rules = [
                    'img' => 'required|image|mimes:jpeg,png|max:1024',
                    'type' => 'required',
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
                    $insertQuery->type = trim($data['type']);
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
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
