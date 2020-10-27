@extends ('layouts.hr-department.app')
@section ('title')
    View Employees
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<style>
.select2-dropdown {
  z-index: 999999;
}
.help {cursor: help;}
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Employee</li>
<li class="breadcrumb-item active">View Employee</li>
@endsection
@section('content')
<div class="row mb-3 ">
    <div class="col-lg-12 d-flex flex-start w-100 mb-2">
        <div class="mr-2 hidden-md-down">
            <span class="icon-stack icon-stack-lg">
                <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
                <i class="ni ni-settings icon-stack-1x opacity-100 color-white" style="font-size: 14px; margin-bottom: 2px;"></i>
            </span>
        </div>
        <div class="row d-flex flex-fill">
            <div class="col-lg-7 flex-fill">
                <span class="h5 mt-0">Employee List</span>
                <br>
                <p class="mb-0">In creating employee, click <b class="text-dark">Add Employee</b>.</p>
            </div>
            <div class="col-lg-5 form-group">
                <!-- <div class="input-group bg-white shadow-inset-2">
                    <input type="search" id="employee-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text bg-transparent border-left-0">
                            <i class="fal fa-search"></i>
                        </span>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Employee List
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="row">
						<div class="col-md-8">
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item  "><a class="nav-link active fs-lg text-primary" data-toggle="tab" href="#regular-tab" role="tab">REGULAR</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#probationary-tab" role="tab">PROBATIONARY</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-warning" data-toggle="tab" href="#casual-tab" role="tab">CASUAL</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-danger" data-toggle="tab" href="#separated-tab" role="tab">SEPARATED</a></li>
							</ul>
						</div>
						<div class="col-md-4 text-right">
							<a href="{{ route('hr-employee-add')  }}" class="btn btn-primary">Add Employee</a>
						</div>
                        <div class="col-md-12">
						<br>
						<div class="tab-content">
							<div class="tab-pane show active" id="regular-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-employees-regular" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Name</th>
												<th>first_name</th>
												<th>last_name</th>
												<th>middle_name</th>
												<th>department</th>
												<th>position</th>
												<th class="sorting" width="15%" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Department</th>
												<th>email</th>
												<th>contact_number</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Contact Details</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2">Date Hired</th>
												<th width="15%">Action</th>
											</tr>
											</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th>Name</th>
												<th>first_name</th>
												<th>last_name</th>
												<th>middle_name</th>
												<th>department</th>
												<th>position</th>
												<th>Department</th>
												<th>email</th>
												<th>contact_number</th>
												<th>Contact Details</th>
												<th>Date Hired</th>
												<th>Action</th>
											</tr>
										</tfoot>
									</table>
									</div>
								</div>
								<div class="tab-pane show" id="probationary-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-employees-probationary" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Name</th>
												<th>first_name</th>
												<th>last_name</th>
												<th>middle_name</th>
												<th>department</th>
												<th>position</th>
												<th class="sorting" width="15%" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Department</th>
												<th>email</th>
												<th>contact_number</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Contact Details</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2">Date Hired</th>
												<th width="15%">Action</th>
											</tr>
											</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th>Name</th>
												<th>first_name</th>
												<th>last_name</th>
												<th>middle_name</th>
												<th>department</th>
												<th>position</th>
												<th>Department</th>
												<th>email</th>
												<th>contact_number</th>
												<th>Contact Details</th>
												<th>Date Hired</th>
												<th>Action</th>
											</tr>
										</tfoot>
									</table>
									</div>
								</div>
								<div class="tab-pane show" id="casual-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-employees-casual" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Name</th>
												<th>first_name</th>
												<th>last_name</th>
												<th>middle_name</th>
												<th>department</th>
												<th>position</th>
												<th width="15%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Department</th>
												<th>email</th>
												<th>contact_number</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Contact Details</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2">Date Hired</th>
												<th width="15%">Action</th>
											</tr>
											</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th>Name</th>
												<th>first_name</th>
												<th>last_name</th>
												<th>middle_name</th>
												<th>department</th>
												<th>position</th>
												<th>Department</th>
												<th>email</th>
												<th>contact_number</th>
												<th>Contact Details</th>
												<th>Date Hired</th>
												<th>Action</th>
											</tr>
										</tfoot>
									</table>
									</div>
								</div>
								<div class="tab-pane show" id="separated-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-employees-separated" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Name</th>
												<th>first_name</th>
												<th>last_name</th>
												<th>middle_name</th>
												<th>department</th>
												<th>position</th>
												<th width="15%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Department</th>
												<th>email</th>
												<th>contact_number</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Contact Details</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2">Date Hired</th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1">Date Resigned</th>
												<th width="15%">Action</th>
											</tr>
											</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th>Name</th>
												<th>first_name</th>
												<th>last_name</th>
												<th>middle_name</th>
												<th>department</th>
												<th>position</th>
												<th>Department</th>
												<th>email</th>
												<th>contact_number</th>
												<th>Contact Details</th>
												<th>Date Hired</th>
												<th>Date Resigned</th>
												<th>Action</th>
											</tr>
										</tfoot>
									</table>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="employee-accounts-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Employee Accounts </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
				<div id="employee-account-content">
				</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="employee-info-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Employee Information </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
				<div id="employee-info-content">

				</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create-erp-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create ERP Account </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
			<form method="post" id="create-erp-form" onsubmit="addbtn.disabled = true;"  action="{{ route('hr-employee-functions', ['id' => 'create-erp']) }}">
                @csrf()
				<div id="employee-erp-content">

				</div>
			</form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" id="addbtn" class="btn btn-primary" form="create-erp-form" >Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script>
 $(function(){
            $('#dt-employees-regular').DataTable( {
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('hr-employee-functions',['id' => 'employee-list-serverside']) }}",
                    type: "POST",  
					data: {status:"REGULAR"},
					"pageLength": 100,
                   "processing": true,
                   "serverSide": true,
                   "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name',orderable: false, searchable: false},
                    { data: 'first_name', name: 'first_name',visible:false},
                    { data: 'last_name', name: 'last_name',visible:false},
                    { data: 'middle_name', name: 'middle_name',visible:false},
                    { data: 'department.name', name: 'department.name',visible:false},
                    { data: 'position.name', name: 'position.name',visible:false},
                    { data: 'department_position', name: 'department_position',orderable: false, searchable: false},
                    { data: 'email', name: 'email',visible:false},
                    { data: 'contact_number', name: 'contact_number',visible:false},
                    { data: 'contact_details', name: 'contact_details',orderable: false, searchable: false},
                    { data: 'date_hired', name: 'date_hired'},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false},
                ]
            });
			$('#dt-employees-probationary').DataTable( {
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('hr-employee-functions',['id' => 'employee-list-serverside']) }}",
                    type: "POST",  
					data: {status:"PROBATIONARY"},
					"pageLength": 100,
                   "processing": true,
                   "serverSide": true,
                   "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name',orderable: false, searchable: false},
                    { data: 'first_name', name: 'first_name',visible:false},
                    { data: 'last_name', name: 'last_name',visible:false},
                    { data: 'middle_name', name: 'middle_name',visible:false},
                    { data: 'department.name', name: 'department.name',visible:false},
                    { data: 'position.name', name: 'position.name',visible:false},
                    { data: 'department_position', name: 'department_position',orderable: false, searchable: false},
                    { data: 'email', name: 'email',visible:false},
                    { data: 'contact_number', name: 'contact_number',visible:false},
                    { data: 'contact_details', name: 'contact_details',orderable: false, searchable: false},
                    { data: 'date_hired', name: 'date_hired'},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false},
                ]
            });
			$('#dt-employees-casual').DataTable( {
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('hr-employee-functions',['id' => 'employee-list-serverside']) }}",
                    type: "POST",  
					data: {status:"CASUAL"},
					"pageLength": 100,
                   "processing": true,
                   "serverSide": true,
                   "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name',orderable: false, searchable: false},
                    { data: 'first_name', name: 'first_name',visible:false},
                    { data: 'last_name', name: 'last_name',visible:false},
                    { data: 'middle_name', name: 'middle_name',visible:false},
                    { data: 'department.name', name: 'department.name',visible:false},
                    { data: 'position.name', name: 'position.name',visible:false},
                    { data: 'department_position', name: 'department_position',orderable: false, searchable: false},
                    { data: 'email', name: 'email',visible:false},
                    { data: 'contact_number', name: 'contact_number',visible:false},
                    { data: 'contact_details', name: 'contact_details',orderable: false, searchable: false},
                    { data: 'date_hired', name: 'date_hired'},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false},
                ]
            });
			$('#dt-employees-separated').DataTable( {
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('hr-employee-functions',['id' => 'separated-employee-list-serverside']) }}",
                    type: "POST",  
					"pageLength": 100,
                   "processing": true,
                   "serverSide": true,
                   "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name',orderable: false, searchable: false},
                    { data: 'first_name', name: 'first_name',visible:false},
                    { data: 'last_name', name: 'last_name',visible:false},
                    { data: 'middle_name', name: 'middle_name',visible:false},
                    { data: 'department.name', name: 'department.name',visible:false},
                    { data: 'position.name', name: 'position.name',visible:false},
                    { data: 'department_position', name: 'department_position',orderable: false, searchable: false},
                    { data: 'email', name: 'email',visible:false},
                    { data: 'contact_number', name: 'contact_number',visible:false},
                    { data: 'contact_details', name: 'contact_details',orderable: false, searchable: false},
                    { data: 'date_hired', name: 'date_hired'},
					{ data: 'date_resigned', name: 'date_resigned'},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false},
                ]
            });
});

