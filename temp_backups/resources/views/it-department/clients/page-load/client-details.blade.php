<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="form-group mb-2">
            <label>Name <span class="text-danger">*</span></label>
            @if($client_type == 'prospect')
                <input type="text" class="form-control" required name="client-name-update" id="client-name-update" value="{{ $client->name }}" placeholder="Client name">
            @else
                <div class="input-group">
                    <input type="text" class="form-control" required name="client-name-update" id="client-name-update" value="{{ $client->name }}" placeholder="Client name" aria-describedby="button-addon5">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary waves-effect waves-themed" id="check_client_update"><i class="fal fa-search"></i></button>
                    </div>
                </div>
                <div class="alert alert-primary alert-dismissible" style="margin-bottom: 1rem; display: none;" id="search_result_div_update">
                    <button type="button" class="close" id="hide_result_btn_update">
                        <span aria-hidden="true">
                            <i class="fal fa-times"></i>
                        </span>
                    </button>
                    <div class="d-flex flex-start w-100">
                        <div class="d-flex flex-fill">
                            <div class="flex-fill">
                                <span class="h5">Result(s)</span>
                                <span id="search_content_update">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="form-group mb-2">
            <label>Position</label>
            <input type="text" class="form-control" name="client-position-update" id="client-position-update" value="{{ $client->position }}">
        </div>
        <div class="form-group mb-2">
            <label>Email <span class="text-danger">*</span></label>
            <input type="text" class="bootstrap-tagsinput" required name="client-email-update" id="client-email-update" value="{{ $client->emails }}">
        </div>
        <div class="form-group mb-2">
            <label>Region <span class="text-danger">*</span></label>
            <select class="form-control" id="select-region-update" required name="select-region-update">
                <option value=""></option>
                @foreach($regions as $region)
                    @php
                        $enc_region_id = encryptor('encrypt', $region->id);
                        $current_region = '';
                        if($region->id == $client_region->region_id) {
                            $current_region = 'selected';
                        }
                    @endphp
                    <option value="{{ $enc_region_id }}" {{ $current_region }}>{{ $region->description }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-2">
            <label>City/Municipality <span class="text-danger">*</span></label>
            <select class="form-control" id="select-city-update" required name="select-city-update">
                <option value=""></option>
                @foreach($cities as $city)
                    @php
                    $enc_city_id = encryptor('encrypt', $city->id);
                    $current_city = '';
                    if($city->id == $client->city_id) {
                        $current_city = 'selected';
                    }
                    @endphp
                    <option value="{{ $enc_city_id }}" {{ $current_city }}>{{ $city->city_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-2">
            @php
                $required_label = '<span class="text-danger">*</span>';
                $required_field = 'required';
                if($client_type == 'prospect') {
                    $required_label = '';
                    $required_field = '';
                }
            @endphp
            <label>Zip Code @php echo $required_label; @endphp</label>
            <input type="number" class="form-control" @php echo $required_field; @endphp name="client-zip-code-update" id="client-zip-code-update" value="{{ $client->zip_code }}">
        </div>
        <div class="form-group mb-2">
            <label>Type of Industry <span class="text-danger">*</span></label>
            <select class="form-control" id="select-industry-update" required name="select-industry-update">
                <option value=""></option>
                @foreach($industries as $industry)
                    @php
                        $enc_industry_id = encryptor('encrypt', $industry->id);
                        $current_industry = '';
                        if($industry->id == $client->industry_id) {
                            $current_industry = 'selected';
                        }
                    @endphp
                    <option value="{{ $enc_industry_id }}" {{ $current_industry }}>{{ $industry->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="form-group mb-2">
            <label>Contact Person <span class="text-danger">*</span></label>
            <input type="text" class="form-control" required name="client-contact-person-update" id="client-contact-person-update" value="{{ $client->contact_person }}">
        </div>
        <div class="form-group mb-2">
            <label>Contact Number <span class="text-danger">*</span></label>
            <input type="text" class="bootstrap-tagsinput" required name="client-contact-number-update" id="client-contact-number-update" value="{{ $client->contact_numbers }}">
        </div>
        <div class="form-group mb-2">
            <label>TIN Number</label>
            <input type="text" class="form-control" name="client-tin-number-update" id="client-tin-number-update" value="{{ $client->tin_number }}">
        </div>
        <div class="form-group mb-2">
            <label>Province <span class="text-danger">*</span></label>
            <select class="form-control" id="select-province-update" required name="select-province-update">
                <option value=""></option>
                @foreach($provinces as $province)
                    @php
                        $enc_province_id = encryptor('encrypt', $province->id);
                        $current_province = '';
                        if($province->id == $client->province_id) {
                            $current_province = 'selected';
                        }
                    @endphp
                    <option value="{{ $enc_province_id }}" {{ $current_province }}>{{ $province->description }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-2">
            <label>Complete Address <span class="text-danger">*</span></label>
            <input type="text" class="form-control" required name="client-complete-address-update" id="client-complete-address-update" value="{{ $client->complete_address }}">
        </div>
        <div class="form-group mb-2">
            <label>Branch</label>
            <select class="form-control" id="select-branch-update" name="select-branch-update" required>
                <option value=""></option>
                @foreach($branches as $branch)
                    @php
                        $enc_branch_id = encryptor('encrypt', $branch->id);
                        $current_branch = '';
                        if($branch->id == $client->branch_id) {
                            $current_branch = 'selected';
                        }
                    @endphp
                    <option value="{{ $enc_branch_id }}" {{ $current_branch }}>@php echo ucwords($branch->name); @endphp</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Business Style</label>
            <select class="form-control" id="select-business-style-update" name="select-business-style-update">
                <option value=""></option>
                @foreach($business_styles as $business_style)
                    @php
                        $enc_business_style_id = encryptor('encrypt', $business_style->id);
                        $current_business_style = '';
                        if($business_style->name == $client->business_style) {
                            $current_business_style = 'selected';
                        }
                    @endphp
                    <option value="{{ $enc_business_style_id }}" {{ $current_business_style }}>@php echo ucwords($business_style->name); @endphp</option>
                @endforeach
            </select>
            <input type="hidden" id="client-business-style-update" name="client-business-style-update" value="{{ $client->business_style }}">
        </div>
    </div>
    @php
        $enc_client_id = encryptor('encrypt', $client->id);
    @endphp
    <input type="hidden" id="client-id" name="client-id" value="{{ $enc_client_id }}">
    <input type="hidden" id="client-type" name="client-type" value="{{ $client_type }}">
</div>

<script>
    $(function(){
        $('input[name="client-tin-number-update"]').mask("999-999-999");
        $('.bootstrap-tagsinput ').tagsinput({
            tagClass: ' btn btn-info btn-sm btn-tags'
        });
    });

    $(document).ready(function(index){
        $('#hidden_terms_input').hide();
        $('input[name="client-tin-number"]').mask("999-999-999");

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
        $("#select-city-update-").select2({ 
            placeholder: "Select City",
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
                    $("#select-city-update").empty().append(data);
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
                    url: '{{ route("client-functions", ["id" => "check-client-exist"]) }}',
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
</script>