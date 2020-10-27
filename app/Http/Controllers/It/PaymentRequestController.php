<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Auth;
use Session;
use Validator;
use DB;
use PDF;
use App\Payee;
use App\Bank;
use App\Client;
use App\Supplier;
use App\Department;
use App\AccountingTitle;
use App\AccountTitleParticular;
use App\Employee;
use App\PurchaseOrder;
use App\Quotation;
use App\Liquidation;
use App\PaymentRequest;
use App\PaymentRequestPartial;
use App\PaymentRequestDetail;
use DataTables;

class PaymentRequestController extends Controller
{
    function showPrintCheque(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['prid'])){
            $payment_request_id = encryptor('decrypt',$data['prid']);
            $selectQuery = PaymentRequest::where('status','=','RELEASED')
                            ->where('id','=',$payment_request_id)
                            ->first();
            $pdf = PDF::loadView('it-department.payment-request.pdf.print-cheque', array('data' => $selectQuery))
                ->setPaper('letter', 'portrait')
                ->stream();
            return $pdf;
        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find payment request data. Please try again');
            return redirect(route('payment-request-list'));
        }
    }
    function showPaymentRequest(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['prid'])){
            $payment_request_id = encryptor('decrypt',$data['prid']);
            $selectQuery = PaymentRequest::with('details')
                                    ->with('liquidations')
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
                    return view('it-department.payment-request.details')
                        ->with('admin_menu', 'ACCOUNTING')
                        ->with('admin_sub_menu', 'CREATE-PR')
                        ->with('payment_request', $selectQuery)
                        ->with('banks', $selectQueryBanks)
                        ->with('user', $user);
                }else{
                    Session::flash('success',0);
                    Session::flash('message','Unable to find payment request data. Please try again');
                    return redirect(route('payment-request-list'));
                }
            }else{
                Session::flash('success',0);
                Session::flash('message','Unable to find payment request data. Please try again');
                return redirect(route('payment-request-list'));
            }

        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find payment request data. Please try again');
            return redirect(route('payment-request-list'));
        }
    }
    function showUpdate(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['prid'])){
            $payment_request_id = encryptor('decrypt',$data['prid']);
            $selectQuery = PaymentRequest::with('details')
                ->with('liquidations')
                ->with('partials')
                ->find($payment_request_id);
            $allowEdit = array(
              'R-DEPARTMENT',
              'PENDING',
            );
            if($selectQuery) {
                if (in_array($selectQuery->status, $allowEdit)) {
                    $selectDepartmentQuery = Department::all();
                    $selectAccountTitleQuery = AccountingTitle::all();
                    return view('it-department.payment-request.update')
                        ->with('admin_menu', 'ACCOUNTING')
                        ->with('admin_sub_menu', 'CREATE-PR')
                        ->with('payment_request', $selectQuery)
                        ->with('departments', $selectDepartmentQuery)
                        ->with('accountTitles', $selectAccountTitleQuery)
                        ->with('user', $user);
                } else {
                    return redirect(route('payment-request-details', ['prid' => $data['prid']]));
                }
            }else{
                Session::flash('success',0);
                Session::flash('message','Unable to find payment request data. Please try again');
                return bacK();
            }
        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find payment request data. Please try again');
            return bacK();
        }
    }
    function showIndex(){
        $user = Auth::user();
        return view('it-department.payment-request.index')
            ->with('admin_menu','ACCOUNTING')
            ->with('admin_sub_menu','LIST')
            ->with('user',$user);
    }
    function showCreate(){
        $user = Auth::user();
        $selectDepartmentQuery = Department::all();
        $selectAccountTitleQuery = AccountingTitle::all();
        return view('it-department.payment-request.create')
            ->with('admin_menu','ACCOUNTING')
            ->with('admin_sub_menu','CREATE-PR')
            ->with('departments',$selectDepartmentQuery)
            ->with('accountTitles',$selectAccountTitleQuery)
            ->with('user',$user);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'add-invoice-details'){
                /***
                     *  Algorithm when creating Invoice in with terms P.O
                     *  Validation
                     *  1. All input fields required
                     *  2. Validate if the P.O Grand total is equal to inputted invoices
                     *  3. If P.O remove, invoices also remove.
                     *  4. Validate if total is less than the P.O G.T
                 ***/
                $attributes = [
                    'po_number' => 'P.O Number',
                    'reference_number' => 'Reference Number',
                    'invoice_amount' => 'Invoice Amount',
                    'remarks' => 'Remarks',
                ];
                $rules = [
                    'po_number' => 'required',
                    'reference_number' => 'required',
                    'invoice_amount' => 'required',
                    'remarks' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' =>implode(',',$validator->errors()->all()));
                }else{
                    $total_added_invoice_amount = $data['total_invoice_amount'];
                    $po_grand_total_amount = $data['po_grand_total'];
                    if($data['invoice_amount'] > $po_grand_total_amount){
                        return array('success' => 0, 'message' =>'Invoice Amount is greater than P.O Grand Total');
                    }
                    elseif(( round($total_added_invoice_amount + $data['invoice_amount'],2)  ) > ( $po_grand_total_amount )){
                        return array('success' => 0, 'message' =>'Listed Invoice in P.O plus your inputted amount is greater than P.O Grand Total');
                    }
                    else{
                        $tempKey =strtotime(getDatetimeNow());
                        $resultHtml = '
                        <tr id="po-invoice-row-'.$tempKey.'" class="po-row-'.$data["po_key"].'">
                            <td>
                                <input type="hidden" name="invoice_po[]" value="'.$data["po_key"].'"/>
                                <input type="hidden" name="invoice_po_number[]" value="'.$data["po_number"].'"/>
                                <input type="hidden" name="invoice_amount[]" value="'.$data["invoice_amount"].'"/>
                                <input type="hidden" name="'.$data["po_key"].'_amount[]" value="'.$data["invoice_amount"].'"/>
                                <input  type="hidden" name="invoice_remarks[]" value="'.$data["remarks"].'"/>
                                <input type="hidden" name="invoice_reference_number[]" value="'.$data["reference_number"].'"/>
                                <p class="m-0"><span class="badge badge-primary">P.O</span> | '.$data["po_number"].'</p>
                                <hr class="m-0">
                                <p class="m-0"><span class="badge badge-primary">S.I</span> | '.$data["reference_number"].'</p>
                                <hr class="m-0">
                                <p class="m-0"><span class="badge badge-primary">AMOUNT</span> | &#8369; '.number_format($data["invoice_amount"],2).'</p>
                            </td>
                            <td>
                                '.$data["remarks"].'
                            </td>
                            <td>
                                <button onClick=removeRow("po-invoice-row-'.$tempKey.'") class="btn btn-icon btn-xs btn-danger"><span class="fas fa-times"></span></button>
                            </td>
                        </tr>';
                        return array('success' => 1, 'message' =>'Invoice Added','data' => $resultHtml);
                    }
                }
            }
            elseif($postMode == 'partial-payment-list'){
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
            elseif($postMode == 'create-partial-payment-request'){
                $attributes = [
                    'request_amount' => 'Request Amount',
                    'partial_amount' => 'Partial Amount',
                    'cheque_date' => 'Cheque Date',
                    'cheque_type' => 'Cheque Type',
                    'partial_purpose' => 'Purpose',
                ];
                $rules = [
                    'request_amount' => 'required',
                    'partial_amount' => 'required',
                    'cheque_date' => 'required',
                    'cheque_type' => 'required',
                    'partial_purpose' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' =>implode(',',$validator->errors()->all()));
                }else{
                    $tempKey =strtotime(getDatetimeNow());
                    if($data["partial_amount"] >= $data['request_amount']){
                        return array('success' => 0, 'message' =>'Partial Amount must less than the Request Amount');
                    }
                    elseif($data['total_partials_added'] >= $data['request_amount']){
                        return array('success' => 0, 'message' =>'Total Partial Amount must equal to the requested amount');
                    }
                    elseif( ( $data['total_partials_added'] + $data["partial_amount"]  ) > $data['request_amount']){
                        return array('success' => 0, 'message' =>'Partial Amount is too high with the remaining request amount');
                    }
                    else{
                        $html = '
                        <tr id="partial-row-'.$tempKey.'">
                            <td>
                                &#8369; '.number_format($data["partial_amount"],2).'
                                <hr class="m-0">
                                <text class="text-info">'.$data["cheque_type"].': '.readableDate($data["cheque_date"]).'</text>
                                <input name="partials[]" type="hidden" value="'.$data["partial_amount"].'">
                                <input name="cheque_dates[]" type="hidden" value="'.$data["cheque_date"].'">
                                <input name="cheque_types[]" type="hidden" value="'.$data["cheque_type"].'">
                            </td>
                            <td>
                                '.$data["partial_purpose"].'
                                <input name="purposes[]" type="hidden" value="'.$data["partial_purpose"].'">
                            </td>
                            <td>
                                <button onClick=removeRow("partial-row-'.$tempKey.'") class="btn btn-sm btn-danger btn-icon" onClick=""><span class="fas fa-times"></span></button>
                            </td>
                        </tr>';
                    }
                    return array('success' => 1, 'message' =>'Partial Payment Request Added', 'data' => $html);
                }
            }
            elseif($postMode == 'released-payment-request-list'){
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
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
                        $returnValue = '';
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
                        $paymentDetails = route('payment-request-update',['prid' => $enc_payment_request_id]);
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
                    ->editColumn('updated_at', function($selectQuery){
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
                        $paymentDetails = route('payment-request-details',['prid' => $enc_payment_request_id]);
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<span class="badge badge-primary">'.$selectQuery->designated_department.'</span> | ';
                        }
                        $returnValue .= $selectQuery->pr_number.' ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>';
                        if($selectQuery->is_partial == true){
                            $returnValue .= ' <button title="Partial " title="Partial " type="button" onClick=showPartial("'.$enc_payment_request_id.'") class="btn btn-icon btn-xs btn-info"><span class="fas fa-bars"></span></button>';
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
                        $paymentDetails = route('payment-request-details',['prid' => $enc_payment_request_id]);
                        if($selectQuery->category == 'OFFICE'){
                            $returnValue .= '<span class="badge badge-primary">'.$selectQuery->designated_department.'</span> | ';
                        }
                        $returnValue .= $selectQuery->pr_number.' ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>';
                        if($selectQuery->is_partial == true){
                            $returnValue .= ' <button title="Partial " title="Partial " type="button" onClick=showPartial("'.$enc_payment_request_id.'") class="btn btn-icon btn-xs btn-info"><span class="fas fa-bars"></span></button>';
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
            elseif($postMode == 'approved-payment-request-list'){
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
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
                        $returnValue = '';
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
                        $paymentDetails = route('payment-request-update',['prid' => $enc_payment_request_id]);
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
            elseif($postMode == 'for-approval-payment-request-list'){
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
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
                        $paymentDetails = route('payment-request-update',['prid' => $enc_payment_request_id]);
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
            elseif($postMode == 'pending-payment-request-list'){
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
                    ->where(function($q) {
                        $q->where('status','=','PENDING')
                            ->orWhere(function($q) {
                                $q->where('is_partial',true)
                                    ->whereHas('partials',function($q1){
                                        $q1->where('status','=','PENDING');
                            });
                        });
                    })
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('details', function($selectQuery){
                        $checkType = $selectQuery->type;
                        $returnValue = '';
                        $enc_payment_request_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = 'Check Type: ' . $checkType;
                        if($selectQuery->type == 'CHEQUE') {
                            $returnValue = 'Check Type: ' . $checkType;
                            if($selectQuery->is_partial == true){
                                $returnValue = 'Check Type: <a class="text-info" href="javascript:;" onClick=showPartial("'.$enc_payment_request_id.'") >Partials</a>';
                            }
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
                        $paymentDetails = route('payment-request-update',['prid' => $enc_payment_request_id]);
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
            elseif($postMode == 'r-department-payment-request-list'){
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
                    ->where(function($q) {
                        $q->where('status','=','R-DEPARTMENT')
                            ->orWhere(function($q) {
                                $q->where('is_partial',true)
                                    ->whereHas('partials',function($q1){
                                        $q1->where('status','=','R-DEPARTMENT');
                                    });
                            });
                    })
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('details', function($selectQuery){
                        $returnValue = '';
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
                        $paymentDetails = route('payment-request-update',['prid' => $enc_payment_request_id]);
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
            elseif($postMode == 'create-payment-request'){
                /**
                 * Insert Procedure ( Payment Request )
                 *  - Standard Inputs
                 *    1. payment_type ( required )
                 *         IF CASH / PETTY CASH
                 *           - control_number ( nullable )
                 *             [ payment type should be: petty cash = x < 3000 ( dynamic ) cash = x >= 3000 ]
                 *         IF CHEQUE
                 *           - payee ( nullable ), check_type ( required )
                 *             [ if payee is null, payee = requested by ]
                 *    2. category ( required )
                 *         IF OFFICE
                 *           - designate_department_id ( required )
                 *         IF CLIENT ( required, multiple )
                 *           - client_id,reference_number [ quote_number ], name = ( quotation # )
                 *             [ At least 1 client ( w/ or w/out quotation # ) ]
                 *         IF SUPPLIER ( required, multiple )
                 *           - supplier_id, reference_number[ po_number ], name = po-number
                 *    3. account_title
                 *    4. particular
                 *    5. requested_by
                 *    6. request_amount
                 *
                **/
                $attributes = [
                    'payment_type' => 'Payment type',
                    'category' => 'Category',
                    'account_title' => 'Account Title',
                    'particular' => 'Particular',
                    'employee' => 'Employee',
                    'requested_by' => 'Requested By',
                  //  'request_amount' => 'Request Amount',
                    'note' => 'Note',
                ];
                $rules = [
                    'payment_type' => 'required',
                    'category' => 'required',
                    'account_title' => 'required',
                    'particular' => 'required',
                    //'employee' => 'required',
                    'requested_by' => 'required',
                   // 'request_amount' => 'required',
                    'note' => 'required',
                ];
                // nilgay ko din dito kase kapag supplier ang category, kapag request amount ang kulang yun ang unang error na lalabas.
                // sa supplier category kase naka readonly ang request amount. Summation ng total p.o yun
                if($data['category'] == 'SUPPLIER'){
                    $attributes['supplier_key'] = 'Supplier';
                    $rules['supplier_key'] = 'required';
                }
                $attributes['request_amount'] = 'Request Amount';
                $rules['request_amount'] = 'required';
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' =>implode(',',$validator->errors()->all()));
                }else{
                    $attributes = array();
                    $rules = array();
                    // validate per base on selected Category.
                    if($data['payment_type'] == 'CASH'){
                        $attributes['control_number'] = 'Control Number';
                        $rules['control_number'] = '';
                    }else if($data['payment_type'] == 'CHEQUE'){
                        // if payee is null, payee = requested by
                        $attributes['payee_key'] = '';
                        $attributes['payee_name'] = 'Payee';
                        //$rules['payee_key'] = 'required';
                        //$rules['payee_name'] = 'required';
                        if(isset($data['partials'])) {
                            $attributes['cheque_types'] = 'Cheque Type';
                            $attributes['cheque_dates'] = 'Cheque Date';
                            $rules['cheque_dates'] = 'required';
                            $rules['cheque_types'] = 'required';
                        }else{
                            $attributes['cheque_type'] = 'Cheque Type';
                            $attributes['cheque_date'] = 'Cheque Date';
                            $rules['cheque_date'] = 'required';
                            $rules['cheque_type'] = 'required';
                        }
                    }
                    if($data['category'] == 'OFFICE'){
                        $attributes['designate_department'] = 'Designate Department';
                        $rules['designate_department'] = 'required';
                    }elseif($data['category'] == 'CLIENT'){
                        $attributes['clients_key'] = 'Client';
                        $rules['clients_key'] = 'required';
                    }
                    elseif($data['category'] == 'SUPPLIER'){
                        $attributes['supplier_key'] = 'Supplier';
                        $attributes['purchase_order'] = 'Purchase Order';
                        $rules['supplier_key'] = 'required';
                        $rules['purchase_order'] = 'required';
                    }
                    $validator = Validator::make($data,$rules,[],$attributes);
                    if($validator->fails()){
                        return array('success' => 0, 'message' =>implode(',',$validator->errors()->all()));
                    }else{
                        // validate partials
                        $isPartialValidated = true;
                        $totalPartialAmount = 0;
                        if(isset($data['partials'])){
                            foreach($data['partials'] as $index=>$partial){
                                $totalPartialAmount += $partial;
                            }
                            if($totalPartialAmount != $data['request_amount']){
                                $isPartialValidated = false;
                            }
                        }
                        if($isPartialValidated == false){
                            return array('success' => 0, 'message' =>'Partial Payments must equal to requested amount.');
                        }else{
                            $pr_number = generatePrNumber();
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($user,$data,$pr_number){
                                    $amountLimit = paymentRequestAmountLimit('PETTY-CASH'); //base on type
                                    $amount_limit = $amountLimit['amount'];
                                    $payment_type = $data['payment_type'];
                                    $insertQuery = new PaymentRequest();
                                    $insertQuery->pr_number = $pr_number;
                                    if($payment_type == 'CASH'){
                                        if($data['request_amount'] <= $amount_limit){
                                            $payment_type = 'PETTY-CASH';
                                        }else{
                                            $amountLimit = paymentRequestAmountLimit('CASH'); //base on type
                                            $amount_limit = $amountLimit['amount'];
                                            $payment_type = 'CASH';
                                        }
                                    }else{
                                        $amountLimit = paymentRequestAmountLimit('CHEQUE'); //base on type
                                        $amount_limit = $amountLimit['amount'];
                                    }
                                    $insertQuery->type = $payment_type;
                                    $insertQuery->category = $data['category'];
                                    $insertQuery->department_id = $user->department_id;
                                    $insertQuery->account_title_id = $data['account_title'];
                                    $insertQuery->amount_limit = $amount_limit;
                                    $insertQuery->particular_id = $data['particular'];
                                    if($data['payment_type'] == 'CASH'){
                                        if($data['control_number'] != null || $data['control_number'] != ''){
                                            $insertQuery->control_number = $data['control_number'];
                                        }
                                    }
                                    elseif($data['payment_type'] == 'CHEQUE'){
                                        if($data['payee_key'] == '' || $data['payee_key'] == null || $data['payee_key'] == 'null'){
                                            $insertQuery->payee_name = $data['requested_by'];
                                        }else{
                                            $insertQuery->payee_id = $data['payee_key'];
                                            $insertQuery->payee_name = $data['payee_name'];
                                        }
                                        if(!isset($data['partials'])) {
                                            $insertQuery->cheque_type = $data['cheque_type'];
                                            $insertQuery->cheque_date = $data['cheque_date'];
                                        }
                                    }
                                    if($data['category'] == 'OFFICE'){
                                        $insertQuery->designated_department = $data['designate_department'];
                                    }
                                    $insertQuery->note = $data['note'];
                                    $insertQuery->employee_id = $data['employee'];
                                    $insertQuery->requested_by = $data['requested_by'];
                                    $insertQuery->requested_amount = $data['request_amount']; // re-update ito kapag ang supplier ang category.
                                    $insertQuery->created_by = $user->id;
                                    $insertQuery->updated_by = $user->id;
                                    $insertQuery->created_at = getDatetimeNow();
                                    $insertQuery->updated_at = getDatetimeNow();
                                    $insertQuery->save();

                                    // if partial payment
                                    if(isset($data['partials'])){
                                        $partialPayments = array();
                                        foreach($data['partials'] as $index=>$partial){
                                            $partialPayment = array();
                                            $partialPayment['payment_request_id'] = $insertQuery->id;
                                            $partialPayment['amount'] = $partial;
                                            $partialPayment['purpose'] = $data['purposes'][$index];
                                            $partialPayment['cheque_type'] = $data['cheque_types'][$index];
                                            $partialPayment['cheque_date'] = $data['cheque_dates'][$index];
                                            $partialPayment['created_by'] = $user->id;
                                            $partialPayment['updated_by'] = $user->id;
                                            $partialPayment['created_at'] = getDatetimeNow();
                                            $partialPayment['updated_at'] = getDatetimeNow();
                                            array_push($partialPayments,$partialPayment);
                                        }
                                        PaymentRequestPartial::insert($partialPayments);
                                        $insertQuery->is_partial = true;
                                    }
                                    if($data['category'] == 'CLIENT'){
                                        $clients = array();
                                        foreach($data['clients_key'] as $index=>$client_key){
                                            $client = array();
                                            $client['payment_request_id'] = $insertQuery->id;
                                            $client['client_id'] = encryptor('decrypt',$client_key);
                                            $client['name'] = $data['quotations'][$index];
                                            $client['created_at'] = getDatetimeNow();
                                            $client['updated_at'] = getDatetimeNow();
                                            $client['created_by'] = $user->id;
                                            $client['updated_by'] = $user->id;
                                            array_push($clients,$client);
                                        }
                                        $insertDetailsQuery = PaymentRequestDetail::insert($clients);
                                    }
                                    elseif($data['category'] == 'SUPPLIER'){
                                        $suppliers = array();
                                        $requested_amount = 0;
                                        $pos = array();
                                        $supplier_id = encryptor('decrypt',$data['supplier_key']);
                                        foreach($data['purchase_order'] as $index=>$purchase_order){
                                            $supplier = array();
                                            $supplier['payment_request_id'] = $insertQuery->id;
                                            $supplier['supplier_id'] = $supplier_id;
                                            $supplier['name'] = $purchase_order;
                                            array_push($pos,$purchase_order); // getting po's
                                            $supplier['amount'] = $data['grand_total'][$index];
                                            $supplier['ewt_amount'] = $data['ewt'][$index];
                                            $supplier['vat_amount'] = $data['vat'][$index];
                                            $supplier['created_at'] = getDatetimeNow();
                                            $supplier['updated_at'] = getDatetimeNow();
                                            $supplier['created_by'] = $user->id;
                                            $supplier['updated_by'] = $user->id;
                                            $requested_amount += $data['grand_total'][$index];
                                            array_push($suppliers,$supplier);
                                        }
                                        $insertDetailsQuery = PaymentRequestDetail::insert($suppliers);
                                        // if has S.I
                                        if(isset($data['invoice_amount'])){
                                            $liquidations = array();
                                            foreach($data['invoice_amount'] as $index=>$invoice_amount){
                                                $liquidate = array();
                                                $liquidate['category'] = 'SUPPLIER';
                                                $liquidate['type'] = 'S.I';
                                                $liquidate['payment_request_id'] = $insertQuery->id;
                                                $liquidate['department_id'] = $user->department_id;
                                                $liquidate['supplier_id'] = $supplier_id;
                                                $liquidate['po_number'] = $data['invoice_po_number'][$index];
                                                $liquidate['amount'] = $invoice_amount;
                                                $liquidate['payee_id'] = $insertQuery->payee_id;
                                                $liquidate['payee_name'] = $insertQuery->payee_name;
                                                $liquidate['reference_number'] = $data['invoice_reference_number'][$index];
                                                $liquidate['remarks'] = $data['invoice_remarks'][$index];
                                                $liquidate['date_collected'] = getDateNow();
                                                $liquidate['created_by'] = $user->id;
                                                $liquidate['updated_by'] = $user->id;
                                                $liquidate['created_at'] = getDatetimeNow();
                                                $liquidate['updated_at'] = getDatetimeNow();
                                                array_push($liquidations,$liquidate);
                                            }
                                            Liquidation::insert($liquidations);
                                        }
                                        //update po payment status
                                        $updatePurchaseOrderQuery = PurchaseOrder::where('payment_status','=','FOR-REQUEST')
                                            ->whereIn('po_number',$pos)
                                            ->update(['payment_status' => 'REQUESTED']);
                                        $insertQuery->requested_amount = $requested_amount;
                                    }
                                    $insertQuery->timestamps = false;
                                    $insertQuery->save();
                                    DB::commit();
                                });
                                return array('success' => 1, 'message' =>'Payment Request [ '.$pr_number.' ] Created. Page reloading...');
                            }catch (QueryException $exception) {
                                DB::rollback();
                                return array('success' => 0, 'message' =>$exception->errorInfo[2]);
                            }
                        }
                    }
                }
            }
            elseif($postMode == 'update-payment-request'){
                /**
                 * Insert Procedure ( Payment Request )
                 *  - Standard Inputs
                 *    1. payment_type ( required )
                 *         IF CASH / PETTY CASH
                 *           - control_number ( nullable )
                 *             [ payment type should be: petty cash = x < 3000 ( dynamic ) cash = x >= 3000 ]
                 *         IF CHEQUE
                 *           - payee ( nullable ), check_type ( required )
                 *             [ if payee is null, payee = requested by ]
                 *    2. category ( required )
                 *         IF OFFICE
                 *           - designate_department_id ( required )
                 *         IF CLIENT ( required, multiple )
                 *           - client_id,reference_number [ quote_number ], name = ( quotation # )
                 *             [ At least 1 client ( w/ or w/out quotation # ) ]
                 *         IF SUPPLIER ( required, multiple )
                 *           - supplier_id, reference_number[ po_number ], name = po-number
                 *    3. account_title
                 *    4. particular
                 *    5. requested_by
                 *    6. request_amount
                 *
                **/
                $attributes = [
                    'payment_type' => 'Payment type',
                    'category' => 'Category',
                    'account_title' => 'Account Title',
                    'particular' => 'Particular',
                    'employee' => 'Employee',
                    'requested_by' => 'Requested By',
                  //  'request_amount' => 'Request Amount',
                    'note' => 'Note',
                ];
                $rules = [
                    'payment_type' => 'required',
                    'category' => 'required',
                    'account_title' => 'required',
                    'particular' => 'required',
                    //'employee' => 'required',
                    'requested_by' => 'required',
                   // 'request_amount' => 'required',
                    'note' => 'required',
                ];
                // nilgay ko din dito kase kapag supplier ang category, kapag request amount ang kulang yun ang unang error na lalabas.
                // sa supplier category kase naka readonly ang request amount. Summation ng total p.o yun
                if($data['category'] == 'SUPPLIER'){
                    $attributes['supplier_key'] = 'Supplier';
                    $rules['supplier_key'] = 'required';
                }
                $attributes['request_amount'] = 'Request Amount';
                $rules['request_amount'] = 'required';

                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' =>implode(',',$validator->errors()->all()));
                }else{
                    $attributes = array();
                    $rules = array();
                    // validate per base on selected Category.
                    if($data['payment_type'] == 'CASH'){
                        $attributes['control_number'] = 'Control Number';
                        $rules['control_number'] = '';
                    }else if($data['payment_type'] == 'CHEQUE'){
                        // if payee is null, payee = requested by
                        $attributes['payee_key'] = '';
                        $attributes['payee_name'] = 'Payee';
                        if(isset($data['partials'])) {
                            $attributes['cheque_types'] = 'Cheque Type';
                            $attributes['cheque_dates'] = 'Cheque Date';
                            $rules['cheque_dates'] = 'required';
                            $rules['cheque_types'] = 'required';
                        }else{
                            $attributes['cheque_type'] = 'Cheque Type';
                            $attributes['cheque_date'] = 'Cheque Date';
                            $rules['cheque_date'] = 'required';
                            $rules['cheque_type'] = 'required';
                        }

                    }
                    if($data['category'] == 'OFFICE'){
                        $attributes['designate_department'] = 'Designate Department';
                        $rules['designate_department'] = 'required';
                    }elseif($data['category'] == 'CLIENT'){
                        $attributes['clients_key'] = 'Client';
                        $rules['clients_key'] = 'required';
                    }
                    elseif($data['category'] == 'SUPPLIER'){
                        $attributes['supplier_key'] = 'Supplier';
                        $attributes['purchase_order'] = 'Purchase Order';
                        $rules['supplier_key'] = 'required';
                        $rules['purchase_order'] = 'required';
                    }
                    $validator = Validator::make($data,$rules,[],$attributes);
                    if($validator->fails()){
                        return array('success' => 0, 'message' =>implode(',',$validator->errors()->all()));
                    }else{
                        //validate partials
                        $isPartialValidated = true;
                        $totalPartialAmount = 0;
                        if(isset($data['partials'])){
                            foreach($data['partials'] as $index=>$partial){
                                $totalPartialAmount += $partial;
                            }
                            if($totalPartialAmount != $data['request_amount']){
                                $isPartialValidated = false;
                            }
                        }
                        if($isPartialValidated == false){
                            return array('success' => 0, 'message' =>'Partial Payments must equal to requested amount.');
                        }else{
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($user,$data) {
                                    $amountLimit = paymentRequestAmountLimit('PETTY-CASH'); //base on type
                                    $amount_limit = $amountLimit['amount'];
                                    $payment_request_id = encryptor('decrypt',$data['payment_request_key']);
                                    $payment_type = $data['payment_type'];
                                    $updateQuery = PaymentRequest::find($payment_request_id);
                                    if($payment_type == 'CASH'){
                                        if($data['request_amount'] <= $amount_limit){
                                            $payment_type = 'PETTY-CASH';
                                        }else{
                                            $amountLimit = paymentRequestAmountLimit('CASH'); //base on type
                                            $amount_limit = $amountLimit['amount'];
                                            $payment_type = 'CASH';
                                        }
                                    }else{
                                        $amountLimit = paymentRequestAmountLimit('CHEQUE'); //base on type
                                        $amount_limit = $amountLimit['amount'];
                                    }
                                    $updateQuery->amount_limit = $amount_limit;
                                    $updateQuery->type = $payment_type;
                                    $updateQuery->category = $data['category'];
                                    $updateQuery->account_title_id = $data['account_title'];
                                    $updateQuery->particular_id = $data['particular'];
                                    if($data['payment_type'] == 'CASH'){
                                        if($data['control_number'] != null || $data['control_number'] != ''){
                                            $updateQuery->control_number = $data['control_number'];
                                        }
                                        if($updateQuery->is_partial == true){
                                            $deleteQuery = PaymentRequestPartial::where('payment_request_id','=',$updateQuery->id)->forceDelete();
                                            $updateQuery->is_partial = false;
                                        }
                                    }
                                    elseif($data['payment_type'] == 'CHEQUE'){
                                        if($data['payee_key'] == '' || $data['payee_key'] == null || $data['payee_key'] == 'null'){
                                            $updateQuery->payee_name = $data['requested_by'];
                                        }else{
                                            $updateQuery->payee_id = $data['payee_key'];
                                            $updateQuery->payee_name = $data['payee_name'];
                                        }
                                        if(!isset($data['partials'])) {
                                            $updateQuery->cheque_type = $data['cheque_type'];
                                            $updateQuery->cheque_date = $data['cheque_date'];
                                        }

                                        // if partial payment
                                        if(isset($data['partials'])){
                                            $partialPayments = array();
                                            foreach($data['partials'] as $index=>$partial){
                                                $partialPayment = array();
                                                $partialPayment['payment_request_id'] = $updateQuery->id;
                                                $partialPayment['amount'] = $partial;
                                                $partialPayment['purpose'] = $data['purposes'][$index];
                                                $partialPayment['cheque_type'] = $data['cheque_types'][$index];
                                                $partialPayment['cheque_date'] = $data['cheque_dates'][$index];
                                                $partialPayment['created_by'] = $user->id;
                                                $partialPayment['updated_by'] = $user->id;
                                                $partialPayment['created_at'] = getDatetimeNow();
                                                $partialPayment['updated_at'] = getDatetimeNow();
                                                array_push($partialPayments,$partialPayment);
                                            }
                                            $deleteQuery = PaymentRequestPartial::where('payment_request_id','=',$updateQuery->id)->forceDelete();
                                            PaymentRequestPartial::insert($partialPayments);
                                            $updateQuery->is_partial = true;
                                        }else{ // kapag walang partial na nilagay
                                            $updateQuery->is_partial = false;
                                        }
                                    }
                                    if($data['category'] == 'OFFICE'){
                                        $updateQuery->designated_department = $data['designate_department'];
                                    }
                                    $updateQuery->note = $data['note'];
                                    $updateQuery->employee_id = $data['employee'];
                                    $updateQuery->requested_by = $data['requested_by'];
                                    $updateQuery->requested_amount = $data['request_amount']; // re-update ito kapag ang supplier ang category.
                                    $updateQuery->updated_by = $user->id;
                                    $updateQuery->updated_at = getDatetimeNow();
                                    $updateQuery->save();

                                    if($data['category'] == 'CLIENT'){
                                        $clients = array();
                                        foreach($data['clients_key'] as $index=>$client_key){
                                            $client = array();
                                            $client['payment_request_id'] = $updateQuery->id;
                                            $client['client_id'] = encryptor('decrypt',$client_key);
                                            $client['name'] = $data['quotations'][$index];
                                            $client['created_at'] = getDatetimeNow();
                                            $client['updated_at'] = getDatetimeNow();
                                            $client['created_by'] = $user->id;
                                            $client['updated_by'] = $user->id;
                                            array_push($clients,$client);
                                        }
                                        $deleteQuery = PaymentRequestDetail::where('payment_request_id','=',$updateQuery->id)->forceDelete();
                                        $insertDetailsQuery = PaymentRequestDetail::insert($clients);
                                    }
                                    // COMMENT MUNA ITO KASE HINDI NA NA EEDIT YUNG PO NG SELCTED SUPPLIER
//                                    elseif($data['category'] == 'SUPPLIER'){
//                                        $suppliers = array();
//                                        $requested_amount = 0;
//                                        $pos = array();
//                                        foreach($data['purchase_order'] as $index=>$purchase_order){
//                                            $supplier = array();
//                                            $supplier['payment_request_id'] = $updateQuery->id;
//                                            $supplier['supplier_id'] = encryptor('decrypt',$data['supplier_key']);
//                                            $supplier['name'] = $purchase_order;
//                                            array_push($pos,$purchase_order);
//                                            $supplier['amount'] = $data['grand_total'][$index];
//                                            $supplier['created_at'] = getDatetimeNow();
//                                            $supplier['updated_at'] = getDatetimeNow();
//                                            $supplier['created_by'] = $user->id;
//                                            $supplier['updated_by'] = $user->id;
//                                            $requested_amount += $data['grand_total'][$index];
//                                            array_push($suppliers,$supplier);
//                                        }
//                                        /**
//                                        $comparePO = array_diff($data['old_purchase_order'],$pos);// kapag same lang no need to update p.o payment status
//                                        if(count($comparePO) > 0){
//                                        // has difference
//                                        //revert p.o payment status to FOR-REQUEST
//                                        $updatePurchaseOrderQuery = PurchaseOrder::where('payment_status','=','REQUESTED')
//                                        ->whereIn('po_number',$data['old_purchase_order'])
//                                        ->update(['payment_status' => 'FOR-REQUEST']);
//                                        //update p.o payment status to REQUESTED
//                                        $updatePurchaseOrderQuery = PurchaseOrder::where('payment_status','=','REQUESTED')
//                                        ->whereIn('po_number',$data['old_purchase_order'])
//                                        ->update(['payment_status' => 'REQUESTED']);
//                                        }
//                                         **/
//                                        $deleteQuery = PaymentRequestDetail::where('payment_request_id','=',$updateQuery->id)->forceDelete();
//                                        $insertDetailsQuery = PaymentRequestDetail::insert($suppliers);
//                                        $updateQuery->timestamps = false;
//                                        $updateQuery->requested_amount = $requested_amount;
//                                    }
                                    DB::commit();
                                });
                                return array('success' => 1, 'message' =>'Payment Request  Updated. Page reloading...');
                            }catch (QueryException $exception) {
                                DB::rollback();
                                return array('success' => 0, 'message' =>$exception->errorInfo[2]);
                            }
                        }

                    }
                }
            }
            elseif($postMode == 'supplier-validated-po'){
                $supplier_id = encryptor('decrypt',$data['supplier']);
                $payment_terms = ['COD','DATED-CHECKS'];
                $withTermsHtml = '';
                if(isset($data['payment_method'])){
                    $payment_terms = ['WITH-TERMS'];
                }
                if(isset($data['is_vat'])){
                    $selectQuery = PurchaseOrder::where('supplier_id','=',$supplier_id)
                        ->where('status','=','APPROVED')
                        ->where('payment_status','=','FOR-REQUEST')
                        ->where('vat_amount','>',0)
                        ->whereIn('payment_type',$payment_terms)
                        ->get();
                }else{
                    $selectQuery = PurchaseOrder::where('supplier_id','=',$supplier_id)
                        ->where('status','=','APPROVED')
                        ->where('payment_status','=','FOR-REQUEST')
                        ->where('vat_amount','<',1)
                        ->whereIn('payment_type',$payment_terms)
                        ->get();
                }

                if($selectQuery->count() > 0){
                    $resultHtml = '';
                    foreach($selectQuery as $purchaseOrder){
                        $enc_purchase_order_id = encryptor('encrypt',$purchaseOrder->id);
                        $paymentType = $purchaseOrder->payment_type;
                        $withTermsHtml = '';
                        if(!empty($purchaseOrder->payment_terms)){
                            $paymentType .= ' [ '.$purchaseOrder->payment_terms.' ] Day/s';
                            $withTermsHtml = ' | <span onClick=addInvoice("'.$enc_purchase_order_id.'") style="cursor:pointer" class="badge badge-info">Add S.I</span>';
                        }
                        $resultHtml .='
                            <tr id="po-row-'.$enc_purchase_order_id.'">
                                <td>
                                    <input type="hidden" name="keys[]" value="'.$enc_purchase_order_id.'" />
                                    <input type="hidden" id="po-number-'.$enc_purchase_order_id.'" name="po[]" value="'.$purchaseOrder->po_number.'" />
                                    <input type="hidden" id="po-grand-total-'.$enc_purchase_order_id.'"  value="'.round($purchaseOrder->grand_total,2).'" />
                                    <input type="hidden" id="invoice-po-supplier-'.$enc_purchase_order_id.'"  value="'.$purchaseOrder->supplier_id.'" />
                                    '.$purchaseOrder->po_number.''.$withTermsHtml.'
                                    <hr class="m-0 mt-1 mb-1">
                                    <text class="text-info">P.TYPE: '.$paymentType.'</text>
                                </td>
                                <td>
                                    <input type="hidden" name="gt[]" value="'.$purchaseOrder->grand_total.'" />
                                    <input type="hidden" name="ewt[]" value="'.$purchaseOrder->ewt_amount.'" />
                                    <input type="hidden" name="vat[]" value="'.$purchaseOrder->vat_amount.'" />
                                    &#8369; '.number_format($purchaseOrder->grand_total,2).'
                                    <hr class="m-0">
                                    <p class="m-0 text-info">VAT: &#8369; '.$purchaseOrder->vat_amount.'</p>
                                    <p class="m-0 text-info">EWT: &#8369; '.$purchaseOrder->ewt_amount.'</p>
                                </td>
                                <td>
                                    <button onClick=removeRow("po-row-'.$enc_purchase_order_id.'") class="btn btn-icon btn-danger btn-sm"><span class="fas fa-times"></span></button>
                                </td>
                            </tr>
                        ';
                    }
                    return array('success' => 1, 'message' =>'Generate Success', 'data' => $resultHtml);
                }else{
                    return array('success' => 0, 'message' =>'No P.O in this supplier is FOR-REQUEST payment');
                }
            }
            elseif($postMode == 'supplier-list'){
                $selectQuery = Supplier::with('industry')->with('department')
                                    ->where('archive','=',false)
                                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('name', function($selectQuery) use($data){
                        $returnValue = '';
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->name;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info">Industry: '.$selectQuery->industry->name.'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_supplier_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '';
                        $returnValue .= '
                            <input type="hidden" id="'.$enc_supplier_id.'-supplier" value="'.$selectQuery->department->code.' | '.$selectQuery->name.'">
                            <div class="custom-control custom-checkbox mb-1 ">
                                <input type="checkbox" class="custom-control-input" checked id="'.$enc_supplier_id.'-with-terms">
                                <label class="custom-control-label" for="'.$enc_supplier_id.'-with-terms">With Terms</label>
                            </div>
                            <div class="custom-control custom-checkbox mb-1">
                                <input type="checkbox" class="custom-control-input" id="'.$enc_supplier_id.'-with-vat" checked="">
                                <label class="custom-control-label" for="'.$enc_supplier_id.'-with-vat">VAT</label>
                            </div>
                            <button type="button" onClick=addPrSupplier("'.$enc_supplier_id.'") class="btn btn-info btn-xs">Select</button>
                        ';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'client-quotations-list'){
                $client_id = encryptor('decrypt',$data['client']);
                $selectQuery = Quotation::where('client_id','=',$client_id)
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('name', function($selectQuery) use($data){
                        $returnValue = '';
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->status.'</span> | '.$selectQuery->quote_number;
                        $returnValue .= '<hr class="mt-1 mb-1">';
                        $returnValue .= '<text class="text-info">Subject: '.$selectQuery->subject.'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_quotation_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<button type="button" onClick=addPrClientQuotation("'.$enc_quotation_id.'","'.$selectQuery->quote_number.'") class="btn btn-info btn-xs">Select</button>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'add-particulars'){
                $account_title_id = $data['key'];
                $attributes = [
                    'key' => 'Account Title',
                    'name' => 'Particular Name',
                ];
                $rules = [
                    'key' => 'required',
                    'name' => 'required|unique:account_title_particulars,name,NULL,id,account_title_id,'.$account_title_id,
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' =>implode(',',$validator->errors()->all()));
                }else{
                    $insertQuery = new AccountTitleParticular();
                    $insertQuery->account_title_id = $account_title_id;
                    $insertQuery->name = trim($data['name']);
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    $insertQuery->created_at = getDatetimeNow();
                    $insertQuery->updated_at = getDatetimeNow();
                    if($insertQuery->save()){
                        return array('success' => 1, 'message' =>'Particular Added');
                    }else{
                        return array('success' => 0, 'message' =>'Unable to save particular. Please try again');
                    }
                }
            }
            elseif($postMode == 'employee-list'){
                $selectQuery = Employee::with('department')
                    ->with('position')
                    ->whereNotIn('status',['SEPARATED'])
                    ->orderBy('first_name','asc');
                    return Datatables::eloquent($selectQuery)
                        ->addColumn('name', function($selectQuery) use($data){
                            $name = $selectQuery->first_name.' '.$selectQuery->last_name;
                            $returnValue = '';
                            $returnValue .= $name;
                            $returnValue .= '<hr class="m-0">';
                            $returnValue .= '<text class="text-info"><strong title="'.$selectQuery->department->name.'">'.$selectQuery->department->code.':</strong> '.$selectQuery->position->name.'</text>';
                            return $returnValue;
                        })
                        ->addColumn('actions', function($selectQuery){
                            $enc_employee_id = encryptor('encrypt',$selectQuery->id);
                            $name = $selectQuery->first_name.' '.$selectQuery->last_name;
                            $returnValue = '<button onClick=selectRequestedBy("'.$enc_employee_id.'") type="button" class="btn btn-info btn-xs">Select</button>';
                            $returnValue.= '
                                <input type="hidden" value="'.$selectQuery->id.'" id="employee-key-'.$enc_employee_id.'"/>
                                <input type="hidden" value="'.$name.'" id="employee-name-'.$enc_employee_id.'"/>
                            ';
                            return $returnValue;
                        })
                        ->smart(true)
                        ->escapeColumns([])
                        ->addIndexColumn()
                        ->make(true);
            }
            elseif($postMode == 'get-particulars'){
                $account_title_id = $data['key'];
                $selectQuery = AccountTitleParticular::where('account_title_id','=',$account_title_id)->get();
                if($selectQuery->count() > 0){
                    return array('success' => 1, 'message' => 'Particulars generated.','data'=>json_encode($selectQuery));
                }else{
                    return array('success' => 0, 'message' => 'No particulars in this account title. Please add particular');
                }
            }
            elseif($postMode == 'clients-list'){
                $selectQuery = Client::with('industry')
                    ->orderBy('name','asc');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('name', function($selectQuery) use($data){
                        $returnValue = '';
                        $returnValue .= $selectQuery->name;
                        $returnValue .= '<hr class="mt-1 mb-1">';
                        $returnValue .= '<text class="text-info">Industry: '.$selectQuery->industry->name.'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $tempKey = strtotime(getDatetimeNow());
                        $enc_client_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '
                            <input type="hidden" id="'.$enc_client_id.'-name" value="'.$selectQuery->name.'">
                            <input type="hidden" id="'.$enc_client_id.'-industry" value="'.$selectQuery->industry->name.'">
                        ';
                        $returnValue .= '<button type="button" onClick=addPrClient("'.$enc_client_id.'","'.$tempKey.'") class="btn btn-info btn-xs">Add</button>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'payee-list'){
                $payee_name = $data['q'];
                $selectQuery = Payee::with('createdBy')
                    ->where('name','LIKE','%'.$payee_name.'%')
                    ->orderBy('name','asc')
                    ->get();
                return array('success' => 1, 'message' =>'Generate','data' => json_encode($selectQuery));
            }
            else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode == 'ewt-liquidation'){
                $payment_request_id = encryptor('decrypt',$data['payment_request_key']);
                $selectQuery = Liquidation::where('payment_request_id','=',$payment_request_id)
                                        ->where('supplier_id','=',$data['supplier_key'])
                                        ->where('po_number','=',$data['po_number'])
                                        ->where('type','=',$data['type'])
                                        ->count();
                if($selectQuery > 1){
                    Session::flash('success', 0);
                    Session::flash('message',$data['po_number'].' EWT and VAT already Liquidated');
                }else{
                    $insertQuery = new Liquidation();
                    $insertQuery->payment_request_id = $payment_request_id;
                    $insertQuery->category = $data['category'];
                    $insertQuery->type = $data['type'];
                    $insertQuery->payee_id = $data['payee_key'];
                    $insertQuery->payee_name = $data['payee_name'];
                    $insertQuery->supplier_id = $data['supplier_key'];
                    $insertQuery->department_id = $user->department_id;
                    $insertQuery->po_number = $data['po_number'];
                    $insertQuery->date_collected = getDateNow();
                    $insertQuery->reference_number = $data['po_number'];
                    $insertQuery->ewt_amount = $data['ewt'];
                    $insertQuery->vat_amount = $data['vat'];
                    $insertQuery->remarks = 'TRIGGERED LIQUIDATION BUTTON IN DETAILS';
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    $insertQuery->created_at = getDatetimeNow();
                    $insertQuery->updated_at = getDatetimeNow();
                    if($insertQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message',$data['po_number'].' EWT and VAT Liquidated');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message','Unable to liquidate ewt. Please try again');
                    }
                }

                return back();
            }
            elseif($postMode == 'update-partial-payment-request-status'){
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
                    if($data['status'] == 'FOR-APPROVAL'){
                        // wala tayo ditong multiple sa part na ito kapag ang p.o staus ay for approval.
                        $partial_payment_request_id = encryptor('decrypt',$data['selected_partials']);
                        $updateQuery = PaymentRequestPartial::find($partial_payment_request_id);
                        if($updateQuery){
                            $updateQuery->status = 'FOR-APPROVAL';
                            $updateQuery->updated_at = getDatetimeNow();
                            $updateQuery->updated_by = $user->id;
                            if($updateQuery->save()){
                                Session::flash('success', 1);
                                Session::flash('message','Partial Payment request status Updated.');
                            }else{
                                Session::flash('success', 0);
                                Session::flash('message','Unable to update partial status. Please try again');
                            }
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message','Unable to find partial details. Please try again');
                        }
                    }
                    elseif($data['status'] == 'APPROVED'){
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
                    elseif($data['status'] == 'RELEASED'){
                        $payment_request_id = encryptor('decrypt',$data['payment_request_key']);
                        $updateQuery = PaymentRequest::with('partials')->find($payment_request_id);
                        if($updateQuery){
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($user, $data, $updateQuery,$statusPosition) {
                                    if($statusPosition['RELEASED'] > $statusPosition[$updateQuery->status]) {
                                        $updateQuery->status = 'RELEASED';
                                        $updateQuery->updated_by = $user->id;
                                        $updateQuery->updated_at = getDatetimeNow();
                                        $updateQuery->save();
                                    }
                                    // update partials
                                    if(is_array($data['selected_partials'])){
                                        // multiple
                                        foreach($data['selected_partials'] as $index=>$partial){
                                            $partial_id = encryptor('decrypt',$partial);
                                            $updatePartialQuery = $updateQuery->partials->where('id','=',$partial_id)->first();
                                            $updatePartialQuery->status = 'RELEASED';
                                            $updatePartialQuery->bank = $data['bank'][$index];
                                            $updatePartialQuery->cheque_date = $data['cheque_date'][$index];
                                            $updatePartialQuery->cheque_number = $data['cheque_number'][$index];
                                            $updatePartialQuery->remarks = $data['remarks'][$index];
                                            $updatePartialQuery->updated_by = $user->id;
                                            $updatePartialQuery->updated_at = getDatetimeNow();
                                            $updatePartialQuery->save();
                                        }
                                    }else{
                                        // hindi array. $data['selected_partials']
                                        $partial_id = encryptor('decrypt',$data['selected_partials']);
                                        $updatePartialQuery = $updateQuery->partials->where('id','=',$partial_id)->first();
                                        $updatePartialQuery->status = 'RELEASED';
                                        $updatePartialQuery->bank = $data['bank'];
                                        $updatePartialQuery->cheque_date = $data['cheque_date'];
                                        $updatePartialQuery->cheque_number = $data['cheque_number'];
                                        $updatePartialQuery->remarks = $data['remarks'];
                                        $updatePartialQuery->updated_by = $user->id;
                                        $updatePartialQuery->updated_at = getDatetimeNow();
                                        $updatePartialQuery->save();
                                    }
                                    if($updateQuery->category == 'SUPPLIER'){
                                        $pos = array();
                                        foreach($updateQuery->details as $po){
                                            array_push($pos,$po->name);
                                        }
                                        if($updateQuery->partials->where('status','!=','RELEASED')->count() > 0) { // kapag may parital pang hindi narerelease
                                            PurchaseOrder::where('payment_status','=','REQUESTED')
                                                ->whereIn('po_number',$pos)
                                                ->update(['payment_status' => 'PARTIAL']);
                                        }else{   // lahat nairelease na
                                            PurchaseOrder::whereIn('po_number',$pos)
                                                ->update(['payment_status' => 'COMPLETED']);
                                        }
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
                        if($data['status'] == 'PENDING'){
                            // also fall here revert from rejected status
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($user, $data, $selectQuery) {
                                    $selectQuery->status = 'PENDING';
                                    $selectQuery->updated_by = $user->id;
                                    $selectQuery->updated_at = getDatetimeNow();
                                    $selectQuery->save();
                                    if($selectQuery->is_partial == true){
                                        foreach($selectQuery->partials as $partial){
                                            $partial->status = 'PENDING';
                                            $partial->updated_by = $user->id;
                                            $partial->updated_at = getDatetimeNow();
                                            //$partial->remarks = $data['remarks'];
                                            $partial->save();
                                        }
                                    }
                                    DB::commit();
                                });
                                Session::flash('success', 1);
                                Session::flash('message', $selectQuery->pr_number.' is now PENDING.');
                                return redirect(route('payment-request-update',['prid' => $enc_payment_request_id]));
                            }catch (QueryException $exception) {
                                DB::rollback();
                                Session::flash('success', 0);
                                Session::flash('message', $exception->errorInfo[2]);
                                return back();
                            }
                        }
                        elseif($data['status'] == 'FOR-APPROVAL'){
                            $attributes = [
                                'account_title' => 'Account_Title',
                                'particular' => 'Particular',
                            ];
                            $rules = [
                                'account_title' => 'required|exists:accounting_titles,id',
                                'particular' => 'required|exists:account_title_particulars,id',
                            ];
                            if($selectQuery->is_partial == true){
                                $attributes['selected_partials'] = 'Partial Amount [ Checkable ]';
                                $rules['selected_partials'] = 'required';
                            }
                            $validator = Validator::make($data,$rules,[],$attributes);
                            if($validator->fails()){
                                Session::flash('success', 0);
                                Session::flash('message',implode(',',$validator->errors()->all()));
                            }else{
                                //validate partials
                                $isPartialValidated = true;
                                if($selectQuery->is_partial == true) {
                                    if($selectQuery->partials->count() != count($data['partials'])){
                                        $isPartialValidated = false;
                                    }
                                }
                                if($isPartialValidated == false){
                                    Session::flash('success', 0);
                                    Session::flash('message','Saved Partials not match on retrieved partials. Please Click `Update request` button to update partials');
                                }else{
                                    DB::beginTransaction();
                                    try {
                                        DB::transaction(function () use ($user, $data, $selectQuery) {
                                            $selectQuery->account_title_id = $data['account_title'];
                                            $selectQuery->particular_id = $data['particular'];
                                            $selectQuery->status = 'FOR-APPROVAL';
                                            $selectQuery->updated_by = $user->id;
                                            $selectQuery->updated_at = getDatetimeNow();
                                            $selectQuery->save();
                                            //for partial payment requests
                                            if($selectQuery->is_partial == true) {
                                                $partial_ids = array();
                                                foreach($data['selected_partials'] as $partial){
                                                    array_push($partial_ids,encryptor('decrypt',$partial));
                                                }
                                                $updatePartialQuery = PaymentRequestPartial::where('payment_request_id','=',$selectQuery->id)
                                                    ->whereIn('id',$partial_ids)
                                                    ->update(['status' => 'FOR-APPROVAL',
                                                            'updated_at' => getDateTimeNow(),
                                                            'updated_by' => $user->id
                                                        ]);
                                            }
                                            DB::commit();
                                        });
                                        Session::flash('success', 1);
                                        if($selectQuery->type == 'CHEQUE'){
                                            Session::flash('message', $selectQuery->pr_number.' is now FOR APPROVAL. Please wait for proprietors response');
                                        }else{
                                            Session::flash('message', $selectQuery->pr_number.' is now FOR APPROVAL. Please wait for accounting response');
                                        }
                                        return redirect(route('payment-request-details',['prid' => $enc_payment_request_id]));
                                    }catch (QueryException $exception) {
                                        DB::rollback();
                                        Session::flash('success', 0);
                                        Session::flash('message', $exception->errorInfo[2]);
                                    }
                                }
                            }
                            return back();
                        }
                        elseif($data['status'] == 'REJECTED'){
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
                                return redirect(route('payment-request-details',['prid' => $enc_payment_request_id]));
                            }catch (QueryException $exception) {
                                DB::rollback();
                                Session::flash('success', 0);
                                Session::flash('message', $exception->errorInfo[2]);
                                return back();
                            }
                        }
                        elseif($data['status'] == 'VOID'){
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($user,$data,$selectQuery) {
                                    $selectQuery->status = 'VOID';
                                    $selectQuery->updated_by = $user->id;
                                    $selectQuery->updated_at = getDatetimeNow();
                                    $selectQuery->remarks = $data['remarks'];
                                    $selectQuery->save();
                                    // revert P.O Status to FOR-REQUEST
                                    if($selectQuery->category == 'SUPPLIER'){
                                        $pos = array();
                                        foreach($selectQuery->details as $po){
                                            array_push($pos,$po->name);
                                        }
                                        $updatePurchaseOrderQuery = PurchaseOrder::where('payment_status','=','REQUESTED')
                                            ->whereIn('po_number',$pos)
                                            ->update(['payment_status' => 'FOR-REQUEST']);
                                    }
                                    if($selectQuery->is_partial == true){
                                        foreach($selectQuery->partials as $partial){
                                            $partial->status = 'VOID';
                                            $partial->updated_by = $user->id;
                                            $partial->updated_at = getDatetimeNow();
                                            $partial->remarks = $data['remarks'];
                                            $partial->save();
                                        }
                                    }
                                    DB::commit();
                                });
                                Session::flash('success', 1);
                                Session::flash('message', $selectQuery->pr_number.' is now VOID. This data will no longer to modify');
                                return redirect(route('payment-request-details',['prid' => $enc_payment_request_id]));
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
                                return redirect(route('payment-request-details',['prid' => $enc_payment_request_id]));
                            }
                            else{
                                Session::flash('success', 0);
                                Session::flash('message', 'Unable to update status. Please try again');
                                return back();
                            }
                        }
                        elseif($data['status'] == 'RELEASED'){
                            $selectQuery->status = 'RELEASED';
                            $attributes = array();
                            $rules = array();
                            if($selectQuery->type == 'CHEQUE'){
                                $attributes['cheque_number'] = 'Cheque Number';
                                $attributes['cheque_date'] = 'Cheque Date';
                                $rules['cheque_number'] = 'required|integer';
                                $rules['cheque_date'] = 'required';
                            }else{
                                $attributes['voucher_number'] = 'Cheque Number';
                                $rules['voucher_number'] = 'required';
                            }
                            $validator = Validator::make($data,$rules,[],$attributes);
                            if($validator->fails()){
                                Session::flash('success', 0);
                                Session::flash('message',implode(',',$validator->errors()->all()));
                                return back();
                            }else{
                                if($selectQuery->type == 'CHEQUE'){
                                    $selectQuery->cheque_number = $data['cheque_number'];
                                    $selectQuery->cheque_date = $data['cheque_date'];
                                }else{
                                    $selectQuery->voucher_number = $data['voucher_number'];
                                }
                                $selectQuery->remarks = $data['remarks'];
                                $selectQuery->updated_by = $user->id;
                                $selectQuery->updated_at = getDatetimeNow();
                                if($selectQuery->save()){
                                    if($selectQuery->category == 'SUPPLIER'){
                                        $pos = array();
                                        foreach($selectQuery->details as $po){
                                            array_push($pos,$po->name);
                                        }
                                        $updatePurchaseOrderQuery = PurchaseOrder::whereIn('po_number',$pos)
                                            ->update(['payment_status' => 'COMPLETED']);
                                    }
                                    Session::flash('success', 1);
                                    Session::flash('message', $selectQuery->pr_number.' is now RELEASED.');
                                    return redirect(route('payment-request-details',['prid' => $enc_payment_request_id]));
                                }
                                else{
                                    Session::flash('success', 0);
                                    Session::flash('message', 'Unable to update status. Please try again');
                                    return back();
                                }
                            }
                        }
                        elseif( $data['status'] == 'CANCELLED' ){
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($user,$data,$selectQuery) {
                                    $selectQuery->status = 'CANCELLED';
                                    $selectQuery->updated_by = $user->id;
                                    $selectQuery->updated_at = getDatetimeNow();
                                    $selectQuery->remarks = $data['remarks'];
                                    $selectQuery->save();
                                    // revert P.O Status to FOR-REQUEST
                                    if($selectQuery->category == 'SUPPLIER'){
                                        $pos = array();
                                        foreach($selectQuery->details as $po){
                                            array_push($pos,$po->name);
                                        }
                                        $updatePurchaseOrderQuery = PurchaseOrder::where('payment_status','=','REQUESTED')
                                            ->whereIn('po_number',$pos)
                                            ->update(['payment_status' => 'FOR-REQUEST']);
                                    }
                                    if($selectQuery->is_partial == true){
                                        foreach($selectQuery->partials as $partial){
                                            $partial->status = 'CANCELLED';
                                            $partial->updated_by = $user->id;
                                            $partial->updated_at = getDatetimeNow();
                                            $partial->remarks = $data['remarks'];
                                            $partial->save();
                                        }
                                    }
                                    DB::commit();
                                });
                                Session::flash('success', 1);
                                Session::flash('message', $selectQuery->pr_number.' is now CANCELLED. This data will no longer to modify');
                                return redirect(route('payment-request-details',['prid' => $enc_payment_request_id]));
                            }catch (QueryException $exception) {
                                DB::rollback();
                                Session::flash('success', 0);
                                Session::flash('message', $exception->errorInfo[2]);
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
