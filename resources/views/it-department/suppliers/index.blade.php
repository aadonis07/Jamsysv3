@extends ('layouts.it-department.app')
@section('title')
    Suppliers
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
    .zoom-in:hover {
        -ms-transform: scale(4.5); /* IE 9 */
        -webkit-transform: scale(4.5); /* Safari 3-8 */
        transform: scale(4.5);
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
<li class="breadcrumb-item active">Suppliers</li>
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
                    <span class="h5 mt-0">Supplier SETTINGS</span>
                    <br>
                    <p class="mb-0">Add note here if applicable.</p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                     <div class="col-md-8 text-right">
                         <div class="input-group bg-white shadow-inset-2">
                             <input type="search" id="suppliers-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                             <div class="input-group-append">
                        <span class="input-group-text bg-transparent border-left-0">
                            <i class="fal fa-search"></i>
                        </span>
                             </div>
                         </div>
                     </div>
                    <div class="col-md-4 m-0">
                        <div class="form-group" align="right">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#add-suppliers-modal"><span class="fa fa-plus"></span> Add Supplier</button>
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
                    Supplier List
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
                            <table id="suppliers-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                    <tr role="row">
                                        <th width="5%">No</th>
                                        <th width="35%">Name</th>
                                            <th>Category</th> <!-- hide, for search purposes -->
                                            <th>Code</th> <!-- hide, for search purposes -->
                                            <th>Tin Number</th> <!-- hide for search purposes -->
                                        <th width="20%">Contact Person</th>
                                        <th width="20%">Contact Number</th>
                                        <th>Email</th><!-- hide, for search purposes -->
                                        <th width="15%">Action</th>
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
<div class="modal fade" id="add-suppliers-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Add Supplier
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="submit-supplier-form" action="{{ route('supplier-functions',['id' => 'add-suppliers']) }}" enctype="multipart/form-data">
                    @csrf()
                        <div class="form-group row mb-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Supplier Name :</label>
                                <input type="text" class="form-control" required name="supplier-name" id="supplier-name">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Code :</label>
                                <input type="text" class="form-control" required name="supplier-code" id="supplier-code">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Category :</label>
                                <select class="form-control" id="select-category" required name="select-category">
                                    <option value="">Select Category</option>
                                    <option value="GOODS">GOODS</option>
                                    <option value="SERVICES">SERVICES</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Type of Industry :</label>
                                <select class="form-control" id="select-industry" required name="select-industry">
                                    <option value=""></option>
                                    @foreach($industries as $industry)
                                    @php
                                        $industry_name = ucwords($industry->name)
                                    @endphp
                                    <option value="{{ $industry->id }}">{{ $industry_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Contact Person :</label>
                                <input type="text" class="form-control" required name="supplier-contact-person" id="supplier-contact-person">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Contact Number :</label>
                                <input type="text" class="bootstrap-tagsinput" name="supplier-contact-number" id="supplier-contact-number" >
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Email :</label>
                                <input type="text" class="form-control" required name="supplier-email" id="supplier-email">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>VAT Type :</label>
                                <select class="form-control" required name="select-vat">
                                    <option value="1">VAT Inclusive</option>
                                    <option selected value="0">VAT Exclusive</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Region :</label>
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
                                <label>Province :</label>
                                <select class="form-control" id="select-province" required name="select-province">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>City/Municipality :</label>
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
                                <label>Complete Address</label>
                                <input type="text" class="form-control" required name="supplier-complete-address" id="supplier-complete-address">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>TIN Number :</label>
                                <input type="text" class="form-control" required name="supplier-tin-number" id="supplier-tin-number">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Payment Type :</label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input supplier-payment-type-radio" id="DatedCheckRadioBtn" required name="supplier-payment-type" value="DATED-CHECKS">
                                    <label class="custom-control-label" for="DatedCheckRadioBtn">Dated Check</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input supplier-payment-type-radio" id="CODRadioBtn" required name="supplier-payment-type" value="COD">
                                    <label class="custom-control-label" for="CODRadioBtn">COD</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input supplier-payment-type-radio" id="WithTermsRadioBtn" required name="supplier-payment-type" value="WITH-TERMS">
                                    <label class="custom-control-label" for="WithTermsRadioBtn">WITH TERMS</label>

                                    <div id="hidden_terms_input" class="input-group input-group-sm" style="margin-top: -27px;margin-left: 89px; width: 36%;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>Remarks :</label>
                                <textarea class="form-control" name="supplier-remarks" id="supplier-remarks"></textarea>
                            </div>
                        </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="submit-supplier-form" class="btn btn-warning">Submit</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="update-suppliers-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Update Supplier
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="update-supplier-form" action="{{ route('supplier-functions',['id' => 'update-suppliers']) }}" enctype="multipart/form-data">
                    @csrf()
                      <div id="update-supplier-content">
                      </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="update-supplier-form" class="btn btn-warning">Update</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="supplier-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
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
                    <div class="col-md-12 text-center table-responsive" id="logs-content">
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
        var supplerTable = null;
        $(function(){
            $('#hidden_terms_input').hide();
            $('input[name="supplier-tin-number"]').mask("999-999-999");
            $('.bootstrap-tagsinput ').tagsinput({
                tagClass: ' btn btn-info btn-sm btn-tags'
            });
            supplerTable = $('#suppliers-tbl').DataTable({
                "pageLength": 100,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('supplier-functions',['id' => 'supplier-list']) }}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(result){  // error handling
                        $('#err').html(JSON.stringify(result));
                        $('#feedback').html(JSON.stringify(result));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name'},
                    { data: 'category', name: 'category',visible:false},
                    { data: 'code', name: 'code',visible:false},
                    { data: 'tin_number', name: 'tin_number',visible:false},
                    { data: 'contact_person', name: 'contact_person'},
                    { data: 'contact_number', name: 'contact_number'},
                    { data: 'email', name: 'email',visible:false},
                    { data: 'actions', name: 'actions'}
                ],
                responsive: true,
                sDom: 'lrtip'
            });
            $( "#suppliers-search" ).keyup(function() {
                $("#dt-departments_filter  input[type='search']").val(this.value);
                supplerTable.search(
                    $(this).val(),
                ).draw() ;
            });
            $("#select-industry").select2({
                placeholder: "Select Industry",
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
            $("#select-vat").select2({
                placeholder: "Select VAT Type",
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
                    url: '{{ route("supplier-functions", ["id" => "fetch-barangays"]) }}',
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
            $('.supplier-payment-type-radio').on('click', function(){
                if($(this).val() == 'WITH-TERMS') {
                    $('#hidden_terms_input').html(
                        '<input type="number" class="form-control" required name="supplier-payment-term" id="supplier-payment-term" min="0">'+
                        '<div class="input-group-append">'+
                        '<span class="input-group-text">days</span>'+
                        '</div>');
                    $('#hidden_terms_input').show();
                } else {
                    $('#hidden_terms_input').hide();
                    $('#hidden_terms_input').empty().append('<input type="hidden" required name="supplier-payment-term" id="supplier-payment-term" value="none">');
                }
            });
        });

        function updateSupplier(id){
            var path = '{{ route("supplier-content") }}?id='+id;
            $('#update-supplier-content').html('');
            $('#update-supplier-content').html('' +
                '<div class="col-md-12 mt-4">'+
                '    <div class="d-flex justify-content-center">'+
                '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                '            <span class="sr-only">Loading...</span>'+
                '        </div>'+
                '    </div>'+
                '</div>'+
            '');
            $('#update-supplier-content').load(path, function() {
                $("input[name='supplier-tin-number-update']").mask("999-999-999");
                $('.bootstrap-tagsinput ').tagsinput({
                    tagClass: ' btn btn-info btn-sm btn-tags'
                });
                $("#select-industry-update").select2({ 
                    placeholder: "Select Industry",
                    allowClear: true,
                    width:"100%"
                });
                $("#select-region-update").select2({ 
                    placeholder: "Select Region",
                    allowClear: true,
                    width:"100%"
                });
                $("#select-province-update").select2({ 
                    placeholder: "Select Province",
                    allowClear: true,
                    width:"100%"
                });
                $("#select-city-update").select2({ 
                    placeholder: "Select City",
                    allowClear: true,
                    width:"100%"
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
                        url: '{{ route("supplier-functions", ["id" => "fetch-provinces"]) }}',
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
                        url: '{{ route("supplier-functions", ["id" => "fetch-cities"]) }}',
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
                        url: '{{ route("supplier-functions", ["id" => "fetch-barangays"]) }}',
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

                $(".supplier-payment-type-radio-update").on("click", function(){
                    if($(this).val() == "WITH-TERMS") {
                        $("#update_hidden_terms_input").html(
                            "<input type='number' class='form-control' required name='supplier-payment-term-update' id='supplier-payment-term-update' min='0'>"+
                            "<div class='input-group-append'>"+
                                "<span class='input-group-text'>days</span>"+
                            "</div>");
                        $("#update_hidden_terms_input").show();
                    } else {
                        $("#update_hidden_terms_input").hide();
                        $("#update_hidden_terms_input").empty().append("<input type='hidden' required name='supplier-payment-term-update' id='supplier-payment-term-update' value='none'>");
                    }
                });
            });
            $('#update-suppliers-modal').modal('show');
        }

        function logsModal(key){
            var url = "{{ route('supplier-logs-details') }}";
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
            $("#logs-content").load(url+"?sid="+key, function () {
                var data = { 'key': key }
                $("#dt-supplier-logs").dataTable().fnDestroy();
                $('#dt-supplier-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('supplier-functions',['id' => 'logs-suppliers-details']) }}", // json datasource
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
            $('#supplier-logs-modal').modal('show');
        }
    </script>
@endsection
