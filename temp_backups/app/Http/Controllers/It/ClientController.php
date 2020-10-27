<?php

namespace App\Http\Controllers\It;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use Validator;
use App\Audit;
use App\Client;
use App\Industry;
use App\CompanyBranch;
use App\BusinessStyle;
use App\Region;
use App\Province;
use App\City;
use Yajra\DataTables\Facades\DataTables;
use Crypt;

class ClientController extends Controller
{
    function showClients(Request $request){
        $user = Auth::user();
        $regions = showRegions();
        $selectIndustryQuery = Industry::where('is_active', '=', 1)
                                ->orderBy('name', 'ASC')
                                ->get();
        $selectBusinessStyleQuery = BusinessStyle::orderBy('name', 'ASC')
                                ->get();
        return view('it-department.clients.index')
                        ->with('admin_menu','CLIENTS')
                        ->with('regions', $regions)
                        ->with('industries', $selectIndustryQuery)
                        ->with('business_styles', $selectBusinessStyleQuery);
    }

    function clientContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $client = Client::where('id', '=', $id)->first();

            if($client) {
                $regions = showRegions();
                $selectIndustryQuery = Industry::where('is_active', '=', 1)
                                ->orderBy('name', 'ASC')->get();
                $selectBusinessStyleQuery = BusinessStyle::orderBy('name', 'ASC')->get();

                $selectClientRegion = Province::select('id', 'region_id')
                                                ->where('id', '=', $client->province_id)
                                                ->first();
                $selectProvincesQuery = Province::where('region_id', '=', $selectClientRegion->region_id)
                                                ->where('is_enable','=',true)
                                                ->orderBy('description', 'ASC')
                                                ->get();
                $selectCitiesQuery = City::where('province_id', '=', $client->province_id)
                                                ->where('region_id', '=', $selectClientRegion->region_id)
                                                ->where('is_enable','=',true)
                                                ->orderBy('city_name', 'ASC')
                                                ->get();
                return view('it-department.clients.page-load.client-details')
                                        ->with('client', $client)
                                        ->with('regions', $regions)
                                        ->with('client_region', $selectClientRegion)
                                        ->with('provinces', $selectProvincesQuery)
                                        ->with('cities', $selectCitiesQuery)
                                        ->with('industries', $selectIndustryQuery)
                                        ->with('business_styles', $selectBusinessStyleQuery)
                                        ->with('client_type', $data['type']);
            } else {
                $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find supplier details. Please try again.
                    </div>
                </div>';
            }


        } else {
            $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find supplier details. Please try again.
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }

    function showClientLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['clid']) && !empty($data['clid'])){
            $client_id = encryptor('decrypt',$data['clid']);
            $selectQuery = Client::find($client_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-client-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Username</th>
                                    <th>Type</th>
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

    function showCompanyBranches(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['cid']) && !empty($data['cid'])){
            $client_id = encryptor('decrypt',$data['cid']);
            $selectQuery = Client::with('companyBranches')->find($client_id);
            $regions = showRegions();
            return view('it-department.clients.company-branches')
                ->with('admin_menu','CLIENTS')
                ->with('client',$selectQuery)
                ->with('regions', $regions);
        } else {
            Session::flash('success',0);
            Session::flash('message','Unable to find product. Please try again');
        }
        return back();
    }

    function companyBranchContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $company_branch = CompanyBranch::where('id', '=', $id)->first();
            $enc_client_id = encryptor('encrypt', $company_branch->client_id);
            $regions = showRegions();
            $selectCompanyBranchRegion = Province::select('id', 'region_id')
                                            ->where('id', '=', $company_branch->province_id)->first();
            $selectProvincesQuery = Province::where([['region_id', '=', $selectCompanyBranchRegion->region_id],
                                                    ['is_enable','=',true]])
                                            ->orderBy('description', 'ASC')->get();
            $selectCitiesQuery = City::where([['province_id', '=', $company_branch->province_id],
                                               ['region_id', '=', $selectCompanyBranchRegion->region_id],
                                               ['is_enable','=',true]])
                                            ->orderBy('city_name', 'ASC')->get();
            $select_regions = '';
            foreach($regions as $region) {
                $enc_region_id = encryptor('encrypt', $region->id);
                $current_region = '';
                if($region->id == $selectCompanyBranchRegion->region_id) { $current_region = 'selected'; }
                $select_regions .= '<option value="'.$enc_region_id.'" '.$current_region.'>'.$region->description.'</option>';
            }
            $select_provinces = '';
            foreach($selectProvincesQuery as $province) {
                $enc_province_id = encryptor('encrypt', $province->id);
                $current_province = '';
                if($province->id == $company_branch->province_id) { $current_province = 'selected'; }
                $select_provinces .= '<option value="'.$enc_province_id.'" '.$current_province.'>'.$province->description.'</option>';
            }
            $select_cities = '';
            foreach($selectCitiesQuery as $city) {
                $enc_city_id = encryptor('encrypt', $city->id);
                $current_city = '';
                if($city->id == $company_branch->city_id) { $current_city = 'selected'; }
                $select_cities .= '<option value="'.$enc_city_id.'" '.$current_city.'>'.$city->city_name.'</option>';
            }
            $resultHtml = '
                <div class="form-group mb-2">
                        <label>Branch Name :</label>
                        <input type="text" class="form-control" required name="company-branch-update" id="company-branch-update" value="'.$company_branch->name.'">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-control-plaintext" for="select-region">Region :</label>
                        <select class="form-control" id="select-region-update" required name="select-region-update">
                            <option value=""></option>
                            '.$select_regions.'
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-control-plaintext" for="select-province">Province :</label>
                        <select class="form-control" id="select-province-update" required name="select-province-update">
                            <option value=""></option>
                            '.$select_provinces.'
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-control-plaintext" for="select-city">City/Municipality :</label>
                        <select class="form-control" id="select-city-update" required name="select-city-update">
                            <option value=""></option>
                            '.$select_cities.'
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Complete Address</label>
                        <input type="text" class="form-control" required name="branch-complete-address-update" id="branch-complete-address-update" value="'.$company_branch->complete_address.'">
                    </div>
                    <div class="form-group">
                        <label>Zip Code :</label>
                        <input type="number" class="form-control" required name="branch-zip-code-update" id="branch-zip-code-update" value="'.$company_branch->zip_code.'">
                    </div>
                    <input type="hidden" name="company-branch-id" value="'.$data['id'].'">
                    <input type="hidden" name="client-id" value="'.$enc_client_id.'">
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

    function showCompanyBranchLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['cbid']) && !empty($data['cbid'])){
            $company_branch_id = encryptor('decrypt',$data['cbid']);
            $selectQuery = CompanyBranch::find($company_branch_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-company-branch-logs" width="100%" class="table table-bordered mt-0 mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Username</th>
                                    <th>Type</th>
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

    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'client-list') {
                $selectQuery = Client::orderBy('created_at');
                        return Datatables::eloquent($selectQuery)
                                    ->addColumn('actions', function($selectQuery){
                                        $enc_client_id = encryptor('encrypt',$selectQuery->id);
                                        if($selectQuery->is_verified == 1) {
                                            $updateFunction = 'updateClient("'.$enc_client_id.'","client")';
                                        } else {
                                            $updateFunction = 'updateClient("'.$enc_client_id.'","prospect")';
                                        }
                                        $returnValue = '<button class="btn btn-info btn-icon btn-sm waves-effect waves-themed mb-1" onClick='.$updateFunction.' data-toggle="tooltip" data-placement="top" title="Update" data-original-title="UPDATE">
                                                    <i class="fas fa-edit"></i>
                                                </button>&nbsp;';
                                        $branchLink = route('client-company-branches',['cid' => $enc_client_id]);
                                        $returnValue .= '<a href="'.$branchLink.'" class="btn btn-success btn-icon btn-sm waves-effect waves-themed mb-1" data-toggle="tooltip" data-placement="top" title="Branches" data-original-title="BRANCHES">
                                                    <i class="fal fa-code-branch"></i></a>&nbsp;';
                                        $returnValue .= '<button onClick=logsModal("'.$enc_client_id.'") class="btn btn-default btn-sm btn-icon waves-effect waves-themed mb-1" data-toggle="tooltip" data-placement="top" title="History Logs" data-original-title="HISTORY LOGS">
                                                    <i class="ni ni-calendar"></i>
                                                </button>';
                                        return $returnValue;
                                    })
                                    ->editColumn('name', function($selectQuery) {
                                        if($selectQuery->is_verified == 1) {
                                            $client_type = 'VERIFIED';
                                        } else {
                                            $client_type = 'PROSPECT';
                                        }
                                        $returnValue ='<span class="badge badge-primary">'.$client_type.'</span> | ';
                                        $returnValue .= $selectQuery->name;
                                        $returnValue .='<hr class="m-0 mt-1">';
                                        $tin_number = '[ ]';
                                        if($selectQuery->tin_number != NULL) {
                                            $tin_number = $selectQuery->tin_number;
                                        }
                                        $returnValue .= '<text title="'.$tin_number.'" class="small text-primary mb-1" style="font-size:12px;">TIN: <b>'.$tin_number.'</b></text><br>';
                                        return $returnValue;
                                    })
                                    ->editColumn('contact_person', function($selectQuery) {
                                        $returnValue = $selectQuery->contact_person;
                                        if(!empty($selectQuery->position) && $selectQuery->position != NULL) {
                                            $returnValue .='<hr class="m-0 mt-1">';
                                            $returnValue .= '<text title="'.$selectQuery->position.'" class="small text-primary" style="font-size:12px;">Position: <b>'.$selectQuery->position.'</b></text>';   
                                        }
                                        return $returnValue;
                                    })
                                    ->editColumn('contact_numbers', function($selectQuery) {
                                        $contact_numbers = explode(',',$selectQuery->contact_numbers);
                                        $returnValue = '';
                                        if($contact_numbers){
                                            foreach($contact_numbers as $number){
                                                $returnValue .='<span title="'.$number.'" class="badge badge-primary">'.$number.'</span> &nbsp;';
                                            }
                                        }
                                        return $returnValue;
                                    })
                                    ->editColumn('emails', function($selectQuery) {
                                        $emails = explode(',',$selectQuery->emails);
                                        $returnValue = '';
                                        if($emails){
                                            foreach($emails as $email){
                                                $returnValue .='<span title="'.$email.'" class="badge badge-primary">'.$email.'</span> &nbsp;';
                                            }
                                        }
                                        return $returnValue;
                                    })
                                   
                                    ->smart(true)
                                    ->escapeColumns([])
                                    ->addIndexColumn()
                                    ->make(true);
            }
            elseif($postMode == 'check-client-exist') {
                $client_name = $data['client_name'];
                $checkClientQuery = Client::where('name', 'like', "$client_name%")
                                            ->where('user_id', '!=', $user->id)
                                            ->with('user')
                                            ->get();
                $array = [];
                if($checkClientQuery) {
                    foreach($checkClientQuery as $client) {
                        $result = "<code>".$client->user->employee->first_name." : </code>".$client->name;
                        array_push($array, $result);
                    }
                    $array = array_unique($array);
                    $return = implode('<br>', $array);
                } else {
                    $return = "<code>Something went wrong. Please try again</code>";
                }
                return $return;
            }
            elseif($postMode == 'logs-clients-details'){
                $enc_client_id = $data['key'];
                $client_id = encryptor('decrypt', $enc_client_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Client')
                                ->where('auditable_id','=',$client_id)
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
            elseif($postMode == 'logs-company-branches-details'){
                $enc_company_branch_id = $data['key'];
                $company_branch_id = encryptor('decrypt', $enc_company_branch_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\CompanyBranch')
                                ->where('auditable_id','=',$company_branch_id)
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
            else {
                return array('success' => 0, 'message' => 'Undefined Method');
            }

        } else {
            if($postMode == 'add-clients') {
                $position = NULL;
                if($data['client-position'] != "") {
                    $position = $data['client-position'];
                }
                $tin_number = NULL;
                if($data['client-tin-number'] != "") {
                    $tin_number = $data['client-tin-number'];
                }
                $business_style = NULL;
                if($data['select-business-style'] != "") {
                    $business_style = $data['client-business-style'];
                }
                if($data['client_type'] == 'client') {
                    $is_verified = 1;
                    $attributes = ['client-zip-code' => 'Zip Code'];
                    $rules = ['client-zip-code' => 'required|min:4'];
                } else {
                    $is_verified = 0;
                }
                $zip_code = NULL;
                if($data['client-zip-code'] != "") {
                    $zip_code = $data['client-zip-code'];
                }
                $province_id = encryptor('decrypt', $data['select-province']);
                $city_id = encryptor('decrypt', $data['select-city']);
                $industry_id = encryptor('decrypt', $data['select-industry']);
                $attributes = [
                    'client-name' => 'Client Name',
                    'client-contact-person' => 'Contact Person',
                    'client-contact-number' => 'Contact Number',
                    'client-email' => 'Email',
                    'select-region' => 'Reqion',
                    'select-province' => 'Province',
                    'select-city' => 'City',
                    'client-complete-address' => 'Complete Address',
                    'select-industry' => 'Industry',
                ];
                $rules = [
                    'client-name' => 'required|unique:clients,name,NULL,id,user_id,'.$user->id.'|max:50',
                    'client-contact-person' => 'required|max:50',
                    'client-contact-number' => 'required|max:50',
                    'client-email' => 'required|max:100',
                    'select-region' => 'required',
                    'select-province' => 'required',
                    'select-city' => 'required',
                    'client-complete-address' => 'required',
                    'select-industry' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertClientQuery = new Client();
                    $insertClientQuery->user_id = $user->id;
                    $insertClientQuery->name = trim($data['client-name']);
                    $insertClientQuery->branch_id = NULL;
                    $insertClientQuery->is_verified = $is_verified;
                    $insertClientQuery->contact_person = $data['client-contact-person'];
                    $insertClientQuery->contact_numbers = $data['client-contact-number'];
                    $insertClientQuery->position = $position;
                    $insertClientQuery->emails = $data['client-email'];
                    $insertClientQuery->province_id = $province_id;
                    $insertClientQuery->city_id = $city_id;
                    $insertClientQuery->zip_code = $zip_code;
                    $insertClientQuery->complete_address = $data['client-complete-address'];
                    $insertClientQuery->tin_number = $tin_number;
                    $insertClientQuery->industry_id = $industry_id;
                    $insertClientQuery->business_style = $business_style;
                    $insertClientQuery->created_by = $user->id;
                    $insertClientQuery->updated_by = $user->id;
                    $insertClientQuery->created_at = getDatetimeNow();
                    $insertClientQuery->updated_at = getDatetimeNow();
                    if($insertClientQuery->save()){
                        Session::flash('success', 1);
                        if($data['client_type'] == 'client') {
                            Session::flash('message', 'Client Added');
                        } else {
                            Session::flash('message', 'Prospect client Added');
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add client. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-clients') {
                $id = encryptor('decrypt', $data['client-id']);
                $position = NULL;
                if($data['client-position-update'] != "") {
                    $position = $data['client-position-update'];
                }
                $tin_number = NULL;
                if($data['client-tin-number-update'] != "") {
                    $tin_number = $data['client-tin-number-update'];
                }
                $business_style = NULL;
                if($data['select-business-style-update'] != "") {
                    $business_style = $data['client-business-style-update'];
                }
                if($data['client-type'] == 'client') {
                    $is_verified = 1;
                    $attributes = ['client-zip-code-update' => 'Zip Code'];
                    $rules = ['client-zip-code-update' => 'required|min:4'];
                } else {
                    $is_verified = 0;
                }
                $zip_code = NULL;
                if($data['client-zip-code-update'] != "") {
                    $zip_code = $data['client-zip-code-update'];
                }
                $province_id = encryptor('decrypt', $data['select-province-update']);
                $city_id = encryptor('decrypt', $data['select-city-update']);
                $industry_id = encryptor('decrypt', $data['select-industry-update']);
                $attributes = [
                    'client-name-update' => 'Client Name',
                    'client-contact-person-update' => 'Contact Person',
                    'client-contact-number-update' => 'Contact Number',
                    'client-email-update' => 'Email',
                    'select-region-update' => 'Reqion',
                    'select-province-update' => 'Province',
                    'select-city-update' => 'City',
                    'client-complete-address' => 'Complete Address',
                    'select-industry-update' => 'Industry',
                ];
                $rules = [
                    'client-name-update' => 'required|unique:clients,name,'.$id.',id,user_id,'.$user->id.'|max:50',
                    'client-contact-person-update' => 'required|max:50',
                    'client-contact-number-update' => 'required|max:50',
                    'client-email-update' => 'required|max:100',
                    'select-region-update' => 'required',
                    'select-province-update' => 'required',
                    'select-city-update' => 'required',
                    'client-complete-address-update' => 'required',
                    'select-industry-update' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateClientQuery = Client::find($id);
                    $updateClientQuery->name = trim($data['client-name-update']);
                    $updateClientQuery->is_verified = $is_verified;
                    $updateClientQuery->contact_person = $data['client-contact-person-update'];
                    $updateClientQuery->contact_numbers = $data['client-contact-number-update'];
                    $updateClientQuery->position = $position;
                    $updateClientQuery->emails = $data['client-email-update'];
                    $updateClientQuery->province_id = $province_id;
                    $updateClientQuery->city_id = $city_id;
                    $updateClientQuery->zip_code = $zip_code;
                    $updateClientQuery->complete_address = $data['client-complete-address-update'];
                    $updateClientQuery->tin_number = $tin_number;
                    $updateClientQuery->industry_id = $industry_id;
                    $updateClientQuery->business_style = $business_style;
                    $updateClientQuery->updated_by = $user->id;
                    $updateClientQuery->updated_at = getDatetimeNow();
                    if($updateClientQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        if($data['client-type'] == 'client') {
                            Session::flash('message', 'Unable to update client. Please try again');
                        } else {
                            Session::flash('message', 'Unable to update prospect. Please try again');
                        }   
                    }
                }
                return back();
            }
            elseif($postMode == 'add-company-branches'){
                $province_id = encryptor('decrypt', $data['select-province']);
                $city_id = encryptor('decrypt', $data['select-city']);
                $client_id = encryptor('decrypt', $data['client-id']);
                $attributes = [
                    'company-branch' => 'Branch Name',
                    'select-region' => 'Region',
                    'select-province' => 'Province',
                    'select-city' => 'City',
                    'branch-complete-address' => 'Complete Address',
                    'branch-zip-code' => 'Zip Code',
                ];
                $rules = [
                    'company-branch' => 'required|unique:company_branches,name,NULL,id,client_id,'.$client_id.'',
                    'select-region' => 'required',
                    'select-province' => 'required',
                    'select-city' => 'required',
                    'branch-complete-address' => 'required',
                    'branch-zip-code' => 'required|min:4|max:11',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertCompanyBranchQuery = new CompanyBranch();
                    $insertCompanyBranchQuery->name = trim(ucwords($data['company-branch']));
                    $insertCompanyBranchQuery->client_id = $client_id;
                    $insertCompanyBranchQuery->province_id = $province_id;
                    $insertCompanyBranchQuery->city_id = $city_id;
                    $insertCompanyBranchQuery->zip_code = $data['branch-zip-code'];
                    $insertCompanyBranchQuery->complete_address = trim($data['branch-complete-address']);
                    $insertCompanyBranchQuery->created_by = $user->id;
                    $insertCompanyBranchQuery->updated_by = $user->id;
                    $insertCompanyBranchQuery->created_at = getDatetimeNow();
                    $insertCompanyBranchQuery->updated_at = getDatetimeNow();
                    if($insertCompanyBranchQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Company Branch Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add company branch. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-company-branches'){
                $id = encryptor('decrypt', $data['company-branch-id']);
                $client_id = encryptor('decrypt', $data['client-id']);
                $province_id = encryptor('decrypt', $data['select-province-update']);
                $city_id = encryptor('decrypt', $data['select-city-update']);
                $attributes = [
                    'company-branch-update' => 'Branch Name',
                    'select-region-update' => 'Region',
                    'select-province-update' => 'Province',
                    'select-city-update' => 'City',
                    'branch-complete-address-update' => 'Complete Address',
                    'branch-zip-code-update' => 'Zip Code',
                ];
                $rules = [
                    'company-branch-update' => 'required|unique:company_branches,name,'.$id.',id,client_id,'.$client_id.'',
                    'select-region-update' => 'required',
                    'select-province-update' => 'required',
                    'select-city-update' => 'required',
                    'branch-complete-address-update' => 'required',
                    'branch-zip-code-update' => 'required|min:4|max:11',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateCompanyBranchQuery = CompanyBranch::where('id', '=', $id)->first();
                    $updateCompanyBranchQuery->name = trim(ucwords($data['company-branch-update']));
                    $updateCompanyBranchQuery->client_id = $client_id;
                    $updateCompanyBranchQuery->province_id = $province_id;
                    $updateCompanyBranchQuery->city_id = $city_id;
                    $updateCompanyBranchQuery->zip_code = $data['branch-zip-code-update'];
                    $updateCompanyBranchQuery->complete_address = trim($data['branch-complete-address-update']);
                    $updateCompanyBranchQuery->updated_by = $user->id;
                    $updateCompanyBranchQuery->updated_at = getDatetimeNow();

                    if($updateCompanyBranchQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update company branch. Please try again');
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
