@extends ('layouts.accounting-department.app')
@section ('title')
    Payment Request
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item">Payment Request</li>
    <li class="breadcrumb-item active">List</li>
@endsection
@section('content')
    @php
        $type = 'CASH';
        $headerTitle = 'CASH / PETTY CASH';
        if(isset($_GET['type']) && $_GET['type'] == 'cheques' ){
            $type = 'CHEQUES';
            $headerTitle = 'CHEQUE';
        }
    @endphp
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
                <div class="col-md-7">
                    <div class="flex-fill">
                        <span class="h5 mt-0">{{ $headerTitle }} Payment Request</span>
                        <p class="mb-0">List of Payment Request. Please update all information listed below.</p>
                    </div>
                </div>
                <div class="col-md-5 ">
                    <div class="form-group" align="right">
                        <a href="{{ route('accounting-payment-request-create') }}" class="btn btn-info"><span class="fa fa-plus"></span> Create P.R</a>
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
                            <li class="nav-item "><a class="nav-link active fs-sm text-primary" data-toggle="tab" href="#pending-tab" role="tab">PENDING</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-info" data-toggle="tab" href="#for-approval-tab" role="tab">FOR-APPROVAL</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-dark" data-toggle="tab" href="#approved-tab" role="tab">APPROVED</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-success" data-toggle="tab" href="#released-tab" role="tab">RELEASED</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-danger" data-toggle="tab" href="#rejected-tab" role="tab">REJECTED</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-danger" data-toggle="tab" href="#cancelled-tab" role="tab">CANCELLED / VOID</a></li>
                        </ul>
                    </div>
                    <div class="col-md-12 p-3">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="pending-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="pending-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="20%">Created</th>
                                                <th width="20%">PR Number</th>
                                                <th width="35%">Details</th>
{{--                                                <th width="10%">Actions</th>--}}
                                                <th style="display:none">Request</th>
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
                            <div class="tab-pane show" id="for-approval-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="for-approval-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="20%">Created</th>
                                                <th width="20%">PR Number</th>
                                                <th width="35%">Details</th>
                                                {{--                                                <th width="10%">Actions</th>--}}
                                                <th style="display:none">Request</th>
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
                            <div class="tab-pane show" id="approved-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="approved-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="20%">Created</th>
                                                <th width="20%">PR Number</th>
                                                <th width="35%">Details</th>
                                                {{--                                                <th width="10%">Actions</th>--}}
                                                <th style="display:none">Request</th>
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
                            <div class="tab-pane show" id="released-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="released-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="20%">Released</th>
                                                <th width="20%">PR Number</th>
                                                <th width="35%">Details</th>
                                                {{--                                                <th width="10%">Actions</th>--}}
                                                <th style="display:none">Request</th>
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
                            <div class="tab-pane show" id="rejected-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="rejected-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="20%">Rejected</th>
                                                    <th width="20%">PR Number</th>
                                                    <th width="35%">Particular</th>
                                                    <th width="20%">R. Amount</th>
                                                    {{--                                                <th width="10%">Actions</th>--}}
                                                    <th style="display:none">requested by</th>
                                                    <th style="display:none">account title</th>
                                                    <th style="display:none">category</th>
                                                    <th style="display:none">type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane show" id="cancelled-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="cancelled-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="20%">Created</th>
                                                    <th width="20%">PR Number</th>
                                                    <th width="35%">Particular</th>
                                                    <th width="20%">Status</th>
                                                    {{--                                                <th width="10%">Actions</th>--}}
                                                    <th style="display:none">requested by</th>
                                                    <th style="display:none">account title</th>
                                                    <th style="display:none">category</th>
                                                    <th style="display:none">type</th>
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
    <div class="modal fade" id="partial-list-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Partial Payment Details </b>
                        <small class="m-0 text-muted">
                            Listed is partial payments with their stats.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="partial-payment-table" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                <thead class="bg-warning-500">
                                <tr>
                                    <th width="5%"></th>
                                    <th width="60%">Partial Amount</th>
                                    <th style="display:none">PR</th>
                                    <th width="35%">Status</th>
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
    <script>
        $('#pending-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax":{
                url :"{{route('accounting-payment-request-functions',['id' => 'pending-payment-request-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                data : { type: '{{ $type }}' },
                error: function(data){  // error handling
                    $('#err').html(JSON.stringify(data));
                }
            },
            columns: [
                { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                { data: 'created_at',name:'created_at', orderable: false,searchable: false},
                { data: 'pr_number_details',name:'pr_number_details',orderable: false,searchable: false},
                { data: 'details',name:'details.name',orderable: false},
                { data: 'requested_amount',name:'requested_amount', orderable: false,searchable: false},
                { data: 'pr_number',name:'pr_number',visible:false},
                { data: 'requested_by',name:'requested_by',visible:false,searchable: false},
                { data: 'payee_name',name:'payee_name',visible:false},
            ]
        });
        $('#for-approval-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax":{
                url :"{{route('accounting-payment-request-functions',['id' => 'for-approval-payment-request-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                data : { type: '{{ $type }}' },
                error: function(data){  // error handling
                    $('#err').html(JSON.stringify(data));
                }
            },
            columns: [
                { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                { data: 'created_at',name:'created_at', orderable: false,searchable: false},
                { data: 'pr_number_details',name:'pr_number_details',orderable: false,searchable: false},
                { data: 'details',name:'details.name',orderable: false},
                { data: 'requested_amount',name:'requested_amount', orderable: false,searchable: false},
                { data: 'pr_number',name:'pr_number',visible:false},
                { data: 'requested_by',name:'requested_by',visible:false,searchable: false},
                { data: 'payee_name',name:'payee_name',visible:false},
            ]
        });
        $('#approved-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax":{
                url :"{{route('accounting-payment-request-functions',['id' => 'approved-payment-request-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                data : { type: '{{ $type }}' },
                error: function(data){  // error handling
                    $('#err').html(JSON.stringify(data));
                }
            },
            columns: [
                { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                { data: 'created_at',name:'created_at', orderable: false,searchable: false},
                { data: 'pr_number_details',name:'pr_number_details',orderable: false,searchable: false},
                { data: 'details',name:'details.name',orderable: false},
                { data: 'requested_amount',name:'requested_amount', orderable: false,searchable: false},
                { data: 'pr_number',name:'pr_number',visible:false},
                { data: 'requested_by',name:'requested_by',visible:false,searchable: false},
                { data: 'payee_name',name:'payee_name',visible:false},
            ]
        });
        $('#released-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax":{
                url :"{{route('accounting-payment-request-functions',['id' => 'released-payment-request-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                data : { type: '{{ $type }}' },
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
        $('#rejected-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax":{
                url :"{{route('accounting-payment-request-functions',['id' => 'rejected-payment-request-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                data : { type: '{{ $type }}' },
                error: function(data){  // error handling
                    $('#err').html(JSON.stringify(data));
                }
            },
            columns: [
                { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                { data: 'updated_at',name:'updated_at'},
                { data: 'pr_number',name:'pr_number'},
                { data: 'account_title_particular.name',name:'accountTitleParticular.name'},
                { data: 'requested_amount',name:'requested_amount'},
                //{ data: 'actions',name:'actions', orderable: false, searchable: false},
                { data: 'requested_by',name:'requested_by',visible:false},
                { data: 'account_title.name',name:'accountTitle.name',visible:false},
                { data: 'category',name:'category',visible:false},
                { data: 'type',name:'type',visible:false}
            ]
        });
        $('#cancelled-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax":{
                url :"{{route('accounting-payment-request-functions',['id' => 'cancelled-payment-request-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                data : { type: '{{ $type }}' },
                error: function(data){  // error handling
                    $('#err').html(JSON.stringify(data));
                }
            },
            columns: [
                { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                { data: 'created_at',name:'created_at'},
                { data: 'pr_number',name:'pr_number'},
                { data: 'account_title_particular.name',name:'accountTitleParticular.name'},
                { data: 'status',name:'status'},
                //{ data: 'actions',name:'actions', orderable: false, searchable: false},
                { data: 'requested_by',name:'requested_by',visible:false},
                { data: 'account_title.name',name:'accountTitle.name',visible:false},
                { data: 'category',name:'category',visible:false},
                { data: 'type',name:'type',visible:false}
            ]
        });
        function showPartial(key){
            $('#partial-payment-table').dataTable().fnDestroy();
            $('#partial-payment-table tbody').empty();
            $('#partial-payment-table').DataTable({
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('accounting-payment-request-functions',['id' => 'partial-payment-list']) }}", // json datasource
                    type: "POST",  // method  , by default get
                    data : { payment_request: key },
                    error: function(result){  // error handling
                        $('#err').html(JSON.stringify(result));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'amount', name: 'amount'},
                    { data: 'payment_request.pr_number', name: 'payment_request.pr_number',visible: false},
                    { data: 'status', name: 'status'},
                ]
            });
            $('#partial-list-modal').modal('show')
        }
    </script>
@endsection
