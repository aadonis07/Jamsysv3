<?php

namespace App\Http\Controllers\PurchasingSupply;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use File;
use Validator;
use Session;
use App\Client;
use App\Region;
use App\Province;
use App\City;
use App\CompanyBranch;
use App\Product;
use App\Swatch;
use App\Attribute;
use App\Quotation;
use App\QuotationProduct;
use App\Agent;
use App\Bank;
use App\Barangay;
use App\QuotationTerm;
class QuotationController extends Controller
{
    
    public function showIndex(){

        return view('purchasing-supply-department.quotations.list')
             ->with('admin_menu','QUOTATION')
             ->with('admin_sub_menu','LIST-QUOTATION');
    }
    
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode=='quotation-list-serverside'){
                $selectQuery = Quotation::with('client')->with('job_request')->where('status','=',$data['status'])
                                ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('created_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small><hr class="m-0">';
            
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('quote_number', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->quote_number.' <small class="text-info">('.$selectQuery->work_nature.')</small>';
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<b class="text-primary">subject :</b> '.$selectQuery->subject;
                    $returnHTml .= ' | <b>Products</b> : <span class="badge bg-fusion-500 ml-2">'.quotationProductCount($selectQuery->id).'</span>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->client->name;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<label class="text-primary"> Contact Number : </label>'.$selectQuery->client->contact_numbers;
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('grand_total', function($selectQuery) use($user) {
                    $returnHTml = '<div align="right">PHP '.number_format($selectQuery->grand_total,2);
                    $returnHTml .= '<hr class="m-0">';
                    if(!empty($selectQuery->total_discount)){
                        $returnHTml .= '<b class="text-danger">Discount : </b> PHP'.number_format($selectQuery->total_discount,2);
                    }
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-quotation" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="View Quotation">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                       
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='quotation-products-serverside'){
                $selectQuery = QuotationProduct::with('product')->with('update_fitout_products')->whereNull('parent_id')->where('quotation_id','=',$data['id'])
                                ->orderBy('order','ASC');
                return Datatables::eloquent($selectQuery)
                ->addColumn('image', function($selectQuery) use($user) {
                    $returnHtml = '';
                    $enc_product_id = encryptor('encrypt',$selectQuery->product_id); 
                    $defaultLink = 'no-img';
                    $destination  = 'assets/img/products/'.$enc_product_id.'/';
                    $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                    if($defaultLink=='no-img'){
                        $enc_product_id = encryptor('encrypt',$selectQuery->product->parent_id); 
                        $defaultLink = 'http://placehold.it/754x400';
                        $destination  = 'assets/img/products/'.$enc_product_id.'/';
                        $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                    }
                    $returnHtml = '<div align="center"><img src="'.$defaultLink.'" style="width:100px;height:100px;" /></div>';
                    $returnHtml .= '<input class="form-control" type="hidden" value="'.encryptor('encrypt',$selectQuery->id).'" name="product_id[]" />';
                    return $returnHtml;
                })
                ->editColumn('qty', function($selectQuery) use($user) {
                    $mode = '';
                    if($selectQuery->remarks == 'DELETED'){
                        $mode = 'readonly';
                    }
                    $returnHtml = '<div align="center"><input name="product_qty[]" '.$mode.' id="qty'.$selectQuery->order.'" data-order="'.$selectQuery->order.'" maxlength="6" style="text-align:center;" class="form-control" placeholder="QTY" value="'.$selectQuery->qty.'" /></div>';
                   
                    return $returnHtml;
                })
                ->editColumn('base_price', function($selectQuery) use($user) {
                    $mode = '';
                    if($selectQuery->remarks == 'DELETED'){
                        $mode = 'readonly';
                    }
                    $returnHtml = '<div align="center"><input name="product_price[]" '.$mode.' id="price'.$selectQuery->order.'" data-order="'.$selectQuery->order.'" style="text-align:right;" value="'.number_format((float)$selectQuery->base_price, 2, '.', '').'" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Based Price" /></div>';

                    return $returnHtml;
                })
                ->editColumn('discount', function($selectQuery) use($user) {
                    $discount = 0;
                    if(!empty($selectQuery->discount)){
                        $discount = $selectQuery->discount;
                    }
                    $mode = '';
                    if($selectQuery->remarks == 'DELETED'){
                        $mode = 'readonly';
                    }
                    $returnHtml = '<div align="center"><input name="product_discount[]" '.$mode.' id="discount'.$selectQuery->order.'" data-order="'.$selectQuery->order.'" style="text-align:right;" value="'.number_format((float)$discount, 2, '.', '').'" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Discount" /></div>';

                    return $returnHtml;
                })
                ->editColumn('description', function($selectQuery) use($user) {
                    $returnHtml = '';
                    if(!empty($selectQuery->product->parent_id)){
                        $product_variants = str_replace('|','<br>',$selectQuery->product->product_name);
                        $returnHtml .= $product_variants;
                    }
                    if($selectQuery->type=='FIT-OUT'){
                        foreach($selectQuery->update_fitout_products as $fitout){
                            $product_variants = str_replace('v:','</b><br>',$fitout->product_name);
                            $product_variants = str_replace('|','<br>',$product_variants);
                            $returnHtml .= '<b>• '.$product_variants.'<br>';
                        }
                    }
                    $returnHtml .= $selectQuery->description;

                    return $returnHtml;
                })
                ->editColumn('total_amount', function($selectQuery) use($user) {
                    $total_amount = floatval($selectQuery->total_price)-floatval($selectQuery->discount);
                    $returnHtml = '<div align="center"><input id="itotal'.$selectQuery->order.'" style="text-align:right;" value="'.number_format((float)$total_amount, 2).'" onkeypress="return isNumberKey(event)" class="form-control" placeholder="0.00" readonly/></div>';
                    $returnHtml .= '<input name="product_total[]" type="hidden" id="total'.$selectQuery->order.'" style="text-align:right;" value="'.number_format((float)$selectQuery->total_amount, 2, '.', '').'" onkeypress="return isNumberKey(event)" class="form-control" placeholder="0.00" readonly/>';
                    return $returnHtml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    if($selectQuery->remarks == 'DELETED'){
                        $returnHtml .= '<a class="btn btn-xs btn-info waves-effect revert-product" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-undo text-white"></span></a> ';
                    }else{
                        $returnHtml .= '<a class="btn btn-xs btn-danger waves-effect cancel-product" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-times text-white"></span></a> ';
                    }
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->setRowClass(function ($selectQuery) {
                    if($selectQuery->remarks == 'DELETED'){
                        return 'alert-danger';
                    }
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode=='action-quotation'){
                $id = encryptor('decrypt',$data['quotationId']);
                $selectQuery = Quotation::where('id','=',$id)->with('terms')->with('barangay')->with('client')->with('products')->with('province')->with('city')->first();
                $vat_types = array(
                    'INCLUSIVE'=>'VAT-INC',
                    'ZERO-RATED'=>'Zero Rated',
                    'SALES-TO-GOVERNMENT'=>'Sales To Government',
                    'EXEMPT-SALES-RECEIPT'=>'Exempt Sales Receipt'
                );
                $payment_terms = QuotationTerm::all();
                $delivery_modes = array(
                    'PICK-UP'=>'Pick-up',
                    'DELIVER'=>'Deliver'
                );
                if($data['actionMode']=='view'){
                    $payment_modes = array(
                        'CASH'=>'Cash',
                        'ONLINE'=>'Online',
                        'CHECK'=>'Check',
                        'CASH-ON-DELIVERY'=>'Cash On Delivery',
                        'TERMS'=>'Terms'
                    );
                    $banks = Bank::all();
                    return view('purchasing-supply-department.quotations.view')
                         ->with('quotation',$selectQuery)
                         ->with('sub_to',$selectQuery->sub_total)
                         ->with('payment_modes',$payment_modes)
                         ->with('vat_types',$vat_types)
                         ->with('banks',$banks)
                         ->with('payment_terms',$payment_terms)
                         ->with('delivery_modes',$delivery_modes);
                }elseif($data['actionMode']=='update'){
                    $work_nature = [
                        'FURNITURE'=>'Furniture',
                        'FIT-OUT'=>'Fit-out'
                    ];
                    $roles=[
                        "GENERAL-CONTRACTOR"=>"General Contractor",
                        "SUB-CONTRACTOR"=>"Sub-con",
                        "SUPPLIER"=>"Supplier"
                    ];
                    $clients = Client::where('user_id','=',$user->id)->get();
                    $regions = showRegions();
                    $selectQuery = Quotation::where('id','=',$id)->with('terms')->with('barangay')->with('client')->with('update_products')->with('province')->with('city')->first();
                    return view('purchasing-supply-department.quotations.update')
                         ->with('work_nature',$work_nature)
                         ->with('roles',$roles)
                         ->with('delivery_modes',$delivery_modes)
                         ->with('clients',$clients)
                         ->with('regions',$regions)
                         ->with('sub_to',$selectQuery->sub_total)
                         ->with('vat_types',$vat_types)
                         ->with('payment_terms',$payment_terms)
                         ->with('quotation',$selectQuery);
                }else{
                    Session::flash('success', 0);
                    Session::flash('message', 'Undefined method please try again');
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
