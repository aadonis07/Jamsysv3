<?php

namespace App\Http\Controllers\Marketing;

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
class SettingsController extends Controller
{
    public function userProfile(){
        $user = Auth::user();
        
        return view('marketing-department.settings.profile')
                ->with('admin_menu','SETTINGS')
                ->with('admin_sub_menu','USER-PROFILE')
                ->with('user',$user);
    }

    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            //ajax request
            // if(){ 
            // }else{
            //     return array('success' => 0, 'message' => 'Undefined Method');
            // }
        }else{
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
