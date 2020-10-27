@extends ('layouts.hr-department.app')
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
@section('breadcrumbs')
<li class="breadcrumb-item">Employee</li>
<li class="breadcrumb-item active">Update Employee</li>
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
                <span class="h5 mt-0">UPDATE EMPLOYEE</span>
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
                <a class="nav-link active" id="personal-info-div" data-toggle="tab" href="#personal-information"><b>Personal Information</b></a>
            </li>
            <li class="nav-item nav-justified">
                <a class="nav-link" id="background-info-div"  data-toggle="tab" href="#background"><b>Backgrounds</b></a>
            </li>
            <li class="nav-item nav-justified">
                <a class="nav-link" id="employee-info-div"  data-toggle="tab" href="#employee-information"><b>Employee Information</b></a>
            </li>
            <li class="nav-item nav-justified">
                <a class="nav-link" id="picture-info-div"  data-toggle="tab" href="#picture-information"><b>Upload Picture</b></a>
            </li>
        </ul>
    </div>
	<div class="col-md-12">
		<div class="tab-content">
        <div class="tab-pane fade show active" id="personal-information" role="tabpanel" aria-labelledby="Personal Info Div">
            <div class="col-lg-12">
                <div id="panel-4" class="panel">
                    <form method="post" id="personal-information-form"  action="{{ route('hr-employee-functions', ['id' => 'update-personal-information']) }}">
                        @csrf()
                        <input type="hidden" class="form-control" value="{{encryptor('encrypt',$employee->id)}}" required name="employeeId" />
                        <div class="row p-5">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label>First Name :</label>
                                    <input type="text" class="form-control" value="{{$employee->first_name}}" name="firstname" placeholder="First Name" required/>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name :</label>
                                    <input type="text" class="form-control" value="{{$employee->middle_name}}" name="middlename" placeholder="Middle Name"/>
                                </div>
                                <div class="form-group">
                                    <label>Surname :</label>
                                    <input type="text" class="form-control" value="{{$employee->last_name}}" name="surname" placeholder="Surname" required/>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>Prefix :</label>
                                            <input type="text" class="form-control" value="{{$employee->prefix}}" name="prefix" placeholder="Ex. Jr, Sr" maxlength="3"/>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <label>Birthdate :</label>
                                                <input type="date" class="form-control" value="{{$employee->birth_date}}" name="birthdate" placeholder="Birthdate" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number :</label>
                                    <input type="text" class="form-control bootstrap-tagsinput" value="{{$employee->contact_number}}" name="contact_number" placeholder="Contact Number" required/>
                                </div>
                                <div class="form-group">
                                    <label>Email Address :</label>
                                    <input type="email" class="form-control" value="{{$employee->email}}" name="email-address" placeholder="Email Address" required/>
                                </div>
                                <div class="row">
                                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                        <label>Gender : </label>
                                        @php 
                                            $male = '';
                                            $female = '';
                                            if($employee->gender=='MALE'){
                                                $male = 'checked';
                                            }else{
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
                                                        $civil_mode = '';
                                                        if($index==$employee->civil_status){
                                                            $civil_mode = 'selected';
                                                        }
                                                   @endphp 
                                                    <option value="{{$index}}" {{$civil_mode}}>{{$civil_status}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding-top:22px;">
                                <div class="form-group">
                                    <label>Address : </label>
                                    <textarea class="form-control" rows="5" name="address" required>{{$employee->address}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>SSS Number : </label>
                                    <input type="text" class="form-control" value="{{$employee->sss}}" data-inputmask="'mask': '99-9999999-9'" name="sss" placeholder="SSS number"/>
                                </div>
                                <div class="form-group">
                                    <label>Philhealth  Number: </label>
                                    <input type="text" class="form-control" value="{{$employee->philhealth}}" data-inputmask="'mask': '99-999999999-9'" name="philhealth" placeholder="Philhealth  Number"/>
                                </div>
                                <div class="form-group">
                                    <label>Pagibig Number : </label>
                                    <input type="text" class="form-control" value="{{$employee->pagibig}}" data-inputmask="'mask': '9999-9999-9999'" name="pagibig_number" placeholder="Pagibig Number"/>
                                </div>
                                <div class="form-group">
                                    <label>TIN Number : </label>
                                    <input type="text" class="form-control" value="{{$employee->tin}}" data-inputmask="'mask': '999-999-999'" name="tin_number" placeholder="TIN Number"/>
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-lg-12  text-center">
                                <button type="submit" class="btn btn-warning waves-effect waves-themed"> <i class="ni ni-note"></i> Update Personal Information</button>
                            </div>
                        </div>
                    </form>
                    
                    
                </div>
            </div>
        </div>
        <div class="tab-pane fade show" id="background" role="tabpanel" aria-labelledby="Background Div">
            <div id="panel-4" class="panel">
                <form method="post" id="background-information-form"  action="{{ route('hr-employee-functions', ['id' => 'update-background-information']) }}">
                    @csrf()
                    <input type="hidden" class="form-control" value="{{encryptor('encrypt',$employee->id)}}" required name="employeeId" />
                <div class="row p-5">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:22px;">
                        <div class="form-group">
                            <h4>Work Experience :
                                <a class="btn btn-success text-white btn-sm" id="add-work"><span class="fa fa-plus text-white"></span> Add Work</a>
                            </h4>
                        </div>
                        <table class="table table-bordered">
                            <tbody id="work-content">
                                @foreach($employee->work as $work)
                                <tr id="added-work-content">
                                    <td align="center"></td>
                                    <td><input type="text" class="form-control" value="{{$work->name}}" required name="company_name[]" placeholder="Company Name" /></td>
                                    <td><input type="text" class="form-control" value="{{$work->position}}" required name="company_position[]" placeholder="Position"  /></td>
                                    <td><input type="text" class="form-control" value="{{$work->years_acquainted}}" required name="company_years[]" placeholder="Years/Months Acquainted"   maxlength="7" /></td>
                                    <td>
                                        <input type="text" class="form-control" value="{{$work->address}}" required name="company_address[]" placeholder="Address" />
                                        <input type="hidden" class="form-control" value="{{encryptor('encrypt',$work->id)}}" required name="company_id[]" />
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:22px;"> 
                        <div class="form-group">
                            <h4>Educational Background :
                                <a class="btn btn-success text-white btn-sm" id="add-school"><span class="fa fa-plus text-white"></span> Add Education</a>
                            </h4>
                            <table class="table table-bordered">
                                <tbody id="school-content">
                                        @foreach($employee->education as $education)
                                        <tr id="added-work-content">
                                            <td align="center"></td>
                                            <td><input type="text" class="form-control" value="{{$education->name}}" required name="school_name[]" placeholder="School Name" /></td>
                                            <td><input type="text" class="form-control" value="{{$education->position}}" required name="school_course[]" placeholder="Course / Educational Attainment"  /></td>
                                            <td>
                                               <input type="text" class="form-control" value="{{$education->years_acquainted}}" required name="school_years[]" placeholder="Years Acquainted"   maxlength="7" />
                                               <input type="hidden" class="form-control" value="{{encryptor('encrypt',$education->id)}}" required name="school_id[]" />
                                            </td>
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:22px;"> 
                        <div class="form-group">
                            <h4>Family Background : 
                                <a class="btn btn-success text-white btn-sm" id="add-family"><span class="fa fa-plus text-white"></span> Add Family</a>
                            </h4>
                            <table class="table table-bordered">
                                <tbody id="family-content">
                                    @foreach($employee->family as $family)
                                    <tr id="added-family-content">
                                        <td align="center"></td>
                                        <td><input type="text" class="form-control" value="{{$family->name}}" required name="family_name[]" placeholder="Family Name" /></td>
                                        <td><input type="text" class="form-control" value="{{$family->relationship}}" required name="family_relationship[]" placeholder="Relationship"  /></td>
                                        <td>
                                            <input type="text" class="form-control" value="{{$family->contact_number}}" required name="family_contact[]" placeholder="Contact Number" />
                                            <input type="hidden" class="form-control" value="{{encryptor('encrypt',$family->id)}}" required name="family_id[]" />
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row p-5">
                    <div class="col-lg-12  text-center">
                        <button type="submit" class="btn btn-warning waves-effect waves-themed"> <i class="ni ni-note"></i> Update Background Information</button>
                    </div>
                </div>
                </form>
            </div> 
        </div>
        <div class="tab-pane fade show" id="employee-information" role="tabpanel" aria-labelledby="Emplyoee Info Div">
            <div id="panel-4" class="panel">
                <form method="post" id="employee-information-form"  action="{{ route('hr-employee-functions', ['id' => 'update-employee-information']) }}">
                @csrf()
                <input type="hidden" class="form-control" value="{{encryptor('encrypt',$employee->id)}}" required name="employeeId" />
                <div class="row p-5">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding-top:22px;">
                                <div class="form-group">
                                    <label>Access Code : </label>
                                    <input type="text" class="form-control" value="{{$employee->access_code}}" name="access_code" placeholder="Access Code" maxlength="5"/>
                                </div>
                                <div class="form-group">
                                    <label>Employee ID : </label>
                                    <input type="number" class="form-control" value="{{$employee->employee_num}}" name="employee_id" placeholder="Employee ID" required maxlength="6"/>
                                </div>
                                <div class="form-group">
                                    <label>Department : </label>
                                    <select class="form-control" name="department_id" required>
                                        <option value=""></option>
                                        @foreach($departments as $department) 
                                            @php 
                                                $mode_deparment = '';
                                                if($department->id==$employee->department_id){
                                                    $mode_deparment = 'selected';
                                                }
                                            @endphp
                                            <option value="{{$department->id}}" {{$mode_deparment}}>{{$department->name}}</option>
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
                                                if($position->id==$employee->position_id){
                                                    $mode_position = 'selected';
                                                }
                                            @endphp
                                            <option value="{{$position->id}}" {{$mode_position}}>{{$position->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Section : </label>
                                    <input type="text" class="form-control" value="{{$employee->section}}" name="section" placeholder="Section"/>
                                </div>
                                <div class="form-group">
                                    <label>Tax Exemption : </label>
                                    @php 
                                        $tax_yes = '';
                                        $tax_no = '';
                                        if($employee->tax_exemp==1){
                                            $tax_yes = 'checked';
                                        }
                                        if($employee->tax_exemp==0){
                                            $tax_no = 'checked';
                                        }
                                    @endphp
                                    <div class="form-group">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="tax_exempt1" {{$tax_yes}} value="1" name="tax_exempt">
                                            <label class="custom-control-label" for="tax_exempt1">Yes</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="tax_exempt2" {{$tax_no}} value="0" name="tax_exempt">
                                            <label class="custom-control-label" for="tax_exempt2">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding-top:22px;">
                                <div class="form-group">
                                    <label>Date Hired : </label>
                                    <input type="date" class="form-control" value="{{$employee->date_hired}}" name="date_hired" placeholder="Date Hired" required/>
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
                                           if($employee->status=='PROBATIONARY'){
                                                $probi = 'selected';
                                           }
                                           if($employee->status=='REGULAR'){
                                                $regular = 'selected';
                                           }
                                           if($employee->status=='CASUAL'){
                                                $casual = 'selected';
                                           }
                                           if($employee->status=='SEPARATED'){
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
                                    @if($employee->status=='REGULAR')
                                        <div class="form-group">
                                            <label>Date of Regularization</label>
                                            <input type="date" class="form-control" value="{{$employee->regularization_date}}" name="date-regularization" required/>
                                        </div>
                                        <div class="form-group">
                                            <label>Date Regularized</label>
                                            <input type="date" class="form-control" value="{{$employee->date_regulized}}" name="date-regulized" required/>
                                        </div>
                                    @endif 
                                    @if($employee->status=='SEPARATED')
                                        <div class="form-group">
                                            <label>Date Resigned</label>
                                            <input type="date" class="form-control" value="{{$employee->date_resigned}}" name="date-resigned" required/>
                                        </div>
                                        <div class="form-group">
                                            <label>Separation Pay</label>
                                            @php 
                                                $mode_separation_yes = '';
                                                $mode_separation_no = '';
                                                
                                                if($employee->separation_pay==1){
                                                    $mode_separation_yes = 'checked';
                                                }
                                                if($employee->separation_pay==0){
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
                                    <input type="number" class="form-control" value="{{$employee->basic_salary}}" name="basic_salary" placeholder="Basic Salary"/>
                                </div>
                                <div class="form-group">
                                    <label>Allowance : </label>
                                    <input type="number" class="form-control" value="{{$employee->allowance}}" name="allowance" step="any" placeholder="Allowance"/>
                                </div>
                                <div class="form-group">
                                    <label>Gross Salary : </label>
                                    <input type="number" class="form-control" value="{{$employee->gross_salary}}" name="gross_salary"  step="any" placeholder="Gross Salary"/>
                                </div>
                                
                            </div>
                           
                </div>
                <div class="row p-5">
                    <div class="col-lg-12  text-center">
                        <button type="submit" class="btn btn-warning waves-effect waves-themed" id="employeeBtn"> <i class="ni ni-note"></i> Update Employee Information </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div class="tab-pane fade show" id="picture-information" role="tabpanel" aria-labelledby="Picture Div">
            <div id="panel-4" class="panel">
                <form method="post" id="picture-information-form"  action="{{ route('hr-employee-functions', ['id' => 'update-picture-information']) }}" enctype="multipart/form-data">
                @csrf()
                <input type="hidden" class="form-control" value="{{encryptor('encrypt',$employee->id)}}" required name="employeeId" />
                    <div class="row p-5">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <h3><b>Profile Picture</b></h3>
                            <hr>
                            @php
                                $destinationProfile = 'assets/img/employee/profile/';
                                $filenameProfile = $employee->employee_num;
                                $imagePathProfile = imagePath($destinationProfile.''.$filenameProfile,'//via.placeholder.com/400X400');
                            @endphp
                            <div class="form-group" align="center">
                                <img src="{{$imagePathProfile}}" style="width:400px;height:400px;" class="rounded" id="image_preview"><br>
                            </div>
                            <div class="form-group" align="center">
                                <input type="file" class="form-control" name="profile_picture" id="select_image"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <h3><b>Signature</b></h3>
                            <hr>
                            @php
                                $destinationSignature = 'assets/img/employee/signature/';
                                $filenameSignature = $employee->employee_num;
                                $imagePathSignature = imagePath($destinationSignature.''.$filenameSignature,'//via.placeholder.com/400X200');
                            @endphp
                            <div class="form-group" align="center">
                                <img src="{{$imagePathSignature}}" style="width:400px;height:200px;" class="rounded" id="image_preview_signature"><br>
                            </div>
                            <div class="form-group" align="center">
                                <input type="file" class="form-control" name="signature_image" id="select_image_signature"/>
                            </div>
                        </div>
                    </div>
                    <div class="row p-5">
                        <div class="col-lg-12  text-center">
                            <button type="submit" class="btn btn-warning waves-effect waves-themed"> <i class="ni ni-note"></i> Update Upload Picture</button>
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
            '<td><input type="text" class="form-control" name="company_address[]" placeholder="Address" required/><input type="hidden" class="form-control" name="company_id[]" value="wala" /></td>'
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
            '<td><input type="text" class="form-control" name="school_years[]" required placeholder="Years Acquainted"   maxlength="7" /><input type="hidden" class="form-control" name="school_id[]" value="wala"/></td>'+
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
            '<td><input type="text" class="form-control" name="family_contact[]" required placeholder="Contact Number" /><input type="hidden" class="form-control" name="family_id[]" value="wala" /></td>'+
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
            $.post("{{ route('hr-employee-functions', ['id' => 'get-position']) }}",
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
                $('#employeeBtn').prop('disabled', true);
                $(this).addClass('is-invalid');
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
