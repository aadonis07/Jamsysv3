<?php

namespace App\Http\Controllers\PurchasingRaw;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Session;
class SettingsController extends Controller
{
    function showProfile(){
		$user = Auth::user();
        return view('purchasing-raw-department.settings.profile')->with('admin_menu','USER-PROFILE')->with('user',$user);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            return array('success' => 0, 'message' => 'Undefined Method');
        }else{
            if($postMode=='change-password'){
                $attributes = [
                    'current_password' => 'Current Password',
                    'password' => 'New Password',
                ];
                $rules = [
                    'current_password' => 'required',
                    'password' => 'required|confirmed',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                    return back();
                }else{
                    $id = encryptor('decrypt',$data['id_user']);
                    $userUpdateQuery = User::find($id);
                    $userUpdateQuery->password = bcrypt($data['password']);
                    if($userUpdateQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'You have successfully change password!');
                        return back();
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'There\'s an error acquired please try again');
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
