<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function department(){
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
    public function position(){
        return $this->belongsTo('App\Position', 'position_id', 'id');
    }
    public function work(){
        return $this->hasMany('App\EmployeeBackground','employee_id','id')->where('type','=','WORK')->orderBy('id','ASC');
    }
    public function education(){
        return $this->hasMany('App\EmployeeBackground','employee_id','id')->where('type','=','EDUCATION')->orderBy('id','ASC');
    }
    public function family(){
        return $this->hasMany('App\EmployeeBackground','employee_id','id')->where('type','=','FAMILY')->orderBy('id','ASC');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
}
