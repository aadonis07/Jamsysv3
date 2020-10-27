@extends ('layouts.accounting-department.app')
@section ('title')
    Collection Schedule List
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Collections</li>
<li class="breadcrumb-item active">Collection List</li>
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
                <span class="h5 mt-0">Collection Schedule</span>
                <br>
                <p class="mb-0">This quotations is already moved by the agent.</p>
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
                    Collection Schedule List
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
								<li class="nav-item  "><a class="nav-link active fs-lg text-primary" data-toggle="tab" href="#forcollection-tab" role="tab">FOR COLLECTION</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-warning" data-toggle="tab" href="#partial-tab" role="tab">PARTIALLY PAID</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#fullypaid-tab" role="tab">FULLY PAID</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-danger" data-toggle="tab" href="#cancelled-tab" role="tab">CANCELLED</a></li>
							</ul>
						</div>
                        <div class="col-md-12">
						<br>
						<div class="tab-content">
								<div class="tab-pane show active" id="forcollection-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-for-collection" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
                                                    <th>Expected Collection Amount</th>
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
													<th>Action</th>
												</tr>
												</thead>
											<tbody>
										   
											</tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
                                                    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
                                                    <th>Expected Collection Amount</th>
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
													<th>Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="partial-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-partial" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                            <thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
                                                    <th>Expected Collection Amount</th>
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
													<th>Action</th>
												</tr>
												</thead>
											<tbody>
										   
											</tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
                                                    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
                                                    <th>Expected Collection Amount</th>
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
													<th>Action</th>
												</tr>
											</tfoot>
									</table>
									</div>
								</div>
                                <div class="tab-pane show" id="fullypaid-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-fullypaid" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                        <thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
                                                    <th>Expected Collection Amount</th>
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
													<th>Action</th>
												</tr>
												</thead>
											<tbody>
										   
											</tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
                                                    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
                                                    <th>Expected Collection Amount</th>
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
													<th>Action</th>
												</tr>
											</tfoot>
									</table>
									</div>
								</div>
                                <div class="tab-pane show" id="cancelled-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-cancelled" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                            <thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
                                                    <th>Expected Collection Amount</th>
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
													<th>Action</th>
												</tr>
												</thead>
											<tbody>
										   
											</tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
                                                    <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
                                                    <th>Expected Collection Amount</th>
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
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
<!-- =============================================================================== -->
<form id="action-form" method="POST" action="{{ route('accounting-quotation-functions',['id' => 'action-quotation']) }}">
    @csrf()
	<input class="form-control" name="quotationId" readonly type="hidden" />
	<input class="form-control" name="actionMode" readonly type="hidden" />
</form>
<!-- =============================================================================== -->
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script>
 $(function(){
    $('#dt-for-collection').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :"{{ route('accounting-collection-functions',['id' => 'collection-list-serverside']) }}",
            type: "POST",  
            data: {status:"FOR-COLLECTION"},
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
            { data: 'quotation.quote_number', name: 'quotation.quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.grand_total', name: 'quotation.grand_total'},
            { data: 'expected_amount', name: 'expected_amount',orderable: false, searchable: false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name',visible:false},
            { data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
    $('#dt-cancelled').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :"{{ route('accounting-collection-functions',['id' => 'collection-list-serverside']) }}",
            type: "POST",  
            data: {status:"CANCELLED"},
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
            { data: 'quotation.quote_number', name: 'quotation.quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.grand_total', name: 'quotation.grand_total'},
            { data: 'expected_amount', name: 'expected_amount',orderable: false, searchable: false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name',visible:false},
            { data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
    $('#dt-fullypaid').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :"{{ route('accounting-collection-functions',['id' => 'collection-list-serverside']) }}",
            type: "POST",  
            data: {status:"FULLY-PAID"},
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
            { data: 'quotation.quote_number', name: 'quotation.quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.grand_total', name: 'quotation.grand_total'},
            { data: 'expected_amount', name: 'expected_amount',orderable: false, searchable: false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name',visible:false},
            { data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
    $('#dt-partial').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :"{{ route('accounting-collection-functions',['id' => 'collection-list-serverside']) }}",
            type: "POST",  
            data: {status:"PARTIAL"},
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
            { data: 'quotation.quote_number', name: 'quotation.quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'quotation.grand_total', name: 'quotation.grand_total'},
            { data: 'expected_amount', name: 'expected_amount',orderable: false, searchable: false},
            { data: 'agent.user.employee.first_name', name: 'agent.user.employee.first_name',visible:false},
            { data: 'agent.user.employee.last_name', name: 'agent.user.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
 });
 $(document).ready(function(index){

 });
</script>
@endsection