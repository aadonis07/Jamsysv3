<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use App\PaymentRequest;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use Validator;
use DataTables;
use App\Payee;
use App\Liquidation;

class LiquidationController extends Controller
{
    function showPrDetails(Request $request){
        $data = $request->all();
        $resultHtml ='';
        if(isset($data['pr']) && !empty($data['pr'])){
            $payment_request_id = encryptor('decrypt',$data['pr']);
            $selectQuery = PaymentRequest::with('details')
                ->with('partials')
                ->with('liquidations')
                ->with('createdBy')
                ->where('id','=',$payment_request_id)
                ->where(function($q) {
                    $q->where('status','=','RELEASED')
                        ->orWhere(function($q) {
                            $q->where('is_partial',true)
                                ->whereHas('partials',function($q1){
                                    $q1->where('status','=','RELEASED');
                                });
                        });
                })
                ->first();
            return view('it-department.liquidation.page-load.pr-details')
                        ->with('payment_request',$selectQuery);
        }else{
            $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <p class="m-0">Unable to fetch data. Please try again</p>
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showIndex(){
        $user = Auth::user();
        return view('it-department.liquidation.index')
            ->with('admin_menu','LIQUIDATION')
            ->with('admin_sub_menu','LIST')
            ->with('user',$user);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'remove-to-liquidation'){
                $liquidation_id = encryptor('decrypt',$data['liquidation_key']);
                $selectQuery = Liquidation::find($liquidation_id);
                if($selectQuery){
                    $selectQuery->forceDelete();
                    return array('success' => 1, 'message' =>'Liquidated data removed.');
                }else{
                    return array('success' => 0, 'message' =>'Unable to remove. Please try again');
                }
            }
            elseif($postMode == 'liquidation-list'){
                $payment_request_id = encryptor('decrypt',$data['payment_request']);
                $selectQuery = Liquidation::where('payment_request_id','=',$payment_request_id)
                                        ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                            ->addColumn('actions', function($selectQuery){
                                $enc_liquidation_id = encryptor('encrypt',$selectQuery->id);
                                $returnValue = '<button id="remove-liquidated-'.$enc_liquidation_id.'" type="button" onClick=removeLiquidated("'.$enc_liquidation_id.'",this) class="btn btn-danger btn-icon btn-sm"><span class="fas fa-times"></span></button>';
                                return $returnValue;
                            })
                            ->editColumn('payee_name', function($selectQuery){
                                  $returnValue = $selectQuery->payee_name;
                                  $returnValue .= '<hr class="m-0">';
                                  $returnValue .= '<text class="text-info">CATEGORY: '.$selectQuery->category.'</text>';
                                  return $returnValue;
                            })
                            ->editColumn('date_collected', function($selectQuery){
                                $returnValue = '<span class="badge badge-info">'.$selectQuery->type.'</span> | '.readableDate($selectQuery->date_collected);
                                if(!empty($selectQuery->po_number)){
                                    $returnValue .= ' [ P.O: '.$selectQuery->po_number.' ]';
                                }
                                $returnValue .= '<hr class="m-0">';
                                $returnValue .= '<text class="text-info">REF #: '.$selectQuery->reference_number.'</text>';
                                return $returnValue;
                            })
                            ->editColumn('amount', function($selectQuery){
                                $returnValue = '&#8369; '.number_format($selectQuery->amount,2);
                                $returnValue .= '<hr class="m-0">';
                                $returnValue .= '<p class="text-muted m-0">EWT: &#8369; '.number_format($selectQuery->ewt_amount,2).' | VAT: &#8369; '.number_format($selectQuery->vat_amount,2).'</p>';
                                return $returnValue;
                            })
                            ->smart(true)
                            ->escapeColumns([])
                            ->addIndexColumn()
                            ->make(true);
            }
            elseif($postMode == 'released-payment-request-list'){ // for liquidation
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
                    ->whereHas("liquidations", function($q) {
                    }, '<', 1)
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
                        $returnValue .= '<input type="hidden" value="'.$selectQuery->pr_number.'" id="'.$enc_payment_request_id.'-pr-number"> ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>&nbsp;';
                        $returnValue .= ' <a title="LIQUIDATE " onClick=liquidate("'.$enc_payment_request_id.'") href="javascript:;" class="btn btn-icon btn-xs btn-info"><span class="fas fa-receipt"></span></a>';
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
            elseif($postMode == 'liquidating-payment-request-list'){
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
                    ->whereHas('liquidations', function($q2){
                        $q2->havingRaw('SUM(liquidations.amount) < payment_requests.requested_amount');
                    })
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
                        $returnValue .= '<input type="hidden" value="'.$selectQuery->pr_number.'" id="'.$enc_payment_request_id.'-pr-number"> ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>&nbsp;';
                        $returnValue .= ' <a title="LIQUIDATE " onClick=liquidate("'.$enc_payment_request_id.'") href="javascript:;" class="btn btn-icon btn-xs btn-info"><span class="fas fa-receipt"></span></a>';
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
            elseif($postMode == 'liquidated-payment-request-list'){
                $selectQuery = PaymentRequest::with('details')
                    ->with('partials')
                    ->with('createdBy')
                    ->whereHas('liquidations', function($q2){
                        $q2->havingRaw('SUM(liquidations.amount) >= payment_requests.requested_amount');
                    })
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
                        $returnValue .= '<input type="hidden" value="'.$selectQuery->pr_number.'" id="'.$enc_payment_request_id.'-pr-number"> ';
                        $returnValue .= '<a target="_blank" href="'.$paymentDetails.'" class="btn btn-icon btn-xs btn-info" title="View Details"><span class="fas fa-eye"></span></a>&nbsp;';
                        $returnValue .= ' <a title="LIQUIDATE " onClick=liquidate("'.$enc_payment_request_id.'") href="javascript:;" class="btn btn-icon btn-xs btn-info"><span class="fas fa-receipt"></span></a>';
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
            elseif($postMode == 'payee-list'){
                $payee_name = $data['q'];
                $selectQuery = Payee::with('createdBy')
                    ->where('name','LIKE','%'.$payee_name.'%')
                    ->orderBy('name','asc')
                    ->get();
                return array('success' => 1, 'message' =>'Generate','data' => json_encode($selectQuery));
            }
            elseif($postMode == 'add-to-liquidation'){
                $attributes = [
                    'payment_request' => 'Selected PR ',
                    'payee_key' => 'Payee',
                    'payee_name' => 'Payee',
                    'type' => 'Type',
                    'date_collected' => 'Date Collected',
                    'reference_num' => 'Reference Number',
                    'amount' => 'Amount',
                    'ewt_amount' => 'Ewt Amount',
                    'vat_amount' => 'Vat Amount',
                ];
                $rules = [
                    'payment_request' => 'required',
                    'payee_key' => 'required|exists:payees,id',
                    'payee_name' => 'required',
                    'type' => 'required',
                    'date_collected' => 'required',
                    'reference_num' => 'required',
                    'amount' => 'required',
                    'ewt_amount' => '',
                    'vat_amount' => '',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $payment_request_id = encryptor('decrypt',$data['payment_request']);
                    $selectQuery = PaymentRequest::with('details')
                        ->with('partials')
                        ->with('createdBy')
                        ->where('id','=',$payment_request_id)
                        ->where(function($q) {
                            $q->where('status','=','RELEASED')
                                ->orWhere(function($q) {
                                    $q->where('is_partial',true)
                                        ->whereHas('partials',function($q1){
                                            $q1->where('status','=','RELEASED');
                                        });
                                });
                        })
                        ->first();
                    if($selectQuery){
                        // check id need to designate to p.o
                        // abang ko muna. Dapat ma control dito yung G.T ng p.o at summation ng na liquidate plus yung entry amount niya.
                        /**
                        if(isset($data['liquidate_po_number'])){
                            //get G.T P.O
                            $po = $selectQuery->details->where('name','=',$data['liquidate_po_number'])->first();
                            if($po){
                                // i validate natin kung yung G.T ng p.o is tugma sa total ng docs at yung mga na liquidate na
                                $po_grand_total = $po->amount;
                                $sumLiquidatedPO = Liquidation::where('payment_request_id','=',$selectQuery->id)
                                                                        ->where('po_number','=',$data['liquidate_po_number'])
                                                                        ->sum('amount');

                                return array('success' => 0, 'message' => ''.$sumLiquidatedPO);
                            }else{
                                return array('success' => 0, 'message' => "Unable to find P.O in this P.R. Please try again");
                            }
                        }
                        **/
                        $insertQuery = new Liquidation();
                        $insertQuery->payment_request_id = $selectQuery->id;
                        $insertQuery->category = $selectQuery->category;
                        $insertQuery->type = $data['type'];
                        $insertQuery->payee_id = $data['payee_key'];
                        $insertQuery->payee_name = $data['payee_name'];
                        $insertQuery->department_id = $user->department_id;
                        $insertQuery->date_collected = $data['date_collected'];
                        $insertQuery->reference_number = $data['reference_num'];
                        $insertQuery->amount = $data['amount'];
                        $insertQuery->remarks = $data['remarks'];
                        if(isset($data['liquidate_po_number'])){
                            $po = $selectQuery->details->where('name','=',$data['liquidate_po_number'])->first();
                            if($po){
                                $insertQuery->supplier_id = $po->supplier_id;
                                $insertQuery->po_number = $data['liquidate_po_number'];
                            }
                        }
                        if(!empty($data['ewt_amount'])){
                            $insertQuery->ewt_amount = $data['ewt_amount'];
                        }
                        if(!empty($data['ewt_amount'])){
                            $insertQuery->vat_amount = $data['vat_amount'];
                        }
                        $insertQuery->created_by = $user->id;
                        $insertQuery->updated_by = $user->id;
                        $insertQuery->created_at = getDatetimeNow();
                        $insertQuery->updated_at = getDatetimeNow();
                        if($insertQuery->save()){
                            return array('success' => 1, 'message' => 'Liquidation created.');
                        }else{
                            return array('success' => 0, 'message' => 'Unable to save liquidation. Please try again');
                        }
                    }else{
                        return array('success' => 0, 'message' => 'Unable to find payment request details. Please try again');
                    }
                }
            }
            else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            Session::flash('success', 0);
            Session::flash('message', 'Undefined method please try again');
            return back();
        }
    }
}
