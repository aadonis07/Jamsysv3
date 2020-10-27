<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use File;
use Validator;
use Session;
use Hash;
use Crypt;
use DB;
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
use App\QuotationTerm;
use App\Agent;
use App\Bank;
use App\Barangay;

class QuotationController extends Controller
{
    public function create(){
        $work_nature = [
            'FURNITURE'=>'Furniture',
            'FIT-OUT'=>'Fit-out'
        ];
        $roles=[
            "GENERAL-CONTRACTOR"=>"General Contractor",
            "SUB-CONTRACTOR"=>"Sub-con",
            "SUPPLIER"=>"Supplier"
        ];
        $delivery_modes=[
            'PICK-UP'=>'Pick-up',
            'DELIVER'=>'Deliver'
        ];
        $vat_types = array(
            'VAT-INC'=>'VAT-INC',
            'ZERO-RATED'=>'Zero Rated',
            'SALES-TO-GOVERNMENT'=>'Sales To Government',
            'EXEMPT-SALES-RECEIPT'=>'Exempt Sales Reciept'
        );
        $warranties = array(
            'No'=>'No Warranty',
            'One (1) Month'=>'1 Month',
            'Two (2) Months'=>'2 Months',
            'Three (3) Months'=>'3 Months',
            'Four (4) Months'=>'4 Months',
            'Five (5) Months'=>'5 Months',
            'Six (6) Months'=>'6 Months',
            'Seven (7) Months'=>'7 Months',
            'Eight (8) Months'=>'8 Months',
            'Nine (9) Months'=>'9 Months',
            'Ten (10) Months'=>'10 Months',
            'Eleven (11) Months'=>'11 Months',
            'One (1) Year'=>'1 Year',
            'One (2) Years'=>'2 Years'
        );
        $payment_terms = QuotationTerm::all();
       
        $user = Auth::user();
        $clients = Client::where('user_id','=',$user->id)->get();
        $regions = showRegions();
        $dateToday = date("Hymds");
        $milliseconds = round(microtime(true) * 1000);
        $newstring = substr($milliseconds, -3);
        $quote_number = $newstring . '' . $dateToday;
        $destination = 'assets/files/quotation_terms/';
        $filename = 'terms';
        $terms = toTxtFile($destination,$filename,'get');
        
        return view('it-department.quotations.create')
             ->with('admin_menu','QUOTATION')
             ->with('admin_sub_menu','CREATE-QUOTATION')
             ->with('work_nature',$work_nature)
             ->with('regions', $regions)
             ->with('roles',$roles)
             ->with('clients',$clients)
             ->with('user',$user)
             ->with('warranties',$warranties)
             ->with('delivery_modes',$delivery_modes)
             ->with('quote_number',$quote_number)
             ->with('vat_types',$vat_types)
             ->with('payment_terms',$payment_terms)
             ->with('terms',$terms['data']);
    }
    
    public function list(){

        return view('it-department.quotations.list')
             ->with('admin_menu','QUOTATION')
             ->with('admin_sub_menu','LIST-QUOTATION');
    }
    function quotationProducts(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $products = array();
        $generatedSavedPoint = encryptor('encrypt',$user->id);
        $destination = 'assets/files/quotations/'.$generatedSavedPoint.'/';
        $pfilename = 'quotation-product-information';
        $product_info = toTxtFile($destination,$pfilename,'get');
        if($product_info['success'] === true){
            $datasproduct = $product_info['data'];
            $products = json_decode($datasproduct);
        }
        $qfilename = 'quotation-information';
        $quotation_info = toTxtFile($destination,$qfilename,'get');
        if($quotation_info['success'] === true){
            $datas = $quotation_info['data'];
            $datas = json_decode($datas);

        }
        $quotation_products = '';
        if(count($products)==0){
            $quotation_products .= '<tr>';
            $quotation_products .= '<td align="center" colspan="7">No Product Available</td>';
            $quotation_products .= '</tr>';
        }else{
            $count =0;
            $subtotal = 0;
            foreach($products as $product){
                $count++;
                $product_price = $product->price;
                $product_qty = $product->qty;
                $total_price = $product_price*$product_qty;
                $subtotal = $subtotal+$total_price;
                $discount =0;
                if(!empty($product->discount)){
                    $discount = $product->discount;
                }
                $total_pricesssss = $total_price-$discount;
                $prodct_json = json_encode($product);
                $variant_details = '';
                if($product->product_type!='RAW'){
                    if($product->product_type!='SPECIAL-ITEM'){
                        if($product->product_type=='FIT-OUT'){
                            $variant_details = '';
                            $variant_id = $product->variant_id;
                            $variant_name = $product->variant_name;
                            $variant_qty = $product->variant_qty;
                            
                            $variant_type = $product->variant_type;
                            $de_variant_type = json_encode($variant_type);
                            
                            $de_variant_id = json_encode($variant_id);
                            $de_variant_name = json_encode($variant_name);
                            $de_variant_qty = json_encode($variant_qty);
                            
                            for($i=0;$i<count($variant_id);$i++){
                                if(!empty($variant_qty[$i])){
                                $variant_name_temp = str_replace("|","<bR>",$variant_name[$i]);
                                $variant_name_temp = str_replace("v:","</b>(QTY: <b class='text-danger'>".$variant_qty[$i]."</b>)<bR>",$variant_name_temp);
                                $variant_details .= '<b class="text-primary">• '.$variant_name_temp.'<br>';
                                }
                            }

                            $variant_details .= '<input class="form-control" name="variant_id[]" value="'.e($de_variant_id ).'" type="hidden" />
                                <input class="form-control" name="variants_data[]" value="'.e($de_variant_name ).'" type="hidden" />
                                <input class="form-control" name="variant_qty[]" value="'.e($de_variant_qty).'" type="hidden" />
                                <input class="form-control" name="variant_type[]" value="'.e($de_variant_type).'" type="hidden" />';
                        }else{
                            $variant_details = str_replace("|","<bR>",$product->variant_name);
                        }
                    }
                }

                $description = '';
                $proddes = '';
                if(!empty($product->description)&&$product->description!=''){
                    $description = "<br><b>Other description :</b><br>".$product->description;
                    $proddes = $product->description;
                    if($description=='<br><b>Other description :</b><br><div><\/div>'||$product->description=='<div></div>'){
                        $description = '';
                        $proddes = '';
                    }
                }else{
                    $proddes = '';
                }
                if($product->product_type=='FIT-OUT'){
                $quotation_products .= "<tr id='".e($prodct_json)."' class='qprod_order productId".$product->order."'>";
                    $quotation_products .= '<td align="center">'.$count.' <input class="form-control" name="order_tbl[]" value="'.$count.'" type="hidden" /></td>';
                    $quotation_products .= '<td align="center">'.$product->product_id.'<br>
                                                <label class="text-info">('.$product->product_type.')</label>
                                                <input class="form-control" name="fitout_id[]" value="'.$product->fitout_id.'" type="hidden" />
                                                <input class="form-control" name="variant_name[]" value="'.$product->product_id.'" type="hidden" />
                                                
                                                <input class="form-control" name="product_type[]" value="'.$product->product_type.'" type="hidden" />
                                                <input class="form-control" name="order[]" value="'.$product->order.'" type="hidden" />
                                                <input class="form-control" name="swatches[]" value="'.$product->swatches.'" type="hidden" />
                                                </td>';
                    $quotation_products .= '<td>
                                            '.$variant_details.'
                                            '.$description.'
                                            <textarea name="description[]" style="display:none;">'.$proddes.'</textarea>
                                            </td>';
                    $quotation_products .= '<td align="center"><input class="form-control" style="text-align:center;" name="product_qty[]" onkeypress="return isNumberKey(event)" maxlength="6" placeholder="QTY" data-id="'.$product->order.'" id="qty'.$product->order.'" value="'.$product->qty.'"/></td>';
                    $quotation_products .= '<td align="right">
                                                <div class="input-group mar-btm">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" disabled> Php</button> 
                                                    </span> 
                                                <input class="form-control" style="text-align:right;" onkeypress="return isNumberKey(event)" name="product_list_price[]" id="plc'.$product->order.'" data-order="'.$product->order.'" value="'.$product->price.'" />
                                                </div>
                                            </td>';
                    $quotation_products .= '<td align="right">
                                                <div class="input-group mar-btm">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" disabled> Php</button> 
                                                    </span> 
                                                    <input class="form-control" name="discountprod[]" id="dis'.$product->order.'" style="text-align:right;" value="'.$product->discount.'" onkeypress="return isNumberKey(event)" data-order="'.$product->order.'" maxlength="10" placeholder="discount ex.100 "/>
                                                </div>
                                                </td>';
                    $quotation_products .= '<td align="right">PHP <span id="total_product_price'.$product->order.'">'.number_format($total_pricesssss,2).'</span> <input class="form-control" type="hidden" id="tpc'.$product->order.'" name="total_product_price[]" value="'.$total_pricesssss.'" /></td>';
                    $quotation_products .= '<td align="center">
                                                <a class="btn btn-sm btn-danger text-white delete-product" data-productId="'.$product->order.'"><span class="fa fa-times"></span></a>
                                            </td>';
                $quotation_products .= ' </tr>';
                }else{
                    $variant_detail_info = '';
                    if($product->product_type!='RAW'&&$product->product_type!='SPECIAL-ITEM'){
                        $variant_detail_info = '
                        <input class="form-control" name="variant_qty[]" value="" type="hidden" />
                        <input class="form-control" name="variant_type[]" value="" type="hidden" />
                                         <input class="form-control" name="variants_data[]" value="'.$product->variant_name.'" type="hidden" />';
                    }else{
                        $variant_detail_info = '
                                        <input class="form-control" name="variant_qty[]" value="" type="hidden" />
                                        <input class="form-control" name="variant_type[]" value="" type="hidden" />
                                         <input class="form-control" name="variants_data[]" value="" type="hidden" />';
                    }

                $quotation_products .= "<tr id='".e($prodct_json)."' class='qprod_order productId".$product->order."'>";
                    $quotation_products .= '<td align="center">'.$count.' <input class="form-control" name="order_tbl[]" value="'.$count.'" type="hidden" /></td>';
                    $quotation_products .= '<td align="center">'.$product->product_id.'<br>
                                                <label class="text-info">('.$product->product_type.')</label>
                                                '.$variant_detail_info.'
                                                <input class="form-control" name="variant_id[]" value="'.$product->variant_id.'" type="hidden" />
                                                <input class="form-control" name="fitout_id[]" type="hidden" />
                                                <input class="form-control" name="variant_name[]" value="'.$product->product_id.'" type="hidden" />
                                                <input class="form-control" name="product_type[]" value="'.$product->product_type.'" type="hidden" />
                                                <input class="form-control" name="order[]" value="'.$product->order.'" type="hidden" />
                                                <input class="form-control" name="swatches[]" value="'.$product->swatches.'" type="hidden" />
                                                </td>';
                    $quotation_products .= '<td>
                                            '.$variant_details.'
                                            '.$description.'
                                            <textarea name="description[]" style="display:none;">'.$proddes.'</textarea>
                                            </td>';
                    $quotation_products .= '<td align="center"><input class="form-control" style="text-align:center;" name="product_qty[]" onkeypress="return isNumberKey(event)" maxlength="6" placeholder="QTY" data-id="'.$product->order.'" id="qty'.$product->order.'" value="'.$product->qty.'"/></td>';
                    $quotation_products .= '<td align="right">
                                                <div class="input-group mar-btm">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" disabled> Php</button> 
                                                    </span> 
                                                <input class="form-control" style="text-align:right;" onkeypress="return isNumberKey(event)" name="product_list_price[]" id="plc'.$product->order.'" data-order="'.$product->order.'" value="'.$product->price.'" />
                                                </div>
                                            </td>';
                    $quotation_products .= '<td align="right">
                                                <div class="input-group mar-btm">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" disabled> Php</button> 
                                                    </span> 
                                                    <input class="form-control" name="discountprod[]" id="dis'.$product->order.'" style="text-align:right;" value="'.$product->discount.'" onkeypress="return isNumberKey(event)" data-order="'.$product->order.'" maxlength="10" placeholder="discount ex.100 "/>
                                                </div>
                                                </td>';
                    $quotation_products .= '<td align="right">PHP <span id="total_product_price'.$product->order.'">'.number_format($total_pricesssss,2).'</span> <input class="form-control" type="hidden" id="tpc'.$product->order.'" name="total_product_price[]" value="'.$total_pricesssss.'" /></td>';
                    $quotation_products .= '<td align="center">
                                                <a class="btn btn-sm btn-danger text-white delete-product" data-productId="'.$product->order.'"><span class="fa fa-times"></span></a>
                                            </td>';
                $quotation_products .= ' </tr>';
                }
            }
        
            $quotation_products .= '<input type="hidden" class="form-control" id="temp_sub" value="'.$subtotal.'" />';
        }
        
        $returnHml = $quotation_products;

        return $returnHml;
    }
    function quotationPreview(Request $request){
        $data = $request->all();
        $user = Auth::user();
        return view('it-department.quotations.preview.quotation_preview')->with('user',$user);
    }
    function jobrequestProductList(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $returnHtml = '';
        $dec_id = encryptor('decrypt', $data['id']);
        if(isset($dec_id)){
            $products = QuotationProduct::with('product')->where('quotation_id','=',$dec_id)
                                        ->where('is_jr','=',0)
                                        ->whereIn('type',['COMBINATION','FIT-OUT','CUSTOMIZED','SUPPLY'])->whereNull('cancelled_date')
                                        ->get();
            if(count($products)==0){
                $returnHtml = '<div class="form-group" align="center"><h1>Please check your product type. only combination, customized and fit-out type can request for designers</h1></div>';
            }else{
                $product_content = '';
                $count=0;
               
                foreach($products as $product){
                    $count++;
                    $product_name = '';
                    $img = '';
                    $defaultLink = 'http://placehold.it/754x400';
                    if($product->product->parent_id==null){
                        $product_name = $product->product->product_name;
                        if($product->parent_id==null){
                            $product_enc = encryptor('encrypt',$product->product_id);
                            $destination  = 'assets/img/products/'.$product_enc.'/';
                            $defaultLink = imagePath($destination.''.$product_enc,$defaultLink);
                            $img = '<img src="'.$defaultLink.'" style="height:150px;width:150px;" />';
                        }else{
                            $img = '<b class="text-danger">FOR FITOUT </b>';
                        }
                    }else{
                        if($product->parent_id!=null){
                            $product_name_temp = $product->product_name;
                            $product_name_temp = str_replace("v:","</b><br>",$product_name_temp);
                            $product_name_temp = str_replace("|","<br>",$product_name_temp);
                            $product_name = '<b>'.$product_name_temp;
                            $img = '<b class="text-danger">FOR FITOUT </b>';
                        }else{
                            $prodData = fetchProduct($product->product->parent_id);
                            $product_name = $prodData->product_name;
                            $product_enc = encryptor('encrypt',$product->product->id);
                            $destination  = 'assets/img/products/'.$product_enc.'/';
                            $defaultLink = imagePath($destination.''.$product_enc,$defaultLink);
                            if($defaultLink=='http://placehold.it/754x400'){
                                $product_enc = encryptor('encrypt',$product->product->parent_id);
                                $destination  = 'assets/img/products/'.$product_enc.'/';
                                $defaultLink = imagePath($destination.''.$product_enc,$defaultLink);
                            }
                            $img = '<img src="'.$defaultLink.'" style="height:150px;width:150px;" />';
                        }
                    }
                    
                    $product_content .= '<tr>';
                        $product_content .= '<td align="center">'.$count.'</td>';
                        $product_content .= '<td align="center">'.$img.'<hr class="m-0"><b class="text-info">['.$product->type.']</b></td>';
                        $product_content .= '<td align="center">'.$product_name.'<br>'.$product->description.'</td>';
                        if($product->type=='SUPPLY'){
                            $product_content .= '<td align="center" class="jr_prods" id="count'.$product->id.'"> <input type="checkbox" class="form-control" name="jr_product[]" value="'.$product->id.'" /> Reupholster</td>';
                        }elseif($product->type=='FIT-OUT'){
                            $product_content .= '<td align="center" class="jr_prods" id="count'.$product->id.'"> <input type="checkbox" class="form-control" name="jr_product[]" value="'.$product->id.'" /></td>';
                        }else{
                            $product_content .= '<td align="center" class="jr_prods" id="count'.$product->id.'"> <input type="checkbox" onclick="return false;" name="jr_product[]" value="'.$product->id.'" class="form-control" checked readonly /></td>';
                        }
                    $product_content .= '</tr>';
                     
                }

                $returnHtml = '<div class="form-group">
                                <b class="text-danger">Please check if all you need to job request is here before you proceed.</b>
                                </div>
                                <div class="row">
                                <div class="table-responsive">
                                <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Request</th>
                                    </tr>
                                </thead>
                                ';
                    $returnHtml .= $product_content;
                    $returnHtml .= '</table>';
                    $returnHtml .= '</div>';
                $returnHtml .= '</div>';
            }            
        }else{
            $returnHtml = '<div class="form-group" align="center"><h1>No Product can be Requested for designers.</h1></div>';
        }

        return $returnHtml;
    }
    
