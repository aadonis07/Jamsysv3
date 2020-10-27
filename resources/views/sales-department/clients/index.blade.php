@extends ('layouts.sales-department.app')
@section('title')
    Clients
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet">
<style>
    .select2-dropdown {
        z-index: 999999;
    }
    .bootstrap-tagsinput {
        width: 100% !important;
    }
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item active">Clients</li>
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
                    <span class="h5 mt-0">CLIENT SETTINGS</span>
                    <br>
                    <p class="mb-0">Add note here if applicable.</p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12 m-0">
                        <div class="form-group" align="right">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#add-clients-modal"><span class="fa fa-plus"></span> Add Client</button>
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
                    Client List
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
                            <table id="clients-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                <thead class="bg-warning-500">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Client</th>
                                        <th>TIN Number</th>
                                        <th width="15%">Contact Person</th>
                                        <th>Position</th>
                                        <th width="14%">Contact Number</th>
                                        <th width="14%">Email</th>
                                        <th>Address</th>
                                        <th>Zip Code</th>
                                        <th width="12%">Saless Agent</th>
                                        <th>first_name</th>
                                        <th>last_name</th>
                                        <th width="10%">Actions</th>
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
{{--====================================================================================--}}
<div class="modal fade" id="add-clients-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Add Client
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <div class="row">
                    <div class="col-md-12" style="padding-top: 1rem;">
                        <form method="post" id="submit-client-form" action="{{ route('sales-client-functions',['id' => 'add-clients']) }}" enctype="multipart/form-data">
                            @csrf()
                            <div class="form-group row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" required name="client-name" id="client-name">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Contact Person <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" required name="client-contact-person" id="client-contact-person">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Position</label>
                                    <input type="text" class="form-control" name="client-position" id="client-position">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" class="bootstrap-tagsinput" name="client-contact-number" id="client-contact-number">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="text" class="bootstrap-tagsinput" name="client-email" id="client-email">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>TIN Number</label>
                                    <input type="text" class="form-control" name="client-tin-number" id="client-tin-number">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Region <span class="text-danger">*</span></label>
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
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Province <span class="text-danger">*</span></label>
                                    <select class="form-control" id="select-province" required name="select-province">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>City/Municipality <span class="text-danger">*</span></label>
                                    <select class="form-control" id="select-city" required name="select-city">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Barangay <span class="text-danger">*</span></label>
                                    <select class="form-control" id="select-barangay" required name="select-barangay">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Complete Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" required name="client-complete-address" id="client-complete-address">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label id="zip-code-label">Zip Code</label>
                                    <input type="number" class="form-control" name="client-zip-code" id="client-zip-code">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Type of Industry <span class="text-danger">*</span></label>
                                    <select class="form-control" id="select-industry" required name="select-industry">
                                        <option value=""></option>
                                        @foreach($industries as $industry)
                                            @php
                                                $enc_industry_id = encryptor('encrypt', $industry->id);
                                            @endphp
                                            <option value="{{ $enc_industry_id }}">@php echo ucwords($industry->name); @endphp</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="client-business-style" name="client-business-style">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Business Style</label>
                                    <select class="form-control" id="select-business-style" name="select-business-style">
                                        <option value=""></option>
                                        @foreach($business_styles as $business_style)
                                            @php
                                                $enc_business_style_id = encryptor('encrypt', $business_style->id);
                                            @endphp
                                            <option value="{{ $enc_business_style_id }}">@php echo ucwords($business_style->name); @endphp</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- <input type="hidden" name="client_type" id="client_type" value="client"> -->
                        </form>
                    </div>
                </div>
            </div>
           <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="submit-client-form" class="btn btn-warning">Submit</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="update-clients-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title update_client_modal_title">
                    Update
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="update-client-form" action="{{ route('sales-client-functions',['id' => 'update-clients']) }}" enctype="multipart/form-data">
                    @csrf()
                      <div id="update-client-content">
                      </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="update-client-form" class="btn btn-warning">Update</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="client-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(function(){
            $('input[name="client-tin-number"]').mask("999-999-999");
            $('input[name="prospect-tin-number"]').mask("999-999-999");
            $('.bootstrap-tagsinput ').tagsinput({
                tagClass: ' btn btn-info btn-sm btn-tags'
            });

            $('#clients-tbl').DataTable({
                "pageLength": 100,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{route('sales-client-functions',['id' => 'client-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'name', name: 'name'},
                    { data: 'tin_number', name: 'tin_number',visible: false},
                    // { data: 'company_branch.name', name: 'companyBranch.name',visible: false},
                    { data: 'contact_person', name: 'contact_person'},
                    { data: 'position', name: 'position',visible: false},
                    { data: 'contact_numbers', name: 'contact_numbers'},
                    { data: 'emails', name: 'emails'},
                    { data: 'complete_address', name: 'complete_address',visible: false},
                    { data: 'zip_code', name: 'zip_code',visible: false},
                    { data: 'agents', name: 'user.employee.first_name', orderable: false, searchable: false},
                    { data: 'user.employee.first_name', name: 'user.employee.first_name',visible: false},
                    { data: 'user.employee.last_name', name: 'user.employee.last_name',visible: false},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false}]
            });
        });

        function updateClient(id,type){
            console.log(type);
            var path = '{{ route("sales-client-content") }}?id='+id+'&type='+type;
            if(type == 'prospect') {
                $('.update_client_modal_title').empty().html('Update Prospect'+
                    '<small class="m-0 text-muted">'+
                        'Special character is not allowed Except spaces.'+
                    '</small>');
            } else {
                $('.update_client_modal_title').empty().html('Update Client'+
                    '<small class="m-0 text-muted">'+
                        'Special character is not allowed Except spaces.'+
                    '</small>');
            }
            $('#update-client-content').html('');
            $('#update-client-content').html('' +
                '<div class="col-md-12 mt-4">'+
                '    <div class="d-flex justify-content-center">'+
                '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                '            <span class="sr-only">Loading...</span>'+
                '        </div>'+
                '    </div>'+
                '</div>'+
            '');
            $('#update-client-content').load(path, function() {
                $('input[name="client-tin-number-update"]').mask("999-999-999");
                $('.bootstrap-tagsinput ').tagsinput({
                    tagClass: ' btn btn-info btn-sm btn-tags'
                });
                $("#select-branch-update").select2({ 
                    placeholder: "Select Branch",
                    allowClear: true
                });
                $("#select-industry-update").select2({ 
                    placeholder: "Select Industry",
                    allowClear: true
                });
                $("#select-business-style-update").select2({ 
                    placeholder: "Select Business Style",
                    allowClear: true
                });
                $("#select-region-update").select2({ 
                    placeholder: "Select Region",
                    allowClear: true
                });
                $("#select-province-update").select2({ 
                    placeholder: "Select Province",
                    allowClear: true
                });
                $("#select-city-update").select2({ 
                    placeholder: "Select City",
                    allowClear: true
                });
                $("#select-barangay-update").select2({ 
                    placeholder: "Select Barangay",
                    allowClear: true
                });
                $('#select-region-update').on('change', function() {
                    formData = new FormData();
                    formData.append('id', $(this).val());
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("sales-supplier-functions", ["id" => "fetch-provinces"]) }}',
                        data: formData,
                        CrossDomain:true,
                        contentType: !1,
                        processData: !1,
                        success: function(data) {
                            $("#select-province-update").empty().append(data).trigger('change');
                        },
                        error: function(textStatus){
                            console.log(textStatus);
                        }
                    });
                });
                $('#select-province-update').on('change', function() {
                    formData = new FormData();
                    formData.append('id', $(this).val());
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("sales-supplier-functions", ["id" => "fetch-cities"]) }}',
                        data: formData,
                        CrossDomain:true,
                        contentType: !1,
                        processData: !1,
                        success: function(data) {
                            $("#select-city-update").empty().append(data).trigger('change');
                        },
                        error: function(textStatus){
                            console.log(textStatus);
                        }
                    });
                });
                $('#select-city-update').on('change', function() {
                formData = new FormData();
                formData.append('id', $(this).val());
                $.ajax({
                    type: 'POST',
                    url: '{{ route("sales-supplier-functions", ["id" => "fetch-barangays"]) }}',
                    data: formData,
                    CrossDomain:true,
                    contentType: !1,
                    processData: !1,
                    success: function(data) {
                        $("#select-barangay-update").empty().append(data);
                    },
                    error: function(textStatus){
                        console.log(textStatus);
                    }
                });
            });

                $('#client-name-update').on('keyup', function (e) {
                    $('#client-name-update').val($('#client-name-update').val().toUpperCase());
                });

                $('#hide_result_btn_update').on('click', function() {
                    $('#search_result_div_update').hide();
                });

                $('#check_client_update').on("click",function() {
                    $('#search_result_div_update').hide();
                    var client_name = $('#client-name-update').val();
                    if(client_name.length >= 4) {
                        formData = new FormData();
                        formData.append('client_name', client_name);
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("sales-client-functions", ["id" => "check-client-exist"]) }}',
                            data: formData,
                            CrossDomain:true,
                            contentType: !1,
                            processData: !1,
                            success: function(data) {
                                console.log(data);
                                $('#search_result_div_update').show();
                                if(data.length !== 0) {
                                    $('#search_content_update').empty();
                                        $('#search_content_update').empty().append('<br>'+data);
                                } else {
                                    $('#search_content_update').empty().append('<br>'+
                                        '<code>Could not search for '+client_name+' from other Sales Executive</code>');
                                }
                            },
                            error: function(textStatus){
                                console.log(textStatus);
                            }
                        });
                    } else {
                        $('#search_result_div_update').show();
                        $('#search_content_update').empty().append('<br>'+
                            '<code>Client name must be at least 4 characters</code>');
                    }
                });

                $('#select-business-style-update').on('change', function() {
                    var client_value = $('#select-business-style-update option:selected').text();
                    $('#client-business-style-update').val(client_value);
                });
            });
            $('#update-clients-modal').modal('show');
        }

        function logsModal(key){
            var url = "{{ route('sales-client-logs-details') }}";
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
            $("#logs-content").load(url+"?clid="+key, function () {
                var data = { 'key': key }
                $("#dt-client-logs").dataTable().fnDestroy();
                $('#dt-client-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('sales-client-functions',['id' => 'logs-clients-details']) }}", // json datasource
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
            $('#client-logs-modal').modal('show');
        }

        $(document).ready(function(index){
            $('input[name="client-tin-number"]').mask("999-999-999");
            $("#select-branch").select2({ 
                placeholder: "Select Branch",
                allowClear: true
            });
            $("#select-industry").select2({ 
                placeholder: "Select Industry",
                allowClear: true
            });
            $("#select-business-style").select2({ 
                placeholder: "Select Business Style",
                allowClear: true
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
            $("#select-barangay").select2({ 
                placeholder: "Select Barangay",
                allowClear: true
            });
            $('#select-region').on('change', function() {
                formData = new FormData();
                formData.append('id', $(this).val());
                $.ajax({
                    type: 'POST',
                    url: '{{ route("sales-supplier-functions", ["id" => "fetch-provinces"]) }}',
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
                    url: '{{ route("sales-supplier-functions", ["id" => "fetch-cities"]) }}',
                    data: formData,
                    CrossDomain:true,
                    contentType: !1,
                    processData: !1,
                    success: function(data) {
                        $("#select-city").empty().append(data).trigger('change');
                    },
                    error: function(textStatus){
                        console.log(textStatus);
                    }
                });
            });
            $('#select-city').on('change', function() {
                formData = new FormData();
                formData.append('id', $(this).val());
                $.ajax({
                    type: 'POST',
                    url: '{{ route("sales-supplier-functions", ["id" => "fetch-barangays"]) }}',
                    data: formData,
                    CrossDomain:true,
                    contentType: !1,
                    processData: !1,
                    success: function(data) {
                        $("#select-barangay").empty().append(data);
                    },
                    error: function(textStatus){
                        console.log(textStatus);
                    }
                });
            });

            $('#client-name').on('keyup', function (e) {
                $('#client-name').val($('#client-name').val().toUpperCase());
            });

            $('#hide_result_btn').on('click', function() {
                $('#search_result_div').hide();
            });

            $('#check_client').on("click",function() {
                $('#search_result_div').hide();
                var client_name = $('#client-name').val();
                if(client_name.length >= 4) {
                    formData = new FormData();
                    formData.append('client_name', client_name);
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("sales-client-functions", ["id" => "check-client-exist"]) }}',
                        data: formData,
                        CrossDomain:true,
                        contentType: !1,
                        processData: !1,
                        success: function(data) {
                            console.log(data);
                            $('#search_result_div').show();
                            if(data.length !== 0) {
                                $('#search_content').empty();
                                    $('#search_content').empty().append('<br>'+data);
                            } else {
                                $('#search_content').empty().append('<br>'+
                                    '<code>Could not search for '+client_name+' from other Sales Executive</code>');
                            }
                        },
                        error: function(textStatus){
                            console.log(textStatus);
                        }
                    });
                } else {
                    $('#search_result_div').show();
                    $('#search_content').empty().append('<br>'+
                        '<code>Client name must be at least 4 characters</code>');
                }
            });

            $('#select-business-style').on('change', function() {
                var client_value = $('#select-business-style option:selected').text();
                $('#client-business-style').val(client_value);
            });
        });
    </script>
@endsection