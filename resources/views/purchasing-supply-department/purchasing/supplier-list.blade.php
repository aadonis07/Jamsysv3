@extends ('layouts.purchasing-supply-department.app')
@section ('title')
    Purchasing | Create P.O
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item "><a href="{{ route('purchasing-supply-list') }}">Purchasing</a></li>
    <li class="breadcrumb-item active">Create P.O</li>
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
                        <span class="h5 mt-0">Create P.O</span>
                        <p class="mb-0">Please select supplier first</p>
                    </div>
                </div>
                <div class="col-lg-5 form-group">
                    <div class="input-group bg-white shadow-inset-2">
                        <input type="search" id="supplier-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search Supplier...">
                        <div class="input-group-append">
                        <span class="input-group-text bg-transparent border-left-0">
                            <i class="fal fa-search"></i>
                        </span>
                        </div>
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
                        <table id="suppliers-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                            <tr role="row">
                                <th width="5%">No</th>
                                <th width="40%">Name</th>
                                <th>Code</th> <!-- hide, for search purposes -->
                                <th width="25%">Contact Person</th>
                                <th width="20%">Contact Number</th>
                                <th>Email</th><!-- hide, for search purposes -->
                                <th width="10%">Action</th>
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
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        var supplerTable = null;
        $(function(){
            supplerTable = $('#suppliers-tbl').DataTable({
                "pageLength": 100,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('purchasing-supply-purchasing-functions',['id' => 'supplier-list']) }}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(result){  // error handling
                        $('#err').html(JSON.stringify(result));
                        $('#feedback').html(JSON.stringify(result));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name'},
                    { data: 'code', name: 'code',visible:false},
                    { data: 'contact_person', name: 'contact_person'},
                    { data: 'contact_number', name: 'contact_number'},
                    { data: 'email', name: 'email',visible:false},
                    { data: 'actions', name: 'actions'}
                ],
                responsive: true,
                sDom: 'lrtip'
            });
            $( "#supplier-search" ).keyup(function() {
                $("#suppliers-tbl  input[type='search']").val(this.value);
                supplerTable.search(
                    $(this).val(),
                ).draw() ;
            });
        });
    </script>
@endsection
