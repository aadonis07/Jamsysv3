<div class="form-group row mb-2">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Supplier Name :</label>
        <input type="text" class="form-control" required name="supplier-name-update" id="supplier-name-update" value="{{ $supplier->name }}">
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Code :</label>
        <input type="text" class="form-control" required name="supplier-code-update" id="supplier-code-update" value="{{ $supplier->code }}">
    </div>
</div>
<div class="form-group row mb-2">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Category :</label>
        <select class="form-control" id="select-category-update" required name="select-category-update">
            <option value="">Select Category</option>
            @foreach($categories as $index => $category)
                @php
                    $current_category = '';
                    if($category == $supplier->category) {
                        $current_category = 'selected';
                    }
                @endphp
                <option value="{{ $category }}" {{ $current_category }}>{{ $category }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Type of Industry :</label>
        <select class="form-control" id="select-industry-update" required name="select-industry-update">
            <option value=""></option>
            @foreach($industries as $industry)
                @php
                    $enc_industry_id = encryptor('encrypt', $industry->id);
                    $current_industry = '';
                    if($industry->id == $supplier->industry_id) {
                        $current_industry = 'selected';
                    }
                @endphp
                <option value="{{ $enc_industry_id }}" {{ $current_industry }}>{{ $industry->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Contact Person :</label>
        <input type="text" class="form-control" required name="supplier-contact-person-update" id="supplier-contact-person-update" value="{{ $supplier->contact_person }}">
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Contact Number :</label>
        <input type="text" class="form-control bootstrap-tagsinput" required name="supplier-contact-number-update" id="supplier-contact-number-update" value="{{ $supplier->contact_number }}">
    </div>
</div>
<div class="form-group row mb-2">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Email :</label>
        <input type="text" class="form-control" required name="supplier-email-update" id="supplier-email-update" value="{{ $supplier->email }}">
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>VAT Type :</label>
        <select class="form-control" id="select-vat-update" required name="select-vat-update">
            @foreach($vat_type as $index => $vat)
                @php
                    $current_vat = '';
                    if($index == $supplier->vatable) {
                        $current_vat = 'selected';
                    }
                @endphp
                <option value="{{ $index }}" {{ $current_vat }}>{{ $vat }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Region :</label>
        <select class="form-control" id="select-region-update" required name="select-region-update">
            <option value=""></option>
            @foreach($regions as $region)
                @php
                    $enc_region_id = encryptor('encrypt', $region->id);
                    $current_region = '';
                    if($region->id == $supplier_region->region_id) {
                        $current_region = 'selected';
                    }
                @endphp
                <option value="{{ $enc_region_id }}" {{ $current_region }}>{{ $region->description }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Province :</label>
        <select class="form-control" id="select-province-update" required name="select-province-update">
            <option value=""></option>
            @foreach($provinces as $province)
                @php
                    $enc_province_id = encryptor('encrypt', $province->id);
                    $current_province = '';
                    if($province->id == $supplier->province_id) {
                        $current_province = 'selected';
                    }
                @endphp
                <option value="{{ $enc_province_id }}" {{ $current_province }}>{{ $province->description }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>City/Municipality :</label>
        <select class="form-control" id="select-city-update" required name="select-city-update">
            <option value=""></option>
            @foreach($cities as $city)
                @php
                $enc_city_id = encryptor('encrypt', $city->id);
                $current_city = '';
                if($city->id == $supplier->city_id) {
                    $current_city = 'selected';
                }
                @endphp
                <option value="{{ $enc_city_id }}" {{ $current_city }}>{{ $city->city_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Complete Address</label>
        <input type="text" class="form-control" required name="supplier-complete-address-update" id="supplier-complete-address-update" value="{{ $supplier->complete_address }}">
    </div>
</div>
<div class="form-group row mb-2">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>TIN Number :</label>
        <input type="text" class="form-control" required name="supplier-tin-number-update" id="supplier-tin-number-update" value="{{ $supplier->tin_number }}">
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label>Payment Type :</label>
        @foreach($payment_types as $payment_type)
            @php
                $current_payment_type = '';
                if($payment_type == $supplier->payment_type) {
                    $current_payment_type = 'checked';
                }
            @endphp
            <div class="custom-radio mb-2">
                <input type="radio" class="supplier-payment-type-radio-update" required name="supplier-payment-type-update" value="{{ $payment_type }}" {{ $current_payment_type }}>
                <label>{{ $payment_type }}</label>
            </div>
        @endforeach 
        @php
            if($supplier->payment_type === "WITH-TERMS") {
                $payment_term = '<input type="number" class="form-control" required name="supplier-payment-term-update" id="supplier-payment-term-update" min="0" value="'.$supplier->payment_terms.'">
                <div class="input-group-append">
                    <span class="input-group-text">days</span>
                </div>';
            } else {
                $payment_term = '<input type="hidden" required name="supplier-payment-term-update" id="supplier-payment-term-update" value="none">';
            }
        @endphp
        <div id="update_hidden_terms_input" class="input-group input-group-sm" style="margin-top: -39px; margin-left: 105px; width: 36%;">
            @php echo $payment_term; @endphp
        </div>
    </div>
</div>
<div class="form-group mt-2">
    <label>Remarks :</label>
    <textarea class="form-control" name="supplier-remarks-update" id="supplier-remarks-update">{{ $supplier->remarks }}</textarea>
</div>

@php $enc_supplier_id = encryptor('encrypt', $supplier->id); @endphp
<input type="hidden" class="form-control" required name="supplier-id" value="{{ $enc_supplier_id }}">