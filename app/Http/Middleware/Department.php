<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;

class Department
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$department)
    {
        if(!Auth::check()){
            return redirect(route('login'));
        }else{
            $user = Auth::user();
            $validateDepartment = validateUserDepartment($department,$user->department_id);
            if($validateDepartment['success'] == false){
//                Session::flash('success',0);
//                Session::flash('message',$validateDepartment['message']);
                Auth::logout();
                return redirect(route('login'))->withErrors(array('username' => $validateDepartment['message']));
            }else{
                return $next($request);
            }
        }
    }
}
