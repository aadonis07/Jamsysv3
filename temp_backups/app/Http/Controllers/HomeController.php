<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use Validator;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /**
     * Show the application dashboard.
     * For Dashboard Controller
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $view  = 'home';
        //Department code references
        /**
         *
            NOTE: UPDATE IF NEEDED.
            REFEFENCES: ( LIST BELOW , DONE ROUTE,BLADE,CONTROLLER)
            CODE: [ IT ] = 'it-dashboard', // alias name dashboard per department
            CODE: [ SLS ] = 'sales-dashboard',
            CODE: [ HR ] = 'hr-dashboard',
            CODE: [ ACCTG ] = 'acctg-dashboard',
            CODE: [ ADMIN ] = 'admin-dashboard',
         **/
        $departments = array( // redirect to dashboard per department
            'IT' => 'it-dashboard', // alias name dashboard per department
            'ADMIN' => 'admin-dashboard',
            'ACCTG' => 'accounting-dashboard',
            'HR' => 'hr-dashboard',
            'SLS' => 'sales-dashboard',
        );
        if(isset( $departments[$user->department_code])){
            $view  = $departments[$user->department_code];
            return redirect(route($view));
        }else{
            Auth::logout();
            return redirect(route('login'))->withErrors(array('username' =>'Unable to find '.$user->department.' Dashboard'));
        }
    }
}
