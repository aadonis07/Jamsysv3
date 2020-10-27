<?php

namespace App\Http\Controllers\Proprietor;

use App\Bank;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\PaymentRequest;
use App\PaymentRequestPartial;
use App\PaymentRequestDetail;
use Auth;
use Session;
use Validator;
use DB;
use App\Employee;
use App\PurchaseOrder;
use App\Quotation;
use DataTables;

class PaymentRequestController extends Controller
{
    function showIndex(){
        $user = Auth::user();
        return view('proprietor-department.payment-request.index')
            ->with('admin_menu','ACCOUNTING')
            ->with('admin_sub_menu','LIST')
            ->with('user',$user);
    }
    function showPaymentRequest(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['prid'])){
            $payment_request_id = encryptor('decrypt',$data['prid']);
            $selectQuery = PaymentRequest::with('details')
                ->with('partials')
                ->with('accountTitle')
                ->with('accountTitleParticular')
                ->with('createdBy')
                ->with('updatedBy')
                ->find($payment_request_id);
            $allowEdit = array(
                'FOR-APPROVAL',
                'APPROVED',
                'CANCELLED',
                'VOID',
                'REJECTED',
                'RELEASED',
            );
            if($selectQuery){
                if(in_array($selectQuery->status,$allowEdit)) {
                    $selectQueryBanks = Bank::all();
                    return view('proprietor-department.payment-request.details')
                        ->with('admin_menu', 'ACCOUNTING')
                        ->with('admin_sub_menu', 'CREATE-PR')
                        ->with('payment_request', $selectQuery)
                        ->with('banks', $selectQueryBanks)
                        ->with('user', $user);
                }else{
                    Session::flash('success',0);
                    Session::flash('message','Unable to find payment request data. Please try again');
                    return redirect(route('proprietor-payment-request-list'));
                }
            }else{
                Session::flash('success',0);
                Session::flash('message','Unable to find payment request data. Please try again');
                return redirect(route('proprietor-payment-request-list'));
            }

        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find payment request data. Please try again');
            return redirect(route('proprietor-payment-request-list'));
        }
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'partial-payment-list'){
                $payment_request_id = encryptor('decrypt',$data['payment_request']);
                $selectQuery = PaymentRequestPartial::with('paymentRequest')->where('payment_request_id','=',$payment_request_id)
                    ->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('amount', function($selectQuery){
                        $returnValue = '';
                        $returnValue .= '&#8369; '.number_format($selectQuery->amount,2);
                        $returnValue .= '<hr class="m-0 ">';
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->cheque_type.'</span> | '.readableDate($selectQuery->cheque_date);
                        $returnValue .= '<hr class="m-0 mt-1">';
                        $returnValue .= '<p class="text-info m-0" title="PR NUMBER">'.$selectQuery->paymentRequest->pr_number.'</p>';
                        return $returnValue;
                    })
                    ->editColumn('status', function($selectQuery){
                        $returnValue = '';
                        $returnValue .= $selectQuery->status;
                        $returnValue .= '<hr class="m-0 ">';
                        $returnValue .= '<text class="text-info" title="Created">'.readableDate($selectQuery->created_at,'date').'</text>';
                        $returnValue .= '<hr class="m-0 ">';
                        $returnValue .= '<p class="m-0">'.$selectQuery->purpose.'</p>';
                        return $returnValue;

                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'for-approval-payment-request-list'){
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
                    ->where('type','=','CHEQUE')
                    ->where(function($q) {
                        $q->where('status','=','FOR-APPROVAL')
                            ->orWhere(function($q) {
                                $q->where('is_partial',true)
                                    ->whereHas('partials',function($q1){
                                        $q1->where('status','=','FOR-APPROVAL');
                                    });
                            });
                    })
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('details', function($selectQuery){
                        $checkType = $selectQuery->check_type;
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        if($selectQuery->type == 'CHEQUE') {
                            $returnValue = 'Check Type: ' . $checkType;
                        }
                        if($selectQuery->is_partial == true){
                            $returnValue = 'Check Type: <a class="text-info" href="javascript:;" onClick=showPartial("'.$enc_payment_request_id.'") >Partials</a>';
                        }
                        if($selectQuery->category == 'SUPPLIER'){
                            foreach($selectQuery->details as $detail){
                                $returnValue.='<p class="m-0 mb-1">'.$detail->name.'</p>';
                            }
                        }
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<p class="m-0 mb-1">Designate: '.$selectQuery->designated_department.'</p>';
                        }
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= 'Note: '.$selectQuery->note;
                        return $returnValue;
                    })
                    ->editColumn('requested_amount', function($selectQuery){
                        $returnValue = '';
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->type.'</span> | &#8369; '.number_format($selectQuery->requested_amount,2);
                        //$returnValue .= '<hr class="m-0">';
                        $balance = $selectQuery->requested_amount;
                        if($selectQuery->is_partial == true){
                            foreach($selectQuery->partials as $partial){
                                if($partial->status == 'RELEASED'){
                                    $balance -= $partial->amount;
                                }
                                $returnValue .= '<p style="font-size:12px" class="m-0 text-muted">&#8369; '.number_format($partial->amount,2).' [ '.$partial->status.' ]</p>';
                                $returnValue .= '<p style="font-size:12px" class="m-0 text-info">&nbsp;&nbsp; -  '.$partial["cheque_type"].' [ '.readableDate($partial["cheque_date"]).' ]</p>';
                            }
                            if($balance !=  $selectQuery->requested_amount){
                                $returnValue .='<hr class="m-0">';
                                $returnValue .= '<text class="text-info">Balance: &#8369; '.number_format($balance,2).' </text>';
                            }
                        }
                        return $returnValue;
                    })
                    ->addColumn('pr_number_details', function($selectQuery){
                        $returnValue = '';
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        $paymentDetails = route('proprietor-payment-request-details',['prid' => $enc_payment_request_id]);
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<span class="badge badge-primary">'.$selectQuery->designated_department.'</span> | ';
                        }
                        $returnValue .= $selectQuery->pr_number.' ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>&nbsp;';
                        if($selectQuery->is_partial == true){
                            $returnValue .= ' <button title="Partial " type="button" onClick=showPartial("'.$enc_payment_request_id.'") class="btn btn-icon btn-xs btn-info"><span class="fas fa-bars"></span></button>';
                        }
                        $returnValue .= '<hr class="mt-1 mb-1">';
                        if($selectQuery->type == 'CHEQUE'){
                            $returnValue .= '<text class="text-info" title="Category"><b>PAYEE: '.$selectQuery->payee_name.'</b></text>';
                        }else{
                            $returnValue .= '<text class="text-info" title="Category"><b>CAT: '.$selectQuery->category.'</b></text>';
                        }
                        return $returnValue;
                    })
                    ->editColumn('category', function($selectQuery){
                        $returnValue = $selectQuery->category;
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery){
                        $returnValue = readableDate($selectQuery->created_at,'time');
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info" title="Requested By"><b>R.B: '.$selectQuery->requested_by.'</b></text><br>';
                        $returnValue .= '<text class="text-info" title="Created By"><b>C.B: '.$selectQuery->createdBy->username.'</b></text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $returnValue = '';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'approved-payment-request-list'){
                $selectQuery = PaymentRequest::with('accountTitle')
                    ->with('accountTitleParticular')
                    ->with('createdBy')
                    ->where('type','=','CHEQUE')
                    ->where(function($q) {
                        $q->where('status','=','APPROVED')
                            ->orWhere(function($q) {
                                $q->where('is_partial',true)
                                    ->whereHas('partials',function($q1){
                                        $q1->where('status','=','APPROVED');
                                    });
                            });
                    })
                    ->orderBy('approved_date','DESC');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('details', function($selectQuery){
                        $checkType = $selectQuery->check_type;
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        if($selectQuery->type == 'CHEQUE') {
                            $returnValue = 'Check Type: ' . $checkType;
                        }
                        if($selectQuery->is_partial == true){
                            $returnValue = 'Check Type: <a class="text-info" href="javascript:;" onClick=showPartial("'.$enc_payment_request_id.'") >Partials</a>';
                        }
                        if($selectQuery->category == 'SUPPLIER'){
                            foreach($selectQuery->details as $detail){
                                $returnValue.='<p class="m-0 mb-1">'.$detail->name.'</p>';
                            }
                        }
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<p class="m-0 mb-1">Designate: '.$selectQuery->designated_department.'</p>';
                        }
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= 'Note: '.$selectQuery->note;
                        return $returnValue;
                    })
                    ->editColumn('requested_amount', function($selectQuery){
                        $returnValue = '';
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->type.'</span> | &#8369; '.number_format($selectQuery->requested_amount,2);
                        //$returnValue .= '<hr class="m-0">';
                        $balance = $selectQuery->requested_amount;
                        if($selectQuery->is_partial == true){
                            foreach($selectQuery->partials as $partial){
                                if($partial->status == 'RELEASED'){
                                    $balance -= $partial->amount;
                                }
                                $returnValue .= '<p style="font-size:12px" class="m-0 text-muted">&#8369; '.number_format($partial->amount,2).' [ '.$partial->status.' ]</p>';
                                $returnValue .= '<p style="font-size:12px" class="m-0 text-info">&nbsp;&nbsp; -  '.$partial["cheque_type"].' [ '.readableDate($partial["cheque_date"]).' ]</p>';
                            }
                            if($balance !=  $selectQuery->requested_amount){
                                $returnValue .='<hr class="m-0">';
                                $returnValue .= '<text class="text-info">Balance: &#8369; '.number_format($balance,2).' </text>';
                            }
                        }
                        return $returnValue;
                    })
                    ->addColumn('pr_number_details', function($selectQuery){
                        $returnValue = '';
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        $paymentDetails = route('proprietor-payment-request-details',['prid' => $enc_payment_request_id]);
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<span class="badge badge-primary">'.$selectQuery->designated_department.'</span> | ';
                        }
                        $returnValue .= $selectQuery->pr_number.' ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>&nbsp;';
                        if($selectQuery->is_partial == true){
                            $returnValue .= ' <button title="Partial " type="button" onClick=showPartial("'.$enc_payment_request_id.'") class="btn btn-icon btn-xs btn-info"><span class="fas fa-bars"></span></button>';
                        }
                        $returnValue .= '<hr class="mt-1 mb-1">';
                        if($selectQuery->type == 'CHEQUE'){
                            $returnValue .= '<text class="text-info" title="Category"><b>PAYEE: '.$selectQuery->payee_name.'</b></text>';
                        }else{
                            $returnValue .= '<text class="text-info" title="Category"><b>CAT: '.$selectQuery->category.'</b></text>';
                        }
                        return $returnValue;
                    })
                    ->editColumn('category', function($selectQuery){
                        $returnValue = $selectQuery->category;
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery){
                        $returnValue = readableDate($selectQuery->created_at,'time');
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info" title="Requested By"><b>R.B: '.$selectQuery->requested_by.'</b></text><br>';
                        $returnValue .= '<text class="text-info" title="Created By"><b>C.B: '.$selectQuery->createdBy->username.'</b></text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $returnValue = '';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'released-payment-request-list'){
                $selectQuery = PaymentRequest::with('accountTitle')
                    ->with('accountTitleParticular')
                    ->with('createdBy')
                    ->where('type','=','CHEQUE')
                    ->where(function($q) {
                        $q->where('status','=','RELEASED')
                            ->orWhere(function($q) {
                                $q->where('is_partial',true)
                                    ->whereHas('partials',function($q1){
                                        $q1->where('status','=','RELEASED');
                                    });
                            });
                    })
                    ->orderBy('updated_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('details', function($selectQuery){
                        $checkType = $selectQuery->check_type;
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        if($selectQuery->type == 'CHEQUE') {
                            $returnValue = 'Check Type: ' . $checkType;
                        }
                        if($selectQuery->is_partial == true){
                            $returnValue = 'Check Type: <a class="text-info" href="javascript:;" onClick=showPartial("'.$enc_payment_request_id.'") >Partials</a>';
                        }
                        if($selectQuery->category == 'SUPPLIER'){
                            foreach($selectQuery->details as $detail){
                                $returnValue.='<p class="m-0 mb-1">'.$detail->name.'</p>';
                            }
                        }
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<p class="m-0 mb-1">Designate: '.$selectQuery->designated_department.'</p>';
                        }
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= 'Note: '.$selectQuery->note;
                        return $returnValue;
                    })
                    ->editColumn('requested_amount', function($selectQuery){
                        $returnValue = '';
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->type.'</span> | &#8369; '.number_format($selectQuery->requested_amount,2);
                        //$returnValue .= '<hr class="m-0">';
                        $balance = $selectQuery->requested_amount;
                        if($selectQuery->is_partial == true){
                            foreach($selectQuery->partials as $partial){
                                if($partial->status == 'RELEASED'){
                                    $balance -= $partial->amount;
                                }
                                $returnValue .= '<p style="font-size:12px" class="m-0 text-muted">&#8369; '.number_format($partial->amount,2).' [ '.$partial->status.' ]</p>';
                                $returnValue .= '<p style="font-size:12px" class="m-0 text-info">&nbsp;&nbsp; -  '.$partial["cheque_type"].' [ '.readableDate($partial["cheque_date"]).' ]</p>';
                            }
                            if($balance !=  $selectQuery->requested_amount){
                                $returnValue .='<hr class="m-0">';
                                $returnValue .= '<text class="text-info">Balance: &#8369; '.number_format($balance,2).' </text>';
                            }
                        }
                        return $returnValue;
                    })
                    ->addColumn('pr_number_details', function($selectQuery){
                        $returnValue = '';
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        $paymentDetails = route('proprietor-payment-request-details',['prid' => $enc_payment_request_id]);
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<span class="badge badge-primary">'.$selectQuery->designated_department.'</span> | ';
                        }
                        $returnValue .= $selectQuery->pr_number.' ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>&nbsp;';
                        if($selectQuery->is_partial == true){
                            $returnValue .= ' <button title="Partial " type="button" onClick=showPartial("'.$enc_payment_request_id.'") class="btn btn-icon btn-xs btn-info"><span class="fas fa-bars"></span></button>';
                        }
                        $returnValue .= '<hr class="mt-1 mb-1">';
                        if($selectQuery->type == 'CHEQUE'){
                            $returnValue .= '<text class="text-info" title="Category"><b>PAYEE: '.$selectQuery->payee_name.'</b></text>';
                        }else{
                            $returnValue .= '<text class="text-info" title="Category"><b>CAT: '.$selectQuery->category.'</b></text>';
                        }
                        return $returnValue;
                    })
                    ->editColumn('category', function($selectQuery){
                        $returnValue = $selectQuery->category;
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery){
                        $returnValue = readableDate($selectQuery->created_at,'time');
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info" title="Requested By"><b>R.B: '.$selectQuery->requested_by.'</b></text><br>';
                        $returnValue .= '<text class="text-info" title="Created By"><b>C.B: '.$selectQuery->createdBy->username.'</b></text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $returnValue = '';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'cancelled-payment-request-list'){
                $selectQuery = PaymentRequest::with('accountTitle')
                    ->with('accountTitleParticular')
                    ->with('createdBy')
                    ->where('type','=','CHEQUE')
                    ->whereIn('status',['CANCELLED','VOID'])
                    ->orderBy('updated_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('requested_amount', function($selectQuery){
                        $returnValue = '';
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->type.'</span> | &#8369; '.number_format($selectQuery->requested_amount,2);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info" title="Requested By"><b>R.B: '.$selectQuery->requested_by.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('pr_number', function($selectQuery){
                        $returnValue = '';
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        $paymentDetails = route('proprietor-payment-request-details',['prid' => $enc_payment_request_id]);
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<span class="badge badge-primary">'.$selectQuery->designated_department.'</span> | ';
                        }
                        $returnValue .= $selectQuery->pr_number.' ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>';
                        if($selectQuery->is_partial == true){
                            $returnValue .= ' <button title="Partial " type="button" onClick=showPartial("'.$enc_payment_request_id.'") class="btn btn-icon btn-xs btn-info"><span class="fas fa-bars"></span></button>';
                        }
                        $returnValue .= '<hr class="mt-1 mb-1">';
                        $returnValue .= '<text class="text-info" title="Category"><b>CAT: '.$selectQuery->category.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('category', function($selectQuery){
                        $returnValue = $selectQuery->category;
                        return $returnValue;
                    })
                    ->editColumn('account_title_particular.name', function($selectQuery){
                        $returnValue = $selectQuery->accountTitleParticular->name;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info" title="Account Title"><b>A.T: '.$selectQuery->accountTitle->name.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery){
                        $returnValue = readableDate($selectQuery->created_at,'time');
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"  title="Created By"><b>C.B: '.$selectQuery->createdBy->username.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('status', function($selectQuery){
                        $returnValue = ''.$selectQuery->status.'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"  title="Last Update At"><b>C.B: '.readableDate($selectQuery->updated_at).'</b></text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $returnValue = '';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'rejected-payment-request-list'){
                $selectQuery = PaymentRequest::with('accountTitle')
                    ->with('accountTitleParticular')
                    ->with('createdBy')
                    ->where('type','=','CHEQUE')
                    ->where('status','=','REJECTED')
                    ->orderBy('updated_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('requested_amount', function($selectQuery){
                        $returnValue = '';
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->type.'</span> | &#8369; '.number_format($selectQuery->requested_amount,2);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info" title="Requested By"><b>R.B: '.$selectQuery->requested_by.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('pr_number', function($selectQuery){
                        $returnValue = '';
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        $paymentDetails = route('proprietor-payment-request-details',['prid' => $enc_payment_request_id]);
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<span class="badge badge-primary">'.$selectQuery->designated_department.'</span> | ';
                        }
                        $returnValue .= $selectQuery->pr_number.' ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>';
                        if($selectQuery->is_partial == true){
                            $returnValue .= ' <button title="Partial "  type="button" onClick=showPartial("'.$enc_payment_request_id.'") class="btn btn-icon btn-xs btn-info"><span class="fas fa-bars"></span></button>';
                        }
                        $returnValue .= '<hr class="mt-1 mb-1">';
                        $returnValue .= '<text class="text-info" title="Category"><b>CAT: '.$selectQuery->category.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('category', function($selectQuery){
                        $returnValue = $selectQuery->category;
                        return $returnValue;
                    })
                    ->editColumn('account_title_particular.name', function($selectQuery){
                        $returnValue = $selectQuery->accountTitleParticular->name;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info" title="Account Title"><b>A.T: '.$selectQuery->accountTitle->name.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('updated_at', function($selectQuery){
                        $returnValue = readableDate($selectQuery->updated_at,'time');
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"  title="Created At"><b>CREATED: '.readableDate($selectQuery->created_at).'</b></text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $returnValue = '';
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
            if($postMode == 'update-partial-payment-request-status'){
                $attributes = [
                    'selected_partials' => 'Selected Partial/s',
                    'status' => 'Status',
                ];
                $rules = [
                    'selected_partials' => 'required',
                    'status' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $statusPosition = array(
                        'PENDING'=>0,
                        'FOR-APPROVAL'=> 1,
                        'APPROVED'=> 2,
                        'RELEASED'=> 3,
                    );
                    /**
                    STATUS MOVEMENT. Kaya ko ito ginagawa para ma anticipate ang pag update ng payment status sa partial payment status
                    $statusPosition[incoming status] > $statusPosition[current payment request status]  == allow update
                     **/
                    if($data['status'] == 'APPROVED'){
                        $payment_request_id = encryptor('decrypt',$data['payment_request_key']);
                        $updateQuery = PaymentRequest::with('partials')->find($payment_request_id);
                        if($updateQuery){
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($user, $data, $updateQuery,$statusPosition) {
                                    if($statusPosition['APPROVED'] > $statusPosition[$updateQuery->status]){
                                        // allow edit
                                        $updateQuery->status = 'APPROVED';
                                        $updateQuery->updated_by = $user->id;
                                        $updateQuery->approved_by = $user->employee->first_name.' '.$user->employee->last_name;
                                        $updateQuery->approved_date = getDateNow();
                                        $updateQuery->updated_at = getDatetimeNow();
                                        $updateQuery->save();
                                    }

                                    // update partials
                                    if(is_array($data['selected_partials'])){
                                        // multiple
                                        foreach($data['selected_partials'] as $partial){
                                            $partial_id = encryptor('decrypt',$partial);
                                            $updatePartialQuery = $updateQuery->partials->where('id','=',$partial_id)->first();
                                            $updatePartialQuery->status = 'APPROVED';
                                            $updatePartialQuery->approved_by = $user->employee->first_name.' '.$user->employee->last_name;
                                            $updatePartialQuery->approved_date = getDateNow();
                                            $updatePartialQuery->updated_by = $user->id;
                                            $updatePartialQuery->updated_at = getDatetimeNow();
                                            $updatePartialQuery->save();
                                        }
                                    }else{
                                        // hindi array. $data['selected_partials']
                                        $partial_id = encryptor('decrypt',$data['selected_partials']);
                                        $updatePartialQuery = $updateQuery->partials->where('id','=',$partial_id)->first();
                                        $updatePartialQuery->status = 'APPROVED';
                                        $updatePartialQuery->approved_by = $user->employee->first_name.' '.$user->employee->last_name;
                                        $updatePartialQuery->approved_date = getDateNow();
                                        $updatePartialQuery->updated_by = $user->id;
                                        $updatePartialQuery->updated_at = getDatetimeNow();
                                        $updatePartialQuery->save();
                                    }
                                    DB::commit();
                                });
                                Session::flash('success', 1);
                                Session::flash('message', 'Payment request [ '.$updateQuery->pr_number.' ] and partials payment is APPROVED.');
                            }catch (QueryException $exception) {
                                DB::rollback();
                                Session::flash('success', 0);
                                Session::flash('message', $exception->errorInfo[2]);
                            }
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message','Unable to find partial/s. Please try again');
                        }
                    }
                    else{
                        Session::flash('success', 0);
                        Session::flash('message', 'This status [ '.$data["status"].' ] is not yet coded. Under review ');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-payment-request-status'){
                $enc_payment_request_id = $data['key'];
                $payment_request_id = encryptor('decrypt',$data['key']);
                $data['key'] = $payment_request_id;
                $attributes = [
                    'key' => 'Payment Request',
                    'status' => 'Status',
                ];
                $rules = [
                    'key' => 'required|exists:payment_requests,id',
                    'status' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $selectQuery = PaymentRequest::with('details')->with('partials')->find($payment_request_id);
                    if($selectQuery){
                        if($data['status'] == 'REJECTED'){
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($user, $data, $selectQuery) {
                                    $selectQuery->status = 'REJECTED';
                                    $selectQuery->updated_by = $user->id;
                                    $selectQuery->updated_at = getDatetimeNow();
                                    $selectQuery->remarks = $data['remarks'];
                                    $selectQuery->save();
                                    if($selectQuery->is_partial == true){
                                        foreach($selectQuery->partials as $partial){
                                            $partial->status = 'REJECTED';
                                            $partial->updated_by = $user->id;
                                            $partial->updated_at = getDatetimeNow();
                                            $partial->remarks = $data['remarks'];
                                            $partial->save();
                                        }
                                    }
                                    DB::commit();
                                });
                                Session::flash('success', 1);
                                Session::flash('message', $selectQuery->pr_number.' is now REJECTED. It can be revert to PENDING to re-approve this PR');
                                return redirect(route('proprietor-payment-request-details',['prid' => $enc_payment_request_id]));
                            }catch (QueryException $exception) {
                                DB::rollback();
                                Session::flash('success', 0);
                                Session::flash('message', $exception->errorInfo[2]);
                                return back();
                            }
                        }
                        elseif($data['status'] == 'APPROVED'){
                            $selectQuery->status = 'APPROVED';
                            $selectQuery->updated_by = $user->id;
                            $selectQuery->approved_by = $user->employee->first_name.' '.$user->employee->last_name;
                            $selectQuery->approved_date = getDateNow();
                            $selectQuery->updated_at = getDatetimeNow();
                            if($selectQuery->save()){
                                Session::flash('success', 1);
                                Session::flash('message', $selectQuery->pr_number.' is now APPROVED.');
                                return redirect(route('proprietor-payment-request-details',['prid' => $enc_payment_request_id]));
                            }
                            else{
                                Session::flash('success', 0);
                                Session::flash('message', 'Unable to update status. Please try again');
                                return back();
                            }
                        }
                        else{
                            Session::flash('success', 0);
                            Session::flash('message', 'This status [ '.$data["status"].' ] is not yet coded. Under review ');
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to find PR details. Please try again');
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
