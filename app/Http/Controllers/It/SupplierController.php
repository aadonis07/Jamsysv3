<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Validator;
use App\Audit;
use App\Supplier;
use App\Industry;
use App\Province;
use App\City;
use App\Barangay;
use App\Product;
use App\ProductVariant;
use App\SupplierProduct;

use Yajra\DataTables\Facades\DataTables;
use Crypt;

class SupplierController extends Controller
{
    function showSuppliers(Request $request){
        $user = Auth::user();
        $selectIndustryQuery = Industry::where('is_active', '=', 1)
                                ->orderBy('name', 'ASC')
                                ->get();
        $regions = showRegions();
        return view('it-department.suppliers.index')
                        ->with('admin_menu','SUPPLIERS')
                        ->with('industries', $selectIndustryQuery)
                        ->with('regions', $regions);
    }

    function supplierContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $supplier = Supplier::where('id', '=', $id)->first();

            if($supplier) {
                $categories = ["GOODS", "SERVICES"];
                $vat_type = ["VAT Exclusive", "VAT Inclusive"];
                $payment_types = ["DATED-CHECKS", "COD", "WITH-TERMS"];
                $regions = showRegions();
                $selectIndustryQuery = Industry::where('is_active', '=', 1)
                                ->orderBy('name', 'ASC')
                                ->get();

                $selectSupplierRegion = Province::select('id', 'region_id')
                                                ->where('id', '=', $supplier->province_id)
                                                ->first();
                $selectProvincesQuery = Province::where([['region_id', '=', $selectSupplierRegion->region_id],
                                                        ['is_enable','=',true]])
                                                ->orderBy('description', 'ASC')
                                                ->get();
                $selectCitiesQuery = City::where([['province_id', '=', $supplier->province_id],
                                                   ['region_id', '=', $selectSupplierRegion->region_id],
                                                   ['is_enable','=',true]])
                                                ->orderBy('city_name', 'ASC')
                                                ->get();
                $selectBarangayQuery = Barangay::where('city_id', '=', $supplier->city_id)
                                                ->where('is_enable','=',true)
                                                ->orderBy('barangay_description', 'ASC')
                                                ->get();
                return view('it-department.suppliers.page-load.supplier-details')
                                        ->with('supplier', $supplier)
                                        ->with('regions', $regions)
                                        ->with('industries', $selectIndustryQuery)
                                        ->with('supplier_region', $selectSupplierRegion)
                                        ->with('provinces', $selectProvincesQuery)
                                        ->with('cities', $selectCitiesQuery)
                                        ->with('barangays', $selectBarangayQuery)
                                        ->with('categories', $categories)
                                        ->with('vat_type', $vat_type)
                                        ->with('payment_types', $payment_types);
            } else {
                $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find supplier details. Please try again.
                    </div>
                </div>';
            }
        } else {
            $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find supplier details. Please try again.
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }

    function showSupplierLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['sid']) && !empty($data['sid'])){
            $supplier_id = encryptor('decrypt',$data['sid']);
            $selectQuery = Supplier::find($supplier_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-supplier-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Event</th>
                                    <th>Old</th>
                                    <th>New</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                ';
            }else{
                $resultHtml = '
                    <div class="alert alert-danger text-left">
                        <strong>Unable to find data. Please try again.</strong>
                    </div>
                ';
            }
        }else{
            $resultHtml = '
                <div class="alert alert-danger text-left">
                    <strong>Unable to find data. Please try again.</strong>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showSupplierProducts(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['sid']) && !empty($data['sid'])){
            $supplier_id = encryptor('decrypt',$data['sid']);
            $selectSupplier = Supplier::find($supplier_id);
            if($selectSupplier){
                return view('it-department.suppliers.supplier-products')
                    ->with('admin_menu','SUPPLIERS')
                    ->with('admin_sub_menu','')
                    ->with('supplier',$selectSupplier)
                    ->with('user',$user);
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Unable to find supplier. Please try again');
                return back();
            }
        }
        else{
            Session::flash('success', 0);
            Session::flash('message', 'Unable to find supplier. Please try again');
            return back();
        }
    }

    function supplierProductContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';
        if(isset($data['id']) && !empty($data['id'])){
            $supplier_product_id = encryptor('decrypt', $data['id']);
            $selectQuery = SupplierProduct::find( $supplier_product_id);
            if($selectQuery) {
                $product_name = '';
                if($selectQuery->type == 'SUPPLY'){
                    $product_name = $selectQuery->variant->parent->product_name.': '.$selectQuery->variant->product_name;
                }else{
                    $product_name = $selectQuery->product->product_name;
                }
                $resultHtml = '
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-md-12 mb-2">
                                    <label class="form-control-plaintext">Product</label>
                                    <textarea class="form-control" disabled>'.$product_name.'</textarea>
                                </div>
                                <input type="hidden" class="form-control" required name="supplier_product_id" value="'.$data['id'].'">
                                <div class="col-md-6 mb-1">
                                    <label>Supplier\'s Product Code :</label>
                                    <input type="text" class="form-control" required name="supplier_code" placeholder="Supplier\'s Product Code" value="'.$selectQuery->code.'">
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label>Supplier\'s Price :</label>
                                    <input type="number" step=".01" class="form-control" required name="price" placeholder="Supplier\'s Price" value="'.number_format($selectQuery->price,2,'.','').'">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Notes :</label>
                            <textarea class="form-control" name="note" id="supplier-product-note-update" required placeholder="Notes">'.$selectQuery->note.'</textarea>
                        </div>
                ';
            } else {
                $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find supplier product details. Please try again.
                    </div>
                </div>';
            }
        } else {
            $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find supplier product details. Please try again.
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }

    function showSupplierProductLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';
        if(isset($data['spid']) && !empty($data['spid'])){
            $supplier_product_id = encryptor('decrypt',$data['spid']);
            $selectQuery = SupplierProduct::find($supplier_product_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-supplier-product-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Event</th>
                                    <th>Old</th>
                                    <th>New</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                ';
            }else{
                $resultHtml = '
                    <div class="alert alert-danger text-left">
                        <strong>Unable to find data. Please try again.</strong>
                    </div>
                ';
            }
        }else{
            $resultHtml = '
                <div class="alert alert-danger text-left">
                    <strong>Unable to find data. Please try again.</strong>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'supplier-raw-products'){
                $supplier_id = encryptor('decrypt',$data['supplier']);
                $selectQuery = SupplierProduct::with('updatedBy')
                                    ->with('product')
                                    ->where('type','=','RAW')
                                    ->where('supplier_id','=',$supplier_id)
                                    ->orderBy('created_at','DESC');
                    return Datatables::eloquent($selectQuery)
                                    ->addColumn('actions', function($selectQuery){
                                        $enc_supplier_product_id = encryptor('encrypt',$selectQuery->id);
                                        $returnValue='';
                                        $returnValue = '<button onClick=updateSupplierProduct("'.$enc_supplier_product_id.'") class="btn btn-info btn-icon btn-sm waves-effect waves-themed mb-1"  data-toggle="tooltip" data-placement="top" title="Update" data-original-title="UPDATE">
                                            <i class="fas fa-edit"></i>
                                        </button>&nbsp;';
                                        $returnValue .= '<button onClick=logsModal("'.$enc_supplier_product_id.'") class="btn btn-default btn-sm btn-icon waves-effect waves-themed mb-1" data-toggle="tooltip" data-placement="top" title="History Logs" data-original-title="HISTORY LOGS">
                                            <i class="ni ni-calendar"></i>
                                        </button>';
                                        return $returnValue;
                                    })
                                    ->editColumn('created_by.username', function($selectQuery){
                                          $returnValue = $selectQuery->createdBy->username;
                                          $returnValue .= '<hr class="m-0">';
                                          $returnValue .= '<text class="text-primary">LAST: <b>'.$selectQuery->updatedBy->username.'</b></text>';
                                          return $returnValue;
                                    })
                                    ->editColumn('code', function($selectQuery){
                                          $returnValue = $selectQuery->code;
                                          $returnValue .= '<hr class="m-0">';
                                          $returnValue .= '<text class="text-primary">SYSTEM: <b>'.$selectQuery->product->product_name.'</b></text>';
                                          return $returnValue;
                                    })
                                    ->editColumn('price', function($selectQuery){
                                          $returnValue = '<b>&#8369; '.number_format($selectQuery->price,2).'</b>';
                                          $returnValue .= '<hr class="m-0">';
                                          $returnValue .= '<text class="text-primary"><b>Supplier`s Price: </b></text>';
                                          return $returnValue;
                                    })
                                    ->smart(true)
                                    ->escapeColumns([])
                                    ->addIndexColumn()
                                    ->make(true);
            }
            elseif($postMode == 'supplier-supply-products'){
                $supplier_id = encryptor('decrypt',$data['supplier']);
                $selectQuery = SupplierProduct::with('updatedBy')
                                    ->with('createdBy')
                                    ->with('variant')
                                    ->where('type','=','SUPPLY')
                                    ->where('supplier_id','=',$supplier_id)
                                    ->orderBy('created_at','DESC');
                    return Datatables::eloquent($selectQuery)
                                    ->addColumn('actions', function($selectQuery){
                                        $enc_supplier_product_id = encryptor('encrypt',$selectQuery->id);
                                        $returnValue='';
                                        $returnValue = '<button onClick=updateSupplierProduct("'.$enc_supplier_product_id.'") class="btn btn-info btn-icon btn-sm waves-effect waves-themed mb-1"  data-toggle="tooltip" data-placement="top" title="Update" data-original-title="UPDATE">
                                            <i class="fas fa-edit"></i>
                                        </button>&nbsp;';
                                        $returnValue .= '<button onClick=logsModal("'.$enc_supplier_product_id.'") class="btn btn-default btn-sm btn-icon waves-effect waves-themed mb-1" data-toggle="tooltip" data-placement="top" title="History Logs" data-original-title="HISTORY LOGS">
                                            <i class="ni ni-calendar"></i>
                                        </button>';
                                        return $returnValue;
                                    })
                                    ->editColumn('variant.product_name', function($selectQuery){
                                          $returnValue = $selectQuery->variant->product_name;
                                          return $returnValue;
                                    })
                                    ->editColumn('created_by.username', function($selectQuery){
                                          $returnValue = $selectQuery->createdBy->username;
                                          $returnValue .= '<hr class="m-0">';
                                          $returnValue .= '<text class="text-primary">LAST: <b>'.$selectQuery->updatedBy->username.'</b></text>';
                                          return $returnValue;
                                    })
                                    ->editColumn('code', function($selectQuery){
                                          $returnValue = $selectQuery->code;
                                          $returnValue .= '<hr class="m-0">';
                                          $returnValue .= '<text class="text-primary">WEBSITE: <b>'.$selectQuery->variant->parent->product_name.'</b></text>';
                                          return $returnValue;
                                    })
                                    ->editColumn('price', function($selectQuery){
                                          $returnValue = '<b>&#8369; '.number_format($selectQuery->price,2).'</b>';
                                          $returnValue .= '<hr class="m-0">';
                                          $returnValue .= '<text class="text-primary">Website: &#8369; '.number_format($selectQuery->variant->base_price,2).'</text>';
                                          return $returnValue;
                                    })
                                    ->smart(true)
                                    ->escapeColumns([])
                                    ->addIndexColumn()
                                    ->make(true);
            }
            elseif($postMode == 'supply-product-variant-list'){
                $supplier_id = encryptor('decrypt',$data['supplier_id']);
                $product_id =  encryptor('decrypt',$data['product']);
                $selectQuery = Product::with('supplierProduct')->with('parent')
                        ->whereNotNull('parent_id')
                        ->where('parent_id','=',$product_id)
                        ->where('type','=','SUPPLY')
                        ->where('status','=','APPROVED')
                        ->where('archive','=',false)
                        ->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                        ->addColumn('actions', function($selectQuery) use($supplier_id) {
                            $returnValue = '';
                            $isAlreadyAdded = $selectQuery->supplierProduct
                                            ->where('type','=','SUPPLY')
                                            ->where('supplier_id','=',$supplier_id)
                                            ->count();
                            if($isAlreadyAdded){
                                $returnValue ='<text class="text-success">Already Added</text>';
                            }else{
                                $enc_product_id = encryptor('encrypt',$selectQuery->id);
                                $returnValue = '<button onClick=addToSupplySupplier("'.$enc_product_id.'") class="btn btn-info btn-xs " title="Add to Supplier">Add To Supplier</a>&nbsp;';
                            }
                            return $returnValue;
                        })
                        ->editColumn('product_name', function($selectQuery){
                            $enc_product_id = encryptor('encrypt',$selectQuery->id);
                            $returnValue = '
                                <input type="hidden" id="variant-name-'.$enc_product_id.'" value="'.$selectQuery->product_name.'"/>
                                <input type="hidden" id="product-name-'.$enc_product_id.'" value="'.$selectQuery->parent->product_name.'"/>
                                <input type="hidden" id="product-type-'.$enc_product_id.'" value="'.$selectQuery->parent->type.'"/>
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
            elseif($postMode == 'raw-product-list') {
                $supplier_id = encryptor('decrypt',$data['supplier_id']);
                /**
                 *  SUPPLY  = WITH VARIANTS ( NOT NULL ON PARENT_ID )
                 *  RAW = NO VARIANTS ( NULL ON PARENT_ID TYPE: RAW )
                 */
                $selectQuery = Product::with('subCategoryWithCategory')
                                    ->with('supplierProduct')
                                    ->with('subCategoryWithCategory.category')
                                    ->whereNull('parent_id')
                                    ->where('type','=','RAW')
                                    ->where('status','=','APPROVED')
                                    ->where('archive','=',false)
                                    ->orderBy('created_at');
                    return Datatables::eloquent($selectQuery)
                                    ->editColumn('actions', function($selectQuery) use($supplier_id) {
                                        $returnValue = '';
                                        $isAlreadyAdded = $selectQuery->supplierProduct
                                            ->where('type','=','RAW')
                                            ->where('supplier_id','=',$supplier_id)
                                            ->count();
                                        if(!$isAlreadyAdded){
                                            $enc_product_id = encryptor('encrypt',$selectQuery->id);
                                            $returnValue .= '
                                                <input type="hidden" id="raw-product-name-'.$enc_product_id.'" value="'.$selectQuery->product_name.'"/>
                                                <input type="hidden" id="raw-product-type-'.$enc_product_id.'" value="'.$selectQuery->type.'"/>
                                            ';
                                            $returnValue .= '<button onClick=addToRawSupplier("'.$enc_product_id.'") class="btn btn-info btn-xs small" >Add to Supplier</a>&nbsp;';
                                        }else{
                                            $returnValue ='<text class="text-success">Already Added</text>';
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
            elseif($postMode == 'supply-product-list') {
                $supplier_id = encryptor('decrypt',$data['supplier_id']);
                /**
                 *  SUPPLY  = WITH VARIANTS ( NOT NULL ON PARENT_ID )
                 *  RAW = NO VARIANTS ( NULL ON PARENT_ID TYPE: RAW )
                 */
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
            elseif($postMode == 'supplier-list') {
                $selectQuery = Supplier::orderBy('created_at');
                    return Datatables::eloquent($selectQuery)
                            ->addColumn('actions', function($selectQuery){
                                $enc_supplier_id = encryptor('encrypt',$selectQuery->id);
                                $productsUrl = route('supplier-products',['sid'=>$enc_supplier_id]);
                                $returnValue = '<button class="btn btn-info btn-icon btn-sm waves-effect waves-themed mb-1" onClick=updateSupplier("'.$enc_supplier_id.'") data-toggle="tooltip" data-placement="top" title="Update" data-original-title="UPDATE">
                                            <i class="fas fa-edit"></i>
                                        </button>&nbsp;';
                                $returnValue .= '<a href="'.$productsUrl.'" class="btn btn-info btn-icon btn-sm waves-effect waves-themed mb-1" data-toggle="tooltip" data-placement="top" title="View Products" data-original-title="VIEW PRODUCTS">
                                                    <i class="fas fa-tag"></i>
                                                </a>&nbsp;';
                                $returnValue .= '<button onClick=logsModal("'.$enc_supplier_id.'") class="btn btn-default btn-sm btn-icon waves-effect waves-themed mb-1" data-toggle="tooltip" data-placement="top" title="History Logs" data-original-title="HISTORY LOGS">
                                            <i class="ni ni-calendar"></i>
                                        </button>';
                                return $returnValue;
                            })
                            ->editColumn('name', function($selectQuery) {
                                $returnValue = $selectQuery->name;
                                $returnValue .='<hr class="m-0 mt-1">';
                                $returnValue .= '<text title="'.$selectQuery->tin_number.'" class="small text-primary mb-1" style="font-size:12px;">CODE: <b>'.$selectQuery->code.'</b></text>';
                                return $returnValue;
                            })
                            ->editColumn('category', function($selectQuery) {
                                $vats = array(
                                  'INCLUSIVE',
                                  'EXCLUSIVE',
                                );
                                $returnValue = $selectQuery->category;
                                $returnValue .='<hr class="m-0 mt-1">';
                                $returnValue .= '<text title="'.$vats[$selectQuery->vatable].'" class="small text-primary" style="font-size:12px;">VAT: <b>'.$vats[$selectQuery->vatable].'</b></text>';
                                return $returnValue;
                            })
                            ->editColumn('contact_person', function($selectQuery) {
                                $returnValue = $selectQuery->contact_person;
                                $returnValue .='<hr class="m-0 mt-1">';
                                $returnValue .= '<text title="'.$selectQuery->email.'" class="small text-primary" style="font-size:12px;"><span class="fas fa-envelope"></span>: <b>'.$selectQuery->email.'</b></text>';
                                return $returnValue;
                            })
                            ->editColumn('contact_number', function($selectQuery) {
                                $contact_numbers = explode(',',$selectQuery->contact_number);
                                $returnValue = '';
                                if($contact_numbers){
                                    foreach($contact_numbers as $contact_number){
                                        $returnValue .='<span title="'.$contact_number.'" class="badge badge-primary">'.$contact_number.'</span> &nbsp;';
                                    }
                                }
                                return $returnValue;
                            })
                            ->smart(true)
                            ->escapeColumns([])
                            ->addIndexColumn()
                            ->make(true);
            }
            elseif($postMode == 'fetch-provinces') {
                $region_id = encryptor('decrypt', $data['id']);
                $resultHtml = '';
                $selectProvincesQuery = Province::where([['region_id', '=', $region_id], ['is_enable','=',1]])
                                                    ->orderBy('description', 'ASC')
                                                    ->get();
                if($selectProvincesQuery) {
                    foreach($selectProvincesQuery as $province) {
                        $enc_province_id = encryptor('encrypt', $province->id);
                        $resultHtml .= '<option value="'.$enc_province_id.'">'.$province->description.'</option>';
                    }
                }
                return $resultHtml;
            }
            elseif($postMode == 'fetch-cities') {
                $province_id = encryptor('decrypt', $data['id']);
                $resultHtml = '';
                $selectCitiesQuery = City::where([['province_id', '=', $province_id], ['is_enable','=',1]])
                                            ->orderBy('city_name', 'ASC')
                                            ->get();
                if($selectCitiesQuery) {
                    foreach($selectCitiesQuery as $city) {
                        $enc_city_id = encryptor('encrypt', $city->id);
                        $resultHtml .= '<option value="'.$enc_city_id.'">'.$city->city_name.'</option>';
                    }
                }
                return $resultHtml;
            }
            elseif($postMode == 'fetch-barangays') {
                $city_id = encryptor('decrypt', $data['id']);
                $resultHtml = '';
                $selectBarangaysQuery = Barangay::where([['city_id', '=', $city_id], ['is_enable','=',1]])
                                            ->orderBy('barangay_description', 'ASC')
                                            ->get();
                if($selectBarangaysQuery) {
                    foreach($selectBarangaysQuery as $barangay) {
                        $enc_barangay_id = encryptor('encrypt', $barangay->id);
                        $resultHtml .= '<option value="'.$enc_barangay_id.'">'.$barangay->barangay_description.'</option>';
                    }
                }
                return $resultHtml;
            }
            elseif($postMode == 'logs-suppliers-details'){
                $enc_supplier_id = $data['key'];
                $supplier_id = encryptor('decrypt', $enc_supplier_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Supplier')
                                ->where('auditable_id','=',$supplier_id)
                                ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('event', function($selectQuery) use($user) {
                        $returnValue = strToUpper($selectQuery->event);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= readableDate($selectQuery->created_at);
                        return $returnValue;
                    })
                    ->editColumn('old_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->old_values,true);
                        $returnValue = '<table class="small table table-small m-0 p-0"> ';
                            if(count($values) > 0){
                                foreach($values as $key=>$value){
                                    $returnValue .= '<tr>';
                                        $returnValue .= '<td class="p-0 m-0" >'.$key.'</td>';
                                        $returnValue .= '<td class="p-0 m-0">'.$value.'</td>';
                                    $returnValue .= '</tr>';
                                }
                            }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->editColumn('new_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->new_values,true);
                        $returnValue = '<table class="small table"> ';
                            if(count($values) > 0){
                                foreach($values as $key=>$value){
                                    $returnValue .= '<tr>';
                                        $returnValue .= '<td>'.$key.'</td>';
                                        $returnValue .= '<td>'.$value.'</td>';
                                    $returnValue .= '</tr>';
                                }
                            }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->addColumn('source_model', function($selectQuery) use($user) {
                        $returnValue = $selectQuery->user->username;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= $selectQuery->auditable_type;
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            elseif($postMode == 'logs-supplier-products-details'){
                $enc_supplier_product_id = $data['key'];
                $supplier_product_id = encryptor('decrypt', $enc_supplier_product_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\SupplierProduct')
                                ->where('auditable_id','=',$supplier_product_id)
                                ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('event', function($selectQuery) use($user) {
                        $returnValue = strToUpper($selectQuery->event);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= readableDate($selectQuery->created_at);
                        return $returnValue;
                    })
                    ->editColumn('old_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->old_values,true);
                        $returnValue = '<table class="small table table-small m-0 p-0"> ';
                            if(count($values) > 0){
                                foreach($values as $key=>$value){
                                    $returnValue .= '<tr>';
                                        $returnValue .= '<td class="p-0 m-0" >'.$key.'</td>';
                                        $returnValue .= '<td class="p-0 m-0">'.$value.'</td>';
                                    $returnValue .= '</tr>';
                                }
                            }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->editColumn('new_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->new_values,true);
                        $returnValue = '<table class="small table"> ';
                            if(count($values) > 0){
                                foreach($values as $key=>$value){
                                    $returnValue .= '<tr>';
                                        $returnValue .= '<td>'.$key.'</td>';
                                        $returnValue .= '<td>'.$value.'</td>';
                                    $returnValue .= '</tr>';
                                }
                            }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->addColumn('source_model', function($selectQuery) use($user) {
                        $returnValue = $selectQuery->user->username;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= $selectQuery->auditable_type;
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            else {
                return array('success' => 0, 'message' => 'Undefined Method');
            }

        } else {
            if($postMode == 'update-supplier-product'){
                $supplier_product_id = encryptor('decrypt',$data['supplier_product_id']);
                $attributes = [
                    'supplier_code' => 'Supplier Code',
                    'price' => 'Price',
                ];
                $rules = [
                    'supplier_code' => 'required|unique:supplier_products,code,'.$supplier_product_id.',id',
                    'price' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateQuery = SupplierProduct::find($supplier_product_id);
                    $updateQuery->code = trim($data['supplier_code']);
                    $updateQuery->price = trim($data['price']);
                    $updateQuery->note = trim($data['note']);
                    if($updateQuery->save()){
                        Session::flash('success',1);
                        Session::flash('message','Product Updated');
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to update product. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-product'){
                $supplier_id = encryptor('decrypt',$data['supplier']);
                $product_id = encryptor('decrypt',$data['product']);
                $attributes = [
                    'supplier' => 'Supplier',
                    'product' => 'Product',
                    'supplier_code' => 'Supplier Code',
                    'price' => 'Price',
                ];
                $rules = [
                    'supplier' => 'required',
                    'product' => 'required',
                    'supplier_code' => 'required|unique:supplier_products,code,NULL,id',
                    'price' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $selectQuery = SupplierProduct::where('supplier_id','=',$supplier_id)
                                        ->where('product_id','=',$product_id)
                                        ->first();
                    if(!$selectQuery){
                        $insertQuery = new SupplierProduct();
                        $insertQuery->supplier_id = $supplier_id;
                        $insertQuery->code = $data['supplier_code'];
                        $insertQuery->product_id = $product_id;
                        $insertQuery->note = $data['note'];
                        $insertQuery->type = $data['type'];
                        $insertQuery->price = $data['price'];
                        $insertQuery->created_at = getDatetimeNow();
                        $insertQuery->updated_at = getDatetimeNow();
                        $insertQuery->updated_by = $user->id;
                        $insertQuery->created_by = $user->id;
                        if($insertQuery->save()){
                            Session::flash('success',1);
                            Session::flash('message','Product Added');
                            return back();
                        }else{
                            Session::flash('success',0);
                            Session::flash('message','Unable to add product. Please try again');
                            return back();
                        }
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Product is already added in this supplier');
                        return back();
                    }
                }
            }
            elseif($postMode == 'add-suppliers') {
                $attributes = [
                    'supplier-name' => 'Supplier Name',
                    'supplier-code' => 'Supplier Code',
                    'select-category' => 'Category',
                    'supplier-contact-person' => 'Contact Person',
                    'supplier-email' => 'Email',
                    'select-region' => 'Region',
                    'select-city' => 'City',
                    'select-barangay' => 'Barangay',
                    'select-province' => 'Province',
                    'supplier-tin-number' => 'TIN Number',
                    'select-industry' => 'Industry',
                    'supplier-contact-number' => 'Contact Number',
                    'select-vat' => 'VAT Type',
                    'supplier-complete-address' => 'Complete Address',
                    'supplier-payment-type' => 'Payment Type',
                ];
                $rules = [
                    'supplier-name' => 'required|unique:suppliers,name,NULL,id,archive,0',
                    'supplier-code' => 'required|unique:suppliers,code,NULL,id',
                    'select-category' => 'required',
                    'supplier-contact-person' => 'required',
                    'supplier-email' => 'required',
                    'select-region' => 'required',
                    'select-city' => 'required',
                    'select-barangay' => 'required',
                    'select-province' => 'required',
                    'supplier-tin-number' => 'required',
                    'select-industry' => 'required',
                    'supplier-contact-number' => 'required',
                    'select-vat' => 'required',
                    'supplier-complete-address' => 'required',
                    'supplier-payment-type' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $province_id = encryptor('decrypt', $data['select-province']);
                    $city_id = encryptor('decrypt', $data['select-city']);
                    $barangay_id = encryptor('decrypt', $data['select-barangay']);
                    if($data['supplier-payment-term'] == 'none'){
                        $supplier_payment_terms = NULL;
                    } else {
                        $supplier_payment_terms = $data['supplier-payment-term'];
                    }
                    $insertSupplierQuery = new Supplier();
                    $insertSupplierQuery->name = trim($data['supplier-name']);
                    $insertSupplierQuery->code = trim($data['supplier-code']);
                    $insertSupplierQuery->department_id = $user->department_id;
                    $insertSupplierQuery->industry_id = $data['select-industry'];
                    $insertSupplierQuery->category = $data['select-category'];
                    $insertSupplierQuery->tin_number = trim($data['supplier-tin-number']);
                    $insertSupplierQuery->vatable = $data['select-vat'];
                    $insertSupplierQuery->contact_person = trim($data['supplier-contact-person']);
                    $insertSupplierQuery->contact_number = $data['supplier-contact-number'];
                    $insertSupplierQuery->barangay_id = $barangay_id;
                    $insertSupplierQuery->city_id = $city_id;
                    $insertSupplierQuery->province_id = $province_id;
                    $insertSupplierQuery->email = $data['supplier-email'];
                    $insertSupplierQuery->complete_address = trim($data['supplier-complete-address']);
                    $insertSupplierQuery->payment_terms = $supplier_payment_terms;
                    $insertSupplierQuery->payment_type = $data['supplier-payment-type'];
                    $insertSupplierQuery->remarks = trim($data['supplier-remarks']);
                    $insertSupplierQuery->created_by = $user->id;
                    $insertSupplierQuery->updated_by = $user->id;
                    $insertSupplierQuery->created_at = getDatetimeNow();
                    $insertSupplierQuery->updated_at = getDatetimeNow();
                    if($insertSupplierQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Supplier Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add supplier. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-suppliers') {
                $id = encryptor('decrypt', $data['supplier-id']);
                $industry_id = encryptor('decrypt', $data['select-industry-update']);
                $province_id = encryptor('decrypt', $data['select-province-update']);
                $city_id = encryptor('decrypt', $data['select-city-update']);
                $barangay_id = encryptor('decrypt', $data['select-barangay-update']);
                if($data['supplier-payment-term-update'] == 'none'){
                    $supplier_payment_terms = NULL;
                } else {
                    $supplier_payment_terms = $data['supplier-payment-term-update'];
                }
                $attributes = [
                    'supplier-name-update' => 'Supplier Name',
                    'supplier-code-update' => 'Supplier Code',
                    'select-category-update' => 'Category',
                    'supplier-contact-person-update' => 'Contact Person',
                    'supplier-email-update' => 'Email',
                    'select-region-update' => 'Region',
                    'select-city-update' => 'City',
                    'select-barangay-update' => 'Barangay',
                    'select-province-update' => 'Province',
                    'supplier-tin-number-update' => 'TIN Number',
                    'select-industry-update' => 'Industry',
                    'supplier-contact-number-update' => 'Contact Number',
                    'select-vat-update' => 'VAT Type',
                    'supplier-complete-address-update' => 'Complete Address',
                    'supplier-payment-type-update' => 'Payment Type',
                ];
                $rules = [
                    'supplier-name-update' => 'required|unique:suppliers,name,'.$id.',id,archive,0',
                    'supplier-code-update' => 'required|unique:suppliers,code,'.$id.',id',
                    'select-category-update' => 'required',
                    'supplier-contact-person-update' => 'required',
                    'supplier-email-update' => 'required',
                    'select-region-update' => 'required',
                    'select-city-update' => 'required',
                    'select-barangay-update' => 'required',
                    'select-province-update' => 'required',
                    'supplier-tin-number-update' => 'required',
                    'select-industry-update' => 'required',
                    'supplier-contact-number-update' => 'required',
                    'select-vat-update' => 'required',
                    'supplier-complete-address-update' => 'required',
                    'supplier-payment-type-update' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateSupplierQuery = Supplier::where('id', '=', $id)->first();
                    $updateSupplierQuery->name = trim($data['supplier-name-update']);
                    $updateSupplierQuery->code = trim($data['supplier-code-update']);
                    $updateSupplierQuery->industry_id = $industry_id;
                    $updateSupplierQuery->category = $data['select-category-update'];
                    $updateSupplierQuery->tin_number = trim($data['supplier-tin-number-update']);
                    $updateSupplierQuery->vatable = $data['select-vat-update'];
                    $updateSupplierQuery->contact_person = trim($data['supplier-contact-person-update']);
                    $updateSupplierQuery->contact_number = $data['supplier-contact-number-update'];
                    $updateSupplierQuery->barangay_id = $barangay_id;
                    $updateSupplierQuery->city_id = $city_id;
                    $updateSupplierQuery->province_id = $province_id;
                    $updateSupplierQuery->email = $data['supplier-email-update'];
                    $updateSupplierQuery->complete_address = trim($data['supplier-complete-address-update']);
                    $updateSupplierQuery->payment_terms = $supplier_payment_terms;
                    $updateSupplierQuery->payment_type = $data['supplier-payment-type-update'];
                    $updateSupplierQuery->remarks = trim($data['supplier-remarks-update']);
                    $updateSupplierQuery->updated_by = $user->id;
                    $updateSupplierQuery->updated_at = getDatetimeNow();
                    if($updateSupplierQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update supplier. Please try again');
                    }
                }

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
