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
use App\Quotation;
use App\QuotationProduct;
use App\JobRequest;
use App\JobRequestProduct;
use App\JobRequestType;
use App\Product;
use App\Department;
use App\User;
use App\Agent;
class JobRequestController extends Controller
{
    function list(){
        return view('sales-department.job_requests.list')
            ->with('admin_menu','JOB-REQUEST');
    }
    function view($id=null){
        $jr_id = encryptor('decrypt',$id);
        $selectQuery = JobRequest::find($jr_id);
        if($selectQuery->quotation->status=='PENDING'){
            $jr_types = JobRequestType::where('name','!=','For Production')->get();
        }else{
            $jr_types = JobRequestType::all();
        }
        //start gelo added
        $designerDept = Department::where('name', '=', 'Design')->first();
        $selectDesigners = User::where('department_id', '=', $designerDept->id)
                                ->where('status', '=', 'ACTIVE')
                                ->with('employee')
                                ->get();
        //end gelo added
        return view('sales-department.job_requests.view')
                ->with('jr',$selectQuery)
                ->with('jr_types',$jr_types)
                ->with('designers', $selectDesigners);
    }
    function imageUpdate(Request $request){
        $data = $request->all();
        $user = Auth::user();
        
        $id = encryptor('decrypt',$data['product_id']);
        $selectQuery = Product::find($id);
        $enc_product_id = $data['product_id']; 
        $defaultLink = 'no-img';
        $destination  = 'assets/img/products/'.$enc_product_id.'/';
        $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
        if($defaultLink=='no-img'){
            $enc_product_id = encryptor('encrypt',$selectQuery->parent_id); 
            $defaultLink = 'http://placehold.it/454x400';
            $destination  = 'assets/img/products/'.$enc_product_id.'/';
            $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
        }
        $returnHtml = '<img class="img-fluid text-center" src="'.$defaultLink.'" id="jrproduct-previewa" style="width: 400px;height:454px;border: 1px solid #0000000f;">';

        return $returnHtml;
    }
    //start gelo added
    function floorPlanImageUpdate(Request $request){
        $data = $request->all();
        $user = Auth::user();

        $defaultLink = 'http://placehold.it/754x400';
        $destination = 'assets/img/job_request_products/floor_plan/';
        $filename = $data['jr_product_id'];
        $defaultLink = imagePath($destination.''.$filename, $defaultLink);
        
        $returnHtml = '<img class="img-fluid text-center" src="'.$defaultLink.'" id="update-fp-img-preview" style="width: 400px;height:454px;border: 1px solid #0000000f;">';
        return $returnHtml;
    }

    function productDescContent(Request $request){
        $data = $request->all();
        $user = Auth::user();

        $id = encryptor('decrypt', $data['jr_product_id']);
        $selectJRQuotationProduct = JobRequestProduct::where('id', '=', $id)->with('jr_quote_product_with_quotation')->first();
        $selectJRProductChild = JobRequestProduct::where('parent_id', '=', $id)->where('status', '=', 'REJECTED')->first();
        $returnHtml = '';
        if(!empty($selectJRQuotationProduct->jr_quote_product_with_quotation)) {
            $quotation_product = $selectJRQuotationProduct->jr_quote_product_with_quotation;
            $quotation = $selectJRQuotationProduct->jr_quote_product_with_quotation->quotation;
            $discount = floatval($quotation->total_discount) - floatval($quotation->total_item_discount);
            $returnHtml = '<div class="form-group mb-0">
                    <label>Product Code : <b>'.$quotation_product->product_name.'</b></label>
                </div>
                <div class="form-group">
                    <label>Description  : </label>
                    <textarea id="product-description" name="product-description">'.$quotation_product->description.'</textarea>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table>
                                <tfoot>
                                    <tr>
                                        <td><b>QUANTITY :</b></td>
                                        <td><input type="text" name="product_qty" class="form-control" value="'.$quotation_product->qty.'" required readonly /></td>
                                    </tr>
                                    <tr>
                                        <td><b>PRODUCT PRICE :</b></td>
                                        <td><input type="text" name="product_price" id="product_price" data-id="'.encryptor('encrypt', $quotation_product->id).'" data-qid="'.encryptor('encrypt', $quotation_product->quotation_id).'" class="form-control numeric_filter" value="'.number_format((float)$quotation_product->base_price,2, '.', '').'" required/></td>
                                    </tr>
                                    <tr>
                                        <td><b>PRODUCT DISCOUNT :</b></td>
                                        <td><input type="text" name="product_discount" id="product_discount" data-id="'.encryptor('encrypt', $quotation_product->id).'" data-qid="'.encryptor('encrypt', $quotation_product->quotation_id).'" class="form-control numeric_filter" value="'.number_format((float)$quotation_product->discount,2, '.', '').'" required/></td>
                                    </tr>
                                    <tr>
                                        <td><b>TOTAL PRICE :</b></td>
                                        <td>
                                            <input type="text" name="product_total" class="form-control" value="'.number_format((float)$quotation_product->total_amount,2).'" required readonly/>
                                            <input type="hidden" name="hidden_product_total_price" value="'.number_format((float)$quotation_product->total_price,2, '.', '').'" required readonly />
                                            <input type="hidden" name="hidden_product_total_amount" value="'.number_format((float)$quotation_product->total_amount,2, '.', '').'" required readonly />
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table>
                                <tfoot>
                                    <tr>
                                        <td><b>SUB TOTAL :</b></td>
                                        <td><input type="text" name="sub_total" class="form-control" value="'.number_format((float)$quotation->sub_total,2, '.', '').'" required readonly /></td>
                                    </tr>
                                    <tr>
                                        <td><b>INSTALLATION CHARGE :</b></td>
                                        <td><input type="text" name="installation_charge" class="form-control" value="'.number_format((float)$quotation->installation_charge,2, '.', '').'" required readonly /></td>
                                    </tr>
                                    <tr>
                                        <td><b>DELIVERY CHARGE :</b></td>
                                        <td><input type="text" name="delivery_charge" class="form-control" value="'.number_format((float)$quotation->delivery_charge,2, '.', '').'" required readonly /></td>
                                    </tr>
                                    <tr>
                                        <td><b>TOTAL PRODUCT DISCOUNT :</b><br><small class="text-danger">*FOR TOTAL PRODUCT EACH DISCOUNT TOTAL</small></td>
                                        <td><input type="text" name="discount_product_quotation" class="form-control" value="'.number_format((float)$quotation->total_item_discount,2, '.', '').'" required readonly/></td>
                                    </tr>
                                    <tr>
                                        <td><b>DISCOUNT :</b><br><small class="text-danger">*FOR WHOLE QUOTATION DISCOUNT</small></td>
                                        <td><input type="text" name="discount_quotation" class="form-control" value="'.number_format((float)$discount,2, '.', '').'" required readonly /></td>
                                    </tr>
                                    <tr>
                                        <td><b>TOTAL DISCOUNT :</b><br><small class="text-danger">*FOR (TOTAL PRODUCT DISCOUNT) + (DISCOUNT)</small></td>
                                        <td><input type="text" name="total_discount" class="form-control" value="'.number_format((float)$quotation->total_discount,2, '.', '').'" required readonly /></td>
                                    </tr>
                                    <tr>
                                        <td><b>GRAND TOTAL :</b></td>
                                        <td><input type="text" name="grand_total_temp" class="form-control" value="'.number_format((float)$quotation->grand_total,2).'" required readonly />
                                        <input type="hidden" name="grand_total" class="form-control" value="'.number_format((float)$quotation->grand_total,2, '.', '').'" required readonly />
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="quotation-product-id" value="'.encryptor('encrypt', $quotation_product->id).'" readonly />
                <input type="hidden" name="quotation-id" value="'.encryptor('encrypt', $quotation->id).'" readonly />
                <input type="hidden" name="jr-product-parent-id" value="'.encryptor('encrypt', $selectJRQuotationProduct->id).'" readonly />
                <input type="hidden" name="jr-product-child-id" value="'.encryptor('encrypt', $selectJRProductChild->id).'" readonly />
                ';
        } else {
            $returnHtml = '<div class="form-group">
                    <h3 class="text-danger">Quotation Product Not Found!</h3>
                </div>';
        }
        return $returnHtml;
    }

    function jrRevisionContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $id = encryptor('decrypt', $data['jr_product_id']);

