@extends ('layouts.accounting-department.app')
@section ('title')
    Collection View
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
.dataTables_filter{
    display:none;
}
.dataTables_length{
    display:none;
}
.dataTables_info{
    display:none;
}
.dataTables_paginate{
    display:none;
}
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Collections</li>
<li class="breadcrumb-item">Collection List</li>
<li class="breadcrumb-item active">Collection View</li>
@endsection
@section('content')
<div class="row mb-3 ">
    <div class="col-lg-12 d-flex flex-start w-100 mb-2">
        <div class="mr-2 hidden-md-down">
            <span class="icon-stack icon-stack-lg">
                <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
            </span>
        </div>
        <div class="row d-flex flex-fill">
            <div class="col-lg-7 flex-fill">
                <span class="h5 mt-0">Collection View</span>
                <br>
                <p class="mb-0">This is Collection view. quotation is already moved. <b class="text-danger">Note :</b> Please check the Payment Terms.</p>
            </div>
            <div class="col-lg-5 form-group">
                
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-container show">
                <div class="panel-content">
                    @php 
                        $balance = floatval($collection->quotation->grand_total)-floatval($collection->collected_amount);
                    @endphp
                        <div class=" mb-3 alert alert-primary alert-dismissible">
                            <div class="row">
                                <div class="col-md-3">
                                    <span class="h5 mb-0">Quotation</span>
                                    <p class="mb-0"><text class="text-dark"><b>[ {{ $collection->quotation->quote_number }} ]</b></text> {{ $collection->quotation->subject }}</p>
                                    <p class="mb-0">STATUS: {{ $collection->quotation->status }}</p>
                                    <p class="mb-0">AGENT: {{ $collection->agent->user->employee->first_name." ".$collection->agent->user->employee->last_name }}</p>
                                    <p class="mb-0">CONTACT AMOUNT: PHP {{number_format($collection->quotation->grand_total,2)}}</p>
                                   
                                </div>
                                <div class="col-md-3">
                                    <span class="h5 mb-0">Client's Information</span>
                                    <p class="mb-0">NAME: {{ $collection->client->name }}</p>
                                    <p class="mb-0"">TIN: {{ $collection->client->tin_number }}</p>
                                    @if(!empty($collection->client_po_number))
                                    <p class="mb-0">CLIENT PO NUMBER: <b class="text-info">{{ $collection->client_po_number }}</b></p>
                                    <p class="">CLIENT TYPE: <b>Company</b></p>
                                    @else 
                                    <p class="">CLIENT TYPE: <b>Personal/Individual</b></p>
                                    @endif
                                    <span class="h5 mb-0">Contact Details:</span>
                                    <p class="mb-0">CONTACT PERSON: {{ $collection->client->contact_person }}</p>
                                    <p class="mb-0">CONTACT NUMBER: {{ $collection->client->contact_numbers }}</p>
                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="h5 mb-0">Collection Information</span>
                                            <p class="mb-0">VAT TYPE: {{ $collection->quotation->vat_type }}</p>
                                            <p class="mb-0">PAYMENT TERMS: {{ $collection->terms }}</p>
                                            <p class="mb-0">DELIVERY MODE: {{ $collection->quotation->delivery_mode }} @ {{ readableDate($collection->quotation->lead_time) }}</p>
                                            <p class="mb-0">BALANCE : <b class="text-danger">PHP {{number_format($balance,2)}}</b></p>
                                        </div>
                                        <div class="col-md-5">
                                            @if($collection->quotation->billing_address == $collection->quotation->shipping_address)
                                                <span class="h5 mb-0">Billing & Shipping</span>
                                                <p class="mb-0">{{ $collection->quotation->shipping_address }}</p>
                                            @else
                                                <p class="mb-0">SHIPPING: {{ $collection->quotation->shipping_address }}</p>
                                                <p class="mb-0">BILLING: {{ $collection->quotation->shipping_address }}</p>
                                            @endif
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
<div class="row">
    <div class="col-lg-4">
        <div id="panel-1" class="panel">
            <div class="panel-hdr bg-fusion-50">
                <h2 class="text-white">
                   Add Schedule Collection  
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <form id="collection-form" method="POST" onsubmit="submitBtn.disabled = true;" action="{{ route('accounting-collection-functions',['id' => 'insert-collection']) }}">
                        @csrf()
                        @if($balance>0)
                        <div class="form-group">
                            <label>Collector</label>
                            <select class="form-control" name="collector">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Collection Date</label>
                            <input class="form-control" name="collection-date" type="date" required />
                        </div>
                        <div class="form-group">
                            <label>Payment Mode</label>
                            <select class="form-control" name="payment-mode" required>
                                <option value="">--Select Payment Mode--</option>
                                @foreach($payment_modes as $index=>$payment_mode)
                                <option value="{{$index}}" >{{$payment_mode}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="form-group" id="check-content" style="display:none;">
                            <div class="input-group input-group-multi-transition">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Check Number</span>
                                </div>
                                <input type="text" class="form-control" name="check-number" onkeypress="return isNumberKey(event)" aria-label="Check Number" placeholder="Check Number">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Check Date</span>
                                </div>
                                <input class="form-control" type="date" aria-label="Check Date" name="check-date" />
                            </div>
                        </div>
                        <div class="form-group" id="cash-content" style="display:none;">
                            <div class="input-group input-group-multi-transition">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">PHP</span>
                                </div>
                                <input type="text" class="form-control" name="amount" value="{{number_format($balance,2)}}" onkeypress="return isNumberKey(event)" aria-label="Amount" placeholder="Amount">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">PHP</span>
                                </div>
                                <input type="text" class="form-control" name="with-held-amount" onkeypress="return isNumberKey(event)" aria-label="With Held Amount" placeholder="With Held Amount">
                            </div><br class="m-0">
                            <div class="input-group input-group-multi-transition">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">PHP</span>
                                </div>
                                <input type="text" class="form-control" name="other-amount" onkeypress="return isNumberKey(event)" aria-label="Other Amount" placeholder="Other Amount">
                            </div>
                        </div>
                        <div class="form-group" id="online-content" style="display:none;">
                            <label>Bank</label>
                            <select class="form-control" name="bank">
                                <option value=""></option>
                                @foreach($banks as $bank)
                                <option value="{{$bank->id}}">{{$bank->name}}</option>
                                @endforeach
                            </select> 
                        </div>
                        <div id="for-document" style="display:none;">
                            <div class="form-group">
                                <label>Document</label>
                                <select class="form-control" name="document">
                                    <option value=""></option>
                                    @foreach($papers as $paper) 
                                    <option value="{{$paper->id}}" data-paper_type="{{$paper->type}}">{{$paper->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">PHP</span>
                                    </div>
                                    <input type="text" class="form-control" name="document-amount" onkeypress="return isNumberKey(event)" placeholder="Amount">
                                </div>
                            </div>
                        </div>
                        <div id="for-invoice" style="display:none;">
                            <br>
                            <div class="form-group">
                                <label>Reference Number</label>
                                <input type="text" class="form-control" name="reference-number" onkeypress="return isNumberKey(event)" aria-label="Reference Number" placeholder="Reference Number">
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input class="form-control" type="date" name="reference-date" />
                            </div>
                        </div>
                        <input class="form-control" type="hidden" name="collection-id" value="{{encryptor('encrypt',$collection->id)}}"/>
                    </form>
                </div>
                <div class="panel-content py-2 rounded-bottom border-faded border-left-0 border-right-0 border-bottom-0 text-muted" align="right">
                   <button type="submit" class="btn btn-success btn-standard waves-effect" id="submitBtn" form="collection-form"><span>SUBMIT</span></button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Collection Information
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="table-responsive">
                        <table id="dt-collection" class="table table-bordered w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th width="30%">Date Created</th>
                                    <th width="40%">Collected Amount</th>
                                    <th>Bank</th>
                                    <th  width="30%">Check Details</th>
                                    <th>Status</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                        </table>
                        <table class="table table-bordered">
                            <tr @if($collection->status=='FULLY-PAID') class="table-success" @else class="table-danger" @endif>
                                <th style="text-align:right;">TOTAL COLLECTED :</th>
                                <th style="text-align:right;">
                                @php 
                                    $total_collected_amount = floatval($collection->quotation->grand_total) - floatval($balance);
                                    echo 'PHP '.number_format($total_collected_amount,2);
                                @endphp
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
    </div>
    <div class="col-lg-8">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Collection Documents
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="table-responsive">
                        <table id="dt-documents" class="table table-bordered w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th>Date Created</th>
                                    <th>Document</th>
                                    <th>Amount</th>
                                    <th>Reference Number</th>
                                    <th>Reference Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- ================================================================================ -->
<div class="modal fade" id="verify-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title update_client_modal_title">
                    Are you sure ? <br>
                    <small>This will verify the collection detail selected.</small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="verify-form" onsubmit="submitBtnVerify.disabled = true;" action="{{ route('accounting-collection-functions',['id' => 'verify-collection']) }}">
                    @csrf()
                    <div class="form-group">
                        <label>Date Collected</label>
                        <input class="form-control" name="collected-date" required type="date"/>
                        <input class="form-control" name="collection-detail-id" required type="hidden"/>
                    </div>
                    <div id="payment-mode-content">
                    </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="verify-form" id="submitBtnVerify" class="btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</div>
<!-- ================================================================================ -->
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script>
$(function(){
    $('#dt-collection').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :"{{ route('accounting-collection-functions',['id' => 'collection-serverside']) }}",
            type: "POST",  
            data: {id:"{{encryptor('encrypt',$collection->id)}}"},
            "pageLength": 100,
            "processing": true,
            "serverSide": true,
            "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at',orderable: false},
            { data: 'collected_amount', name: 'collected_amount',orderable: false},
            { data: 'bank_details', name: 'bank_details',orderable: false},
            { data: 'cheque_details', name: 'cheque_details',orderable: false},
            { data: 'status', name: 'status',orderable: false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
    $('#dt-documents').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :"{{ route('accounting-collection-functions',['id' => 'documents-serverside']) }}",
            type: "POST",  
            data: {id:"{{encryptor('encrypt',$collection->id)}}"},
            "pageLength": 100,
            "processing": true,
            "serverSide": true,
            "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at',orderable: false},
            { data: 'document.name', name: 'document.name',orderable: false},
            { data: 'amount_paid', name: 'amount_paid',orderable: false},
            { data: 'reference_number', name: 'reference_number',orderable: false},
            { data: 'reference_date', name: 'reference_date',orderable: false},
            { data: 'status', name: 'status',orderable: false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
});
$(document).ready(function(){
    $('select[name="payment-mode"]').select2({
		placeholder: "Select Payment Mode",
		allowClear: true,
		width:"100%"
	});
    $('select[name="bank"]').select2({
		placeholder: "Select Bank",
		allowClear: true,
		width:"100%"
	});
    $('select[name="document"]').select2({
		placeholder: "Select Document",
		allowClear: true,
		width:"100%"
	});
    $('select[name="collector"]').select2({
		placeholder: "Select Collector",
		allowClear: true,
		width:"100%"
	});
    $(document).on('change','select[name="payment-mode"]',function(){
        var payment_mode = $(this).val();
        if(payment_mode=='CASH'){
            $('#cash-content').show();
            $('input[name="amount"]').prop('required',true);
            $('input[name="other-amount"]').prop('required',true);
            $('input[name="with-held-amount"]').prop('required',true);
            $('#online-content').hide();
            $('select[name="bank"]').prop('required',false);
            $('#check-content').hide();
            $('input[name="check-number"]').prop('required',false);
            $('input[name="check-date"]').prop('required',false);
            $('#for-document').hide();
            $('select[name="document"]').prop('required',false);
            $('input[name="document-amount"]').prop('required',false);
            $('#for-invoice').hide();
            $('input[name="reference-number"]').prop('required',false);
            $('input[name="reference-date"]').prop('required',false);
            $('input[name="collection-date"]').prop('required',true);
        }else if(payment_mode=='ONLINE'){
            $('#online-content').show();
            $('#cash-content').show();
            $('#check-content').hide();
            $('input[name="amount"]').prop('required',true);
            $('input[name="other-amount"]').prop('required',true);
            $('input[name="with-held-amount"]').prop('required',true);
            $('select[name="bank"]').prop('required',true);
            $('input[name="check-number"]').prop('required',false);
            $('input[name="check-date"]').prop('required',false);
            $('#for-document').hide();
            $('select[name="document"]').prop('required',false);
            $('input[name="document-amount"]').prop('required',false);
            $('#for-invoice').hide();
            $('input[name="reference-number"]').prop('required',false);
            $('input[name="reference-date"]').prop('required',false);
            $('input[name="collection-date"]').prop('required',true);
        }else if(payment_mode=='CHECK'){
            $('#check-content').show();
            $('#online-content').show();
            $('#cash-content').show();
            $('input[name="amount"]').prop('required',true);
            $('input[name="other-amount"]').prop('required',true);
            $('input[name="with-held-amount"]').prop('required',true);
            $('input[name="check-number"]').prop('required',true);
            $('input[name="check-date"]').prop('required',true);
            $('select[name="bank"]').prop('required',true);
            $('#for-document').hide();
            $('select[name="document"]').prop('required',false);
            $('input[name="document-amount"]').prop('required',false);
            $('#for-invoice').hide();
            $('input[name="reference-number"]').prop('required',false);
            $('input[name="reference-date"]').prop('required',false);
            $('input[name="collection-date"]').prop('required',true);
        }else{
            $('#check-content').hide();
            $('#cash-content').hide();
            $('#online-content').hide();
            $('input[name="amount"]').prop('required',false);
            $('input[name="with-held-amount"]').prop('required',false);
            $('input[name="other-amount"]').prop('required',false);
            $('input[name="check-number"]').prop('required',false);
            $('input[name="check-date"]').prop('required',false);
            $('select[name="bank"]').prop('required',false);
            $('input[name="collection-date"]').prop('required',false);
            $('#for-document').show();
            $('select[name="document"]').prop('required',true);
            $('input[name="document-amount"]').prop('required',true);
        }
        
    });

    $(document).on('change','select[name="upayment-mode"]',function(){
        var payment_mode = $(this).val();
        if(payment_mode=='CASH'){
            $('#ucash-content').show();
            $('input[name="uamount"]').prop('required',true);
            $('input[name="uother-amount"]').prop('required',true);
            $('input[name="uwith-held-amount"]').prop('required',true);
            $('#uonline-content').hide();
            $('select[name="ubank"]').prop('required',false);
            $('#ucheck-content').hide();
            $('input[name="ucheck-number"]').prop('required',false);
            $('input[name="ucheck-date"]').prop('required',false);
        }else if(payment_mode=='ONLINE'){
            $('#uonline-content').show();
            $('#ucash-content').show();
            $('#ucheck-content').hide();
            $('input[name="uamount"]').prop('required',true);
            $('input[name="uother-amount"]').prop('required',true);
            $('input[name="uwith-held-amount"]').prop('required',true);
            $('select[name="ubank"]').prop('required',true);
            $('input[name="ucheck-number"]').prop('required',false);
            $('input[name="ucheck-date"]').prop('required',false);
        }else{
            $('#ucheck-content').show();
            $('#uonline-content').show();
            $('#ucash-content').show();
            $('input[name="uamount"]').prop('required',true);
            $('input[name="uother-amount"]').prop('required',true);
            $('input[name="uwith-held-amount"]').prop('required',true);
            $('input[name="ucheck-number"]').prop('required',true);
            $('input[name="ucheck-date"]').prop('required',true);
            $('select[name="ubank"]').prop('required',true);
        }
        
    });

    $(document).on('click','.verify-collection',function(){
        var id = $(this).data('id');
        $('input[name="collection-detail-id"]').val(id);
        $('#payment-mode-content').html('' +
                '<div class="col-md-12 mt-4">'+
                '    <div class="d-flex justify-content-center">'+
                '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                '            <span class="sr-only">Loading...</span>'+
                '        </div>'+
                '    </div>'+
                '</div>'+
            '');
        var path = '{{route("accounting-collection-payment-mode")}}?id='+id;
        $('#payment-mode-content').load(path,function(){
            $('select[name="upayment-mode"]').select2({
                placeholder: "Select Payment Mode",
                allowClear: true,
                width:"100%"
            });
        });
        $('#verify-modal').modal('show');
    });

    $(document).on('click','.bounce-check',function(){
        var id = $(this).data('id');
        Swal.fire({
            title: 'Confirm Bounce Check',
            text: "Are you sure you want set bounce check this collection detail ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, It was bounced check'
        }).then((result) => {
            if (result.value) {
                $.post("{{ route('accounting-collection-functions', ['id' => 'bounce-check-collection']) }}",
                {id: id},
                function(data){
                    if(data.success==1){
                        alert_message('Success',data.message,'success');
                        location.reload();
                    }else{
                        $(this).prop('disabled', false);
                    }
                });
            }else{
                $(this).prop('disabled', false);
            }
        });
    });

    $(document).on('click','.change-status',function(){
        var id = $(this).data('id');
        var status = $(this).data('status');
        Swal.fire({
            title: 'Confirm '+status,
            text: "Are you sure you want set "+status+" this document ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $.post("{{ route('accounting-collection-functions', ['id' => 'change-status-document']) }}",
                {id: id,status:status},
                function(data){
                    if(data.success==1){
                        alert_message('Success',data.message,'success');
                        location.reload();
                    }else{
                        $(this).prop('disabled', false);
                    }
                });
            }else{
                $(this).prop('disabled', false);
            }
        });
    });

    $(document).on('click','.void-collection',function(){
        var id = $(this).data('id');
        Swal.fire({
            title: 'Confirm Bounce Check',
            text: "Are you sure you want set VOID this collection detail ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, It VOID this'
        }).then((result) => {
            if (result.value) {
                $.post("{{ route('accounting-collection-functions', ['id' => 'void-collection']) }}",
                {id: id},
                function(data){
                    if(data.success==1){
                        alert_message('Success',data.message,'success');
                        location.reload();
                    }else{
                        $(this).prop('disabled', false);
                    }
                });
            }else{
                $(this).prop('disabled', false);
            }
        });
    });

    $(document).on('change','select[name="document"]',function(){
        var document_type = $(this).find(':selected').data('paper_type');
        if(document_type=='INVOICE'){
            $('#for-invoice').show();
            $('input[name="reference-number"]').prop('required',true);
            $('input[name="reference-date"]').prop('required',true);
        }else{
            $('#for-invoice').hide();
            $('input[name="reference-number"]').prop('required',false);
            $('input[name="reference-date"]').prop('required',false);
        }
    });

    if({{$balance}}<=0){
        $('select[name="payment-mode"]').val('DOCUMENT').trigger('change');
        $('#check-content').hide();
        $('#cash-content').hide();
        $('#online-content').hide();
        $('input[name="amount"]').prop('required',false);
        $('input[name="with-held-amount"]').prop('required',false);
        $('input[name="other-amount"]').prop('required',false);
        $('input[name="check-number"]').prop('required',false);
        $('input[name="check-date"]').prop('required',false);
        $('select[name="bank"]').prop('required',false);

        $('#for-document').show();
        $('select[name="document"]').prop('required',true);
        $('input[name="document-amount"]').prop('required',true);
    }
});
</script>
@endsection