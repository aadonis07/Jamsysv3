@extends ('layouts.proprietor-department.app')
@section ('title')
    Dashboard | Purchasing
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item active">Purchasing</li>
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
                        <span class="h5 mt-0">Purchase Orders</span>
                        <p class="mb-0">List of purchase order. Please update all information listed below.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="panel-1" class="panel">
                <div class="row p-3">
                    <div class="col-md-12 ">
                        <ul class="nav nav-tabs " role="tablist">
                            <li class="nav-item "><a class="nav-link fs-sm text-info" data-toggle="tab" href="#for-approval-tab" role="tab">FOR-APPROVAL</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-dark" data-toggle="tab" href="#approved-tab" role="tab">APPROVED</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-success" data-toggle="tab" href="#completed-tab" role="tab">COMPLETED</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-danger" data-toggle="tab" href="#cancelled-tab" role="tab">CANCELLED / REJECTED</a></li>
                        </ul>
                    </div>
                    <div class="col-md-12 p-3">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="in-progress-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="in-progress-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="20%">Created</th>
                                                <th width="40%">Purchase Order</th>
                                                <th>Supplier</th> <!-- hidden -->
                                                <th>Department</th> <!-- hidden -->
                                                <th width="23%">Amount</th>
                                                <th width="12%">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane " id="pending-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="pending-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="20%">Created</th>
                                                    <th width="40%">Purchase Order</th>
                                                    <th>Supplier</th> <!-- hidden -->
                                                    <th>Department</th> <!-- hidden -->
                                                    <th width="23%">Amount</th>
                                                    <th width="12%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane " id="for-approval-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="for-approval-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="20%">Created</th>
                                                    <th width="40%">Purchase Order</th>
                                                    <th>Supplier</th> <!-- hidden -->
                                                    <th>Department</th> <!-- hidden -->
                                                    <th width="23%">Amount</th>
                                                    <th width="12%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane " id="approved-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="approved-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="20%">Approve details</th>
                                                    <th width="40%">Purchase Order</th>
                                                    <th>Supplier</th> <!-- hidden -->
                                                    <th>Department</th> <!-- hidden -->
                                                    <th width="23%">Amount</th>
                                                    <th width="12%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane " id="completed-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="completed-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="20%">Created</th>
                                                    <th width="40%">Purchase Order</th>
                                                    <th>Supplier</th> <!-- hidden -->
                                                    <th>Department</th> <!-- hidden -->
                                                    <th width="23%">Amount</th>
                                                    <th width="12%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane " id="cancelled-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <table id="cancelled-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                            <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="20%">Created</th>
                                                    <th width="40%">Purchase Order</th>
                                                    <th>Supplier</th> <!-- hidden -->
                                                    <th>Department</th> <!-- hidden -->
                                                    <th width="23%">Amount</th>
                                                    <th width="12%">Actions</th>
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

    <div class="modal fade default-example-modal-right-sm" id="multiple-approval-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-right ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title  mb-0">
                        <b> Multiple Approval </b>
                        <small class="m-0 text-muted">
                            Please check you want to approve P.O
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body  pt-0">
                    <div class="row">
                        <table class="table table-bordered ">
                            <thead class="bg-warning-500">
                                <tr>
                                    <th width="10%">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check-all-forapproval">
                                            <label class="custom-control-label" for="check-all-forapproval">#</label>
                                        </div>

                                    </th>
                                    <th width="90%">
                                        P.O
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check-forapproval">
                                            <label class="custom-control-label" for="check-forapproval">1</label>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">PUR-RM</span> | JEC-RAW-1598102273
                                        <hr class="m-0">
                                        <text class="text-info">By: cess_jecams</text>
                                    </td>
                                </tr>
                                <tr>
                                <tr>
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check-forapproval">
                                            <label class="custom-control-label" for="check-forapproval">1</label>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">PUR-RM</span> | JEC-RAW-1598102273
                                        <hr class="m-0">
                                        <text class="text-info">By: cess_jecams</text>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check-forapproval">
                                            <label class="custom-control-label" for="check-forapproval">1</label>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">PUR-RM</span> | JEC-RAW-1598102273
                                        <hr class="m-0">
                                        <text class="text-info">By: cess_jecams</text>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"   class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm">Approved [ Checked ]</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(function(){
            $('#for-approval-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('proprietor-purchasing-functions',['id' => 'for-approval-po-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'created_at',name:'created_at'},
                    { data: 'po_number',name:'po_number'},
                    { data: 'supplier.name',name:'supplier.name',visible:false},
                    { data: 'department.code',name:'department.code',visible:false},
                    { data: 'grand_total',name:'grand_total', orderable: false, searchable: false},
                    { data: 'actions',name:'actions', orderable: false, searchable: false}
                ],
                'fnDrawCallback': function (oSettings) {
                    $('#for-approval-tbl_length').append('&nbsp;&nbsp;' +
                        '<label class="">| <strong>Multiple:</strong>&nbsp;&nbsp;</label>'+
                        '<button title="Multiple Approval" onClick=forApprovalPO() class="btn btn-success btn-sm "><span class="fas fa-check"></span></button>' +
                    '');
                }
            });
            $('#approved-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('proprietor-purchasing-functions',['id' => 'approved-po-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'updated_at',name:'updated_at'},
                    { data: 'po_number',name:'po_number'},
                    { data: 'supplier.name',name:'supplier.name',visible:false},
                    { data: 'department.code',name:'department.code',visible:false},
                    { data: 'grand_total',name:'grand_total', orderable: false, searchable: false},
                    { data: 'actions',name:'actions', orderable: false, searchable: false}
                ]
            });
            $('#completed-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('proprietor-purchasing-functions',['id' => 'completed-po-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'created_at',name:'created_at'},
                    { data: 'po_number',name:'po_number'},
                    { data: 'supplier.name',name:'supplier.name',visible:false},
                    { data: 'department.code',name:'department.code',visible:false},
                    { data: 'grand_total',name:'grand_total', orderable: false, searchable: false},
                    { data: 'actions',name:'actions', orderable: false, searchable: false}
                ]
            });
            $('#cancelled-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('proprietor-purchasing-functions',['id' => 'cancelled-po-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'created_at',name:'created_at'},
                    { data: 'po_number',name:'po_number'},
                    { data: 'supplier.name',name:'supplier.name',visible:false},
                    { data: 'department.code',name:'department.code',visible:false},
                    { data: 'grand_total',name:'grand_total', orderable: false, searchable: false},
                    { data: 'actions',name:'actions', orderable: false, searchable: false}
                ]
            });
        });
        function updatePOStat(status = null){
            $('#update-p-o-status-modal').modal('show');
        }
        function forApprovalPO(){
            $('#multiple-approval-modal').modal('show');
        }
    </script>
@endsection
