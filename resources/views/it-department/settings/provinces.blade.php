@extends ('layouts.it-department.app')
@section ('title')
    {{ $region->description }} | Provinces
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<style>
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
<li class="breadcrumb-item">
    <a class="text-info" href="{{ route('settings-regions') }}">Regions</a>
</li>
<li class="breadcrumb-item">{{ $region->description }}</li>
<li class="breadcrumb-item">Provinces</li>
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
                    <span class="h5 mt-0">{{ $region->description }} PROVINCES</span>
                    <br>
                    <p class="mb-0">Add note here if applicable.</p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                     <div class="col-md-8 text-right">
                         <div class="input-group bg-white shadow-inset-2">
                             <input type="search" id="provinces-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                             <div class="input-group-append">
                        <span class="input-group-text bg-transparent border-left-0">
                            <i class="fal fa-search"></i>
                        </span>
                             </div>
                         </div>
                     </div>
                    <div class="col-md-4 m-0">
                        <div class="form-group" align="right">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#add-provinces-modal"><span class="fa fa-plus"></span> Add Province</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php
    $enc_region_id = encryptor('encrypt', $region->id);
@endphp
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Province List
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
                            <table id="dt-provinces" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                    <tr role="row">
                                        <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" >No</th>
                                        <th width="20%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Name</th>
                                        <th width="13%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2">PSGC Code</th>
                                        <th width="13%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="3">Province Code</th>
                                        <th width="13%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="4">Delivery Charge</th>
                                        <th width="11%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="5">Status</th>
                                        <th width="24%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="6">Action</th>
                                    </tr>
                                    </thead>
                                <tbody>
                                    @foreach($region->provinces as $index => $province)
                                    @php
                                        $enc_province_id = encryptor('encrypt', $province->id);
                                        if($province->is_enable == 1){
                                            $province_status = 'ACTIVE';
                                            $toggleStatusLabel = 'INACTIVE';
                                        }else{
                                            $province_status = 'INACTIVE';
                                            $toggleStatusLabel = 'ACTIVE';
                                        }
                                        $cityUrl = route('settings-cities',['pid'=>$enc_province_id]);
                                    @endphp
                                    <tr role="row " class="odd">
                                        <td  style="vertical-align: middle" tabindex="0" class="text-center">{{ ($index + 1) }}</td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $province->description }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $province->psgc_code }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            {{ $province->province_code }}
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            @php
                                                $delivery_charge = "-";
                                                if($province->delivery_charge != "") {
                                                    $delivery_charge = number_format($province->delivery_charge, 2);
                                                }
                                                echo $delivery_charge;
                                            @endphp
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center" id="dynamicProvinceStatus{{ $enc_province_id }}">
                                            {{ $province_status }}
                                        </td>
                                        <td style="vertical-align: middle" class="pb-0">
                                            <div class="demo text-center mb-0">
                                                <a  class="pb-0 btn btn-info btn-icon waves-effect waves-themed text-white" onClick="updateProvince('{{ $enc_province_id }}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                                    <i class="ni ni-note"></i>
                                                </a>
                                                <a href="{{ $cityUrl }}" class="pb-0 btn btn-danger btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="CITIES">
                                                    <i class="fal fa-map-marker-alt"></i>
                                                </a>
                                                <a href="javascript:void(0);" onClick="logsModal('{{ $enc_province_id }}')" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="HISTORY LOGS">
                                                    <i class="ni ni-calendar"></i>
                                                </a>
                                                <label class="switch" data-toggle="tooltip" data-placement="top" title="" data-original-title="Change to {{ $toggleStatusLabel }} ?" id="dynamicProvinceStatusLabel{{ $enc_province_id }}" style="vertical-align: middle;">
                                                <input type="checkbox" class="trigger" onChange="updateProvinceStatus('{{ $enc_province_id }}')" @if($province_status == 'ACTIVE') checked @endif>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-themed">
                                    <tr class="text-center">
                                        <th rowspan="1" colspan="1">No</th>
                                        <th rowspan="1" colspan="1">Name</th>
                                        <th rowspan="1" colspan="1">PSGC Code</th>
                                        <th rowspan="1" colspan="1">Province Code</th>
                                        <th rowspan="1" colspan="1">Delivery Charge</th>
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
<div class="modal fade" id="add-provinces-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Add Province
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="submit-province-form" action="{{ route('settings-functions',['id' => 'add-provinces']) }}" enctype="multipart/form-data">
                    @csrf()
                    <div class="form-group">
                        <label>Province Name :</label>
                        <input type="text" class="form-control" required name="province-name" id="province-name">
                    </div>
                    <div class="form-group">
                        <label>PSGC Code :</label>
                        <input type="text" class="form-control" required name="psgc-code" id="psgc-code">
                    </div>
                    <div class="form-group">
                        <label>Province Code :</label>
                        <input type="text" class="form-control" required name="province-code" id="province-code">
                    </div>
                    <div class="form-group">
                        <label>Delivery Charge :</label>
                        <input type="number" class="form-control" name="delivery-charge" id="delivery-charge">
                    </div>
                    <input type="hidden" name="region-id" value="{{ $enc_region_id }}">
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" onsubmit="this.disabled=true;" form="submit-province-form" class="btn btn-warning">Submit</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="update-provinces-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Update Province
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="update-province-form" action="{{ route('settings-functions',['id' => 'update-provinces']) }}" enctype="multipart/form-data">
                    @csrf()
                      <div id="update-provinces-content">
                      </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="update-province-form" class="btn btn-warning">Update</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="province-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
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
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(function(){
            var province_tbl = $('#dt-provinces').DataTable({
                responsive: true,
                paging: false,
                sDom: 'lrtip'
            });

            $( "#provinces-search" ).keyup(function() {
               $("#dt-provinces_filter  input[type='search']").val(this.value);
                 province_tbl.search(
                    $(this).val(),
                ).draw() ;
            });
        });

        function updateProvince(id){
            var path = '{{ route("province-content") }}?id='+id;
            $('#update-provinces-content').html('');
            $('#update-provinces-content').html('' +
                '<div class="col-md-12 mt-4">'+
                '    <div class="d-flex justify-content-center">'+
                '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                '            <span class="sr-only">Loading...</span>'+
                '        </div>'+
                '    </div>'+
                '</div>'+
            '');
            $('#update-provinces-content').load(path);
            $('#update-provinces-modal').modal('show');
        }

        function updateProvinceStatus(id){
            formData = new FormData();
            formData.append('id',id);
            $.ajax({
                type: "POST",
                url: "{{ route('settings-functions',['id' => 'province-status']) }}",
                data: formData,
                CrossDomain:true,
                contentType: !1,
                processData: !1,
                success: function(e) {
                    if(e == 1) {
                        var toggleLabel = 'INACTIVE';
                        var tdValue = 'ACTIVE';
                    } else {
                        var toggleLabel = 'ACTIVE';
                        var tdValue = 'INACTIVE';
                    }
                    document.getElementById("dynamicProvinceStatus"+id).innerHTML = tdValue;
                    const label = document.querySelector('#dynamicProvinceStatusLabel'+id);
                    label.dataset.originalTitle = "Change to "+toggleLabel+" ?";
                },
                error: function(result){
                    alert('error');
                }
            });
        }

        function logsModal(key){
            var url = "{{ route('settings-province-logs-details') }}";
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
            $("#logs-content").load(url+"?pid="+key, function () {
                var data = { 'key': key }
                $("#dt-province-logs").dataTable().fnDestroy();
                $('#dt-province-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('settings-functions',['id' => 'logs-provinces-details']) }}", // json datasource
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
            $('#province-logs-modal').modal('show');
        }
    </script>
@endsection
