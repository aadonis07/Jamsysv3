@extends ('layouts.sales-department.app')
@section ('title')
    Quotation List
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<style>
.select2-dropdown {
  z-index: 999999;
}
.help {cursor: help;}
.btn-standard{
	
}
.btn-standard:hover{
	color:white !important;
}
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Quotations</li>
<li class="breadcrumb-item active">Quotation List</li>
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
                <span class="h5 mt-0">Quotation List</span>
                <br>
                <p class="mb-0">In creating quotation, click <a href="{{ route('sales-quotation-create')  }}" class="btn btn-primary btn-sm">Create Quotaion</a>.</p>
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
                    Quotation List 
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
								<li class="nav-item  "><a class="nav-link active fs-lg text-primary" data-toggle="tab" href="#pending-tab" role="tab">PENDING @php echo countStatusQuotation('primary','PENDING'); @endphp</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#moved-tab" role="tab">MOVED @php echo countStatusQuotation('success','FOR-APPROVAL'); @endphp</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-warning" data-toggle="tab" href="#approved-tab" role="tab">APPROVED @php echo countStatusQuotation('warning','APPROVED-PROPRIETOR'); @endphp</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-info" data-toggle="tab" href="#accounting-approved-tab" role="tab">APPROVED BY ACCOUNTING @php echo countStatusQuotation('info','APPROVED-ACCOUNTING'); @endphp</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-primary" data-toggle="tab" href="#on-processed-tab" role="tab">ON PROCESSED @php echo countStatusQuotation('primary','ON-PROCESS'); @endphp</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#completed-tab" role="tab">COMPLETED @php echo countStatusQuotation('success','COMPLETED'); @endphp</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-warning" data-toggle="tab" href="#request-reject-tab" role="tab">REQUEST FOR REJECTION @php echo countStatusQuotation('warning','R-REJECT'); @endphp</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-danger" data-toggle="tab" href="#rejected-tab" role="tab">REJECTED @php echo countStatusQuotation('danger','REJECTED'); @endphp</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-danger" data-toggle="tab" href="#cancelled-tab" role="tab">CANCELLED @php echo countStatusQuotation('danger','CANCELLED'); @endphp</a></li>
							</ul>
						</div>
                        <div class="col-md-12">
						<br>
						<div class="tab-content">
							<div class="tab-pane show active" id="pending-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-quotation-pending" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
												<th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</tfoot>
									</table>
									</div>
								</div>
								<div class="tab-pane show" id="moved-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-quotation-moved" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
												<th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</tfoot>
									</table>
									</div>
								</div>
								<div class="tab-pane show" id="approved-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-quotation-approved-proprietor" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
												<th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</tfoot>
									</table>
									</div>
								</div>
								<div class="tab-pane show" id="accounting-approved-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-quotation-approved-accounting" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
													<th>subject</th>
													<th>work nature</th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
													<th width="15%">Action</th>
												</tr>
											</thead>
											<tbody>
										
											</tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
													<th>subject</th>
													<th>work nature</th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
													<th width="15%">Action</th>
												</tr>
											</tfoot>
										</table>
										</div>
									</div>
									<div class="tab-pane show" id="on-processed-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-quotation-on-process" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
												<th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</tfoot>
										</table>
										</div>
									</div>
									<div class="tab-pane show" id="completed-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-employees-separated" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
												<th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</tfoot>
										</table>
										</div>
									</div>
									<div class="tab-pane show" id="request-reject-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-employees-separated" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
												<th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</tfoot>
										</table>
										</div>
									</div>
									<div class="tab-pane show" id="rejected-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-employees-separated" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
												<th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</tfoot>
										</table>
										</div>
									</div>
									<div class="tab-pane show" id="cancelled--tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-employees-separated" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
												<th width="15%">Action</th>
											</tr>
										</thead>
										<tbody>
									   
										</tbody>
										<tfoot class="thead-themed">
											<tr class="text-center">
											    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Moved</th>
												<th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												<th>Contract Amount</th>
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
<!-- ============================================================================ -->
<form id="action-form" method="POST" action="{{ route('sales-quotation-functions',['id' => 'action-quotation']) }}">
    @csrf()
	<input class="form-control" name="quotationId" readonly type="hidden" />
	<input class="form-control" name="actionMode" readonly type="hidden" />
</form>

<!-- ============================================================================ -->
<div class="modal fade" id="jobrequest-info-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Job Request </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
				<form id="job-request-form" method="POST" action="{{ route('sales-job-request-functions',['id' => 'create-job-request']) }}">
					@csrf()
					<div id="jobrequest-products-content">

					</div>
					<input class="form-control" name="qID" type="hidden" readonly/>
				</form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="job-request-form" class="btn btn-success" >Create Job Request</button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================================ -->
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script>
 $(function(){
    $('#dt-quotation-pending').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('sales-quotation-functions',['id' => 'quotation-list-serverside']) }}",
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
            { data: 'created_at', name: 'created_at'},
            { data: 'subject', name: 'subject',visible:false},
            { data: 'work_nature', name: 'work_nature',visible:false},
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'grand_total', name: 'grand_total'},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
	$('#dt-quotation-moved').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('sales-quotation-functions',['id' => 'quotation-moved-serverside']) }}",
            type: "POST",  
            data: {status:"FOR-APPROVAL"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'date_moved', name: 'date_moved'},
            { data: 'subject', name: 'subject',visible:false},
            { data: 'work_nature', name: 'work_nature',visible:false},
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'grand_total', name: 'grand_total'},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

	$('#dt-quotation-approved-proprietor').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('sales-quotation-functions',['id' => 'quotation-moved-serverside']) }}",
            type: "POST",  
            data: {status:"APPROVED-PROPRIETOR"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'date_moved', name: 'date_moved'},
            { data: 'subject', name: 'subject',visible:false},
            { data: 'work_nature', name: 'work_nature',visible:false},
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'grand_total', name: 'grand_total'},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

	$('#dt-quotation-approved-accounting').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('sales-quotation-functions',['id' => 'quotation-moved-serverside']) }}",
            type: "POST",  
            data: {status:"APPROVED-ACCOUNTING"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'date_moved', name: 'date_moved'},
            { data: 'subject', name: 'subject',visible:false},
            { data: 'work_nature', name: 'work_nature',visible:false},
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'grand_total', name: 'grand_total'},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

	$('#dt-quotation-on-process').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('sales-quotation-functions',['id' => 'quotation-moved-serverside']) }}",
            type: "POST",  
            data: {status:"ON-PROCESS"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'date_moved', name: 'date_moved'},
            { data: 'subject', name: 'subject',visible:false},
            { data: 'work_nature', name: 'work_nature',visible:false},
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'grand_total', name: 'grand_total'},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
	
 });
