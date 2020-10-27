<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Validator;
use App\Department;
use App\Position;
use App\Audit;
use App\Category;
use App\Attribute;
use App\SubCategory;
use App\Swatch;
use App\SwatchGroup;
use App\Team;
use App\Industry;
use App\QuotationTerm;
use App\Employee;
use App\EmployeeBackground;
use App\BusinessStyle;
use App\CompanyBranch;
use App\Region;
use App\Province;
use App\City;
use App\Barangay;
use App\Vehicle;
use App\AccountingPaper;
use App\Bank;
use App\Payee;
use App\AccountingTitle;
use App\AccountTitleParticular;
use App\EmployeeRequirement;
use App\User;
use App\Agent;
use App\PaymentLimit;
use App\JobRequestType;
use Yajra\DataTables\Facades\DataTables;
use Crypt;
class SettingsController extends Controller
{
	public function showAccountingTitleParticulars(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['acctid'])){
            $account_title_id = encryptor('decrypt',$data['acctid']);
            $selectQuery = AccountingTitle::with('particulars')->find($account_title_id);
            if($selectQuery){
                return view('it-department.settings.accounting-title-particulars')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','ACCOUNTING-TITLES')
                    ->with('account_title',$selectQuery)
                    ->with('user',$user);
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Unable to find account title. Please try again');
                return back();
            }
        }else{
            Session::flash('success', 0);
            Session::flash('message', 'Unable to find account title. Please try again');
            return back();
        }
    }
    public function showSubCategorySwatcheDetails(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $result =''; //html
        if(isset($data['scwgid'])){
            $swatch_group_id = encryptor('decrypt',$data['scwgid']);
            $selectQuery  = SwatchGroup::with('swatches')->find($swatch_group_id);
            $swatches = '';
            $swatchesChoices = '';
            foreach(swatchesCategory() as $index=>$swatch){
                if($selectQuery->category ==  $index){
                    $swatches = $swatches.'<option selected  value="'.$swatch.'">'.strtoupper($swatch).'</option>';
                }else{
                    $swatches = $swatches.'<option value="'.$swatch.'">'.strtoupper($swatch).'</option>';
                }
            }
            //selected swatches
            $selectQuerySwactches = Swatch::where('category','=',$selectQuery->category)->get();
            if($selectQuerySwactches){
                $selectedSwatches = $selectQuery->swatches;
                $result = ''; // html format
                $destination  = 'assets/img/swatches/';

                foreach($selectQuerySwactches as $index=>$swatch){
                    $ischecked ='';
                    $isDisabled ='disabled';
                    $order = '';
                    $swatchGroupQuery = $selectQuery->where('swatch_id',$swatch->id)->first();
                    if($swatchGroupQuery){
                        $ischecked ='checked';
                        $isDisabled ='';
                        $order = $swatchGroupQuery->order;
                    }
                    $swatch_id = encryptor('encrypt',$swatch->id);
                    $basePath = '//via.placeholder.com/300x300';
                    $filename = $swatch_id;
                    $path = imagePath($destination.''.$filename,$basePath);
                    $swatchesChoices = $swatchesChoices.'
                                <div class="col-md-2 p-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input id="form-2-'.$index.'" name="swatches[]" '.$ischecked.' onClick=isEnable("'.$swatch_id.'",this.id) type="checkbox" value="'.$swatch_id.'">
                                        </div>
                                        <div class="col-md-9">
                                            <input for="form-2-'.$index.'" '.$isDisabled.' value="'.$order.'" name="'.$swatch_id.'-order" id="'.$swatch_id.'-order"   class="input input-xs form-control" value="" min="1" maxlength="100" type="number"/>
                                        </div>
                                    </div>
                                    <input name="'.$swatch_id.'" value="'.$swatch->name.'" readonly type="hidden"/>
                                    <img src="'.$path.'" alt="'.$swatch->swatch_name.'"  title="'.$swatch->name.'" class="img-fluid mt-0"/>
                                </div>
                            ';
                }
            }
            $result = '
                <input type="hidden" name="swatch_group_id" value="'.$data['scwgid'].'" readonly/>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Group name</label>
                        <input class="input form-control"  name="group_name" value="'.$selectQuery->name.'" type="text" maxlength="50"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Swatch Category</label>
                        <select class="input form-control" name="category" id="update-swatch-category" onChange=genSwatch(this.value,"update-swatch-category","update-swatches")>
                            <option selected value="">Choose swatch category</option>
                            '.$swatches.'
                        </select>
                    </div>
                </div>
                <div class="col-md-12" id="swatch-details">
                    <hr>
                    <text class="text-danger" >Please Select Swatch</text>
                    <div class="row" id="update-swatches">
                        '.$swatchesChoices.'
                    </div>
                </div>
            ';
        }else{
            $result = '
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        Unable to find swatch group please try again
                    </div>
                </div>
            ';
        }
        return $result;
    }
    function showSwatchesGroups(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['scid']) && !empty($data['scid'])){
            $sub_category_id = encryptor('decrypt',$data['scid']);
            $selectQuery = SubCategory::with('category')->find($sub_category_id);
            $selectQuerySwatch = SwatchGroup::whereNull('parent_id')->where('sub_category_id',$sub_category_id)->get();
            if($selectQuery){
                return view('it-department.settings.swatch-groups')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','CATEGORIES')
                    ->with('group_swatches',$selectQuerySwatch)
                    ->with('sub_category',$selectQuery)
                    ->with('user',$user);
                return $selectQuery;
            }else{
                Session::flash('success',0);
                Session::flash('message', 'Unable to fetch sub category');
            }
        }else{
            Session::flash('success',0);
            Session::flash('message', 'Unable to fetch sub category');
        }
        return back();
    }
    function showSubCategories(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['cid']) && !empty($data['cid'])){
            $category_id = encryptor('decrypt',$data['cid']);
            $selectCategoryQuery = Category::find($category_id);
            $selectQuery = SubCategory::with('category')->where('category_id','=',$category_id)->orderBy('name')->get();
            return view('it-department.settings.sub-categories')
                ->with('admin_menu','SETTINGS')
                ->with('admin_sub_menu','CATEGORIES')
                ->with('category',$selectCategoryQuery)
                ->with('sub_categories',$selectQuery)
                ->with('user',$user);
        }else{
            Session::flash('success',0);
            Session::flash('message', 'Unable to fetch sub categories');
            return back();
        }
    }
    function showCategories(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $selectQuery = Category::orderBy('name')->get();
        return view('it-department.settings.categories')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','CATEGORIES')
                    ->with('categories',$selectQuery)
                    ->with('user',$user);
    }
    function showSubCategoryLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';
        if(isset($data['scid']) && !empty($data['scid'])){
            $sub_category_id = encryptor('decrypt',$data['scid']);
            $selectQuery = SubCategory::find($sub_category_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-sub-category-logs" width="100%" class="table table-bordered mt-0 mb-3">
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
                return view('it-department.settings.positions')
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
    function showDepartments(){
        $user = Auth::user();
        $selectQuery = Department::with('createdBy')->with('updatedBy')
                                ->orderBy('name','ASC')
                                ->get();
        return view('it-department.settings.departments')
            ->with('admin_menu','SETTINGS')
            ->with('admin_sub_menu','DEPARTMENTS')
                ->with('user',$user)
                ->with('departments',$selectQuery);
    }
    function showSwatches(){
        $user = Auth::user();
        $selectQuery = Swatch::orderBy('name','ASC')
                            ->get();
        $categories = swatchesCategory();

        return view('it-department.settings.swatches')
        ->with('user',$user)
        ->with('admin_menu','SETTINGS')
        ->with('admin_sub_menu','SWATCHES')
        ->with('swatches',$selectQuery)
        ->with('categories',$categories);
    }

    function showTeams(){
        $user = Auth::user();
        $selectQuery = Team::orderBy('name', 'ASC')
                            ->get();
        return view('it-department.settings.teams')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','TEAMS')
                    ->with('teams', $selectQuery);
    }

    function teamContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])) {
            $id = encryptor('decrypt', $data['id']);
            $team = Team::where('id', '=', $id)->first();
            if($team->branch == 'QUEZON-CITY') {
                $teamBranchOpt = '<option value="QUEZON-CITY" selected>Quezon City</option>
                            <option value="MAKATI">Makati</option>';
            } elseif($team->branch == 'MAKATI') {
                $teamBranchOpt = '<option value="QUEZON-CITY">Quezon City</option>
                            <option value="MAKATI" selected>Makati</option>';
            }
            $resultHtml = '
                    <div class="form-group">
                        <label>Team Name :</label>
                        <input type="text" class="form-control" required name="team-name-update" id="team-name-update" value="'.$team->name.'">
                    </div>
                    <div class="form-group">
                        <label>Display Name :</label>
                        <input type="text" class="form-control" required name="display-name-update" id="display-name-update" value="'.$team->display_name.'">
                    </div>
                    <div class="form-group">
                        <label>Select Branch :</label>
                        <select class="form-control" id="select-branch-update" required name="select-branch-update">
                            <option value="">Select Branch</option>
                            "'.$teamBranchOpt.'"
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Telephone :</label>
                        <input type="text" class="bootstrap-tagsinput" required name="team-telephone-update" id="team-telephone-update" value="'.$team->telephone.'">
                    </div>
                    <input type="hidden" class="form-control" required name="team-id" value="'.$data['id'].'">
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

    function changeTeamManagerContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])) {
            $id = encryptor('decrypt', $data['id']);
            $team = Team::where('id', '=', $id)->first();
            if($team) {
                $enc_manager_id = encryptor('encrypt', $team->team_manager_id);
                $salesManagerPosition = Position::where('name', '=', 'SALES MANAGER')
                                                ->where('department_code', '=', 'SLS')
                                                ->first();
                $selectSalesManager = User::where('department_code', '=', 'SLS')
                                            ->where('position_id', '=', $salesManagerPosition->id)
                                            ->where('team_id', '=', NULL)
                                            ->where('id', '!=', $team->team_manager_id)
                                            ->with('employee')
                                            ->get();
                $select_sales_managers = '';
                foreach($selectSalesManager as $user) {
                    $enc_user_id = encryptor('encrypt', $user->id);
                    $select_sales_managers .= '<option value="'.$enc_user_id.'">'.$user->employee->first_name.' '.$user->employee->last_name.'</option>';
                }
                $resultHtml = '
                        <div class="form-group">
                            <label>Sales Manager :</label>
                            <select class="form-control" id="select-manager-change" required name="select-manager-change">
                                <option value="">Select Sales Manager</option>
                                "'.$select_sales_managers.'"
                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Date end for previous team :</label>
                                <input type="date" class="form-control" id="date-end" required name="date-end">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Date Start :</label>
                                <input type="date" class="form-control" id="date-start" required name="date-start">
                            </div>
                        </div>
                        <input type="hidden" class="form-control" required name="team-id" value="'.$data['id'].'">
                        <input type="hidden" class="form-control" required name="team-manager-id" value="'.$enc_manager_id.'">
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

    function showTeamLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['tid']) && !empty($data['tid'])){
            $team_id = encryptor('decrypt',$data['tid']);
            $selectQuery = Team::find($team_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-team-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showIndustries(){
        $user = Auth::user();
        $selectQuery = Industry::orderBy('name', 'ASC')
                            ->get();

        return view('it-department.settings.industries')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','INDUSTRIES')
                    ->with('industries', $selectQuery);
    }

    function industryContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $industry = Industry::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-md" required name="industry-name-update" id="industry-name-update" value="'.$industry->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="industry-id" value="'.$data['id'].'">
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

    function showIndustryLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['iid']) && !empty($data['iid'])){
            $industry_id = encryptor('decrypt',$data['iid']);
            $selectQuery = Industry::find($industry_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-industry-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showQuotationTerms(){
        $user = Auth::user();
        $selectQuery = QuotationTerm::orderBy('name', 'ASC')
                            ->get();

        return view('it-department.settings.quotation-terms')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','QUOTATION-TERMS')
                    ->with('quotation_terms', $selectQuery);
    }

    function quotationTermContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $quotation_term = QuotationTerm::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-md" required name="quotation-term-name-updated" id="quotation-term-name-updated" value="'.$quotation_term->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="quotation-term-id" value="'.$data['id'].'">
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
    
    function showQuotationTermLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['qtid']) && !empty($data['qtid'])){
            $quotation_term_id = encryptor('decrypt',$data['qtid']);
            $selectQuery = QuotationTerm::find($quotation_term_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-quotation-term-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showBusinessStyles(){
        $user = Auth::user();
        $selectQuery = BusinessStyle::orderBy('name', 'ASC')
                                    ->get();
        return view('it-department.settings.business-styles')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','BUSINESS-STYLES')
                    ->with('business_styles', $selectQuery);
    }

    function businessStyleContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $business_style = BusinessStyle::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-md" required name="business-style-update" id="business-style-update" value="'.$business_style->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="business-style-id" value="'.$data['id'].'">
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

    function showBusinessStyleLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['bsid']) && !empty($data['bsid'])){
            $business_style_id = encryptor('decrypt',$data['bsid']);
            $selectQuery = BusinessStyle::find($business_style_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-business-style-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showCompanyBranches(){
        $user = Auth::user();
        $selectQuery = CompanyBranch::orderBy('name', 'ASC')
                                    ->get();
        $regions = showRegions();
        return view('it-department.settings.company-branches')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','COMPANY-BRANCHES')
                    ->with('company_branches', $selectQuery)
                    ->with('regions', $regions);
    }

    function companyBranchContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $company_branch = CompanyBranch::where('id', '=', $id)->first();
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
                    <input type="hidden" class="form-control" required name="company-branch-id" value="'.$data['id'].'">
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

    function showRegions(){
        $user = Auth::user();
        $selectQuery = Region::all();
        return view('it-department.settings.regions')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','REGIONS')
                    ->with('regions', $selectQuery);
    }

    function regionContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $region = Region::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="form-group">
                        <label>Region Name :</label>
                        <input type="text" class="form-control" required name="region-name-update" id="region-name-update" value="'.$region->description.'">
                    </div>
                    <div class="form-group">
                        <label>PSGC Code :</label>
                        <input type="text" class="form-control" required name="psgc-code-update" id="psgc-code-update" value="'.$region->psgc_code.'">
                    </div>
                    <div class="form-group">
                        <label>Country Code :</label>
                        <input type="text" class="form-control" required name="country-code-update" id="country-code-update" value="'.$region->country_code.'">
                    </div>
                    <input type="hidden" class="form-control" required name="region-id" value="'.$data['id'].'">
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

    function showRegionLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['rid']) && !empty($data['rid'])){
            $region_id = encryptor('decrypt',$data['rid']);
            $selectQuery = Region::find($region_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-region-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showProvinces(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['rid']) && !empty($data['rid'])){
            $region_id = encryptor('decrypt',$data['rid']);
            $selectQuery = Region::with('provinces')->find($region_id);
            if($selectQuery){
                return view('it-department.settings.provinces')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','REGIONS')
                    ->with('region',$selectQuery);
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Unable to find region. Please try again');
                return back();
            }
        }
        else{
            Session::flash('success', 0);
            Session::flash('message', 'Unable to find region. Please try again');
            return back();
        }
    }

    function provinceContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $province = Province::where('id', '=', $id)->first();
            $enc_region_id = encryptor('encrypt', $province->region_id);
            $resultHtml = '
                    <div class="form-group">
                        <label>Province Name :</label>
                        <input type="text" class="form-control" required name="province-name-update" id="province-name-update" value="'.$province->description.'">
                    </div>
                    <div class="form-group">
                        <label>PSGC Code :</label>
                        <input type="text" class="form-control" required name="psgc-code-update" id="psgc-code-update" value="'.$province->psgc_code.'">
                    </div>
                    <div class="form-group">
                        <label>Province Code :</label>
                        <input type="text" class="form-control" required name="province-code-update" id="province-code-update" value="'.$province->province_code.'">
                    </div>
                    <div class="form-group">
                        <label>Delivery Charge :</label>
                        <input type="number" class="form-control" name="delivery-charge-update" id="delivery-charge-update" value="'.$province->delivery_charge.'">
                    </div>
                    <input type="hidden" class="form-control" required name="province-id" value="'.$data['id'].'">
                    <input type="hidden" class="form-control" required name="region-id" value="'.$enc_region_id.'">
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

    function showProvinceLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['pid']) && !empty($data['pid'])){
            $province_id = encryptor('decrypt',$data['pid']);
            $selectQuery = Province::find($province_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-province-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showCities(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['pid']) && !empty($data['pid'])){
            $province_id = encryptor('decrypt',$data['pid']);
            $selectQuery = Province::with('cities')->with('region')->find($province_id);
            if($selectQuery){
                return view('it-department.settings.cities')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','REGIONS')
                    ->with('province',$selectQuery);
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Unable to find province. Please try again');
                return back();
            }
        }
        else{
            Session::flash('success', 0);
            Session::flash('message', 'Unable to find province. Please try again');
            return back();
        }
    }

    function cityContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $city = City::where('id', '=', $id)->first();
            $enc_province_id = encryptor('encrypt', $city->province_id);
            $resultHtml = '
                    <div class="form-group">
                        <label>City Name :</label>
                        <input type="text" class="form-control" required name="city-name-update" id="city-name-update" value="'.$city->city_name.'">
                    </div>
                    <div class="form-group">
                        <label>PSGC Code :</label>
                        <input type="text" class="form-control" required name="psgc-code-update" id="psgc-code-update" value="'.$city->psgc_code.'">
                    </div>
                    <div class="form-group">
                        <label>City/Municipality Code :</label>
                        <input type="text" class="form-control" required name="city-code-update" id="city-code-update" value="'.$city->city_municipality_code.'">
                    </div>
                    <div class="form-group">
                        <label>Delivery Charge :</label>
                        <input type="number" class="form-control" name="delivery-charge-update" id="delivery-charge-update" value="'.$city->delivery_charge.'">
                    </div>
                    <input type="hidden" class="form-control" required name="city-id" value="'.$data['id'].'">
                    <input type="hidden" class="form-control" required name="province-id" value="'.$enc_province_id.'">
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

    function showCityLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['cid']) && !empty($data['cid'])){
            $city_id = encryptor('decrypt',$data['cid']);
            $selectQuery = City::find($city_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-city-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showBarangays(Request $request){
        $user = Auth::user();
        $data = $request->all();
        if(isset($data['cid']) && !empty($data['cid'])){
            $city_id = encryptor('decrypt',$data['cid']);
            $selectQuery = City::with('barangays')->with('province')->with('region')->find($city_id);
            if($selectQuery){
                return view('it-department.settings.barangays')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','REGIONS')
                    ->with('city',$selectQuery);
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Unable to find city. Please try again');
                return back();
            }
        }
        else{
            Session::flash('success', 0);
            Session::flash('message', 'Unable to find city. Please try again');
            return back();
        }
    }

    function barangayContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $barangay = Barangay::where('id', '=', $id)->first();
            $enc_city_id = encryptor('encrypt', $barangay->city_id);
            $resultHtml = '
                    <div class="form-group">
                        <label>Barangay Name :</label>
                        <input type="text" class="form-control" required name="barangay-name-update" id="barangay-name-update" value="'.$barangay->barangay_description.'">
                    </div>
                    <div class="form-group">
                        <label>Barangay Code :</label>
                        <input type="text" class="form-control" required name="barangay-code-update" id="barangay-code-update" value="'.$barangay->barangay_code.'">
                    </div>
                    <div class="form-group">
                        <label>Delivery Charge :</label>
                        <input type="number" class="form-control" name="delivery-charge-update" id="delivery-charge-update" value="'.$barangay->additional_charge.'">
                    </div>
                    <input type="hidden" class="form-control" required name="barangay-id" value="'.$data['id'].'">
                    <input type="hidden" class="form-control" required name="city-id" value="'.$enc_city_id.'">
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

    function showBarangayLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['bid']) && !empty($data['bid'])){
            $barangay_id = encryptor('decrypt',$data['bid']);
            $selectQuery = Barangay::find($barangay_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-barangay-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showBanks(){
        $user = Auth::user();
        $selectQuery = Bank::orderBy('name', 'ASC')->get();
        return view('it-department.settings.banks')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','BANKS')
                    ->with('banks', $selectQuery);
    }

    function bankContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $bank = Bank::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="form-group">
                        <label>Bank Name :</label>
                        <input type="text" class="form-control" required name="bank-name-update" id="bank-name-update" value="'.$bank->name.'">
                    </div>
                    <div class="form-group">
                        <label>Display Name :</label>
                        <input type="text" class="form-control" required name="display-name-update" id="display-name-update" value="'.$bank->display_name.'">
                    </div>
                    <input type="hidden" class="form-control" required name="bank-id" value="'.$data['id'].'">
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

    function showBankLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['bid']) && !empty($data['bid'])){
            $bank_id = encryptor('decrypt',$data['bid']);
            $selectQuery = Bank::find($bank_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-bank-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showPayees(){
        $user = Auth::user();
        $selectQuery = Payee::orderBy('name', 'ASC')->get();
        return view('it-department.settings.payees')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','PAYEES')
                    ->with('payees', $selectQuery);
    }

    function payeeContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $payee = Payee::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-md" required name="payee-name-update" id="payee-name-update" value="'.$payee->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="payee-id" value="'.$data['id'].'">
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

    function showPayeeLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['pid']) && !empty($data['pid'])){
            $payee_id = encryptor('decrypt',$data['pid']);
            $selectQuery = Payee::find($payee_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-payee-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showVehicles(){
        $user = Auth::user();
        $selectQuery = Vehicle::all();
        return view('it-department.settings.vehicles')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','VEHICLES')
                    ->with('vehicles', $selectQuery);
    }

    function vehicleContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $vehicle = Vehicle::where('id', '=', $id)->first();

            $vehicle_type = ['JECAMS', 'TRANSPORTIFY'];
            $select_vehicle_type = "";
            foreach($vehicle_type as $type) {
                $current_vehicle_type = "";
                if($vehicle->type == $type) {
                    $current_vehicle_type = "selected";
                }
                $select_vehicle_type .= '<option value="'.$type.'" '.$current_vehicle_type.'>'.$type.'</option>';
            }

            $resultHtml = '
                    <div class="form-group">
                        <label>Vehicle Plate Number :</label>
                        <input type="text" class="form-control" required name="plate-number-update" id="plate-number-update" value="'.$vehicle->plate_number.'">
                    </div>
                    <div class="form-group">
                        <label>Vehicle Brand :</label>
                        <input type="text" class="form-control" required name="vehicle-brand-update" id="vehicle-brand-update" value="'.$vehicle->brand.'">
                    </div>
                    <div class="form-group">
                        <label>Type :</label>
                        <select class="form-control" id="select-type-update" required name="select-type-update">
                            <option value="">Select Type</option>
                            '.$select_vehicle_type.'
                        </select>
                    </div>
                    <input type="hidden" class="form-control" required name="vehicle-id" value="'.$data['id'].'">
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

    function showVehicleLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['vid']) && !empty($data['vid'])){
            $vehicle_id = encryptor('decrypt',$data['vid']);
            $selectQuery = Vehicle::find($vehicle_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-vehicle-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showAccountingPapers(){
        $user = Auth::user();
        $selectQuery = AccountingPaper::orderBy('name', 'ASC')->get();
        return view('it-department.settings.accounting-papers')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','ACCOUNTING-PAPERS')
                    ->with('accounting_papers', $selectQuery);
    }

    function accountingPaperContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $accounting_paper = AccountingPaper::where('id', '=', $id)->first();

            $accounting_paper_type = ['COLLECTION', 'SOA', 'INVOICE', 'CR'];
            $select_accounting_paper_type = "";
            foreach($accounting_paper_type as $type) {
                $current_accounting_paper_type = "";
                if($accounting_paper->type == $type) {
                    $current_accounting_paper_type = "selected";
                }
                $select_accounting_paper_type .= '<option value="'.$type.'" '.$current_accounting_paper_type.'>'.$type.'</option>';
            }

            $resultHtml = '
                    <div class="form-group">
                        <label>Accounting Paper :</label>
                        <input type="text" class="form-control" required name="accounting-paper-update" id="accounting-paper-update" value="'.$accounting_paper->name.'">
                    </div>
                    <div class="form-group">
                        <label>Type :</label>
                        <select class="form-control" id="select-type-update" required name="select-type-update">
                            <option value="">Select Type</option>
                            '.$select_accounting_paper_type.'
                        </select>
                    </div>
                    <input type="hidden" class="form-control" required name="accounting-paper-id" value="'.$data['id'].'">
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

    function showAccountingPaperLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['apid']) && !empty($data['apid'])){
            $accounting_paper_id = encryptor('decrypt',$data['apid']);
            $selectQuery = AccountingPaper::find($accounting_paper_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-accounting-paper-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showAccountingTitles(){
        $user = Auth::user();
        $selectQuery = AccountingTitle::orderBy('name', 'ASC')->get();
        return view('it-department.settings.accounting-titles')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','ACCOUNTING-TITLES')
                    ->with('accounting_titles', $selectQuery);
    }

    function accountingTitleContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $accounting_title = AccountingTitle::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-md" required name="accounting-title-name-update" id="accounting-title-name-update" value="'.$accounting_title->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="accounting-title-id" value="'.$data['id'].'">
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

    function showAccountingTitleLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['atid']) && !empty($data['atid'])){
            $accounting_title_id = encryptor('decrypt',$data['atid']);
            $selectQuery = AccountingTitle::find($accounting_title_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-accounting-title-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showEmployeeRequirements(){
        $user = Auth::user();
        $selectQuery = EmployeeRequirement::orderBy('name', 'ASC')->get();
        return view('it-department.settings.employee-requirements')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','EMPLOYEE-REQUIREMENTS')
                    ->with('employee_requirements', $selectQuery);
    }

    function employeeRequirementContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $employee_requirement = EmployeeRequirement::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-md" required name="employee-requirement-name-update" id="employee-requirement-name-update" value="'.$employee_requirement->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="employee-requirement-id" value="'.$data['id'].'">
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

    function showEmployeeRequirementLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['erid']) && !empty($data['erid'])){
            $employee_requirement_id = encryptor('decrypt',$data['erid']);
            $selectQuery = EmployeeRequirement::find($employee_requirement_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-employee-requirement-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showTermsAndConditions(){
        $user = Auth::user();
        $qoute_term_destination = 'assets/files/quotation_terms/';
        $quote_term_filename = 'terms';
        $quote_terms = toTxtFile($qoute_term_destination,$quote_term_filename,'get');
        
        $po_term_destination = 'assets/files/purchase_order_terms/';
        $po_term_filename = 'terms';
        $po_terms = toTxtFile($po_term_destination,$po_term_filename,'get');
        return view('it-department.settings.terms-and-conditions')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','TERMS-AND-CONDITIONS')
                    ->with('quotation_terms', $quote_terms)
                    ->with('po_terms', $po_terms);
    }

    function showPaymentRequestLimitations(){
        $user = Auth::user();
        $selectQuery = PaymentLimit::where('date_end', '=', NULL)->orderBy('type', 'ASC')->get();
        return view('it-department.settings.payment-request-limitations')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','PAYMENT-REQUEST-LIMITATIONS')
                    ->with('payment_limits', $selectQuery);
    }

    function paymentRequestLimitationContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $payment_limit = PaymentLimit::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="form-group">
                        <label>Payment Request Type :</label>
                        <input type="text" class="form-control" required name="payment-limit-type" value="'.$payment_limit->type.'" disabled>
                    </div>
                    <div class="form-group">
                        <label>Amount :</label>
                        <input type="number" class="form-control" required name="amount-limit-update" id="amount-limit-update" value="'.$payment_limit->amount.'">
                    </div>
                    <input type="hidden" class="form-control" required name="payment-limit-id" value="'.$data['id'].'">
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

    function showPaymentRequestLimitationLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['prlid']) && !empty($data['prlid'])){
            $payment_limit_id = encryptor('decrypt',$data['prlid']);
            $selectQuery = PaymentLimit::find($payment_limit_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-payment-request-limitation-logs" width="100%" class="table table-bordered mt-0 mb-3">
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

    function showJobRequestTypes(){
        $user = Auth::user();
        $selectQuery = JobRequestType::orderBy('name', 'ASC')->get();
        return view('it-department.settings.job-request-types')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','JOB-REQUEST-TYPES')
                    ->with('job_request_types', $selectQuery);
    }

    function jobRequestTypeContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])){
            $id = encryptor('decrypt', $data['id']);
            $job_request_type = JobRequestType::where('id', '=', $id)->first();

            $resultHtml = '
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-md" required name="job-request-type-name-update" id="job-request-type-name-update" value="'.$job_request_type->name.'">

                            <button type="submit" onClick="$(this).attr(\'disable\',true)" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                <i class="ni ni-note pt-2s pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" required name="job-request-type-id" value="'.$data['id'].'">
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

    function showJobRequestTypeLogs(Request $request){
        $user = Auth::user();
        $data = $request->all();
        $resultHtml = '';

        if(isset($data['jrtid']) && !empty($data['jrtid'])){
            $job_request_type_id = encryptor('decrypt',$data['jrtid']);
            $selectQuery = JobRequestType::find($job_request_type_id);
            if($selectQuery){
                $resultHtml = '
                        <table id="dt-job-request-type-logs" width="100%" class="table table-bordered mt-0 mb-3">
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
            if($postMode == 'update-category-status'){
                $category_id = encryptor('decrypt',$data['cid']);
                $updateQuery = Category::find($category_id);
                if($updateQuery){
                    $updateQuery->status = $data['stat'];
                    if($updateQuery->save()){
                        return array('success' => 1, 'message' => 'Category Status updated');
                    }else{
                        return array('success' => 0, 'message' => 'Unable to update category status. Please try again');
                    }
                }else{
                    return array('success' => 0, 'message' => 'Unable to update category status. Please try again');
                }
            }
            elseif($postMode == 'update-sub-category-status'){
                $category_id = encryptor('decrypt',$data['scid']);
                $updateQuery = SubCategory::find($category_id);
                if($updateQuery){
                    $updateQuery->status = $data['stat'];
                    if($updateQuery->save()){
                        return array('success' => 1, 'message' => 'Sub Category Status updated');
                    }else{
                        return array('success' => 0, 'message' => 'Unable to update sub category status. Please try again');
                    }
                }else{
                    return array('success' => 0, 'message' => 'Unable to update sub category status. Please try again');
                }
            }
            elseif($postMode == 'add-attribute'){
                $category_id = encryptor('decrypt',$data['category']);
                $attributes = [
                    'name' => 'Attribute name',
                ];
                $rules = [
                    'name' => 'required|unique:attributes,name,NULL,id,category_id,'.$category_id.'|max:20',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $insertQuery = new Attribute();
                    $insertQuery->name = trim($data['name']);
                    $insertQuery->category_id = trim($category_id);
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    if($insertQuery->save()){
                        return array('success' => 1, 'message' => 'Attribute Added');
                    }else{
                        return array('success' => 0, 'message' => 'Unable to add attribute. Please try again');
                    }
                }
            }
            elseif($postMode == 'attributes-list'){
                $category_id = encryptor('decrypt',$data['category']);
                $selectQuery = Attribute::where('category_id','=',$category_id)->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                    ->smart(true)
                    ->addIndexColumn()
                    ->escapeColumns([])
                    ->make(true);
            }
            elseif($postMode == 'logs-category-details'){
                $enc_category_id = $data['key'];
                $category_id = encryptor('decrypt',$enc_category_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Category')
                                ->where('auditable_id','=',$category_id)
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
            elseif($postMode == 'logs-sub-category-details'){
                $enc_sub_category_id = $data['key'];
                $sub_category_id = encryptor('decrypt',$enc_sub_category_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\SubCategory')
                                ->where('auditable_id','=',$sub_category_id)
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
            elseif($postMode == 'logs-departments-details'){
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
            elseif($postMode=='swatches-status'){
                $id = encryptor('decrypt',$data['id']);

                $swatchUpdate = Swatch::where('id','=',$id)->first();
                if($swatchUpdate->status=='INACTIVE'){
                    $change_status = 'ACTIVE';
                }else{
                    $change_status = 'INACTIVE';
                }

                $swatchUpdate->status = $change_status;
                $swatchUpdate->save();
            }
            elseif($postMode == 'create-swatch-group'){
                $attributes = [
                    'category' => 'Swatch Category',
                ];
                $rules = [
                    'category' => 'required|string|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $selectQuery = Swatch::where('category','=',$data['category'])->get();
                    if($selectQuery){
                        $result = ''; // html format
                        $destination  = 'assets/img/swatches/';
                        foreach($selectQuery as $index=>$swatch){
                            $swatch_id = encryptor('encrypt',$swatch->id);
                            $basePath = '//via.placeholder.com/300x300';
                            $filename = $swatch_id;
                            $path = imagePath($destination.''.$filename,$basePath);
                            $result = $result.'
                                <div class="col-md-2 p-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input checked="" id="form-2-'.$index.'" name="swatches[]" onClick=isEnable("'.$swatch_id.'",this.id) type="checkbox" value="'.$swatch_id.'">
                                        </div>
                                        <div class="col-md-9">
                                            <input for="form-2-'.$index.'" name="'.$swatch_id.'-order" id="'.$swatch_id.'-order"    class="input input-xs form-control" value="" min="1" maxlength="100" type="number"/>
                                        </div>
                                    </div>
                                    <input name="'.$swatch_id.'" value="'.$swatch->name.'" readonly type="hidden"/>
                                    <img src="'.$path.'" alt="'.$swatch->name.'"  title="'.$swatch->name.'" class="img-fluid mt-0"/>
                                </div>
                            ';
                        }
                        return array('success' => 1, 'message' => 'Success','data'=>$result);
                    }else{
                        return array('success' => 0, 'message' => 'No swatches in this category');
                    }
                }
            }
            else if($postMode == 'teams-status'){
                $id = encryptor('decrypt', $data['id']);

                $teamStatusUpdate = Team::where('id', '=', $id)->first();
                if($teamStatusUpdate->status == 'INACTIVE'){
                    $change_status = 'ACTIVE';
                }else{
                    $change_status = 'INACTIVE';
                }

                $teamStatusUpdate->status = $change_status;
                $teamStatusUpdate->save();

                return $change_status;
            }
            elseif($postMode == 'logs-teams-details'){
                $enc_team_id = $data['key'];
                $team_id = encryptor('decrypt', $enc_team_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Team')
                                ->where('auditable_id','=',$team_id)
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
            elseif($postMode == 'logs-industries-details'){
                $enc_industry_id = $data['key'];
                $industry_id = encryptor('decrypt', $enc_industry_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Industry')
                                ->where('auditable_id','=',$industry_id)
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
            elseif($postMode == 'industry-status'){
                $id = encryptor('decrypt', $data['id']);

                $industryStatusUpdate = Industry::where('id', '=', $id)->first();
                if($industryStatusUpdate->is_active == 0){
                    $change_status = 1;
                }else{
                    $change_status = 0;
                }

                $industryStatusUpdate->is_active = $change_status;
                $industryStatusUpdate->save();

                return $change_status;
            }
            elseif($postMode == 'logs-quotation-terms-details'){
                $enc_quotation_term_id = $data['key'];
                $quotation_term_id = encryptor('decrypt', $enc_quotation_term_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\QuotationTerm')
                                ->where('auditable_id','=',$quotation_term_id)
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
            elseif($postMode == 'logs-business-styles-details'){
                $enc_business_style_id = $data['key'];
                $business_style_id = encryptor('decrypt', $enc_business_style_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\BusinessStyle')
                                ->where('auditable_id','=',$business_style_id)
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
            elseif($postMode == 'logs-regions-details'){
                $enc_region_id = $data['key'];
                $region_id = encryptor('decrypt', $enc_region_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Region')
                                ->where('auditable_id','=',$region_id)
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
            elseif($postMode == 'region-status'){
                $id = encryptor('decrypt', $data['id']);

                $regionStatusUpdate = Region::where('id', '=', $id)->first();
                if($regionStatusUpdate->is_enable == 0){
                    $change_status = 1;
                }else{
                    $change_status = 0;
                }
                $regionStatusUpdate->is_enable = $change_status;
                $regionStatusUpdate->save();

                return $change_status;
            }
            elseif($postMode == 'logs-provinces-details'){
                $enc_province_id = $data['key'];
                $province_id = encryptor('decrypt', $enc_province_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Province')
                                ->where('auditable_id','=',$province_id)
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
            elseif($postMode == 'province-status'){
                $id = encryptor('decrypt', $data['id']);

                $provinceStatusUpdate = Province::where('id', '=', $id)->first();
                if($provinceStatusUpdate->is_enable == 0){
                    $change_status = 1;
                }else{
                    $change_status = 0;
                }
                $provinceStatusUpdate->is_enable = $change_status;
                $provinceStatusUpdate->save();

                return $change_status;
            }
            elseif($postMode == 'logs-cities-details'){
                $enc_city_id = $data['key'];
                $city_id = encryptor('decrypt', $enc_city_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\City')
                                ->where('auditable_id','=',$city_id)
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
            elseif($postMode == 'city-status'){
                $id = encryptor('decrypt', $data['id']);

                $cityStatusUpdate = City::where('id', '=', $id)->first();
                if($cityStatusUpdate->is_enable == 0){
                    $change_status = 1;
                }else{
                    $change_status = 0;
                }
                $cityStatusUpdate->is_enable = $change_status;
                $cityStatusUpdate->save();

                return $change_status;
            }
            elseif($postMode == 'logs-barangays-details'){
                $enc_barangay_id = $data['key'];
                $barangay_id = encryptor('decrypt', $enc_barangay_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Barangay')
                                ->where('auditable_id','=',$barangay_id)
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
            elseif($postMode == 'barangay-status'){
                $id = encryptor('decrypt', $data['id']);

                $barangayStatusUpdate = Barangay::where('id', '=', $id)->first();
                if($barangayStatusUpdate->is_enable == 0){
                    $change_status = 1;
                }else{
                    $change_status = 0;
                }
                $barangayStatusUpdate->is_enable = $change_status;
                $barangayStatusUpdate->save();

                return $change_status;
            }
            elseif($postMode == 'logs-banks-details'){
                $enc_bank_id = $data['key'];
                $bank_id = encryptor('decrypt', $enc_bank_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Bank')
                                ->where('auditable_id','=',$bank_id)
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
            elseif($postMode == 'logs-payees-details'){
                $enc_payee_id = $data['key'];
                $payee_id = encryptor('decrypt', $enc_payee_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Payee')
                                ->where('auditable_id','=',$payee_id)
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
            elseif($postMode == 'logs-vehicles-details'){
                $enc_vehicle_id = $data['key'];
                $vehicle_id = encryptor('decrypt', $enc_vehicle_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\Vehicle')
                                ->where('auditable_id','=',$vehicle_id)
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
            elseif($postMode == 'logs-accounting-papers-details'){
                $enc_accounting_paper_id = $data['key'];
                $accounting_paper_id = encryptor('decrypt', $enc_accounting_paper_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\AccountingPaper')
                                ->where('auditable_id','=',$accounting_paper_id)
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
            elseif($postMode == 'logs-accounting-titles-details'){
                $enc_accounting_title_id = $data['key'];
                $accounting_title_id = encryptor('decrypt', $enc_accounting_title_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\AccountingTitle')
                                ->where('auditable_id','=',$accounting_title_id)
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
            elseif($postMode == 'logs-employee-requirements-details'){
                $enc_employee_requirement_id = $data['key'];
                $employee_requirement_id = encryptor('decrypt', $enc_employee_requirement_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\EmployeeRequirement')
                                ->where('auditable_id','=',$employee_requirement_id)
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
            elseif($postMode == 'logs-payment-request-limitations-details'){
                $enc_payment_limit_id = $data['key'];
                $payment_limit_id = encryptor('decrypt', $enc_payment_limit_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\PaymentLimit')
                                ->where('auditable_id','=',$payment_limit_id)
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
            elseif($postMode == 'logs-job-request-types-details'){
                $enc_job_request_type_id = $data['key'];
                $job_request_type_id = encryptor('decrypt', $enc_job_request_type_id);
                $selectQuery = Audit::with('user')
                                ->where('auditable_type','=','App\JobRequestType')
                                ->where('auditable_id','=',$job_request_type_id)
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
            if($postMode == 'create-sub-categories'){
                $category_id = encryptor('decrypt',$data['category_key']);
                $attributes = [
                    'sub_category' => 'Sub Category',
                ];
                $rules = [
                    'sub_category' => 'required|unique:sub_categories,name,'.$category_id.',id|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertQuery = new SubCategory();
                    $insertQuery->name = trim($data['sub_category']);
                    $insertQuery->category_id = $category_id;
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    if($insertQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Sub Category Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add category. Please try again');
                    }
                }
                return back();
            }
			elseif($postMode == 'update-accounting-title-particular'){
				
                $account_title_id = encryptor('decrypt',$data['key']);
                $account_title_particular_id = encryptor('decrypt',$data['particular_key']);
                $attributes = [
                    'particulars' => 'Particular',
                ];
                $rules = [
                    'particulars' => 'required|unique:account_title_particulars,name,'.$account_title_particular_id.',id,account_title_id,'.$account_title_id,
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateQuery = AccountTitleParticular::find($account_title_particular_id);
                    if($updateQuery){
                        $updateQuery->name = trim($data['particulars']);
                        $updateQuery->updated_by = $user->id;
                        $updateQuery->updated_at = getDatetimeNow();
                        if($updateQuery->save()){
                            Session::flash('success',1);
                            Session::flash('message','Particular Updated');
                        }else{
                            Session::flash('success',0);
                            Session::flash('message','Unable to update particular. Please try again');
                        }
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to update particular. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-accounting-title-particular'){
                $account_title_id = encryptor('decrypt',$data['key']);
                $attributes = [
                    'particulars' => 'Particular',
                ];
                $rules = [
                    'particulars' => 'required|unique:account_title_particulars,name,NULL,id,account_title_id,'.$account_title_id,
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertQuery = new AccountTitleParticular();
                    $insertQuery->account_title_id = $account_title_id;
                    $insertQuery->name = trim($data['particulars']);
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    $insertQuery->created_at = getDatetimeNow();
                    $insertQuery->updated_at = getDatetimeNow();
                    if($insertQuery->save()){
                        Session::flash('success',1);
                        Session::flash('message','Particular Added');
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to save particular. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-sub-categories'){
                $sub_category_id = encryptor('decrypt',$data['sub_category']);
                $attributes = [
                    'sub_category_name' => 'Category',
                ];
                $rules = [
                    'sub_category_name' => 'required|unique:sub_categories,name,'.$sub_category_id.',id|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateQuery = SubCategory::find($sub_category_id);
                    $updateQuery->name = trim($data['sub_category_name']);
                    $updateQuery->updated_by = $user->id;
                    if($updateQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Sub Category Updated');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update sub category. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-categories'){
                $category_id = encryptor('decrypt',$data['category']);
                $attributes = [
                    'category_name' => 'Category',
                ];
                $rules = [
                    'category_name' => 'required|unique:categories,name,'.$category_id.',id|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateQuery = Category::find($category_id);
                    $updateQuery->name = trim($data['category_name']);
                    $updateQuery->updated_by = $user->id;
                    if($updateQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Category Updated');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update category. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'create-categories'){
                $attributes = [
                    'category' => 'Category',
                ];
                $rules = [
                    'category' => 'required|unique:categories,name,NULL|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertQuery = new Category();
                    $insertQuery->name = trim($data['category']);
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    if($insertQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Category Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add category. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-position'){
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
            elseif($postMode == 'add-department'){
                $attributes = [
                    'department_code' => 'Department Code',
                    'department_name' => 'Department Name',
                ];
                $rules = [
                    'department_code' => 'required|unique:departments,code|max:10',
                    'department_name' => 'required|unique:departments,name|max:50',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertQuery = new Department();
                    $insertQuery->code = trim(strtoupper($data['department_code']));
                    $insertQuery->name = trim($data['department_name']);
                    $insertQuery->created_by = $user->id;
                    $insertQuery->updated_by = $user->id;
                    if($insertQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Department Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add department. Please try again');
                    }
                    return back();
                }
            }
            elseif($postMode == 'add-swatches'){

                $insertSwatchQuery = new Swatch();
                $insertSwatchQuery->name = $data['swatch-name'];
                $insertSwatchQuery->category = $data['select-category'];
                $insertSwatchQuery->status = 'INACTIVE';
                $insertSwatchQuery->created_by = $user->id;
                $insertSwatchQuery->updated_by = $user->id;

                if($insertSwatchQuery->save()){
                    $destination = 'assets/img/swatches/';
                    $filename = encryptor('encrypt',$insertSwatchQuery->id);
                    $isExist = isExistFile($destination.''.$filename);
                    if($isExist['is_exist'] == true) {
                        unlink($isExist['path']);
                    }
                    $resultUpload = fileStorageUpload($data['swatch-img'],$destination,$filename,'resize',300,300);
                    Session::flash('success',1);
                    Session::flash('message','Successfully Added!');
                    return back();
                }
            }
            elseif($postMode == 'update-swatches'){
                $id = encryptor('decrypt',$data['swatch_key']);
                $updateSwatchQuery = Swatch::find($id);
                $updateSwatchQuery->name = $data['swatch-name-update'];
                $updateSwatchQuery->category = $data['select-category-update'];
                $updateSwatchQuery->updated_by = $user->id;
                $updateSwatchQuery->updated_at = getDatetimeNow();
                if($updateSwatchQuery->save()){
                    if(!empty($data['img'])){
                        $destination = 'assets/img/swatches/';
                        $filename = encryptor('encrypt',$id);
                        $isExist = isExistFile($destination.''.$filename);
                        if($isExist['is_exist'] == true) {
                            unlink($isExist['path']);
                        }
                        $resultUpload = fileStorageUpload($data['img'],$destination,$filename,'resize',300,300);
                        Session::flash('success',1);
                        Session::flash('message','Successfully Updated!');
                        return back();
                    }else{
                        Session::flash('success',1);
                        Session::flash('message','Successfully Updated!');
                        return back();
                    }
                }
            }
            elseif($postMode == 'update-swatch-group'){
                if(isset($data['swatch_group_id'])){
                    $swatch_group_id = $data['swatch_group_id'];
                    $swatch_group_id = encryptor('decrypt',$swatch_group_id);
                    $attributes = [
                        'group_name' => 'Group Name',
                        'category' => 'Category',
                        'swatches' => 'Swatches',
                    ];
                    $rules = [
                        'group_name' => 'required|string|max:50',
                        'category' => 'required|string|max:100',
                        'swatches' => 'required',
                    ];
                    $validator = Validator::make($data,$rules,[],$attributes);
                    if($validator->fails()){
                        Session::flash('success',0);
                        Session::flash('message',implode(',',$validator->errors()->all()));
                    }else{
                        $selectQuerySwatch = SwatchGroup::whereNull('parent_id')->where('id',$swatch_group_id)->first();
                        $childColumn = array();
                        $selected_swatches = $data['swatches'];
                        if($selectQuerySwatch){
                            $selectQuerySwatch->name= $data['group_name'];
                            $selectQuerySwatch->category= $data['category'];
                            $selectQuerySwatch->updated_by= $user->id;
                            if($selectQuerySwatch->save()){
                                $parent_id = $selectQuerySwatch->id;
                                foreach($selected_swatches as $selected_swatch){
                                    $swatch_id = encryptor('decrypt',$selected_swatch);
                                    $swatch_name = $data[$selected_swatch];
                                    $order = $data[$selected_swatch.'-order'];
                                    $date = getDatetimeNow();
                                    $array = array(
                                        'parent_id' => $parent_id,
                                        'swatch_id' => $swatch_id,
                                        'order' => $order,
                                        'swatch' => $swatch_name,
                                        'updated_by' => $user->id,
                                        'created_by' => $user->id,
                                        'created_at' => $date,
                                        'updated_at' => $date
                                    );
                                    array_push($childColumn,$array);
                                }
                                SwatchGroup::where('parent_id','=',$parent_id)->forceDelete();
                                insertSwatchType('bulk',$childColumn);
                                Session::flash('success',1);
                                Session::flash('message','Swatch group Updated');
                            }
                        }
                        else{
                            Session::flash('success',0);
                            Session::flash('message','Unable to update please try again');
                        }
                        return back();
                    }
                }
                else{
                    Session::flash('success',0);
                    Session::flash('message','Unable to update please try again');
                    return back();
                }
            }
            elseif($postMode == 'create-swatch-group'){
                $attributes = [
                    'group_name' => 'Group Name',
                    'category' => 'Category',
                    'swatches' => 'Swatches',
                ];
                $rules = [
                    'group_name' => 'required|string|max:50',
                    'category' => 'required|string|max:100',
                    'swatches' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $data['subcategory'] = encryptor('decrypt',$data['subcategory']);
                    $selected_swatches = $data['swatches'];
                    $parentColumn = array(
                        'name' => $data['group_name'],
                        'sub_category_id' => $data['subcategory'],
                        'category' => $data['category'],
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    );
                    $childColumn = array();
                    $resultInsert = insertSwatchType('parent',$parentColumn);
                    if($resultInsert['success'] == true){
                        $swatchParent = $resultInsert['data'];
                        foreach($selected_swatches as $selected_swatch){
                            $parent_id = $swatchParent->id;
                            $swatch_id = encryptor('decrypt',$selected_swatch);
                            $swatch_name = $data[$selected_swatch];
                            $order = $data[$selected_swatch.'-order'];
                            $date = getDatetimeNow();
                            $array = array(
                                'parent_id' => $parent_id,
                                'swatch_id' => $swatch_id,
                                'swatch' => $swatch_name,
                                'order' => $order,
                                'created_by' => $user->id,
                                'updated_by' => $user->id,
                                'created_at' => $date,
                                'updated_at' => $date
                            );
                            array_push($childColumn,$array);
                        }
                        insertSwatchType('bulk',$childColumn);
                        Session::flash('success',1);
                        Session::flash('message','Swatch group created.');
                        return back();
                    }else{
                        Session::flash('success',0);
                        Session::flash('message','Unable to create group swatches please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-teams') {
                $attributes = [
                    'team-name' => 'Team Name',
                    'display-name' => 'Display Name',
                    'select-branch' => 'Branch',
                    'team-telephone' => 'Telephone',
                ];
                $rules = [
                    'team-name' => 'required|unique:teams,name,NULL,id|max:50',
                    'display-name' => 'required|max:100',
                    'select-branch' => 'required',
                    'team-telephone' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertTeamQuery = new Team();
                    $insertTeamQuery->name = trim(strtoupper($data['team-name']));
                    $insertTeamQuery->display_name = trim(ucwords($data['display-name']));
                    $insertTeamQuery->branch = $data['select-branch'];
                    $insertTeamQuery->status = 'INACTIVE';
                    $insertTeamQuery->telephone = $data['team-telephone'];
                    $insertTeamQuery->team_manager = NULL;
                    $insertTeamQuery->team_manager_id = NULL;
                    $insertTeamQuery->created_by = $user->id;
                    $insertTeamQuery->updated_by = $user->id;
                    $insertTeamQuery->created_at = getDatetimeNow();
                    $insertTeamQuery->updated_at = getDatetimeNow();

                    if($insertTeamQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Team Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add team. Please try again');
                    }
                }
                return back();

            } 
            elseif($postMode == 'update-teams'){
                $id = encryptor('decrypt', $data['team-id']);
                $attributes = [
                    'team-name-update' => 'Team Name',
                    'display-name-update' => 'Display Name',
                    'select-branch-update' => 'Branch',
                    'team-telephone-update' => 'Telephone',
                ];
                $rules = [
                    'team-name-update' => 'required|unique:teams,name,'.$id.',id|max:50',
                    'display-name-update' => 'required|max:100',
                    'select-branch-update' => 'required',
                    'team-telephone-update' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateTeamQuery = Team::where('id', '=', $id)->first();
                    $updateTeamQuery->name = trim(strtoupper($data['team-name-update']));
                    $updateTeamQuery->display_name = $data['display-name-update'];
                    $updateTeamQuery->branch = $data['select-branch-update'];
                    $updateTeamQuery->telephone = $data['team-telephone-update'];
                    $updateTeamQuery->updated_by = $user->id;
                    $updateTeamQuery->updated_at = getDatetimeNow();
                    
                    if($updateTeamQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update team. Please try again'); 
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
            elseif($postMode == 'add-industries'){
                $attributes = [
                    'industry-name' => 'Industry Name',
                ];
                $rules = [
                    'industry-name' => 'required|unique:industries,name,NULL,id,is_active,1',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertIndustryQuery = new Industry();
                    $insertIndustryQuery->name = trim(ucwords($data['industry-name']));
                    $insertIndustryQuery->created_by = $user->id;
                    $insertIndustryQuery->updated_by = $user->id;
                    $insertIndustryQuery->created_at = getDatetimeNow();
                    $insertIndustryQuery->updated_at = getDatetimeNow();

                    if($insertIndustryQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Industry Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add industry. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-industries'){
                $id = encryptor('decrypt', $data['industry-id']);   
                $attributes = [
                    'industry-name-update' => 'Industry Name',
                ];
                $rules = [
                    'industry-name-update' => 'required|unique:industries,name,'.$id.',id,is_active,1',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateIndustryQuery = Industry::where('id', '=', $id)->first();
                    $updateIndustryQuery->name = $data['industry-name-update'];
                    $updateIndustryQuery->updated_by = $user->id;
                    $updateIndustryQuery->updated_at = getDatetimeNow();

                    if($updateIndustryQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update industry. Please try again');
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
            elseif($postMode == 'add-quotation-terms'){
                $attributes = [
                    'quotation-term-name' => 'Quotation Term',
                ];
                $rules = [
                    'quotation-term-name' => 'required|unique:quotation_terms,name,NULL,id',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertQuotationTermQuery = new QuotationTerm();
                    $insertQuotationTermQuery->name = trim(ucwords($data['quotation-term-name']));
                    $insertQuotationTermQuery->created_by = $user->id;
                    $insertQuotationTermQuery->updated_by = $user->id;
                    $insertQuotationTermQuery->created_at = getDatetimeNow();
                    $insertQuotationTermQuery->updated_at = getDatetimeNow();

                    if($insertQuotationTermQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Quotation Term Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add quotation term. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-quotation-terms'){
                $id = encryptor('decrypt', $data['quotation-term-id']);
                $attributes = [
                    'quotation-term-name-updated' => 'Quotation Term',
                ];
                $rules = [
                    'quotation-term-name-updated' => 'required|unique:quotation_terms,name,'.$id.',id',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateQuotationTermQuery = QuotationTerm::where('id', '=', $id)->first();
                    $updateQuotationTermQuery->name = $data['quotation-term-name-updated'];
                    $updateQuotationTermQuery->updated_by = $user->id;
                    $updateQuotationTermQuery->updated_at = getDatetimeNow();

                    if($updateQuotationTermQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update quotation term. Please try again');
                    }
                }
                
                return back();
            }
            elseif($postMode == 'add-business-styles'){
                $attributes = [
                    'business-style' => 'Business Style',
                ];
                $rules = [
                    'business-style' => 'required|unique:business_styles,name,NULL,id',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertBusinessStyleQuery = new BusinessStyle();
                    $insertBusinessStyleQuery->name = trim(ucwords($data['business-style']));
                    $insertBusinessStyleQuery->created_by = $user->id;
                    $insertBusinessStyleQuery->updated_by = $user->id;
                    $insertBusinessStyleQuery->created_at = getDatetimeNow();
                    $insertBusinessStyleQuery->updated_at = getDatetimeNow();
                    if($insertBusinessStyleQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Business Style Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add business style. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-business-styles'){
                $id = encryptor('decrypt', $data['business-style-id']);   
                $attributes = [
                    'business-style-update' => 'Business Style',
                ];
                $rules = [
                    'business-style-update' => 'required|unique:business_styles,name,'.$id.',id',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateIndustryQuery = BusinessStyle::where('id', '=', $id)->first();
                    $updateIndustryQuery->name = $data['business-style-update'];
                    $updateIndustryQuery->updated_by = $user->id;
                    $updateIndustryQuery->updated_at = getDatetimeNow();

                    if($updateIndustryQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update business style. Please try again');
                    }
                }
                
                return back();
            }
            elseif($postMode == 'add-company-branches'){
                $province_id = encryptor('decrypt', $data['select-province']);
                $city_id = encryptor('decrypt', $data['select-city']);
                $attributes = [
                    'company-branch' => 'Branch Name',
                    'select-region' => 'Region',
                    'select-province' => 'Province',
                    'select-city' => 'City',
                    'branch-complete-address' => 'Complete Address',
                    'branch-zip-code' => 'Zip Code',
                ];
                $rules = [
                    'company-branch' => 'required|unique:company_branches,name,NULL,id',
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
                    'company-branch-update' => 'required|unique:company_branches,name,'.$id.',id',
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
            elseif($postMode == 'add-regions'){
                $attributes = [
                    'region-name' => 'Region Name',
                    'psgc-code' => 'PSGC Code',
                    'country-code' => 'Country Code',
                ];
                $rules = [
                    'region-name' => 'required|unique:regions,description,NULL,id',
                    'psgc-code' => 'required|unique:regions,psgc_code,NULL,id|max:255',
                    'country-code' => 'required|max:10',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertRegionQuery = new Region();
                    $insertRegionQuery->psgc_code = trim($data['psgc-code']);
                    $insertRegionQuery->description = trim(strtoupper($data['region-name']));
                    $insertRegionQuery->country_code = trim($data['country-code']);
                    $insertRegionQuery->is_enable = 1;
                    $insertRegionQuery->created_at = getDatetimeNow();
                    $insertRegionQuery->updated_at = getDatetimeNow();
                    if($insertRegionQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Region Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add region. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-regions'){
                $id = encryptor('decrypt', $data['region-id']);
                $attributes = [
                    'region-name-update' => 'Region Name',
                    'psgc-code-update' => 'PSGC Code',
                    'country-code-update' => 'Country Code',
                ];
                $rules = [
                    'region-name-update' => 'required|unique:regions,description,'.$id.',id',
                    'psgc-code-update' => 'required|unique:regions,psgc_code,'.$id.',id|max:255',
                    'country-code-update' => 'required|max:10',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateRegionQuery = Region::where('id', '=', $id)->first();
                    $updateRegionQuery->psgc_code = trim($data['psgc-code-update']);
                    $updateRegionQuery->description = trim(strtoupper($data['region-name-update']));
                    $updateRegionQuery->country_code = trim($data['country-code-update']);
                    $updateRegionQuery->updated_at = getDatetimeNow();
                    if($updateRegionQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update region. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-provinces'){
                $region_id = encryptor('decrypt', $data['region-id']);
                $delivery_charge = NULL;
                if($data['delivery-charge'] != "") {
                    $delivery_charge = $data['delivery-charge'];
                }
                $attributes = [
                    'province-name' => 'Province Name',
                    'psgc-code' => 'PSGC Code',
                    'province-code' => 'Province Code',
                ];
                $rules = [
                    'province-name' => 'required|unique:provinces,description,NULL,id,region_id,'.$region_id.'',
                    'psgc-code' => 'required|unique:provinces,psgc_code,NULL,id,region_id,'.$region_id.'|max:255',
                    'province-code' => 'required|unique:provinces,province_code,NULL,id,region_id,'.$region_id.'|max:255',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertProvinceQuery = new Province();
                    $insertProvinceQuery->psgc_code = trim($data['psgc-code']);
                    $insertProvinceQuery->description = trim(strtoupper($data['province-name']));
                    $insertProvinceQuery->region_id = $region_id;
                    $insertProvinceQuery->province_code = trim($data['province-code']);
                    $insertProvinceQuery->delivery_charge = $delivery_charge;
                    $insertProvinceQuery->is_enable = 1;
                    $insertProvinceQuery->created_at = getDatetimeNow();
                    $insertProvinceQuery->updated_at = getDatetimeNow();
                    if($insertProvinceQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Province Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add province. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-provinces'){
                $id = encryptor('decrypt', $data['province-id']);
                $region_id = encryptor('decrypt', $data['region-id']);
                $delivery_charge = NULL;
                if($data['delivery-charge-update'] != "") {
                    $delivery_charge = $data['delivery-charge-update'];
                }
                $attributes = [
                    'province-name-update' => 'Province Name',
                    'psgc-code-update' => 'PSGC Code',
                    'province-code-update' => 'Province Code',
                ];
                $rules = [
                    'province-name-update' => 'required|unique:provinces,description,'.$id.',id,region_id,'.$region_id.'',
                    'psgc-code-update' => 'required|unique:provinces,psgc_code,'.$id.',id,region_id,'.$region_id.'|max:255',
                    'province-code-update' => 'required|unique:provinces,province_code,'.$id.',id,region_id,'.$region_id.'|max:255',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateProvinceQuery = Province::where('id', '=', $id)->first();
                    $updateProvinceQuery->psgc_code = trim($data['psgc-code-update']);
                    $updateProvinceQuery->description = trim(strtoupper($data['province-name-update']));
                    $updateProvinceQuery->province_code = trim($data['province-code-update']);
                    $updateProvinceQuery->delivery_charge = $delivery_charge;
                    $updateProvinceQuery->updated_at = getDatetimeNow();
                    if($updateProvinceQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update province. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-cities'){
                $region_id = encryptor('decrypt', $data['region-id']);
                $province_id = encryptor('decrypt', $data['province-id']);
                $province_code = $data['province-code'];
                $delivery_charge = NULL;
                if($data['delivery-charge'] != "") {
                    $delivery_charge = $data['delivery-charge'];
                }
                $attributes = [
                    'city-name' => 'City Name',
                    'psgc-code' => 'PSGC Code',
                    'city-code' => 'City/Municipality Code',
                ];
                $rules = [
                    'city-name' => 'required|unique:cities,city_name,NULL,id,province_id,'.$province_id.'',
                    'psgc-code' => 'required|unique:cities,psgc_code,NULL,id,province_id,'.$province_id.'|max:255',
                    'city-code' => 'required|unique:cities,city_municipality_code,NULL,id,province_id,'.$province_id.'|max:255',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertCityQuery = new City();
                    $insertCityQuery->psgc_code = trim($data['psgc-code']);
                    $insertCityQuery->city_name = trim(strtoupper($data['city-name']));
                    $insertCityQuery->region_id = $region_id;
                    $insertCityQuery->province_code = trim($province_code);
                    $insertCityQuery->province_id = $province_id;
                    $insertCityQuery->city_municipality_code = trim($data['city-code']);
                    $insertCityQuery->delivery_charge = $delivery_charge;
                    $insertCityQuery->is_enable = 1;
                    $insertCityQuery->created_at = getDatetimeNow();
                    $insertCityQuery->updated_at = getDatetimeNow();
                    if($insertCityQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'City Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add city. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-cities'){
                $id = encryptor('decrypt', $data['city-id']);
                $province_id = encryptor('decrypt', $data['province-id']);
                $delivery_charge = NULL;
                if($data['delivery-charge-update'] != "") {
                    $delivery_charge = $data['delivery-charge-update'];
                }
                $attributes = [
                    'city-name-update' => 'City Name',
                    'psgc-code-update' => 'PSGC Code',
                    'city-code-update' => 'City/Municipality Code',
                ];
                $rules = [
                    'city-name-update' => 'required|unique:cities,city_name,'.$id.',id,province_id,'.$province_id.'',
                    'psgc-code-update' => 'required|unique:cities,psgc_code,'.$id.',id,province_id,'.$province_id.'|max:255',
                    'city-code-update' => 'required|unique:cities,city_municipality_code,'.$id.',id,province_id,'.$province_id.'|max:255',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateCityQuery = City::where('id', '=', $id)->first();
                    $updateCityQuery->psgc_code = trim($data['psgc-code-update']);
                    $updateCityQuery->city_name = trim(strtoupper($data['city-name-update']));
                    $updateCityQuery->city_municipality_code = trim($data['city-code-update']);
                    $updateCityQuery->delivery_charge = $delivery_charge;
                    $updateCityQuery->updated_at = getDatetimeNow();
                    if($updateCityQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update city. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-barangays'){
                $region_code = encryptor('decrypt', $data['region-code']);
                $province_code = encryptor('decrypt', $data['province-code']);
                $city_id = encryptor('decrypt', $data['city-id']);
                $city_code = encryptor('decrypt', $data['city-code']);
                $delivery_charge = '0.00';
                if($data['delivery-charge'] != "") {
                    $delivery_charge = $data['delivery-charge'];
                }
                $attributes = [
                    'barangay-name' => 'Barangay Name',
                    'barangay-code' => 'Barangay Code',
                ];
                $rules = [
                    'barangay-name' => 'required|unique:barangays,barangay_description,NULL,id,city_id,'.$city_id.'',
                    'barangay-code' => 'required|unique:barangays,barangay_code,NULL,id,city_id,'.$city_id.'|max:255',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertBarangayQuery = new Barangay();
                    $insertBarangayQuery->additional_charge = $delivery_charge;
                    $insertBarangayQuery->barangay_code = trim($data['barangay-code']);
                    $insertBarangayQuery->barangay_description = trim($data['barangay-name']);
                    $insertBarangayQuery->region_code = $region_code;
                    $insertBarangayQuery->province_code = $province_code;
                    $insertBarangayQuery->city_municipality_code = $city_code;
                    $insertBarangayQuery->city_id = $city_id;
                    $insertBarangayQuery->is_enable = 1;
                    $insertBarangayQuery->created_at = getDatetimeNow();
                    $insertBarangayQuery->updated_at = getDatetimeNow();
                    if($insertBarangayQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Barangay Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add barangay. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-barangays'){
                $id = encryptor('decrypt', $data['barangay-id']);
                $city_id = encryptor('decrypt', $data['city-id']);
                $delivery_charge = '0.00';
                if($data['delivery-charge-update'] != "") {
                    $delivery_charge = $data['delivery-charge-update'];
                }
                $attributes = [
                    'barangay-name-update' => 'Barangay Name',
                    'barangay-code-update' => 'Barangay Code',
                ];
                $rules = [
                    'barangay-name-update' => 'required|unique:barangays,barangay_description,'.$id.',id,city_id,'.$city_id.'',
                    'barangay-code-update' => 'required|unique:barangays,barangay_code,'.$id.',id,city_id,'.$city_id.'|max:255',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateBarangayQuery = Barangay::where('id', '=', $id)->first();
                    $updateBarangayQuery->additional_charge = $delivery_charge;
                    $updateBarangayQuery->barangay_code = trim($data['barangay-code-update']);
                    $updateBarangayQuery->barangay_description = trim($data['barangay-name-update']);
                    $updateBarangayQuery->updated_at = getDatetimeNow();
                    if($updateBarangayQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update barangay. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-banks'){
                $attributes = [
                    'bank-name' => 'Bank Name',
                    'display-name' => 'Display Name',
                ];
                $rules = [
                    'bank-name' => 'required|unique:banks,name,NULL,id|max:50',
                    'display-name' => 'required|unique:banks,display_name,NULL,id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertBankQuery = new Bank();
                    $insertBankQuery->name = trim($data['bank-name']);
                    $insertBankQuery->display_name = trim($data['display-name']);
                    $insertBankQuery->created_by = $user->id;
                    $insertBankQuery->updated_by = $user->id;
                    $insertBankQuery->created_at = getDatetimeNow();
                    $insertBankQuery->updated_at = getDatetimeNow();
                    if($insertBankQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Bank Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add bank. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-banks'){
                $id = encryptor('decrypt', $data['bank-id']);
                $attributes = [
                    'bank-name-update' => 'Bank Name',
                    'display-name-update' => 'Display Name',
                ];
                $rules = [
                    'bank-name-update' => 'required|unique:banks,name,'.$id.',id|max:50',
                    'display-name-update' => 'required|unique:banks,display_name,'.$id.',id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateBankQuery = Bank::where('id', '=', $id)->first();
                    $updateBankQuery->name = trim($data['bank-name-update']);
                    $updateBankQuery->display_name = trim($data['display-name-update']);
                    $updateBankQuery->updated_by = $user->id;
                    $updateBankQuery->updated_at = getDatetimeNow();
                    if($updateBankQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update bank. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-payees'){
                $attributes = [
                    'payee-name' => 'Payee Name',
                ];
                $rules = [
                    'payee-name' => 'required|unique:payees,name,NULL,id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertPayeeQuery = new Payee();
                    $insertPayeeQuery->name = trim(strtoupper($data['payee-name']));
                    $insertPayeeQuery->created_by = $user->id;
                    $insertPayeeQuery->updated_by = $user->id;
                    $insertPayeeQuery->created_at = getDatetimeNow();
                    $insertPayeeQuery->updated_at = getDatetimeNow();
                    if($insertPayeeQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Payee Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add payee. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-payees'){
                $id = encryptor('decrypt', $data['payee-id']);
                $attributes = [
                    'payee-name-update' => 'Payee Name',
                ];
                $rules = [
                    'payee-name-update' => 'required|unique:payees,name,'.$id.',id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updatePayeeQuery = Payee::where('id', '=', $id)->first();
                    $updatePayeeQuery->name = trim(strtoupper($data['payee-name-update']));
                    $updatePayeeQuery->updated_by = $user->id;
                    $updatePayeeQuery->updated_at = getDatetimeNow();
                    if($updatePayeeQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update payee. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-vehicles'){
                $attributes = [
                    'plate-number' => 'Vehicle Plate Number',
                    'vehicle-brand' => 'Vehicle Brand',
                    'select-type' => 'Type',
                ];
                $rules = [
                    'plate-number' => 'required|unique:vehicles,plate_number,NULL,id|max:20',
                    'vehicle-brand' => 'required|max:100',
                    'select-type' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertVehicleQuery = new Vehicle();
                    $insertVehicleQuery->plate_number = trim($data['plate-number']);
                    $insertVehicleQuery->type = $data['select-type'];
                    $insertVehicleQuery->brand = trim($data['vehicle-brand']);
                    $insertVehicleQuery->created_by = $user->id;
                    $insertVehicleQuery->updated_by = $user->id;
                    $insertVehicleQuery->created_at = getDatetimeNow();
                    $insertVehicleQuery->updated_at = getDatetimeNow();
                    if($insertVehicleQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Vehicle Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add vehicle. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-vehicles'){
                $id = encryptor('decrypt', $data['vehicle-id']);
                $attributes = [
                    'plate-number-update' => 'Vehicle Plate Number',
                    'vehicle-brand-update' => 'Vehicle Brand',
                    'select-type-update' => 'Type',
                ];
                $rules = [
                    'plate-number-update' => 'required|unique:vehicles,plate_number,'.$id.',id|max:20',
                    'vehicle-brand-update' => 'required|max:100',
                    'select-type-update' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateVehicleQuery = Vehicle::where('id', '=', $id)->first();
                    $updateVehicleQuery->plate_number = trim($data['plate-number-update']);
                    $updateVehicleQuery->type = $data['select-type-update'];
                    $updateVehicleQuery->brand = trim($data['vehicle-brand-update']);
                    $updateVehicleQuery->updated_by = $user->id;
                    $updateVehicleQuery->updated_at = getDatetimeNow();
                    if($updateVehicleQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update vehicle. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-accounting-papers'){
                $attributes = [
                    'accounting-paper' => 'Accounting Paper',
                    'select-type' => 'Type',
                ];
                $rules = [
                    'accounting-paper' => 'required|unique:accounting_papers,name,NULL,id|max:100',
                    'select-type' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertAccountingPaperQuery = new AccountingPaper();
                    $insertAccountingPaperQuery->name = trim($data['accounting-paper']);
                    $insertAccountingPaperQuery->type = $data['select-type'];
                    $insertAccountingPaperQuery->created_by = $user->id;
                    $insertAccountingPaperQuery->updated_by = $user->id;
                    $insertAccountingPaperQuery->created_at = getDatetimeNow();
                    $insertAccountingPaperQuery->updated_at = getDatetimeNow();
                    if($insertAccountingPaperQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Accounting Paper Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add accounting paper. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-accounting-papers'){
                $id = encryptor('decrypt', $data['accounting-paper-id']);
                $attributes = [
                    'accounting-paper-update' => 'Accounting Paper',
                    'select-type-update' => 'Type',
                ];
                $rules = [
                    'accounting-paper-update' => 'required|unique:accounting_papers,name,'.$id.',id|max:100',
                    'select-type-update' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateAccountingPaperQuery = AccountingPaper::where('id', '=', $id)->first();
                    $updateAccountingPaperQuery->name = trim($data['accounting-paper-update']);
                    $updateAccountingPaperQuery->type = $data['select-type-update'];
                    $updateAccountingPaperQuery->updated_by = $user->id;
                    $updateAccountingPaperQuery->updated_at = getDatetimeNow();
                    if($updateAccountingPaperQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update accounting paper. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-accounting-titles'){
                $attributes = [
                    'accounting-title-name' => 'Accounting Title',
                ];
                $rules = [
                    'accounting-title-name' => 'required|unique:accounting_titles,name,NULL,id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertAccountingTitleQuery = new AccountingTitle();
                    $insertAccountingTitleQuery->name = trim($data['accounting-title-name']);
                    $insertAccountingTitleQuery->created_by = $user->id;
                    $insertAccountingTitleQuery->updated_by = $user->id;
                    $insertAccountingTitleQuery->created_at = getDatetimeNow();
                    $insertAccountingTitleQuery->updated_at = getDatetimeNow();
                    if($insertAccountingTitleQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Accounting Title Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add accounting title. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-accounting-titles'){
                $id = encryptor('decrypt', $data['accounting-title-id']);
                $attributes = [
                    'accounting-title-name-update' => 'Accounting Title',
                ];
                $rules = [
                    'accounting-title-name-update' => 'required|unique:accounting_titles,name,'.$id.',id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateAccountingTitleQuery = AccountingTitle::where('id', '=', $id)->first();
                    $updateAccountingTitleQuery->name = trim($data['accounting-title-name-update']);
                    $updateAccountingTitleQuery->updated_by = $user->id;
                    $updateAccountingTitleQuery->updated_at = getDatetimeNow();
                    if($updateAccountingTitleQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update accounting title. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-employee-requirements'){
                $attributes = [
                    'employee-requirement-name' => 'Requirement',
                ];
                $rules = [
                    'employee-requirement-name' => 'required|unique:employee_requirements,name,NULL,id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertEmployeeRequirementQuery = new EmployeeRequirement();
                    $insertEmployeeRequirementQuery->name = trim($data['employee-requirement-name']);
                    $insertEmployeeRequirementQuery->created_by = $user->id;
                    $insertEmployeeRequirementQuery->updated_by = $user->id;
                    $insertEmployeeRequirementQuery->created_at = getDatetimeNow();
                    $insertEmployeeRequirementQuery->updated_at = getDatetimeNow();
                    if($insertEmployeeRequirementQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Employee Requirement Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add employee requirement. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-employee-requirements'){
                $id = encryptor('decrypt', $data['employee-requirement-id']);
                $attributes = [
                    'employee-requirement-name-update' => 'Requirement',
                ];
                $rules = [
                    'employee-requirement-name-update' => 'required|unique:employee_requirements,name,'.$id.',id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateEmployeeRequirementQuery = EmployeeRequirement::where('id', '=', $id)->first();
                    $updateEmployeeRequirementQuery->name = trim($data['employee-requirement-name-update']);
                    $updateEmployeeRequirementQuery->updated_by = $user->id;
                    $updateEmployeeRequirementQuery->updated_at = getDatetimeNow();
                    if($updateEmployeeRequirementQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update employee requirement. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'change-team-manager') {
                $team_id = encryptor('decrypt', $data['team-id']);
                $prev_manager_id = encryptor('decrypt', $data['team-manager-id']);
                $attributes = [
                    'select-manager-change' => 'Sales Manager',
                    'date-end' => 'Date End',
                    'date-start' => 'Date Start',
                ];
                $rules = [
                    'select-manager-change' => 'required',
                    'date-end' => 'required',
                    'date-start' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    //change team_id to null for previous manager
                    $updateUserTeam = User::where('id', '=', $prev_manager_id)->first();
                    $updateUserTeam->team_id = NULL;
                    if($updateUserTeam->save()) {
                        //change team_id to null for new manager
                        $manager_id = encryptor('decrypt', $data['select-manager-change']);
                        $updateUserTeam = User::where('id', '=', $manager_id)->first();
                        $updateUserTeam->team_id = $team_id;
                        if($updateUserTeam->save()) {
                            //chnage manager of team
                            $selectEmployee = User::where('id', '=', $manager_id)->with('employee')->first();
                            $updateTeamManager = Team::where('id', '=', $team_id)->first();
                            $updateTeamManager->team_manager = $selectEmployee->employee->first_name.' '.$selectEmployee->employee->last_name;
                            $updateTeamManager->team_manager_id = $manager_id;
                            $updateTeamManager->status = 'ACTIVE';
                            if($updateTeamManager->save()) {
                                //end previous team
                                $updateAgentTeam = Agent::where('team_id', '=', $team_id)
                                                        ->where('user_id', '=', $prev_manager_id)
                                                        ->where('date_end', '=', NULL)
                                                        ->first();
                                $updateAgentTeam->date_end = $data['date-end'];
                                if($updateAgentTeam->save()) {
                                    //add new agent team
                                    $addAgentTeam = new Agent();
                                    $addAgentTeam->user_id = $manager_id;
                                    $addAgentTeam->team_id = $team_id;
                                    $addAgentTeam->team_name = $updateTeamManager->name;
                                    $addAgentTeam->quota = NULL;
                                    $addAgentTeam->date_start = $data['date-start'];
                                    $addAgentTeam->date_end = NULL;
                                    $addAgentTeam->manager_id = NULL;
                                    $addAgentTeam->is_manager = 1;
                                    $addAgentTeam->created_by = $user->id;
                                    $addAgentTeam->updated_by = $user->id;
                                    $addAgentTeam->created_at = getDatetimeNow();
                                    $addAgentTeam->updated_at = getDatetimeNow();
                                    if($addAgentTeam->save()) {
                                        Session::flash('success', 1);
                                        Session::flash('message', 'Team Manager has been updated!');
                                        return back();
                                    } else {
                                        Session::flash('success', 0);
                                        Session::flash('message', 'Unable to update team manager. Please try again');
                                        return back();
                                    }
                                } else {
                                    Session::flash('success', 0);
                                    Session::flash('message', 'Unable to update team. Please try again');
                                    return back();
                                }
                            } else {
                                Session::flash('success', 0);
                                Session::flash('message', 'Unable to update new manager team. Please try again');    
                                return back();
                            }
                        } else {
                            Session::flash('success', 0);
                            Session::flash('message', 'Unable to update current manager team. Please try again');
                            return back();
                        }
                    } else {
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update current manager team. Please try again');
                        return back();
                    }
                }
            }
            elseif($postMode == 'update-terms-and-conditions') {
                if($data['term-type'] == 'quotation') {
                        $destination = 'assets/files/quotation_terms/';
                        $terms_condition = $data['quote_term'];
                        $attributes = ['quote_term' => 'TERMS AND CONDITION'];
                        $rules = ['quote_term' => 'required'];
                } elseif($data['term-type'] == 'po') {
                        $destination = 'assets/files/purchase_order_terms/';
                        $terms_condition = $data['po_term'];
                        $attributes = ['po_term' => 'TERMS AND CONDITION'];
                        $rules = ['po_term' => 'required'];
                }
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success', 0);
                    Session::flash('message', implode(',',$validator->errors()->all()));
                }else{
                    $filename = 'terms';
                    $isExist = isExistFile($destination.''.$filename); 
                    if ($isExist['is_exist'] == true){
                        unlink($isExist['path']);
                    }
                    $result = toTxtFile($destination,$filename,'put',$terms_condition);
                    if($result['success'] == true){
                        Session::flash('success', 1);
                        Session::flash('message', 'Terms and Conditions has been updated!');
                    } else {
                        Session::flash('success', 1);
                        Session::flash('message', 'Unable to update Terms and Conditions. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-payment-request-limitations'){
                $attributes = [
                    'select-type' => 'Payment Request Type',
                    'amount-limit' => 'Amount',
                ];
                $rules = [
                    'select-type' => 'required',
                    'amount-limit' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $selectActivePaymentLimitType = PaymentLimit::where('type', '=', $data['select-type'])
                                                                ->where('date_end', '=', NULL)
                                                                ->first();
                    if(!empty($selectActivePaymentLimitType)) {
                        //update date end before add of same type
                        $selectActivePaymentLimitType->date_end = getDateNow();
                        $selectActivePaymentLimitType->updated_by = $user->id;
                        $selectActivePaymentLimitType->updated_at = getDatetimeNow();
                        $selectActivePaymentLimitType->save();
                    }

                    $insertPaymentLimitQuery = new PaymentLimit();
                    $insertPaymentLimitQuery->amount = $data['amount-limit'];
                    $insertPaymentLimitQuery->date_start = getDateNow();
                    $insertPaymentLimitQuery->date_end = NULL;
                    $insertPaymentLimitQuery->type = $data['select-type'];
                    $insertPaymentLimitQuery->created_by = $user->id;
                    $insertPaymentLimitQuery->updated_by = $user->id;
                    $insertPaymentLimitQuery->created_at = getDatetimeNow();
                    $insertPaymentLimitQuery->updated_at = getDatetimeNow();
                    if($insertPaymentLimitQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Payment Request Limitation Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add payment request limitation. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-payment-request-limitations'){
                $id = encryptor('decrypt', $data['payment-limit-id']);
                $attributes = [
                    'amount-limit-update' => 'Amount',
                ];
                $rules = [
                    'amount-limit-update' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updatePaymentLimitQuery = PaymentLimit::where('id', '=', $id)->first();
                    $updatePaymentLimitQuery->amount = $data['amount-limit-update'];
                    $updatePaymentLimitQuery->updated_by = $user->id;
                    $updatePaymentLimitQuery->updated_at = getDatetimeNow();
                    if($updatePaymentLimitQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update payment request limitation. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'add-job-request-types'){
                $attributes = [
                    'job-request-type-name' => 'Job Request Type',
                ];
                $rules = [
                    'job-request-type-name' => 'required|unique:job_request_types,name,NULL,id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $insertJobRequestTypeQuery = new JobRequestType();
                    $insertJobRequestTypeQuery->name = trim($data['job-request-type-name']);
                    $insertJobRequestTypeQuery->created_by = $user->id;
                    $insertJobRequestTypeQuery->updated_by = $user->id;
                    $insertJobRequestTypeQuery->created_at = getDatetimeNow();
                    $insertJobRequestTypeQuery->updated_at = getDatetimeNow();
                    if($insertJobRequestTypeQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Job Request Type Added');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to add job request type. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-job-request-types'){
                $id = encryptor('decrypt', $data['job-request-type-id']);
                $attributes = [
                    'job-request-type-name-update' => 'Job Request Type',
                ];
                $rules = [
                    'job-request-type-name-update' => 'required|unique:job_request_types,name,'.$id.',id|max:100',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    Session::flash('success',0);
                    Session::flash('message',implode(',',$validator->errors()->all()));
                }else{
                    $updateJobRequestTypeQuery = JobRequestType::where('id', '=', $id)->first();
                    $updateJobRequestTypeQuery->name = trim($data['job-request-type-name-update']);
                    $updateJobRequestTypeQuery->updated_by = $user->id;
                    $updateJobRequestTypeQuery->updated_at = getDatetimeNow();
                    if($updateJobRequestTypeQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Successfully Updated!');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update job request type. Please try again');
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
