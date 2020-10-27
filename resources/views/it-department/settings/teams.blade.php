@extends ('layouts.it-department.app')
@section ('title')
    Teams
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet">
<style>
    .bootstrap-tagsinput {
        width: 100% !important;
    }
    .select2-dropdown {
      z-index: 999999;
    }
    .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    }

    .switch input { 
    opacity: 0;
    width: 0;
    height: 0;
    }

    .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }

    input:checked + .slider {
    background-color: #2196F3;
    }

    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }

    .slider.round:before {
    border-radius: 50%;
    }
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item active">Teams</li>
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
            <div class="flex-fill">
                <span class="h5 mt-0">TEAM SETTINGS</span>
                <br>
                <p class="mb-0">Add note here if applicable.</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Team List
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group bg-white shadow-inset-2">
                                    <input type="search" id="teams-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-transparent border-left-0">
                                            <i class="fal fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6" align="right">
                                <div class="form-group" align="right">
                                    <button class="btn btn-success" data-toggle="modal" data-target="#add-teams-modal"><span class="fa fa-plus"></span> Add Team</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="dt-teams" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                    <tr role="row">
                                        <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" >No</th>
                                        <th width="12%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Name</th>
                                        <th width="12%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Display Name</th>
                                        <th width="10%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2" aria-sort="ascending" >Branch</th>
                                        <th width="15%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="3">Telephone</th>
                                        <th width="15%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="4">Manager</th>
                                        <th width="10%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="5">Status</th>
                                        <th width="20%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="6">Action</th>
                                    </tr>
                                    </thead>
                                <tbody>
                                    @foreach($teams as $index => $team)
                                    @php
                                        if($team->status == 'INACTIVE'){
                                            $team_status = 'ACTIVE';
                                        }else{
                                            $team_status = 'INACTIVE';
                                        }
                                        if($team->team_manager == NULL && $team->team_manager_id == NULL) {
                                            $team_manager = 'Not yet assigned';
                                            $disabled = "disabled";
                                        } else {
                                            $team_manager = $team->team_manager;
                                            $disabled = "";
                                        }
                                        $telephone_numbers = explode(',',$team->telephone);
                                        $telephone = '';
                                        if($telephone_numbers){
                                            foreach($telephone_numbers as $telephone_number){
                                                $telephone .='<span title="'.$telephone_number.'" class="badge badge-primary">'.$telephone_number.'</span> &nbsp;';
                                            }
                                        }
                                        $enc_team_id = encryptor('encrypt', $team->id);
                                    @endphp
                                    <tr role="row " class="odd">
                                        <td  style="vertical-align: middle" tabindex="0" class="text-center">{{ ($index + 1) }}</td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $team->name }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $team->display_name }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $team->branch }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            @php echo $telephone; @endphp
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $team_manager }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center" id="dynamicTeamStatus{{ $enc_team_id }}">
                                            {{ $team->status }}
                                        </td>
                                        <td style="vertical-align: middle" class="pb-0">
                                            <div class="demo text-center mb-0">
                                                <a class="pb-0 btn btn-info btn-icon waves-effect waves-themed text-white" onClick="updateTeam('{{ $enc_team_id }}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                                    <i class="ni ni-note"></i>
                                                </a>
                                                @if($team->team_manager != NULL && $team->team_manager_id != NULL)
                                                    <a class="pb-0 btn btn-danger btn-icon waves-effect waves-themed text-white" onClick="changeTeamManager('{{ $enc_team_id }}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="CHANGE TEAM MANAGER">
                                                        <i class="fal fa-sync"></i>
                                                    </a>
                                                @endif
                                                <a href="javascript:void(0);" onClick="logsModal('{{ $enc_team_id }}')" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="HISTORY LOGS">
                                                    <i class="ni ni-calendar"></i>
                                                </a>
                                                <!-- <label class="switch" data-toggle="tooltip" data-placement="top" title="" data-original-title="Change to {{$team_status}} ?" id="dynamicTeamStatusLabel{{ $enc_team_id }}" style="
                                                vertical-align: middle;">
                                                <input type="checkbox" class="trigger" onChange="updateTeamStatus('{{ $enc_team_id }}')" @if($team->status == 'ACTIVE') checked @endif {{ $disabled }}>
                                                    <span class="slider round"></span>
                                                </label> -->
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-themed">
                                    <tr class="text-center">
                                        <th rowspan="1" colspan="1">No</th>
                                        <th rowspan="1" colspan="1">Name</th>
                                        <th rowspan="1" colspan="1">Display Name</th>
                                        <th rowspan="1" colspan="1">Branch</th>
                                        <th rowspan="1" colspan="1">Telephone</th>
                                        <th rowspan="1" colspan="1">Manager</th>
                                        <th rowspan="1" colspan="1">Status</th>
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
<div class="modal fade" id="add-teams-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Add Team
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="submit-team-form" action="{{ route('settings-functions',['id' => 'add-teams']) }}" enctype="multipart/form-data">
                    @csrf()
                        <div class="form-group">
                            <label>Team Name :</label>
                            <input type="text" class="form-control" required name="team-name" id="team-name">
                        </div>
                        <div class="form-group">
                            <label>Display Name :</label>
                            <input type="text" class="form-control" required name="display-name" id="display-name">
                        </div>
                        <div class="form-group">
                            <label>Select Branch :</label>
                            <select class="form-control" id="select-branch" required name="select-branch">
                                <option value="">Select Branch</option>
                                <option value="QUEZON-CITY">Quezon City</option>
                                <option value="MAKATI">Makati</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Telephone :</label>
                            <input type="text" class="bootstrap-tagsinput" required name="team-telephone" id="team-telephone">
                        </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="submit-team-form" class="btn btn-warning">Submit</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="update-teams-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Update Team
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="update-team-form" action="{{ route('settings-functions',['id' => 'update-teams']) }}" enctype="multipart/form-data">
                    @csrf()
                      <div id="update-team-content">
                      </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="update-team-form" class="btn btn-warning">Update</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="team-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
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
{{--====================================================================================--}}
<div class="modal fade" id="change-team-managers-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Change Team Manager
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="change-team-manager-form" action="{{ route('settings-functions',['id' => 'change-team-manager']) }}" enctype="multipart/form-data">
                    @csrf()
                        <div id="change-team-manager-content">
                        </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="change-team-manager-form" class="btn btn-warning">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection
