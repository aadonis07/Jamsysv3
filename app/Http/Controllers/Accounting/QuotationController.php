<?php

namespace App\Http\Controllers\Accounting;

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
use App\CollectionPaper;
use App\Collection;
class QuotationController extends Controller
{
    public function sales_invoice(){
        return view('accounting-department.collections.sales_invoice')
             ->with('admin_menu','SALES-INVOICE');
    }
    public function view(Request $request){
        $data = $request->all();
        $id = encryptor('decrypt',$data['id']);
        $quotation = Quotation::find($id);


        return view('accounting-department.quotation.view')
        ->with('quotation',$quotation)
        ->with('admin_menu','QUOTATION')
        ->with('admin_sub_menu','LIST-QUOTATION');
    }
    public function list(){

        return view('accounting-department.quotation.list')
             ->with('admin_menu','QUOTATION');
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode=='sales-invoice-serverside'){
                if($data['status']=='PENDING'){
                    $selectQuery = Quotation::with('client')->with('sales_agent')->with('terms')->whereNotNull('is_requested_si')->whereNotNull('date_moved')->whereNull('is_sales_invoice_serve')
                                  ->orderBy('is_requested_si','DESC');
                }else{
                    $selectQuery = Quotation::with('collection')->with('client')->with('sales_agent')->with('terms')->whereNotNull('is_requested_si')->whereNotNull('date_moved')->whereNotNull('is_sales_invoice_serve')    
                                    ->orderBy('is_requested_si','DESC');
                }
                return Datatables::eloquent($selectQuery)
                ->editColumn('quote_number', function($selectQuery) use($user) {
                    $returnHtml = '<b class="text-info">'.$selectQuery->quote_number.'</b>';
                    if(!empty($selectQuery->is_sales_invoice_serve)){
                        $returnHtml .= '| date served: '.date('F d,Y h:i a',strtotime($selectQuery->is_sales_invoice_serve));
                    }else{
                        $returnHtml .= '| requested date: '.date('F d,Y h:i a',strtotime($selectQuery->is_requested_si));
                    }
                    $returnHtml .= '<hr class="m-0">';
                    $returnHtml .= '<b>Agent : </b>'.$selectQuery->sales_agent->employee->first_name." ".$selectQuery->sales_agent->employee->last_name;
                    return $returnHtml;
                })
                ->editColumn('grand_total', function($selectQuery) use($user) {
                    $returnHtml = '<div align="right">Php'.number_format($selectQuery->grand_total,2);
                    $returnHtml .= '<hr class="m-0">';
                    $returnHtml .= '</div>';
                    $returnHtml .= '<div align="center"><b>Terms :</b> '.$selectQuery->terms->name;
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHtml = $selectQuery->client->name;
                    $returnHtml .= '<hr class="m-0">';
                    $returnHtml .= '<b>TIN Number :</b>'.$selectQuery->client->tin_number;
                    return $returnHtml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1 view-quotation" data-id="'. encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-eye"></span></a>';
                        if(empty($selectQuery->is_sales_invoice_serve)){
                            $returnHtml .= '<a class="btn btn-outline-info btn-standard waves-effect issue-invoice" data-quote_number="'.$selectQuery->quote_number.'" data-id="'. encryptor('encrypt',$selectQuery->id).'"><b>Issue Invoice</b></a>';
                        }
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='quotation-moved-serverside'){
                $selectQuery = Quotation::with('client')->with('job_request')->with('collection')
                                            ->where('status','=',$data['status'])
                                            ->whereNull('hold_date')
                                            ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('date_moved', function($selectQuery) use($user) {
                    $returnHTml = '<div align="center">'.date('F d,Y ',strtotime($selectQuery->date_moved));
                    $returnHTml .= ' <hr class="m-0">';
                   
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
                        $returnHtml .= '<a class="btn btn-icon btn-outline-secondary btn-standard waves-effect rounded-circle mr-1" href="'.route('accounting-quotation-view',['id'=>encryptor('encrypt',$selectQuery->id)]).'" title="View Quotation">
                                            <i class="fal fa-eye"></i>
                                        </a>';
                      
                        if(empty($selectQuery->date_accounting_approved)){
                        $returnHtml .= '<a class="btn btn-icon btn-outline-success btn-standard waves-effect rounded-circle mr-1 approved-quotation" data-status="'.$selectQuery->status.'" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Approved Quotation">
                                                <i class="fa fa-thumbs-up for-icon text-success"></i>
                                            </a>';
                        }
                        $returnHtml .= '<a href="'.route('accounting-create-collection-schedule', ['id' => encryptor('encrypt',$selectQuery->collection->id)]).'" class="btn btn-icon btn-outline-success btn-standard waves-effect rounded-circle mr-1 update-collection"><span class="fa fa-database"></span></a>';
                        $returnHtml .= '<a class="btn btn-icon btn-outline-success waves-effect rounded-circle mr-1 request-reject-quotation" data-id="'.encryptor('encrypt',$selectQuery->id).'" title="Monitor Quotation">
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
            }elseif($postMode=='action-quotation'){
                $id = encryptor('decrypt',$data['id']);
                $updateQuery = Quotation::find($id);
                if($data['qstatus']=='APPROVED-ACCOUNTING'){
                    $updateQuery->status = $data['qstatus'];
                    $updateQuery->date_accounting_approved = getDateNow();
                    $updateQuery->accounting_approved_by = $user->nickname;
                }elseif($data['qstatus']=='R-REJECT'){
                    $updateQuery->status = $data['qstatus'];
                    $updateQuery->rejected_by = $user->nickname;
                }elseif($data['qstatus']=='CANCELLED'){
                    $updateQuery->status = $data['qstatus'];
                    $updateQuery->date_cancelled = getDateNow();
                    $updateQuery->cancelled_by = $user->nickname;
                }else{
                    $updateQuery->hold_date = getDateNow();
                    $updateQuery->hold_by = $user->nickname;
                }
                $updateQuery->updated_by = $user->id;
                if($updateQuery->save()){
                    return $updateQuery->status;
                }
            } else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode=='issue-invoice'){
                $attributes = [
                    'invoice-date'=>'Invoice Date',
                    'invoice-number'=>'Invoice Number',
                    'invoice-amount'=>'Invoice Amount'
                ];
                $rules = [
                    'invoice-date'=>'required',
                    'invoice-number'=>'required',
                    'invoice-amount'=>'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    DB::beginTransaction();
                    try {
                        $id = encryptor('decrypt',$data['quote-id']);
                        $selectQuotation = Quotation::find($id);
                        $selectQuotation->is_sales_invoice_serve = getDatetimeNow();
                        if($selectQuotation->save()){
                            DB::commit();
                            $insertCollectionPaper =new CollectionPaper();
                            $insertCollectionPaper->collection_id = $selectQuotation->collection->id;
                            $insertCollectionPaper->amount_paid = $data['invoice-amount'];
                            $insertCollectionPaper->accounting_paper_id = 6;
                            $insertCollectionPaper->reference_number = $data['invoice-number'];
                            $insertCollectionPaper->reference_date = $data['invoice-date'];
                            $insertCollectionPaper->status = 'ON-HAND';
                            $insertCollectionPaper->created_by = $user->id;
                            $insertCollectionPaper->updated_by = $user->id;
                            $insertCollectionPaper->created_at = getDatetimeNow(); 
                            $insertCollectionPaper->updated_at = getDatetimeNow();
                            if($insertCollectionPaper->save()){
                                DB::commit();
                                Session::flash('success',1);
                                Session::flash('message','Your '.$selectQuotation->quote_number.'Invoice Has been Issued.');
                                return back();
                            }else{
                                DB::rollback();
                                Session::flash('success',0);
                                Session::flash('message','Collection Paper is not save');
                                return back();
                            }
                        }else{
                            DB::rollback();
                            Session::flash('success',0);
                            Session::flash('message','Your Quotation Update is not save.');
                            return back();
                        }
                    }catch (QueryException $exception) {
                        DB::rollback();
                        Session::flash('success',0);
                        Session::flash('message',$exception->errorInfo[2]);
                        return back();
                    }
                }
            }elseif($postMode=='action-quotation'){
                $id = encryptor('decrypt',$data['quotationId']);
                $selectQuery = Quotation::where('id','=',$id)->with('terms')->with('barangay')->with('client')->with('products')->with('province')->with('city')->first();
                return view('accounting-department.quotation.view')
                ->with('quotation',$selectQuery)
                ->with('sub_to',$selectQuery->sub_total);
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