        $selectJRProduct = JobRequestProduct::find($id);
        if(!empty($selectJRProduct)) {
            $returnHtml = '<div class="form-group">
                <label>Deadline Date</label>
                <input type="text" class="form-control" required name="update-deadline-date" value="'.$selectJRProduct->deadline_date.'"/>
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" rows="5" name="update-revision-remarks" required>'.$selectJRProduct->remarks.'</textarea>
            </div>
            <input type="hidden" name="jr-product-id" value="'.encryptor('encrypt', $selectJRProduct->id).'" />';
        } else {
            $returnHtml = '<div class="form-group">
                    <h3 class="text-danger">Job Request Product Not Found!</h3>
                </div>';
        }
        return $returnHtml;
    }
    //end gelo added
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        $agent = Agent::where('user_id','=', $user->id)->whereNull('date_end')->first();

        if ($request->ajax()){
            //start gelo
            if($postMode=='jr-pending-serverside'){
                $selectQuery = JobRequest::with('client')->with('quotation')->with('agent')->where('status','=',$data['status'])->where('agent_id', '=', $agent->id)->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('updated_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center"><b class="text-info">UPDATED :</b>'.date('F d,Y ',strtotime($selectQuery->updated_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->updated_at).']</small><hr class="m-0">';
                    $returnHTml .= '<b class="text-danger">CREATED :</b>'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small>';
                    $returnHTml .= '</div>';

                    return $returnHTml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->client->name;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<label class="text-primary"> ['.$selectQuery->quotation->quote_number.'] </label>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('jr_number', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->jr_number;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<b>Products</b> : <span class="badge bg-fusion-500 ml-2">'.jobRequestCount($selectQuery->id).'</span>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('agent.user.employee.first_name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->agent->user->employee->first_name." ".$selectQuery->agent->user->employee->last_name;
                    $returnHTml .= '<hr class="m-0">';
                    
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('sales-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            } elseif($postMode=='jr-ongoing-serverside'){
                $selectQuery = JobRequest::with('client')->with('quotation')->with('agent')->where('status','=',$data['status'])->where('agent_id', '=', $agent->id)->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('updated_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center"><b class="text-info">UPDATED :</b>'.date('F d,Y ',strtotime($selectQuery->updated_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->updated_at).']</small><hr class="m-0">';
                    $returnHTml .= '<b class="text-danger">CREATED :</b>'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small>';
                    $returnHTml .= '</div>';

                    return $returnHTml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->client->name;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<label class="text-primary"> ['.$selectQuery->quotation->quote_number.'] </label>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('jr_number', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->jr_number;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<b>Products</b> : <span class="badge bg-fusion-500 ml-2">'.jobRequestCount($selectQuery->id).'</span>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('agent.user.employee.first_name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->agent->user->employee->first_name." ".$selectQuery->agent->user->employee->last_name;
                    $returnHTml .= '<hr class="m-0">';
                    
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('sales-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            } elseif($postMode=='jr-accomplished-serverside'){
                $selectQuery = JobRequest::with('client')->with('quotation')->with('agent')->where('status','=',$data['status'])->where('agent_id', '=', $agent->id)->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('updated_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center"><b class="text-info">UPDATED :</b>'.date('F d,Y ',strtotime($selectQuery->updated_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->updated_at).']</small><hr class="m-0">';
                    $returnHTml .= '<b class="text-danger">CREATED :</b>'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small>';
                    $returnHTml .= '</div>';

                    return $returnHTml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->client->name;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<label class="text-primary"> ['.$selectQuery->quotation->quote_number.'] </label>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('jr_number', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->jr_number;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<b>Products</b> : <span class="badge bg-fusion-500 ml-2">'.jobRequestCount($selectQuery->id).'</span>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('agent.user.employee.first_name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->agent->user->employee->first_name." ".$selectQuery->agent->user->employee->last_name;
                    $returnHTml .= '<hr class="m-0">';
                    
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('sales-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            } elseif($postMode=='jr-request-reject-serverside'){
                $selectQuery = JobRequest::with('client')->with('quotation')->with('agent')->where('status','=',$data['status'])->where('agent_id', '=', $agent->id)->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('updated_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center"><b class="text-info">UPDATED :</b>'.date('F d,Y ',strtotime($selectQuery->updated_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->updated_at).']</small><hr class="m-0">';
                    $returnHTml .= '<b class="text-danger">CREATED :</b>'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small>';
                    $returnHTml .= '</div>';

                    return $returnHTml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->client->name;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<label class="text-primary"> ['.$selectQuery->quotation->quote_number.'] </label>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('jr_number', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->jr_number;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<b>Products</b> : <span class="badge bg-fusion-500 ml-2">'.jobRequestCount($selectQuery->id).'</span>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('agent.user.employee.first_name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->agent->user->employee->first_name." ".$selectQuery->agent->user->employee->last_name;
                    $returnHTml .= '<hr class="m-0">';
                    
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('sales-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            } elseif($postMode=='jr-rejected-serverside'){
                $selectQuery = JobRequest::with('client')->with('quotation')->with('agent')->where('status','=',$data['status'])->where('agent_id', '=', $agent->id)->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('updated_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center"><b class="text-info">UPDATED :</b>'.date('F d,Y ',strtotime($selectQuery->updated_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->updated_at).']</small><hr class="m-0">';
                    $returnHTml .= '<b class="text-danger">CREATED :</b>'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small>';
                    $returnHTml .= '</div>';

                    return $returnHTml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->client->name;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<label class="text-primary"> ['.$selectQuery->quotation->quote_number.'] </label>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('jr_number', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->jr_number;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<b>Products</b> : <span class="badge bg-fusion-500 ml-2">'.jobRequestCount($selectQuery->id).'</span>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('agent.user.employee.first_name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->agent->user->employee->first_name." ".$selectQuery->agent->user->employee->last_name;
                    $returnHTml .= '<hr class="m-0">';
                    
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('sales-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            } elseif($postMode=='jr-cancelled-serverside'){
                $selectQuery = JobRequest::with('client')->with('quotation')->with('agent')->where('status','=',$data['status'])->where('agent_id', '=', $agent->id)->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('updated_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center"><b class="text-info">UPDATED :</b>'.date('F d,Y ',strtotime($selectQuery->updated_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->updated_at).']</small><hr class="m-0">';
                    $returnHTml .= '<b class="text-danger">CREATED :</b>'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small>';
                    $returnHTml .= '</div>';

                    return $returnHTml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->client->name;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<label class="text-primary"> ['.$selectQuery->quotation->quote_number.'] </label>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('jr_number', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->jr_number;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<b>Products</b> : <span class="badge bg-fusion-500 ml-2">'.jobRequestCount($selectQuery->id).'</span>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('agent.user.employee.first_name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->agent->user->employee->first_name." ".$selectQuery->agent->user->employee->last_name;
                    $returnHTml .= '<hr class="m-0">';
                    
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('sales-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            } elseif($postMode=='jr-declined-serverside'){
                $selectQuery = JobRequest::with('client')->with('quotation')->with('agent')->where('status','=',$data['status'])->where('agent_id', '=', $agent->id)->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('updated_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center"><b class="text-info">UPDATED :</b>'.date('F d,Y ',strtotime($selectQuery->updated_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->updated_at).']</small><hr class="m-0">';
                    $returnHTml .= '<b class="text-danger">CREATED :</b>'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small>';
                    $returnHTml .= '</div>';

                    return $returnHTml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.$selectQuery->client->name;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<label class="text-primary"> ['.$selectQuery->quotation->quote_number.'] </label>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('jr_number', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->jr_number;
                    $returnHTml .= '<hr class="m-0">';
                    $returnHTml .= '<b>Products</b> : <span class="badge bg-fusion-500 ml-2">'.jobRequestCount($selectQuery->id).'</span>';
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->editColumn('agent.user.employee.first_name', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">';
                    $returnHTml .= $selectQuery->agent->user->employee->first_name." ".$selectQuery->agent->user->employee->last_name;
                    $returnHTml .= '<hr class="m-0">';
                    
                    $returnHTml .= '</div>';
                    return $returnHTml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('sales-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            //end gelo
            } elseif($postMode=='jr-view-serverside'){
                $selectQuery = JobRequestProduct::select('job_request_products.id','job_request_products.date_added_revision','job_request_products.product_id','job_request_products.type','job_request_products.parent_id','job_request_products.quotation_product_id','job_request_products.status','job_request_products.deadline_date','job_request_products.designer_name')
                                                ->with('jr_product')->with('jr_quotation_product')
                                                ->with('jr_revisions')
                                                ->where('job_request_products.job_request_id', '=', $data['id'])
                                                ->whereNull('job_request_products.parent_id')
                                                ->where('job_request_products.type','=',$data['product_type'])
                                                ->orderBy('job_request_products.created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->addColumn('details', function($selectQuery) use($user) {
                    $enc_product_id = encryptor('encrypt',$selectQuery->product_id); 
                    $defaultLink = 'no-img';
                    $destination  = 'assets/img/products/'.$enc_product_id.'/';
                    $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                    if($defaultLink=='no-img'){
                        $enc_product_id = encryptor('encrypt',$selectQuery->jr_product->parent_id); 
                        $defaultLink = 'http://placehold.it/754x400';
                        $destination  = 'assets/img/products/'.$enc_product_id.'/';
                        $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                    }
                    $product_name = str_replace('v:','</b><br>',$selectQuery->jr_quotation_product->product_name);
                    $product_name = str_replace('|','<br>',$product_name);
                    $product_name = '<b>'.$product_name.'<br>';

                    $update_desc_button = '';
                    if($selectQuery->status == 'REJECTED') {
                        $update_desc_button = '<button class="btn btn-xs btn-info text-white update-product-description" data-id="'.encryptor('encrypt',$selectQuery->id).'">
                                    <span class="fa fa-edit text-white"></span> Update Description
                                </button><br>';
                    }

                    $description = '';
                    if(!empty($selectQuery->jr_quotation_product->description)){
                        $description = '<tr><td colspan="2">'.$update_desc_button.'
                                            <b>Description : </b>
                                            '.$selectQuery->jr_quotation_product->description.'
                                        </td></tr>';
                    }
                    $deadline = '<font class="text-danger">Unknown</font>';
                    if(!empty($selectQuery->deadline_date)){
                        $deadline = date('F d,Y',strtotime($selectQuery->deadline_date));
                    }
                    $date_revision_added = '<font class="text-danger">Unknown</font>';
                    if(!empty($selectQuery->date_added_revision)){
                        $date_revision_added = date('F d,Y h:i a',strtotime($selectQuery->date_added_revision));
                    }
                    $if_cancelled = '';
                    $avalability = 'warning';
                    if(!empty($selectQuery->jr_quotation_product->cancelled_date)){
                        $avalability = 'danger';
                        $if_cancelled = '<tr style="border: 3px solid #ff090952;box-shadow: 2px 2px 2px 2px #00000052;">
                                            <td style="border: none;">
                                                <h4><b>Cancelled Reason : </b></h4> 
                                            </td>
                                            <td width="68%" style="border: none;">
                                                '.$selectQuery->jr_quotation_product->cancelled_reason.'
                                            </td>
                                        </tr>';
                    }
                    $returnHtml = '<table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-'.$avalability.'-50">
                                            <td width="300px" colspan="2">
                                                <p align="center">Product Code: '.$product_name.'</p></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$if_cancelled.'
                                            <tr>
                                                <td width="30%"><b>Deadline Date : </b>'.$deadline.' </td>
                                                <td rowspan="4" align="center">
                                                    <img src="'.$defaultLink.'" width="40%"><br>
                                                    <a class="btn btn-outline-success m-1 btn-standard waves-effect fixed-image" data-id="'.encryptor('encrypt',$selectQuery->product_id).'"><span class="fa fa-image"></span> Fix Image ?</a>
                                                </td>
                                            </tr>
                                            <tr><td><b>Request Revision Date : </b><br>'.$date_revision_added.'</td></tr>
                                            <tr><td><b>Status : </b>'.$selectQuery->status.'</td></tr>
                                            <tr><td><b>Quantity : </b>'.$selectQuery->jr_quotation_product->qty.'<br></td></tr>
                                            '.$description.'
                                        </tbody>
                                    </table>';
                    return $returnHtml;
                })
                ->addColumn('revision', function($selectQuery) use($user) {
                    $returnHtml = '';
                    $disabled = '';
                    if($selectQuery->status == 'REJECTED') {
                        $disabled = 'disabled';
                    }
                    if(empty($selectQuery->jr_quotation_product->cancelled_date)){
                        $returnHtml = '<div align="right">
                                <button class="btn btn-danger text-white add-revision" data-id="'.encryptor('encrypt',$selectQuery->id).'" '.$disabled.'>
                                    <span class="fa fa-plus text-white"></span>
                                    Add Type / Revision
                                </button>
                            </div>';
                    }
                    if(count($selectQuery->jr_revisions)!=0){
                        foreach($selectQuery->jr_revisions as $revision){
                            $actual_finish = '<font class="text-danger">Unknown</font>';
                            if(!empty($revision->actual_date_finished)){
                                $actual_finish = date('F d,Y h:i A',strtotime($revision->actual_date_finished));
                            }

                            //start gelo added
                            $end = '<font class="text-danger">Unknown</font>';
                            if(!empty($revision->actual_date_finished)){
                                $end = date('F d,Y h:i A',strtotime($revision->actual_date_finished));
                            }

                            $start = '<font class="text-danger">This revision is not yet started by designer.</font>';
                            if(!empty($revision->date_started)) {
                                $start = date('F d,Y h:i A',strtotime($revision->date_started));
                            }

                            $estimated_finish = '<font class="text-danger">Unknown</font>';
                            if(!empty($revision->estimated_finish)){
                                $estimated_finish = date('F d,Y h:i A',strtotime($revision->estimated_finish));
                            }

                            $designer_assign = 'No Designer Assigned Yet';
                            if(!empty($revision->designer_name)){
                                $designer_assign = '
                                <div class="row">
                                    <div class="col-md-7">
                                        <b>Date Assigned</b>: '.date('F d,Y h:i a',strtotime($revision->date_assigned)).'<br>
                                        <b>Designer</b>: '.$revision->designer_name.'<br>
                                        <b>Assigned Task</b>: '.$revision->assigned_task.'<br>
                                        <font style="font-weight:bold;">Status</font>: '.$revision->status.'<br>
                                    </div>
                                    <div class="col-md-5">
                                        <font style="font-weight:bold;">Estimated Finish</font>: '.$estimated_finish.'<br>
                                        <font style="font-weight:bold;">Actual START</font>: '.$start.'<br>
                                        <font style="font-weight:bold;">Actual END</font>: '.$end.'
                                    </div>
                                </div>          
                                ';
                            }

                            $delete_button = '<a class="btn btn-icon btn-secondary waves-effect rounded-circle mr-1 delete-revision" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Delete Revision">
                                            <span class="fa fa-trash"></span>
                                        </a>';
                            if(!empty($revision->designer_name)){
                                if(!empty($revision->date_started)) {
                                    $delete_button = '';
                                }
                            }
                            //end gelo added
                            $avalability = 'warning';
                            $if_cancelled = '';
                            $update_remarks_button = '';
                            if(!empty($revision->date_cancelled) || !empty($revision->date_rejected)){
                                $delete_button = '';
                                $avalability = 'danger';

                                if($revision->status == 'REJECTED') {
                                    $update_remarks_button = '<br>
                                            <button class="btn btn-xs btn-info text-white update-revision-remarks" data-id="'.encryptor('encrypt',$revision->id).'">
                                                <span class="fa fa-edit text-white"></span> Update Remarks
                                            </button>';
                                }

                                if($revision->status == 'DECLINED') {
                                    $update_remarks_button = '<br>
                                        <button class="btn btn-xs btn-success text-white done-update-revision" data-id="'.encryptor('encrypt',$revision->id).'">
                                            <span class="fa fa-check text-white"></span> Done Updating the Revision ?
                                        </button>';
                                }

                                if(!empty($revision->date_cancelled)) {
                                    $if_cancelled = '<tr style="border: 3px solid #ff090952;box-shadow: 2px 2px 2px 2px #00000052;">
                                            <td style="border: none;">
                                                <h4><b>Cancelled Reason : </b></h4> 
                                            </td>
                                            <td width="68%" style="border: none;">
                                                '.$revision->cancelled_reason.'
                                            </td>
                                        </tr>';
                                }

                                if(!empty($revision->date_rejected)) {
                                    $if_cancelled = '<tr style="border: 3px solid #ff090952;box-shadow: 2px 2px 2px 2px #00000052;">
                                            <td style="border: none;">
                                                <h4><b>Rejected Reason : </b></h4> 
                                            </td>
                                            <td width="68%" style="border: none;">
                                                '.$revision->reject_reason.'
                                            </td>
                                        </tr>';
                                }
                            }
                           
                            $returnHtml .= '
                                <div class="form-group mt-3" id="'.encryptor('encrypt',$revision->id).'">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr class="bg-'.$avalability.'-50">
                                                <th colspan="2">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <h3><b>'.$revision->jr_type->name.'</b></h3>
                                                        </div>
                                                        <div class="col-lg-6" align="right">
                                                            '.$delete_button.'
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                            '.$if_cancelled.'
                                            <tr>
                                                <td colspan="2">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <font style="font-weight:bold;">Deadline Date</font> : '.date('F d,Y',strtotime($revision->deadline_date)).'<br>
                                                            <font style="font-weight:bold;">Actual Date Finished</font> : '.$actual_finish.'
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <font style="font-weight:bold;">Remarks</font> : '.$revision->remarks.'
                                                            '.$update_remarks_button.'
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    '.$designer_assign.'
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        ';
                        }
                    }
                    return $returnHtml;
                })
                ->setRowClass(function ($selectQuery) {
                    if(!empty($selectQuery->jr_quotation_product->cancelled_date)){
                        return 'alert-danger';
                    }
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            //start gelo added
            }elseif($postMode=='jr-floor-plan-serverside'){
                 $selectQuery = JobRequestProduct::select('job_request_products.id','job_request_products.date_added_revision','job_request_products.product_id','job_request_products.type','job_request_products.parent_id','job_request_products.quotation_product_id','job_request_products.status','job_request_products.deadline_date','job_request_products.designer_name')
                                                ->with('jr_product')
                                                ->with('jr_quotation_product')
                                                ->with('jr_revisions')
                                                ->where('job_request_products.job_request_id', '=', $data['id'])
                                                ->whereNull('job_request_products.parent_id')
                                                ->where('job_request_products.type','=','FIT-OUT')
                                                ->where('status', '!=', 'CANCELLED')
                                                ->orderBy('job_request_products.created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->addColumn('details', function($selectQuery) use($user) {
                    if(!empty($selectQuery->jr_quotation_product)) {
                        $enc_product_id = encryptor('encrypt',$selectQuery->product_id); 
                        $defaultLink = 'no-img';
                        $destination  = 'assets/img/products/'.$enc_product_id.'/';
                        $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                        if($defaultLink=='no-img'){
                            $enc_product_id = encryptor('encrypt',$selectQuery->jr_product->parent_id); 
                            $defaultLink = 'http://placehold.it/754x400';
                            $destination  = 'assets/img/products/'.$enc_product_id.'/';
                            $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                        }
                        $product_name = str_replace('v:','</b><br>',$selectQuery->jr_quotation_product->product_name);
                        $product_name = str_replace('|','<br>',$product_name);
                        $product_name = '<b>'.$product_name.'<br>';

                        $table_title = '<p align="center">Product Code: '.$product_name.'</p>';
                        $image_content = '<img src="'.$defaultLink.'" width="40%"><br>
                            <a class="btn btn-outline-success m-1 btn-standard waves-effect fixed-image" data-id="'.encryptor('encrypt',$selectQuery->product_id).'"><span class="fa fa-image"></span> Fix Image ?</a>';
                        $quantity = '<tr><td><b>Quantity : </b>'.$selectQuery->jr_quotation_product->qty.'<br></td></tr>';

                        $update_desc_button = '';
                        if($selectQuery->status == 'REJECTED') {
                            $update_desc_button = '<button class="btn btn-xs btn-info text-white update-product-description" data-id="'.encryptor('encrypt',$selectQuery->id).'">
                                        <span class="fa fa-edit text-white"></span> Update Description
                                    </button><br>';
                        }

                        $description = '';
                        if(!empty($selectQuery->jr_quotation_product->description)){
                            $description = '<tr><td colspan="2">'.$update_desc_button.'
                                                <b>Description : </b>
                                                '.$selectQuery->jr_quotation_product->description.'
                                            </td></tr>';
                        }
                        $deadline = '<font class="text-danger">Unknown</font>';
                        if(!empty($selectQuery->deadline_date)){
                            $deadline = date('F d,Y',strtotime($selectQuery->deadline_date));
                        }
                        $date_revision_added = '<font class="text-danger">Unknown</font>';
                        if(!empty($selectQuery->date_added_revision)){
                            $date_revision_added = date('F d,Y h:i a',strtotime($selectQuery->date_added_revision));
                        }
                        $if_cancelled = '';
                        $rowspan = 4;
                        $avalability = 'warning';
                        if(!empty($selectQuery->jr_quotation_product->cancelled_date)) {
                            $avalability = 'danger';
                            $if_cancelled = '<tr style="border: 3px solid #ff090952;box-shadow: 2px 2px 2px 2px #00000052;">
                                                <td style="border: none;">
                                                    <h4><b>Cancelled Reason : </b></h4> 
                                                </td>
                                                <td width="68%" style="border: none;">
                                                    '.$selectQuery->jr_quotation_product->cancelled_reason.'
                                                </td>
                                            </tr>';
                        }
                    } else {
                        $defaultLink = 'http://placehold.it/754x400';
                        $destination = 'assets/img/job_request_products/floor_plan/';
                        $filename = encryptor('encrypt', $selectQuery->id);
                        $defaultLink = imagePath($destination.''.$filename, $defaultLink);
                        if($defaultLink == 'http://placehold.it/754x400'){
                            $image_content = '<a class="btn btn-outline-success m-1 btn-standard waves-effect add-floor-plan-image" data-id="'.encryptor('encrypt', $selectQuery->id).'"><span class="fa fa-image"></span> Add Image ?</a>';
                        } else {
                            $image_content = '<img src="'.$defaultLink.'" width="40%"><br>
                            <a class="btn btn-outline-success m-1 btn-standard waves-effect update-floor-plan-image" data-id="'.encryptor('encrypt', $selectQuery->id).'"><span class="fa fa-image"></span> Update Image ?</a>';
                        }

                        $avalability = 'warning';
                        $delete_fp_button = '';
                        if($selectQuery->status == 'PENDING') {
                            $delete_fp_button = '<a class="btn btn-icon btn-secondary waves-effect rounded-circle mr-1 float-right delete-floor-plan" data-actiontype="Delete" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Delete this Floor Plan ?">
                                    <span class="fa fa-trash"></span>
                                </a>';
                        }

                        $table_title = '<p align="center"><b>Floor Plan</b> '.$delete_fp_button.'</p>';
                        $quantity = '';
                        $description = '';
                        if(!empty($selectQuery->remarks)){
                            $description = '<tr><td colspan="2"><b>Description : </b> <br>
                                                '.$selectQuery->remarks.'
                                            </td></tr>';
                        }
                        $deadline = '<font class="text-danger">Unknown</font>';
                        if(!empty($selectQuery->deadline_date)){
                            $deadline = date('F d,Y',strtotime($selectQuery->deadline_date));
                        }
                        $date_revision_added = '<font class="text-danger">Unknown</font>';
                        if(!empty($selectQuery->date_added_revision)){
                            $date_revision_added = date('F d,Y h:i a',strtotime($selectQuery->date_added_revision));
                        }
                        $if_cancelled = '';
                        $rowspan = 3;
                    }
                    
                    $returnHtml = '<table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-'.$avalability.'-50">
                                            <td width="300px" colspan="2">'.$table_title.'</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$if_cancelled.'
                                            <tr>
                                                <td width="30%"><b>Deadline Date : </b>'.$deadline.' </td>
                                                <td rowspan="'.$rowspan.'" align="center">
                                                    '.$image_content.'
                                                </td>
                                            </tr>
                                            <tr><td><b>Request Revision Date : </b><br>'.$date_revision_added.'</td></tr>
                                            <tr><td><b>Status : </b>'.$selectQuery->status.'</td></tr>
                                            '.$quantity.'
                                            '.$description.'
                                        </tbody>
                                    </table>';
                    return $returnHtml;
                })
                ->addColumn('revision', function($selectQuery) use($user) {
                    $returnHtml = '';
                    $disabled = '';
                    if($selectQuery->status == 'REJECTED') {
                        $disabled = 'disabled';
                    }
                    if(empty($selectQuery->jr_quotation_product->cancelled_date)){
                        $returnHtml = '<div align="right">
                                <button class="btn btn-danger text-white add-revision" data-id="'.encryptor('encrypt',$selectQuery->id).'" '.$disabled.'>
                                    <span class="fa fa-plus text-white"></span>
                                    Add Type / Revision
                                </button>
                            </div>';
                    }
                        
                    if(count($selectQuery->jr_revisions)!=0){
                        foreach($selectQuery->jr_revisions as $revision){
                            $actual_finish = '<font class="text-danger">Unknown</font>';
                            if(!empty($revision->actual_date_finished)){
                                $actual_finish = date('F d,Y h:i A',strtotime($revision->actual_date_finished));
                            }

                            $end = '<font class="text-danger">Unknown</font>';
                            if(!empty($revision->actual_date_finished)){
                                $end = date('F d,Y h:i A',strtotime($revision->actual_date_finished));
                            }

                            $start = '<font class="text-danger">This revision is not yet started by designer.</font>';
                            if(!empty($revision->date_started)) {
                                $start = date('F d,Y h:i A',strtotime($revision->date_started));
                            }

                            $estimated_finish = '<font class="text-danger">Unknown</font>';
                            if(!empty($revision->estimated_finish)){
                                $estimated_finish = date('F d,Y h:i A',strtotime($revision->estimated_finish));
                            }

                            $designer_assign = 'No Designer Assigned Yet';
                            if(!empty($revision->designer_name)){
                                $designer_assign = '
                                <div class="row">
                                    <div class="col-md-7">
                                        <b>Date Assigned</b>: '.date('F d,Y h:i a',strtotime($revision->date_assigned)).'<br>
                                        <b>Designer</b>: '.$revision->designer_name.'<br>
                                        <b>Assigned Task</b>: '.$revision->assigned_task.'<br>
                                        <font style="font-weight:bold;">Status</font>: '.$revision->status.'<br>
                                    </div>
                                    <div class="col-md-5">
                                        <font style="font-weight:bold;">Estimated Finish</font>: '.$estimated_finish.'<br>
                                        <font style="font-weight:bold;">Actual START</font>: '.$start.'<br>
                                        <font style="font-weight:bold;">Actual END</font>: '.$end.'
                                    </div>
                                </div>          
                                ';
                            }
                            $delete_button = '<a class="btn btn-icon btn-secondary waves-effect rounded-circle mr-1 delete-revision" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Delete Revision">
                                            <span class="fa fa-trash"></span>
                                        </a>';
                            if(!empty($revision->designer_name)){
                                if(!empty($revision->date_started)) {
                                    $delete_button = '';
                                }
                            }
                            $avalability = 'warning';
                            $if_cancelled = '';
                            $update_remarks_button = '';
                            if(!empty($revision->date_cancelled) || !empty($revision->date_rejected)){
                                $delete_button = '';
                                $avalability = 'danger';

                                if($revision->status == 'REJECTED') {
                                    $update_remarks_button = '<br>
                                        <button class="btn btn-xs btn-info text-white update-revision-remarks" data-id="'.encryptor('encrypt',$revision->id).'">
                                            <span class="fa fa-edit text-white"></span> Update Remarks
                                        </button>';
                                }

                                if($revision->status == 'DECLINED') {
                                    $update_remarks_button = '<br>
                                        <button class="btn btn-xs btn-success text-white done-update-revision" data-id="'.encryptor('encrypt',$revision->id).'">
                                            <span class="fa fa-check text-white"></span> Done Updating the Revision ?
                                        </button>';
                                }

                                if(!empty($revision->date_cancelled)) {
                                    $if_cancelled = '<tr style="border: 3px solid #ff090952;box-shadow: 2px 2px 2px 2px #00000052;">
                                            <td style="border: none;">
                                                <h4><b>Cancelled Reason : </b></h4> 
                                            </td>
                                            <td width="68%" style="border: none;">
                                                '.$revision->cancelled_reason.'
                                            </td>
                                        </tr>';
                                }

                                if(!empty($revision->date_rejected)) {
                                    $if_cancelled = '<tr style="border: 3px solid #ff090952;box-shadow: 2px 2px 2px 2px #00000052;">
                                            <td style="border: none;">
                                                <h4><b>Rejected Reason : </b></h4> 
                                            </td>
                                            <td width="68%" style="border: none;">
                                                '.$revision->reject_reason.'
                                            </td>
                                        </tr>';
                                }
                            }
                           
                            $returnHtml .= '
                                <div class="form-group mt-3" id="'.encryptor('encrypt',$revision->id).'">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr class="bg-'.$avalability.'-50">
                                                <th colspan="2">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <h3><b>'.$revision->jr_type->name.'</b></h3>
                                                        </div>
                                                        <div class="col-lg-6" align="right">
                                                            '.$delete_button.'
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                            '.$if_cancelled.'
                                            <tr>
                                                <td colspan="2">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <font style="font-weight:bold;">Deadline Date</font> : '.date('F d,Y',strtotime($revision->deadline_date)).'<br>
                                                            <font style="font-weight:bold;">Actual Date Finished</font> : '.$actual_finish.'
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <font style="font-weight:bold;">Remarks</font> : '.$revision->remarks.'
                                                            '.$update_remarks_button.'
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    '.$designer_assign.'
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        ';
                        }
                    }
                    return $returnHtml;
                })
                ->setRowClass(function ($selectQuery) {
                    if(!empty($selectQuery->jr_quotation_product->cancelled_date)){
                        return 'alert-danger';
                    }
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            //end gelo added
            } elseif($postMode=='add-revision'){
                $attributes = [
                    'jr_type' => 'Job Request Type',
                    'deadline' => 'Deadline',
                    'remarks' => 'Remarks'
                ];
                $rules = [
                    'jr_type' => 'required',
                    'deadline' => 'required',
                    'remarks' => 'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    //start gelo
                    //check status of revisions
                    $dec_id = encryptor('decrypt',$data['id']);
                    $updateJRParentStatus = JobRequestProduct::find($dec_id);
                    $selectJRProductRevisions = JobRequestProduct::where('parent_id', '=', $dec_id)->whereNull('date_cancelled')->get();
                    $jr_revision_status = [];
                    foreach($selectJRProductRevisions as $jrp_revisions) {
                        if($jrp_revisions->status != 'ACCOMPLISHED') {
                            $ret = 0;
                        } else {
                            $ret = 1;
                        }
                        array_push($jr_revision_status, $ret);
                    }
                    if(in_array('0', $jr_revision_status)) {
                        //not all revision are accomplished
                    } else {
                        //all revision are accomplished udpdate jrp_parent status
                        $updateJRParentStatus->status = 'ONGOING';
                        $updateJRParentStatus->updated_by = $user->id;
                        $updateJRParentStatus->updated_at = getDatetimeNow();
                        if($updateJRParentStatus->save()) {
                            $updateJR = JobRequest::where('id', '=', $updateJRParentStatus->job_request_id)->first();
                            $updateJR->updated_by = $user->id;
                            $updateJR->updated_at = getDatetimeNow();
                            if($updateJR->save()) {
                                // 'Job Request status updated';
                            } else {
                                return array('success' => 0, 'message'=>'Error acquired while updating JR status!');
                            }
                        } else {
                            return array('success' => 0, 'message'=>'Error acquired while updating JR Product status!');
                        }
                    }
                    //end gelo

                    $updateDeadline = JobRequestProduct::find($dec_id);
                    $insertRevision = new JobRequestProduct();
                    $insertRevision->job_request_id = $updateDeadline->job_request_id;
                    $insertRevision->quotation_product_id = $updateDeadline->quotation_product_id;
                    $insertRevision->agent_id = $updateDeadline->agent_id;
                    $insertRevision->type = $updateDeadline->type;
                    $insertRevision->product_id = $updateDeadline->product_id;
                    $insertRevision->created_by = $user->id;
                    $insertRevision->updated_by = $user->id;
                    $insertRevision->created_at = getDatetimeNow();
                    $insertRevision->updated_at = getDatetimeNow();
                    $insertRevision->remarks = $data['remarks'];
                    $insertRevision->deadline_date = $data['deadline'];
                    $insertRevision->job_request_type_id = $data['jr_type'];
                    $insertRevision->parent_id = encryptor('decrypt',$data['id']);
                    if($insertRevision->save()){
                        $selectJRProducts = JobRequestProduct::where('parent_id','=',$insertRevision->parent_id)->whereNull('date_cancelled')->get();
                        if(count($selectJRProducts)>0){
                            $new_deadline = strtotime($insertRevision->deadline_date);
                            foreach($selectJRProducts as $selectJRProduct){
                                $exist_deadline = strtotime($selectJRProduct->deadline_date);
                                if($new_deadline>$exist_deadline){
                                    $new_deadline = $new_deadline;
                                }else{
                                    $new_deadline = $exist_deadline;
                                }
                            }
                            $new_deadline = date('Y-m-d',$new_deadline);
                            $updateDeadline->deadline_date = $new_deadline;
                            $updateDeadline->date_added_revision =  getDatetimeNow();
                            $updateDeadline->updated_by = $user->id;
                            $updateDeadline->updated_at = getDatetimeNow();
                            if($updateDeadline->save()){
                                return array('success'=>1,'message'=>'Successfuly Added!','jrtype'=>$updateDeadline->type,'countFP'=>1);
                            } else {
                                return array('success'=>0,'message'=>'Error acquired while adding revision!');
                            }
                        }else{
                            $updateDeadline->deadline_date = $insertRevision->deadline_date;
                            $updateDeadline->updated_by = $user->id;
                            $updateDeadline->updated_at = getDatetimeNow();
                            if($updateDeadline->save()){
                                return array('success'=>1,'message'=>'Successfuly Added!','jrtype'=>$updateDeadline->type,'countFP'=>1);
                            } else {
                                return array('success'=>0,'message'=>'Error acquired while adding revision!');
                            }
                        }
                    } else {
                        return array('success'=>0,'message'=>'Error acquired while adding revision!');
                    }
                }
            }elseif($postMode=='cancel-revision'){
                $id = encryptor('decrypt',$data['id']);
                $cancelQuery = JobRequestProduct::find($id);
                $cancelQuery->date_cancelled = date('Y-m-d');
                $cancelQuery->cancelled_by = $user->id;
                $cancelQuery->status = 'CANCELLED';
                $cancelQuery->updated_by = $user->id;
                $cancelQuery->updated_at = getDatetimeNow();
                if($data['deltype']==1){
                    $msg = 'Successfuly Cancelled!';
                    $cancelQuery->cancelled_reason = $data['reason_cancelled'];
                } else {
                    $msg = 'Successfuly Deleted!';
                }
                if($cancelQuery->save()){
                    $selectJRProducts = JobRequestProduct::where('parent_id','=',$cancelQuery->parent_id)->whereNull('date_cancelled')->get();
                    $selectQuery = JobRequestProduct::find($cancelQuery->parent_id);
                    $new_deadline = 0;
                    if(count($selectJRProducts)>0){
                        foreach($selectJRProducts as $selectJRProduct){
                            $exist_deadline = strtotime($selectJRProduct->deadline_date);
                            if($new_deadline>$exist_deadline){
                                $new_deadline = $new_deadline;
                            }else{
                                $new_deadline = $exist_deadline;
                            }
                        }
                        $selectQuery->deadline_date = date('Y-m-d',$new_deadline);
                    }else{
                        $selectQuery->deadline_date = null;
                        $selectQuery->date_added_revision = null;
                    }

                    $selectQuery->updated_by = $user->id;
                    $selectQuery->updated_at = getDatetimeNow();
                    if($selectQuery->save()){
                        return array('success' => 1,'message'=>$msg,'jrtype'=>$selectQuery->type, 'countFP' => 1);
                    }else{
                        return array('success' => 0,'message'=>'Error Acquired!');
                    }
                }else{
                    return array('success' => 0,'message'=>'Error Acquired!');
                }
            //start gelo added
            }elseif($postMode=='add-floor-plan'){
                $attributes = [
                    'jr_type' => 'Job Request Type',
                    'deadline' => 'Deadline',
                    'remarks' => 'Remarks'
                ];
                $rules = [
                    'jr_type' => 'required',
                    'deadline' => 'required',
                    'remarks' => 'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $jr_id = encryptor('decrypt',$data['id']);
                    $selectJR = JobRequest::find($jr_id);
                    $countFPJrProduct = JobRequestProduct::where('job_request_id', $selectJR->id)
                                                            ->where('type', '=', 'FIT-OUT')
                                                            ->count();

                    $insertFloorPlan = new JobRequestProduct();
                    $insertFloorPlan->job_request_id = $selectJR->id;
                    $insertFloorPlan->job_request_type_id = $data['jr_type'];

                    $insertFloorPlan->agent_id = $selectJR->agent_id;
                    
                    $insertFloorPlan->type = 'FIT-OUT';
                    $insertFloorPlan->remarks = NULL;
                    $insertFloorPlan->deadline_date = $data['deadline'];
                    $insertFloorPlan->date_added_revision =  getDatetimeNow();
                    $insertFloorPlan->created_by = $user->id;
                    $insertFloorPlan->updated_by = $user->id;
                    $insertFloorPlan->created_at = getDatetimeNow();
                    $insertFloorPlan->updated_at = getDatetimeNow();
                    if($insertFloorPlan->save()){
                        $insertRevision = new JobRequestProduct();
                        $insertRevision->job_request_id = $selectJR->id;
                        $insertRevision->job_request_type_id = $data['jr_type'];
                        $insertRevision->parent_id = $insertFloorPlan->id;
                        $insertRevision->agent_id = $selectJR->agent_id;
                        $insertRevision->type = 'FIT-OUT';
                        $insertRevision->remarks = $data['remarks'];
                        $insertRevision->deadline_date = $data['deadline'];
                        $insertRevision->created_by = $user->id;
                        $insertRevision->updated_by = $user->id;
                        $insertRevision->created_at = getDatetimeNow();
                        $insertRevision->updated_at = getDatetimeNow();
                        if($insertRevision->save()) {
                            return array('success' => 1,'message' => 'Successfuly Added!', 'jrtype'=> 'FIT-OUT', 'countFP' => $countFPJrProduct);
                        } else {
                            return array('success' => 0,'message' => 'Unable to add floor plan, please try again', 'jrtype' => 'FIT-OUT');
                        }
                    }else{
                        return array('success' => 0,'message' => 'Unable to add floor plan, please try again', 'jrtype' => 'FIT-OUT');
                    }
                }
            }elseif($postMode=='add-designer'){
                $attributes = [
                    'designer' => 'Designer',
                    'task' => 'Task',
                    'estimated_date' => 'Estimated Date',
                    'estimated_time' => 'Estimated Time'
                ];
                $rules = [
                    'designer' => 'required',
                    'task' => 'required',
                    'estimated_date' => 'required',
                    'estimated_time' => 'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $jr_prod_id = encryptor('decrypt', $data['id']);
                    $estimated_datetime = $data['estimated_date'].' '.$data['estimated_time'];
                    $estimated_finish = date('Y-m-d H:i:s', strtotime($estimated_datetime));
                    $updateJRProd = JobRequestProduct::find($jr_prod_id);
                    if(!empty($updateJRProd)) {
                        $selectDesigner =  User::where('id', '=', $data['designer'])->with('employee')->first();
                        $designer_name = $selectDesigner->employee->first_name.' '.$selectDesigner->employee->last_name;
                        $updateJRProd->date_assigned = getDatetimeNow();
                        $updateJRProd->designer_id = $selectDesigner->id;
                        $updateJRProd->designer_name = $designer_name;
                        $updateJRProd->assigned_task = $data['task'];
                        $updateJRProd->status = 'PENDING';
                        $updateJRProd->estimated_finish = $estimated_finish;
                        $updateJRProd->updated_by = $user->id;
                        $updateJRProd->updated_at = getDatetimeNow();
                        if($updateJRProd->save()){
                            $selectJRProductParent = JobRequestProduct::where('id', $updateJRProd->parent_id)->first();
                            if($selectJRProductParent->status != 'ONGOING') {
                                $selectJRProductParent->status = 'ONGOING';
                                $selectJRProductParent->updated_by = $user->id;
                                $selectJRProductParent->updated_at = getDatetimeNow();
                                if($selectJRProductParent->save()) {
                                    $selectJR = JobRequest::where('id', $selectJRProductParent->job_request_id)->first();
                                    $selectJR->status = 'ONGOING';
                                    $selectJR->updated_by = $user->id;
                                    $selectJR->updated_at = getDatetimeNow();
                                    if($selectJR->save()) {
                                        return array('success' => 1,'message' => 'Successfuly Added!', 'jrtype'=> $updateJRProd->type, 'countFP' => 1);
                                    } else {
                                        return array('success' => 0,'message' => 'Unable to add designer, please try again', 'jrtype' => $updateJRProd->type);
                                    }
                                } else {
                                    return array('success' => 0,'message' => 'Unable to add designer, please try again', 'jrtype' => $updateJRProd->type);
                                }
                            } else {
                                $selectJR = JobRequest::where('id', $selectJRProductParent->job_request_id)->first();
                                if($selectJR->status != 'ONGOING') {
                                    $selectJR->status = 'ONGOING';
                                    $selectJR->updated_by = $user->id;
                                    $selectJR->updated_at = getDatetimeNow();
                                    if($selectJR->save()) {
                                        return array('success' => 1,'message' => 'Successfuly Added!', 'jrtype'=> $updateJRProd->type, 'countFP' => 1);
                                    } else {
                                        return array('success' => 0,'message' => 'Unable to add designer, please try again', 'jrtype' => $updateJRProd->type);
                                    }
                                } else {
                                    return array('success' => 1,'message' => 'Successfuly Added!', 'jrtype'=> $updateJRProd->type, 'countFP' => 1);
                                }
                            }
                        }else{
                            return array('success' => 0,'message' => 'Unable to add designer, please try again', 'jrtype' => $updateJRProd->type);
                        }
                    } else {
                        return array('success' => 0,'message' => 'Job request product not found!', 'jrtype' => '');
                    }
                }
            }elseif($postMode=='delete-floor-plan'){
                $id = encryptor('decrypt',$data['id']);
                $action = $data['action'];
                $JRProductQuery = JobRequestProduct::find($id);
                $JRProductChild = JobRequestProduct::where('parent_id', '=', $id)->get();
                $JRProductQuery->date_cancelled = date('Y-m-d');
                $JRProductQuery->cancelled_by = $user->id;
                $JRProductQuery->status = 'CANCELLED';
                $JRProductQuery->updated_by = $user->id;
                $JRProductQuery->updated_at = getDatetimeNow();
                if($JRProductQuery->save()){
                    if(count($JRProductChild) > 0) {
                        $arr_resp = [];
                        foreach($JRProductChild as $jr_product) {
                            $jr_product->date_cancelled = date('Y-m-d');
                            $jr_product->cancelled_by = $user->id;
                            $jr_product->status = 'CANCELLED';
                            $jr_product->updated_by = $user->id;
                            $jr_product->updated_at = getDatetimeNow();
                            if($jr_product->save()) {
                                array_push($arr_resp, '1');
                            } else {
                                array_push($arr_resp, '0');
                            }
                        }
                        if(in_array('0', $arr_resp)) {
                            return array('success' => 0,'message'=>'Error Acquired!');
                        } else {
                            return array('success' => 1,'message'=>'Floor plan has been deleted.','jrtype' => $JRProductQuery->type, 'countFP' => 1);
                        }
                    } else {
                        return array('success' => 1,'message'=>'Floor plan has been deleted.','jrtype' => $JRProductQuery->type, 'countFP' => 1);
                    }
                } else {
                    return array('success' => 0,'message'=>'Error Acquired!');
                }
            }elseif($postMode == 'fetch-quotation-details') {
                $id = encryptor('decrypt', $data['id']);
                $quotation_id = encryptor('decrypt', $data['quotation_id']);
                $quotation_products = QuotationProduct::where('quotation_id', '=', $quotation_id)
                                                    ->where('id', '!=', $id)
                                                    ->whereNull('cancelled_date')
                                                    ->get();
                $getQuotationDetails = Quotation::find($quotation_id);
                
                if(count($quotation_products) > 0) {
                    $new_subtotal = 0;
                    $new_product_discount = 0;
                    foreach($quotation_products as $qproduct) {
                        $new_subtotal += floatval($qproduct->total_price);
                        $new_product_discount += floatval($qproduct->discount);
                    }
                    $new_subtotal = floatval($new_subtotal) + floatval($data['new_total_price']);
                    $installation_charge = number_format((float)$getQuotationDetails->installation_charge,0, '.', '');
                    $delivery_charge = number_format((float)$getQuotationDetails->delivery_charge,0, '.', '');
                    $new_product_discount = floatval($new_product_discount) + floatval($data['product_discount']);
                    $new_quotation_discount = number_format((float)$data['discount_quotation'],0, '.', '');
                    $new_total_discount = floatval($new_product_discount) + floatval($new_quotation_discount);
                    $new_grand_total = floatval($new_subtotal) + floatval($installation_charge) + floatval($delivery_charge) - floatval($new_total_discount);
                } else {
                    $new_subtotal = $data['new_total_price'];
                    $installation_charge = number_format((float)$getQuotationDetails->installation_charge,0, '.', '');
                    $delivery_charge = number_format((float)$getQuotationDetails->delivery_charge,0, '.', '');
                    $new_product_discount = $data['product_discount'];
                    $new_quotation_discount = number_format((float)$data['discount_quotation'],0, '.', '');
                    $new_total_discount = floatval($new_product_discount) + floatval($new_quotation_discount); //150 + 100 = 250
                    $new_grand_total = floatval($new_subtotal) + floatval($installation_charge) + floatval($delivery_charge) - floatval($new_total_discount);
                }

                $datas = array(
                    'sub_total'=>$new_subtotal,
                    'installation_charge'=>$installation_charge,
                    'delivery_charge'=>$delivery_charge,
                    'total_product_discount'=>$new_product_discount,
                    'discount'=>$new_quotation_discount,
                    'total_discount'=>$new_total_discount,
                    'grand_total'=>$new_grand_total,
                    'temp_grand_total'=>number_format($new_grand_total,2),
                    'last_added'=> encryptor('decrypt',$data['id'])
                );
                return $datas;
            } elseif($postMode == 'update-revision-remarks') {
                $attributes = [
                    'deadline' => 'Deadline',
                    'remarks' => 'Remarks'
                ];
                $rules = [
                    'deadline' => 'required',
                    'remarks' => 'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $dec_id = encryptor('decrypt',$data['id']);
                    $updateRevision = JobRequestProduct::find($dec_id);
                    $updateRevision->status = 'DECLINED';
                    $updateRevision->remarks = $data['remarks'];
                    $updateRevision->deadline_date = $data['deadline'];
                    $updateRevision->updated_by = $user->id;
                    $updateRevision->updated_at = getDatetimeNow();
                    if($updateRevision->save()){
                        $updateDeadline = JobRequestProduct::where('id', '=', $updateRevision->parent_id)->first();
                        $selectJRProducts = JobRequestProduct::where('parent_id','=', $updateRevision->parent_id)->whereNull('date_cancelled')->get();
                        if(count($selectJRProducts)>0){
                            $new_deadline = strtotime($updateRevision->deadline_date);
                            foreach($selectJRProducts as $selectJRProduct){
                                $exist_deadline = strtotime($selectJRProduct->deadline_date);
                                if($new_deadline>$exist_deadline){
                                    $new_deadline = $new_deadline;
                                }else{
                                    $new_deadline = $exist_deadline;
                                }
                            }
                            $new_deadline = date('Y-m-d',$new_deadline);
                            $updateDeadline->deadline_date = $new_deadline;
                            $updateDeadline->date_added_revision =  getDatetimeNow();
                            $updateDeadline->updated_by = $user->id;
                            $updateDeadline->updated_at = getDatetimeNow();
                            if($updateDeadline->save()){
                                return array('success' => 1,'message'=>'Successfuly Updated!','jrtype'=>$updateDeadline->type, 'countFP' => 1);
                            }
                        }else{
                            $updateDeadline->deadline_date = $updateRevision->deadline_date;
                            $updateDeadline->updated_by = $user->id;
                            $updateDeadline->updated_at = getDatetimeNow();
                            if($updateDeadline->save()){
                                return array('success' => 1,'message'=>'Successfuly Added!','jrtype'=>$updateRevision->type, 'countFP' => 1);
                            }
                        }
                    } else {
                        return array('success' => 0, 'message' => 'Unable to update revision, please try again');
                    }
                }
            } elseif($postMode == 'done-update-revision') {
                $id = encryptor('decrypt', $data['id']);
                $updateJRProductChild = JobRequestProduct::where('id', '=', $id)->first();
                $updateJRProductChild->status = 'ONGOING';
                $updateJRProductChild->date_rejected = NULL;
                $updateJRProductChild->updated_by = $user->id;
                $updateJRProductChild->updated_at = getDatetimeNow();
                if($updateJRProductChild->save()) {
                    $updateJRProductParent = JobRequestProduct::where('id', '=', $updateJRProductChild->parent_id)->first();
                    $updateJRProductParent->status = 'ONGOING';
                    $updateJRProductParent->updated_by = $user->id;
                    $updateJRProductParent->updated_at = getDatetimeNow();
                    if($updateJRProductParent->save()) {
                        return array('success' => 1,'message'=>'Job Request Product is now Ongoing','jrtype'=>$updateJRProductChild->type, 'countFP' => 1);
                    } else {
                        return array('success' => 0, 'message' => 'Unable to update status of revision, please try again');
                    }
                } else {
                    return array('success' => 0, 'message' => 'Unable to update status of revision, please try again');
                }
            //end gelo added
            } else {
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode=='create-job-request'){
                $attributes = [
                    'jr_product' => 'PRODUCT'
                ];
                $rules = [
                    'jr_product' => 'required'
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
                            $id = encryptor('decrypt',$data['qID']);
                            $selectQuotation = Quotation::find($id);
                            if(empty($selectQuotation->job_request)){
                                $insertJobRequestQuery = new JobRequest();
                                $insertJobRequestQuery->jr_number = 'JECJR-'.$selectQuotation->quote_number;
                                $insertJobRequestQuery->client_id = $selectQuotation->client_id;
                                $insertJobRequestQuery->team_id = $selectQuotation->team_id;
                                $insertJobRequestQuery->agent_id = $selectQuotation->agent->id;
                                $insertJobRequestQuery->quotation_id = $selectQuotation->id;
                                $insertJobRequestQuery->created_by = $user->id;
                                $insertJobRequestQuery->updated_by = $user->id;
                                $insertJobRequestQuery->created_at = getDatetimeNow();
                                $insertJobRequestQuery->updated_at = getDatetimeNow();
                                if($insertJobRequestQuery->save()){
                                    DB::commit();
                                    for($i=0;$i<count($data['jr_product']);$i++){
                                        $selectQuotationProduct = QuotationProduct::find($data['jr_product'][$i]);
                                        $insertProduct = new JobRequestProduct();
                                        $insertProduct->job_request_id = $insertJobRequestQuery->id;
                                        $insertProduct->agent_id = $selectQuotation->agent->id;
                                        $insertProduct->agent_name = $selectQuotation->agent->user->employee->first_name.' '.$selectQuotation->agent->user->employee->last_name;
                                        if($selectQuotationProduct->type=='SUPPLY'){
                                            $insertProduct->type = 'REUPHOLSTER';
                                        }elseif($selectQuotationProduct->type=='FIT-OUT'){
                                            $insertProduct->type = 'FIT-OUT';
                                        }else{
                                            $insertProduct->type = 'NEW-DESIGN';
                                        }
                                        $insertProduct->quotation_product_id = $data['jr_product'][$i];
                                        $insertProduct->product_id = $selectQuotationProduct->product_id;
                                        $insertProduct->created_by = $user->id;
                                        $insertProduct->updated_by = $user->id;
                                        $insertProduct->created_at = getDatetimeNow();
                                        $insertProduct->updated_at = getDatetimeNow();
                                        if($insertProduct->save()){
                                            DB::commit();
                                            $selectQuotationProduct->is_jr = 1;
                                            if($selectQuotationProduct->save()){
                                                DB::commit();
                                            }
                                        }else{
                                            DB::rollback();
                                        }
                                    }
                                
                                }else{
                                    DB::rollback();
                                }
                            }else{
                                for($i=0;$i<count($data['jr_product']);$i++){
                                    $selectQuotationProduct = QuotationProduct::find($data['jr_product'][$i]);
                                    $insertProduct = new JobRequestProduct();
                                    $insertProduct->job_request_id = $selectQuotation->job_request->id;
                                    $insertProduct->agent_id = $selectQuotation->user_id;
                                    $insertProduct->agent_name = $selectQuotation->agent->nickname;
                                    if($selectQuotationProduct->type=='SUPPLY'){
                                        $insertProduct->type = 'REUPHOLSTER';
                                    }elseif($selectQuotationProduct->type=='FIT-OUT'){
                                        $insertProduct->type = 'FIT-OUT';
                                    }else{
                                        $insertProduct->type = 'NEW-DESIGN';
                                    }
                                    $insertProduct->quotation_product_id = $data['jr_product'][$i];
                                    $insertProduct->product_id = $selectQuotationProduct->product_id;
                                    $insertProduct->created_by = $user->id;
                                    $insertProduct->updated_by = $user->id;
                                    $insertProduct->created_at = getDatetimeNow();
                                    $insertProduct->updated_at = getDatetimeNow();
                                    if($insertProduct->save()){
                                        DB::commit();
                                        $selectQuotationProduct->is_jr = 1;
                                        if($selectQuotationProduct->save()){
                                            DB::commit();
                                        }
                                    }else{
                                        DB::rollback();
                                    }
                                }
                            }
                        });
                        return back();
                    }catch (QueryException $exception) {
                        DB::rollback();
                        return array('success' => 0, 'message' =>$exception->errorInfo[2]);
                        return back()->withInput($data);
                    }
                }
            }elseif($postMode=='update-product-image'){
                $id = $data['productId'];
                $attributes = [
                    'productsimg' => 'PRODUCT IMAGE'
                ];
                $rules = [
                    'productsimg' => 'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $file = $data['productsimg'];
                    $destination_prod  = 'assets/img/products/'.$id.'/';
                    $prodctExist = isExistFile($destination_prod . '' . $id);
                    if ($prodctExist['is_exist'] == true){
                        unlink($prodctExist['path']);
                    }
                    fileStorageUpload($file, $destination_prod, $id, 'resize', 685, 888);
                    Session::flash('success', 1);
                    Session::flash('message', 'Product Image Successfuly Changed');
                    return back();
                }
            //start gelo added
            } elseif($postMode == 'add-floor-plan-image') {
                $id = $data['floorPlanProductId'];
                $attributes = [
                    'fp-jrproduct-img' => 'FLOOR PLAN IMAGE'
                ];
                $rules = [
                    'fp-jrproduct-img' => 'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $destination = 'assets/img/job_request_products/floor_plan/';
                    $isExist = isExistFile($destination.''.$id);
                    if($isExist['is_exist'] == true) {
                        unlink($isExist['path']);
                    }
                    $resultUpload = fileStorageUpload($data['fp-jrproduct-img'], $destination, $id, 'resize', 685, 888);
                    Session::flash('success', 1);
                    Session::flash('message', 'Floor Plan Image Successfuly Added');
                    return back();
                }
            } elseif($postMode == 'update-floor-plan-image') {
                $id = $data['updateFloorPlanProductId'];
                $attributes = [
                    'update-fp-jrproduct-img' => 'FLOOR PLAN IMAGE'
                ];
                $rules = [
                    'update-fp-jrproduct-img' => 'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $destination = 'assets/img/job_request_products/floor_plan/';
                    $isExist = isExistFile($destination.''.$id);
                    if($isExist['is_exist'] == true) {
                        unlink($isExist['path']);
                    }
                    $resultUpload = fileStorageUpload($data['update-fp-jrproduct-img'], $destination, $id, 'resize', 685, 888);
                    Session::flash('success', 1);
                    Session::flash('message', 'Floor Plan Image Successfuly Changed');
                    return back();
                }
            } elseif($postMode == 'update-product-description') {
                $attributes = [
                    'product-description' => 'DESCRIPTION',
                    'product_price' => 'PRODUCT PRICE',
                    'product_discount' => 'PRODUCT DISCOUNT',
                ];
                $rules = [
                    'product-description' => 'required',
                    'product_price' => 'required',
                    'product_discount' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    // return $data;
                    $quotation_id = encryptor('decrypt', $data['quotation-id']);
                    $quotation_product_id = encryptor('decrypt', $data['quotation-product-id']);
                    $jr_product_parent_id = encryptor('decrypt', $data['jr-product-parent-id']);
                    $jr_product_child_id = encryptor('decrypt', $data['jr-product-child-id']);

                    $description=null;
                    if(!empty($data['product-description'])){
                        if($data['product-description'] != "<p><br><\/p><p><br><\/p>"){
                            if($data['product-description']!="<div style=\"color: rgb(0, 0, 0); font-family: Roboto, -apple-system, system-ui, BlinkMacSystemFont, \" segoe=\"\" ui\",=\"\" \"helvetica=\"\" neue\",=\"\" arial,=\"\" sans-serif;=\"\" font-size:=\"\" 14px;=\"\" letter-spacing:=\"\" 0.2px;\"=\"\"><br><\/div>"){
                                if($data['product-description'] != '<div style="font-family: Montserrat, sans-serif; letter-spacing: normal;"><br></div>'){
                                    $description = $data['product-description'];
                                }
                            }
                        }
                    }
                    $product_qty = $data['product_qty'];
                    $product_price = $data['product_price'];
                    $product_discount = $data['product_discount'];
                    $product_total_price = $data['hidden_product_total_price'];
                    $product_total_amount = $data['hidden_product_total_amount'];

                    $sub_total = $data['sub_total'];
                    $installation_charge = $data['installation_charge'];
                    $delivery_charge = $data['delivery_charge'];
                    $discount_product_quotation = $data['discount_product_quotation'];
                    $discount_quotation = $data['discount_quotation'];
                    $total_discount = $data['total_discount'];
                    $grand_total = $data['grand_total'];

                    $updateQuotationProduct = QuotationProduct::find($quotation_product_id);
                    if(!empty($updateQuotationProduct)) {
                        $updateQuotationProduct->base_price = $product_price;
                        $updateQuotationProduct->discount = $product_discount;
                        $updateQuotationProduct->total_price = $product_total_price;
                        $updateQuotationProduct->total_amount = $product_total_amount;
                        $updateQuotationProduct->description = $description;
                        $updateQuotationProduct->updated_by = $user->id;
                        $updateQuotationProduct->updated_at = getDatetimeNow();
                        if($updateQuotationProduct->save()) {
                            $updateQuotation = Quotation::find($quotation_id);
                            if(!empty($updateQuotation)) {
                                $updateQuotation->sub_total = $sub_total;
                                $updateQuotation->installation_charge = $installation_charge;
                                $updateQuotation->delivery_charge = $delivery_charge;
                                $updateQuotation->total_item_discount = $discount_product_quotation;
                                $updateQuotation->total_discount = $total_discount;
                                $updateQuotation->grand_total = $grand_total;
                                $updateQuotation->updated_by = $user->id;
                                $updateQuotation->updated_at = getDatetimeNow();
                                if($updateQuotation->save()) {
                                    $updateJRProductChild = JobRequestProduct::where('id', '=', $jr_product_child_id)->first();
                                    $updateJRProductChild->status = 'ONGOING';
                                    $updateJRProductChild->date_rejected = NULL;
                                    $updateJRProductChild->updated_by = $user->id;
                                    $updateJRProductChild->updated_at = getDatetimeNow();
                                    if($updateJRProductChild->save()) {
                                        $updateJRProductParent = JobRequestProduct::where('id', '=', $updateJRProductChild->parent_id)->first();
                                        $updateJRProductParent->status = 'ONGOING';
                                        $updateJRProductParent->updated_by = $user->id;
                                        $updateJRProductParent->updated_at = getDatetimeNow();
                                        if($updateJRProductParent->save()) {
                                            $savedPoint = $updateQuotation->quote_number;
                                            $destination = 'assets/files/quotation_update/';
                                            $datas = array(
                                                'sub_total'=>$sub_total,
                                                'installation_charge'=>$installation_charge,
                                                'delivery_charge'=>$delivery_charge,
                                                'total_product_discount'=>$discount_product_quotation,
                                                'discount'=>$discount_quotation,
                                                'total_discount'=>$total_discount,
                                                'grand_total'=>$grand_total,
                                                'temp_grand_total'=>number_format($grand_total,2),
                                                'last_added'=> $quotation_product_id
                                            );
                                            $datas = json_encode($datas);
                                            $filename = $savedPoint;
                                            $isExist = isExistFile($destination.''.$filename); 
                                            if ($isExist['is_exist'] == true){
                                                unlink($isExist['path']);
                                            }
                                            $result = toTxtFile($destination,$filename,'put',$datas);
                                            if($result['success'] == true){
                                                Session::flash('success', 1);
                                                Session::flash('message', 'Product description successfuly updated');
                                            }
                                        } else {
                                            Session::flash('success', 0);
                                            Session::flash('message', 'Unable to update job request status, please try again.');
                                        }
                                    } else {
                                        Session::flash('success', 0);
                                        Session::flash('message', 'Unable to update job request status, please try again.');
                                    }
                                } else {
                                    Session::flash('success', 0);
                                    Session::flash('message', 'Unable to update product description, please try again.');
                                }
                            } else {
                                Session::flash('success', 0);
                                Session::flash('message', 'Quotation details not found.');
                            }
                        } else {
                            Session::flash('success', 0);
                            Session::flash('message', 'Unable to update product description, please try again.');
                        }
                    } else {
                        Session::flash('success', 0);
                        Session::flash('message', 'Quotation Product not found.');
                    }
                }
                return back();
            //end gelo added
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
