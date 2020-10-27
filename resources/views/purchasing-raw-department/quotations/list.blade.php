@extends ('layouts.purchasing-raw-department.app')
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
								<li class="nav-item "><a class="nav-link active fs-lg text-success" data-toggle="tab" href="#moved-tab" role="tab">MOVED @php echo countStatusQuotation('success','FOR-APPROVAL'); @endphp</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-info" data-toggle="tab" href="#accounting-approved-tab" role="tab">APPROVED BY ACCOUNTING @php echo countStatusQuotation('info','APPROVED-ACCOUNTING'); @endphp</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-primary" data-toggle="tab" href="#on-processed-tab" role="tab">ON PROCESSED @php echo countStatusQuotation('primary','ON-PROCESS'); @endphp</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#completed-tab" role="tab">COMPLETED @php echo countStatusQuotation('success','COMPLETED'); @endphp</a></li>
                                <li class="nav-item "><a class="nav-link fs-lg text-danger" data-toggle="tab" href="#rejected-tab" role="tab">REJECTED @php echo countStatusQuotation('danger','REJECTED'); @endphp</a></li>
							</ul>
						</div>
                        <div class="col-md-12">
						<br>
						<div class="tab-content">
								<div class="tab-pane show active" id="moved-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-quotation-moved" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
									<thead class="bg-warning-500 text-center">
											<tr role="row">
												<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
												<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
                                                <th>subject</th>
                                                <th>work nature</th>
                                                <th>Quotation Number</th>
												<th>Client</th>
												 
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
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
													<th>subject</th>
													<th>work nature</th>
													<th>Quotation Number</th>
													<th>Client</th>
													 
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
													 
													<th width="15%">Action</th>
												</tr>
											</tfoot>
										</table>
										</div>
									</div>
									<div class="tab-pane show" id="on-processed-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-employees-separated" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
													<th>subject</th>
													<th>work nature</th>
													<th>Quotation Number</th>
													<th>Client</th>
													 
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
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
													<th>subject</th>
													<th>work nature</th>
													<th>Quotation Number</th>
													<th>Client</th>
													 
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
													<th class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Date Created</th>
													<th>subject</th>
													<th>work nature</th>
													<th>Quotation Number</th>
													<th>Client</th>
													 
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
<form id="action-form" method="POST" action="{{ route('purchasing-raw-quotation-functions',['id' => 'action-quotation']) }}">
    @csrf()
	<input class="form-control" name="quotationId" readonly type="hidden" />
	<input class="form-control" name="actionMode" readonly type="hidden" />
</form>
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script>
 $(function(){
 
	$('#dt-quotation-moved').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('purchasing-raw-quotation-functions',['id' => 'quotation-list-serverside']) }}",
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
            { data: 'created_at', name: 'created_at'},
            { data: 'subject', name: 'subject',visible:false},
            { data: 'work_nature', name: 'work_nature',visible:false},
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });

	$('#dt-quotation-approved-accounting').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('purchasing-raw-quotation-functions',['id' => 'quotation-list-serverside']) }}",
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
            { data: 'created_at', name: 'created_at'},
            { data: 'subject', name: 'subject',visible:false},
            { data: 'work_nature', name: 'work_nature',visible:false},
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
	
 });
$(document).ready(function(index){
	$(document).on('click','.view-quotation',function(){
		var id = $(this).data('id');
		$('input[name="quotationId"]').val(id);
		$('input[name="actionMode"]').val('view');
		$('#action-form').submit();
	});
});
</script>
@endsection