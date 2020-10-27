<?php

namespace App\Http\Controllers\Sales;

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
use App\Quotation;
use App\QuotationProduct;
use App\Agent;
use App\Bank;
use App\Barangay;

class DeliveryScheduleController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $id = encryptor('decrypt',$data['id']);
        $delivery_modes = array(
            'PICK-UP'=>'Pick-up',
            'DELIVER'=>'Deliver'
        );
        $quotation = Quotation::with('client')->with('job_request')->with('terms')->with('sales_agent')
                                ->where('id','=',$id)->where('user_id','=',$user->id)->first();

        return view('sales-department.delivery_schedules.create')->with('quotation',$quotation)->with('delivery_modes',$delivery_modes);
    }
    public function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        
        if($request->ajax()){
            if($postMode=='product-list-serverside'){
                $id = encryptor('decrypt',$data['id']);
                $selectQuery = QuotationProduct::with('purchase_order_product')->with('product')->with('update_fitout_products')
                                                ->where('quotation_id','=',$id)
                                                ->whereNull('cancelled_date')
                                                ->orderBy('order','ASC');
                return Datatables::eloquent($selectQuery) 
                ->addColumn('image', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
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
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->addColumn('product_status', function($selectQuery) use($user) {
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
                            <td>'.$revision_details.'</td>
                            <td>'.$selectQuery->job_request_product->status.'</td>
                            <td></td>
                        </tr>
                        ';
                    }else{
                        $for_job_request = '
                        <tr class="bg-danger-50">
                            <td>Job Request</td>
                            <td colspan="3"><b class="text-danger">No Current Process in JR</b></td>
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
                                                        <td colspan="3"><b class="text-danger">No Current Process in JR</b></td>
                                                    </tr>
                                                    ';
                            }
                        }
                    }
                    $for_po = '';
                    if(!empty($selectQuery->purchase_order_product)){
                        $payment_req = '<b class="text-danger">no payment request</b>';
                        if(!empty($selectQuery->purchase_order_product->payment_request_id)){
                            $payment_req = '<b class="text-info">with payment request</b>';
                        }
                        $for_po = '
                        <tr>
                            <td>Purchase Order</td>
                            <td>
                                '.$selectQuery->purchase_order_product->purchaseOrder->po_number.' <br class="m-0">
                                PAYMENT STATUS :'.$selectQuery->purchase_order_product->purchaseOrder->payment_status.'
                            </td>
                            <td>'.$selectQuery->purchase_order_product->purchaseOrder->status.'</td>
                            <td>'.$selectQuery->purchase_order_product->qty.'</td>
                        </tr>';
                    }else{
                        $for_po = '
                            <tr class="bg-danger-50">
                                <td>Purchase Order</td>
                                <td colspan="3"><b class="text-danger">No Current Process in P.O.</b></td>
                            </tr>
                            ';
                    }
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<table class="table table-bordered">
                                            <tr>
                                                <th>PROCESS</th>
                                                <th>DETAILS</th>
                                                <th>STATUS</th>
                                                <th>QTY</th>
                                            </tr>
                                            '.$for_job_request.'
                                            '.$for_po.'
                                        </table>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('qty', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    if($selectQuery->delivered_qty<$selectQuery->qty){
                        $returnHtml = '<div align="center"><input name="product_qty[]" maxlength="6" style="text-align:center;" class="form-control" placeholder="QTY" value="'.$selectQuery->qty.'" /></div>';
                    }else{
                        $returnHtml = $selectQuery->qty.'/'.$selectQuery->delivered_qty;
                    }
                    
                    return $returnHtml;
                })
                ->editColumn('product_name', function($selectQuery) use($user) {
                    $returnHtml = '<b>'.$selectQuery->product_name.'</b><hr class="m-0">';
                    $returnHtml .= '<br class="m-0"><b>Description</b><br class="m-0">';
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
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);                              
            }else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            Session::flash('success', 0);
            Session::flash('message', 'Undefined method please try again');
            return back();
        }
    }
}
