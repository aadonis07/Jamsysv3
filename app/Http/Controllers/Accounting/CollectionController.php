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
use App\CollectionDetail;
use App\Bank;
use App\AccountingPaper;
class CollectionController extends Controller
{
    public function list(){
       
        return view('accounting-department.collections.collection_list')
              ->with('admin_menu','COLLECTION')
              ->with('admin_sub_menu','COLLECTION-LIST');
    }
    public function create_schedule(Request $request){
        $data = $request->all();
        $id = encryptor('decrypt',$data['id']);
        $collection = Collection::find($id);
        $payment_modes = array(
            'CASH'=>'Cash',
            'ONLINE'=>'Online',
            'CHECK'=>'Check',
            'DOCUMENT'=>'Document'
        );
        $banks = Bank::all();
        $papers = AccountingPaper::all();
        return view('accounting-department.collections.view')
              ->with('admin_menu','COLLECTION')
              ->with('admin_sub_menu','ADD-COLLECTION')
              ->with('collection',$collection)
              ->with('payment_modes',$payment_modes)
              ->with('banks',$banks)
              ->with('papers',$papers);
    }
    function showPaymentMode(Request $request){
        $data = $request->all();
        $id = encryptor('decrypt',$data['id']);
        $returnHtml = '';
        if(isset($id)){
            $selectQuery = CollectionDetail::find($id);
            $payment_modes = array(
                'CASH'=>'Cash',
                'ONLINE'=>'Online',
                'CHECK'=>'Check'
            );
            $banks = Bank::all();
            $bank_content = '';
            foreach($banks as $bank){
                $bank_mode = '';
                if($selectQuery->bank_id==$bank->id){
                    $bank_mode = 'selected';
                }
                $bank_content = '<option value="'.$bank->id.'" '.$bank_mode.'>'.$bank->name.'</option>';
            }
            $payment_type = '';
            foreach($payment_modes as $index=>$payment_mode){
                $mode='';
                if($selectQuery->payment_type==$index){
                    $mode='selected';
                }
                $payment_type .= '<option value="'.$index.'" '.$mode.'>'.$payment_mode.'</option>';
            }
            $cheque = '
                <div class="form-group" id="ucheck-content">
                    <div class="input-group input-group-multi-transition">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Check Number</span>
                        </div>
                        <input type="text" class="form-control" required name="ucheck-number" value="'.$selectQuery->check_number.'" onkeypress="return isNumberKey(event)" aria-label="Check Number" placeholder="Check Number">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Check Date</span>
                        </div>
                        <input class="form-control" type="date" required aria-label="Check Date" value="'.$selectQuery->check_date.'" name="ucheck-date" />
                    </div>
                </div>
                <div class="form-group" id="uonline-content" style="display:none;">
                    <label>Bank</label>
                    <select class="form-control" name="ubank" required>
                        <option value=""></option>
                        '.$bank_content.'
                    </select> 
                </div>
            ';
            $cash = '
                <div class="form-group" id="ucheck-content" style="display:none;">
                        <div class="input-group input-group-multi-transition">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Check Number</span>
                            </div>
                            <input type="text" class="form-control" required name="ucheck-number" value="'.$selectQuery->check_number.'" onkeypress="return isNumberKey(event)" aria-label="Check Number" placeholder="Check Number">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Check Date</span>
                            </div>
                            <input class="form-control" type="date" required aria-label="Check Date" value="'.$selectQuery->check_date.'" name="ucheck-date" />
                        </div>
                </div>
                <div class="form-group" id="ucash-content">
                    <div class="input-group input-group-multi-transition">
                        <div class="input-group-prepend">
                            <span class="input-group-text">PHP</span>
                        </div>
                        <input type="text" class="form-control" required name="uamount" value="'.$selectQuery->amount_paid.'" onkeypress="return isNumberKey(event)" aria-label="Amount" placeholder="Amount">
                        <div class="input-group-prepend">
                            <span class="input-group-text">PHP</span>
                        </div>
                        <input type="text" class="form-control" name="uwith-held-amount" value="'.$selectQuery->ewt_amount.'" onkeypress="return isNumberKey(event)" aria-label="With Held Amount" placeholder="With Held Amount">
                    </div><br class="m-0">
                    <div class="input-group input-group-multi-transition">
                        <div class="input-group-prepend">
                            <span class="input-group-text">PHP</span>
                        </div>
                        <input type="text" class="form-control" name="uother-amount" value="'.$selectQuery->charge_amount.'" onkeypress="return isNumberKey(event)" aria-label="Other Amount" placeholder="Other Amount">
                    </div>
                </div>
                <div class="form-group" id="uonline-content" style="display:none;">
                    <label>Bank</label>
                    <select class="form-control" name="ubank" required>
                        <option value=""></option>
                        '.$bank_content.'
                    </select> 
                </div>
            ';
            $online = '
                <div class="form-group" id="ucheck-content" style="display:none;">
                    <div class="input-group input-group-multi-transition">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Check Number</span>
                        </div>
                        <input type="text" class="form-control" required name="ucheck-number" value="'.$selectQuery->check_number.'" onkeypress="return isNumberKey(event)" aria-label="Check Number" placeholder="Check Number">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Check Date</span>
                        </div>
                        <input class="form-control" type="date" required aria-label="Check Date" value="'.$selectQuery->check_date.'" name="ucheck-date" />
                    </div>
                </div>
              
                <div class="form-group" id="uonline-content">
                    <label>Bank</label>
                    <select class="form-control" name="ubank" required>
                        <option value=""></option>
                        '.$bank_content.'
                    </select> 
                </div>
            ';
            $selected_inputs = '';
            if($selectQuery->payment_type=='CASH'){
                $selected_inputs .= $cash;
            }elseif($selectQuery->payment_type=='ONLINE'){
                $selected_inputs .= $cash;
                $selected_inputs .= $online;
            }else{
                $selected_inputs = $cash;
                $selected_inputs .= $online;
                $selected_inputs .= $cheque;
            }
            $returnHtml = '
                <div class="form-group">
                    <label>Payment Mode</label>
                    <select class="form-control" name="upayment-mode" required>
                        <option value=""></option>
                        '.$payment_type.'
                    </select>
                </div>
                '.$selected_inputs.'
            ';
        }else{
            $returnHtml = '<b class="text-danger">No Payment Method Selected</b>';
        }

        return $returnHtml;
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode=='collection-list-serverside'){
                $selectQuery = Collection::with('collection_details')->with('quotation')->with('agent')->with('client')
                ->where('status','=',$data['status'])
                ->whereHas('quotation',function($q1){
                    $q1->where('status','!=','PENDING');
                })
                ->orderBy('collections.created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->addColumn('expected_amount', function($selectQuery) use($user) {
                    $expected_date = strtotime(date('Y-m-d'));
                    $expected_amount= 0;
                    foreach($selectQuery->collection_details as $scheduled){
                        $expected_date = strtotime($scheduled->collection_date);
                        $expected_amount = $scheduled->amount_paid;
                        if($expected_date<strtotime($scheduled->collection_date)){
                            $expected_date = strtotime($scheduled->collection_date);
                            $expected_amount = $scheduled->amount_paid;
                        }else{
                            $expected_date = $expected_date;
                            $expected_amount = $expected_amount;
                        }
                    }
                    if($expected_amount==0){
                        $returnHtml = '<div align="center">';
                            $returnHtml .= '<b>TERMS : </b>'.$selectQuery->terms;
                        $returnHtml .= '</div>';
                    }else{
                        $returnHtml = '<div align="right">';
                        $returnHtml .= 'PHP '.number_format($expected_amount,2);
                        $returnHtml .= '<hr class="m-0">';
                        if(!empty($expected_date)){
                            $returnHtml .= '<b>Expected Next Collection Date :</b>'.date('F d,Y',$expected_date).'<br class="m-0">';
                        }
                        $returnHtml .= '<b>TERMS : </b>'.$selectQuery->terms.'</div>';
                    }

                    return $returnHtml;
                })
                ->editColumn('quotation.grand_total', function($selectQuery) use($user) {
                    $returnHtml = '<div align="right">';
                    $returnHtml .= 'PHP '.number_format($selectQuery->quotation->grand_total);
                    if($selectQuery->quotation->grand_total!=$selectQuery->contract_amount){
                        $returnHtml .= '<del>PHP '.number_format($selectQuery->contract_amount).'</del>';
                    }
                    $returnHtml .= '<hr class="m-0">';
                    $collected_amount = 0;
                    if(!empty($selectQuery->collected_amount)){
                        $collected_amount = $selectQuery->collected_amount;
                    }
                    $balance = floatval($selectQuery->quotation->grand_total)-floatval($collected_amount);
                    $returnHtml .= '<b class="text-danger">BALANCE : </b> PHP '.number_format($balance,2);
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('client.name', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= $selectQuery->client->name;
                        $returnHtml .= '<hr class="m-0">';
                        $returnHtml .= '<b>TIN Number :</b>'.$selectQuery->tin_number.'<br class="m-0">';
                        $returnHtml .= '<b>Contact Number : </b>'.$selectQuery->client->contact_numbers;
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('quotation.quote_number', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= $selectQuery->quotation->quote_number;
                        $returnHtml .= '<hr class="m-0">';
                        $returnHtml .= '<b>AGENT :</b>'.$selectQuery->agent->user->employee->first_name." ".$selectQuery->agent->user->employee->last_name;
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<a href="'.route('accounting-create-collection-schedule', ['id' => encryptor('encrypt',$selectQuery->id)]).'" class="btn btn-icon btn-outline-success btn-standard waves-effect rounded-circle mr-1 update-collection"><span class="far fa-database"></span></a>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='collection-serverside'){
                $id = encryptor('decrypt',$data['id']);
                $selectQuery = CollectionDetail::with('bank')->where('collection_id','=',$id)->orderBy('created_at','ASC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('created_at', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    $returnHtml .= date('F d,Y',strtotime($selectQuery->created_at));
                    $returnHtml .= '<br class="m-0">'.date('h:i a',strtotime($selectQuery->created_at));
                    if(!empty($selectQuery->collection_date)){
                        $returnHtml .= '<hr class="m-0">';
                        $returnHtml .= '<b>Expected Collection Date </b> <br class="m-0">'.date('F d,Y h:i a',strtotime($selectQuery->collection_date));
                    }
                    if(!empty($selectQuery->collector)){
                    $returnHtml .= '<br class="m-0"><b>Collector </b> <br class="m-0">'.$selectQuery->collector->employee->first_name.' '.$selectQuery->collector->employee->last_name;
                    }
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->addColumn('collected_amount', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    $returnHtml .= '<table class="table table-bordered">';
                    $returnHtml .= '<tr>';
                        $returnHtml .= '<th>Amount</th><td align="right"> PHP '.number_format($selectQuery->amount_paid,2).'</td>';
                    $returnHtml .= '</tr>';
                    $returnHtml .= '<tr>';
                        $returnHtml .= '<th>With Held Amount</th><td align="right">PHP '.number_format($selectQuery->ewt_amount,2).'</td>';
                    $returnHtml .= '</tr>';
                    $returnHtml .= '<tr>';
                        $returnHtml .= '<th>Other Amount</th><td align="right">PHP '.number_format($selectQuery->charge_amount,2).'</td>';
                    $returnHtml .= '</tr>';
                    $returnHtml .= '</table>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->addColumn('bank_details', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    if(!empty($selectQuery->bank_id)){
                        $returnHtml .= '<b>'.$selectQuery->bank->name.'</b>';
                    }else{
                        $returnHtml .= '<b>Payment Mode </b>'.$selectQuery->payment_type;
                    }
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->addColumn('cheque_details', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        if(!empty($selectQuery->check_number)){
                            $returnHtml .= '<b>Check Number </b><br class="m-0">'.$selectQuery->check_number;
                            $returnHtml .= '<hr class="m-0">';
                            $returnHtml .= '<b>Check Date </b><br class="m-0">'.date('F d,Y',strtotime($selectQuery->check_date));
                        }else{
                            $returnHtml .= '-';
                        }
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('status', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        if($selectQuery->status == 'UNVERIFIED'||$selectQuery->status == 'VOID'){
                            $returnHtml .= '<b class="text-danger">'.$selectQuery->status.'</b>';
                        }elseif($selectQuery->status =='VERIFIED'){
                            $returnHtml .= '<b class="text-success">'.$selectQuery->status.'</b>';
                        }else{
                            $returnHtml .= '<b class="text-warning">'.$selectQuery->status.'</b>';
                        }
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        if($selectQuery->status=='UNVERIFIED'){
                            $returnHtml .= '<a class="btn btn-icon btn-outline-success btn-standard waves-effect rounded-circle mr-1 verify-collection" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-check"></span></a>';
                            if(!empty($selectQuery->check_number)){
                                $returnHtml .= '<a class="btn btn-icon btn-outline-warning btn-standard waves-effect rounded-circle mr-1 bounce-check" data-toggle="tooltip" title="Bounce Cheque" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="far fa-bolt"></span></a>';
                            }
                        }else{
                            if($selectQuery->status!='BOUNCE-CHECK'){
                                if(!empty($selectQuery->check_number)){
                                    $returnHtml .= '<a class="btn btn-icon btn-outline-warning btn-standard waves-effect rounded-circle mr-1 bounce-check" data-toggle="tooltip" title="Bounce Cheque" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="far fa-bolt"></span></a>';
                                }
                            }
                        }
                        if($user->position->name == 'ACCOUNTING OFFICER' || $user->position->name == 'ACCOUNTING CONSULTANT'){
                            if($selectQuery->status!='VOID'){
                                $returnHtml .= '<a class="btn btn-icon btn-outline-danger btn-standard waves-effect rounded-circle mr-1 void-collection" data-toggle="tooltip" title="VOID COLLECTION" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa da-times"></span></a>';
                            }
                        }
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='documents-serverside'){
                $id = encryptor('decrypt',$data['id']);
                $selectQuery = CollectionPaper::with('document')->where('collection_id','=',$id)->orderBy('created_at','ASC');
                return Datatables::eloquent($selectQuery)
                ->editColumn('created_at', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    $returnHtml .= date('F d,Y',strtotime($selectQuery->created_at));
                    $returnHtml .= '<br class="m-0">'.date('h:i a',strtotime($selectQuery->created_at));
                 
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('document.name', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<b class="text-primary">'.$selectQuery->document->name.'</b>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('amount_paid', function($selectQuery) use($user) {
                    $returnHtml = '<div align="right">';
                        $returnHtml .= 'PHP '.number_format($selectQuery->amount_paid,2);
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('reference_number', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        $returnHtml .= '<b>'.$selectQuery->reference_number.'</b>';
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('reference_date', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                    $returnHtml .= date('F d,Y',strtotime($selectQuery->reference_date));
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->editColumn('status', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        if($selectQuery->status == 'PENDING'){
                            $returnHtml = '<b class="text-danger">'.$selectQuery->status.'</b>';
                        }elseif($selectQuery->status=='ON-HAND'){
                            $returnHtml = '<b class="text-success">'.$selectQuery->status.'</b>';
                        }else{
                            $returnHtml = '<b class="text-warning">'.$selectQuery->status.'</b>';
                        }
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnHtml = '<div align="center">';
                        if($selectQuery->status != 'ON-HAND'){
                            $returnHtml .= '<a class="btn btn-icon btn-outline-success btn-standard waves-effect rounded-circle mr-1 change-status" data-status="ON-HAND" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-check"></span></a>';
                            if($selectQuery->status!='ONGOING'){
                                $returnHtml .= '<a class="btn btn-icon btn-outline-warning btn-standard waves-effect rounded-circle mr-1 change-status" data-status="ONGOING" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-arrow-up"></span></a>';
                            }
                        }
                        
                    $returnHtml .= '</div>';
                    return $returnHtml;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='bounce-check-collection'){
                $id = encryptor('decrypt',$data['id']);
                $verifyCollectionQuery = CollectionDetail::find($id);
                $old_status = $verifyCollectionQuery->status;
                $verifyCollectionQuery->status = 'BOUNCE-CHECK';
                $verifyCollectionQuery->updated_at = getDatetimeNow();
                $total_collection = floatval($verifyCollectionQuery->amount_paid)+floatval($verifyCollectionQuery->ewt_amount)+floatval($verifyCollectionQuery->charge_amount);
                if($verifyCollectionQuery->save()){
                    $collectionQuery = Collection::find($verifyCollectionQuery->collection_id);
                    $collected_amount = floatval($collectionQuery->collected_amount)-floatval($total_collection);
                    if($old_status=='VERIFIED'){
                        $collectionQuery->collected_amount = $collected_amount;
                    }
                    if($collected_amount<=0){
                        $collectionQuery->status = 'FOR-COLLECTION';
                    }
                    $collectionQuery->updated_at = getDatetimeNow();
                    if($collectionQuery->save()){
                        return array('success' => 1, 'message' => 'Successfully Updated!');
                    }else{
                        return array('success' => 0, 'message' => 'Error While saving collection');
                    }
                }else{
                    return array('success' => 0, 'message' => 'Error While saving collection detail');
                }
            }elseif($postMode=='void-collection'){
                $id = encryptor('decrypt',$data['id']);
                $verifyCollectionQuery = CollectionDetail::find($id);
                $old_status = $verifyCollectionQuery->status;
                $verifyCollectionQuery->status = 'VOID';
                $verifyCollectionQuery->updated_at = getDatetimeNow();
                $total_collection = floatval($verifyCollectionQuery->amount_paid)+floatval($verifyCollectionQuery->ewt_amount)+floatval($verifyCollectionQuery->charge_amount);
                if($verifyCollectionQuery->save()){
                    $collectionQuery = Collection::find($verifyCollectionQuery->collection_id);
                    $collected_amount = floatval($collectionQuery->collected_amount)-floatval($total_collection);
                    if($old_status=='VERIFIED'){
                        $collectionQuery->collected_amount = $collected_amount;
                    }
                    if($collected_amount<=0){
                        $collectionQuery->status = 'FOR-COLLECTION';
                    }
                    $collectionQuery->updated_at = getDatetimeNow();
                    if($collectionQuery->save()){
                        return array('success' => 1, 'message' => 'Successfully Void!');
                    }else{
                        return array('success' => 0, 'message' => 'Error While saving collection');
                    }
                }else{
                    return array('success' => 0, 'message' => 'Error While saving collection detail');
                }
            }elseif($postMode=='change-status-document'){
                $id = encryptor('decrypt',$data['id']);
                $collectionPaperQuery = CollectionPaper::find($id);
                $collectionPaperQuery->status = $data['status'];
                $collectionPaperQuery->updated_at = getDatetimeNow();
                if($collectionPaperQuery->save()){
                    return array('success' => 1, 'message' => 'Successfully '.$data['status'].'!');
                }else{
                    return array('success' => 0, 'message' => 'Error While saving collection paper');
                }
            }else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode=='insert-collection'){
                $id = encryptor('decrypt',$data['collection-id']);
                $attributes = [
                   'collection-id'=>'Collection ID'
                ];
                $rules = [
                    'collection-id'=>'required'
                ];
                $payment_mode = 'DOCUMENT';
                if(!empty($data['payment-mode'])){
                    $payment_mode = $data['payment-mode'];
                }
                if($payment_mode=='CASH'||$payment_mode=='ONLINE'||$payment_mode=='CHECK'){
                    $attributes['payment-mode'] = 'Payment Mode';
                    $attributes['amount'] = 'Collection Amount';
                    $attributes['other-amount'] = 'Other Amount';
                    $attributes['with-held-amount'] = 'With Held Amount';
                    if($payment_mode=='ONLINE'||$payment_mode=='CHECK'){
                        $attributes['bank'] = 'Bank';
                        if($payment_mode=='CHECK'){
                            $attributes['check-number'] = 'Check Number';
                            $attributes['check-date'] = 'Check Date';
                            $rules['check-number'] = 'required';
                            $rules['check-date'] = 'required';
                        }
                        $rules['bank'] = 'required';
                    }
                    $rules['payment-mode'] = 'required';
                    $rules['amount'] = 'required';
                    $rules['other-amount'] = 'required';
                    $rules['with-held-amount'] = 'required';
                }else{
                    $attributes['document-amount']='Amount';
                    $attributes['document'] = 'Document';
                    $rules['document-amount']='required';
                    $rules['document'] = 'required';
                }
                if(!empty($data['collector'])){
                    $attributes['collection-date'] = 'Collection Date';
                    $attributes['collector'] = 'Collector';
                    $rules['collection-date'] = 'required';
                    $rules['collector'] = 'required';
                }
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $collectionQuery = Collection::find($id);
                    if($payment_mode=='CASH'||$payment_mode=='ONLINE'||$payment_mode=='CHECK'){
                        $total_collection = floatval($data['with-held-amount'])+floatval($data['amount'])+floatval($data['other-amount']);
                        $total_payment = floatval($collectionQuery->collected_amount)+floatval($total_collection);
                        $insertCollectionDetail = new CollectionDetail();
                        $insertCollectionDetail->collection_id = $id;
                        if(!empty($data['collector'])){
                            $insertCollectionDetail->collector_user_id = $data['collector'];
                        }
                        if(!empty($data['collection-date'])){
                            $insertCollectionDetail->collection_date = $data['collection-date'];
                        }
                        $payment_type = 'PARTIAL';
                        if($total_payment>=$collectionQuery->quotation->grand_total){
                            $payment_type = 'FULLY-PAID';
                        }else{
                            if(count($collectionQuery->collection_details)==0){
                                $payment_type = 'DOWNPAYMENT';
                            }else{
                                $payment_type = 'PARTIAL';
                            }
                        }
                        $insertCollectionDetail->type = $payment_type;
                        $insertCollectionDetail->amount_paid = str_replace(",","",$data['amount']);
                        $insertCollectionDetail->ewt_amount = str_replace(",","",$data['with-held-amount']);
                        $insertCollectionDetail->payment_type = $payment_mode;
                        $insertCollectionDetail->charge_amount = str_replace(",","",$data['other-amount']);
                        $insertCollectionDetail->bank_id = $data['bank'];
                        $insertCollectionDetail->check_number = $data['check-number'];
                        $insertCollectionDetail->check_date = $data['check-date'];
                        $insertCollectionDetail->created_by = $user->id;
                        $insertCollectionDetail->updated_by = $user->id;
                        $insertCollectionDetail->created_at = getDatetimeNow();
                        $insertCollectionDetail->updated_at = getDatetimeNow();
                        $insertCollectionDetail->status = 'UNVERIFIED';
                        // $insertCollectionDetail->date_collected = $data[''];
                        // $insertCollectionDetail->charge_type = $data[''];
                        if($insertCollectionDetail->save()){
                            Session::flash('success', 1);
                            Session::flash('message', 'Successfully Added but you need to verify it on the table.');
                            return back();
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'Error while saving collection detail');
                            return back();
                        }
                    }else{
                        $insertCollectionPaper = new CollectionPaper();
                        $insertCollectionPaper->collection_id = $id;
                        $insertCollectionPaper->amount_paid = $data['document-amount'];
                        $insertCollectionPaper->accounting_paper_id = $data['document'];
                        if(!empty($data['reference-number'])){
                            $insertCollectionPaper->reference_number = $data['reference-number'];
                            $insertCollectionPaper->reference_date = $data['reference-date'];
                        } 
                        $insertCollectionPaper->created_by = $user->id;
                        $insertCollectionPaper->updated_by = $user->id;
                        $insertCollectionPaper->created_at = getDatetimeNow();
                        $insertCollectionPaper->updated_at = getDatetimeNow();
                        if($insertCollectionPaper->save()){
                            Session::flash('success', 1);
                            Session::flash('message', 'Successfully Inserted Accounting paper '.$insertCollectionPaper->reference_number);
                            return back();
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'Error while saving collection paper');
                            return back();
                        }
                    }
                }
            }else if($postMode=='verify-collection'){
                $id = encryptor('decrypt',$data['collection-detail-id']);
                $verifyCollectionQuery = CollectionDetail::find($id);
                $verifyCollectionQuery->date_collected = $data['collected-date'];
                $verifyCollectionQuery->status = 'VERIFIED';
                $verifyCollectionQuery->updated_at = getDatetimeNow();
                if(!empty($data['uamount'])){
                    $verifyCollectionQuery->amount_paid = $data['uamount'];
                }else{
                    $verifyCollectionQuery->amount_paid = 0;
                }
                if(!empty($data['uwith-held-amount'])){
                    $verifyCollectionQuery->ewt_amount = $data['uwith-held-amount'];
                }else{
                    $verifyCollectionQuery->ewt_amount = 0;
                }
                if(!empty($data['uother-amount'])){
                    $verifyCollectionQuery->charge_amount = $data['uother-amount'];
                }else{
                    $verifyCollectionQuery->charge_amount = 0;
                }
                $verifyCollectionQuery->payment_type = $data['upayment-mode'];
                if(!empty($data['ubank'])){
                    $verifyCollectionQuery->bank_id = $data['ubank'];
                }else{
                    $verifyCollectionQuery->bank_id = null;
                }
                if(!empty($data['ucheck-number'])){
                    $verifyCollectionQuery->check_number = $data['ucheck-number'];
                }else{
                    $verifyCollectionQuery->check_number = null;
                }
                if(!empty($data['ucheck-date'])){
                    $verifyCollectionQuery->check_date = $data['ucheck-date'];
                }else{
                    $verifyCollectionQuery->check_date = null;
                }
                $total_collection = floatval($data['uamount'])+floatval($data['uwith-held-amount'])+floatval($data['uother-amount']);
                if($verifyCollectionQuery->save()){
                    $collectionQuery = Collection::find($verifyCollectionQuery->collection_id);
                    $collectionQuery->last_collected_date = $data['collected-date'];
                    $collected_amount = floatval($collectionQuery->collected_amount)+floatval($total_collection);
                    $collectionQuery->collected_amount = $collected_amount;
                    if($collectionQuery->quotation->grand_total == $collected_amount){
                        $collectionQuery->status = 'FULLY-PAID';
                    }else{
                        $collectionQuery->status = 'PARTIAL';
                    }
                    $collectionQuery->updated_at = getDatetimeNow();
                    if($collectionQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Verified Total Amount of PHP '.number_format($total_collection,2));
                        return back();
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Error While saving Collection');
                        return back();
                    }
                }else{
                    Session::flash('success', 0);
                    Session::flash('message', 'Error While Verifiying the collection');
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
