@extends ('layouts.sales-department.app')
@section ('title')
    Delivery Schedule
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<style>
.select2-dropdown {
    z-index: 999999;
}
#dt-quotation-products_filter{
    display:none;
}
#dt-quotation-products_length{
    display:none;
}
#dt-quotation-products_paginate{
    display:none;
}
</style>
@endsection 
@section('breadcrumbs')
<li class="breadcrumb-item">Delivery Schedule</li>
<li class="breadcrumb-item active">Create</li>
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
                <span class="h5 mt-0">Create Delivery Schedule for [<b class="text-info">{{$quotation->quote_number}}</b>]</span>
                <br>
                <p class="mb-0">Please estimate/check the availability of products before schedule.</p>
            </div>
            <div class="col-lg-5 form-group">
               
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class=" mb-3 alert alert-primary alert-dismissible">
            <div class="row">
                <div class="col-md-3">
                    <span class="h5 mb-0">Quotation</span>
                    <p class="mb-0">STATUS: {{ $quotation->status }} </p>
                    <p class="mb-0">SUBJECT: {{ $quotation->subject }}</p>
                    <p class="mb-0">WORK NATURE: {{ $quotation->work_nature }}</p>
                    <p class="mb-0">AGENT: {{ $quotation->sales_agent->employee->first_name.' '.$quotation->sales_agent->employee->last_name }}</p>
                    <p class="mb-0">JECAMS ROLE: {{ $quotation->jecams_role }}</p>
                    <p class="mb-0">VALIDITY: {{ readableDate($quotation->validity_date) }}</p>
                    <p class="">LEAD TIME: {{ readableDate($quotation->lead_time) }}</p>
                </div>
                <div class="col-md-3">
                    <span class="h5 mb-0">Client's Information</span>
                    <p class="mb-0">NAME: {{ $quotation->client->name }}</p>
                    <p class="mb-0">TIN: {{ $quotation->client->tin_number }}</p>
                    <span class="h5 mb-0">Contact Details:</span>
                    <p class="mb-0">CONTACT PERSON: {{ $quotation->client->contact_person }}</p>
                    <p class="mb-0">CONTACT NUMBER: {{ $quotation->client->contact_numbers }}</p>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-7">
                            <span class="h5 mb-0">Payments & Delivery</span>
                            <p class="mb-0">VAT TYPE: {{ $quotation->vat_type }}</p>
                            @if($quotation->terms->name)
                                <p class="">PAYMENT TERMS: {{ $quotation->terms->name }}</p>
                            @else
                                <p class="">P. TERMS: ---</p>
                            @endif
                            @if(!empty($quotation->job_request))
                            <span class="h5 mb-0">Job Request's Information</span>
                            <p class="mb-0">JR NUMBER: {{ $quotation->job_request->jr_number }}</p>
                            <p class="mb-0">JR STATUS: {{ $quotation->job_request->status }}</p>
                            @endif
                        </div>
                        <div class="col-md-5">
                            @if($quotation->billing_address == $quotation->shipping_address)
                                <span class="h5 mb-0">Billing & Shipping</span>
                                <p class="mb-0">{{ $quotation->shipping_address }}</p>
                            @else
                                <p class="mb-0">SHIPPING: {{ $quotation->shipping_address }}</p>
                                <p class="mb-0">BILLING: {{ $quotation->shipping_address }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Delivery Details 
                </h2>
            </div>
            <div class="panel-container">
                <div class="panel-content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Delivery Mode</label>
                                <select class="form-control" required name="delivery-mode-schedule">
                                    <option value=""></option>
                                    @foreach($delivery_modes as $index_deliverys=>$delivery_mode)
                                    @php 
                                        $modes = '';
                                        $requirement = '';
                                        if($index_deliverys==$quotation->delivery_mode){
                                            $modes='selected';
                                        }
                                        if($quotation->delivery_mode=='DELIVER'){
                                            $requirement = 'required';
                                        }
                                    @endphp
                                    <option value="{{$index_deliverys}}" {{$modes}}>{{$delivery_mode}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Expected Date</label>
                                <input class="form-control" type="text" name="expected-date" placeholder="YYYY-MM-DD" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Expected Time</label>
                                <input class="form-control" type="time" name="expected-time" required />
                            </div>
                        </div>
                    </div>
                    @if($quotation->delivery_mode=='DELIVER')
                        <div id="for-deliver-content">
                            <br>
                           
                            @if(Branches($quotation->client_id)!='<option value=""></option>') 
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" id="defaultInline1" name="branch-select">
                                        <label class="custom-control-label" for="defaultInline1">Select Branch Address ?</label>
                                    </div>
                                </div>
                                <div class="form-group" id="branch-content" style="display:none;">
                                    <label>Branch</label>
                                    <select class="form-control" name="branch">
                                        @php 
                                            echo Branches($quotation->client_id);
                                        @endphp
                                    </select>
                                </div>
                            @endif 
                            <div class="form-group">
                                <label>Shipping Address</label>
                                <textarea row="5" class="form-control" name="shipping-address" {{$requirement}}>{{$quotation->shipping_address}}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Billing Address</label>
                                <textarea row="5" class="form-control" name="billing-address" {{$requirement}}>{{$quotation->billing_address}}</textarea>
                            </div>
                        </div>
                    @endif
                </div>
            </div>    
        </div>
    </div>
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Quotation Product List 
                </h2>
            </div>
            <div class="panel-container">
                <div class="panel-content">
                    <div class="table-responsive">
                        <table id="dt-quotation-products" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" style="width: 100%;">
                            <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th width="2%">#</th>
                                    <th width="10%">Image</th>
                                    <th width="30%">Product Name</th>
                                    <th>Product Status</th>
                                    <th width="10%">QTY</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-container">
                <div class="panel-content" align="center">
                    <button type="button" class="btn btn-info btn-lg waves-effect" align="Center" data-toggle="modal" data-target="#myModal">Submit Schedule</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ================================================================================== -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Delivery Checklist</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="frame-wrap">
                <div class="demo row" align="center">
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                    <div class="col-md-4 custom-control custom-checkbox custom-checkbox-circle">
                        <input type="checkbox" class="custom-control-input" id="defaultUnchecked">
                        <label class="custom-control-label" for="defaultUnchecked">Gatepass</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success">Submit</button>
        </div>
      </div>
      
    </div>
  </div>
<!-- ================================================================================== -->
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script>
 $(function(){
    $('#dt-quotation-products').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax":{
				url :"{{ route('sales-delivery-functions',['id' => 'product-list-serverside']) }}",
				type: "POST",  
				data : {id:"{{encryptor('encrypt',$quotation->id)}}"},
				"pageLength": 100,
				"processing": true,
				"serverSide": true,
				"lengthMenu": [[5, 10, 15, 20], [5, 10, 15, 20]],
				error: function(data){  // error handling
					$('#err').html(JSON.stringify(data));
				}
			},
			columns: [
                { data: 'DT_RowIndex',orderable: false, searchable: false },
				{ data: 'image', name: 'image',orderable: false, searchable: false},
				{ data: 'product_name', name: 'product_name',orderable: false, searchable: false},
				{ data: 'product_status', name: 'product_status',orderable: false, searchable: false},
                { data: 'qty', name: 'qty',orderable: false, searchable: false},
			]
		});
 });
 $(document).ready(function(){
    var date = new Date();
	date.setDate(date.getDate() - 1);
	$('input[name="expected-date"]')
		.datepicker({
		format: 'yyyy-mm-dd',
		startDate: '+0d',
	});
    $('select[name="delivery-mode-schedule"]').select2({
		placeholder: "Select Delivery Mode",
		allowClear: true,
		width:"100%"
	});
    $(document).on('change','select[name="delivery-mode-schedule"]',function(){
        var delivery_mode = $(this).val();
        if(delivery_mode=='DELIVER'){
            $('#for-deliver-content').show();
            $('textarea[name="shipping-address"]').prop('required',true);
            $('textarea[name="billing-address"]').prop('required',true);
        }else{
            $('#for-deliver-content').hide();
            $('textarea[name="shipping-address"]').prop('required',false);
            $('textarea[name="billing-address"]').prop('required',false);
        }
    });
    $(document).on('click','input[name="branch-select"]',function(){
        if ($(this).is(':checked')) {
            $('#branch-content').show();
            $('select[name="branch"]').prop('required',true);
        }else{
            $('#branch-content').hide();
            $('select[name="branch"]').prop('required',false);
            $('textarea[name="shipping-address"]').val('{{$quotation->shipping_address}}');
            $('textarea[name="billing-address"]').val('{{$quotation->billing_address}}');
        }
    });
    $(document).on('change','select[name="branch"]',function(){
        var id = $(this).val();
        if($.trim(id)){
            $.post("{{ route('sales-quotation-functions', ['id' => 'fetch-branch-details']) }}",
            {id: id,},
            function(data){
                $('textarea[name="shipping-address"]').val(data.client_data.complete_address);
                $('textarea[name="billing-address"]').val(data.client_data.complete_address);
            });
        }else{
            $('textarea[name="shipping-address"]').val('{{$quotation->shipping_address}}');
            $('textarea[name="billing-address"]').val('{{$quotation->billing_address}}');
        }
        
    });
 });
</script>
@endsection