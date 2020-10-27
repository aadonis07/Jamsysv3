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
            CODE: [ FTO ] = 'fitout-dashboard',
            CODE: [ PLT ] = 'plant-dashboard',
            CODE: [ CST ] = 'cst-dashboard',
            CODE: [ PROD ] = 'production-dashboard',
            CODE: [ WHSE ] = 'warehouse-bicutan-dashboard',
            CODE: [ MKTG ] = 'marketing-dashboard',
            CODE: [ DSGN ] = 'design-dashboard',
            CODE: [ PRPTR ] = 'proprietor-dashboard',
            CODE: [ LGCS ] = 'logistics-dashboard',
            CODE: [ LGCS-WRM ] = 'logistics-warehouse-raw-dashboard',
            CODE: [ LGCS-WSUPP ] = 'logistics-warehouse-supply-dashboard',
            CODE: [ PHR ] = 'purchasing-dashboard',
            CODE: [ PUR-RM ] = 'purchasing-raw-dashboard',
            CODE: [ PUR-SUPP ] = 'purchasing-supply-dashboard',
         **/
        $departments = array( // redirect to dashboard per department
            'IT' => 'it-dashboard', // alias name dashboard per department
            'ADMIN' => 'admin-dashboard',
            'ACCTG' => 'accounting-dashboard',
            'HR' => 'hr-dashboard',
            'SLS' => 'sales-dashboard',
            'FTO' => 'fitout-dashboard',
            'PLT' => 'plant-dashboard',
            'CST' => 'cst-dashboard',
            'PROD' => 'production-dashboard',
            'WHSE' => 'warehouse-bicutan-dashboard',
            'MKTG' => 'marketing-dashboard',
            'DSGN' => 'design-dashboard',
            'PRPTR' => 'proprietor-dashboard',
            'LGCS' => 'logistics-dashboard',
            'LGCS-WRM' => 'logistics-warehouse-raw-dashboard',
            'LGCS-WSUPP' => 'logistics-warehouse-supply-dashboard',
            'PHR' => 'purchasing-dashboard',
            'PUR-RM' => 'purchasing-raw-dashboard',
            'PUR-SUPP' => 'purchasing-supply-dashboard',
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
