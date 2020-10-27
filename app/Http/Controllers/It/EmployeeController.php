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
use App\Department;
use App\Position;
use App\Employee;
use App\EmployeeBackground;
use App\EmployeeAccount;
use App\User;
use App\Team;
use App\Agent;
class EmployeeController extends Controller
{
    public function index(){
        $civil_statuses = [
            'SINGLE'=>'Single',
            'MARRIED'=>'Married',
            'WIDOW'=>'Widow',
            'WIDOWER'=>'Widower',
            'ANNULED'=>'Annulled',
            'DIVORCED'=>'Divorced',
            'COMMON-LAW-WIFE'=>'Common Law Wife',
            'COMMON-LAW-HUSBAND'=>'Common Law Husband'
        ];
        $user = Auth::user();
        $positions = Position::all();
        $departments = Department::all();

        return view('it-department.employee.index')
             ->with('admin_menu','EMPLOYEE')
             ->with('admin_sub_menu','ADD-EMPLOYEE')
             ->with('civil_statuses',$civil_statuses)
             ->with('positions',$positions)
             ->with('departments',$departments)
             ->with('user',$user); 
    }
    public function view(){
        
        return view('it-department.employee.view')
        ->with('admin_menu','EMPLOYEE')
        ->with('admin_sub_menu','VIEW-EMPLOYEE');
    }
    public function update(Request $request){
        $data = $request->all();
        $id = encryptor('decrypt',$data['id']);
        $employee = Employee::where('id','=',$id)->with('work')->with('education')->with('family')->first();

        $civil_statuses = [
            'SINGLE'=>'Single',
            'MARRIED'=>'Married',
            'WIDOW'=>'Widow',
            'WIDOWER'=>'Widower',
            'ANNULED'=>'Annulled',
            'DIVORCED'=>'Divorced',
            'COMMON-LAW-WIFE'=>'Common Law Wife',
            'COMMON-LAW-HUSBAND'=>'Common Law Husband'
        ];
        $user = Auth::user();
        $positions = Position::all();
        $departments = Department::all();
        return view('it-department.employee.update')
             ->with('civil_statuses',$civil_statuses)
             ->with('positions',$positions)
             ->with('departments',$departments)
             ->with('employee',$employee);
    }
    function employeeAccounts(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $id = encryptor('decrypt',$data['id']);
        $returnHml ='';
        $accountType= employeeAccountTypes();
        
        $account_types = '';
        for($i=0;$i<count($accountType);$i++){
            $account_types .= '<option value="'.$accountType[$i].'">'.$accountType[$i].'</option>';
        }

        if(isset($id) && !empty($id)){
            $selectQuery = EmployeeAccount::where('employee_id','=',$id)->get();
            $accountContent = '';
            if(count($selectQuery)!=0){
                foreach($selectQuery as $account){
                    $accountContent .= '<tr id="tab'.$account->id.'">';
                        $accountContent .= '<td>'.$account->type.'</td>';
                        $accountContent .= '<td>'.$account->username.'</td>';
                        $accountContent .= '<td align ="center"><div id="account'.$account->id.'"> <a class="btn btn-info text-white show-password" data-id="'.encryptor('encrypt',$account->id).'"><span class="fa fa-eye text-white"></span> Show Password</a></div></td>';
                        $accountContent .= '<td>';
                        $accountContent .='<a class="btn btn-warning text-white update-account" data-id="'.$account->id.'" data-enc_id="'.encryptor('encrypt',$account->id).'" data-type="UPDATE"><span class="fa fa-pencil-alt text-white"></span></a>';
                        $accountContent .='</td>';
                    $accountContent .= '</tr>';
                }
            }else{
                $accountContent .= '<tr>';
                        $accountContent .= '<td colspan="4"><div class="alert bg-danger-400 text-white" role="alert">
                                                <strong>No Data Available</strong>
                                            </div></td>';
                    $accountContent .= '</tr>';
            }
            $returnHml ='
                        <div class="input-group alert alert-primary mb-4 input-group-multi-transition">
                            <select class="form-control" name="account-type">
                                <option value=""></option>
                                '.$account_types.'
                            </select> 
                            <input type="text" maxlength="100" class="form-control" name="username" placeholder="Email/Username">
                            <input type="text" maxlength="100" class="form-control" name="password" placeholder="Password">
                            <input type="hidden" class="form-control" name="employee-id" value="'.$data['id'].'">
                            <div class="input-group-append">
                                <button type="button" id="add-account-btn" class="btn btn-dark waves-themed waves-effect waves-themed">ADD ACCOUNT <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="table-responsive">
                            <table class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                    <tr>
                                        <th>Account Type</th>
                                        <th>Email Address / Username</th>
                                        <th>Password</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="account-table-content">
                                    '.$accountContent.'
                                </tbody>
                            </table>
                            </div>
                        </div>';
        }else{
            $returnHml ='<div class="alert bg-danger-600 alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="fal fa-times"></i></span>
                            </button>
                            <div class="d-flex align-items-center">
                                <div class="alert-icon width-8">
                                    <span class="icon-stack icon-stack-xl">
                                        <i class="base-2 icon-stack-3x color-danger-400"></i>
                                        <i class="base-10 text-white icon-stack-1x"></i>
                                        <i class="ni md-profile color-danger-800 icon-stack-2x"></i>
                                    </span>
                                </div>
                                <div class="flex-1 pl-1">
                                    <span class="h2">
                                        There was no employee account in this account!
                                    </span>
                                    <br>
                                    Please contact the system admin to input the account/s.
                                </div>
                            </div>
                        </div>';
        }
        return $returnHml;
    }
    function updateAccount(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $returnHml = '';
        if(isset($data['id']) && !empty($data['id'])){
            $accountType= employeeAccountTypes();

            $selectQuery = EmployeeAccount::find($data['id']);

            $account_types = '';
            for($i=0;$i<count($accountType);$i++){
                $mode = '';
                if($accountType[$i]==$selectQuery->type){
                    $mode = 'selected';
                }
                $account_types .= '<option value="'.$accountType[$i].'" '.$mode.'>'.$accountType[$i].'</option>';
            }
            
            if($data['type']=="UPDATE"){
                $returnHml .= '<td><select class="form-control" id="type'.$data['id'].'"><option value=""></option>'.$account_types.'</select></td>';
                $returnHml .= '<td><input class="form-control" type="text" id="username'.$data['id'].'" value="'.$selectQuery->username.'" /></td>';
                $returnHml .= '<td><input class="form-control" type="text" id="password'.$data['id'].'"  placeholder="New Password" /></td>';
                $returnHml .= '<td><a class="btn btn-success submit-update" data-id="'.$data['id'].'"><span class="text-white">UPDATE</span> </a>  <a class="btn btn-danger update-account" data-id="'.$data['id'].'" data-enc_id="'.encryptor('encrypt',$data['id']).'" data-type="CANCEL"><span class="text-white fa fa-times"> Cancel</span> </a></td>';
            }else{
                $returnHml .= '<td>'.$selectQuery->type.'</td>';
                $returnHml .= '<td>'.$selectQuery->username.'</td>';
                $returnHml .= '<td align ="center"><div id="account'.$selectQuery->id.'"> <a class="btn btn-info text-white show-password" data-id="'.encryptor('encrypt',$selectQuery->id).'"><span class="fa fa-eye text-white"></span> Show Password</a></div></td>';
                $returnHml .= '<td>';
                $returnHml .= '<a class="btn btn-warning text-white update-account" data-enc_id="'.encryptor('encrypt',$selectQuery->id).'" data-id="'.$selectQuery->id.'" data-type="UPDATE"><span class="fa fa-pencil-alt text-white"></span></a>';
                $returnHml .= '</td>';
            }
        }else{
            $returnHml = "<td colspan='4'>No Data</td>";
        }
        return $returnHml;
    }
    public function employeeInfo(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $returnHml = '';
        $id = encryptor('decrypt',$data['id']);
        if(isset($id) && !empty($id)){
            $selectQuery = Employee::where('id','=',$id)->with('position')->with('department')->first();
            $destinationProfile = 'assets/img/employee/profile/';
            $filenameProfile = $selectQuery->employee_num;
            $imagePathProfile = imagePath($destinationProfile.''.$filenameProfile,'//via.placeholder.com/400X400');
            $destinationSignature = 'assets/img/employee/signature/';
            $filenameSignature = $selectQuery->employee_num;
            $imagePathSignature = imagePath($destinationSignature.''.$filenameSignature,'//via.placeholder.com/400X200');
            $firstname = strtoupper($selectQuery->first_name);
            $middlename = strtoupper($selectQuery->middle_name);
            $surname = strtoupper($selectQuery->last_name);
            $prefix = '';
            if($selectQuery->prefix!='N/A'){
            $prefix = strtoupper($selectQuery->prefix);
            }
            $name = $firstname.' '.$middlename.' '.$surname.' '.$prefix;
            $regular = '';
            $separated = '';
            $section = '';
            $birthYear = date('Y',strtotime($selectQuery->birth_date));
            $year = date('Y');
            $age= $year-$birthYear;
            if(!empty($selectQuery->section)){
                $section = '
                            <tr>
                                <td align="left"><b>Section: </b></td>
                                <td align="left">'.$selectQuery->section.'</td>
                            </tr>
                    ';
            }
            if(!empty($selectQuery->date_regulized)){
                $regular = '
                        <tr>
                            <td align="left"><b>Date of Regularization: </b></td>
                            <td align="left">'.date('F d,Y',strtotime($selectQuery->regularization_date)).'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Date Regularized: </b></td>
                            <td align="left">'.date('F d,Y',strtotime($selectQuery->date_regulized)).'</td>
                        </tr>
                ';
            }
            if($selectQuery->status=='SEPARATED'){
                $pay = '';
                if($selectQuery->separation_pay==1){
                    $pay = 'YES';
                }else{
                    $pay = 'NO';
                }
                $separated = '
                            <tr>
                                <td align="left"><b>Date Resigned: </b></td>
                                <td align="left">'.date('F d,Y',strtotime($selectQuery->date_resigned)).'</td>
                            </tr>
                            <tr>
                                <td align="left"><b>Separation Pay: </b></td>
                                <td align="left">'.$pay.'</td>
                            </tr>
                            ';
            }
            $returnHml = '
            <div class="row">
                <div class="col-md-6 col-sm-12" align="center">
                        <img src="'.$imagePathProfile.'" style="width:200px;height:200px;" class="rounded" id="image_preview">
                    <div class="form-group">
                        <img src="'.$imagePathSignature.'" style="width:200px;height:100px;" class="rounded" id="image_preview_signature">
                    </div>
                    <table>
                        <tr>
                            <td align="center" colspan="2"><h3 class="text-success">Government Information</h3></td>
                        </tr>
                        <tr>
                            <td align="left"><b>SSS: </b></td>
                            <td align="left">'.$selectQuery->sss.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Pag-Ibig: </b></td>
                            <td align="left">'.$selectQuery->pagibig.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Philhealth: </b></td>
                            <td align="left">'.$selectQuery->philhealth.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>TIN NUmber: </b></td>
                            <td align="left">'.$selectQuery->tin.'</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 col-sm-12">
                    <table>
                        <tr>
                            <td align="center" colspan="2"><h3 class="text-primary">Company Information</h3></td>
                        </tr>
                        <tr>
                            <td align="left"><b>Name: </b></td>
                            <td align="left">'.$name.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Employee ID: </b></td>
                            <td align="left">'.$selectQuery->employee_num.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Status: </b></td>
                            <td align="left">'.$selectQuery->status.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Date Hired: </b></td>
                            <td align="left">'.date('F d,Y',strtotime($selectQuery->date_hired)).'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Department: </b></td>
                            <td align="left">'.strtoupper($selectQuery->department->name).' DEPARTMENT</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Position: </b></td>
                            <td align="left">'.$selectQuery->position->name.'</td>
                        </tr>
                        '.$section.'
                        '.$regular.'
                        '.$separated.'
                        <tr>
                            <td align="center" colspan="2"><h3 class="text-danger">Personal Information</h3></td>
                        </tr>
                        <tr>
                            <td align="left"><b>Date of Birth: </b></td>
                            <td align="left">'.date('F d,Y',strtotime($selectQuery->birth_date)).'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Age: </b></td>
                            <td align="left">'.$age.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Gender: </b></td>
                            <td align="left">'.$selectQuery->gender.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Civil Status: </b></td>
                            <td align="left">'.$selectQuery->civil_status.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Email: </b></td>
                            <td align="left">'.$selectQuery->email.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Contact #: </b></td>
                            <td align="left">'.$selectQuery->contact_number.'</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Address: </b></td>
                            <td align="left">'.$selectQuery->address.'</td>
                        </tr>
                    </table>
                </div>
            </div>
            ';
        }else{
            $returnHml = '<div class="alert bg-danger-600 alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="alert-icon width-8">
                                <span class="icon-stack icon-stack-xl">
                                    <i class="base-2 icon-stack-3x color-danger-400"></i>
                                    <i class="base-10 text-white icon-stack-1x"></i>
                                    <i class="ni md-profile color-danger-800 icon-stack-2x"></i>
                                </span>
                            </div>
                            <div class="flex-1 pl-1">
                                <span class="h2">
                                    There was no employee information!
                                </span>
                                <br>
                                Please contact the system admin to input the informations.
                            </div>
                        </div>
                    </div>';
        }
        return $returnHml;
    }

