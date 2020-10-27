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
use App\Quotation;
use App\QuotationProduct;
use App\JobRequest;
use App\JobRequestProduct;
use App\JobRequestType;
use App\Product;
class JobRequestController extends Controller
{
    function list(){
        return view('it-department.job_requests.list')
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
        return view('it-department.job_requests.view')
                ->with('jr',$selectQuery)
                ->with('jr_types',$jr_types);
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
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode=='jr-list-serverside'){
                $selectQuery = JobRequest::with('client')->with('quotation')->with('agent')->where('status','=',$data['status'])
                ->orderBy('created_at','DESC');
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='jr-view-serverside'){
                $selectQuery = JobRequestProduct::select('job_request_products.id','job_request_products.date_added_revision','job_request_products.product_id','job_request_products.type','job_request_products.parent_id','job_request_products.quotation_product_id','job_request_products.status','job_request_products.deadline_date','job_request_products.designer_name')
                                                ->with('jr_product')->with('jr_quotation_product')
                                                ->with('jr_revisions')
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
                    $description = '';
                    if(!empty($selectQuery->jr_quotation_product->description)){
                        $description = '<tr><td colspan="2"><b>Description : </b>
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
                    if(empty($selectQuery->jr_quotation_product->cancelled_date)){
                    $returnHtml = '<div align="right"><a class="btn btn-danger  text-white add-revision" data-id="'.encryptor('encrypt',$selectQuery->id).'">
                                        <span class="fa fa-plus text-white"></span>
                                        Add Type / Revision
                                    </a></div>';
                    }
                    if(count($selectQuery->jr_revisions)!=0){
                        foreach($selectQuery->jr_revisions as $revision){
                            $actual_finish = '<font class="text-danger">Unknown</font>';
                            if(!empty($revision->actual_date_finished)){
                                $actual_finish = date('F d,Y',strtotime($revision->actual_date_finished));
                            }
                            $start = '<font class="text-danger">This revision is not yet started by designer.</font>';
                            if(!empty($revision->date_started)){
                                $actual_finish = '<font class="text-danger">This revision is not yet finished.</font>';
                                $start = date('F d,Y',strtotime($revision->date_started));
                            }
                            $end = '<font class="text-danger">Unknown</font>';
                            if(!empty($revision->date_started)){
                                $end = date('F d,Y',strtotime($revision->actual_date_finished));
                            }
                            $designer_assign = 'No Designer Assigned Yet';
                            if(!empty($revision->designer_name)){
                                $designer_assign = '
                                <div class="row">
                                    <div class="col-md-7">
                                        <b>Date Assigned</b>: '.date('F d,Y h:i a',strtotime($revision->date_assigned)).'<br>
                                        <b>Designer</b>: '.$revision->designer_name.'<br>
                                        <b>Assigned Task</b>: '.$revision->assigned_task.'
                                    </div>
                                    <div class="col-md-5">
                                        <font style="font-weight:bold;">Status</font>: '.$revision->status.'<br>
                                        <font style="font-weight:bold;">Actual START</font>: '.$start.' <br>
                                        <font style="font-weight:bold;">Actual END</font>: '.$end.'
                                    </div>
                                </div>          
                                ';
                            }
                            $delete_button = '<a class="btn btn-warning text-white delete-revision" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0">
                                                <span class="fa fa-trash text-white"></span> Delete
                                            </a>';
                            if(!empty($revision->designer_name)){
                                $delete_button = '<a class="btn btn-warning text-white delete-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="1">
                                                <span class="fa fa-times text-white"></span> Cancel Revision
                                            </a>';
                            }
                            $avalability = 'warning';
                            $if_cancelled = '';
                            if(!empty($revision->date_cancelled)){
                                $delete_button = '';
                                $avalability = 'danger';
                                $if_cancelled = '<tr style="border: 3px solid #ff090952;box-shadow: 2px 2px 2px 2px #00000052;">
                                                    <td style="border: none;">
                                                        <h4><b>Cancelled Reason : </b></h4> 
                                                    </td>
                                                    <td width="68%" style="border: none;">
                                                        '.$revision->cancelled_reason.'
                                                    </td>
                                                </tr>';
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
            }elseif($postMode=='add-revision'){
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
                    $dec_id = encryptor('decrypt',$data['id']);
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
                            if($updateDeadline->save()){
                                return array('success' => 1,'message'=>'Successfuly Added!','jrtype'=>$updateDeadline->type);
                            }
                        }else{
                            $updateDeadline->deadline_date = $insertRevision->deadline_date;
                            if($updateDeadline->save()){
                                return array('success' => 1,'message'=>'Successfuly Added!','jrtype'=>$insertRevision->type);
                            }
                        }
                    }
                }
            }elseif($postMode=='cancel-revision'){
                $id = encryptor('decrypt',$data['id']);
                $cancelQuery = JobRequestProduct::find($id);
                $cancelQuery->date_cancelled = date('Y-m-d');
                $cancelQuery->cancelled_by = $user->id;
                if($data['deltype']==1){
                    $cancelQuery->cancelled_reason = $data['reason_cancelled'];
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
                    if($selectQuery->save()){
                        return array('success' => 1,'message'=>'Successfuly Deleted!','jrtype'=>$selectQuery->type);
                    }else{
                        return array('success' => 0,'message'=>'Error Acquired!');
                    }
                }
            }else{
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
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
