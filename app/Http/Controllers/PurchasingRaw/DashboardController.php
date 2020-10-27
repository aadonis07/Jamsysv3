<?php

namespace App\Http\Controllers\PurchasingRaw;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Validator;
class DashboardController extends Controller
{
    public function showIndex(){
        $user = Auth::user();
        return view('purchasing-raw-department.index')
            ->with('admin_menu','DASHBOARD')
            ->with('admin_sub_menu','')
            ->with('user',$user);
    }
}
