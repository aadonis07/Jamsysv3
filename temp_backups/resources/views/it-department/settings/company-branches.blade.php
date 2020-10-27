@extends ('layouts.it-department.app')
@section ('title')
	Company Branches
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<style>
    .select2-dropdown {
      z-index: 999999;
    }
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item active">Company Branches</li>
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
            <div class="col-md-5 ">
                <div class="flex-fill">
                    <span class="h5 mt-0">COMPANY BRANCH SETTINGS</span>
                    <br>
                    <p class="mb-0">Add note here if applicable.</p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                     <div class="col-md-8 text-right">
                         <div class="input-group bg-white shadow-inset-2">
                             <input type="search" id="company-branches-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                             <div class="input-group-append">
                        <span class="input-group-text bg-transparent border-left-0">
                            <i class="fal fa-search"></i>
                        </span>
                             </div>
                         </div>
                     </div>
                    <div class="col-md-4 m-0">
                        <div class="form-group" align="right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#add-company-branches-modal"><span class="fa fa-plus"></span> Add Company Branch</button>
                        </div>
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
                    Company Branch List
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
                        <div class="col-sm-12">
                            <table id="dt-company-branches" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                    <tr role="row">
                                        <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" >No</th>
                                        <th width="25%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Name</th>
                                        <th width="30%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2">Address</th>
                                        <th width="15%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="3">Zip Code</th>
                                        <th width="24%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="4">Action</th>
                                    </tr>
                                    </thead>
                                <tbody>
                                    @foreach($company_branches as $index => $company_branch)
                                    @php
                                        $enc_company_branch_id = encryptor('encrypt', $company_branch->id);
                                    @endphp
                                    <tr role="row " class="odd">
                                        <td  style="vertical-align: middle" tabindex="0" class="text-center">{{ ($index + 1) }}</td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $company_branch->name }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $company_branch->complete_address }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $company_branch->zip_code }}
                                        </td>
                                        <td style="vertical-align: middle" class="pb-0">
                                            <div class="demo text-center mb-0">
                                                <a  class="pb-0 btn btn-info btn-icon waves-effect waves-themed text-white" onClick="updateCompanyBranch('{{ $enc_company_branch_id }}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                                    <i class="ni ni-note"></i>
                                                </a>
                                                <a href="javascript:void(0);" onClick="logsModal('{{ $enc_company_branch_id }}')" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="HISTORY LOGS">
	                                                <i class="ni ni-calendar"></i>
	                                            </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-themed">
                                    <tr class="text-center">
                                        <th rowspan="1" colspan="1">No</th>
                                        <th rowspan="1" colspan="1">Name</th>
                                        <th rowspan="1" colspan="1">Address</th>
                                        <th rowspan="1" colspan="1">Zip Code</th>
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
{{--====================================================================================--}}
<div class="modal fade" id="add-company-branches-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Add Company Branch
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="submit-company-branch-form" action="{{ route('settings-functions',['id' => 'add-company-branches']) }}" enctype="multipart/form-data">
                    @csrf()
                        <div class="form-group mb-2">
                            <label>Branch Name :</label>
                            <input type="text" class="form-control" required name="company-branch" id="company-branch">
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-control-plaintext" for="select-region">Region :</label>
                            <select class="form-control" id="select-region" required name="select-region">
                                <option value=""></option>
                                @foreach($regions as $region)
                                    @php
                                        $enc_region_id = encryptor('encrypt', $region->id);
                                    @endphp
                                    <option value="{{ $enc_region_id }}">{{ $region->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-control-plaintext" for="select-province">Province :</label>
                            <select class="form-control" id="select-province" required name="select-province">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-control-plaintext" for="select-city">City/Municipality :</label>
                            <select class="form-control" id="select-city" required name="select-city">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Complete Address</label>
                            <input type="text" class="form-control" required name="branch-complete-address" id="branch-complete-address">
                        </div>
                        <div class="form-group">
                            <label>Zip Code :</label>
                            <input type="number" class="form-control" required name="branch-zip-code" id="branch-zip-code">
                        </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" onsubmit="this.disabled=true;" form="submit-company-branch-form" class="btn btn-info">Submit</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="update-company-branches-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Update Company Branch
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="update-company-branch-form" action="{{ route('settings-functions',['id' => 'update-company-branches']) }}" enctype="multipart/form-data">
                    @csrf()
                      <div id="update-company-branches-content">
                      </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="update-company-branch-form" class="btn btn-warning">Update</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="company-branch-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
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
                    <div class="col-md-12 text-center" id="logs-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(function(){
            var company_branch_tbl = $('#dt-company-branches').DataTable({
                responsive: true,
                paging: false,
                sDom: 'lrtip'
            });

            $( "#company-branches-search" ).keyup(function() {
               $("#dt-company-branches_filter  input[type='search']").val(this.value);
                 company_branch_tbl.search(
                    $(this).val(),
                ).draw() ;
            });

            $("#select-region").select2({
                placeholder: "Select Region",
                allowClear: true
            });

            $("#select-province").select2({
                placeholder: "Select Province",
                allowClear: true
            });

            $("#select-city").select2({
                placeholder: "Select City",
                allowClear: true
            });

            $('#select-region').on('change', function() {
                formData = new FormData();
                formData.append('id', $(this).val());
                $.ajax({
                    type: 'POST',
                    url: '{{ route("supplier-functions", ["id" => "fetch-provinces"]) }}',
                    data: formData,
                    CrossDomain:true,
                    contentType: !1,
                    processData: !1,
                    success: function(data) {
                        $("#select-province").empty().append(data).trigger('change');
                    },
                    error: function(textStatus){
                        console.log(textStatus);
                    }
                });
            });

            $('#select-province').on('change', function() {
                formData = new FormData();
                formData.append('id', $(this).val());
                $.ajax({
                    type: 'POST',
                    url: '{{ route("supplier-functions", ["id" => "fetch-cities"]) }}',
                    data: formData,
                    CrossDomain:true,
                    contentType: !1,
                    processData: !1,
                    success: function(data) {
                        $("#select-city").empty().append(data);
                    },
                    error: function(textStatus){
                        console.log(textStatus);
                    }
                });
            });
        });

        function updateCompanyBranch(id){
            var path = '{{ route("company-branch-content") }}?id='+id;
            $('#update-company-branches-content').html('');
            $('#update-company-branches-content').html('' +
                '<div class="col-md-12 mt-4">'+
                '    <div class="d-flex justify-content-center">'+
                '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                '            <span class="sr-only">Loading...</span>'+
                '        </div>'+
                '    </div>'+
                '</div>'+
            '');
            $('#update-company-branches-content').load(path);
            $('#update-company-branches-modal').modal('show');
        }

        function logsModal(key){
            var url = "{{ route('settings-company-branch-logs-details') }}";
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
            $("#logs-content").load(url+"?cbid="+key, function () {
                var data = { 'key': key }
                $("#dt-company-branch-logs").dataTable().fnDestroy();
                $('#dt-company-branch-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('settings-functions',['id' => 'logs-company-branches-details']) }}", // json datasource
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
            $('#company-branch-logs-modal').modal('show');
        }

        $(document).ready(function() {
            
        });
    </script>
@endsection
