@extends ('layouts.it-department.app')
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
                <div class="col-md-7">
                    <div class="flex-fill">
                        <span class="h5 mt-0">Purchase Orders</span>
                        <p class="mb-0">List of purchase order. Please update all information listed below.</p>
                    </div>
                </div>
                <div class="col-md-5 ">
                    <div class="form-group" align="right">
                        <a href="{{ route('purchasing-supplier-list') }}" class="btn btn-primary"><span class="fa fa-plus"></span> Create P.O</a>
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
                            <li class="nav-item  "><a class="nav-link active fs-sm text-warning" data-toggle="tab" href="#in-progress-tab" role="tab">IN-PROGRESS</a></li>
                            <li class="nav-item "><a class="nav-link fs-sm text-primary" data-toggle="tab" href="#pending-tab" role="tab">PENDING</a></li>
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
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <span class="h5 mt-0">Payment request enabled.</span>
                                                <p class="mb-0 text-muted">For may payment status, please see legends for more info.</p>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-6 p-1">
                                                        <h6 class="m-0"> <span class="fas text-muted fa-dot-circle"></span> FOR-REQUEST</h6>
                                                    </div>
                                                    <div class="col-md-6 p-1">
                                                        <h5 class="m-0"><span class="fas text-info fa-dot-circle"></span> REQUESTED</h5>
                                                    </div>
                                                    <div class="col-md-6 p-1">
                                                        <h5 class="m-0"><span class="fas text-warning fa-dot-circle"></span> PARTIAL</h5>
                                                    </div>
                                                    <div class="col-md-6 p-1">
                                                        <h5 class="m-0"><span class="fas text-success fa-dot-circle"></span> COMPLETED</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 p-2">
                                        <table id="approved-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
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
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(function(){
            $('#in-progress-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('purchasing-functions',['id' => 'in-progress-po-list'])}}", // json datasource
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
            $('#pending-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('purchasing-functions',['id' => 'pending-po-list'])}}", // json datasource
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
            $('#for-approval-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('purchasing-functions',['id' => 'for-approval-po-list'])}}", // json datasource
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
            $('#approved-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('purchasing-functions',['id' => 'approved-po-list'])}}", // json datasource
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
            $('#completed-tbl').DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('purchasing-functions',['id' => 'completed-po-list'])}}", // json datasource
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
                    url :"{{route('purchasing-functions',['id' => 'cancelled-po-list'])}}", // json datasource
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
    </script>
@endsection