$(document).ready(function(index){
    $(document).on('click','.job_request',function(){
        var id = $(this).data('id');
        var path = "{{route('sales-quotation-jobrequest')}}?id="+id;
        $('#jobrequest-products-content').load(path);
        $('input[name="qID"]').val(id);
        $('#jobrequest-info-modal').modal('show');
    });
	$(document).on('click','.view-quotation',function(){
		var id = $(this).data('id');
		$('input[name="quotationId"]').val(id);
		$('input[name="actionMode"]').val('view');
		$('#action-form').submit();
	});
	$(document).on('click','.update-quotation',function(){
		var id = $(this).data('id');
		$('input[name="quotationId"]').val(id);
		$('input[name="actionMode"]').val('update');
		$('#action-form').submit();
	});
	$(document).on('click','.hold-quotation',function(){
		var id = $(this).data('id');
		var status = $(this).data('status');
		Swal.fire({
			title: 'Confirm Save',
			text: "Are you sure you want to request for hold this quotation ?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes!, Request for Hold this quotation.'
		}).then((result) => {
			if (result.value) {
				toastMessage('Success','Your quotation is on hold request.','success','toast-top-right');
				$.post("{{ route('sales-quotation-functions', ['id' => 'request-hold']) }}",
				{id: id,},
				function(data){
					var status_quotation = data;
					var table_data = '';
					if(status == 'FOR-APPROVAL'){
						table_data = 'moved';
					}else{
						table_data = status_quotation.toLowerCase();
					}
					$("#dt-quotation-"+table_data).dataTable().fnDestroy();
					$('#dt-quotation-'+table_data).DataTable({
						"processing": true,
						"serverSide": true,
						"pageLength": 50,
						"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
						"ajax":{
							url :"{{ route('sales-quotation-functions',['id' => 'quotation-list-serverside']) }}",
							type: "POST",  
							data: {status:status_quotation},
							"processing": true,
							"serverSide": true,
							error: function(data){  // error handling
								$('#err').html(JSON.stringify(data));
							}
						},
						columns: [
							{ data: 'DT_RowIndex',orderable: false, searchable: false },
							{ data: 'created_at', name: 'created_at'},
							{ data: 'subject', name: 'subject',visible:false},
							{ data: 'work_nature', name: 'work_nature',visible:false},
							{ data: 'quote_number', name: 'quote_number'},
							{ data: 'client.name', name: 'client.name'},
							{ data: 'grand_total', name: 'grand_total'},
							{ data: 'actions', name: 'actions',orderable: false, searchable: false},
						]
					});
					$('.hold-quotation').prop('disabled', false);
				});
			}else{
				$('.hold-quotation').prop('disabled', false);
			}
		});
	});
	$(document).on('click','.request-reject-quotation',function(){
		var id = $(this).data('id');
		var status = $(this).data('status');
		Swal.fire({
			title: 'Confirm Save',
			text: "Are you sure you want to request for reject this quotation ?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes!, Request for Reject this quotation.'
		}).then((result) => {
			if (result.value) {
				toastMessage('Success','Your quotation is for request for rejection.','success','toast-top-right');
				$.post("{{ route('sales-quotation-functions', ['id' => 'request-reject']) }}",
				{id: id,},
				function(data){
					var status_quotation = data;
					var table_data = '';
					if(status == 'FOR-APPROVAL'){
						table_data = 'moved';
					}else{
						table_data = status_quotation.toLowerCase();
					}
					$("#dt-quotation-"+table_data).dataTable().fnDestroy();
					$('#dt-quotation-'+table_data).DataTable({
						"processing": true,
						"serverSide": true,
						"pageLength": 50,
						"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
						"ajax":{
							url :"{{ route('sales-quotation-functions',['id' => 'quotation-list-serverside']) }}",
							type: "POST",  
							data: {status:status_quotation},
							"processing": true,
							"serverSide": true,
							error: function(data){  // error handling
								$('#err').html(JSON.stringify(data));
							}
						},
						columns: [
							{ data: 'DT_RowIndex',orderable: false, searchable: false },
							{ data: 'created_at', name: 'created_at'},
							{ data: 'subject', name: 'subject',visible:false},
							{ data: 'work_nature', name: 'work_nature',visible:false},
							{ data: 'quote_number', name: 'quote_number'},
							{ data: 'client.name', name: 'client.name'},
							{ data: 'grand_total', name: 'grand_total'},
							{ data: 'actions', name: 'actions',orderable: false, searchable: false},
						]
					});
					$('.request-reject-quotation').prop('disabled', false);
				});
			}else{
				$('.request-reject-quotation').prop('disabled', false);
			}
		});
	});
});
</script>
@endsection