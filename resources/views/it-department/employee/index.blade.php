@extends ('layouts.it-department.app')
@section ('title')
    Add Employee
@endsection
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/summernote/summernote.css') }}"/>
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet">
	<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
    <style>
        .bootstrap-tagsinput {
            width: 100% !important;
        }
    </style>
@php 
    $generatedSavedPoint = '';
    if(Session::has('savedpoint')){
        $generatedSavedPoint = Session::get('savedpoint');
    }else{
        $generatedSavedPoint = encryptor('encrypt',$user->id);
        Session::put('savedpoint',$generatedSavedPoint);
    }

    $destination = 'assets/files/users/savepoint/'.$generatedSavedPoint.'/';
    $personal_info_filename = 'personal-information';
    $personal_info = toTxtFile($destination,$personal_info_filename,'get');
    $background_filename = 'background-information';
    $background_info = toTxtFile($destination,$background_filename,'get');
    $employee_filename = 'employee-information';
    $employee_info = toTxtFile($destination,$employee_filename,'get');
    $firstname = '';
    $middle = '';
    $surname = '';
    $prefix = '';
    $birthdate = '';
    $contact_number = '';
    $gender = '';
    $civil_Status = '';
    $address = '';
    $sss = '';
    $philhealth = '';
    $pagibig = '';
    $tin = '';
    $email = '';
    $personal_tab = 'active';
    $background_tab = 'disabled';
    $employee_tab = 'disabled';
    $picture_tab = 'disabled';

    $access_code = '';
    $section = '';
    $date_regularization = '';
    $date_regularized = '';
    $date_resigned = '';
    $separation_pay = '';
    $basic_salary = '';
    $allowance = '';
    $gross_salary = '';
    $employee_id = '';
    $position_id = '';
    $department_id = '';
    $date_hired = '';
    $employee_status = '';
    $tax_exempt = '3';
    $background_mode = '';
    $mode_alert = 'no';
    if($personal_info['success'] === true){
        $datas = $personal_info['data'];
        $datas = json_decode($datas);
        $firstname = $datas->firstname;
        $middle = $datas->middlename;
        $surname = $datas->surname;
        $prefix = $datas->prefix;
        $birthdate = $datas->birthdate;
        $contact_number = $datas->contact_number;
        $gender = $datas->gender;
        $civil_Status = $datas->civil_status;
        $address = $datas->address;
        $sss = $datas->sss;
        $philhealth = $datas->philhealth;
        $pagibig = $datas->pagibig_number;
        $tin = $datas->tin_number;
        $email = $datas->email;
        $personal_tab = 'disabled';
        $background_tab = 'active';
        $employee_tab = 'disabled';
        $picture_tab = 'disabled';
        $mode_alert = 'yes';
    }
    if($background_info['success'] === true){
        $data_background = $background_info['data'];
        $data_background = json_decode($data_background);
        $background_mode = 'submited';
        $personal_tab = 'disabled';
        $background_tab = 'disabled';
        $picture_tab = 'disabled';
        $employee_tab = 'active';
    }
    if($employee_info['success'] === true){
        $employee_data = $employee_info['data'];
        $employee_data = json_decode($employee_data);

        $access_code = $employee_data->access_code;
        $section = $employee_data->section;
        $date_regularization = $employee_data->date_regularization;
        $date_regularized = $employee_data->date_regulized;
        $date_resigned = $employee_data->date_resigned;
        $separation_pay = $employee_data->separation_pay;
        $basic_salary = $employee_data->basic_salary;
        $allowance = $employee_data->allowance;
        $gross_salary = $employee_data->gross_salary;
        $employee_id = $employee_data->employee_id;
        $position_id = $employee_data->position_id;
        $department_id = $employee_data->department_id;
        $date_hired = $employee_data->date_hired;
        $employee_status = $employee_data->employee_status;
        $tax_exempt = $employee_data->tax_exempt;
        $personal_tab = 'disabled';
        $background_tab = 'disabled';
        $employee_tab = 'disabled';
        $picture_tab = 'active';
    }
