<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
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
class UserController extends Controller
{
    public function user_list(){
        return view('hr-department.users.list')
              ->with('admin_menu','USERS')
              ->with('admin_sub_menu','USERS-LIST');
    }
    public function user_profile(){
        $auth_user = Auth::user();
        $user = User::where('id','=',$auth_user->id)->with('department')->with('position')->with('employee')->first();
        
        return view('hr-department.users.profile')->with('admin_menu','USER-PROFILE')->with('user',$user);
    }

    function controlContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $returnHml = '';
        $id = encryptor('decrypt',$data['id']);
        if(isset($id) && !empty($id)){
            $selectQuery = User::find($id);

            $activate = '';
            $archieve = '';
            $secured = '';

            if($selectQuery->status=='ACTIVE'){
                $activate = 'checked';
            }
            if($selectQuery->archive==1){
                $archieve = 'checked';
            }
            if($selectQuery->is_secured==1){
                $secured = 'checked';
            }

            $returnHml = '<div class="frame-wrap">
                            <div class="demo">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="activate-account" '.$activate.' value="ACTIVE" id="activate-account">
                                    <label class="custom-control-label" for="activate-account"><b class="text-success">Activate</b> Account ?</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="archive-account" '.$archieve.' value="1" id="archive-account">
                                    <label class="custom-control-label" for="archive-account"><b class="text-warning">Archive</b> Account ?</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="secured-account" '.$secured.' value="1" id="secured-account">
                                    <label class="custom-control-label" for="secured-account"><b>Secured</b> Account ?</label>
                                </div>
                                <input type="hidden" name="user-id" value="'.$data['id'].'"/>
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
                                        There was no user account in this account!
                                    </span>
                                    <br>
                                    Please contact the system admin to input the account.
                                </div>
                            </div>
                        </div>';
        }
        return $returnHml;
    }

    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode=='user-active-serverside'){
                $selectQuery = User::with('employee')->with('department')->with('position')->where('status','=',$data['status'])->where('id','!=',1)->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                ->addColumn('name', function($selectQuery) use($user) {
                    $firstname = strtoupper($selectQuery->employee->first_name);
                    $middlename = strtoupper($selectQuery->employee->middle_name);
                    $surname = strtoupper($selectQuery->employee->last_name);
                    $prefix = '';
                    if($selectQuery->employee->prefix!='N/A'){
                    $prefix = strtoupper($selectQuery->employee->prefix);
                    }
                    $name = $firstname.' '.$middlename.' '.$surname.' '.$prefix;
                    $id_enc = encryptor('encrypt',$selectQuery->employee->id);
                    $returnValue = '<a class="text-info help employee-info" data-toggle="tooltip" data-placement="top" title="Click This to See Employee Info" data-id="'.$id_enc.'">'.$name.'</a>';
                    $returnValue .= '<hr class="m-0 mt-1">';
                    $returnValue .= '<span>Nickname: </span> <b>'.$selectQuery->nickname.'</b>';
                    return $returnValue;
                })
                ->editColumn('username', function($selectQuery) {
                    $returnValue = $selectQuery->username;
                    $returnValue .= '<hr class="m-0 mt-1">';
                    $returnValue .= '<span>Email: </span> <b>'.$selectQuery->email.'</b>';
                    return $returnValue;
                })
                ->editColumn('department_position', function($selectQuery) {
                    $returnValue = strtoupper($selectQuery->department->name).' DEPARTMENT'.'<hr class="m-0 mt-1">'.'Position: <b>'.strtoupper($selectQuery->position->name).'</b>';
                    return $returnValue;
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $returnValue = '<div class="demo text-center mb-0">
                                        <a class="pb-0 btn btn-success btn-icon waves-effect text-white waves-themed action-btn" data-id="'.encryptor('encrypt',$selectQuery->id).'"  data-toggle="tooltip" data-placement="top" title="CONTROLLER"  data-original-title="CONTROLLER">
                                            <i class="far fa-wrench"></i>
                                        </a>
                                    </div>';
                    return $returnValue;
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode=='control-account'){
                $id = encryptor('decrypt',$data['user-id']);
                $controlQuery = User::find($id);
                if(!empty($data['activate-account'])){
                    $controlQuery->status = 'ACTIVE';
                }else{
                    $controlQuery->status = 'INACTIVE';
                }
                if(!empty($data['archive-account'])){
                    $controlQuery->archive = 1;
                }else{
                    $controlQuery->archive = 0;
                }
                if(!empty($data['secured-account'])){
                    $controlQuery->is_secured = 1;
                }else{
                    $controlQuery->is_secured = 0;
                }
                if($controlQuery->save()){
                    Session::flash('success', 1);
                    Session::flash('message', 'Successfuly saved!');
                    return back();
                }
            }elseif($postMode=='change-password'){
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
                            Session::flash('message', 'Theres an error acquired please try again');
                            return back();
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Your password is incorrect');
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
