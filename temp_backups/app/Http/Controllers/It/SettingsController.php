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
use Yajra\DataTables\Facades\DataTables;
use Crypt;
class SettingsController extends Controller
{
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
                                    <input name="'.$swatch_id.'" value="'.$swatch->swatch_name.'" readonly type="hidden"/>
                                    <img src="'.$path.'" alt="'.$swatch->swatch_name.'"  title="'.$swatch->swatch_name.'" class="img-fluid mt-0"/>
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
        //for dynamic selection of position and dept.
        $salesDept = Department::select('id', 'name', 'code')
                                ->where('name', '=', 'Sales')
                                ->orWhere('code', '=', 'SLS')->first();
        $salesManagerPos = Position::select('id', 'name')
                                ->where([['name', '=', 'MANAGER'], 
                                        ['department_id', '=', $salesDept->id],
                                        ['department_code', '=', $salesDept->code]])->first();
        $selectTeamManagers = [];
        if($salesDept) {
            if($salesManagerPos) {
                $selectTeamManagers = Employee::select('id', 'first_name', 'last_name')
                                            ->where([['position_id', '=', $salesManagerPos->id], 
                                                    ['department_id', '=', $salesDept->id]])->get();
            }
        }
        return view('it-department.settings.teams')
                    ->with('admin_menu','SETTINGS')
                    ->with('admin_sub_menu','TEAMS')
                    ->with('teams', $selectQuery)
                    ->with('team_managers', $selectTeamManagers);
    }

    function teamContent(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $resultHtml = '';

        if(isset($data['id']) && !empty($data['id'])) {
            $id = encryptor('decrypt', $data['id']);
            $team = Team::where('id', '=', $id)->first();
            //for dynamic selection of position and dept.
            $salesDept = Department::select('id', 'name', 'code')
                                    ->where('name', '=', 'Sales')
                                    ->orWhere('code', '=', 'SLS')
                                    ->first();
            $salesManagerPos = Position::select('id', 'name')
                                    ->where([['name', '=', 'MANAGER'], 
                                            ['department_id', '=', $salesDept->id],
                                            ['department_code', '=', $salesDept->code]])
                                    ->first();
            $selectTeamManagers = Employee::select('id', 'first_name', 'last_name')
                                    ->where([['position_id', '=', $salesManagerPos->id], 
                                            ['department_id', '=', $salesDept->id]])
                                    ->get();

            if($team->branch == 'QUEZON-CITY') {
                $teamBranchOpt = '<option value="QUEZON-CITY" selected>Quezon City</option>
                            <option value="MAKATI">Makati</option>';
            } elseif($team->branch == 'MAKATI') {
                $teamBranchOpt = '<option value="QUEZON-CITY">Quezon City</option>
                            <option value="MAKATI" selected>Makati</option>';
            } else {
                $teamBranchOpt = '<option value="QUEZON-CITY">Quezon City</option>
                            <option value="MAKATI">Makati</option>';
            }

            $select_team_manager = '';
            foreach($selectTeamManagers as $manager) {
                $current_team_manager = '';
                if($manager->id == $team->team_manager_id) {
                    $current_team_manager = 'selected';
                }
                $enc_manager_id = encryptor('encrypt', $manager->id);
                $select_team_manager .= '<option value="'.$enc_manager_id.'" '.$current_team_manager.'>'.$manager->first_name.' '.$manager->last_name.'</option>';
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
                            <option value=""></option>
                            "'.$teamBranchOpt.'"
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Telephone :</label>
                        <input type="text" class="form-control" required name="team-telephone-update" id="team-telephone-update" value="'.$team->telephone.'">
                    </div>
                    <div class="form-group">
                        <label>Team Manager :</label>
                        <select class="form-control" id="select-team-manager-update" required name="select-team-manager-update">
                            <option value=""></option>
                            '.$select_team_manager.'
                        </select>
                        <input type="hidden" id="team-manager-name-update" name="team-manager-name-update" value="'.$team->team_manager.'">
                    </div>
                    <input type="hidden" class="form-control" required name="team-id" value="'.$data['id'].'">
                    <script>
                        $("#select-branch-update").select2({ 
                            placeholder: "Select Branch",
                            allowClear: true,
                            width:"100%",
                            dropdownParent: $("#update-teams-modal")
                        });
                        $("#select-team-manager-update").select2({ 
                            placeholder: "Select Team Manager",
                            allowClear: true,
                            width:"100%",
                            dropdownParent: $("#update-teams-modal")
                        });

                        $("#select-team-manager-update").on("change", function() {
                            var manager = $("#select-team-manager-update option:selected").text();
                            $("#team-manager-name-update").val($.trim(manager));
                        });
                    </script>
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
                    <script>
                        $("#select-region-update").select2({
                        placeholder: "Select Region",
                        allowClear: true
                    });

                    $("#select-province-update").select2({
                        placeholder: "Select Province",
                        allowClear: true
                    });

                    $("#select-city-update").select2({
                        placeholder: "Select City",
                        allowClear: true
                    });

                    $("#select-region-update").on("change", function() {
                        formData = new FormData();
                        formData.append("id", $(this).val());
                        $.ajax({
                            type: "POST",
                            url: "'.route("supplier-functions", ["id" => "fetch-provinces"]).'",
                            data: formData,
                            CrossDomain:true,
                            contentType: !1,
                            processData: !1,
                            success: function(data) {
                                $("#select-province-update").empty().append(data).trigger("change");
                            },
                            error: function(textStatus){
                                console.log(textStatus);
                            }
                        });
                    });

                    $("#select-province-update").on("change", function() {
                        formData = new FormData();
                        formData.append("id", $(this).val());
                        $.ajax({
                            type: "POST",
                            url: "'.route("supplier-functions", ["id" => "fetch-cities"]).'",
                            data: formData,
                            CrossDomain:true,
                            contentType: !1,
                            processData: !1,
                            success: function(data) {
                                $("#select-city-update").empty().append(data);
                            },
                            error: function(textStatus){
                                console.log(textStatus);
                            }
                        });
                    });
                    </script>
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
                                        'name' => $swatch_name,
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
                    'team-telephone' => 'Telephone'
                ];
                $rules = [
                    'team-name' => 'required|unique:teams,name,NULL,id|max:50',
                    'display-name' => 'required|max:100',
                    'select-branch' => 'required',
                    'team-telephone' => 'required'
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
                    $insertTeamQuery->status = 'ACTIVE';
                    $insertTeamQuery->telephone = $data['team-telephone'];
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
                $sales_manager_id = encryptor('decrypt', $data['select-team-manager-update']);

                $attributes = [
                    'team-name-update' => 'Team Name',
                    'display-name-update' => 'Display Name',
                    'select-branch-update' => 'Branch',
                    'team-telephone-update' => 'Telephone',
                    'select-team-manager-update' => 'Team Manager',
                ];
                $rules = [
                    'team-name-update' => 'required|unique:teams,name,'.$id.',id|max:50',
                    'display-name-update' => 'required|max:100',
                    'select-branch-update' => 'required',
                    'team-telephone-update' => 'required',
                    'select-team-manager-update' => 'required',
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
                    $updateTeamQuery->team_manager = $data['team-manager-name-update'];
                    $updateTeamQuery->team_manager_id = $sales_manager_id;
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
            else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