@endphp
@section('breadcrumbs')
<li class="breadcrumb-item">Employee</li>
<li class="breadcrumb-item active">Add Employee</li>
@endsection
@section('content')
<div class="mb-3">
    <div class="d-flex flex-start w-100">
        <div class="mr-2 hidden-md-down">
            <span class="icon-stack icon-stack-lg">
                <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
                <i class="ni ni-blog-read icon-stack-1x opacity-100 color-white"></i>
            </span>
        </div>
        <div class="d-flex flex-fill">
            <div class="flex-fill">
                <span class="h5 mt-0">ADD EMPLOYEE</span>
                <br>
                <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore...</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{$personal_tab}}" id="personal-info-div" data-toggle="tab" href="#personal-information"><b>Personal Information</b></a>
            </li>
            <li class="nav-item nav-justified">
                <a class="nav-link {{$background_tab}}" id="background-info-div"  data-toggle="tab" href="#background"><b>Backgrounds</b></a>
            </li>
            <li class="nav-item nav-justified">
                <a class="nav-link {{$employee_tab}}" id="employee-info-div"  data-toggle="tab" href="#employee-information"><b>Employee Information</b></a>
            </li>
            <li class="nav-item nav-justified">
                <a class="nav-link {{$picture_tab}}" id="picture-info-div"  data-toggle="tab" href="#picture-information"><b>Upload Picture</b></a>
            </li>
        </ul>
    </div>
	<div class="col-md-12">
		<div class="tab-content">
        <div class="tab-pane fade show {{$personal_tab}}" id="personal-information" role="tabpanel" aria-labelledby="Personal Info Div">
            <div class="col-lg-12">
                <div id="panel-4" class="panel">
                    <form method="post" id="personal-information-form"  action="{{ route('employee-functions', ['id' => 'personal-information']) }}">
                        @csrf()
                        <input type="hidden" name="savedpoint" value="{{ $generatedSavedPoint }}"/>
                        <div class="row p-5">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label>First Name :</label>
                                    <input type="text" value="{{$firstname}}" class="form-control" name="firstname" placeholder="First Name" required/>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name :</label>
                                    <input type="text" value="{{$middle}}" class="form-control" name="middlename" placeholder="Middle Name"/>
                                </div>
                                <div class="form-group">
                                    <label>Surname :</label>
                                    <input type="text" value="{{$surname}}" class="form-control" name="surname" placeholder="Surname" required/>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>Prefix :</label>
                                            <input type="text" value="{{$prefix}}" class="form-control" name="prefix" placeholder="Ex. Jr, Sr" maxlength="3"/>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <label>Birthdate :</label>
                                                <input type="date" value="{{$birthdate}}" class="form-control" name="birthdate" placeholder="Birthdate" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number :</label>
                                    <input type="text" value="{{$contact_number}}" class="form-control bootstrap-tagsinput" name="contact_number" placeholder="Contact Number" required/>
                                </div>
                                <div class="form-group">
                                    <label>Email Address :</label>
                                    <input type="email" value="{{$email}}" class="form-control" name="email-address" placeholder="Email Address" required/>
                                </div>
                                <div class="row">
                                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                        <label>Gender : </label>
                                        @php 
                                            $male = '';
                                            $female = '';
                                            if($gender=='MALE'){
                                                $male = 'checked';
                                            }
                                            if($gender=='FEMALE'){
                                                $female = 'checked';
                                            }

                                        @endphp
                                        <div class="form-group">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" {{$male}} id="defaultInline1Radio" value="MALE" name="gender">
                                                <label class="custom-control-label" for="defaultInline1Radio">Male</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" {{$female}} id="defaultInline2Radio" value="FEMALE" name="gender">
                                                <label class="custom-control-label" for="defaultInline2Radio">Female</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label>Civil Status : </label>
                                            <select class="form-control" name="civil_status" required>
                                                <option value=""></option>
                                                @foreach($civil_statuses as $index=>$civil_status) 
                                                    @php 
                                                    $mode_civil = '';
                                                    if($index==$civil_Status){
                                                        $mode_civil = 'selected';
                                                    }
                                                     @endphp
                                                <option value="{{$index}}" {{$mode_civil}}>{{$civil_status}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding-top:22px;">
                                <div class="form-group">
                                    <label>Address : </label>
                                    <textarea class="form-control" rows="5" name="address" required>{{$address}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>SSS Number : </label>
                                    <input type="text" class="form-control" data-inputmask="'mask': '99-9999999-9'" name="sss" value="{{$sss}}" placeholder="SSS number"/>
                                </div>
                                <div class="form-group">
                                    <label>Philhealth  Number: </label>
                                    <input type="text" class="form-control" data-inputmask="'mask': '99-999999999-9'" value="{{$philhealth}}" name="philhealth" placeholder="Philhealth  Number"/>
                                </div>
                                <div class="form-group">
                                    <label>Pagibig Number : </label>
                                    <input type="text" class="form-control" data-inputmask="'mask': '9999-9999-9999'" value="{{$pagibig}}" name="pagibig_number" placeholder="Pagibig Number"/>
                                </div>
                                <div class="form-group">
                                    <label>TIN Number : </label>
                                    <input type="text" class="form-control" data-inputmask="'mask': '999-999-999'" value="{{$tin}}" name="tin_number" placeholder="TIN Number"/>
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-lg-12  text-center">
                                <button class="btn btn-success waves-effect waves-themed text-white" id="reset-details" {{$personal_tab}}>RESET DETAILS</button>
                                <button type="submit" class="btn btn-warning waves-effect waves-themed" form="personal-information-form">Next <i class="fas fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </form>
                    
                    
                </div>
            </div>
        </div>
        <div class="tab-pane fade show {{$background_tab}}" id="background" role="tabpanel" aria-labelledby="Background Div">
            <div id="panel-4" class="panel">
                <form method="post" id="background-information-form"  action="{{ route('employee-functions', ['id' => 'background-information']) }}">
                        @csrf()
                <div class="row p-5">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:22px;">
                        <div class="form-group">
                            <h4>Work Experience :
                            @if($background_mode=='submited')
                                <a class="btn btn-success text-white btn-sm" id="add-work"><span class="fa fa-plus text-white"></span> Add Work</a>
                            @endif
                            </h4>
                        </div>
                        <input type="hidden" name="savedpoint" value="{{ $generatedSavedPoint }}"/>
                        <table class="table table-bordered">
                            <tbody id="work-content">
                                @if($background_mode=='submited')
                                    @php 
                                        $count=0;
                                    @endphp 
                                    @for($i=0;$i<count($data_background->WORK->name);$i++)
                                    @php 
                                        $count++;
                                    @endphp 
                                        <tr id="added-work-content{{$count}}">
                                            <td align="center"><a class="btn btn-danger remove-work text-white" data-count="{{$count}}"><span class="text-white fa fa-times"></span></a></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->WORK->name[$i]}}" name="company_name[]" placeholder="Company Name" /></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->WORK->position[$i]}}" name="company_position[]" placeholder="Position"  /></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->WORK->yrs[$i]}}" name="company_years[]" placeholder="Years/Months Acquainted"   maxlength="7" /></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->WORK->address[$i]}}" name="company_address[]" placeholder="Address" /></td>
                                        </tr>
                                    @endfor
                                @else 
                                    <tr>
                                        <td width="18%"><a class="btn btn-success text-white" id="add-work"><span class="fa fa-plus text-white"></span> Add Work</a></td>
                                        <td><input type="text" class="form-control" required name="company_name[]" placeholder="Company Name" /></td>
                                        <td><input type="text" class="form-control" required name="company_position[]" placeholder="Position"  /></td>
                                        <td><input type="text" class="form-control" required name="company_years[]" placeholder="Years/Months Acquainted"   maxlength="7" /></td>
                                        <td><input type="text" class="form-control" required name="company_address[]" placeholder="Address" /></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:22px;"> 
                        <div class="form-group">
                            <h4>Educational Bakground :
                                @if($background_mode=='submited')
                                    <a class="btn btn-success text-white btn-sm" id="add-school"><span class="fa fa-plus text-white"></span> Add Education</a>
                                @endif
                            </h4>
                            <table class="table table-bordered">
                                <tbody id="school-content">
                                    @if($background_mode=='submited')
                                        @php 
                                            $count_school=0;
                                        @endphp 
                                        @for($a=0;$a<count($data_background->EDUCATION->name);$a++)
                                        @php 
                                            $count_school++;
                                        @endphp 
                                        <tr id="added-work-content{{$count_school}}">
                                            <td align="center"><a class="btn btn-danger remove-school text-white" data-count="{{$count_school}}"><span class="text-white fa fa-times"></span></a></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->EDUCATION->name[$a]}}" name="school_name[]" placeholder="School Name" /></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->EDUCATION->education[$a]}}" name="school_course[]" placeholder="Course / Educational Attainment"  /></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->EDUCATION->yrs[$a]}}" name="school_years[]" placeholder="Years Acquainted"   maxlength="7" /></td>
                                        </tr>
                                        @endfor
                                    @else 
                                        <tr>
                                            <td><a class="btn btn-success text-white" id="add-school"><span class="fa fa-plus text-white"></span> Add Education</a></td>
                                            <td><input type="text" class="form-control" required name="school_name[]" placeholder="School Name" /></td>
                                            <td><input type="text" class="form-control" required name="school_course[]" placeholder="Course / Educational Attainment"  /></td>
                                            <td><input type="text" class="form-control" required name="school_years[]" placeholder="Years Acquainted"   maxlength="7" /></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:22px;"> 
                        <div class="form-group">
                            <h4>Family Bakground : 
                                @if($background_mode=='submited')
                                    <a class="btn btn-success text-white btn-sm" id="add-family"><span class="fa fa-plus text-white"></span> Add Family</a>
                                @endif
                            </h4>
                            <table class="table table-bordered">
                                <tbody id="family-content">
                                    @if($background_mode=='submited')
                                        @php 
                                            $count_family=0;
                                        @endphp 
                                        @for($b=0;$b<count($data_background->FAMILY->name);$b++)
                                        @php 
                                            $count_family++;
                                        @endphp 
                                        <tr id="added-family-content{{$count_family}}">
                                            <td align="center"><a  class="btn btn-danger remove-family text-white" data-count="{{$count_family}}"><span class="text-white fa fa-times"></span></a></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->FAMILY->name[$b]}}" name="family_name[]" placeholder="Family Name" /></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->FAMILY->relationship[$b]}}" name="family_relationship[]" placeholder="Relationship"  /></td>
                                            <td><input type="text" class="form-control" required value="{{$data_background->FAMILY->contact[$b]}}" name="family_contact[]" placeholder="Contact Number" /></td>
                                        </tr>
                                        @endfor
                                    @else 
                                        <tr>
                                            <td><a class="btn btn-success text-white" id="add-family"><span class="fa fa-plus text-white"></span> Add Family</a></td>
                                            <td><input type="text" class="form-control" required name="family_name[]" placeholder="Family Name" /></td>
                                            <td><input type="text" class="form-control" required name="family_relationship[]" placeholder="Relationship"  /></td>
                                            <td><input type="text" class="form-control" required name="family_contact[]" placeholder="Contact Number" /></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row p-5">
                        <div class="col-lg-12  text-center">
                            <a class="btn btn-success waves-effect waves-themed text-white" id="to-personal-info">Previous</a>
                            <button type="submit" class="btn btn-warning waves-effect waves-themed" form="background-information-form">Next <i class="fas fa-arrow-right"></i></button>
                        </div>
                </div>
                </form>
            </div> <!--- --->
        </div>
        <div class="tab-pane fade show {{$employee_tab}}" id="employee-information" role="tabpanel" aria-labelledby="Emplyoee Info Div">
            <div id="panel-4" class="panel">
                <form method="post" id="employee-information-form"  action="{{ route('employee-functions', ['id' => 'employee-information']) }}">
                @csrf()
                <div class="row p-5">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding-top:22px;">
                                <div class="form-group">
                                    <label>Access Code : </label>
                                    <input type="text" class="form-control" value="{{$access_code}}" name="access_code" placeholder="Access Code" maxlength="5"/>
                                </div>
                                <div class="form-group">
                                    <label>Employee ID : </label>
                                    <input type="number" class="form-control" value="{{$employee_id}}" name="employee_id" placeholder="Employee ID" required maxlength="6"/>
                                </div>
                                <div class="form-group">
                                    <label>Department : </label>
                                    <select class="form-control" name="department_id" required>
                                        <option value=""></option>
                                        @foreach($departments as $department) 
                                            @php 
                                                $mode_department = '';
                                                if($department->id==$department_id){
                                                    $mode_department = 'selected';
                                                }
                                            @endphp 
                                            <option value="{{$department->id}}" {{$mode_department}}>{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Position : </label>
                                    <select class="form-control" name="position_id" required>
                                        <option value=""></option>
                                        @foreach($positions as $position) 
                                            @php 
                                                $mode_position = '';
                                                if($position->id==$position_id){
                                                    $mode_position = 'selected';
                                                }
                                            @endphp 
                                            <option value="{{$position->id}}" {{$mode_position}}>{{$position->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Section : </label>
                                    <input type="text" class="form-control" value="{{$section}}" name="section" placeholder="Section"/>
                                </div>
                                <div class="form-group">
                                    <label>Tax Exemption : </label>
                                    @php 
                                        $yes_tax = '';
                                        $no_tax= '';

                                        if($tax_exempt==1){
                                            $yes_tax = 'checked';
                                        }
                                        if($tax_exempt==0){
                                            $no_tax = 'checked';
                                        }

                                    @endphp
                                    <div class="form-group">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="tax_exempt1" {{$yes_tax}} value="1" name="tax_exempt">
                                            <label class="custom-control-label" for="tax_exempt1">Yes</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="tax_exempt2" {{$no_tax}} value="0" name="tax_exempt">
                                            <label class="custom-control-label" for="tax_exempt2">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding-top:22px;">
                                <div class="form-group">
                                    <label>Date Hired : </label>
                                    <input type="date" class="form-control" value="{{$date_hired}}" name="date_hired" placeholder="Date Hired" required/>
                                </div>
                                <div class="form-group">
                                    <label>Employee Status : </label>
                                    <select class="form-control" name="employee-status" required>
                                        <option value=""></option>
                                        @php 
                                            $probi = '';
                                            $regular = '';
                                            $casual = '';
                                            $separated = '';
                                            if($employee_status=='PROBATIONARY'){
                                                $probi = 'selected';
                                            }
                                            if($employee_status=='REGULAR'){
                                                $regular = 'selected';
                                            }
                                            if($employee_status=='CASUAL'){
                                                $casual = 'selected';
                                            }
                                            if($employee_status=='SEPARATED'){
                                                $separated = 'selected';
                                            }
                                        @endphp 
                                        <option value="PROBATIONARY" {{$probi}}>Probationary</option>
                                        <option value="REGULAR" {{$regular}}>Regular</option>
                                        <option value="CASUAL" {{$casual}}>Casual</option>
                                        <option value="SEPARATED" {{$separated}}>Separated</option>
                                    </select>
                                </div>
                                <div id="employee-status-content">
                                    @if($employee_status=='REGULAR')
                                        <div class="form-group">
                                            <label>Date of Regularization</label>
                                            <input type="date" class="form-control" value="{{$date_regularization}}" name="date-regularization" required/>
                                        </div>
                                        <div class="form-group">
                                            <label>Date Regulized</label>
                                            <input type="date" class="form-control" value="{{$date_regularized}}" name="date-regulized" required/>
                                        </div>
                                    @endif   
                                    @if($employee_status=='SEPARATED')
                                        <div class="form-group">
                                            <label>Date Resigned</label>
                                            <input type="date" class="form-control" value="{{$date_resigned}}" name="date-resigned" required/>
                                        </div>
                                        <div class="form-group">
                                            <label>Separation Pay</label>
                                            @php 
                                                $mode_separation_yes = '';
                                                $mode_separation_no = '';
                                                if($separation_pay==1){
                                                    $mode_separation_yes = 'checked';
                                                }
                                                if($separation_pay==0){
                                                    $mode_separation_no = 'checked';
                                                }
                                            @endphp
                                            <div class="form-group" style="padding-bottom: 17px;">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" class="custom-control-input" id="separation1" {{$mode_separation_yes}} value="1" name="separation-pay">
                                                    <label class="custom-control-label" for="separation1">Yes</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" class="custom-control-input" id="separation2" {{$mode_separation_no}} value="0" name="separation-pay">
                                                    <label class="custom-control-label" for="separation2">No</label>
                                                </div>
                                            </div>
                                        </div> 
                                    @endif      
                                </div>
                                <div class="form-group">
                                    <label>Basic Salary : </label>
                                    <input type="number" class="form-control" value="{{$basic_salary}}" name="basic_salary" placeholder="Basic Salary"/>
                                </div>
                                <div class="form-group">
                                    <label>Allowance : </label>
                                    <input type="number" class="form-control" value="{{$allowance}}" name="allowance" step="any" placeholder="Allowance"/>
                                </div>
                                <div class="form-group">
                                    <label>Gross Salary : </label>
                                    <input type="number" class="form-control" value="{{$gross_salary}}" name="gross_salary"  step="any" placeholder="Gross Salary"/>
                                </div>
                                
                            </div>
                           
                </div>
                <div class="row p-5">
                        <div class="col-lg-12  text-center">
                            <a class="btn btn-success waves-effect waves-themed text-white" id="to-background-info">Previous</a>
                            <button type="submit" class="btn btn-warning waves-effect waves-themed" id="employeeBtn" form="employee-information-form">Next <i class="fas fa-arrow-right"></i></button>
                        </div>
                </div>
                </form>
            </div>
        </div>
        <div class="tab-pane fade show {{$picture_tab}}" id="picture-information" role="tabpanel" aria-labelledby="Picture Div">
            <div id="panel-4" class="panel">
                <form method="post" id="picture-information-form"  action="{{ route('employee-functions', ['id' => 'save-information']) }}" enctype="multipart/form-data">
                @csrf()
                    <div class="row p-5">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <h3><b>Profile Picture</b></h3>
                            <hr>
                            <div class="form-group" align="center">
                                <img src="//via.placeholder.com/400X400" style="width:400px;height:400px;" class="rounded" id="image_preview"><br>
                            </div>
                            <div class="form-group" align="center">
                                <input type="file" class="form-control" name="profile_picture" required id="select_image"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <h3><b>Signature</b></h3>
                            <hr>
                            <div class="form-group" align="center">
                                <img src="//via.placeholder.com/400X200" style="width:400px;height:200px;" class="rounded" id="image_preview_signature"><br>
                            </div>
                            <div class="form-group" align="center">
                                <input type="file" class="form-control" name="signature_image" required id="select_image_signature"/>
                            </div>
                        </div>
                    </div>
                    <div class="row p-5">
                        <div class="col-lg-12  text-center">
                            <a class="btn btn-success waves-effect waves-themed text-white" id="to-employee-info">Previous</a>
                            <button  onclick='confirmData()' id="btn-save-employee" class="btn btn-danger waves-effect waves-themed">Save </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
	</div>
</div>

@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/inputmask/inputmask.bundle.js') }}"></script>

<script>
 function confirmData(){
        $('#btn-save-employee').prop('disabled', true);
            var profile = $('input[name="profile_picture"]').val();
            var signature = $('input[name="signature_image"]').val();
    if($.trim(profile)){
        if($.trim(signature)){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#008000b0',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Save'
            }).then((result) => {
                if (result.value) {
                    $('#picture-information-form').submit();
                }else{
                    $('#btn-save-employee').prop('disabled', false);
                }
            });
        }else{
            Swal.fire(
                'No Signature',
                'Signature is Required !!!',
                'info'
            );
        }
    }else{
        Swal.fire(
                'No Profile Picture',
                'Profile Picture is Required !!!',
                'info'
            );
    }
            
           
    }
   $(document).ready(function(index){
       
        $('.bootstrap-tagsinput ').tagsinput({
            tagClass: ' btn btn-info btn-smal btn-tags',
            maxTags: 5
        });
		$('select[name="civil_status"]').select2({
			placeholder: "Select Civil Status",
			allowClear: true,
			width:"100%"
		});
        $('select[name="employee-status"]').select2({
			placeholder: "Select Employee Status",
			allowClear: true,
			width:"100%"
		});
        $('select[name="position_id"]').select2({
			placeholder: "Select Position",
			allowClear: true,
			width:"100%"
		});
        $('select[name="department_id"]').select2({
			placeholder: "Select Department",
			allowClear: true,
			width:"100%"
		});
        $('input[name="tin_number"]').inputmask();
        $('input[name="sss"]').inputmask();
        $('input[name="philhealth"]').inputmask();
        $('input[name="pagibig_number"]').inputmask();
		
		$(document).on('click','#reset-details',function(){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset it!'
            }).then((result) => {
                if (result.value) {
                    location.reload();
                }
            });
		});

        $(document).on('click','#add-work',function(){
            var count = $('.remove-work').length;
            $('#work-content').append('<tr id="added-work-content'+count+'">'+
            '<td align="center"><a class="btn btn-danger remove-work text-white" data-count="'+count+'"><span class="text-white fa fa-times"></span></a></td>'+
            '<td><input type="text" class="form-control" name="company_name[]" placeholder="Company Name" required /></td>'+
            '<td><input type="text" class="form-control" name="company_position[]" placeholder="Position"  required/></td>'+
            '<td><input type="text" class="form-control" name="company_years[]" placeholder="Years/Months Acquainted"  required  maxlength="7" /></td>'+
            '<td><input type="text" class="form-control" name="company_address[]" placeholder="Address" required/></td>'
            +'</tr>');
        });
        $(document).on('click','.remove-work',function(){
            var count = $(this).data('count');
            $('#added-work-content'+count).remove();
        });
        $(document).on('click','#add-school',function(){
            var count = $('.remove-school').length;
            $('#school-content').append('<tr id="added-school-content'+count+'">'+
            '<td align="center"><a class="btn btn-danger remove-school text-white" data-count="'+count+'"><span class="text-white fa fa-times"></span></a></td>'+
            '<td><input type="text" class="form-control" name="school_name[]" placeholder="School Name" required/></td>'+
            '<td><input type="text" class="form-control" name="school_course[]" required placeholder="Course / Educational Attainment"  /></td>'+
            '<td><input type="text" class="form-control" name="school_years[]" required placeholder="Years Acquainted"   maxlength="7" /></td>'+
            +'</tr>');
        });
        $(document).on('click','.remove-school',function(){
            var count = $(this).data('count');
            $('#added-school-content'+count).remove();
        });
        $(document).on('click','#add-family',function(){
            var count = $('.remove-family').length;
            $('#family-content').append('<tr id="added-family-content'+count+'">'+
            '<td align="center"><a  class="btn btn-danger remove-family text-white" data-count="'+count+'"><span class="text-white fa fa-times"></span></a></td>'+
            '<td><input type="text" class="form-control" name="family_name[]" required placeholder="Family Name" /></td>'+
            '<td><input type="text" class="form-control" name="family_relationship[]" required placeholder="Relationship"  /></td>'+
            '<td><input type="text" class="form-control" name="family_contact[]" required placeholder="Contact Number" /></td>'+
            +'</tr>');
        });
        $(document).on('click','.remove-family',function(){
            var count = $(this).data('count');
            $('#added-family-content'+count).remove();
        });
        $(document).on('click','#to-personal-info',function(){
            $('#personal-info-div').removeClass('disabled');
            $('#background-info-div').removeClass('active');
            $('#background-info-div').addClass('disabled');
            $('#personal-info-div').addClass('active');

            $('#personal-information').addClass('active');
            $('#personal-information').removeClass('disabled');
            $('#background').removeClass('active');
            $('#background').addClass('disabled');
        });

        $(document).on('click','#to-background-info',function(){
            $('#background-info-div').removeClass('disabled');
            $('#background-info-div').addClass('active');
            $('#background').removeClass('disabled');
            $('#background').addClass('active');

            $('#employee-info-div').removeClass('active');
            $('#employee-info-div').addClass('disabled');
            $('#employee-information').removeClass('active');
            $('#employee-information').addClass('disabled');
        });

        $(document).on('click','#to-employee-info',function(){
            $('#employee-info-div').removeClass('disabled');
            $('#employee-info-div').addClass('active');
            $('#employee-information').removeClass('disabled');
            $('#employee-information').addClass('active');

            $('#picture-info-div').removeClass('active');
            $('#picture-info-div').addClass('disabled');
            $('#picture-information').removeClass('active');
            $('#picture-information').addClass('disabled');
        });

        $(document).on('change','select[name="department_id"]',function(){
            var id = $(this).val();
            $('select[name="position_id"]').html('<option></option>');
            $.post("{{ route('employee-functions', ['id' => 'get-position']) }}",
            {
                id: id,
            },
            function(data){
                $('select[name="position_id"]').html(data);
            });
        });

        $(document).on('change','input[name="date-regularization"]',function(){
            var date_hired = $('input[name="date_hired"]').val();
            var temp = $(this).val();
            if(temp<date_hired){
                $(this).addClass('is-invalid');
                $('#employeeBtn').prop('disabled', true);
            }else{
                $('#employeeBtn').prop('disabled', false);
                $(this).removeClass('is-invalid');
            }
        });

        $(document).on('change','input[name="date-regulized"]',function(){
            var date_hired = $('input[name="date_hired"]').val();
            var temp = $(this).val();
            if(temp<date_hired){
                $(this).addClass('is-invalid');
                $('#employeeBtn').prop('disabled', true);
            }else{
                $('#employeeBtn').prop('disabled', false);
                $(this).removeClass('is-invalid');
            }
        });

        $(document).on('change','select[name="employee-status"]',function(){
            var status = $(this).val();
            if(status=='REGULAR'){
                $('#employee-status-content').html('<div class="form-group">'+
                    '<label>Date of Regularization</label>'+
                    '<input type="date" class="form-control" name="date-regularization" required/>'+
                    '</div><div class="form-group">'+
                    '<label>Date Regulized</label>'+
                    '<input type="date" class="form-control" name="date-regulized" required/>'+
                '</div>');
            }
            if(status=='SEPARATED'){
                $('#employee-status-content').html('<div class="form-group">'+
                    '<label>Date Resigned</label>'+
                    '<input type="date" class="form-control" name="date-resigned" required/>'+
                    '</div><div class="form-group">'+
                    '<label>Separation Pay</label>'+
                    '<div class="form-group" style="padding-bottom: 17px;">'+
                        '<div class="custom-control custom-radio custom-control-inline">'+
                            '<input type="radio" class="custom-control-input" id="separation1" value="1" name="separation-pay">'+
                            '<label class="custom-control-label" for="separation1">Yes</label>'+
                        '</div>'+
                        '<div class="custom-control custom-radio custom-control-inline">'+
                            '<input type="radio" class="custom-control-input" id="separation2" value="0" name="separation-pay">'+
                            '<label class="custom-control-label" for="separation2">No</label>'+
                        '</div>'+
                    '</div>'+
                '</div>');
            }
            if(status=='PROBATIONARY'||status=='CASUAL'){
                $('#employee-status-content').html('');
            }
        });
        
        $(document).on('change','#select_image_signature',function(){
            readURL('select_image_signature','image_preview_signature',"//via.placeholder.com/400X200");
        });

        $(document).on('change','#select_image',function(){
            readURL('select_image','image_preview',"//via.placeholder.com/400X400");
        });
   });
</script>
@endsection