    function erpCreateContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $returnHml = '';
        $id = encryptor('decrypt',$data['id']);
        if(isset($id) && !empty($id)){
            $selectQuery = Employee::find($id);
            $accounts = EmployeeAccount::where('employee_id','=',$id)->where('type','=','ERP')->get();
            
            $accountContent = '';
            foreach($accounts as $account){
                $accountContent .= '<tr>';
                $accountContent .= '<td>'.$account->username.'</td>';
                $accountContent .= '<td align ="center"><div id="account'.$account->id.'"> <a class="btn btn-info text-white show-password" data-id="'.encryptor('encrypt',$account->id).'"><span class="fa fa-eye text-white"></span> Show Password</a></div></td>';
                $accountContent .= '</tr>';
            }
            $email_content = '';
            if(count($accounts)==0){
                $email_content = '<div class="form-group">
                                    <label>JECAMS Email Address :</label>
                                    <input class="form-control" name="email-address" type="email" required />
                                  </div>';
            }

            $firstname = strtoupper($selectQuery->first_name);
            $middlename = strtoupper($selectQuery->middle_name);
            $surname = strtoupper($selectQuery->last_name);
            $prefix = '';
            if($selectQuery->prefix!='N/A'){
            $prefix = strtoupper($selectQuery->prefix);
            }
            $name = $firstname.' '.$middlename.' '.$surname.' '.$prefix;
            $departments = Department::all();

            $department_content = '';
            foreach($departments as $department){
                $department_content .= '<option value="'.$department->id.'">'.$department->name.'</option>';
            }

            $returnHml = '
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Name :</label>
                        <input class="form-control" disabled value="'.$name.'" />
                    </div>
                    <div class="form-group">
                        <label>Nickname :</label>
                        <input class="form-control" name="nickname" type="text" required />
                    </div>
                    <div class="form-group">
                        <label>Username :</label>
                        <input class="form-control" name="username" type="text" required />
                    </div>
                    '.$email_content.'
                    <div class="form-group">
                        <label>Password :</label>
                        <input class="form-control" name="password" type="text" required />
                    </div>
                    <div class="form-group">
                        <label>Department :</label>
                        <select class="form-control" name="department_id" required>
                        <option value=""></option>
                        '.$department_content.'
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Position :</label>
                        <select class="form-control" name="position_id" required>
                        <option value=""></option>
                        </select>
                    </div>
                    <input class="form-control" name="employee_id" type="hidden" value="'.$data['id'].'" />
                </div>
                <div class="col-md-6">
                    <div class="form-group sales-content" style="display:none;">
                        <label>Team</label>
                        <select class="form-control sales-inputs" name="sales-team">
                            <option value=""></option>
                            
                        </select>
                        <input type="hidden" class="form-control sales-inputs" name="team-name" />
                        <input type="hidden" class="form-control" name="with-manager" />
                    </div>
                    <div class="form-group sales-content" style="display:none;">
                        <label>Date From</label>
                        <input type="date" class="form-control sales-inputs" name="date-from" />
                    </div>
                    <div class="form-group sales-content" style="display:none;">
                        <label>Quota</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="number" class="form-control" step="any" style="text-align: right;" name="quota">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h3 class="text-primary">ERP Accounts</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                    <thead class="bg-warning-500 text-center">
                                        <tr role="row">
                                            <th>Username</th>
                                            <th>Password</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        '.$accountContent.'
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
            ';
        }else{
            $returnHml = '';
        }
        return $returnHml;
    }

    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode=='get-position'){
                $selectQuery = Position::where('department_id','=',$data['id'])->get();

                $returnHtml = '<option value=""></option>';
                foreach($selectQuery as $position){
                    $returnHtml .= '<option value="'.$position->id.'">'.$position->name.'</option>';
                }
                
                return $returnHtml;
            }elseif($postMode=='get-team'){
                if($data['id']==8){
                    $teams = Team::whereNull('team_manager_id')->get();
                }else{
                    $teams = Team::where('status','=','ACTIVE')->whereNotNull('team_manager_id')->get();
                }
                $team_content = '<option value=""></option>';
                foreach($teams as $team){
                    $team_content .= '<option value="'.$team->id.'" data-name="'.$team->name.'" data-manager="'.$team->team_manager_id.'">'.$team->display_name.'</option>';
                }

                return $team_content;
            }elseif($postMode=='employee-list-serverside'){
                $selectQuery = Employee::with('department')->with('position')->with('createdBy')->where('status','=',$data['status'])
                                ->orderBy('first_name','DESC');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('name', function($selectQuery) use($user) {
                        $firstname = strtoupper($selectQuery->first_name);
                        $middlename = strtoupper($selectQuery->middle_name);
                        $surname = strtoupper($selectQuery->last_name);
                        $prefix = '';
                        if($selectQuery->prefix!='N/A'){
                        $prefix = strtoupper($selectQuery->prefix);
                        }
                        $name = $firstname.' '.$middlename.' '.$surname.' '.$prefix;
                        $id_enc = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<a class="text-info help employee-info" data-toggle="tooltip" data-placement="top" title="Click This to See Employee Info" data-id="'.$id_enc.'">'.$name.'</a>';
                        $returnValue .= '<hr class="m-0 mt-1">';
                        $returnValue .= '<span>Employee ID: </span> <b>'.$selectQuery->employee_num.'</b>';

                        return $returnValue;
                    })
                    ->addColumn('contact_details', function($selectQuery) use($user) {
                        $returnValue = $selectQuery->contact_number.'<hr class="m-0 mt-1">';
                        $returnValue .= '<text title="'.$selectQuery->email.'" class="small text-primary" style="font-size:12px;"><span class="fas fa-envelope"></span>: <b>'.$selectQuery->email.'</b></text>';
                        return $returnValue;
                    })
                    ->addColumn('department_position', function($selectQuery) use($user) {
                        $returnValue = strtoupper($selectQuery->department->name).' DEPARTMENT'.'<hr class="m-0 mt-1">'.'Position: <b>'.strtoupper($selectQuery->position->name).'</b>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery) use($user) {
                        $id_enc = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '<div class="demo text-center mb-0">
                                            <a href="'.route("employee-update").'?id='.$id_enc.'" class="pb-0 btn btn-info btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="UPDATE" data-original-title="UPDATE">
                                                <i class="ni ni-note"></i>
                                            </a>
                                            <a class="pb-0 btn btn-danger btn-icon waves-effect text-white waves-themed employee_account" data-id="'.$id_enc.'" data-toggle="tooltip" data-placement="top" title="EMPLOYEE ACCOUNTS"  data-original-title="EMPLOYEE ACCOUNTS">
                                                <i class="fas fa-envelope text-white"></i>
                                            </a>
                                            <a class="pb-0 btn btn-success btn-icon waves-effect text-white waves-themed add-erp-account" data-id="'.$id_enc.'" data-toggle="tooltip" data-placement="top" title="ADD ERP ACCOUNT"  data-original-title="ADD ERP ACCOUNT">
                                                <i class="ni ni-user-follow"></i>
                                            </a>
                                        </div>';
                        return $returnValue;
                    })
                    ->editColumn('date_hired', function($selectQuery) {
                        $returnValue = date('F d,Y',strtotime($selectQuery->date_hired));
                        if(!empty($selectQuery->date_regulized)){
                            $returnValue .= '<hr class="m-0 mt-1">';
                            $returnValue .= 'Regularized: <b>'.date('F d,Y',strtotime($selectQuery->date_regulized)).'</b>';
                        }
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }elseif($postMode=='separated-employee-list-serverside'){
                $selectQuery = Employee::with('department')->with('position')->where('status','=','SEPARATED')
                ->orderBy('date_resigned','DESC');
                return Datatables::eloquent($selectQuery)
                ->addColumn('name', function($selectQuery) use($user) {
                    $firstname = strtoupper($selectQuery->first_name);
                    $middlename = strtoupper($selectQuery->middle_name);
                    $surname = strtoupper($selectQuery->last_name);
                    $prefix = '';
                    if($selectQuery->prefix!='N/A'){
                    $prefix = strtoupper($selectQuery->prefix);
                    }
                    $name = $firstname.' '.$middlename.' '.$surname.' '.$prefix;
                    $id_enc = encryptor('encrypt',$selectQuery->id);
                    $returnValue = '<a class="text-info help employee-info" data-toggle="tooltip" data-placement="top" title="Click This to See Employee Info" data-id="'.$id_enc.'">'.$name.'</a>';
                    $returnValue .= '<hr class="m-0 mt-1">';
                    $returnValue .= '<span>Employee ID: </span> <b>'.$selectQuery->employee_num.'</b>';

                    return $returnValue;
                })
                ->addColumn('contact_details', function($selectQuery) use($user) {
                    $returnValue = $selectQuery->contact_number.'<hr class="m-0 mt-1">';
                    $returnValue .= '<text title="'.$selectQuery->email.'" class="small text-primary" style="font-size:12px;"><span class="fas fa-envelope"></span>: <b>'.$selectQuery->email.'</b></text>';
                    return $returnValue;
                })
                ->addColumn('department_position', function($selectQuery) use($user) {
                    $returnValue = strtoupper($selectQuery->department->name).' DEPARTMENT'.'<hr class="m-0 mt-1">'.'Position: <b>'.strtoupper($selectQuery->position->name).'</b>';
                    return $returnValue;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $id_enc = encryptor('encrypt',$selectQuery->id);
                    $returnValue = '<div class="demo text-center mb-0">
                                        <a href="'.route("employee-update").'?id='.$id_enc.'" class="pb-0 btn btn-info btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                            <i class="ni ni-note"></i>
                                        </a>
                                        <a class="pb-0 btn btn-danger btn-icon waves-effect text-white waves-themed employee_account" data-id="'.$id_enc.'" data-toggle="tooltip" data-placement="top"  data-original-title="EMPLOYEE ACCOUNTS">
                                            <i class="fas fa-envelope text-white"></i>
                                        </a>
                                        <a class="pb-0 btn btn-success btn-icon waves-effect text-white waves-themed add-erp-account" data-id="'.$id_enc.'" data-toggle="tooltip" data-placement="top" title="ADD ERP ACCOUNT"  data-original-title="ADD ERP ACCOUNT">
                                            <i class="ni ni-user-follow"></i>
                                        </a>
                                    </div>';
                    return $returnValue;
                })
                ->editColumn('date_hired', function($selectQuery) {
                    $returnValue = date('F d,Y',strtotime($selectQuery->date_hired));
                    if(!empty($selectQuery->date_regulized)){
                        $returnValue .= '<hr class="m-0 mt-1">';
                        $returnValue .= 'Regularized: <b>'.date('F d,Y',strtotime($selectQuery->date_regulized)).'</b>';
                    }
                    return $returnValue;
                })
                ->editColumn('date_resigned', function($selectQuery) {
                    $returnValue = date('F d,Y',strtotime($selectQuery->date_resigned));
                    return $returnValue;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='add-account'){
                $id = encryptor('decrypt',$data['employeeid']);
                $inserAccount = new EmployeeAccount();
                $inserAccount->employee_id = $id;
                $inserAccount->username = $data['username'];
                $inserAccount->password =  encryptor('encrypt',$data['password']);
                $inserAccount->type = $data['type'];
                $inserAccount->created_by = $user->id;
                $inserAccount->updated_by = $user->id;
                $inserAccount->save();
            }elseif($postMode=='validate-password'){
                $id = encryptor('decrypt',$data['id']);
                if(Hash::check($data['login'], $user->password)){
                    $selectQuery = EmployeeAccount::find($id);
                    $password = encryptor('decrypt',$selectQuery->password);
                    return array('id'=>$id, 'password'=>$password);
                }else{
                    return array('id' => 0, 'password' => 'Your Password is Incorrect');
                }
            }elseif($postMode=='update-account'){
                if($data['type']=='ERP'){
                    $updatePassword = User::where('username','=',$data['username'])->first();
                    $updatePassword->password = bcrypt($data['password']);
                    $updatePassword->save();
                }
                $updateAccount = EmployeeAccount::find($data['id']);
                $updateAccount->username = $data['username'];
                $updateAccount->password =  encryptor('encrypt',$data['password']);
                $updateAccount->type = $data['type'];
                $updateAccount->updated_by = $user->id;
                if($updateAccount->save()){
                    return encryptor('encrypt',$updateAccount->employee_id);
                }
            }else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode=='personal-information'){
                $attributes = [
                    'firstname' => 'Firstname',
                    'surname' => 'Surname',
                    'birthdate'=>'Birthdate',
                    'contact_number'=>'Contact Number',
                    'email-address'=>'Email Address',
                    'gender'=>'Gender',
                    'civil_status'=>'Civil Status',
                    'address'=>'Address',
                ];
                $rules = [
                    'firstname' => 'required',
                    'surname' => 'required',
                    'birthdate'=>'required',
                    'contact_number'=>'required',
                    'email-address'=>'required',
                    'gender'=>'required',
                    'civil_status'=>'required',
                    'address'=>'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $savedPoint = $data['savedpoint'];
                    $destination = 'assets/files/users/savepoint/'.$savedPoint.'/';
                    $default = 'N/A';
                    $middlename = $default;
                    $prefix = $default;
                    $sss = $default;
                    $philhealth = $default;
                    $pagibig_number = $default;
                    $tin_number = $default;
                    
                    if(!empty($data['middlename'])){
                        $middlename = $data['middlename'];
                    }
                    if(!empty($data['prefix'])){
                        $prefix = $data['prefix'];
                    }
                    if(!empty($data['sss'])){
                        $sss = $data['sss'];
                    }
                    if(!empty($data['philhealth'])){
                        $philhealth = $data['philhealth'];
                    }
                    if(!empty($data['pagibig_number'])){
                        $pagibig_number = $data['pagibig_number'];
                    }
                    if(!empty($data['tin_number'])){
                        $tin_number = $data['tin_number'];
                    }
                    
                    $datas = array(
                        'firstname'=>$data['firstname'],
                        'middlename'=>$middlename,
                        'surname'=>$data['surname'],
                        'prefix'=>$prefix,
                        'birthdate'=>$data['birthdate'],
                        'contact_number'=>$data['contact_number'],
                        'gender'=>$data['gender'],
                        'civil_status'=>$data['civil_status'],
                        'address'=>$data['address'],
                        'sss'=>$sss,
                        'philhealth'=>$philhealth,
                        'pagibig_number'=>$pagibig_number,
                        'tin_number'=>$tin_number,
                        'email'=>$data['email-address']
                    );
                    $datas = json_encode($datas);
                    $filename = 'personal-information';
                    $isExist = isExistFile($destination.''.$filename); 
                    if ($isExist['is_exist'] == true){
                        unlink($isExist['path']);
                    }
                    $result = toTxtFile($destination,$filename,'put',$datas);
                    if($result['success'] == true){
                        Session::flash('success', 1);
                        Session::flash('message', 'Success!');
                        return back();
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Theres an error while saving this data!');
                        return back();
                    }
                }
            }elseif($postMode=='background-information'){
                $attributes = [
                    'company_name' => 'Company Name',
                    'company_position' => 'Company Position',
                    'company_years'=>'Company Years Acquianted',
                    'company_address'=>'Company Address',
                    'school_name'=>'School Name',
                    'school_course'=>'Course / Education Attainment',
                    'school_years'=>'School Years Acquianted',
                    'family_name'=>'Name of Family',
                    'family_relationship'=>'Family Relationship',
                    'family_contact'=>'Contact Number of Family'
                ];
                $rules = [
                    'company_name' => 'required',
                    'company_position' => 'required',
                    'company_years'=>'required',
                    'company_address'=>'required',
                    'school_name'=>'required',
                    'school_course'=>'required',
                    'school_years'=>'required',
                    'family_name'=>'required',
                    'family_relationship'=>'required',
                    'family_contact'=>'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $savedPoint = $data['savedpoint'];
                    $destination = 'assets/files/users/savepoint/'.$savedPoint.'/';
                    $datas = array(
                        'WORK'=> array(
                            'name'=> $data['company_name'],
                            'position'=> $data['company_position'],
                            'yrs'=> $data['company_years'],
                            'address'=> $data['company_address']
                        ),
                        'EDUCATION'=> array(
                            'name' => $data['school_name'],
                            'education' => $data['school_course'],
                            'yrs' => $data['school_years']
                        ),
                        'FAMILY'=> array(
                            'name'=> $data['family_name'],
                            'relationship'=> $data['family_relationship'],
                            'contact'=> $data['family_contact']
                        )
                    );
                    $datas = json_encode($datas);
                    $filename = 'background-information';
                    $isExist = isExistFile($destination.''.$filename); 
                    if ($isExist['is_exist'] == true){
                        unlink($isExist['path']);
                    }
                
                    $result = toTxtFile($destination,$filename,'put',$datas);
                    
                    if($result['success'] == true){
                        Session::flash('success', 1);
                        Session::flash('message', 'Success!');
                        return back();
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Theres an error while saving this data!');
                        return back();
                    }
                }
            }else if($postMode=='employee-information'){
                $attributes = [
                    'date_hired' => 'Date Hired',
                    'position_id' => 'Position',
                    'department_id' => 'Department',
                    'employee-status' => 'Employee Status',
                    'tax_exempt'=>'Tax Exemption',
                    'employee_id'=>'Employee ID'
                ];
                $rules = [
                    'date_hired' => 'required',
                    'position_id' => 'required',
                    'department_id' => 'required',
                    'employee-status' => 'required',
                    'tax_exempt'=>'required',
                    'employee_id'=>'unique:employees,employee_num'
                ];
                if($data['employee-status']=='SEPARATED'){
                    $attributes['date-resigned'] = 'Date Resigned';
                    $attributes['separation-pay'] = 'Separation Pay';
                    $rules['date-resigned'] = 'required';
                    $rules['separation-pay'] = 'required';
                }
                if($data['employee-status']=='REGULAR'){
                    $attributes['date-regularization'] = 'Date Regularization';
                    $attributes['date-regulized'] = 'Date Regulized';
                    $rules['date-regularization'] = 'required';
                    $rules['date-regulized'] = 'required';
                }
                
                $validator = Validator::make($data,$rules,[],$attributes);

                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{ 
                    $access_code = null;
                    $section = null;
                    $date_regularization = null;
                    $date_regularized = null;
                    $date_resigned = null;
                    $separation_pay = null;
                    $basic_salary = null;
                    $allowance = null;
                    $gross_salary = null;

                    if(!empty($data['access_code'])){
                        $access_code = null;
                    }
                    if(!empty($data['section'])){
                        $section = $data['section'];
                    }
                    if(!empty($data['date-regularization'])){
                        $date_regularization = $data['date-regularization'];
                    }
                    if(!empty($data['date-regulized'])){
                        $date_regularized = $data['date-regulized'];
                    }
                    if(!empty($data['date-resigned'])){
                        $date_resigned = $data['date-resigned'];
                    }
                    if(!empty($data['separation-pay'])){
                        $separation_pay = $data['separation-pay'];
                    }
                    if(!empty($data['basic_salary'])){
                        $basic_salary = $data['basic_salary'];
                    }
                    if(!empty($data['gross_salary'])){
                        $gross_salary = $data['gross_salary'];
                    }
                    if(!empty($data['allowance'])){
                        $allowance = $data['allowance'];
                    }

                    $savedPoint = encryptor('encrypt',$user->id);
                    $destination = 'assets/files/users/savepoint/'.$savedPoint.'/';
                    $datas = array(
                        'employee_id'=>$data['employee_id'],
                        'position_id'=>$data['position_id'],
                        'department_id'=>$data['department_id'],
                        'date_hired'=>$data['date_hired'],
                        'employee_status'=>$data['employee-status'],
                        'tax_exempt'=>$data['tax_exempt'],
                        'access_code'=>$access_code,
                        'section'=>$section,
                        'basic_salary'=>$basic_salary,
                        'allowance'=>$allowance,
                        'gross_salary'=>$gross_salary,
                        'date_regularization'=>$date_regularization,
                        'date_regulized'=>$date_regularized,
                        'date_resigned'=>$date_resigned,
                        'separation_pay'=>$separation_pay
                    );
                    $filename = 'employee-information';
                    $isExist = isExistFile($destination.''.$filename); 
                    if ($isExist['is_exist'] == true){
                        unlink($isExist['path']);
                    }
                    $datas = json_encode($datas);
                    $result = toTxtFile($destination,$filename,'put',$datas);
                    if($result['success'] == true){
                        Session::flash('success', 1);
                        Session::flash('message', 'Success!');
                        return back();
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Theres an error while saving this data!');
                        return back();
                    }
                }
            }else if($postMode=='save-information'){
                $default = 'N/A';
                $access_code = $default;
                $section = $default;
                $basic_salary = $default;
                $allowance = $default;
                $gross_salary = $default;
                $tax_exempt = $default;
                if(!empty($data['access_code'])){
                    $access_code = $data['access_code'];
                }
                if(!empty($data['section'])){
                    $section = $data['section'];
                }
                $generatedSavedPoint = encryptor('encrypt',$user->id);
                $destination = 'assets/files/users/savepoint/'.$generatedSavedPoint.'/';
                $personal_info_filename = 'personal-information';
                $personal_info = toTxtFile($destination,$personal_info_filename,'get');
                $background_filename = 'background-information';
                $background_info = toTxtFile($destination,$background_filename,'get');
                $employee_filename = 'employee-information';
                $employee_info = toTxtFile($destination,$employee_filename,'get');
                $firstname = '';
                $middle = '';
                $surname = '';
                $prefix = '';
                $birthdate = '';
                $contact_number = '';
                $gender = '';
                $civil_Status = '';
                $address = '';
                $sss = '';
                $philhealth = '';
                $pagibig = '';
                $tin = '';
                $email = '';

                $access_code = '';
                $section = '';
                $date_regularization = '';
                $date_regularized = '';
                $date_resigned = '';
                $separation_pay = '';
                $basic_salary = '';
                $allowance = '';
                $gross_salary = '';
                $employee_id = '';
                $position_id = '';
                $department_id = '';
                $date_hired = '';
                $employee_status = '';


                if($personal_info['success'] === true){
                    $datas = $personal_info['data'];
                    $datas = json_decode($datas);
                    $firstname = $datas->firstname;
                    $middle = $datas->middlename;
                    $surname = $datas->surname;
                    $prefix = $datas->prefix;
                    $birthdate = $datas->birthdate;
                    $contact_number = $datas->contact_number;
                    $gender = $datas->gender;
                    $civil_Status = $datas->civil_status;
                    $address = $datas->address;
                    $sss = $datas->sss;
                    $philhealth = $datas->philhealth;
                    $pagibig = $datas->pagibig_number;
                    $tin = $datas->tin_number;
                    $email = $datas->email;
                }
                if($background_info['success'] === true){
                    $data_background = $background_info['data'];
                    $data_background = json_decode($data_background);
                }
                if($employee_info['success'] === true){
                    $employee_data = $employee_info['data'];
                    $employee_data = json_decode($employee_data);

                    $access_code = $employee_data->access_code;
                    $section = $employee_data->section;
                    $date_regularization = $employee_data->date_regularization;
                    $date_regularized = $employee_data->date_regulized;
                    $date_resigned = $employee_data->date_resigned;
                    $separation_pay = $employee_data->separation_pay;
                    $basic_salary = $employee_data->basic_salary;
                    $allowance = $employee_data->allowance;
                    $gross_salary = $employee_data->gross_salary;
                    $employee_id = $employee_data->employee_id;
                    $position_id = $employee_data->position_id;
                    $department_id = $employee_data->department_id;
                    $date_hired = $employee_data->date_hired;
                    $employee_status = $employee_data->employee_status;
                    $tax_exempt = $employee_data->tax_exempt;
                }
                    $insertEmployee = new Employee();
                    $insertEmployee->employee_num = $employee_id;
                    $insertEmployee->first_name = $firstname;
                    $insertEmployee->last_name = $surname;
                    $insertEmployee->middle_name = $middle;
                    $insertEmployee->prefix = $prefix;
                    $insertEmployee->birth_date = $birthdate;
                    $insertEmployee->gender = $gender;
                    $insertEmployee->civil_status = $civil_Status;
                    $insertEmployee->address = $address;
                    $insertEmployee->contact_number = $contact_number;
                    $insertEmployee->email = $email;
                    $insertEmployee->sss = $sss;
                    $insertEmployee->pagibig = $pagibig;
                    $insertEmployee->philhealth = $philhealth;
                    $insertEmployee->tin = $tin;
                    $insertEmployee->created_by = $user->id;
                    $insertEmployee->updated_by = $user->id;
                    if(!empty($access_code)){
                        $insertEmployee->access_code = $access_code;
                    }
                    $insertEmployee->position_id = $position_id;
                    $insertEmployee->department_id = $department_id;
                    if(!empty($section)){
                        $insertEmployee->section = $section;
                    }
                    $insertEmployee->date_hired = $date_hired;
                    if(!empty($date_regularization)){
                        $insertEmployee->regularization_date = $date_regularization;
                    }
                    if(!empty($date_regularized)){
                        $insertEmployee->date_regulized = $date_regularized;
                    }
                    $insertEmployee->status = $employee_status;
                    if(!empty($basic_salary)){
                        $insertEmployee->basic_salary = $basic_salary;
                    }
                    if(!empty($allowance)){
                        $insertEmployee->allowance = $allowance;
                    }
                    if(!empty($gross_salary)){
                        $insertEmployee->gross_salary = $gross_salary;
                    }
                    if(!empty($tax_exempt)){
                        $insertEmployee->tax_exemp = $tax_exempt;
                    }
                    if(!empty($date_resigned)){
                        $insertEmployee->date_resigned =$date_resigned;
                    }
                    if(!empty($separation_pay)){
                        $insertEmployee->separation_pay = $separation_pay;
                    }
                    if($insertEmployee->save()){
                        $destination_profile = 'assets/img/employee/profile/';
                        $filename_profile = $insertEmployee->employee_num;
                        $isExist = isExistFile($destination_profile.''.$filename_profile);
                        if($isExist['is_exist'] == true) {
                            unlink($isExist['path']);
                        }
                        $resultUpload = fileStorageUpload($data['profile_picture'],$destination_profile,$filename_profile,'resize',400,400);

                        for($i=0;$i<count($data_background->WORK->name);$i++){
                            $insertBackgroundWork = new EmployeeBackground();
                            $insertBackgroundWork->employee_id = $insertEmployee->id;
                            $insertBackgroundWork->type = 'WORK';
                            $insertBackgroundWork->name = $data_background->WORK->name[$i];
                            $insertBackgroundWork->position = $data_background->WORK->position[$i];
                            $insertBackgroundWork->address = $data_background->WORK->address[$i];
                            $insertBackgroundWork->years_acquainted = $data_background->WORK->yrs[$i];
                            $insertBackgroundWork->save();
                        }
                        for($a=0;$a<count($data_background->EDUCATION->name);$a++){
                            $insertBackgroundEducation = new EmployeeBackground();
                            $insertBackgroundEducation->employee_id = $insertEmployee->id;
                            $insertBackgroundEducation->type = 'EDUCATION';
                            $insertBackgroundEducation->name = $data_background->EDUCATION->name[$a];
                            $insertBackgroundEducation->position = $data_background->EDUCATION->education[$a];
                            $insertBackgroundEducation->years_acquainted = $data_background->EDUCATION->yrs[$a];
                            $insertBackgroundEducation->save();
                        }
                        for($b=0;$b<count($data_background->FAMILY->name);$b++){
                            $insertBackground = new EmployeeBackground();
                            $insertBackground->employee_id = $insertEmployee->id;
                            $insertBackground->type = 'FAMILY';
                            $insertBackground->name = $data_background->FAMILY->name[$b];
                            $insertBackground->relationship = $data_background->FAMILY->relationship[$b];
                            $insertBackground->contact_number = $data_background->FAMILY->contact[$b];
                            $insertBackground->save();
                        }

                        $destination_signature = 'assets/img/employee/signature/';
                        $filename_signature = $insertEmployee->employee_num;
                        $isExistss = isExistFile($destination_signature.''.$filename_signature);
                        if($isExistss['is_exist'] == true) {
                            unlink($isExist['path']);
                        }
                        $resultUpload = fileStorageUpload($data['signature_image'],$destination_signature,$filename_signature,'resize',400,200);

                        $destination_temp = 'assets/files/users/savepoint/'.$generatedSavedPoint; 
                        File::deleteDirectory($destination_temp);
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfuly Added!');
                        return back();
                    }
            }elseif($postMode=='update-personal-information'){
                $attributes = [
                    'firstname'=>'First Name',
                    'middlename'=>'Middle Name',
                    'surname'=>'SurName',
                    'birthdate'=>'Birthdate',
                    'contact_number'=>'Contact Number',
                    'gender'=>'Gender',
                    'civil_status'=>'Civil Status',
                    'address'=>'Address',
                    'email-address'=>'Email'
                ];
                $rules = [
                    'firstname'=>'required',
                    'middlename'=>'required',
                    'surname'=>'required',
                    'birthdate'=>'required',
                    'contact_number'=>'required',
                    'gender'=>'required',
                    'civil_status'=>'required',
                    'address'=>'required',
                    'email-address'=>'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $id = encryptor('decrypt',$data['employeeId']);
                    $updatePersonalInfoQuery = Employee::find($id);
                    $updatePersonalInfoQuery->first_name = $data['firstname'];
                    $updatePersonalInfoQuery->last_name = $data['surname'];
                    $updatePersonalInfoQuery->middle_name = $data['middlename'];
                    if(!empty($data['prefix'])){
                    $updatePersonalInfoQuery->prefix = $data['prefix'];
                    }
                    $updatePersonalInfoQuery->birth_date = $data['birthdate'];
                    $updatePersonalInfoQuery->gender = $data['gender'];
                    $updatePersonalInfoQuery->civil_status = $data['civil_status'];
                    $updatePersonalInfoQuery->address = $data['address'];
                    $updatePersonalInfoQuery->contact_number = $data['contact_number'];
                    $updatePersonalInfoQuery->email = $data['email-address'];
                    if(!empty($data['sss'])){
                    $updatePersonalInfoQuery->sss = $data['sss'];
                    }
                    if(!empty($data['pagibig_number'])){
                    $updatePersonalInfoQuery->pagibig = $data['pagibig_number'];
                    }
                    if(!empty($data['philhealth'])){
                    $updatePersonalInfoQuery->philhealth = $data['philhealth'];
                    }
                    if(!empty($data['tin_number'])){
                    $updatePersonalInfoQuery->tin = $data['tin_number'];
                    }
                    $updatePersonalInfoQuery->updated_by = $user->id;
                    if($updatePersonalInfoQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfuly Updated!');
                        return back();
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Theres an error acquired please try again');
                        return back();
                    }
                }
            }elseif($postMode=='update-background-information'){
                $id = encryptor('decrypt',$data['employeeId']);
                $attributes = [
                    'company_name' => 'Company Name',
                    'company_position' => 'Company Position',
                    'company_years'=>'Company Years Acquianted',
                    'company_address'=>'Company Address',
                    'school_name'=>'School Name',
                    'school_course'=>'Course / Education Attainment',
                    'school_years'=>'School Years Acquianted',
                    'family_name'=>'Name of Family',
                    'family_relationship'=>'Family Relationship',
                    'family_contact'=>'Contact Number of Family'
                ];
                $rules = [
                    'company_name' => 'required',
                    'company_position' => 'required',
                    'company_years'=>'required',
                    'company_address'=>'required',
                    'school_name'=>'required',
                    'school_course'=>'required',
                    'school_years'=>'required',
                    'family_name'=>'required',
                    'family_relationship'=>'required',
                    'family_contact'=>'required'
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    for($i=0;$i<count($data['company_name']);$i++){
                        if($data['company_id'][$i]=='wala'){
                            $updateBackgroundWork = new EmployeeBackground();
                            $updateBackgroundWork->employee_id = $id;
                            $updateBackgroundWork->type = 'WORK';
                        }
                        if($data['company_id'][$i]!='wala'){
                            $companyId = encryptor('decrypt',$data['company_id'][$i]);
                            $updateBackgroundWork = EmployeeBackground::find($companyId);
                        }
                            $updateBackgroundWork->name = $data['company_name'][$i];
                            $updateBackgroundWork->position = $data['company_position'][$i];
                            $updateBackgroundWork->address = $data['company_years'][$i];
                            $updateBackgroundWork->years_acquainted = $data['company_address'][$i];
                            $updateBackgroundWork->save();
                        
                    }
                    for($a=0;$a<count($data['school_name']);$a++){
                        if($data['school_id'][$a]=='wala'){
                            $updateBackgroundEducation = new EmployeeBackground();
                            $updateBackgroundEducation->employee_id = $id;
                            $updateBackgroundEducation->type = 'EDUCATION';
                        }
                        if($data['school_id'][$a]!='wala'){
                            $schoolId = encryptor('decrypt',$data['school_id'][$a]);
                            $updateBackgroundEducation = EmployeeBackground::find($schoolId);
                        }
                            $updateBackgroundEducation->name = $data['school_name'][$a];
                            $updateBackgroundEducation->position = $data['school_course'][$a];
                            $updateBackgroundEducation->years_acquainted = $data['school_years'][$a];
                            $updateBackgroundEducation->save();
                        
                    }
                    for($b=0;$b<count($data['family_name']);$b++){
                        if($data['family_id'][$b]=='wala'){
                            $updateBackground = new EmployeeBackground();
                            $updateBackground->employee_id = $id;
                            $updateBackground->type = 'FAMILY';
                        }
                        if($data['family_id'][$b]!='wala'){
                            $familyId = encryptor('decrypt',$data['family_id'][$b]);
                            $updateBackground = EmployeeBackground::find($familyId);
                        }
                            $updateBackground->name = $data['family_name'][$b];
                            $updateBackground->relationship = $data['family_relationship'][$b];
                            $updateBackground->contact_number = $data['family_contact'][$b];
                            $updateBackground->save();
                        
                    }
                    Session::flash('success', 1);
                    Session::flash('message', 'Successfuly Updated!');
                    return back();
                }
            }elseif($postMode=='update-employee-information'){
                $attributes = [
                    'date_hired' => 'Date Hired',
                    'position_id' => 'Position',
                    'department_id' => 'Department',
                    'employee-status' => 'Employee Status',
                    'tax_exempt'=>'Tax Exemption'
                ];
                $rules = [
                    'date_hired' => 'required',
                    'position_id' => 'required',
                    'department_id' => 'required',
                    'employee-status' => 'required',
                    'tax_exempt'=>'required'
                ];
                if($data['employee-status']=='SEPARATED'){
                    $attributes = [
                        'date_hired' => 'Date Hired',
                        'position_id' => 'Position',
                        'department_id' => 'Department',
                        'employee-status' => 'Employee Status',
                        'date-resigned'=>'Date Resignerd',
                        'separation-pay'=>'Separation Pay',
                        'tax_exempt'=>'Tax Exemption'
                    ];
                    $rules = [
                        'date_hired' => 'required',
                        'position_id' => 'required',
                        'department_id' => 'required',
                        'employee-status' => 'required',
                        'date-resigned'=>'required',
                        'separation-pay'=>'required',
                        'tax_exempt'=>'required'
                    ];
                }
                if($data['employee-status']=='REGULAR'){
                    $attributes = [
                        'date_hired' => 'Date Hired',
                        'position_id' => 'Position',
                        'department_id' => 'Department',
                        'employee-status' => 'Employee Status',
                        'date-regularization'=>'Date Regularization',
                        'date-regulized'=>'Date Regulized',
                        'tax_exempt'=>'Tax Exemption'
                    ];
                    $rules = [
                        'date_hired' => 'required',
                        'position_id' => 'required',
                        'department_id' => 'required',
                        'employee-status' => 'required',
                        'date-regularization'=>'required',
                        'date-regulized'=>'required',
                        'tax_exempt'=>'required'
                    ];
                }
                
                $validator = Validator::make($data,$rules,[],$attributes);

                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{ 
                    $no_data = null;
                    $id = encryptor('decrypt',$data['employeeId']);
                    $updateEmployee = Employee::find($id);
                    $updateEmployee->updated_by = $user->id;
                    if(!empty($data['access_code'])){
                        $updateEmployee->access_code = $data['access_code'];
                    }else{
                        $updateEmployee->access_code = $no_data;
                    }
                    $updateEmployee->position_id = $data['position_id'];
                    $updateEmployee->department_id = $data['department_id'];
                    if(!empty($data['section'])){
                        $updateEmployee->section = $data['section'];
                    }else{
                        $updateEmployee->section = $no_data;
                    }
                    $updateEmployee->date_hired = $data['date_hired'];
                    if(!empty($data['date-regularization'])){
                        $updateEmployee->regularization_date = $data['date-regularization'];
                    }else{
                        $updateEmployee->regularization_date = $no_data;
                    }
                    if(!empty($data['date-regulized'])){
                        $updateEmployee->date_regulized = $data['date-regulized'];
                    }else{
                        $updateEmployee->date_regulized = $no_data;
                    }
                    $updateEmployee->status = $data['employee-status'];
                    if(!empty($data['basic_salary'])){
                        $updateEmployee->basic_salary = $data['basic_salary'];
                    }else{
                        $updateEmployee->basic_salary = $no_data;
                    }
                    if(!empty($data['allowance'])){
                        $updateEmployee->allowance = $data['allowance'];
                    }else{
                        $updateEmployee->allowance = $no_data;
                    }
                    if(!empty($data['gross_salary'])){
                        $updateEmployee->gross_salary = $data['gross_salary'];
                    }else{
                        $updateEmployee->gross_salary = $no_data;
                    }
                    $updateEmployee->tax_exemp = $data['tax_exempt'];
                    if(!empty($data['date-resigned'])){
                        $updateEmployee->date_resigned =$data['date-resigned'];
                    }else{
                        $updateEmployee->date_resigned = $no_data;
                    }
                    if(!empty($data['separation-pay'])){
                        $updateEmployee->separation_pay = $data['separation-pay'];
                    }else{
                        $updateEmployee->separation_pay = $no_data;
                    }
                    $updateEmployee->employee_num = $data['employee_id'];
                    if($updateEmployee->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfuly Updated!');
                        return back();
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Theres an error acquired please try again');
                        return back();
                    }
                }
            }elseif($postMode=='update-picture-information'){

                $id = encryptor('decrypt',$data['employeeId']);
                    $updateEmployee = Employee::find($id);
                if(!empty($data['profile_picture'])){
                    $destination_profile = 'assets/img/employee/profile/';
                    $filename_profile = $updateEmployee->employee_num;
                    $isExists = isExistFile($destination_profile.''.$filename_profile);
                    if($isExists['is_exist'] == true) {
                        unlink($isExists['path']);
                    }
                    $resultUploads = fileStorageUpload($data['profile_picture'],$destination_profile,$filename_profile,'resize',400,400);

                }
                if(!empty($data['signature_image'])){
                    $destination_signature = 'assets/img/employee/signature/';
                    $filename_signature = $updateEmployee->employee_num;
                    $isExistss = isExistFile($destination_signature.''.$filename_signature);
                    if($isExistss['is_exist'] == true) {
                        unlink($isExistss['path']);
                    }
                    $resultUpload = fileStorageUpload($data['signature_image'],$destination_signature,$filename_signature,'resize',400,200);
                }

                Session::flash('success', 1);
                Session::flash('message', 'Successfuly Updated!');
                return back();
            }elseif($postMode=='create-erp'){
                $attributes = [
                    'nickname' => 'Nickname',
                    'username' => 'Username',
                    'password' => 'Password',
                    'department_id'=>'Department',
                    'position_id'=>'Position'
                ];
                $rules = [
                    'nickname' => 'required',
                    'username' => 'required|unique:users,username',
                    'password' => 'required',
                    'department_id'=>'required',
                    'position_id'=>'required'
                ];
                if($data['department_id']==18){
                    $attributes['sales-team'] = 'Team';
                    $attributes['date-from'] = 'Date From';
                    $attributes['quota'] = 'Quota';
                    $rules['sales-team'] = 'required';
                    $rules['date-from'] = 'required';
                    $rules['quota'] = 'required';
                    if(!empty($data['email-address'])){
                        $attributes['email-address'] = 'Email Address';
                        $rules['email-address'] = 'required';
                    }
                }
                $validator = Validator::make($data,$rules,[],$attributes);

                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{ 
                    $selectDepartment = Department::find($data['department_id']);
                    $id = encryptor('decrypt',$data['employee_id']);
                    $createERPAccount = new User();
                    $createERPAccount->nickname = $data['nickname'];
                    $createERPAccount->username = $data['username'];
                    $createERPAccount->password = bcrypt($data['password']);
                    if(!empty($data['email-address'])){
                        $createERPAccount->email = $data['email-address'];
                    }
                    $createERPAccount->position_id = $data['position_id'];
                    $createERPAccount->department_id = $data['department_id'];
                    $createERPAccount->department_code = $selectDepartment->code;
                    $createERPAccount->employee_id = $id;
                    $createERPAccount->status = 'INACTIVE';
                    $createERPAccount->created_by = $user->id;
                    $createERPAccount->updated_by = $user->id;
                    if($createERPAccount->save()){
                        $inserAccount = new EmployeeAccount();
                        $inserAccount->employee_id = $id;
                        $inserAccount->username = $data['username'];
                        $inserAccount->password =  encryptor('encrypt',$data['password']);
                        $inserAccount->type = 'ERP';
                        $inserAccount->created_by = $user->id;
                        $inserAccount->updated_by = $user->id;
                        if($inserAccount->save()){
                            if($data['department_id']==18){
                                $insertAgent = new Agent();
                                $insertAgent->user_id = $createERPAccount->id;
                                $insertAgent->team_id = $data['sales-team'];
                                $insertAgent->team_name = $data['team-name'];
                                $insertAgent->quota = $data['quota'];
                                $insertAgent->date_start = $data['date-from'];
                                if($data['position_id']==8){
                                    $insertAgent->is_manager = 1;
                                }else{
                                    $insertAgent->manager_id = $data['with-manager'];
                                }
                                $insertAgent->created_by = $user->id;
                                $insertAgent->updated_by = $user->id;
                                $insertAgent->created_at = getDatetimeNow();
                                $insertAgent->updated_at = getDatetimeNow();
                                if($insertAgent->save()){
                                    if($data['position_id']==8){
                                        $updateTeam = Team::find($data['sales-team']);
                                        $updateTeam->team_manager_id = $insertAgent->id;
                                        $updateTeam->team_manager = $data['username'];
                                        if($updateTeam->save()){
                                            Session::flash('success', 1);
                                            Session::flash('message', 'Successfuly Created! Please wait for account Approval Thank you.');
                                            return back();
                                        }else{
                                            Session::flash('success', 0);
                                            Session::flash('message', 'Theres an error while saving agent');
                                            return back();
                                        }
                                    }else{
                                        Session::flash('success', 1);
                                        Session::flash('message', 'Successfuly Created! Please wait for account Approval Thank you.');
                                        return back();
                                    }
                                }else{
                                    Session::flash('success', 0);
                                    Session::flash('message', 'Theres an error while saving agent');
                                    return back();
                                }
                            }else{
                                Session::flash('success', 1);
                                Session::flash('message', 'Successfuly Created! Please wait for account Approval Thank you.');
                                return back();
                            }
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'Theres an error acquired please try again');
                            return back();
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Theres an error acquired please try again');
                        return back();
                    }
                }
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
