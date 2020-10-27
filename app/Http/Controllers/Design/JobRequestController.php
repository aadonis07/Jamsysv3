<?php

namespace App\Http\Controllers\Design;

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
class JobRequestController extends Controller
{
    function list(){
        return view('design-department.job_requests.list')
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
        $designerDept = Department::where('name', '=', 'Design')->first();
        $selectDesigners = User::where('department_id', '=', $designerDept->id)
                                ->where('status', '=', 'ACTIVE')
                                ->with('employee')
                                ->get();
        return view('design-department.job_requests.view')
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
    //end gelo added
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            //start gelo
            if($postMode=='jr-pending-serverside'){
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('design-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('design-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('design-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('design-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('design-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('design-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-jr" href="'.route('design-job-request-view', ['id' => encryptor('encrypt',$selectQuery->id)]).'" title="View Job Request">
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
                    if(count($selectQuery->jr_revisions)!=0){
                        foreach($selectQuery->jr_revisions as $revision){
                            if($user->position->name != 'DESIGN HEAD') {
                                if($revision->designer_id == $user->id) {
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
                                    $hold_task_button = '';
                                    $finish_task_button = '';
                                    if(!empty($revision->date_started)) {
                                        if($revision->status == 'ON-HOLD') {
                                            if($revision->designer_id == $user->id) {
                                                $start = '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Start" data-id="'.encryptor('encrypt',$revision->id).'"><span class="fa fa-play text-white"></span> START</a><br>'.date('F d,Y h:i A',strtotime($revision->date_started));
                                            } else {
                                                $start = date('F d,Y h:i A',strtotime($revision->date_started));
                                            }
                                        } else {
                                            $start = date('F d,Y h:i A',strtotime($revision->date_started));
                                        }

                                        if($revision->status == 'ONGOING') {
                                            $end = '';
                                            if($revision->designer_id == $user->id) {
                                                $finish_task_button = '<a class="btn btn-xs btn-info text-white action-task" data-actiontype="Finish" data-id="'.encryptor('encrypt',$revision->id).'">
                                                    <span class="fa fa-check text-white"></span> FINISH
                                                </a>';
                                            }

                                            if($user->position->name == 'DESIGN HEAD') {
                                                $hold_task_button = '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Hold" data-id="'.encryptor('encrypt',$revision->id).'">
                                                    <span class="fa fa-pause text-white"></span> HOLD
                                                </a>';
                                            }
                                        }
                                    }

                                    $estimated_finish = '<font class="text-danger">Unknown</font>';
                                    if(!empty($revision->estimated_finish)){
                                        if(empty($revision->date_started)) {
                                            if($revision->designer_id == $user->id) {
                                                $start .= '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Start" data-id="'.encryptor('encrypt',$revision->id).'"><span class="fa fa-play text-white"></span> START</a>';
                                            }
                                        }
                                        $estimated_finish = date('F d,Y h:i A',strtotime($revision->estimated_finish));
                                    }

                                    if(!empty($revision->date_cancelled)){
                                        $hold_task_button = '';
                                        $finish_task_button = '';
                                    }
                                    //end gelo added

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
                                                <font style="font-weight:bold;">Actual END</font>: '.$end.''.$hold_task_button.''.$finish_task_button.'
                                            </div>
                                        </div>          
                                        ';
                                    }

                                    //start gelo added
                                    $add_designer_button = '';
                                    $delete_button = '';
                                    if($user->position->name == 'DESIGN HEAD') {
                                        $reject_revision_button = '<a class="btn btn-xs btn-warning btn-standard waves-effect mr-1 reject-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" title="Reject Revision">
                                                    <span class="fa fa-times"></span> Reject Revision
                                                </a>';
                                        $add_designer_button = '<a class="btn btn-icon btn-danger waves-effect rounded-circle mr-1 add-designer" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Add Designer">
                                            <span class="fa fa-user"></span>
                                        </a>';
                                        $delete_button = '<a class="btn btn-icon btn-secondary waves-effect rounded-circle mr-1 delete-revision" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Delete Revision">
                                                    <span class="fa fa-trash"></span>
                                                </a>';
                                        if(!empty($revision->designer_name)){
                                            $add_designer_button = '';
                                            if(!empty($revision->date_started)) {
                                                $delete_button = '';
                                            } else {
                                                $delete_button = '<a class="btn btn-icon btn-secondary btn-standard waves-effect rounded-circle mr-1 delete-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="1" title="Cancel Revision">
                                                        <span class="fa fa-times"></span>
                                                    </a>';
                                            }
                                        }

                                        if(!empty($revision->date_rejected) || !empty($revision->actual_date_finished)) {
                                            $reject_revision_button = '';
                                        }
                                    }
                                    //end gelo added
                                    
                                    $avalability = 'warning';
                                    $if_cancelled = '';
                                    if(!empty($revision->date_cancelled) || !empty($revision->date_rejected)){
                                        $reject_revision_button = '';
                                        $add_designer_button = '';
                                        $delete_button = '';
                                        $avalability = 'danger';

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
                                                                    <h3><b>'.$revision->jr_type->name.'</b> '.$reject_revision_button.'</h3>
                                                                </div>
                                                                <div class="col-lg-6" align="right">
                                                                    '.$add_designer_button.'
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
                            } else {
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
                                $hold_task_button = '';
                                $finish_task_button = '';
                                if(!empty($revision->date_started)) {
                                    if($revision->status == 'ON-HOLD') {
                                        if($revision->designer_id == $user->id) {
                                            $start = '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Start" data-id="'.encryptor('encrypt',$revision->id).'"><span class="fa fa-play text-white"></span> START</a><br>'.date('F d,Y h:i A',strtotime($revision->date_started));
                                        } else {
                                            $start = date('F d,Y h:i A',strtotime($revision->date_started));
                                        }
                                    } else {
                                        $start = date('F d,Y h:i A',strtotime($revision->date_started));
                                    }

                                    if($revision->status == 'ONGOING') {
                                        $end = '';
                                        if($revision->designer_id == $user->id) {
                                            $finish_task_button = '<a class="btn btn-xs btn-info text-white action-task" data-actiontype="Finish" data-id="'.encryptor('encrypt',$revision->id).'">
                                                <span class="fa fa-check text-white"></span> FINISH
                                            </a>';
                                        }

                                        if($user->position->name == 'DESIGN HEAD') {
                                            $hold_task_button = '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Hold" data-id="'.encryptor('encrypt',$revision->id).'">
                                                <span class="fa fa-pause text-white"></span> HOLD
                                            </a>';
                                        }
                                    }
                                }

                                $estimated_finish = '<font class="text-danger">Unknown</font>';
                                if(!empty($revision->estimated_finish)){
                                    if(empty($revision->date_started)) {
                                        if($revision->designer_id == $user->id) {
                                            $start .= '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Start" data-id="'.encryptor('encrypt',$revision->id).'"><span class="fa fa-play text-white"></span> START</a>';
                                        }
                                    }
                                    $estimated_finish = date('F d,Y h:i A',strtotime($revision->estimated_finish));
                                }

                                if(!empty($revision->date_cancelled)){
                                    $hold_task_button = '';
                                    $finish_task_button = '';
                                }
                                //end gelo added

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
                                            <font style="font-weight:bold;">Actual END</font>: '.$end.''.$hold_task_button.''.$finish_task_button.'
                                        </div>
                                    </div>          
                                    ';
                                }

                                //start gelo added
                                $add_designer_button = '';
                                $delete_button = '';
                                if($user->position->name == 'DESIGN HEAD') {
                                    $reject_revision_button = '<a class="btn btn-xs btn-warning btn-standard waves-effect mr-1 reject-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" title="Reject Revision">
                                                    <span class="fa fa-times"></span> Reject Revision
                                                </a>';
                                    $add_designer_button = '<a class="btn btn-icon btn-danger waves-effect rounded-circle mr-1 add-designer" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Add Designer">
                                        <span class="fa fa-user"></span>
                                    </a>';
                                    $delete_button = '<a class="btn btn-icon btn-secondary waves-effect rounded-circle mr-1 delete-revision" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Delete Revision">
                                                <span class="fa fa-trash"></span>
                                            </a>';
                                    if(!empty($revision->designer_name)){
                                        $add_designer_button = '';
                                        if(!empty($revision->date_started)) {
                                            $delete_button = '';
                                        } else {
                                            $delete_button = '<a class="btn btn-icon btn-secondary btn-standard waves-effect rounded-circle mr-1 delete-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="1" title="Cancel Revision">
                                                    <span class="fa fa-times"></span>
                                                </a>';
                                        }
                                    }

                                    if(!empty($revision->date_rejected) || !empty($revision->actual_date_finished)) {
                                        $reject_revision_button = '';
                                    }
                                }
                                //end gelo added
                                
                                $avalability = 'warning';
                                $if_cancelled = '';
                                if(!empty($revision->date_cancelled) || !empty($revision->date_rejected)){
                                    $reject_revision_button = '';
                                    $add_designer_button = '';
                                    $delete_button = '';
                                    $avalability = 'danger';

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
                                                                <h3><b>'.$revision->jr_type->name.'</b> '.$reject_revision_button.'</h3>
                                                            </div>
                                                            <div class="col-lg-6" align="right">
                                                                '.$add_designer_button.'
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
                    if(count($selectQuery->jr_revisions)!=0){
                        foreach($selectQuery->jr_revisions as $revision){
                            if($user->position->name != 'DESIGN HEAD') {
                                if($revision->designer_id == $user->id) {
                                    $actual_finish = '<font class="text-danger">Unknown</font>';
                                    if(!empty($revision->actual_date_finished)){
                                        $actual_finish = date('F d,Y h:i A',strtotime($revision->actual_date_finished));
                                    }

                                    $end = '<font class="text-danger">Unknown</font>';
                                    if(!empty($revision->actual_date_finished)){
                                        $end = date('F d,Y h:i A',strtotime($revision->actual_date_finished));
                                    }

                                    $start = '<font class="text-danger">This revision is not yet started by designer.</font>';
                                    $hold_task_button = '';
                                    $finish_task_button = '';
                                    if(!empty($revision->date_started)) {
                                        if($revision->status == 'ON-HOLD') {
                                            if($revision->designer_id == $user->id) {
                                                $start = '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Start" data-id="'.encryptor('encrypt',$revision->id).'"><span class="fa fa-play text-white"></span> START</a><br>'.date('F d,Y h:i A',strtotime($revision->date_started));
                                            } else {
                                                $start = date('F d,Y h:i A',strtotime($revision->date_started));
                                            }
                                            
                                        } else {
                                            $start = date('F d,Y h:i A',strtotime($revision->date_started));
                                        }
                                        
                                        if($revision->status == 'ONGOING') {
                                            $end = '';
                                            if($revision->designer_id == $user->id) {
                                                $finish_task_button = '<a class="btn btn-xs btn-info text-white action-task" data-actiontype="Finish" data-id="'.encryptor('encrypt',$revision->id).'">
                                                    <span class="fa fa-check text-white"></span> FINISH
                                                </a>';
                                            }

                                            if($user->position->name == 'DESIGN HEAD') {
                                                $hold_task_button = '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Hold" data-id="'.encryptor('encrypt',$revision->id).'">
                                                    <span class="fa fa-pause text-white"></span> HOLD
                                                </a>';
                                            }
                                        }
                                    }

                                    $estimated_finish = '<font class="text-danger">Unknown</font>';
                                    if(!empty($revision->estimated_finish)){
                                        if(empty($revision->date_started)) {
                                            if($revision->designer_id == $user->id) {
                                                $start .= '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Start" data-id="'.encryptor('encrypt',$revision->id).'"><span class="fa fa-play text-white"></span> START</a>';
                                            }
                                        }
                                        $estimated_finish = date('F d,Y h:i A',strtotime($revision->estimated_finish));
                                    }

                                    if(!empty($revision->date_cancelled)){
                                        $hold_task_button = '';
                                        $finish_task_button = '';
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
                                                <font style="font-weight:bold;">Actual END</font>: '.$end.''.$hold_task_button.''.$finish_task_button.'
                                            </div>
                                        </div>          
                                        ';
                                    }

                                    $add_designer_button = '';
                                    $delete_button = '';
                                    if($user->position->name == 'DESIGN HEAD') {
                                        $reject_revision_button = '<a class="btn btn-xs btn-warning btn-standard waves-effect mr-1 reject-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" title="Reject Revision">
                                                    <span class="fa fa-times"></span> Reject Revision
                                                </a>';
                                        $add_designer_button = '<a class="btn btn-icon btn-danger waves-effect rounded-circle mr-1 add-designer" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Add Designer">
                                            <span class="fa fa-user"></span>
                                        </a>';
                                        $delete_button = '<a class="btn btn-icon btn-secondary waves-effect rounded-circle mr-1 delete-revision" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Delete Revision">
                                                    <span class="fa fa-trash"></span>
                                                </a>';
                                        if(!empty($revision->designer_name)){
                                            $add_designer_button = '';
                                            if(!empty($revision->date_started)) {
                                                $delete_button = '';
                                            } else {
                                                $delete_button = '<a class="btn btn-icon btn-secondary btn-standard waves-effect rounded-circle mr-1 delete-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="1" title="Cancel Revision">
                                                        <span class="fa fa-times"></span>
                                                    </a>';
                                            }
                                        }

                                        if(!empty($revision->date_rejected) || !empty($revision->actual_date_finished)) {
                                            $reject_revision_button = '';
                                        }
                                    }

                                    $avalability = 'warning';
                                    $if_cancelled = '';
                                    if(!empty($revision->date_cancelled) || !empty($revision->date_rejected)){
                                        $reject_revision_button = '';
                                        $add_designer_button = '';
                                        $delete_button = '';
                                        $avalability = 'danger';

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
                                                                    <h3><b>'.$revision->jr_type->name.'</b> '.$reject_revision_button.'</h3>
                                                                </div>
                                                                <div class="col-lg-6" align="right">
                                                                    '.$add_designer_button.'
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
                            } else {
                                $actual_finish = '<font class="text-danger">Unknown</font>';
                                if(!empty($revision->actual_date_finished)){
                                    $actual_finish = date('F d,Y',strtotime($revision->actual_date_finished));
                                }

                                $end = '<font class="text-danger">Unknown</font>';
                                if(!empty($revision->actual_date_finished)){
                                    $end = date('F d,Y h:i A',strtotime($revision->actual_date_finished));
                                }

                                $start = '<font class="text-danger">This revision is not yet started by designer.</font>';
                                $hold_task_button = '';
                                $finish_task_button = '';
                                if(!empty($revision->date_started)) {
                                    if($revision->status == 'ON-HOLD') {
                                        if($revision->designer_id == $user->id) {
                                            $start = '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Start" data-id="'.encryptor('encrypt',$revision->id).'"><span class="fa fa-play text-white"></span> START</a><br>'.date('F d,Y h:i A',strtotime($revision->date_started));
                                        } else {
                                            $start = date('F d,Y h:i A',strtotime($revision->date_started));
                                        }
                                        
                                    } else {
                                        $start = date('F d,Y h:i A',strtotime($revision->date_started));
                                    }
                                    
                                    if($revision->status == 'ONGOING') {
                                        $end = '';
                                        if($revision->designer_id == $user->id) {
                                            $finish_task_button = '<a class="btn btn-xs btn-info text-white action-task" data-actiontype="Finish" data-id="'.encryptor('encrypt',$revision->id).'">
                                                <span class="fa fa-check text-white"></span> FINISH
                                            </a>';
                                        }

                                        if($user->position->name == 'DESIGN HEAD') {
                                            $hold_task_button = '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Hold" data-id="'.encryptor('encrypt',$revision->id).'">
                                                <span class="fa fa-pause text-white"></span> HOLD
                                            </a>';
                                        }
                                    }
                                }

                                $estimated_finish = '<font class="text-danger">Unknown</font>';
                                if(!empty($revision->estimated_finish)){
                                    if(empty($revision->date_started)) {
                                        if($revision->designer_id == $user->id) {
                                            if(empty($revision->date_cancelled)){
                                                $start .= '<a class="btn btn-xs btn-secondary text-white action-task" data-actiontype="Start" data-id="'.encryptor('encrypt',$revision->id).'"><span class="fa fa-play text-white"></span> START</a>';
                                            }
                                        }
                                    }
                                    $estimated_finish = date('F d,Y h:i A',strtotime($revision->estimated_finish));
                                }

                                if(!empty($revision->date_cancelled)){
                                    $hold_task_button = '';
                                    $finish_task_button = '';
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
                                            <font style="font-weight:bold;">Actual END</font>: '.$end.''.$hold_task_button.''.$finish_task_button.'
                                        </div>
                                    </div>          
                                    ';
                                }

                                $add_designer_button = '';
                                $delete_button = '';
                                if($user->position->name == 'DESIGN HEAD') {
                                    $reject_revision_button = '<a class="btn btn-xs btn-warning btn-standard waves-effect mr-1 reject-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" title="Reject Revision">
                                                    <span class="fa fa-times"></span> Reject Revision
                                                </a>';
                                    $add_designer_button = '<a class="btn btn-icon btn-danger waves-effect rounded-circle mr-1 add-designer" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Add Designer">
                                        <span class="fa fa-user"></span>
                                    </a>';
                                    $delete_button = '<a class="btn btn-icon btn-secondary waves-effect rounded-circle mr-1 delete-revision" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="0" title="Delete Revision">
                                                <span class="fa fa-trash"></span>
                                            </a>';
                                    if(!empty($revision->designer_name)){
                                        $add_designer_button = '';
                                        if(!empty($revision->date_started)) {
                                            $delete_button = '';
                                        } else {
                                            $delete_button = '<a class="btn btn-icon btn-secondary btn-standard waves-effect rounded-circle mr-1 delete-revision-withwork" data-id="'.encryptor('encrypt',$revision->id).'" data-deltype="1" title="Cancel Revision">
                                                    <span class="fa fa-times"></span>
                                                </a>';
                                        }
                                    }

                                    if(!empty($revision->date_rejected) || !empty($revision->actual_date_finished)) {
                                        $reject_revision_button = '';
                                    }
                                }

                                $avalability = 'warning';
                                $if_cancelled = '';
                                if(!empty($revision->date_cancelled) || !empty($revision->date_rejected)){
                                    $reject_revision_button = '';
                                    $add_designer_button = '';
                                    $delete_button = '';
                                    $avalability = 'danger';

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
                                                                <h3><b>'.$revision->jr_type->name.'</b> '.$reject_revision_button.'</h3>
                                                            </div>
                                                            <div class="col-lg-6" align="right">
                                                                '.$add_designer_button.'
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
            } elseif($postMode=='cancel-revision'){
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
                    if($selectQuery->save()){
                        return array('success' => 1,'message'=>$msg,'jrtype'=>$selectQuery->type);
                    }else{
                        return array('success' => 0,'message'=>'Error Acquired!');
                    }
                }else{
                    return array('success' => 0,'message'=>'Error Acquired!');
                }
            //start gelo added
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
                                        return array('success' => 1,'message' => 'Successfuly Added!', 'jrtype'=> $updateJRProd->type);
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
                                        return array('success' => 1,'message' => 'Successfuly Added!', 'jrtype'=> $updateJRProd->type);
                                    } else {
                                        return array('success' => 0,'message' => 'Unable to add designer, please try again', 'jrtype' => $updateJRProd->type);
                                    }
                                } else {
                                    return array('success' => 1,'message' => 'Successfuly Added!', 'jrtype'=> $updateJRProd->type);
                                }
                            }
                        }else{
                            return array('success' => 0,'message' => 'Unable to add designer, please try again', 'jrtype' => $updateJRProd->type);
                        }
                    } else {
                        return array('success' => 0,'message' => 'Job request product not found!', 'jrtype' => '');
                    }
                }
            }elseif($postMode=='action-task'){
                $id = encryptor('decrypt',$data['id']);
                $action = $data['action'];
                $JRProductQuery = JobRequestProduct::find($id);
                if($action == 'Start') {
                    $type = 'started';
                    if($JRProductQuery->date_started == NULL) {
                        $JRProductQuery->date_started = getDatetimeNow();
                    }
                    $JRProductQuery->status = 'ONGOING';
                    $JRProductQuery->updated_by = $user->id;
                    $JRProductQuery->updated_at = getDatetimeNow();
                } elseif($action == 'Hold') {
                    $type = 'hold';
                    $JRProductQuery->hold_date = getDatetimeNow();
                    $JRProductQuery->status = 'ON-HOLD';
                    $JRProductQuery->updated_by = $user->id;
                    $JRProductQuery->updated_at = getDatetimeNow();
                } elseif($action == 'Finish') {
                    $type = 'finished';
                    $JRProductQuery->date_accomplished = getDatetimeNow();
                    $JRProductQuery->actual_date_finished = getDatetimeNow();
                    $JRProductQuery->accomplished_by = $user->id;
                    $JRProductQuery->status = 'ACCOMPLISHED';
                    $JRProductQuery->updated_by = $user->id;
                    $JRProductQuery->updated_at = getDatetimeNow();
                } else {
                    return array('success' => 0, 'message' => 'Undefined Method');
                }
                if($JRProductQuery->save()){
                    //check revisions statuses
                    if($type == 'finished') {
                        $selectJRProductRevisions = JobRequestProduct::where('parent_id', '=', $JRProductQuery->parent_id)->whereNull('date_cancelled')->get();
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
                            return array('success' => 1,'message'=>'Task has been '.$type.'!','jrtype' => $JRProductQuery->type);
                        } else {
                            //update jr_product_parent to Accomplished
                            $updateJRProductParent = JobRequestProduct::where('id', '=', $JRProductQuery->parent_id)->first();
                            $updateJRProductParent->date_accomplished = getDatetimeNow();
                            $updateJRProductParent->actual_date_finished = getDatetimeNow();
                            $updateJRProductParent->status = 'ACCOMPLISHED';
                            $updateJRProductParent->updated_by = $user->id;
                            $updateJRProductParent->updated_at = getDatetimeNow();
                            if($updateJRProductParent->save()) {
                                //check jr_parents statuses
                                $selectJRProductParents = JobRequestProduct::where('job_request_id', '=', $JRProductQuery->job_request_id)
                                                                            ->whereNull('parent_id')
                                                                            ->whereNull('date_cancelled')
                                                                            ->get();
                                $jr_parent_status = [];
                                foreach($selectJRProductParents as $jrp_parent) {
                                    if($jrp_parent->status != 'ACCOMPLISHED') {
                                        $rets = 0;
                                    } else {
                                        $rets = 1;
                                    }
                                    array_push($jr_parent_status, $rets);
                                }
                                if(in_array('0', $jr_parent_status)) {
                                    //not all jr_parent are accomplished 
                                    return array('success' => 1,'message'=>'Task has been '.$type.'!','jrtype' => $JRProductQuery->type);
                                } else {
                                    //update jr to Accomplished
                                    $updateJR = JobRequest::where('id', '=', $JRProductQuery->job_request_id)->first();
                                    $updateJR->isJRPAccomplished = 1;
                                    $updateJR->updated_by = $user->id;
                                    $updateJR->updated_at = getDatetimeNow();
                                    if($updateJR->save()) {
                                        return array('success' => 1,'message'=>'Task has been '.$type.'!','jrtype' => $JRProductQuery->type);
                                    } else {
                                        return array('success' => 0,'message'=>'Error Acquired!');
                                    }
                                }
                            } else {
                                return array('success' => 0,'message'=>'Error Acquired!');
                            }
                        }
                    } else {
                        return array('success' => 1,'message'=>'Task has been '.$type.'!','jrtype' => $JRProductQuery->type);
                    }
                } else {
                    return array('success' => 0,'message'=>'Error Acquired!');
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
                            return array('success' => 1,'message'=>'Floor plan has been deleted.','jrtype' => $JRProductQuery->type);
                        }
                    } else {
                        return array('success' => 1,'message'=>'Floor plan has been deleted.','jrtype' => $JRProductQuery->type);
                    }
                } else {
                    return array('success' => 0,'message'=>'Error Acquired!');
                }
            } elseif($postMode=='reject-revision'){
                $id = encryptor('decrypt',$data['id']);
                $rejectQuery = JobRequestProduct::find($id);
                $rejectQuery->date_rejected = date('Y-m-d');
                $rejectQuery->rejected_by = $user->id;
                $rejectQuery->status = 'REJECTED';
                $rejectQuery->reject_reason = $data['reason_reject'];
                $rejectQuery->updated_by = $user->id;
                $rejectQuery->updated_at = getDatetimeNow();
                if($rejectQuery->save()){
                    $selectJRProducts = JobRequestProduct::where('parent_id','=',$rejectQuery->parent_id)->whereNull('date_cancelled')->get();
                    $selectQuery = JobRequestProduct::find($rejectQuery->parent_id);
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
                    $selectQuery->status = 'REJECTED';
                    $selectQuery->date_rejected = date('Y-m-d');
                    $selectQuery->rejected_by = $user->id;
                    $selectQuery->updated_by = $user->id;
                    $selectQuery->updated_at = getDatetimeNow();
                    if($selectQuery->save()){
                        return array('success' => 1,'message'=>'Successfuly Rejected!','jrtype'=>$selectQuery->type);
                    }else{
                        return array('success' => 0,'message'=>'Error Acquired!');
                    }
                }else{
                    return array('success' => 0,'message'=>'Error Acquired!');
                }
            //end gelo added
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
            //end gelo added
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