<!-- gelo start -->
@section('scripts')
    <script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>
    <script>
        $(function(){
            var team_tbl = $('#dt-teams').DataTable({
                responsive: true,
                paging: false,
                sDom: 'lrtip'
            });

            $( "#teams-search" ).keyup(function() {
               $("#dt-teams_filter  input[type='search']").val(this.value);
                 team_tbl.search(
                    $(this).val(),
                ).draw() ;
            });

            $('.bootstrap-tagsinput ').tagsinput({
                tagClass: ' btn btn-info btn-sm btn-tags'
            });
        });

        function updateTeam(id){
            var path = '{{ route("team-content") }}?id='+id;
            $('#update-team-content').html('');
            $('#update-team-content').html('' +
                '<div class="col-md-12 mt-4">'+
                '    <div class="d-flex justify-content-center">'+
                '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                '            <span class="sr-only">Loading...</span>'+
                '        </div>'+
                '    </div>'+
                '</div>'+
            '');
            $('#update-team-content').load(path, function() {
                $('.bootstrap-tagsinput ').tagsinput({
                    tagClass: ' btn btn-info btn-sm btn-tags'
                });
            });
            $('#update-teams-modal').modal('show');
        }

        function updateTeamStatus(id){
            formData = new FormData();

            formData.append('id',id);
            $.ajax({
                type: "POST",
                url: "{{ route('settings-functions',['id' => 'teams-status']) }}",
                data: formData,
                CrossDomain:true,
                contentType: !1,
                processData: !1,
                success: function(e) {
                    document.getElementById("dynamicTeamStatus"+id).innerHTML = e;
                    if(e == 'ACTIVE') {
                        var toggleLabel = 'INACTIVE';
                    } else {
                        var toggleLabel = 'ACTIVE';
                    }
                    const label = document.querySelector('#dynamicTeamStatusLabel'+id);
                    label.dataset.originalTitle = "Change to "+toggleLabel+" ?";
                    
                },
                error: function(result){
                    alert('error');
                }
            });
        }

        function logsModal(key){
            var url = "{{ route('settings-team-logs-details') }}";
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
            $("#logs-content").load(url+"?tid="+key, function () {
                var data = { 'key': key }
                $("#dt-team-logs").dataTable().fnDestroy();
                $('#dt-team-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('settings-functions',['id' => 'logs-teams-details']) }}", // json datasource
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
            $('#team-logs-modal').modal('show');
        }

        function changeTeamManager(id){
            var path = '{{ route("change-team-manager-content") }}?id='+id;
            $('#change-team-manager-content').html('');
            $('#change-team-manager-content').html('' +
                '<div class="col-md-12 mt-4">'+
                '    <div class="d-flex justify-content-center">'+
                '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                '            <span class="sr-only">Loading...</span>'+
                '        </div>'+
                '    </div>'+
                '</div>'+
            '');
            $('#change-team-manager-content').load(path, function() {
                $('.bootstrap-tagsinput ').tagsinput({
                    tagClass: ' btn btn-info btn-sm btn-tags'
                });

                $('#select-manager-change').select2({
                    placeholder: "Select Sales Manager",
                    allowClear: true
                });
            });
            $('#change-team-managers-modal').modal('show');
        }
    </script>
@endsection
<!-- gelo end -->
