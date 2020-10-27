@extends ('layouts.accounting-department.app')
@section ('title')
    Sales Invoice
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item active">Requested Sales Invoice</li>
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
                <span class="h5 mt-0">Requested Sales Invoice</span>
                <br>
                <p class="mb-0">Quotation must moved before you can see the advance invoice.</p>
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
                    Requested Sales Invoice List
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
								<li class="nav-item  "><a class="nav-link active fs-lg text-primary" data-toggle="tab" href="#pending-tab" role="tab">PENDING</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#serve-tab" role="tab">SERVED</a></li>
							</ul>
						</div>
                        <div class="col-md-12">
						<br>
						<div class="tab-content">
								<div class="tab-pane show active" id="pending-tab" role="tabpanel">
									<div class="table-responsive">
										<table id="dt-si-pending" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
													<th>Quotation Number</th>
													<th>Client</th>
													<th>Contract Amount</th>
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
                                                    <th>agent firstname</th>
                                                    <th>agent lastname</th>
													<th>Action</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="tab-pane show" id="serve-tab" role="tabpanel">
								<div class="table-responsive">
									<table id="dt-si-serve" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
										<thead class="bg-warning-500 text-center">
											<tr role="row">
                                                <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
                                                <th>Quotation Number</th>
                                                <th>Client</th>
                                                <th>Contract Amount</th>
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
<div class="modal fade" id="issue-invoice-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title update_client_modal_title">
                    Issue Advance Invoice
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="issue-invoice-form" onsubmit="submitBtnInvoice.disabled = true;" action="{{ route('accounting-quotation-functions',['id' => 'issue-invoice']) }}">
                    @csrf()
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Invoice Number</label>
                                <input class="form-control" name="quote-id" type="hidden"/>
                                <input class="form-control" id="quote-num" style="text-align:center;font-weight:bold;font-family:Arial Black;" readonly/>
                            </div>
                            <div class="col-md-8">
                                <label>Invoice Date</label>
                                <input class="form-control" name="invoice-date" required type="date"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Invoice Number</label>
                                <input class="form-control" name="invoice-number" onkeypress="return isNumberKey(event)" maxlength="30" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Invoice Amount</label>
                                <div class="input-group mar-btm">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" disabled> Php</button> 
                                    </span> 
                                    <input class="form-control" name="invoice-amount" onkeypress="return isNumberKey(event)" maxlength="20" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="issue-invoice-form" id="submitBtnInvoice" class="btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</div>
<!-- =============================================================================== -->
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script>
 $(function(){
    $('#dt-si-pending').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :"{{ route('accounting-quotation-functions',['id' => 'sales-invoice-serverside']) }}",
            type: "POST",  
            data: {status:"PENDING"},
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
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'grand_total', name: 'grand_total'},
            { data: 'sales_agent.employee.first_name', name: 'sales_agent.employee.first_name',visible:false},
            { data: 'sales_agent.employee.last_name', name: 'sales_agent.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
    $('#dt-si-serve').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :"{{ route('accounting-quotation-functions',['id' => 'sales-invoice-serverside']) }}",
            type: "POST",  
            data: {status:"SERVE"},
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
            { data: 'quote_number', name: 'quote_number'},
            { data: 'client.name', name: 'client.name'},
            { data: 'grand_total', name: 'grand_total'},
            { data: 'sales_agent.employee.first_name', name: 'sales_agent.employee.first_name',visible:false},
            { data: 'sales_agent.employee.last_name', name: 'sales_agent.employee.last_name',visible:false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
 });
$(document).ready(function(index){
	$(document).on('click','.issue-invoice',function(){
        var id = $(this).data('id');
        var quote_num = $(this).data('quote_number');
        $('input[name="quote-id"]').val(id);
        $('#quote-num').val(quote_num);
        $('#issue-invoice-modal').modal('show');
    });
    $(document).on('click','.view-quotation',function(){
		var id = $(this).data('id');
		$('input[name="quotationId"]').val(id);
		$('input[name="actionMode"]').val('view');
		$('#action-form').submit();
	});
});
</script>
@endsection