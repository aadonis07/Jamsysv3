@extends ('layouts.hr-department.app')
@section ('title')
    ERP Account List
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<style>
.help {cursor: help;}
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Users</li>
<li class="breadcrumb-item active">ERP Account List</li>
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
                <span class="h5 mt-0">ERP Account List</span>
                <br>
                <p class="mb-0">In creating ERP Account, open <b class="text-dark">View Employee</b> and search the name of creating account.</p>
            </div>
            <div class="col-lg-5 form-group">
               
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    ERP Account List
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
						<div class="col-md-12">
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item  "><a class="nav-link active fs-lg text-primary" data-toggle="tab" href="#active-tab" role="tab">ACTIVE</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#inactive-tab" role="tab">INACTIVE</a></li>
							</ul>
						</div>
                        <div class="col-md-12">
						<br>
						<div class="tab-content">
								<div class="tab-pane show active" id="active-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-users-active" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Name</th>
													<th>firstname</th>
													<th>lastname</th>
													<th>middlename</th>
													<th>nickname</th>
													<th>Username</th>
													<th>Email</th>
													<th>Department_position</th>
													<th>Department</th>
													<th>position</th>
													<th>Action</th>
												</tr>
												</thead>
											<tbody>
										   
											</tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Name</th>
													<th>firstname</th>
													<th>lastname</th>
													<th>middlename</th>
													<th>nickname</th>
													<th>Username</th>
													<th>Email</th>
													<th>Department_position</th>
													<th>Department</th>
													<th>position</th>
													<th>Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="inactive-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-users-inactive" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th>Name</th>
												<th>firstname</th>
												<th>lastname</th>
												<th>middlename</th>
												<th>nickname</th>
												<th>Username</th>
												<th>Email</th>
												<th>Department_position</th>
												<th>Department</th>
												<th>position</th>
												<th>Action</th>
											</tr>
											</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th>Name</th>
												<th>firstname</th>
												<th>lastname</th>
												<th>middlename</th>
												<th>nickname</th>
												<th>Username</th>
												<th>Email</th>
												<th>Department_position</th>
												<th>Department</th>
												<th>position</th>
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
<div class="modal fade" id="activate-modal">
	<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Activate Account</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" id="control-form"  action="{{ route('hr-user-functions', ['id' => 'control-account']) }}">
					@csrf()
					<div id="account-content">

					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary waves-effect waves-themed" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary waves-effect waves-themed" form="control-form">Save changes</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>

<script>
 $(function(){
		$('#dt-users-active').DataTable( {
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('hr-user-functions',['id' => 'user-active-serverside']) }}",
                    type: "POST",  
					data: {status:"ACTIVE"},
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
                    { data: 'employee.first_name', name: 'employee.first_name',visible:false},
                    { data: 'employee.last_name', name: 'employee.last_name',visible:false},
                    { data: 'employee.middle_name', name: 'employee.middle_name',visible:false},
					{ data: 'nickname', name: 'nickname',visible:false},
					{ data: 'username', name: 'username'},
					{ data: 'email', name: 'email',visible:false},
					{ data: 'department_position', name: 'department_position',orderable: false, searchable: false},
                    { data: 'department.name', name: 'department.name',visible:false},
                    { data: 'position.name', name: 'position.name',visible:false},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false},
                ]
            });
			$('#dt-users-inactive').DataTable( {
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('hr-user-functions',['id' => 'user-active-serverside']) }}",
                    type: "POST",  
					data: {status:"INACTIVE"},
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
                    { data: 'employee.first_name', name: 'employee.first_name',visible:false},
                    { data: 'employee.last_name', name: 'employee.last_name',visible:false},
                    { data: 'employee.middle_name', name: 'employee.middle_name',visible:false},
					{ data: 'nickname', name: 'nickname',visible:false},
					{ data: 'username', name: 'username'},
					{ data: 'email', name: 'email',visible:false},
					{ data: 'department_position', name: 'department_position',orderable: false, searchable: false},
                    { data: 'department.name', name: 'department.name',visible:false},
                    { data: 'position.name', name: 'position.name',visible:false},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false},
                ]
            });
 });
$(document).ready(function(index){
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
	$(document).on('click','.action-btn',function(){
		$('#account-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
		var id = $(this).data('id');
		var path = "{{route('hr-user-control')}}?id="+id;
		$('#account-content').load(path);
		$('#activate-modal').modal('show');
	});
});
</script>
@endsection