    function quotationProcess(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $returnHtml = '';
        $id = encryptor('decrypt',$data['id']);
        if(isset($id)){
            $selectQuery = QuotationProduct::find($id);
            $for_job_request = '';
            if(!empty($selectQuery->job_request_product)){
                $revision_details = '';
                if(count($selectQuery->job_request_product->revisions)!=0){
                    foreach($selectQuery->job_request_product->revisions as $revision){
                        $designer = '<b class="text-danger">Designer Not Yet Assigned</b>';
                        if(!empty($revision->designer_name)){
                            $designer = 'DESIGNER ASSIGNED : '.$revision->designer_name;
                        }
                        $revision_details = '<ul class="">
                                                <li>'.$revision->jr_type->name.'<br>'.$designer.'<br>REVISION STATUS : '.$revision->status.'</li>
                                            </ul>';
                    }
                }else{
                    $revision_details = '<b class="text-danger">No Revisions</b>';
                }
                $for_job_request = '
                <tr>
                    <td>Job Request</td>
                    <td>'.$selectQuery->product_name.'</td>
                    <td>'.$revision_details.'</td>
                    <td>'.$selectQuery->job_request_product->status.'</td>
                    <td></td>
                </tr>
                ';
            }else{
                $for_job_request = '
                <tr class="bg-danger-50">
                    <td>Job Request</td>
                    <td colspan="4"><b class="text-danger">No Current Process in JR</b></td>
                </tr>
                ';
                if($selectQuery->type == 'FIT-OUT'){
                    $revision_details = '';
                    if(count($selectQuery->fitout_jr_items)!=0){
                        // return $selectQuery->fitout_jr_items;
                        foreach($selectQuery->fitout_jr_items as $jr_prod){
                            if(count($jr_prod->job_request_product->revisions)!=0){
                                foreach($jr_prod->job_request_product->revisions as $revision){
                                    $designer = '<b class="text-danger">Designer Not Yet Assigned</b>';
                                    if(!empty($revision->designer_name)){
                                        $designer = 'DESIGNER ASSIGNED : '.$revision->designer_name;
                                    }
                                    $revision_details = '<ul class="">
                                                            <li>'.$revision->jr_type->name.'<br>'.$designer.'<br>REVISION STATUS : '.$revision->status.'</li>
                                                        </ul>';
                                }
                            }else{
                                $revision_details = '<b class="text-danger">No Revisions</b>';
                            }
                            $for_job_request = '
                                                <tr>
                                                    <td>Job Request</td>
                                                    <td>'.$jr_prod->product_name.'</td>
                                                    <td>'.$revision_details.'</td>
                                                    <td>'.$jr_prod->job_request_product->status.'</td>
                                                    <td></td>
                                                </tr>
                                                ';
                        }
                    }else{
                        $for_job_request = '
                                            <tr class="bg-danger-50">
                                                <td>Job Request</td>
                                                <td colspan="4"><b class="text-danger">No Current Process in JR</b></td>
                                            </tr>
                                            ';
                    }
                }
               
            }
            $for_delivery = '';
            // if(!empty($selectQuery->delivered_qty)){
                $for_delivery = '
                <tr class="bg-danger-50">
                    <td>Delivery Schedule</td>
                    <td colspan="4"><b class="text-danger">Soon</b></td>
                </tr>
                ';
            // }
            $for_demo = '';
            // if($selectQuery->is_demo_product!=0){
                $for_demo = '
                <tr class="bg-danger-50">
                    <td>Demo Product</td>
                    <td colspan="4"><b class="text-danger">Soon</b></td>
                </tr>
                ';
            // }
            $for_purchase_order = '';
            // if($selectQuery->is_demo_product!=0){
                $for_purchase_order = '
                <tr class="bg-danger-50">
                    <td>Purchase Order</td>
                    <td colspan="4"><b class="text-danger">Soon</b></td>
                </tr>
                ';
            // }
            $returnHtml = '
            <div class="form-group">
                <b>All This Process Will Cancel. Please inform all department affected for this cancellation.</b>
            </div>
            <table class="table table-bordered">
                <thead class="bg-warning-500 text-center">
                    <tr>
                        <th>Process Module</th>
                        <th>Product Name</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Qty</th>
                    </tr>
                    
                </thead>
                <tbody class="text-center">
                '.$for_job_request.'
                '.$for_purchase_order.'
                '.$for_delivery.'
                '.$for_demo.'
                </tbody>
            ';
        }else{
            $returnHtml = '<div class="alert-danger" align="center">THIS IS AN ERROR PLEASE AS THE IT PERSONNEL.</div>';
        }
        return $returnHtml;
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
                    if(jrProductCount($selectQuery->id)!=0){
                        if(empty($selectQuery->job_request)){
                            $returnHTml .=  '<a class="btn btn-sm btn-info text-white job_request p-2" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-plus text-white"></span> Add Job Request</a>';
                        }else{
                            $returnHTml .=  '<div class="input-group">
                            <div class="input-group-prepend">
                                <a class="btn btn-info waves-effect waves-themed" href="'.route('job-request-view', ['id' => encryptor('encrypt',$selectQuery->job_request->id)]).'" ><i class="fal fa-search text-white"></i></a>
                            </div>
                            <input id="button-addon4" type="text" class="form-control" value="'.$selectQuery->job_request->jr_number.'" disabled>
                            <div class="input-group-prepend">
                                <a class="btn btn-success waves-effect waves-themed job_request" data-id="'.encryptor('encrypt',$selectQuery->id).'" ><i class="fa fa-plus text-white"></i></a>
                            </div>
                            </div>'; 
                        }
                    }
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-info btn-standard waves-effect rounded-circle mr-1 update-quotation" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Update Quotation">
                                            <i class="fal fa-pencil-alt"></i>
                                        </a>';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-white waves-effect rounded-circle mr-1 cancel-quotation" style="border-color:black;" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Cancel Quotation">
                                            <i class="fal fa-times"></i>
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
                        $is_demo = '';
                        if($selectQuery->is_demo_product==1){
                            $is_demo = 'disabled';
                        }
                        if($selectQuery->is_jr==0&&$selectQuery->delivered_qty==null&&$selectQuery->type!='FIT-OUT'){
                            $returnHtml .= '<a class="btn btn-xs btn-danger waves-effect '.$is_demo.' cancel-product" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-times text-white"></span></a> ';
                        }else{
                            $returnHtml .= '<a class="btn btn-xs btn-danger waves-effect '.$is_demo.' cancel-product-reason" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-times text-white"></span></a> ';
                        }
                        if($is_demo=='disabled'){
                            $returnHtml .= '<br><small class="text-danger">This product is on DEMO</small>';
                        }
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
            }elseif($postMode=='get-branches'){
                $selectQuery = Client::where('id','=',$data['id'])->with('companyBranches')->with('province')->first();
                $enc_region_id = encryptor('encrypt', $selectQuery->province->region_id);
                $barangayQuery = Barangay::where('city_id','=',$selectQuery->city_id)->get();

                if(count($selectQuery->companyBranches)==0){
                    $returnArray = [
                        'client_data'=>$selectQuery,
                        'status'=>'no-branches',
                        'region_id'=>$enc_region_id
                    ];
                    return $returnArray;
                }else{
                    $returnHtml = '<option value=""></option>';
                    foreach($selectQuery->companyBranches as $branch){
                        $returnHtml .= '<option value="'.$branch->id.'">'.$branch->name.'</option>';
                    } 
                    $returnArray = [
                        'client_data'=>$selectQuery,
                        'client_branches'=>$returnHtml,
                        'status'=>'with-branches',
                        'region_id'=>$enc_region_id
                    ];
                    return $returnArray;
                }
            }elseif($postMode=='get-address'){
                $selectProvincesQuery = Province::where([['region_id', '=', $data['region_id']], ['is_enable','=',true]])
                                ->orderBy('description', 'ASC')
                                ->get();
                $provinceContent = '';
                    foreach($selectProvincesQuery as $province) {
                        $modeProvince = '';
                        if($province->id==$data['province_id']){
                            $modeProvince = 'selected';
                        }
                        $enc_province_id = encryptor('encrypt', $province->id);
                        $provinceContent .= '<option value="'.$enc_province_id.'" '.$modeProvince.'>'.$province->description.'</option>';
                    }
                
                $selectCitiesQuery = City::where([['province_id', '=', $data['province_id']], ['is_enable','=',true]])
                                        ->orderBy('city_name', 'ASC')
                                        ->get();
                $cityContent = '';
			    	foreach($selectCitiesQuery as $city) {
                        $modeCity = '';
                        if($city->id==$data['city_id']){
                            $modeCity = 'selected';
                        }
                        $enc_city_id = encryptor('encrypt', $city->id);
			    		$cityContent .= '<option value="'.$enc_city_id.'" '.$modeCity.'>'.$city->city_name.'</option>';
                    }
                
                $selectBarangayQuery = Barangay::where('city_id','=',$data['city_id'])->get();
                $barangayContent = '';
                    foreach($selectBarangayQuery as $barangay){
                        $barangayContent .= '<option value="'.encryptor('encrypt',$barangay->id).'">'.$barangay->barangay_description.'</option>';
                    }
                
			    $returnHtml = [
                    'province'=>$provinceContent,
                    'city'=>$cityContent,
                    'barangay'=>$barangayContent
                ];

                return $returnHtml;
            }elseif($postMode=='fetch-branch-details'){
                $selectQuery = CompanyBranch::where('id','=',$data['id'])->with('province')->first();
                $enc_region_id = encryptor('encrypt', $selectQuery->province->region_id);
                $returnData = [
                    'client_data'=>$selectQuery,
                    'region'=>$enc_region_id
                ];
                return $returnData;
            }elseif($postMode=='fetch-barangays'){
                $id = encryptor('decrypt', $data['id']);
                $selectQuery = Barangay::where('city_id','=',$id)->get();

                $barangay_content = '<option value=""></option>';
                foreach($selectQuery as $barangay){
                    $barangay_content .= '<option value="'.$barangay->id.'">'.$barangay->barangay_description.'</option>';
                }

                return $barangay_content;
            }elseif($postMode=='product-list-serverside'){
                if($data['nature']=='FURNITURE'){
                    $selectQuery = Product::where('status','=','APPROVED')
                                      ->where('type','!=','FIT-OUT')
                                      ->where('archive','=',false)
                                      ->whereNull('parent_id')
                                      ->orderBy('product_name','DESC');
                }else{
                $selectQuery = Product::where('status','=','APPROVED')
                                      ->where('archive','=',false)
                                      ->whereNull('parent_id')
                                      ->orderBy('product_name','DESC');
                }
                return Datatables::eloquent($selectQuery)
                ->addColumn('image', function($selectQuery) use($user) {
                    $returnValue = '<div align="center">';
                    $enc_product_id = encryptor('encrypt',$selectQuery->id);
                    $destination  = 'assets/img/products/'.$enc_product_id.'/';
                    $defaultLink = 'http://placehold.it/754x977';
                    $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                    $returnValue .= '<img class="img-fluid text-center" id="product-preview" style="width: 56px;height:56px;" src="'.$defaultLink.'" alt="">';
                    $returnValue .= '</div>';
                    return $returnValue; 
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $enc_id = encryptor('encrypt',$selectQuery->id);
                    $returnValue = '<div align="center">';
                    $returnValue .= '<a class="btn btn-success text-white product" data-id="'.$enc_id.'" ><span>SELECT</span></a>';
                    $returnValue .= '</div>';
                    return $returnValue; 
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='fetch-product-details'){
                $id = encryptor('decrypt',$data['id']);
                $selectQuery = Product::with('variants')->with('default_product')->with('subCategoryWithCategory')->find($id);
                
                // return $selectQuery->variants;
                $variants = '';
                $variant_name = '';
                $price_temp = 0;
                if($selectQuery->type!='FIT-OUT'){
                    foreach($selectQuery->variants as $index=>$variant){
                        $variantMode = '';
                        if($variant->is_default==1){
                            $variantMode = 'checked';
                            $variant_name = $variant->product_name;
                            $price_temp = $variant->base_price;
                        }
                        $variants .= '<tr id="update'.$variant->id.'">';
                            $variants .= '<td>
                            <div class="custom-control custom-radio custom-radio-rounded">
                            <input type="radio" class="custom-control-input" id="variant'.$index.'" '.$variantMode.' data-variant_name="'.$variant->product_name.'" name="variant" value="'.$variant->id.'" />
                            <label class="custom-control-label" for="variant'.$index.'">'.$variant->product_name.'<label>
                            </div></td>';
                            $variants .= '<td><button class="btn btn-success btn-sm text-white add-variant" data-product_enc="'.$data['id'].'"  data-product="'.$id.'" data-category="'.$selectQuery->category_id.'" data-id="'.$variant->id.'"  data-new_value="'.$variant->product_name.'"><span class="fa fa-plus"></span> Add Variant</button></td>';
                        $variants .= '</tr>';
                    }
              
                    $variants_content = '
                            <div class="accordion" id="variantssCollapse">
                                <div class="card" style="border: 1px #00000029 solid;">
                                    <div class="card-header" id="headingFour">
                                        <a href="javascript:void(0);" id="select-variant-drop" class="card-title collapsed bg-fusion-600 text-white" data-toggle="collapse" data-target="#variantsContent" aria-expanded="true" aria-controls="variantsContent">
                                            Select Variant
                                            <span class="ml-auto">
                                                <span class="collapsed-reveal">
                                                    <i class="fal fa-minus-circle text-white"></i>
                                                </span>
                                                <span class="collapsed-hidden">
                                                    <i class="fal fa-plus-circle text-white"></i>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                    <div id="variantsContent" class="collapse show" aria-labelledby="headingFour" data-parent="#variantssCollapse">
                                        <div class="card-body">
                                            <label class="text-danger">*If you add variant your product type will changed to combination.</label><br>
                                            <br>
                                            <table class="table table-striped" id="dt-variant">
                                                <thead class="bg-info-200 text-center">
                                                    <tr>
                                                        <th>Attributes & Values</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    '.$variants.'
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
                    }else{
                        $count=1;
                        foreach($selectQuery->variants as $index=>$variant){
                            $variants .= '<tr>';
                                $variants .= '<td>
                                                <div class="custom-control custom-checkbox custom-radio-rounded">
                                                    <input type="checkbox" class="custom-control-input" id="variant'.$index.'" data-count="'.$count.'" data-variant_name="'.e($variant->product_name).'" name="variant[]" data-price="'.$variant->base_price.'" checked value="'.$variant->id.'" />
                                                <label class="custom-control-label" for="variant'.$index.'">'.$variant->product_name.'</label>
                                                </div>
                                            </td>';
                                $variants .= '<td>
                                <input class="form-control" id="variant_n'.$count.'" type="checkbox" style="display:none;" checked name="variant_ns[]" value="'.e($variant->product_name).'" />
                                <input type="text" onkeypress="return isNumberKey(event)" placeholder="QTY" data-id="'.$variant->id.'" data-price="'.$variant->base_price.'" class="form-control" id="variantQty'.$variant->id.'" value="1" required name="variant_qty[]" />
                                <input type="hidden" onkeypress="return isNumberKey(event)" value="'.$variant->base_price.'" class="form-control variant_total_price compute'.$count.'" id="variant_total_price'.$variant->id.'" />
                                <input type="hidden" name="variant_type[]" value="'.$variant->type.'" class="form-control" />
                                </td>';
                            $variants .= '</tr>';
                            $count++;
                        }
                        $variants_content = '
                            <h6><b>Fitout Products </b></h6>
                            <br>
                            <table class="table table-striped" id="dt-variant">
                                <thead class="bg-info-200 text-center">
                                    <tr>
                                        <th>Products</th>
                                        <th>QTY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$variants.'
                                </tbody>
                             </table>
                            ';
                    }

                    
                $defaultLink = 'http://placehold.it/754x400';
                $destination  = 'assets/img/products/'.$data['id'].'/';
                $defaultLink = imagePath($destination.''.$data['id'],$defaultLink);
                $description_data='';
                $description = toTxtFile($destination,'description','get');
                if($description['success'] == true){
                    $description_data = $description['data'];
                }else{
                    $description_data = '<div></div>';
                }

                $swatches = Swatch::where('category','=',$selectQuery->swatches)->get();
                $swatches_content = '<option value=""></option>';
                foreach($swatches as $swwatch){
                    $swatches_content .= '<option value="'.$swwatch->id.'">'.$swwatch->name.'</option>';
                }
                if(count($swatches)==0){
                    $swatches_content = 'no-swatch';
                }
                if($selectQuery->type=='FIT-OUT'){
                    $price = $selectQuery->base_price;
                    $display_price = number_format($selectQuery->base_price,2);
                }else{
                    if($selectQuery->type=='RAW'||$selectQuery->type=='SPECIAL-ITEM'){
                        $price = $selectQuery->base_price;
                        $display_price = number_format($selectQuery->base_price,2);
                    }else{
                        $price = $selectQuery->default_product->base_price;
                        $display_price = number_format($selectQuery->default_product->base_price,2);
                        $defaultLinkTemp = 'no-img';
                        $enc_product_id_data = encryptor('encrypt',$selectQuery->default_product->id);
                        $destination  = 'assets/img/products/'.$enc_product_id_data.'/';
                        $defaultLinkTemp = imagePath($destination.''.$enc_product_id_data,$defaultLinkTemp);
                        if($defaultLinkTemp!='no-img'){
                            $defaultLink = $defaultLinkTemp;
                        }
                    }
                }
                

                $returnArray = [
                    'variant'=>$variants_content,
                    'variant_name'=>$variant_name,
                    'product_name'=>$selectQuery->product_name,
                    'product_img'=>$defaultLink,
                    'product_type'=>$selectQuery->type,
                    'description'=>html_entity_decode($description_data),
                    'price'=>$price,
                    'display_price'=>$display_price,
                    'swatches_data'=>$swatches_content
                ];

                return $returnArray;
            }elseif($postMode=='fetch-swatch-details'){
                $swatches = Swatch::find($data['id']);
                $destination = 'assets/img/swatches/';
                $filename = encryptor('encrypt',$swatches->id);
                $defaultLink = 'http://placehold.it/400x400';
                $defaultLink = imagePath($destination.''.$filename,$defaultLink);

                $returnHtml = '<img class="img-fluid text-center" style="width: 100px;height:100px;" src="'.$defaultLink.'">';

                return $returnHtml;
            }elseif($postMode=='fetch-variant-details'){
                $selectQuery = Product::find($data['id']);

                $enc_product_id = encryptor('encrypt',$selectQuery->id); //child_id
                $defaultLink = 'no-img';
                $destination  = 'assets/img/products/'.$enc_product_id.'/';
                $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                if($defaultLink == 'no-img'){
                    $enc_product_id = encryptor('encrypt',$selectQuery->parent_id); //parent_id
                    $defaultLink = 'no-img';
                    $destination  = 'assets/img/products/'.$enc_product_id.'/';
                    $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                }

                $arrayReturn = [
                    'price'=>$selectQuery->base_price,
                    'display_price'=>number_format($selectQuery->base_price,2),
                    'product_type'=>$selectQuery->type,
                    'product_img'=>$defaultLink
                ];
                return $arrayReturn;
            }elseif($postMode=='fetch_attribute'){
                $selectQuery = Attribute::where('category_id','=',$data['id'])->get();
                $attributeOption = '<option></option>';
                foreach($selectQuery  as $attribute){
                    $attributeOption .= '<option value="'.strtoupper($attribute->name).'">'.$attribute->name.'</option>';
                }
                return $attributeOption;
            }elseif($postMode=='create-attribute'){
                $attributes = [
                    'attribute' => 'Attribute',
                    'attribute_value' => 'Value',
                    'price'=>'Price'
                ];
                $rules = [
                    'attribute' => 'required',
                    'attribute_value' => 'required',
                    'price'=>'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $createProduct = new Product();
                    $createProduct->product_name = $data['value_added'].' | '.$data['attribute'].':'.$data['attribute_value'];
                    $createProduct->parent_id = $data['product_id'];
                    $createProduct->base_price = $data['price'];
                    $createProduct->type = 'COMBINATION';
                    $createProduct->status = 'APPROVED';
                    if($createProduct->save()){
                        return encryptor('encrypt',$createProduct->parent_id);
                    }
                }
            }elseif($postMode=='save-arrangement'){
                $products = $data['id_array'];
                $new_count = count($products)-1;
                $generatedSavedPoint = encryptor('encrypt',$user->id);
                $destination = 'assets/files/quotations/'.$generatedSavedPoint.'/';
                $filename = 'quotation-product-information';
                $isExist = isExistFile($destination.''.$filename); 
                if ($isExist['is_exist'] == true){
                    unlink($isExist['path']);
                }
                $temp_products = array();
                for($i=0;$i<count($products);$i++){
                    if($products[$i]==null){
                        echo $products[$i];
                    }else{
                        $new_prodct = json_decode($products[$i]);
                        if($new_prodct==null){
                            $new_prodctss = $products[$i];
                            echo 'error';
                        }else{
                            array_push($temp_products,$new_prodct);
                        }
                    }
                }
                    $datas = json_encode($temp_products);
                    $result = toTxtFile($destination,$filename,'put',$datas);
                    if($result['success'] == true){
                        if($i==$new_count){
                            return count($products);
                        }
                    }
            }elseif($postMode=='quotation-amount-info'){
                $sub_total = $data['sub_total'];
                $installation_charge = $data['installation'];
                $delivery_charge = $data['delivery_charge'];
                $total_discount = $data['discount_total'];
                $total_item_discount = $data['discount_product_total'];
                $grand_total = $data['grand_total'];

                $generatedSavedPoint = encryptor('encrypt',$user->id);
                $destination = 'assets/files/quotations/'.$generatedSavedPoint.'/';
                $filename = 'quotation-amount-information';
                $isExist = isExistFile($destination.''.$filename); 
                if ($isExist['is_exist'] == true){
                    unlink($isExist['path']);
                }
                $datas = array(
                    'sub_total'=>$sub_total,
                    'installation_charge'=>$installation_charge,
                    'delivery_charge'=>$delivery_charge,
                    'total_discount'=>$total_discount,
                    'total_item_discount'=>$total_item_discount,
                    'grand_total'=>$grand_total
                );
                $datas = json_encode($datas);
                $result = toTxtFile($destination,$filename,'put',$datas);
                if($result['success'] == true){
                    return 'success!';
                }
            }elseif($postMode=='quotation-products-temporary'){
                $generatedSavedPoint = encryptor('encrypt',$user->id);
                $destination = 'assets/files/quotations/'.$generatedSavedPoint.'/';
                $filename = 'quotation-product-information';
                $isExist = isExistFile($destination.''.$filename); 
                if ($isExist['is_exist'] == true){
                    unlink($isExist['path']);
                }
                $temp_products = array();
                for($i=0;$i<count($data['order']);$i++){
                    $descript=null;
                    if(!empty($data['description'][$i])){
                        $descript = $data['description'][$i];
                    }
                   
                    if($data['product_type'][$i]=='RAW'||$data['product_type'][$i]=='SPECIAL-ITEM'){
                        $variant_ids = null;
                        $variant_names = null;
                    }else{
                        if($data['product_type'][$i]!='FIT-OUT'){
                            $variant_ids = null;
                            if(isset($data['variant_id'][$i])){
                                $variant_ids = $data['variant_id'][$i];
                            }
                            $variant_names = null;
                            if(isset($data['variant_name'][$i])){
                                $variant_names = $data['variant_name'][$i];
                            }
                        }
                    }
                    if($data['product_type'][$i]=='FIT-OUT'){
                        
                        $datas = array(
                            'order'=>$data['order'][$i],
                            'product_id'=>$data['product_id'][$i],
                            'variant_id'=>json_decode($data['variant_id'][$i]),
                            'variant_name'=>json_decode($data['variant_name'][$i]),
                            'variant_qty'=>json_decode($data['variant_qty'][$i]),
                            'variant_type'=>json_decode($data['variant_type'][$i]),
                            'qty'=>$data['qty'][$i],
                            'price'=>$data['price'][$i],
                            'product_type'=>$data['product_type'][$i],
                            'discount'=>$data['discount'][$i],
                            'total_amount'=>$data['total_amount'][$i],
                            'description'=>$descript,
                            'swatches'=>null,
                            'fitout_id'=>$data['fitout_id'][$i]
                        );
                    }else{
                        if($data['product_type'][$i]=='RAW'||$data['product_type'][$i]=='SPECIAL-ITEM'){
                            $datas = array(
                                'order'=>$data['order'][$i],
                                'product_id'=>$data['product_id'][$i],
                                'variant_id'=>$data['variant_id'][$i],
                                'variant_name'=>$data['variant_name'][$i],
                                'variant_qty'=>null,
                                'variant_type'=>null,
                                'qty'=>$data['qty'][$i],
                                'price'=>$data['price'][$i],
                                'product_type'=>$data['product_type'][$i],
                                'discount'=>$data['discount'][$i],
                                'total_amount'=>$data['total_amount'][$i],
                                'description'=>$descript,
                                'swatches'=>null,
                                'fitout_id'=>null
                            );
                        }else{
                            $datas = array(
                                'order'=>$data['order'][$i],
                                'product_id'=>$data['product_id'][$i],
                                'variant_id'=>$variant_ids,
                                'variant_name'=>$variant_names,
                                'variant_qty'=>null,
                                'variant_type'=>null,
                                'qty'=>$data['qty'][$i],
                                'price'=>$data['price'][$i],
                                'product_type'=>$data['product_type'][$i],
                                'discount'=>$data['discount'][$i],
                                'total_amount'=>$data['total_amount'][$i],
                                'description'=>$descript,
                                'swatches'=>$data['swatches'][$i],
                                'fitout_id'=>null
                            );
                        }
                        
                    }
                    array_push($temp_products,$datas);
                }

                $temp_products = json_encode($temp_products);
                $result = toTxtFile($destination,$filename,'put',$temp_products);
                if($result['success'] == true){
                    return json_decode($temp_products);
                }
            }elseif($postMode=='quotation-cancel'){
                $generatedSavedPoint = encryptor('encrypt',$user->id);
                $destination = 'assets/files/quotations/'.$generatedSavedPoint.'/';
                File::deleteDirectory($destination);
            }elseif($postMode=='delete-product-update'){
                $id = encryptor('decrypt',$data['id']);
                $updateProductQuery = QuotationProduct::find($id);
                $updateProductQuery->remarks = 'DELETED';
                $updateProductQuery->cancelled_Date = date('Y-m-d');
                if(isset($data['reason'])){
                    $updateProductQuery->cancelled_reason = $data['reason'];
                }
                if($updateProductQuery->save()){
                    $new_sub = floatval($data['sub_total'])-floatval($updateProductQuery->total_price);
                    $new_product_discount = floatval($data['discount_product_quotation'])-floatval($updateProductQuery->discount);
                    $new_total_discount = floatval($new_product_discount)+floatval($data['discount_quotation']);
                    $new_grand_total = floatval($new_sub)+floatval($data['delivery_charge'])+floatval($data['installation_charge'])-floatval($new_total_discount);
                    $savedPoint = $data['quote_number'];
                    $destination = 'assets/files/quotation_update/';
                    $computation = temporaryQuotationTotal($updateProductQuery->quotation_id,$data['discount_quotation'],$data['installation_charge'],$data['delivery_charge']);
                    $datas = array(
                        'sub_total'=>$computation['sub_total'],
                        'installation_charge'=>$data['installation_charge'],
                        'delivery_charge'=>$data['delivery_charge'],
                        'total_product_discount'=>$computation['total_product_discount'],
                        'discount'=>$data['discount_quotation'],
                        'total_discount'=>$new_total_discount,
                        'grand_total'=>$computation['grand_total'],
                        'temp_grand_total'=>number_format($computation['grand_total'],2),
                        'last_added'=>encryptor('decrypt',$data['id'])
                    );
                    $temp_datas = $datas;
                    $datas = json_encode($datas);
                    $filename = $savedPoint;
                    $isExist = isExistFile($destination.''.$filename); 
                    if ($isExist['is_exist'] == true){
                        unlink($isExist['path']);
                    }
                    $result = toTxtFile($destination,$filename,'put',$datas);
                    if($result['success'] == true){
                        return $temp_datas;
                    }
                }else{
                    return 0;
                }
            }elseif($postMode=='revert-product-update'){
                $id = encryptor('decrypt',$data['id']);
                $updateProductQuery = QuotationProduct::find($id);
                $updateProductQuery->remarks = 'TEMPORARY';
                $updateProductQuery->cancelled_Date = null;
                $updateProductQuery->cancelled_reason = null;
                if($updateProductQuery->save()){
                    $new_sub = floatval($data['sub_total'])+floatval($updateProductQuery->total_price);
                    $new_product_discount = floatval($data['discount_product_quotation'])+floatval($updateProductQuery->discount);
                    $new_total_discount = floatval($new_product_discount)+floatval($data['discount_quotation']);
                    $new_grand_total = floatval($new_sub)+floatval($data['delivery_charge'])+floatval($data['installation_charge'])-floatval($new_total_discount);
                    $savedPoint = $data['quote_number'];
                    $destination = 'assets/files/quotation_update/';
                    $computation = temporaryQuotationTotal($updateProductQuery->quotation_id,$data['discount_quotation'],$data['installation_charge'],$data['delivery_charge']);
                    $datas = array(
                        'sub_total'=>$computation['sub_total'],
                        'installation_charge'=>$data['installation_charge'],
                        'delivery_charge'=>$data['delivery_charge'],
                        'total_product_discount'=>$computation['total_product_discount'],
                        'discount'=>$data['discount_quotation'],
                        'total_discount'=>$new_total_discount,
                        'grand_total'=>$computation['grand_total'],
                        'temp_grand_total'=>number_format($computation['grand_total'],2),
                        'last_added'=>encryptor('decrypt',$data['id'])
                    );
                    $temp_datas = $datas;
                    $datas = json_encode($datas);
                    $filename = $savedPoint;
                    $isExist = isExistFile($destination.''.$filename); 
                    if ($isExist['is_exist'] == true){
                        unlink($isExist['path']);
                    }
                    $result = toTxtFile($destination,$filename,'put',$datas);
                    if($result['success'] == true){
                        return $temp_datas;
                    }
                }else{
                    return 0;
                }
            }elseif($postMode=='compute-commission'){
                $computation = commissionComputation($data['commi_type'],$data['contract_amount'],$data['amount_requested'],$data['vat_data']);

                return $computation;
            }else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode=='quotation-information'){
                $user_enc = encryptor('encrypt',$user->id);
                $attributes = [
                    'work-nature' => 'NATURE OF WORK',
                    'subject' => 'SUBJECT',
                    'jecams-role'=>'JECAMS ROLE',
                    'validity-date'=>'VALIDITY DATE',
                    'client'=>'CLIENT',
                    'payment-terms'=>'Payment Terms',
                    'vat-type'=>'VAT Type',
                    'warranty'=>'Warranty'
                ];
                $rules = [
                    'work-nature' => 'required',
                    'subject' => 'required',
                    'jecams-role'=>'required',
                    'validity-date'=>'required',
                    'client'=>'required',
                    'payment-terms'=>'required',
                    'vat-type'=>'required',
                    'warranty'=>'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $savedPoint = $user_enc;
                    $destination = 'assets/files/quotations/'.$savedPoint.'/';
                    $branch = null;
                    if(!empty($data['branch'])){
                        $branch = $data['branch'];
                    }
                    $datas = array(
                        'work_nature'=>$data['work-nature'],
                        'subject'=>$data['subject'],
                        'jecams_role'=>$data['jecams-role'],
                        'validity_date'=>$data['validity-date'],
                        'client'=>$data['client'],
                        'branch_id'=>$branch,
                        'contact_person'=>$data['contact_person'],
                        'position'=>$data['position'],
                        'contact_number'=>$data['contact_number'],
                        'payment_terms'=>$data['payment-terms'],
                        'vat_type'=>$data['vat-type'],
                        'warranty'=>$data['warranty']
                    );
                    $datas = json_encode($datas);
                    $filename = 'quotation-information';
                    $isExist = isExistFile($destination.''.$filename); 
                    if ($isExist['is_exist'] == true){
                        unlink($isExist['path']);
                    }
                    $result = toTxtFile($destination,$filename,'put',$datas);
                    if($result['success'] == true){
                        Session::flash('success', 1);
                        Session::flash('message', 'Next process is Delivery and Billig information.');
                        return back();
                    }

                }
            }elseif($postMode=='delivery-information'){
                $user_enc = encryptor('encrypt',$user->id);
                $attributes = [
                    'delivery-mode' => 'MODE OF DELIVERY',
                    'tentative-date' => 'TENTATIVE DELIVERY OR PICKUP DATE',
                    'complete-address'=>'COMPLETE ADDRESS',
                    'select-region'=>'REGION',
                    'city-content'=>'CITY',
                    'barangay-data'=>'BARANGAY',
                    'select-province'=>'PROVINCE',
                    'save-option'=>'SAVE OPTION'
                ];
                $rules = [
                    'delivery-mode' => 'required',
                    'tentative-date' => 'required'
                ];
                if($data['delivery-mode']=='DELIVER'){
                    $rules['save-option'] = 'required';
                    $rules['complete-address'] = 'required';
                    $rules['select-region'] = 'required';
                    $rules['city-content'] = 'required';
                    $rules['select-province'] = 'required';
                    $rules['barangay-data'] = 'required';
                }
                if($data['delivery-mode']=='DELIVER'){
                    if(empty($data['save-option'])){
                        $savedPoint = $user_enc;
                        $destination = 'assets/files/quotations/'.$savedPoint.'/';
                        $filename = 'delivery-information';
                        $exist = isExistFile($destination . '' . $filename);
                        if ($exist['is_exist'] == true){
                            unlink($exist['path']);
                        }
                        Session::flash('success', 0);
                        Session::flash('message', 'SAVE OPTION is required!');
                        return back();
                    }
                    $save = $data['save-option'];
                    $address = $data['complete-address'];
                    $region = $data['select-region'];
                    $city = $data['city-content'];
                    $province = $data['select-province'];
                    $barangay = $data['barangay-data'];
                }else{
                    $save = null;
                    $address = null;
                    $region = null;
                    $city = null;
                    $province = null;
                    $barangay = null;
                }
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $savedPoint = $user_enc;
                    $destination = 'assets/files/quotations/'.$savedPoint.'/';
                    $datas = array(
                        'barangay'=>$barangay,
                        'delivery_mode'=>$data['delivery-mode'],
                        'tentative_date'=>$data['tentative-date'],
                        'complete_address'=>$address,
                        'region'=>$region,
                        'city'=>$city,
                        'province'=>$province,
                        'save_option'=>$save,
                    );
                    $datas = json_encode($datas);
                    $filename = 'delivery-information';
                    $isExist = isExistFile($destination.''.$filename); 
                    if ($isExist['is_exist'] == true){
                        unlink($isExist['path']);
                    }
                    $result = toTxtFile($destination,$filename,'put',$datas);
                    if($result['success'] == true){
                        Session::flash('success', 1);
                        Session::flash('message', 'Next process is Products.');
                        return back();
                    }
                }
            }elseif($postMode=='create-quotation-product'){
                $attributes = [
                    'product-id' => 'PRODUCT',
                    'product_qty'=>'QUANTITY OF PRODUCT',
                    'product_price'=>'PRODUCT PRICE',
                    'type'=>'PRODUCT TYPE'
                ];
                $rules = [
                    'product-id' => 'required',
                    'product_qty'=>'required',
                    'product_price'=>'required',
                    'type'=>'required'
                ];
                if($data['type']!='RAW'&&$data['type']!='SPECIAL-ITEM'){
                    $attributes['variant'] = 'VARIANT';
                    $rules['variant'] = 'required';
                }
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $generatedSavedPoint = encryptor('encrypt',$user->id);
                    $destination = 'assets/files/quotations/'.$generatedSavedPoint.'/';
                    $filename = 'quotation-product-information';
                    $product_info = toTxtFile($destination,$filename,'get');
                    if($product_info['success'] === true){
                        $datas = $product_info['data'];
                        $new_data = json_decode($datas);
                        $total_amount = floatval($data['product_qty'])*floatval($data['product_price']);
                        $descript=null;
                        if(!empty($data['product-description'])){
                            if($data['product-description']!='<h6><br></h6>'){
                                if($data['product-description']!='<div style="color: rgb(0, 0, 0); font-family: Roboto, -apple-system, system-ui, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Helvetica Neue&quot;, Arial, sans-serif; font-size: 14px; letter-spacing: 0.2px;"><br></div>'){
                                    if($data['product-description']!='<div style="font-family: Montserrat, sans-serif; letter-spacing: normal;"><br></div>'){
                                        $descript = $data['product-description'];
                                    }
                                }
                            }
                        }
                        if($data['type']=='FIT-OUT'){
                            $varants = $data['variant_ns'];
                        }else{
                            $varants = $data['variant_name'];
                        }
                        if(isset($data['variant'])){
                            $varnnts = $data['variant'];
                        }else{
                            $varnnts = null;
                        }
                        if(isset($data['swatch'])){
                            $swatchs = $data['swatch'];
                        }else{
                            $swatchs = null;
                        }
                        $neww = array(
                            'order'=>$data['product_count'],
                            'product_id'=>$data['product-id'],
                            'variant_id'=>$varnnts,
                            'variant_name'=>$varants,
                            'qty'=>$data['product_qty'],
                            'price'=>$data['product_price'],
                            'product_type'=>$data['type'],
                            'discount'=>'',
                            'total_amount'=>$total_amount,
                            'description'=>$descript,
                            'swatches'=>$swatchs,
                            'fitout_id'=>$data['fitout_id']
                        );
                        if($data['type']=='FIT-OUT'){
                            $neww['variant_qty'] = $data['variant_qty'];
                            $neww['variant_type'] = $data['variant_type'];
                        }
                        if(isset($data['productsimg'])){
                            if($data['type']=='FIT-OUT'){
                                $filename_product = $data['fitout_id'];
                            }elseif($data['type']=='RAW'){
                                $filename_product=$data['variant'];
                            }else{
                                $filename_product= encryptor('encrypt',$data['variant']);
                            }
                            $file = $data['productsimg'];
                            $destination_prod  = 'assets/img/products/'.$filename_product.'/';
                            $prodctExist = isExistFile($destination_prod . '' . $filename_product);
                            if ($prodctExist['is_exist'] == true){
                                unlink($prodctExist['path']);
                            }
                            fileStorageUpload($file, $destination_prod, $filename_product, 'resize', 685, 888);
                        }
                        array_push($new_data,$neww);

                        $new_data = json_encode($new_data);
                        $result = toTxtFile($destination,$filename,'put',$new_data);
                        if($result['success'] == true){
                            return back();
                        }
                    }else{
                        if($data['type']=='FIT-OUT'){
                            $varants = $data['variant_ns'];
                        }else{
                            $varants = $data['variant_name'];
                        }
                        $isExist = isExistFile($destination.''.$filename); 
                        if ($isExist['is_exist'] == true){
                            unlink($isExist['path']);
                        }
                        $total_amount = floatval($data['product_qty'])*floatval($data['product_price']);
                        $descript=null;
                        if(!empty($data['product-description'])){
                            if($data['product-description']!='<h6><br></h6>'){
                                if($data['product-description']!='<div style="color: rgb(0, 0, 0); font-family: Roboto, -apple-system, system-ui, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Helvetica Neue&quot;, Arial, sans-serif; font-size: 14px; letter-spacing: 0.2px;"><br></div>'){
                                   if($data['product-description']!='<div style="font-family: Montserrat, sans-serif; letter-spacing: normal;"><br></div>'){
                                      $descript = $data['product-description'];
                                   }
                                }
                            }
                        }
                        if(isset($data['variant'])){
                            $varnnts = $data['variant'];
                        }else{
                            $varnnts = null;
                        }
                        if(isset($data['swatch'])){
                            $swatchs = $data['swatch'];
                        }else{
                            $swatchs = null;
                        }
                        $datas = array(
                            'order'=>$data['product_count'],
                            'product_id'=>$data['product-id'],
                            'variant_id'=>$varnnts,
                            'variant_name'=>$varants,
                            'qty'=>$data['product_qty'],
                            'price'=>$data['product_price'],
                            'product_type'=>$data['type'],
                            'discount'=>'',
                            'total_amount'=>$total_amount,
                            'description'=>$descript,
                            'swatches'=>$swatchs,
                            'fitout_id'=>$data['fitout_id']
                        );
                        if($data['type']=='FIT-OUT'){
                            $datas['variant_qty'] = $data['variant_qty'];
                            $datas['variant_type'] = $data['variant_type'];
                        }
                        if(!empty($data['swatch'])){
                            $datas['swatches'] = $data['swatch'];
                        }
                        if(isset($data['productsimg'])){
                            if($data['type']=='FIT-OUT'){
                            $filename_product = $data['fitout_id'];
                            }
                            elseif($data['type']=='RAW'){
                                $filename_product=$data['variant'];
                            }else{
                                $filename_product=encryptor('encrypt',$data['variant']);
                            }
                            $file = $data['productsimg'];
                            $destination_prod  = 'assets/img/products/'.$filename_product.'/';
                            $prodctExist = isExistFile($destination_prod . '' . $filename_product);
                            if ($prodctExist['is_exist'] == true){
                                unlink($prodctExist['path']);
                            }
                            fileStorageUpload($file, $destination_prod, $filename_product, 'resize', 685, 888);
                        }
                        if(!empty($data['product-description'])){
                            $datas['description'] = $data['product-description'];
                        }
                        $new_push = array();
                        array_push($new_push,$datas);
                        $datas = json_encode($new_push);
                        $result = toTxtFile($destination,$filename,'put',$datas);
                        if($result['success'] == true){
                            return back();
                        }
                    }
                }
            }elseif($postMode=='save-quotation'){
                $attributes = [
                    'terms' => 'TERMS AND CONDITION',
                    'sub_total'=>'SUBTOTAL',
                    'grand_total'=>'GRAND TOTAL',
                    'quotation_number'=>'QUOTATION NUMBER'
                ];
                $rules = [
                    'terms' => 'required',
                    'sub_total'=>'required',
                    'grand_total'=>'required',
                    'quotation_number'=>'required|unique:quotations,quote_number'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    DB::beginTransaction();
                    try {
                        DB::transaction(function () use ($data, $user) {
                        $generatedSavedPoint = encryptor('encrypt',$user->id);
                        $destination = 'assets/files/quotations/'.$generatedSavedPoint.'/';
                        $qfilename = 'quotation-information';
                        $quotation_info = toTxtFile($destination,$qfilename,'get');
                        //-------------------------------------------------------
                        $dfilename = 'delivery-information';
                        $delivery_info = toTxtFile($destination,$dfilename,'get');
                        //-------------------------------------------------------
                        $wNature = null;
                        $qSubject = null;
                        $qJecamsRole = null;
                        $qValidDate = null;
                        $qClient = null;
                        $qBranch = null;
                        $address = null;
                        $region_data = null;
                        $cityOptions = null;
                        $provinceOptions = null;
                        $qcontact_number = null;
                        $qposition = null;
                        $qcontact_person = null;
    
                        $qvat_type = null;
                        $qpayment_terms = null;
                        if($quotation_info['success'] === true){
                            $datas = $quotation_info['data'];
                            $datas = json_decode($datas);
                            $wNature = $datas->work_nature;
                            $qSubject = $datas->subject;
                            $qJecamsRole = $datas->jecams_role;
                            $qValidDate = $datas->validity_date;
                            $qClient = $datas->client;
                            $qvat_type = $datas->vat_type;
                            $qpayment_terms = $datas->payment_terms;
                        }
                        $delivery_mode = null;
                        $tentative_date = null;
                        $address = null;
                        $region_data = null;
                        $city_data = null;
                        $province_data = null;
                        $save_option = null;
                        $barangay_data = null;
                        if($delivery_info['success'] === true){
                            $datas_delivery = $delivery_info['data'];
                            $datas_delivery = json_decode($datas_delivery);
                            $delivery_mode = $datas_delivery->delivery_mode;
                            $tentative_date = $datas_delivery->tentative_date;
                            $address = $datas_delivery->complete_address;
                            $region_data = encryptor('decrypt',$datas_delivery->region);
                            $city_data = encryptor('decrypt',$datas_delivery->city);
                            $province_data = encryptor('decrypt',$datas_delivery->province);
                            $barangay_data = encryptor('decrypt',$datas_delivery->barangay);
                            $save_option = $datas_delivery->save_option;
                        }
                        
                        $getTeam = Agent::where('user_id','=',$user->id)->whereNull('date_end')->first();
                        $insertQuotation = new Quotation();
                        $insertQuotation->quote_number = $data['quotation_number'];
                        $insertQuotation->client_id = $qClient;
                        $insertQuotation->team_id = $getTeam->team_id;
                        $insertQuotation->user_id = $user->id;
                        $insertQuotation->work_nature = $wNature;
                        $insertQuotation->jecams_role = $qJecamsRole;
                        $insertQuotation->subject = $qSubject;
                        $insertQuotation->sub_total = $data['sub_total'];
                        $insertQuotation->grand_total = $data['grand_total'];
                        $insertQuotation->validity_date = $qValidDate;
                        $insertQuotation->delivery_mode = $delivery_mode;
                        $insertQuotation->lead_time = $tentative_date;
                        $insertQuotation->province_id = $province_data;
                        $insertQuotation->city_id = $city_data;
                        $insertQuotation->barangay_id = $barangay_data;
                        $insertQuotation->vat_type = $qvat_type;
                        $insertQuotation->quotation_term_id = $qpayment_terms;
                        $insertQuotation->remarks = $data['quotation-remarks'];
                        if($save_option=='BILLING'){
                            $insertQuotation->billing_address = $address;
                        }
                        if($save_option=='SHIPPING'){
                            $insertQuotation->shipping_address = $address;
                        }
                        if($save_option=='BILLING&SHIPPING'){
                            $insertQuotation->billing_address = $address;
                            $insertQuotation->shipping_address = $address;
                        }
                        if(!empty($data['installation_charge'])){
                            $insertQuotation->installation_charge = $data['installation_charge'];
                        }
                        if(!empty($data['delivery_charge'])){
                            $insertQuotation->delivery_charge = $data['delivery_charge'];
                        }
                        if(!empty($data['total_discount'])){
                            $insertQuotation->total_discount = $data['total_discount'];
                        }
                        if(!empty($data['discount_product_quotation'])){
                            $insertQuotation->total_item_discount = $data['discount_product_quotation'];
                        }

                        $insertQuotation->created_by = $user->id;
                        $insertQuotation->updated_by = $user->id;
                        $insertQuotation->created_at = getDatetimeNow();
                        $insertQuotation->updated_at = getDatetimeNow();
                        if($data['request-si']=='true'){
                            $insertQuotation->is_requested_si = getDatetimeNow();
                        }
                        if(!empty($data['commission-solution'])){
                            $insertQuotation->request_commission = $data['request-commi'];
                            $insertQuotation->final_commission = $data['final-commission'];
                            $insertQuotation->commission_type = $data['commission-type'];
                            $insertQuotation->commission_formula = $data['commission-solution'];
                            $insertQuotation->note = $data['formula-legend'];
                        }
                        if($insertQuotation->save()){
                            DB::commit();
                            $product_id = $data['variant_id'];
                            $swatch_id = $data['swatches'];
                            $product_name = $data['variant_name'];
                            $qty = $data['product_qty'];
                            $description = $data['description'];
                            $base_price = $data['product_list_price'];
                            $discount = $data['discountprod'];
                            $total_amount = $data['total_product_price'];
                            $type = $data['product_type'];
                            $remarks = 'Product Succesfully Added!';
                            $quotation_id = $insertQuotation->id;
                            $order = 1;
                            $count_quotation_save = 0;
                            for($i=0;$i<count($product_name);$i++){
                                $total_price = $discount[$i]+$total_amount[$i]; //discount+total_amount
                                $insertQuotationProduct = new QuotationProduct();
                                $insertQuotationProduct->quotation_id = $quotation_id;
                                if($type[$i]=='FIT-OUT'){
                                    $fitout_id = encryptor('decrypt',$data['fitout_id'][$i]);
                                    $insertQuotationProduct->product_id = $fitout_id;
                                }elseif($type[$i]=='RAW'||$type[$i]=='SPECIAL-ITEM'){
                                    $insertQuotationProduct->product_id = encryptor('decrypt',$product_id[$i]);
                                }else{
                                    $insertQuotationProduct->product_id = $product_id[$i];
                                }
                                $insertQuotationProduct->product_name = $product_name[$i];
                                $insertQuotationProduct->qty = $qty[$i];
                                $insertQuotationProduct->base_price = $base_price[$i];
                                $insertQuotationProduct->discount = $discount[$i];
                                $insertQuotationProduct->total_price = $total_price;
                                $insertQuotationProduct->total_amount = $total_amount[$i];
                                $insertQuotationProduct->type = $type[$i];
                                $insertQuotationProduct->description = $description[$i];
                                $insertQuotationProduct->remarks = $remarks;
                                $insertQuotationProduct->order = $order;
                                $insertQuotationProduct->swatch_id =$swatch_id[$i];
                                $insertQuotationProduct->created_by = $user->id;
                                $insertQuotationProduct->updated_by = $user->id;
                                $insertQuotationProduct->created_at = getDatetimeNow();
                                $insertQuotationProduct->updated_at = getDatetimeNow();
                                if($insertQuotationProduct->save()){
                                    if($insertQuotationProduct->type=='FIT-OUT'){
                                        $productLinkedId = json_decode($data['variant_id'][$i]);
                                        $productLinked = json_decode($data['variants_data'][$i]);
                                        $productQty = json_decode($data['variant_qty'][$i]);
                                        $productType = json_decode($data['variant_type'][$i]);
                                        $productVariants = $productLinked;
                                        for($x=0;$x<count($productVariants);$x++){
                                            $productQuery = Product::find($productLinkedId[$x]);
                                            $insertChildProducts = new QuotationProduct();
                                            $insertChildProducts->parent_id = $insertQuotationProduct->id;
                                            $insertChildProducts->product_id =$productLinkedId[$x];
                                            $insertChildProducts->qty = $productQty[$x];
                                            $insertChildProducts->quotation_id = $insertQuotationProduct->quotation_id;
                                            $insertChildProducts->product_name = $productLinked[$x];
                                            $insertChildProducts->type = $productType[$x];
                                            $insertChildProducts->base_price = $productQuery->base_price;
                                            $insertChildProducts->created_by = $user->id;
                                            $insertChildProducts->updated_by = $user->id;
                                            $insertChildProducts->created_at = getDatetimeNow();
                                            $insertChildProducts->updated_at = getDatetimeNow();
                                            if($insertChildProducts->save()){
                                                DB::commit();
                                            }else{
                                                DB::rollback();
                                                return array('success' => 0, 'message' =>'Error while saving fitout products');
                                                return back()->withInput($data);
                                            }
                                        }
                                    }
                                }else{
                                    DB::rollback();
                                    return array('success' => 0, 'message' =>'Error while saving products');
                                    return back()->withInput($data);
                                }
                                $count_quotation_save = $count_quotation_save+1;
                                $order++;
                            }
                            if($count_quotation_save==count($product_id)){
                                $destination_terms = 'assets/files/quotation_num/';
                                $filename_terms = $data['quotation_number'];
                                $isExist = isExistFile($destination_terms.''.$filename_terms); 
                                if ($isExist['is_exist'] == true){
                                    unlink($isExist['path']);
                                }
                                $terms_condition = array(
                                    'terms'=>$data['terms']
                                );
                                $terms_condition = json_encode($terms_condition);
                                $result = toTxtFile($destination_terms,$filename_terms,'put',$terms_condition);
                                if($result['success'] == true){
                                    File::deleteDirectory($destination);
                                    return back()->withInput($data);
                                }
                            }
                        }else{
                            DB::rollback();
                            return array('success' => 0, 'message' =>'Error while saving quotations');
                            return back()->withInput($data);
                        }
                    });
                    return back();
                }catch (QueryException $exception) {
                    DB::rollback();
                    return array('success' => 0, 'message' =>$exception->errorInfo[2]);
                    return back()->withInput($data);
                }


                }
            }elseif($postMode=='action-quotation'){
                $id = encryptor('decrypt',$data['quotationId']);
                $selectQuery = Quotation::where('id','=',$id)->with('terms')->with('barangay')->with('client')->with('products')->with('province')->with('city')->first();
                $vat_types = array(
                    'VAT-INC'=>'VAT-INC',
                    'ZERO-RATED'=>'Zero Rated',
                    'SALES-TO-GOVERNMENT'=>'Sales To Government',
                    'EXEMPT-SALES-RECEIPT'=>'Exempt Sales Reciept'
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
                    return view('it-department.quotations.view')
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
                    return view('it-department.quotations.update')
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
            }elseif($postMode=='quotation-update'){
                $id = encryptor('decrypt',$data['quotationId']);
                $selectQuery = Quotation::where('id','=',$id)->with('terms')->with('barangay')->with('client')->with('products')->with('province')->with('city')->first();
                $vat_types = array(
                    'VAT-INC'=>'VAT-INC',
                    'ZERO-RATED'=>'Zero Rated',
                    'SALES-TO-GOVERNMENT'=>'Sales To Government',
                    'EXEMPT-SALES-RECEIPT'=>'Exempt Sales Reciept'
                );
                $payment_terms = QuotationTerm::all();
                $delivery_modes = array(
                    'PICK-UP'=>'Pick-up',
                    'DELIVER'=>'Deliver'
                );
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

                $attributes = [
                    'work-nature' => 'Nature of Work',
                    'jecams-role' => 'JECAMS Role',
                    'client' => 'Client',
                    'delivery-mode' => 'Delivery Mode',
                    'tentative-date' => 'Tentative Delivery or Pickup Date',
                    'subject' => 'Subject',
                    'validity-date' => 'Validity Date',
                    'payment-terms' => 'Payment Terms',
                    'vat-type' => 'VAT Type'
                ];
                $rules = [
                    'work-nature' => 'required',
                    'jecams-role' => 'required',
                    'client' => 'required',
                    'delivery-mode' => 'required',
                    'tentative-date' => 'required',
                    'subject' => 'required',
                    'validity-date' => 'required',
                    'payment-terms' => 'required',
                    'vat-type' => 'required'
                ];
                if($data['delivery-mode']=='DELIVER'){
                    $attributes['select-region'] = 'Region';
                    $attributes['select-province'] = 'Province';
                    $attributes['city-content'] = 'City';
                    $attributes['barangay-data'] ='Barangay';
                    $rules['select-region'] = 'required';
                    $rules['select-province'] = 'required';
                    $rules['city-content'] = 'required';
                    $rules['barangay-data'] = 'required';
                    if($data['shipping-address']==''&&$data['billing-address']==''){
                        $attributes['shipping-address'] = 'Shipping Address';
                        $attributes['billing-address'] = 'Billing Address';
                        $rules['shipping-address'] = 'required';
                        $rules['billing-address'] = 'required';
                    }
                }
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                    return view('it-department.quotations.update')
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
                    DB::beginTransaction();
                    try {
                        $updateQuery = Quotation::find($id);
                        $updateQuery->work_nature = $data['work-nature'];
                        $updateQuery->jecams_role = $data['jecams-role'];
                        $updateQuery->client_id = $data['client'];
                        $updateQuery->delivery_mode = $data['delivery-mode'];
                        $updateQuery->lead_time = $data['tentative-date'];
                        $updateQuery->subject = $data['subject'];
                        $updateQuery->validity_date = $data['validity-date'];
                        $updateQuery->quotation_term_id = $data['payment-terms'];
                        $updateQuery->vat_type = $data['vat-type'];
                        $remarks = null;
                        if(!empty($data['quotation-remarks'])){
                            $remarks = $data['quotation-remarks'];
                        }
                        $updateQuery->remarks = $remarks;
                        if($data['delivery-mode']=='DELIVER'){
                            if(!empty($data['shipping-address'])){
                                $updateQuery->shipping_address = $data['shipping-address'];
                            }else{
                                $updateQuery->shipping_address = null;
                            }
                            if(!empty($data['billing-address'])){
                                $updateQuery->billing_address = $data['billing-address'];
                            }else{
                                $updateQuery->billing_address = null;
                            }
                            $updateQuery->province_id = encryptor('decrypt',$data['select-province']);
                            $updateQuery->city_id = encryptor('decrypt',$data['city-content']);
                            $updateQuery->barangay_id = encryptor('decrypt',$data['barangay-data']);
                        }else{
                            $updateQuery->shipping_address = null;
                            $updateQuery->billing_address = null;
                            $updateQuery->province_id = null;
                            $updateQuery->city_id = null;
                            $updateQuery->barangay_id = null;
                        }
                        $updateQuery->sub_total = $data['sub_total'];
                        $updateQuery->installation_charge = $data['installation_charge'];
                        $updateQuery->delivery_charge = $data['delivery_charge'];
                        $updateQuery->total_discount = $data['total_discount'];
                        $updateQuery->total_item_discount = $data['discount_product_quotation'];
                        $updateQuery->grand_total = $data['grand_total'];
                        if($updateQuery->save()){
                            DB::commit();
                            $count = 0;
                            for($i=0;$i<count($data['product_id']);$i++){
                                $count++;
                                $dec_id = encryptor('decrypt',$data['product_id'][$i]);
                                $updateProductQuery = QuotationProduct::find($dec_id);
                                $updateProductQuery->qty = $data['product_qty'][$i];
                                $updateProductQuery->base_price = $data['product_price'][$i];
                                $updateProductQuery->discount = $data['product_discount'][$i];
                                $updateProductQuery->total_price = $data['product_total'][$i];
                                $total_price = floatval($data['product_total'][$i])+floatval($data['product_discount'][$i]);
                                $updateProductQuery->total_amount = $data['product_total'][$i];
                                $updateProductQuery->total_price = $total_price;
                                $updateProductQuery->order = $count;
                                if($updateProductQuery->remarks=='TEMPORARY'||$updateProductQuery->remarks=='Product Succesfully Added!'){
                                    $updateProductQuery->remarks = 'Product Succesfully Added!';
                                    $updateProductQuery->cancelled_date = null;
                                }
                                if($updateProductQuery->remarks=='DELETED'){
                                    $updateProductQuery->cancelled_date = date('Y-m-d');
                                }
                                if($updateProductQuery->save()){
                                    DB::commit();
                                    if($count==count($data['product_id'])){
                                        $destination_terms = 'assets/files/quotation_num/';
                                        $filename_terms = $selectQuery->quote_number;
                                        $isExist = isExistFile($destination_terms.''.$filename_terms); 
                                        $terms_condition = array(
                                            'terms'=>$data['terms_condition']
                                        );
                                        if ($isExist['is_exist'] == true){
                                            unlink($isExist['path']);
                                        }
                                        $terms_condition = json_encode($terms_condition);
                                        $result = toTxtFile($destination_terms,$filename_terms,'put',$terms_condition);
                                        if($result['success'] == true){
                                            $selectQuery = Quotation::where('id','=',$updateProductQuery->quotation_id)->with('terms')->with('barangay')->with('client')->with('products')->with('province')->with('city')->first();
                                            $destination_quote = 'assets/files/quotation_update/';
                                            $filename_qupte = $selectQuery->quote_number;
                                            $isExist = isExistFile($destination_quote.''.$filename_qupte); 
                                            if ($isExist['is_exist'] == true){
                                                unlink($isExist['path']);
                                            }
                                                 return view('it-department.quotations.list')
                                                ->with('admin_menu','QUOTATION')
                                                ->with('admin_sub_menu','LIST-QUOTATION');
                                        }else{
                                            DB::rollback();
                                            return view('it-department.quotations.update')
                                                ->with('work_nature',$work_nature)
                                                ->with('roles',$roles)
                                                ->with('delivery_modes',$delivery_modes)
                                                ->with('clients',$clients)
                                                ->with('regions',$regions)
                                                ->with('sub_to',$selectQuery->sub_total)
                                                ->with('vat_types',$vat_types)
                                                ->with('payment_terms',$payment_terms)
                                                ->with('quotation',$selectQuery);
                                        }
                                    }
                                }else{
                                    DB::rollback();
                                    return view('it-department.quotations.update')
                                        ->with('work_nature',$work_nature)
                                        ->with('roles',$roles)
                                        ->with('delivery_modes',$delivery_modes)
                                        ->with('clients',$clients)
                                        ->with('regions',$regions)
                                        ->with('sub_to',$selectQuery->sub_total)
                                        ->with('vat_types',$vat_types)
                                        ->with('payment_terms',$payment_terms)
                                        ->with('quotation',$selectQuery);
                                }
                            }
                            
                        }else{
                            DB::rollback();
                            return view('it-department.quotations.update')
                                ->with('work_nature',$work_nature)
                                ->with('roles',$roles)
                                ->with('delivery_modes',$delivery_modes)
                                ->with('clients',$clients)
                                ->with('regions',$regions)
                                ->with('sub_to',$selectQuery->sub_total)
                                ->with('vat_types',$vat_types)
                                ->with('payment_terms',$payment_terms)
                                ->with('quotation',$selectQuery);
                        }
                    }catch (QueryException $exception) {
                        DB::rollback();
                        return array('success' => 0, 'message' =>$exception->errorInfo[2]);
                        return view('it-department.quotations.update')
                         ->with('work_nature',$work_nature)
                         ->with('roles',$roles)
                         ->with('delivery_modes',$delivery_modes)
                         ->with('clients',$clients)
                         ->with('regions',$regions)
                         ->with('vat_types',$vat_types)
                         ->with('sub_to',$selectQuery->sub_total)
                         ->with('payment_terms',$payment_terms)
                         ->with('quotation',$selectQuery)
                         ->withInput($data);
                    }
                }
            }elseif($postMode=='update-quotationProduct'){
                $id = encryptor('decrypt',$data['quotationId']);
                $selectQuery = Quotation::where('id','=',$id)->with('terms')->with('barangay')->with('client')->with('products')->with('province')->with('city')->first();
                $vat_types = array(
                    'VAT-INC'=>'VAT-INC',
                    'ZERO-RATED'=>'Zero Rated',
                    'SALES-TO-GOVERNMENT'=>'Sales To Government',
                    'EXEMPT-SALES-RECEIPT'=>'Exempt Sales Reciept'
                );
                $payment_terms = QuotationTerm::all();
                $delivery_modes = array(
                    'PICK-UP'=>'Pick-up',
                    'DELIVER'=>'Deliver'
                );
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
                $attributes = [
                    'product-id' => 'PRODUCT',
                    'product_qty'=>'QUANTITY OF PRODUCT',
                    'product_price'=>'PRODUCT PRICE',
                    'type'=>'PRODUCT TYPE'
                ];
                $rules = [
                    'product-id' => 'required',
                    'product_qty'=>'required',
                    'product_price'=>'required',
                    'type'=>'required'
                ];
                if($data['type']!='RAW'&&$data['type']!='SPECIAL-ITEM'){
                    $attributes['variant'] = 'VARIANT';
                    $rules['variant'] = 'required';
                }
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return view('it-department.quotations.update')
                    ->with('work_nature',$work_nature)
                    ->with('roles',$roles)
                    ->with('delivery_modes',$delivery_modes)
                    ->with('clients',$clients)
                    ->with('regions',$regions)
                    ->with('vat_types',$vat_types)
                    ->with('sub_to',$selectQuery->sub_total)
                    ->with('payment_terms',$payment_terms)
                    ->with('quotation',$selectQuery);
                }else{
                    
                    if($data['type']=='FIT-OUT'){
                        $product_name = $data['product-id'];
                        $qty = $data['product_qty'];
                        $product_id = encryptor('decrypt',$data['fitout_id']);
                        $variant_ids = $data['variant'];
                        $variant_names = $data['variant_ns'];
                        $variants_qty = $data['variant_qty'];
                        $variant_type = $data['variant_type'];
                        $product_price = $data['product_price'];
                        $product_type = $data['type'];
                        $description=null;
                        if(!empty($data['product-description'])){
                            if($data['product-description']!='<h6><br></h6>'){
                                if($data['product-description']!='<div style="color: rgb(0, 0, 0); font-family: Roboto, -apple-system, system-ui, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Helvetica Neue&quot;, Arial, sans-serif; font-size: 14px; letter-spacing: 0.2px;"><br></div>'){
                                    if($data['product-description']!='<div style="font-family: Montserrat, sans-serif; letter-spacing: normal;"><br></div>'){
                                        $description = $data['product-description'];
                                    }
                                }
                            }
                        }
                        $total_amount = floatval($data['product_qty'])*floatval($data['product_price']);
                        $product_count = count($selectQuery->products);
                        $order = floatval($product_count)+1;

                        $insertProduct = new QuotationProduct();
                        $insertProduct->quotation_id = $selectQuery->id;
                        $insertProduct->product_id = $product_id;
                        $insertProduct->product_name = $product_name;
                        $insertProduct->qty = $qty;
                        $insertProduct->base_price = $product_price;
                        $insertProduct->total_price = $total_amount;
                        $insertProduct->total_amount = $total_amount;
                        $insertProduct->type = $product_type;
                        $insertProduct->description = $description;
                        $insertProduct->remarks = 'TEMPORARY';
                        $insertProduct->cancelled_date = date('Y-m-d');
                        $insertProduct->order = $order;
                        $insertProduct->created_by = $user->id;
                        $insertProduct->updated_by = $user->id;
                        $insertProduct->created_at = getDatetimeNow();
                        $insertProduct->updated_at = getDatetimeNow();
                        if($insertProduct->save()){
                            for($i=0;$i<count($variant_names);$i++){
                                $insertVariantProduct = new QuotationProduct();
                                $insertVariantProduct->quotation_id = $selectQuery->id;
                                $insertVariantProduct->product_id = $variant_ids[$i];
                                $insertVariantProduct->product_name = $variant_names[$i];
                                $insertVariantProduct->qty = $variants_qty[$i];
                                $insertVariantProduct->type = $variant_type[$i];
                                $insertVariantProduct->parent_id = $insertProduct->id;
                                $insertVariantProduct->created_by = $user->id;
                                $insertVariantProduct->updated_by = $user->id;
                                $insertVariantProduct->created_at = getDatetimeNow();
                                $insertVariantProduct->updated_at = getDatetimeNow();
                                $insertVariantProduct->save();
                            }
                            if(!empty($data['productsimg'])){
                                $filename_product= encryptor('encrypt',$product_id);
                                $file = $data['productsimg'];
                                $destination_prod  = 'assets/img/products/'.$filename_product.'/';
                                $prodctExist = isExistFile($destination_prod . '' . $filename_product);
                                if ($prodctExist['is_exist'] == true){
                                    unlink($prodctExist['path']);
                                }
                                fileStorageUpload($file, $destination_prod, $filename_product, 'resize', 685, 888);
                            }
                            $computation = temporaryQuotationTotal($selectQuery->id,$data['discount_data'],$data['installation_data'],$data['delivery_data']);
                       
                            $savedPoint = $selectQuery->quote_number;
                            $destination = 'assets/files/quotation_update/';
                            $datas = array(
                                'sub_total'=>$computation['sub_total'],
                                'installation_charge'=>$data['installation_data'],
                                'delivery_charge'=>$data['delivery_data'],
                                'total_product_discount'=>$computation['total_product_discount'],
                                'discount'=>$data['discount_data'],
                                'total_discount'=>$data['total_discount_data'],
                                'grand_total'=>$computation['grand_total'],
                                'last_added'=>$insertProduct->id
                            );
                            $datas = json_encode($datas);
                            $filename = $savedPoint;
                            $isExist = isExistFile($destination.''.$filename); 
                            if ($isExist['is_exist'] == true){
                                unlink($isExist['path']);
                            }
                            $result = toTxtFile($destination,$filename,'put',$datas);
                            if($result['success'] == true){
                                return view('it-department.quotations.update')
                                    ->with('work_nature',$work_nature)
                                    ->with('roles',$roles)
                                    ->with('delivery_modes',$delivery_modes)
                                    ->with('clients',$clients)
                                    ->with('regions',$regions)
                                    ->with('vat_types',$vat_types)
                                    ->with('sub_to',$selectQuery->sub_total)
                                    ->with('payment_terms',$payment_terms)
                                    ->with('quotation',$selectQuery);
                            }

                        }
                    }else{

                        $product_name = $data['product-id'];
                        $qty = $data['product_qty'];
                        if($data['type']=='RAW'||$data['type']=='SPECIAL-ITEM'){
                            $product_id = encryptor('decrypt',$data['variant']);
                        }else{
                            $product_id = $data['variant'];
                        }
                        $product_price = $data['product_price'];
                        $product_type = $data['type'];
                        $product_count = count($selectQuery->products);
                        $order = floatval($product_count)+1;
                        $description=null;
                        if(!empty($data['product-description'])){
                            if($data['product-description']!='<h6><br></h6>'){
                                if($data['product-description']!='<div style="color: rgb(0, 0, 0); font-family: Roboto, -apple-system, system-ui, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Helvetica Neue&quot;, Arial, sans-serif; font-size: 14px; letter-spacing: 0.2px;"><br></div>'){
                                    if($data['product-description']!='<div style="font-family: Montserrat, sans-serif; letter-spacing: normal;"><br></div>'){
                                        $description = $data['product-description'];
                                    }
                                }
                            }
                        }
                        $total_amount = floatval($data['product_qty'])*floatval($data['product_price']);
                        if(isset($data['swatch'])){
                            $swatch = $data['swatch'];
                        }else{
                            $swatch = null;
                        }
                        $check_products = checkRedundant($product_id);
                        
                        if($check_products<=0){
                        $insertProduct = new QuotationProduct();
                        $insertProduct->quotation_id = $selectQuery->id;
                        $insertProduct->product_id = $product_id;
                        $insertProduct->product_name = $product_name;
                        $insertProduct->swatch_id = $swatch;
                        $insertProduct->qty = $qty;
                        $insertProduct->base_price = $product_price;
                        $insertProduct->total_price = $total_amount;
                        $insertProduct->total_amount = $total_amount;
                        $insertProduct->type = $product_type;
                        $insertProduct->description = $description;
                        $insertProduct->remarks = 'TEMPORARY';
                        $insertProduct->cancelled_date = date('Y-m-d');
                        $insertProduct->order = $order;
                        $insertProduct->created_by = $user->id;
                        $insertProduct->updated_by = $user->id;
                        $insertProduct->created_at = getDatetimeNow();
                        $insertProduct->updated_at = getDatetimeNow();
                        if($insertProduct->save()){
                            if(!empty($data['productsimg'])){
                                $filename_product= encryptor('encrypt',$product_id);
                                $file = $data['productsimg'];
                                $destination_prod  = 'assets/img/products/'.$filename_product.'/';
                                $prodctExist = isExistFile($destination_prod . '' . $filename_product);
                                if ($prodctExist['is_exist'] == true){
                                    unlink($prodctExist['path']);
                                }
                                fileStorageUpload($file, $destination_prod, $filename_product, 'resize', 685, 888);
                            }
                            $computation = temporaryQuotationTotal($selectQuery->id,$data['total_discount_data'],$data['installation_data'],$data['delivery_data']);
                       
                            $savedPoint = $selectQuery->quote_number;
                            $destination = 'assets/files/quotation_update/';
                            $datas = array(
                                'sub_total'=>$computation['sub_total'],
                                'installation_charge'=>$data['installation_data'],
                                'delivery_charge'=>$data['delivery_data'],
                                'total_product_discount'=>$computation['total_product_discount'],
                                'discount'=>$data['discount_data'],
                                'total_discount'=>$data['total_discount_data'],
                                'grand_total'=>$computation['grand_total'],
                                'last_added'=>$insertProduct->id
                            );
                            $datas = json_encode($datas);
                            $filename = $savedPoint;
                            $isExist = isExistFile($destination.''.$filename); 
                            if ($isExist['is_exist'] == true){
                                unlink($isExist['path']);
                            }
                            $result = toTxtFile($destination,$filename,'put',$datas);
                            if($result['success'] == true){
                                return view('it-department.quotations.update')
                                    ->with('work_nature',$work_nature)
                                    ->with('roles',$roles)
                                    ->with('delivery_modes',$delivery_modes)
                                    ->with('clients',$clients)
                                    ->with('regions',$regions)
                                    ->with('vat_types',$vat_types)
                                    ->with('sub_to',$selectQuery->sub_total)
                                    ->with('payment_terms',$payment_terms)
                                    ->with('quotation',$selectQuery);
                            }
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'Error has been detected while saving quotation product.');
                            return view('it-department.quotations.update')
                                    ->with('work_nature',$work_nature)
                                    ->with('roles',$roles)
                                    ->with('delivery_modes',$delivery_modes)
                                    ->with('clients',$clients)
                                    ->with('regions',$regions)
                                    ->with('vat_types',$vat_types)
                                    ->with('sub_to',$selectQuery->sub_total)
                                    ->with('payment_terms',$payment_terms)
                                    ->with('quotation',$selectQuery);
                        }
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'Product already existing.');
                            return view('it-department.quotations.update')
                                    ->with('work_nature',$work_nature)
                                    ->with('roles',$roles)
                                    ->with('delivery_modes',$delivery_modes)
                                    ->with('clients',$clients)
                                    ->with('regions',$regions)
                                    ->with('vat_types',$vat_types)
                                    ->with('sub_to',$selectQuery->sub_total)
                                    ->with('payment_terms',$payment_terms)
                                    ->with('quotation',$selectQuery);
                        }
                    }
                }
            }elseif($postMode=='quotation-clear'){
                $id = encryptor('decrypt',$data['quoteId']);
                $selectQuery = Quotation::where('id','=',$id)->with('products')->with('terms')->with('barangay')->with('client')->with('temporary')->with('province')->with('city')->first();
                $vat_types = array(
                    'VAT-INC'=>'VAT-INC',
                    'ZERO-RATED'=>'Zero Rated',
                    'SALES-TO-GOVERNMENT'=>'Sales To Government',
                    'EXEMPT-SALES-RECEIPT'=>'Exempt Sales Reciept'
                );
                $payment_terms = QuotationTerm::all();
                $delivery_modes = array(
                    'PICK-UP'=>'Pick-up',
                    'DELIVER'=>'Deliver'
                );
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
                foreach($selectQuery->temporary as $product){
                    if($product->type == 'FIT-OUT'){
                        $deleteProduct = QuotationProduct::where('parent_id','=',$product->id)->delete();
                        $deleteProduct = QuotationProduct::where('id','=',$product->id)->delete();
                    }else{
                        $deleteProduct = QuotationProduct::where('id','=',$product->id)->delete();
                    }
                }
                $destination_quote = 'assets/files/quotation_update/';
                $filename_qupte = $selectQuery->quote_number;
                $isExist = isExistFile($destination_quote.''.$filename_qupte); 
                if ($isExist['is_exist'] == true){
                    unlink($isExist['path']);
                }
                $sub_total = 0; 
                foreach($selectQuery->products as $orig_product){
                   $total_price = floatval($orig_product->qty)*floatval($orig_product->base_price);
                   $sub_total = floatval($sub_total)+floatval($total_price);
                }

                $grand_total = floatval($sub_total)+floatval($selectQuery->installation_charge)+floatval($selectQuery->delivery_charge)-floatval($selectQuery->total_discount);
                $selectQuery->sub_total = $sub_total;
                $selectQuery->grand_total = $grand_total;
                if($selectQuery->save()){
                    return view('it-department.quotations.update')
                    ->with('work_nature',$work_nature)
                    ->with('roles',$roles)
                    ->with('delivery_modes',$delivery_modes)
                    ->with('clients',$clients)
                    ->with('regions',$regions)
                    ->with('vat_types',$vat_types)
                    ->with('sub_to',$sub_total)
                    ->with('payment_terms',$payment_terms)
                    ->with('quotation',$selectQuery);
                }

            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
