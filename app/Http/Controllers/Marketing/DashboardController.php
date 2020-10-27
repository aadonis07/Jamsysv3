<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Validator;
class DashboardController extends Controller
{
    public function showIndex(){
        $user = Auth::user();
        return view('marketing-department.index')
            ->with('admin_menu','DASHBOARD')
            ->with('admin_sub_menu','')
            ->with('user',$user);
    }
}
