<?php

namespace App\Http\Controllers\Accounting;

use App\AccountingPaper;
use App\AccountingTitle;
use App\AccountTitleParticular;
use App\Audit;
use App\Bank;
use App\Http\Controllers\Controller;
use App\Payee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use File;
use Validator;
use Session;
use Hash;
use Crypt;
use App\User;
use App\Employee;
use App\Department;
use App\Position;


class SettingsController extends Controller
{
    function showPayeeLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['pid']) && !empty($data['pid'])){
            $payee_id = encryptor('decrypt',$data['pid']);
            $selectQuery = Payee::find($payee_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-payee-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Event</th>
                                    <th>Old</th>
                                    <th>New</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                ';
            }else{
                $resultHtml = '
                    <div class="alert alert-danger text-left">
                        <strong>Unable to find data. Please try again.</strong>
                    </div>
                ';
            }
        }else{
            $resultHtml = '
                <div class="alert alert-danger text-left">
                    <strong>Unable to find data. Please try again.</strong>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showPayees(){
        $user = Auth::user();
        $selectQuery = Payee::orderBy('name', 'ASC')->get();
        return view('accounting-department.settings.payees')
            ->with('admin_menu','SETTINGS')
            ->with('admin_sub_menu','PAYEES')
            ->with('payees', $selectQuery);
    }
    function showAccountingTitleLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['atid']) && !empty($data['atid'])){
            $accounting_title_id = encryptor('decrypt',$data['atid']);
            $selectQuery = AccountingTitle::find($accounting_title_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-accounting-title-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Event</th>
                                    <th>Old</th>
                                    <th>New</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                ';
            }else{
                $resultHtml = '
                    <div class="alert alert-danger text-left">
                        <strong>Unable to find data. Please try again.</strong>
                    </div>
                ';
            }
        }else{
            $resultHtml = '
                <div class="alert alert-danger text-left">
                    <strong>Unable to find data. Please try again.</strong>
                </div>
            ';
        }
        return $resultHtml;
    }
    public function showAccountingTitleParticulars(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['acctid'])){
            $account_title_id = encryptor('decrypt',$data['acctid']);
            $selectQuery = AccountingTitle::with('particulars')->find($account_title_id);
            if($selectQuery){
                return view('accounting-department.settings.accounting-title-particulars')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','ACCOUNTING-TITLES')
                    ->with('account_title',$selectQuery)
                    ->with('user',$user);
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Unable to find account title. Please try again');
                return back();
            }
        }else{
            Session::flash('success', 0);
            Session::flash('message', 'Unable to find account title. Please try again');
            return back();
        }
    }
    function accountingTitleContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $accounting_title = AccountingTitle::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-md" required name="accounting-title-name-update" id="accounting-title-name-update" value="'.$accounting_title->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="accounting-title-id" value="'.$data['id'].'">
            ';
        } else {
            $resultHtml='
                <div class="ibox-content">
                    <div class="alert-danger">
                        <h3>
                        <strong>Undefined!</strong> This Place is Undefined.
                    </h3>
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showAccountingTitles(){
        $user = Auth::user();
        $selectQuery = AccountingTitle::orderBy('name', 'ASC')->get();
        return view('accounting-department.settings.accounting-titles')
            ->with('admin_menu','SETTINGS')
            ->with('admin_sub_menu','ACCOUNTING-TITLES')
            ->with('accounting_titles', $selectQuery);
    }
    function accountingPaperContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $accounting_paper = AccountingPaper::where('id', '=', $id)->first();

            $accounting_paper_type = ['COLLECTION', 'SOA', 'INVOICE', 'CR'];
            $select_accounting_paper_type = "";
            foreach($accounting_paper_type as $type) {
                $current_accounting_paper_type = "";
                if($accounting_paper->type == $type) {
                    $current_accounting_paper_type = "selected";
                }
                $select_accounting_paper_type .= '<option value="'.$type.'" '.$current_accounting_paper_type.'>'.$type.'</option>';
            }

            $resultHtml = '
                <div class="form-group">
                    <label>Accounting Paper :</label>
                    <input type="text" class="form-control" required name="accounting-paper-update" id="accounting-paper-update" value="'.$accounting_paper->name.'">
                </div>
                <div class="form-group">
                    <label>Type :</label>
                    <select class="form-control" id="select-type-update" required name="select-type-update">
                        <option value="">Select Type</option>
                        '.$select_accounting_paper_type.'
                    </select>
                </div>
                <input type="hidden" class="form-control" required name="accounting-paper-id" value="'.$data['id'].'">
            ';
        } else {
            $resultHtml='
                <div class="ibox-content">
                    <div class="alert-danger">
                        <h3>
                        <strong>Undefined!</strong> This Place is Undefined.
                    </h3>
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showAccountingPaperLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['apid']) && !empty($data['apid'])){
            $accounting_paper_id = encryptor('decrypt',$data['apid']);
            $selectQuery = AccountingPaper::find($accounting_paper_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-accounting-paper-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Event</th>
                                    <th>Old</th>
                                    <th>New</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                ';
            }else{
                $resultHtml = '
                    <div class="alert alert-danger text-left">
                        <strong>Unable to find data. Please try again.</strong>
                    </div>
                ';
            }
        }else{
            $resultHtml = '
                <div class="alert alert-danger text-left">
                    <strong>Unable to find data. Please try again.</strong>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showAccountingPapers(){
        $user = Auth::user();
        $selectQuery = AccountingPaper::orderBy('name', 'ASC')->get();
        return view('accounting-department.settings.accounting-papers')
            ->with('admin_menu','SETTINGS')
            ->with('admin_sub_menu','ACCOUNTING-PAPERS')
            ->with('accounting_papers', $selectQuery);
    }
    function bankContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $bank = Bank::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="form-group">
                        <label>Bank Name :</label>
                        <input type="text" class="form-control" required name="bank-name-update" id="bank-name-update" value="'.$bank->name.'">
                    </div>
                    <div class="form-group">
                        <label>Display Name :</label>
                        <input type="text" class="form-control" required name="display-name-update" id="display-name-update" value="'.$bank->display_name.'">
                    </div>
                    <input type="hidden" class="form-control" required name="bank-id" value="'.$data['id'].'">
            ';
        } else {
            $resultHtml='
                <div class="ibox-content">
                    <div class="alert-danger">
                        <h3>
                        <strong>Undefined!</strong> This Place is Undefined.
                    </h3>
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showBankLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['bid']) && !empty($data['bid'])){
            $bank_id = encryptor('decrypt',$data['bid']);
            $selectQuery = Bank::find($bank_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-bank-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Event</th>
                                    <th>Old</th>
                                    <th>New</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                ';
            }else{
                $resultHtml = '
                    <div class="alert alert-danger text-left">
                        <strong>Unable to find data. Please try again.</strong>
                    </div>
                ';
            }
        }else{
            $resultHtml = '
                <div class="alert alert-danger text-left">
                    <strong>Unable to find data. Please try again.</strong>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showBanks(){
        $user = Auth::user();
        $selectQuery = Bank::orderBy('name', 'ASC')->get();
        return view('accounting-department.settings.banks')
            ->with('admin_menu','SETTINGS')
            ->with('admin_sub_menu','BANKS')
            ->with('banks', $selectQuery);
    }
    public function userProfile(){
        $user = Auth::user();

        return view('accounting-department.settings.profile')
                ->with('admin_menu','SETTINGS')
                ->with('admin_sub_menu','USER-PROFILE')
                ->with('user',$user);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'logs-accounting-papers-details'){
                $enc_accounting_paper_id = $data['key'];
                $accounting_paper_id = encryptor('decrypt', $enc_accounting_paper_id);
                $selectQuery = Audit::with('user')
                    ->where('auditable_type','=','App\AccountingPaper')
                    ->where('auditable_id','=',$accounting_paper_id)
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('event', function($selectQuery) use($user) {
                        $returnValue = strToUpper($selectQuery->event);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= readableDate($selectQuery->created_at);
                        return $returnValue;
                    })
                    ->editColumn('old_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->old_values,true);
                        $returnValue = '<table class="small table table-small m-0 p-0"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td class="p-0 m-0" >'.$key.'</td>';
                                $returnValue .= '<td class="p-0 m-0">'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->editColumn('new_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->new_values,true);
                        $returnValue = '<table class="small table"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td>'.$key.'</td>';
                                $returnValue .= '<td>'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->addColumn('source_model', function($selectQuery) use($user) {
                        $returnValue = $selectQuery->user->username;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= $selectQuery->auditable_type;
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            elseif($postMode == 'logs-payees-details'){
                $enc_payee_id = $data['key'];
                $payee_id = encryptor('decrypt', $enc_payee_id);
                $selectQuery = Audit::with('user')
                    ->where('auditable_type','=','App\Payee')
                    ->where('auditable_id','=',$payee_id)
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('event', function($selectQuery) use($user) {
                        $returnValue = strToUpper($selectQuery->event);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= readableDate($selectQuery->created_at);
                        return $returnValue;
                    })
                    ->editColumn('old_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->old_values,true);
                        $returnValue = '<table class="small table table-small m-0 p-0"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td class="p-0 m-0" >'.$key.'</td>';
                                $returnValue .= '<td class="p-0 m-0">'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->editColumn('new_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->new_values,true);
                        $returnValue = '<table class="small table"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td>'.$key.'</td>';
                                $returnValue .= '<td>'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->addColumn('source_model', function($selectQuery) use($user) {
                        $returnValue = $selectQuery->user->username;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= $selectQuery->auditable_type;
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            elseif($postMode == 'logs-banks-details'){
                $enc_bank_id = $data['key'];
                $bank_id = encryptor('decrypt', $enc_bank_id);
                $selectQuery = Audit::with('user')
                    ->where('auditable_type','=','App\Bank')
                    ->where('auditable_id','=',$bank_id)
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('event', function($selectQuery) use($user) {
                        $returnValue = strToUpper($selectQuery->event);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= readableDate($selectQuery->created_at);
                        return $returnValue;
                    })
                    ->editColumn('old_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->old_values,true);
                        $returnValue = '<table class="small table table-small m-0 p-0"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td class="p-0 m-0" >'.$key.'</td>';
                                $returnValue .= '<td class="p-0 m-0">'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->editColumn('new_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->new_values,true);
                        $returnValue = '<table class="small table"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td>'.$key.'</td>';
                                $returnValue .= '<td>'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->addColumn('source_model', function($selectQuery) use($user) {
                        $returnValue = $selectQuery->user->username;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= $selectQuery->auditable_type;
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            elseif($postMode == 'logs-accounting-titles-details'){
                $enc_accounting_title_id = $data['key'];
                $accounting_title_id = encryptor('decrypt', $enc_accounting_title_id);
                $selectQuery = Audit::with('user')
                    ->where('auditable_type','=','App\AccountingTitle')
                    ->where('auditable_id','=',$accounting_title_id)
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('event', function($selectQuery) use($user) {
                        $returnValue = strToUpper($selectQuery->event);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= readableDate($selectQuery->created_at);
                        return $returnValue;
                    })
                    ->editColumn('old_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->old_values,true);
                        $returnValue = '<table class="small table table-small m-0 p-0"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td class="p-0 m-0" >'.$key.'</td>';
                                $returnValue .= '<td class="p-0 m-0">'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->editColumn('new_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->new_values,true);
                        $returnValue = '<table class="small table"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td>'.$key.'</td>';
                                $returnValue .= '<td>'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->addColumn('source_model', function($selectQuery) use($user){
                        $returnValue = $selectQuery->user->username;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= $selectQuery->auditable_type;
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }
        else{
            if($postMode=='change-password'){
                $attributes = [
                    'current_password' => 'Current Password',
                    'new_password' => 'New Password',
                    'confirm_password'=>'Confirm Password'
                ];
                $rules = [
                    'current_password' => 'required',
                    'new_password' => 'required',
                    'confirm_password'=>'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    if(Hash::check($data['login'], $user->password)){
                        $id = encryptor('decrypt',$data['id_user']);
                        $userUpdateQuery = User::find($id);
                        $userUpdateQuery->password = bcrypt($data['new_password']);
                        if($userUpdateQuery->save()){
                            Session::flash('success', 1);
                            Session::flash('message', 'You have successfully change password!');
                            return back();
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'There`s an error acquired please try again');
                            return back();
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Your password is incorrect');
                        return back();
                    }
                }
            }
            elseif($postMode == 'add-payees'){
                $attributes = [
                    'payee-name' => 'Payee Name',
                ];
                $rules = [
                    'payee-name' => 'required|unique:payees,name,NULL,id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertPayeeQuery = new Payee();
                    $insertPayeeQuery->name = trim(strtoupper($data['payee-name']));
                    $insertPayeeQuery->created_by = $user->id;
                    $insertPayeeQuery->updated_by = $user->id;
                    $insertPayeeQuery->created_at = getDatetimeNow();
                    $insertPayeeQuery->updated_at = getDatetimeNow();
                    if($insertPayeeQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Payee Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add payee. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-payees'){
                $id = encryptor('decrypt', $data['payee-id']);
                $attributes = [
                    'payee-name-update' => 'Payee Name',
                ];
                $rules = [
                    'payee-name-update' => 'required|unique:payees,name,'.$id.',id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updatePayeeQuery = Payee::where('id', '=', $id)->first();
                    $updatePayeeQuery->name = trim(strtoupper($data['payee-name-update']));
                    $updatePayeeQuery->updated_by = $user->id;
                    $updatePayeeQuery->updated_at = getDatetimeNow();
                    if($updatePayeeQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update payee. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-accounting-title-particular'){
                $account_title_id = encryptor('decrypt',$data['key']);
                $attributes = [
                    'particulars' => 'Particular',
                ];
                $rules = [
                    'particulars' => 'required|unique:account_title_particulars,name,NULL,id,account_title_id,'.$account_title_id,
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertQuery = new AccountTitleParticular();
                    $insertQuery->account_title_id = $account_title_id;
                    $insertQuery->name = trim($data['particulars']);
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    $insertQuery->created_at = getDatetimeNow();
                    $insertQuery->updated_at = getDatetimeNow();
                    if($insertQuery->save()){
                        Session::flash('success',1);
                        Session::flash('message','Particular Added');
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to save particular. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-accounting-title-particular'){
                $account_title_id = encryptor('decrypt',$data['key']);
                $account_title_particular_id = encryptor('decrypt',$data['particular_key']);
                $attributes = [
                    'particulars' => 'Particular',
                ];
                $rules = [
                    'particulars' => 'required|unique:account_title_particulars,name,'.$account_title_particular_id.',id,account_title_id,'.$account_title_id,
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateQuery = AccountTitleParticular::find($account_title_particular_id);
                    if($updateQuery){
                        $updateQuery->name = trim($data['particulars']);
                        $updateQuery->updated_by = $user->id;
                        $updateQuery->updated_at = getDatetimeNow();
                        if($updateQuery->save()){
                            Session::flash('success',1);
                            Session::flash('message','Particular Updated');
                        }else{
                            Session::flash('success',0);
                            Session::flash('message','Unable to update particular. Please try again');
                        }
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to update particular. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-accounting-titles'){
                $id = encryptor('decrypt', $data['accounting-title-id']);
                $attributes = [
                    'accounting-title-name-update' => 'Accounting Title',
                ];
                $rules = [
                    'accounting-title-name-update' => 'required|unique:accounting_titles,name,'.$id.',id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateAccountingTitleQuery = AccountingTitle::where('id', '=', $id)->first();
                    $updateAccountingTitleQuery->name = trim($data['accounting-title-name-update']);
                    $updateAccountingTitleQuery->updated_by = $user->id;
                    $updateAccountingTitleQuery->updated_at = getDatetimeNow();
                    if($updateAccountingTitleQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update accounting title. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-accounting-papers'){
                $attributes = [
                    'accounting-paper' => 'Accounting Paper',
                    'select-type' => 'Type',
                ];
                $rules = [
                    'accounting-paper' => 'required|unique:accounting_papers,name,NULL,id|max:100',
                    'select-type' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertAccountingPaperQuery = new AccountingPaper();
                    $insertAccountingPaperQuery->name = trim($data['accounting-paper']);
                    $insertAccountingPaperQuery->type = $data['select-type'];
                    $insertAccountingPaperQuery->created_by = $user->id;
                    $insertAccountingPaperQuery->updated_by = $user->id;
                    $insertAccountingPaperQuery->created_at = getDatetimeNow();
                    $insertAccountingPaperQuery->updated_at = getDatetimeNow();
                    if($insertAccountingPaperQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Accounting Paper Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add accounting paper. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-accounting-papers'){
                $id = encryptor('decrypt', $data['accounting-paper-id']);
                $attributes = [
                    'accounting-paper-update' => 'Accounting Paper',
                    'select-type-update' => 'Type',
                ];
                $rules = [
                    'accounting-paper-update' => 'required|unique:accounting_papers,name,'.$id.',id|max:100',
                    'select-type-update' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateAccountingPaperQuery = AccountingPaper::where('id', '=', $id)->first();
                    $updateAccountingPaperQuery->name = trim($data['accounting-paper-update']);
                    $updateAccountingPaperQuery->type = $data['select-type-update'];
                    $updateAccountingPaperQuery->updated_by = $user->id;
                    $updateAccountingPaperQuery->updated_at = getDatetimeNow();
                    if($updateAccountingPaperQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update accounting paper. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-banks'){
                $attributes = [
                    'bank-name' => 'Bank Name',
                    'display-name' => 'Display Name',
                ];
                $rules = [
                    'bank-name' => 'required|unique:banks,name,NULL,id|max:50',
                    'display-name' => 'required|unique:banks,display_name,NULL,id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertBankQuery = new Bank();
                    $insertBankQuery->name = trim($data['bank-name']);
                    $insertBankQuery->display_name = trim($data['display-name']);
                    $insertBankQuery->created_by = $user->id;
                    $insertBankQuery->updated_by = $user->id;
                    $insertBankQuery->created_at = getDatetimeNow();
                    $insertBankQuery->updated_at = getDatetimeNow();
                    if($insertBankQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Bank Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add bank. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-banks'){
                $id = encryptor('decrypt', $data['bank-id']);
                $attributes = [
                    'bank-name-update' => 'Bank Name',
                    'display-name-update' => 'Display Name',
                ];
                $rules = [
                    'bank-name-update' => 'required|unique:banks,name,'.$id.',id|max:50',
                    'display-name-update' => 'required|unique:banks,display_name,'.$id.',id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateBankQuery = Bank::where('id', '=', $id)->first();
                    $updateBankQuery->name = trim($data['bank-name-update']);
                    $updateBankQuery->display_name = trim($data['display-name-update']);
                    $updateBankQuery->updated_by = $user->id;
                    $updateBankQuery->updated_at = getDatetimeNow();
                    if($updateBankQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update bank. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-accounting-titles'){
                $attributes = [
                    'accounting-title-name' => 'Accounting Title',
                ];
                $rules = [
                    'accounting-title-name' => 'required|unique:accounting_titles,name,NULL,id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertAccountingTitleQuery = new AccountingTitle();
                    $insertAccountingTitleQuery->name = trim($data['accounting-title-name']);
                    $insertAccountingTitleQuery->created_by = $user->id;
                    $insertAccountingTitleQuery->updated_by = $user->id;
                    $insertAccountingTitleQuery->created_at = getDatetimeNow();
                    $insertAccountingTitleQuery->updated_at = getDatetimeNow();
                    if($insertAccountingTitleQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Accounting Title Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add accounting title. Please try again');
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
