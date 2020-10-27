<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Audit;
use Auth;
use Session;
use Validator;
use App\Department;
use App\Position;
use Yajra\DataTables\Facades\DataTables;
use Crypt;
class SettingsController extends Controller
{
    function showDepartments(){
        $user = Auth::user();
        $selectQuery = Department::with('createdBy')->with('updatedBy')
            ->orderBy('name','ASC')
            ->get();
        return view('hr-department.settings.departments')
            ->with('admin_menu','SETTINGS')
            ->with('admin_sub_menu','DEPARTMENTS')
            ->with('user',$user)
            ->with('departments',$selectQuery);
    }
    function showCategoryLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';
        if(isset($data['cid']) && !empty($data['cid'])){
            $category_id = encryptor('decrypt',$data['cid']);
            $selectQuery = Category::find($category_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-category-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>username</th>
                                    <th>type</th>
                                    <th>Source</th>
                                    <th>Event</th>
                                    <th>Old</th>
                                    <th>New</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                ';
            }else{
                $resultHtml = '
                    <div class="alert alert-danger text-left">
                        <strong>Unable to find data. Please try again.</strong>
                    </div>
                ';
            }
        }else{
            $resultHtml = '
                <div class="alert alert-danger text-left">
                    <strong>Unable to find data. Please try again.</strong>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showDepartmentLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';
        if(isset($data['did']) && !empty($data['did'])){
            $department_id = encryptor('decrypt',$data['did']);
            $selectQuery = Department::find($department_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-department-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>username</th>
                                    <th>type</th>
                                    <th>Source</th>
                                    <th>Event</th>
                                    <th>Old</th>
                                    <th>New</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                ';
            }else{
                $resultHtml = '
                    <div class="alert alert-danger text-left">
                        <strong>Unable to find data. Please try again.</strong>
                    </div>
                ';
            }
        }else{
            $resultHtml = '
                <div class="alert alert-danger text-left">
                    <strong>Unable to find data. Please try again.</strong>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showPositions(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['did']) && !empty($data['did'])){
            $department_id = encryptor('decrypt',$data['did']);
            $selectQuery = Department::with('positions')->find($department_id);
            if($selectQuery){
                return view('hr-department.settings.positions')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','DEPARTMENTS')
                    ->with('department',$selectQuery)
                    ->with('user',$user);
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Unable to find department. Please try again');
                return back();
            }
        }
        else{
            Session::flash('success', 0);
            Session::flash('message', 'Unable to find department. Please try again');
            return back();
        }
    }
    function departmentContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])) {
            $id = encryptor('decrypt', $data['id']);
            $department = Department::where('id', '=', $id)->first();

            $resultHtml = '
                <div class="form-group">
                    <label>Department Code :</label>
                    <input type="text" class="form-control" required name="department-code-update" id="department-code-update" value="'.$department->code.'">
                </div>
                    <div class="form-group">
                        <label>Department Name :</label>
                        <input type="text" class="form-control" required name="department-name-update" id="department-name-update" value="'.$department->name.'">
                    </div>
                    <input type="hidden" class="form-control" required name="department-id" value="'.$data['id'].'">
            ';
        } else{
            $resultHtml='
                <div class="ibox-content">
                    <div class="alert-danger">
                        <h3>
                        <strong>Undefined!</strong> This Place is Undefined.
                    </h3>
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function positionContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $position = Position::where('id', '=', $id)->first();
            $department_id = encryptor('encrypt', $position->department_id);

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control" required name="position-name-update" id="position-name-update" value="'.$position->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="position-id" value="'.$data['id'].'">
                    <input type="hidden" class="form-control" required name="department-key" value="'.$department_id.'">
            ';
        } else {
            $resultHtml='
                <div class="ibox-content">
                    <div class="alert-danger">
                        <h3>
                        <strong>Undefined!</strong> This Place is Undefined.
                    </h3>
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'logs-departments-details'){
                $enc_department_id = $data['key'];
                $department_id = encryptor('decrypt',$enc_department_id);
                $selectQuery = Audit::with('user')
                    ->where('auditable_type','=','App\Department')
                    ->where('auditable_id','=',$department_id)
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('event', function($selectQuery) use($user) {
                        $returnValue = strToUpper($selectQuery->event);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= readableDate($selectQuery->created_at);
                        return $returnValue;
                    })
                    ->editColumn('old_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->old_values,true);
                        $returnValue = '<table class="small table table-small m-0 p-0"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td class="p-0 m-0" >'.$key.'</td>';
                                $returnValue .= '<td class="p-0 m-0">'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->editColumn('new_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->new_values,true);
                        $returnValue = '<table class="small table"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td>'.$key.'</td>';
                                $returnValue .= '<td>'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->addColumn('source_model', function($selectQuery) use($user) {
                        $returnValue = $selectQuery->user->username;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= $selectQuery->auditable_type;
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            elseif($postMode == 'logs-position-details'){
                $enc_position_id = $data['key'];
                $position_id = encryptor('decrypt',$enc_position_id);
                $selectQuery = Audit::with('user')
                    ->where('auditable_type','=','App\Position')
                    ->where('auditable_id','=',$position_id)
                    ->orderBy('created_at','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('event', function($selectQuery) use($user) {
                        $returnValue = strToUpper($selectQuery->event);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= readableDate($selectQuery->created_at);
                        return $returnValue;
                    })
                    ->editColumn('old_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->old_values,true);
                        $returnValue = '<table class="small table table-small m-0 p-0"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td class="p-0 m-0" >'.$key.'</td>';
                                $returnValue .= '<td class="p-0 m-0">'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->editColumn('new_values', function($selectQuery) use($user) {
                        $values = json_decode($selectQuery->new_values,true);
                        $returnValue = '<table class="small table"> ';
                        if(count($values) > 0){
                            foreach($values as $key=>$value){
                                $returnValue .= '<tr>';
                                $returnValue .= '<td>'.$key.'</td>';
                                $returnValue .= '<td>'.$value.'</td>';
                                $returnValue .= '</tr>';
                            }
                        }
                        $returnValue .= '<table class="small table"> ';
                        return $returnValue;
                    })
                    ->addColumn('source_model', function($selectQuery) use($user) {
                        $returnValue = $selectQuery->user->username;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= $selectQuery->auditable_type;
                        return $returnValue;
                    })
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }
        else{
            if($postMode == 'add-position'){
                $department_id = encryptor('decrypt',$data['department_key']);
                $attributes = [
                    'position' => 'Position',
                ];
                $rules = [
                    'position' => 'required|unique:positions,name,NULL,id,department_id,'.$department_id.'|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertQuery = new Position();
                    $insertQuery->name = strToUpper(trim($data['position']));
                    $insertQuery->department_id = $department_id;
                    $insertQuery->department_code = trim($data['department_code']);
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    if($insertQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Position Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add position. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-departments'){
                $id = encryptor('decrypt', $data['department-id']);

                $attributes = [
                    'department-code-update' => 'Department Code',
                    'department-name-update' => 'Department Name',
                ];
                $rules = [
                    'department-code-update' => 'required|unique:departments,code,'.$id.',id|max:10',
                    'department-name-update' => 'required|unique:departments,name,'.$id.',id|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateDepartmentQuery = Department::where('id', '=', $id)->first();
                    $updateDepartmentQuery->name = $data['department-name-update'];
                    $updateDepartmentQuery->code = $data['department-code-update'];
                    $updateDepartmentQuery->updated_by = $user->id;
                    $updateDepartmentQuery->updated_at = getDatetimeNow();

                    if($updateDepartmentQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update department. Please try again');
                    }
                }
                return back();

            }
            elseif($postMode == 'update-positions'){
                $id = encryptor('decrypt', $data['position-id']);
                $department_id = encryptor('decrypt', $data['department-key']);
                $attributes = [
                    'position-name-update' => 'Position',
                ];
                $rules = [
                    'position-name-update' => 'required|unique:positions,name,'.$id.',id,department_id,'.$department_id.'|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updatePositionQuery = Position::where('id', '=', $id)->first();
                    $updatePositionQuery->name = strToUpper(trim($data['position-name-update']));
                    $updatePositionQuery->updated_by = $user->id;
                    $updatePositionQuery->updated_at = getDatetimeNow();

                    if($updatePositionQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update position. Please try again');
                    }
                }
                return back();
            }
            else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
