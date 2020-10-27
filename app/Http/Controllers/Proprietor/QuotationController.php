<?php

namespace App\Http\Controllers\Proprietor;

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
use App\Collection;
use App\CollectionDetail;

class QuotationController extends Controller
{
   
    public function list(){
        return view('proprietor-department.quotations.list')
             ->with('admin_menu','QUOTATION')
             ->with('admin_sub_menu','LIST-QUOTATION');
    }
    public function view(Request $request){
        $data = $request->all();
        $id = encryptor('decrypt',$data['id']);
        $quotation = Quotation::find($id);


        return view('proprietor-department.quotations.view')
        ->with('quotation',$quotation)
        ->with('admin_menu','QUOTATION')
        ->with('admin_sub_menu','LIST-QUOTATION');
    }
    
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode=='quotation-list-serverside'){
                $selectQuery = Quotation::with('sales_agent')->with('client')->with('job_request')->where('status','=',$data['status'])
                                        ->whereNull('hold_date')
                                        ->orderBy('created_at','DESC');
                
                return Datatables::eloquent($selectQuery)
                ->editColumn('created_at', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.date('F d,Y ',strtotime($selectQuery->created_at));
                    $returnHTml .= ' <small>['.time_elapsed_string($selectQuery->created_at).']</small><hr class="m-0">';
               
                    if(!empty($selectQuery->job_request)){
                        $returnHTml .=  '<div class="input-group">
                                            <div class="input-group-prepend">
                                                <a class="btn btn-info waves-effect waves-themed" href="'.route('proprietor-job-request-view', ['id' => encryptor('encrypt',$selectQuery->job_request->id)]).'" ><i class="fal fa-search text-white"></i></a>
                                            </div>
                                            <input id="button-addon4" type="text" class="form-control" value="'.$selectQuery->job_request->jr_number.'" disabled>
                                        </div>';
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
                ->editColumn('sales_agent.employee.first_name', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    $returnHtml .= $selectQuery->sales_agent->employee->first_name.' '.$selectQuery->sales_agent->employee->last_name;
                    $returnHtml .= '</div>';

                    return $returnHtml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                       $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-quotation" href="'.route('proprietor-quotation-view',['id'=>encryptor('encrypt',$selectQuery->id)]).'" title="View Quotation">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                       $returnHtml .= '<a class="btn btn-icon btn-outline-danger waves-effect rounded-circle mr-1 reject-quotation" data-status="'.$selectQuery->status.'" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Reject Quotation">
                                            <i class="fa fa-times"></i>
                                        </a>';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-danger waves-effect rounded-circle mr-1 hold-quotation" data-status="'.$selectQuery->status.'" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Hold Quotation">
                                            <i class="fa fa-lock"></i>
                                        </a>';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-success waves-effect rounded-circle mr-1" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Monitor Quotation">
                                            <i class="fa fa-book"></i>
                                        </a>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->setRowClass(function ($selectQuery) {
                    if(!empty($selectQuery->hold_request_date)){
                        return 'alert-warning';
                    }
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            
            }elseif($postMode=='quotation-moved-serverside'){
                    $selectQuery = Quotation::with('sales_agent')->with('client')->with('job_request')->where('status','=',$data['status'])
                                                ->whereNull('hold_date')
                                                ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('date_moved', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.date('F d,Y ',strtotime($selectQuery->date_moved));
                    $returnHTml .= ' <hr class="m-0">';
                    if(empty($selectQuery->job_request)){
                        $returnHTml .=  '<a class="btn btn-sm btn-info text-white job_request p-2" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-plus text-white"></span> Add Job Request</a>';
                    }else{
                        $returnHTml .=  '<div class="input-group">
                                            <div class="input-group-prepend">
                                                <a class="btn btn-info waves-effect waves-themed" href="'.route('sales-job-request-view', ['id' => encryptor('encrypt',$selectQuery->job_request->id)]).'" ><i class="fal fa-search text-white"></i></a>
                                            </div>
                                            <input id="button-addon4" type="text" class="form-control" value="'.$selectQuery->job_request->jr_number.'" disabled>
                                            <div class="input-group-prepend">
                                                <a class="btn btn-success waves-effect waves-themed job_request" data-id="'.encryptor('encrypt',$selectQuery->id).'" ><i class="fa fa-plus text-white"></i></a>
                                            </div>
                                        </div>';
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
                ->editColumn('sales_agent.employee.first_name', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    $returnHtml .= $selectQuery->sales_agent->employee->first_name.' '.$selectQuery->sales_agent->employee->last_name;
                    $returnHtml .= '</div>';

                    return $returnHtml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-quotation" href="'.route('proprietor-quotation-view',['id'=>encryptor('encrypt',$selectQuery->id)]).'" title="View Quotation">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                        if(empty($selectQuery->date_approved)){
                            $returnHtml .= '<a class="btn btn-icon btn-outline-success btn-standard waves-effect rounded-circle mr-1 approved-quotation" data-status="'.$selectQuery->status.'" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Approved Quotation">
                                                <i class="fa fa-thumbs-up for-icon text-success"></i>
                                            </a>';
                        }
                        if(!empty($selectQuery->date_moved)){
                            $returnHtml .= '<a class="btn btn-icon btn-outline-danger btn-standard waves-effect rounded-circle mr-1 reject-quotation" data-status="'.$selectQuery->status.'" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Reject Quotation">
                                                <i class="fa fa-times for-icon text-danger"></i>
                                            </a>';
                            if(empty($selectQuery->hold_date)){
                                $returnHtml .= '<a class="btn btn-icon btn-outline-danger waves-effect rounded-circle mr-1 hold-quotation" data-status="'.$selectQuery->status.'" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Hold Quotation">
                                                    <i class="fa fa-lock"></i>
                                                </a>';
                            }
                        }
                        $returnHtml .= '<a class="btn btn-icon btn-outline-success waves-effect rounded-circle mr-1 monitor-quotation" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Monitor Quotation">
                                            <i class="fa fa-book"></i>
                                        </a>';


                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->setRowClass(function ($selectQuery) {
                    if(!empty($selectQuery->hold_request_date)){
                        return 'alert-warning';
                    }
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='compute-commission'){
                $computation = commissionComputation($data['commi_type'],$data['contract_amount'],$data['amount_requested'],$data['vat_data']);
                return $computation;
            }elseif($postMode=='action-quotation'){
                $id = encryptor('decrypt',$data['id']);
                $updateQuery = Quotation::find($id);
                if($data['qstatus']=='APPROVED-PROPRIETOR'){
                    $updateQuery->status = $data['qstatus'];
                    $updateQuery->date_approved = getDateNow();
                    $updateQuery->approved_by = 'PROPRIETOR';
                }elseif($data['qstatus']=='REJECTED'){
                    $updateQuery->status = $data['qstatus'];
                    $updateQuery->date_rejected = getDateNow();
                    $updateQuery->rejected_by = 'PROPRIETOR';
                }else{
                    $updateQuery->hold_date = getDateNow();
                    $updateQuery->hold_by = 'PROPRIETOR';
                }
                $updateQuery->updated_by = $user->id;
                if($updateQuery->save()){
                    return $updateQuery->status;
                }
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