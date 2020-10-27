@extends ('layouts.it-department.app')
@section ('title')
    Departments
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item active">Departments</li>
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
                <span class="h5 mt-0">DEPARTMENT SETTINGS</span>
                <br>
                <p class="mb-0">In creating department, please do not add the word <b class="text-dark">DEPARTMENT</b> in department name field.</p>
            </div>
            <div class="col-lg-5 form-group">
                <div class="input-group bg-white shadow-inset-2">
                    <input type="search" id="department-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
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
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">

                <h2>
                    Department List
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

                        {{-- <div class="col-md-12">
                            <form class="" role="form" id="create-department"  method="POST"  action="{{route('settings-functions',['id' => 'add-department'])}}">
                                @csrf()
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-group alert alert-primary mb-4  input-group-multi-transition">
                                            <span class="input-group-text"><i class="ni ni-my-apps"></i></span>
                                            <input required type="text" maxlength="10" class="form-control" name="department_code" placeholder="Department Code"/>
                                            <input required type="text" class="form-control" name="department_name" maxlength="50" placeholder="Department Name..."/>
                                            <div class="input-group-append">
                                                <button  type="submit" action="$('#'+this.id,'disable',true);" id="add-department-btn" class="btn btn-dark waves-themed">ADD DEPARTMENT <i class="fas fa-arrow-right"></i></button>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </form>
                        </div> --}}
                        <div class="col-sm-12">
                            <table id="dt-departments" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                    <tr role="row">
                                        <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" ></th>
                                        <th width="20%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" aria-sort="ascending" >Code</th>
                                        <th width="30%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Department</th>
                                        <th width="20%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2">Date Created</th>
                                        <th width="24%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="4">Action</th>
                                    </tr>
                                    </thead>
                                <tbody>
                                @foreach($departments as $index=>$department)
                                <tr role="row " class="odd">
                                    <td  style="vertical-align: middle" tabindex="0" class="text-center">{{ ($index + 1) }}</td>
                                    <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">{{  $department->code }}</td>
                                    <td style="vertical-align: middle" class="text-center">{{ $department->name }}</td>
                                    <td style="vertical-align: middle" class="small text-center">
                                        {{ readableDate($department->created_at,'readable') }} <br><span class="">Added By:</span> {{  $department->createdBy->username }}
                                    </td>
                                    <td style="vertical-align: middle" class="pb-0">
                                        @php
                                            $enc_department_id = encryptor('encrypt',$department->id);
                                            $positionUrl = route('settings-positions',['did'=>$enc_department_id]);
                                        @endphp
                                        <div class="demo text-center mb-0">
                                            <a href="javascript:void(0);" class="pb-0 btn btn-info btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                                <i class="ni ni-note"></i>
                                            </a>
                                            <a href="{{ $positionUrl }}" class="pb-0 btn btn-success btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="POSITIONS">
                                                <i class="ni ni-user-following"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="pb-0 btn btn-dark btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="EMPLOYEES">
                                                <i class="ni ni-users fs-md"></i>
                                            </a>
                                            <a href="javascript:void(0);" onClick="logsModal('{{ $enc_department_id }}')" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="HISTORY LOGS">
                                                <i class="ni ni-calendar"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="thead-themed">
                                    <tr class="text-center">
                                        <th rowspan="1" colspan="1"></th>
                                        <th rowspan="1" colspan="1">Code</th>
                                        <th rowspan="1" colspan="1">Department</th>
                                        <th rowspan="1" colspan="1">Date Created</th>
                                        <th rowspan="1" colspan="1">Action</th>
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
<div class="modal fade" id="department-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mb-0">
                <h5 class="modal-title"><b> <i class="ni ni-calendar"></i> History Logs </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="feedback"></div>
                    <div class="col-md-12 text-center"  id="logs-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(function(){
            var department_tbl = $('#dt-departments').DataTable({
                responsive: true,
                paging: false,
                sDom: 'lrtip'
            });
           $( "#department-search" ).keyup(function() {
               $("#dt-departments_filter  input[type='search']").val(this.value);
                department_tbl.search(
                    $(this).val(),
                ).draw() ;
            });
        });
        function logsModal(key){
            var url = "{{ route('settings-department-logs-details') }}";
            $('#logs-content').html('');
            $('#logs-content').html('' +
                '<div class="loading mt-6 mb-6">'+
                    '<div class="spinner-grow text-secondary" style="width: 4rem; height: 4rem;" role="status">'+
                        '<span class="sr-only">Loading...</span>'+
                    '</div>'+
                    '<br>'+
                    '<br>'+
                '</div>'+
            '');
            $("#logs-content").load(url+"?did="+key, function () {
                var data = { 'key': key }
                $("#dt-department-logs").dataTable().fnDestroy();
                $('#dt-department-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('settings-functions',['id' => 'logs-departments-details']) }}", // json datasource
                        type: "POST",  // method  , by default get
                        data : data,
                        error: function(result){  // error handling
                            $('#err').html(JSON.stringify(result));
                            $('#feedback').html(JSON.stringify(result));
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex',orderable: false, searchable: false },
                        { data: 'auditable_type', name: 'auditable_type', visible: false},
                        { data: 'user.username', name: 'user.username', visible: false},
                        { data: 'source_model', name: 'source_model',orderable: false, searchable: false},
                        { data: 'event', name: 'event'},
                        { data: 'old_values', name: 'old_values'},
                        { data: 'new_values', name: 'new_values'},
                    ]
                });
            });
            $('#department-logs-modal').modal('show');
        }
    </script>
@endsection