$(document).ready(function(index){
	$(document).on('click','.employee_account',function(){
		var id = $(this).data('id');
		$('#employee-account-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
		var path = "{{route('hr-employee-account')}}?id="+id;
		$('#employee-account-content').load(path,function(){
			$('select[name="account-type"]').select2({
				placeholder:"Select Account Type",
				allowClear: true,
			});
		});
		$('#employee-accounts-modal').modal('show');
	});
	$(document).on('click','#add-account-btn',function(){
		formData = new FormData();
		var employeeid = $('input[name="employee-id"]').val();
		var type = $('select[name="account-type"]').val();
		var username = $('input[name="username"]').val();
		var password = $('input[name="password"]').val();
		formData.append('type',type);
		formData.append('username',username);
		formData.append('password',password);
		formData.append('employeeid',employeeid);
		if($.trim(type)){
			if($.trim(username)){
				$('input[name="username"]').removeClass('is-invalid');
				$('input[name="username"]').addClass('is-valid');
				if($.trim(password)){
					$('input[name="password"]').removeClass('is-invalid');
					$('input[name="password"]').addClass('is-valid');
						$('#account-table-content').html('<tr><td colspan="4">'+
						'<div class="col-md-12 mt-4">'+
						'    <div class="d-flex justify-content-center">'+
						'        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
						'            <span class="sr-only">Loading...</span>'+
						'        </div>'+
						'    </div>'+
						'</div>'+
						'</td></tr>');
						$.ajax({
							type: "POST",
							url: "{{ route('hr-employee-functions', ['id' => 'add-account']) }}",
							data: formData,
							CrossDomain:true,
							contentType: !1,
							processData: !1,
							success: function(e) {
								var id = $('input[name="employee-id"]').val();
								var path = "{{route('hr-employee-account')}}?id="+id;
								$('#employee-account-content').load(path,function(){
									$('select[name="account-type"]').select2({
										placeholder:"Select Account Type",
										allowClear: true,
									});
								});
							},
							error: function(result){
								Swal.fire({
									type: 'error',
									title: 'Oops...',
									text: 'Theres an error while saving',
								})
							}
						});
				}else{
					$('input[name="password"]').addClass('is-invalid');
				}
			}else{
				$('input[name="username"]').addClass('is-invalid');
			}
		}else{
			Swal.fire({
				type: 'error',
				title: 'Oops...',
				text: 'Account Type Cannot be empty',
			})
		}
	});
	$(document).on('click','.show-password',function(){
		formData = new FormData();
		var id = $(this).data('id');
		formData.append('id',id);
		Swal.fire({
			title: 'Enter your password',
			input: 'password',
			inputAttributes: {
				autocapitalize: 'off'
			},
			showCancelButton: true,
			confirmButtonText: 'Submit',
			showLoaderOnConfirm: true,
			preConfirm: (login) => {
				formData.append('login',login);
				$.ajax({
					type: "POST",
					url: "{{ route('hr-employee-functions', ['id' => 'validate-password']) }}",
					data: formData,
					CrossDomain:true,
					contentType: !1,
					processData: !1,
					success: function(success) {
						if(success.id!=0){
							$('#account'+success.id).text(success.password);
						}else{
							Swal.fire({
							type: 'error',
							title: 'Oops...',
							text: 'Wrong Password!',
						})
						}
					},
					error: function(result){
						Swal.fire({
							type: 'error',
							title: 'Oops...',
							text: 'Theres an error while validating',
						})
					}
				});
			}
		});
	});
	$(document).on('click','.update-account',function(){
		var id = $(this).data('id');
		var enc_id = $(this).data('enc_id');
		var type = $(this).data('type');
		
	formData = new FormData();
	formData.append('id',enc_id);
		Swal.fire({
			title: 'Enter your password',
			input: 'password',
			inputAttributes: {
				autocapitalize: 'off'
			},
			showCancelButton: true,
			confirmButtonText: 'Submit',
			showLoaderOnConfirm: true,
			preConfirm: (login) => {
				formData.append('login',login);
				$.ajax({
					type: "POST",
					url: "{{ route('hr-employee-functions', ['id' => 'validate-password']) }}",
					data: formData,
					CrossDomain:true,
					contentType: !1,
					processData: !1,
					success: function(success) {
						if(success.id!=0){
							$('#tab'+id).html('<td colspan="4">'+
							'<div class="col-md-12 mt-4">'+
							'    <div class="d-flex justify-content-center">'+
							'        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
							'            <span class="sr-only">Loading...</span>'+
							'        </div>'+
							'    </div>'+
							'</div>'+
							'</td>');
							var path = "{{route('hr-employee-update-account')}}?id="+id+"&&type="+type;
							$('#tab'+id).load(path);
						}else{
							Swal.fire({
							type: 'error',
							title: 'Oops...',
							text: 'Wrong Password!',
						})
						}
					},
					error: function(result){
						console.log(result);
						Swal.fire({
							type: 'error',
							title: 'Oops...',
							text: 'Theres an error while validating',
						})
					}
				});
			}
		});
	});
	$(document).on('click','.submit-update',function(){
		formData = new FormData();
		var id = $(this).data('id');
		var type = $('#type'+id).val();
		var username = $('#username'+id).val();
		var password = $('#password'+id).val();
		formData.append('type',type);
		formData.append('username',username);
		formData.append('password',password);
		formData.append('id',id);

		if($.trim(type)){
			if($.trim(username)){
				$('#username'+id).removeClass('is-invalid');
				$('#username'+id).addClass('is-valid');
				if($.trim(password)){
					$('#password'+id).removeClass('is-invalid');
					$('#password'+id).addClass('is-valid');
						$.ajax({
							type: "POST",
							url: "{{ route('hr-employee-functions', ['id' => 'update-account']) }}",
							data: formData,
							CrossDomain:true,
							contentType: !1,
							processData: !1,
							success: function(e) {
								var path = "{{route('hr-employee-account')}}?id="+e;
								$('#employee-account-content').load(path,function(){
									$('select[name="account-type"]').select2({
										placeholder:"Select Account Type",
										allowClear: true,
									});
								});
							},
							error: function(result){
								Swal.fire({
									type: 'error',
									title: 'Oops...',
									text: 'Theres an error while saving',
								})
							}
						});
				}else{
					$('#password'+id).addClass('is-invalid');
				}
			}else{
				$('#username'+id).addClass('is-invalid');
			}
		}else{
			Swal.fire({
				type: 'error',
				title: 'Oops...',
				text: 'Account Type Cannot be empty',
			})
		}
	});

	$(document).on('click','.employee-info',function(){
		var id = $(this).data('id');
		$('#employee-info-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
		var path = "{{route('hr-employee-info')}}?id="+id;
		$('#employee-info-content').load(path);
		$('#employee-info-modal').modal('show');
	});

	$(document).on('click','.add-erp-account',function(){
		var id = $(this).data('id');
		$('#employee-erp-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
		var path = "{{route('hr-employee-erp-create')}}?id="+id;
		$('#employee-erp-content').load(path,function(){
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
		});
		$('#create-erp-modal').modal('show');
	});
	$(document).on('change','select[name="department_id"]',function(){
		var id = $(this).val();
		$('select[name="position_id"]').html('<option></option>');
		$.post("{{ route('hr-employee-functions', ['id' => 'get-position']) }}",
		{id: id},
		function(data){
			$('select[name="position_id"]').html(data);
		});
		if(id==18){
			$('.sales-content').show();
			$('select[name="sales-team"]').prop('required',true);
			$('input[name="date-from"]').prop('required',true);
			$('input[name="quota"]').prop('required',true);
			$('select[name="sales-team"]').select2({
				placeholder: "Select Team",
				allowClear: true,
				width:"100%"
			});
		}else{
			$('.sales-content').hide();
			$('select[name="sales-team"]').prop('required',false);
			$('input[name="date-from"]').prop('required',false);
			$('input[name="quota"]').prop('required',false);
		}
	});
	$(document).on('change','select[name="position_id"]',function(){
		var id = $(this).val();
		if(id==8||id==7){
			$.post("{{ route('hr-employee-functions', ['id' => 'get-team']) }}",
			{id: id},
			function(data){
				$('select[name="sales-team"]').html(data);
			});
		}
	});

	$(document).on('change','select[name="sales-team"]',function(){
		var temp_name = $(this).find(':selected').data('name');
		var manager = $(this).find(':selected').data('manager');
		$('input[name="team-name"]').val(temp_name);
		$('input[name="with-manager"]').val(manager);
	});
	
	
});

</script>
@endsection