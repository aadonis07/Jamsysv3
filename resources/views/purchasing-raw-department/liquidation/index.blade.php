@extends ('layouts.purchasing-raw-department.app')
@section ('title')
    Liquidation
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item">Liquidation</li>
    <li class="breadcrumb-item active">List</li>
@endsection
@section('content')
    <div class="row mb-3 ">
        <div class="col-lg-12 d-flex flex-start w-100">
            <div class="mr-2 hidden-md-down">
                <span class="icon-stack icon-stack-lg">
                    <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                    <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
                    <i class="ni ni-settings icon-stack-1x opacity-100 color-white" style="font-size: 14px; margin-bottom: 2px;"></i>
                </span>
            </div>
            <div class="d-flex flex-fill">
                <div class="col-md-12">
                    <div class="flex-fill">
                        <span class="h5 mt-0">Liquidation</span>
                        <p class="mb-0">Liquidate All <b>RELEASED</b> payment request</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="panel-1" class="panel">
                <div class="row p-3">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs " role="tablist">
                            <li class="nav-item "><a class="nav-link active fs-sm text-info" data-toggle="tab" href="#for-liquidation-tab" role="tab">FOR LIQUIDATION</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-warning" data-toggle="tab" href="#liquidating-tab" role="tab">LIQUIDATING</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-success" data-toggle="tab" href="#liquidated-tab" role="tab">LIQUIDATED</a></li>
                        </ul>
                    </div>
                    <div class="col-md-12 p-3">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="for-liquidation-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <p class="m-0 mb-2 text-danger">*Note: Listed Below are the <b>RELEASED</b> payment request</p>
                                        <table id="released-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="20%">RELEASED</th>
                                                    <th width="20%">PR Number</th>
                                                    <th width="35%">Details</th>
                                                    {{--                                                <th width="10%">Actions</th>--}}
                                                    <th width="30%">Request</th>
                                                    <th style="display:none">pr number</th>
                                                    <th style="display:none">requested_by</th>
                                                    <th style="display:none">payee</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane show" id="liquidating-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="liquidating-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="20%">RELEASED</th>
                                                <th width="20%">PR Number</th>
                                                <th width="35%">Details</th>
                                                {{--                                                <th width="10%">Actions</th>--}}
                                                <th width="30%">Request</th>
                                                <th style="display:none">pr number</th>
                                                <th style="display:none">requested_by</th>
                                                <th style="display:none">payee</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane show" id="liquidated-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="liquidated-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="20%">RELEASED</th>
                                                <th width="20%">PR Number</th>
                                                <th width="35%">Details</th>
                                                {{--                                                <th width="10%">Actions</th>--}}
                                                <th width="30%">Request</th>
                                                <th style="display:none">pr number</th>
                                                <th style="display:none">requested_by</th>
                                                <th style="display:none">payee</th>
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
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-fullscreen" id="liquidation-details-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> LIQUIDATE [ <text id="pr-number"></text> ] </b>
                        <small class="m-0 text-muted">
                            All receipts or proof of payment must be recorded.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0" style="overflow: auto;">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row" id="liquidate-section">
                                <div class="col-md-6">
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Category</label>
                                        <select class="form-control form-control-sm" id="category" name="category">
                                            <option value="">Choose Category</option>
                                            <option value="OFFICE">OFFICE</option>
                                            <option value="SUPPLIER">SUPPLIER</option>
                                            <option value="CLIENT">CLIENT</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Type</label>
                                        <select class="form-control form-control-sm" id="type" name="type">
                                            <option value="" selected>Choose Type</option>
                                            @foreach(liquidationTypes() as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Payee</label>
                                        <select class="form-control form-control-sm" id="type" name="type">
                                            <option value="" selected>Choose Payee</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Date Collected</label>
                                        <input class="form-control-sm form-control" value="" id="date-collected" name="date_collected"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Reference #</label>
                                        <input class="form-control-sm form-control" value="" id="reference-number" name="reference_number"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Amount</label>
                                        <input type="number" class="form-control-sm form-control" value="" id="amount" name="amount"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Ewt Amount</label>
                                        <input type="number" class="form-control-sm form-control" value="" id="ewt-amount" name="ewt_amount"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Vat</label>
                                        <input type="number" class="form-control-sm form-control" value="" id="vat-amount" name="vat_amount"/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr class="m-0 mt-1 mb-1">
                                    <p class="m-0 text-muted"><b>DETAILS</b></p>
                                    <div class="row">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 border-left">
                            <div class="row mb-2">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <h5 class="m-0">LIQUIDATED</h5>
                                        <p class="m-0">List of liquidated receipts/ proof of payment.</p>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <div class="input-group m-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">&#8369;</span>
                                            </div>
                                            <input type="number" readonly class="form-control" id="total_liquidated" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table id="liquidated-table" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                <thead class="bg-warning-500">
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="45%">Payee</th>
                                        <th width="20%">Date Collected</th>
                                        <th width="30%">Amount</th>
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
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    <script>
        var selected_payee = 0;
        var selected_payee_name = '';
        var selected_payment_request = '';
        var controls = {
            leftArrow: '<i class="fal fa-angle-left" style="font-size: 1.25rem"></i>',
            rightArrow: '<i class="fal fa-angle-right" style="font-size: 1.25rem"></i>'
        }
        $(function(){
            releasedPr();
            liquidatingPr();
            liquidatedPr();
        })
        function releasedPr(){ // for liquidation
            $('#released-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ajax":{
                    url :"{{route('purchasing-raw-liquidation-functions',['id' => 'released-payment-request-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'updated_at',name:'updated_at', orderable: false,searchable: false},
                    { data: 'pr_number_details',name:'pr_number_details',orderable: false,searchable: false},
                    { data: 'details',name:'details.name',orderable: false},
                    { data: 'requested_amount',name:'requested_amount', orderable: false,searchable: false},
                    { data: 'pr_number',name:'pr_number',visible:false},
                    { data: 'requested_by',name:'requested_by',visible:false,searchable: false},
                    { data: 'payee_name',name:'payee_name',visible:false},
                ]
            });
        }
        function liquidatingPr(){ // liquidating
            $('#liquidating-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ajax":{
                    url :"{{route('purchasing-raw-liquidation-functions',['id' => 'liquidating-payment-request-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'updated_at',name:'updated_at', orderable: false,searchable: false},
                    { data: 'pr_number_details',name:'pr_number_details',orderable: false,searchable: false},
                    { data: 'details',name:'details.name',orderable: false},
                    { data: 'requested_amount',name:'requested_amount', orderable: false,searchable: false},
                    { data: 'pr_number',name:'pr_number',visible:false},
                    { data: 'requested_by',name:'requested_by',visible:false,searchable: false},
                    { data: 'payee_name',name:'payee_name',visible:false},
                ]
            });
        }
        function liquidatedPr(){ // liquidated
            $('#liquidated-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ajax":{
                    url :"{{route('purchasing-raw-liquidation-functions',['id' => 'liquidated-payment-request-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'updated_at',name:'updated_at', orderable: false,searchable: false},
                    { data: 'pr_number_details',name:'pr_number_details',orderable: false,searchable: false},
                    { data: 'details',name:'details.name',orderable: false},
                    { data: 'requested_amount',name:'requested_amount', orderable: false,searchable: false},
                    { data: 'pr_number',name:'pr_number',visible:false},
                    { data: 'requested_by',name:'requested_by',visible:false,searchable: false},
                    { data: 'payee_name',name:'payee_name',visible:false},
                ]
            });
        }
        function liquidate(payment_request_key){
            selected_payment_request = payment_request_key;
            $('#liquidated-table').dataTable().fnDestroy();
            $('#liquidated-table tbody').empty();
            var url = "{{ route('purchasing-raw-liquidation-pr-details') }}";
            $('#pr-number').text($('#'+payment_request_key+"-pr-number").val());
            $('#liquidate-section').html('');
            $('#liquidate-section').html('' +
                    '<div class="col-md-12 mt-4">'+
                    '    <div class="d-flex justify-content-center">'+
                    '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                    '            <span class="sr-only">Loading...</span>'+
                    '        </div>'+
                    '    </div>'+
                    '</div>'+
                '');
            $("#liquidate-section").load(url+"?pr="+payment_request_key, function(responseTxt, statusTxt, jqXHR){

                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: controls
                });
                selected_payee = $('#payee_id').val();
                selected_payee_name = $('#payee_name').val();
                $('#total_liquidated').val($('#liquidated-amount').val());
                payees();
                showLiquidated(payment_request_key);
            });
            $('#liquidation-details-modal').modal('show');
        }
        function payees(){
            $("#payee").select2({
                ajax:{
                    type: "POST",
                    url: "{{ route('purchasing-raw-liquidation-functions',['id' => 'payee-list']) }}",
                    //dataType: 'json',
                    delay: 250,
                    data: function(params){
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(result, params)
                    {
                        if(result.success == 1){
                            params.page = params.page || 1;
                            var data = JSON.parse(result.data);
                            return {
                                results: data,
                                pagination:
                                    {
                                        more: (params.page * 5) < data.length
                                    }
                            };
                        }
                    },
                    cache: true
                },
                initSelection : function (element, callback) {
                    if(selected_payee != '' || selected_payee != 0){
                        callback({id:selected_payee,text:selected_payee_name});
                    }
                },
                placeholder: "Select Payee",
                allowClear: true,
                dropdownParent: $('#liquidation-details-modal'),
                escapeMarkup: function(markup)
                {
                    return markup;
                }, // let our custom formatter work
                minimumInputLength: 1,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        }
        function formatRepo(repo){
            if (repo.loading){
                return repo.text;
            }
            var markup = "<div class='select2-result-repository clearfix d-flex'>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title small fs-lg fw-500'> "+ repo.name + "</div>";
            if (repo.created_by.username){
                markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>Added By: " + repo.created_by.username + "</div>";
            }
            return markup;
        }
        function formatRepoSelection(repo){
            if(repo.name){
                selected_payee = repo.id;
                selected_payee_name = repo.name;
                return repo.name;
            }else{
                return repo.text;
            }
        }
        function createLiquidation(btn){
            $(btn).attr('disabled',true);
            confirm_message('Liquidate','Are you sure you want to  liquidate this details ?' , function (confirmed) {
                if(confirmed) {
                    toastMessage('Liquidating..','Validate and creating request....', 'info', 'toast-bottom-right');
                    formData = new FormData();
                    formData.append('payment_request',selected_payment_request);
                    formData.append('payee_key',selected_payee);
                    formData.append('payee_name',selected_payee_name);
                    formData.append('type',$('#type').val());
                    formData.append('date_collected',$('#date-collected').val());
                    formData.append('reference_num',$('#reference-number').val());
                    formData.append('amount',$('#amount').val());
                    formData.append('ewt_amount',$('#ewt-amount').val());
                    formData.append('for_liquidate',$('#need_to_liquidate_amount').val());
                    formData.append('vat_amount',$('#vat-amount').val());
                    formData.append('remarks',$('#remarks').val());
                    if($('#liquidate_po_number').length){
                        formData.append('liquidate_po_number',$('#liquidate_po_number').val());
                    }
                    $.ajax({
                        type: "POST",
                        url: "{{route('purchasing-raw-liquidation-functions',['id' => 'add-to-liquidation'])}}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            if(result.success == 1){
                                alert_message('Create Liquidation',result.message,'success');
                                liquidate(selected_payment_request);
                                toastr.clear();
                                $('#released-tbl').dataTable().fnDestroy();
                                $('#liquidating-tbl').dataTable().fnDestroy();
                                $('#liquidated-tbl').dataTable().fnDestroy();
                                $('#released-tbl tbody').empty();
                                $('#liquidating-tbl tbody').empty();
                                $('#liquidated-tbl tbody').empty();
                                releasedPr();
                                liquidatingPr();
                                liquidatedPr();
                            }else{
                                alert_message('Create Liquidation',result.message,'danger');
                                $(btn).attr('disabled',false);
                                toastr.clear();
                            }
                        },
                        error: function(result){
                            alert_message('Create Liquidation',result.responseText,'danger');
                            $(btn).attr('disabled',false);
                            toastr.clear();
                        }
                    });

                }else{
                    $(btn).attr('disabled',false);
                    toastr.clear();
                }
            });
        }
        function showLiquidated(payment_request){
            $('#liquidated-table').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ajax":{
                    url :"{{route('purchasing-raw-liquidation-functions',['id' => 'liquidation-list'])}}", // json datasource
                    data: {payment_request: payment_request},
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'actions',name:'actions', orderable: false, searchable: false},
                    { data: 'payee_name',name:'payee_name'},
                    { data: 'date_collected',name:'date_collected'},
                    { data: 'amount',name:'amount'}
                ]
            });
        }
        function removeLiquidated(key,btn){
            $(btn).attr('disabled',true);
            confirm_message('Remove Liquidated data','Are you sure you want to remove this details ?' , function (confirmed) {
                if(confirmed) {
                    toastMessage('Removing Liquidation..','Validating and removing request..', 'info', 'toast-bottom-right');
                    formData = new FormData();
                    formData.append('liquidation_key',key);
                    $.ajax({
                        type: "POST",
                        url: "{{route('purchasing-raw-liquidation-functions',['id' => 'remove-to-liquidation'])}}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            if(result.success == 1){
                                alert_message('Create Liquidation',result.message,'success');
                                liquidate(selected_payment_request);
                                toastr.clear();
                                $('#released-tbl').dataTable().fnDestroy();
                                $('#liquidating-tbl').dataTable().fnDestroy();
                                $('#liquidated-tbl').dataTable().fnDestroy();
                                $('#released-tbl tbody').empty();
                                $('#liquidating-tbl tbody').empty();
                                $('#liquidated-tbl tbody').empty();
                                releasedPr();
                                liquidatingPr();
                                liquidatedPr();
                            }else{
                                alert_message('Create Liquidation',result.message,'danger');
                                $(btn).attr('disabled',false);
                                toastr.clear();
                            }
                        },
                        error: function(result){
                            alert_message('Create Liquidation',result.responseText,'danger');
                            $(btn).attr('disabled',false);
                            toastr.clear();
                        }
                    });

                }else{
                    $(btn).attr('disabled',false);
                    toastr.clear();
                }
            });
        }
    </script>
@endsection
