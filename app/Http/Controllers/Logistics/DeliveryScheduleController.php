<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliveryScheduleController extends Controller
{
    public function list(){
        return view('logistics-department.delivery_schedules.list')
                ->with('admin_menu','DELIVERY-SCHEDULE');
    }
}
