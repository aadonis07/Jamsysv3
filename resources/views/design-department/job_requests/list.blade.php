@extends ('layouts.design-department.app')
@section ('title')
    Job Request List
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
<li class="breadcrumb-item">Job Request</li>
<li class="breadcrumb-item active">JR List</li>
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
                <span class="h5 mt-0">Job Request List</span>
                <br>
                <p class="mb-0">This work is on Design Department. </p>
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
                    Job Request List 
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
								<li class="nav-item  "><a class="nav-link active fs-lg text-primary" data-toggle="tab" href="#pending-tab" role="tab">PENDING </a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#ongoing-tab" role="tab">ONGOING </a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-warning" data-toggle="tab" href="#accomplished-tab" role="tab">ACCOMPLISHED </a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-info" data-toggle="tab" href="#request-reject-tab" role="tab">REQUEST FOR REJECTION </a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-primary" data-toggle="tab" href="#rejected-tab" role="tab">REJECTED </a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#cancelled-tab" role="tab">CANCELLED</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-warning" data-toggle="tab" href="#declined-tab" role="tab">DECLINED </a></li>
							</ul>
						</div>
                        <div class="col-md-12">
						<br>
							<div class="tab-content">
								<div class="tab-pane show active" id="pending-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-jr-pending" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
	                                                <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="ongoing-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-jr-ongoing" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
	                                                <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="accomplished-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-jr-accomplished" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
	                                                <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="request-reject-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-jr-request-reject" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
	                                                <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="rejected-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-jr-rejected" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
	                                                <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="cancelled-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-jr-cancelled" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
	                                                <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="declined-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-jr-declined" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
	                                                <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Updated</th>
	                                                <th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
	                                                <th>Job Request Number</th>
	                                                <th>Client<br><small>[Quotation Number]</small></th>
	                                                <th>Quote Number</th>
													<th>Agent</th>
													<th>Lastname</th>
													<th width="15%">Action</th>
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

@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script>
$(function(){
    $('#dt-jr-pending').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('design-job-request-functions',['id' => 'jr-pending-serverside']) }}",
            type: "POST",  
            data: {status:"PENDING"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'updated_at', name: 'updated_at'},
            { data: 'created_at', name: 'created_at',visible:false},
            { data: 'jr_number', name: 'jr_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.quote_number', name: 'quotation.quote_number',visible:false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name'},
			{ data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

    $('#dt-jr-ongoing').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('design-job-request-functions',['id' => 'jr-ongoing-serverside']) }}",
            type: "POST",  
            data: {status:"ONGOING"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'updated_at', name: 'updated_at'},
            { data: 'created_at', name: 'created_at',visible:false},
            { data: 'jr_number', name: 'jr_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.quote_number', name: 'quotation.quote_number',visible:false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name'},
			{ data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

    $('#dt-jr-accomplished').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('design-job-request-functions',['id' => 'jr-accomplished-serverside']) }}",
            type: "POST",  
            data: {status:"ACCOMPLISHED"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'updated_at', name: 'updated_at'},
            { data: 'created_at', name: 'created_at',visible:false},
            { data: 'jr_number', name: 'jr_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.quote_number', name: 'quotation.quote_number',visible:false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name'},
			{ data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

    $('#dt-jr-request-reject').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('design-job-request-functions',['id' => 'jr-request-reject-serverside']) }}",
            type: "POST",  
            data: {status:"REJECT-REQUEST"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'updated_at', name: 'updated_at'},
            { data: 'created_at', name: 'created_at',visible:false},
            { data: 'jr_number', name: 'jr_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.quote_number', name: 'quotation.quote_number',visible:false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name'},
			{ data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

    $('#dt-jr-rejected').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('design-job-request-functions',['id' => 'jr-rejected-serverside']) }}",
            type: "POST",  
            data: {status:"REJECTED"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'updated_at', name: 'updated_at'},
            { data: 'created_at', name: 'created_at',visible:false},
            { data: 'jr_number', name: 'jr_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.quote_number', name: 'quotation.quote_number',visible:false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name'},
			{ data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

    $('#dt-jr-cancelled').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('design-job-request-functions',['id' => 'jr-cancelled-serverside']) }}",
            type: "POST",  
            data: {status:"CANCELLED"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'updated_at', name: 'updated_at'},
            { data: 'created_at', name: 'created_at',visible:false},
            { data: 'jr_number', name: 'jr_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.quote_number', name: 'quotation.quote_number',visible:false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name'},
			{ data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

    $('#dt-jr-declined').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('design-job-request-functions',['id' => 'jr-declined-serverside']) }}",
            type: "POST",  
            data: {status:"DECLINED"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'updated_at', name: 'updated_at'},
            { data: 'created_at', name: 'created_at',visible:false},
            { data: 'jr_number', name: 'jr_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.quote_number', name: 'quotation.quote_number',visible:false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name'},
			{ data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
});
</script>
@endsection