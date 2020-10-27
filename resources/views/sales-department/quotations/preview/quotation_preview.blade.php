<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Office Furniture Solutions. Your Innovative Partner. Wide Selection of Furniture and Furnishing for Offices, Schools, Resto-Bar, Hotels and Home Environment.">
<meta property="og:description" content="jecams inc. | office furniture solutions,office table,office chair,office,partition,office furniture,office fit out" />
<!-- Vendor CSS-->
<link rel="stylesheet" href="{{ asset('assets/css/vendors.bundle.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/css/app.bundle.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/css/notifications/sweetalert2/sweetalert2.bundle.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/css/notifications/toastr/toastr.css') }}"/>
<!-- End-Vendor CSS-->
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">

<link rel="stylesheet" href="//use.fontawesome.com/releases/v5.1.0/css/all.css" >
<meta name="csrf_token" content="{{ csrf_token() }}">
@php 
    $generatedSavedPoint = encryptor('encrypt',$user->id);
    $destination = 'assets/files/quotations/'.$generatedSavedPoint.'/';
    $qfilename = 'quotation-information';
    $quotation_info = toTxtFile($destination,$qfilename,'get');
    //-------------------------------------------------------
    $dfilename = 'delivery-information';
    $delivery_info = toTxtFile($destination,$dfilename,'get');
    //-------------------------------------------------------
    $pfilename = 'quotation-product-information';
    $product_info = toTxtFile($destination,$pfilename,'get');
    //-------------------------------------------------------
    $afilename = 'quotation-amount-information';
    $amount_info = toTxtFile($destination,$afilename,'get');
    //-------------------------------------------------------
    $wNature = '';
    $qSubject = '';
    $qJecamsRole = '';
    $qValidDate = '';
    $qClient = '';
    $qBranch = '';
    $address = '';
    $region_data = '';
    $cityOptions = '';
    $provinceOptions = '';
    $qcontact_number = '';
    $qposition = '';
    $qcontact_person = '';
    if($quotation_info['success'] === true){
        $datas = $quotation_info['data'];
		$datas = json_decode($datas);
		
		$wNature = $datas->work_nature;
		$qSubject = $datas->subject;
		$qJecamsRole = $datas->jecams_role;
		$qValidDate = $datas->validity_date;
		$qClient = $datas->client;
		$qBranch = $datas->branch_id;
		$qcontact_number = $datas->contact_number;
		$qposition = $datas->position;
		$qcontact_person = $datas->contact_person;

		$temp_client = $datas->client;
		$data_info = 'client';
		if(!empty($qBranch)){
			$temp_client = $qBranch;
			$data_info = 'branch';
		}
		$client_data = Client($temp_client,$data_info,0);
		$provinceOptions = Client($client_data->province->region_id,'province',$client_data->province_id);
		$cityOptions = Client($client_data->province_id,'city',$client_data->city_id);

		$address = '';
		if(!empty($client_data->complete_address)){
			$address = $client_data->complete_address;
		}

		$region_data = '';
		if(!empty($client_data->complete_address)){
			$region_data = $client_data->province->region_id;
		}
    }
    $delivery_mode = '';
    $tentative_date = '';
    $complete_address = '';
    $region = '';
    $city = '';
    $province = '';
    $barangay = '';
    $save_option = '';

    if($delivery_info['success'] === true){
            $datas_delivery = $delivery_info['data'];
            $datas_delivery = json_decode($datas_delivery);
          
            $delivery_mode = $datas_delivery->delivery_mode;
            $tentative_date = $datas_delivery->tentative_date;
            if($delivery_mode=='DELIVER'){
                $address = $datas_delivery->complete_address;
                $region_data = encryptor('decrypt',$datas_delivery->region);
                $city_data = encryptor('decrypt',$datas_delivery->city);
                $province_data = encryptor('decrypt',$datas_delivery->province);
                $save_option = $datas_delivery->save_option;
                $addrsData = Client($city_data,'everything',$city_data);
                $barangay = Client($datas_delivery->barangay,'barangay_query',$city_data);
            }
            $product_details = '';        
    }
    $pfilename = 'quotation-product-information';
    $product_info = toTxtFile($destination,$pfilename,'get');
    $products = array();
    if($product_info['success'] === true){
        $datasproduct = $product_info['data'];
        $products = json_decode($datasproduct);
    }
    $sub_total_a = 0;
    $installation_charge_a = 0;
    $delivery_charge_a = 0;
    $total_discount_a = 0;
    $discount_a = 0;
    $total_item_discount_a = 0;
    $grand_total_a = 0;
    if($amount_info['success'] === true){
        $data_amount = $amount_info['data'];
        $data_amount = json_decode($data_amount);
        $sub_total_a = $data_amount->sub_total;
        $installation_charge_a = $data_amount->installation_charge;
        $delivery_charge_a = $data_amount->delivery_charge;
        $total_discount_a = $data_amount->total_discount;
        $total_item_discount_a = $data_amount->total_item_discount;
        $grand_total_a = $data_amount->grand_total;

        $discount_a = $total_discount_a-$total_item_discount_a;
    }
@endphp
<div class="row" style="padding:20px;">
    <div class="col-md-12">
        <div class="row" style="padding:5px;background: #80808014;">
            <div class="col-md-6">
                <img src="{{ asset('assets/img/jecamslogo.png') }}" style="width: 310px;" alt="JECAMS LOGO">
            </div>
            <div class="col-md-6" align="right">
                <h1 style="font-size: 58px;">Quotation | <label style="font-size: 38px;">PREVIEW</label> </h1>
            </div>
        </div>
        <hr>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <h3><b>Nature of Work :</b> {{$wNature}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>JECAMS Role :</b> {{$qJecamsRole}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Subject :</b> {{$qSubject}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Validity Date :</b> {{date('F d,Y',strtotime($qValidDate))}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>VAT Type :</b> {{$datas->vat_type}}</h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <h3><b>Client :</b> {{$client_data->name}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Contact Person :</b> {{$client_data->contact_person}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Position :</b> {{$client_data->position}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Contact Number :</b> {{$client_data->contact_numbers}}</h3>
                </div>
                
            </div>
        </div>
        <hr>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <h3><b>Delivery Mode :</b> {{$delivery_mode}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Tentative Delivery or Pickup Date :</b> {{date('F d,Y',strtotime($tentative_date))}}</h3>
                </div>
                @if($delivery_mode=='DELIVER')
                <div class="form-group">
                    <h3><b>Complete Address :</b> {{$address}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Region :</b>{{$addrsData->region->description}} </h3>
                </div>
                @endif
            </div>
            @if($delivery_mode=='DELIVER')
            <div class="col-md-6">
                <div class="form-group">
                    <h3><b>Province :</b>{{$addrsData->province->description}} </h3>
                </div>
                <div class="form-group">
                    <h3><b>City :</b> {{$addrsData->city_name}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Barangay :</b> {{$barangay->barangay_description}}</h3>
                </div>
                <div class="form-group">
                    <h3><b>Save As :</b> {{$save_option}}</h3>
                </div>
            </div>
            @endif
        </div>
        <hr>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product Code</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>List Price</th>
                        <th>Discount</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $count=0;
                    @endphp 
                    @foreach($products as $product)
                        @php 
                            $count++;
                            $description = '';
                            $proddes = '';
                            $product_qty = $product->qty;
                            $product_price = $product->price;
                            $total_price = $product_price*$product_qty;
                            $discount =0;
                            if(!empty($product->discount)){
                                $discount = $product->discount;
                            }
                            $variant_details = '';
                            $total_pricesssss = $total_price-$discount;
                            $enc_product_id = encryptor('encrypt',$product->product_id); 
                            $defaultLink = 'no-img';
                            $destination  = 'assets/img/products/'.$enc_product_id.'/';
                            $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                            if($defaultLink=='no-img'){
                                if($product->product_type!='RAW'){
                                    $new_link = fetchProduct($product->variant_id);
                                    $enc_product_id = encryptor('encrypt',$new_link->id);
                                    $destination  = 'assets/img/products/'.$enc_product_id.'/';
                                    $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                                    if($defaultLink=='no-img'){
                                        $enc_product_id = encryptor('encrypt',$new_link->parent_id);
                                        $destination  = 'assets/img/products/'.$enc_product_id.'/';
                                        $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                                    }
                                }else{
                                    $dec_id = encryptor('decrypt',$product->variant_id);
                                    $new_link = fetchProduct($dec_id);
                                    $enc_product_id = encryptor('encrypt',$new_link->id);

                                    $destination  = 'assets/img/products/'.$enc_product_id.'/';
                                    $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                                }
                                 
                                
                            }
                        @endphp 
                            @if($product->product_type!='RAW')
                                @if($product->product_type=='FIT-OUT')
                                    @php 
                                    $variant_id = $product->variant_id;
                                    $variant_name = $product->variant_name;
                                    $variant_qty = $product->variant_qty;

                                    $de_variant_id = json_encode($variant_id);
                                    $de_variant_name = json_encode($variant_name);
                                    $de_variant_qty = json_encode($variant_qty);
                                    @endphp 
                                    @for($i=0;$i<count($variant_id);$i++)
                                        @if(!empty($variant_qty[$i]))
                                            @php 
                                                $variant_name_temp = str_replace("|","<bR>",$variant_name[$i]);
                                                $variant_name_temp = str_replace("v:","</b>(QTY: <b class='text-danger'>".$variant_qty[$i]."</b>)<bR>",$variant_name_temp);
                                                $variant_details .= '<b class="text-primary">• '.$variant_name_temp.'<br>';
                                            @endphp
                                        @endif
                                    @endfor
                                @else 
                                   
                                    @if($product->product_type!='RAW')
                                        @php 
                                            $variant_details = str_replace("|","<bR>",$product->variant_name);
                                        @endphp
                                    @endif
                                @endif
                            @endif 

                            @if(!empty($product->description)&&$product->description!='')
                                 @php
                                    $description = "<br><b>Other description :</b><br>".$product->description;
                                    $proddes = $product->description;
                                    if($description=='<br><b>Other description :</b><br><div><\/div>'||$product->description=='<div></div>'||$product->description=='<div style="color: rgb(0, 0, 0); font-family: Roboto, -apple-system, system-ui, BlinkMacSystemFont, " segoe="" ui",="" "helvetica="" neue",="" arial,="" sans-serif;="" font-size:="" 14px;="" letter-spacing:="" 0.2px;"=""><br></div>'){
                                        $description = '';
                                        $proddes = '';
                                    }
                                @endphp
                            @else
                                @php 
                                    $proddes = '';
                                @endphp
                            @endif
                        <tr>
                            <td>{{$count}}</td>
                            <td><img style="width:100px;height:100px;" src="{{$defaultLink}}" /></td>
                            <td>{{$product->product_id}}</td>
                            <td>@php echo $variant_details; echo $description;  @endphp</td>
                            <td>{{$product->qty}}</td>
                            <td>PHP {{number_format($product->price,2)}}</td>
                            <td>PHP {{number_format($discount,2)}}</td>
                            <td>PHP {{number_format($total_pricesssss,2)}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row">
                <div class="col-md-7"></div>
                <div class="col-md-5" align="right">
                    <div class="table-responsive">
                        <table>
                            <tfoot>
                                <tr>
                                    <td><b>SUB TOTAL :</b></td>
                                    <td><input type="text" name="sub_total" class="form-control" value="PHP {{number_format($sub_total_a,2)}}" readonly></td>
                                </tr>
                                <tr>
                                    <td><b>INSTALLATION CHARGE :</b></td>
                                    <td><input type="text" name="installation_charge" class="form-control" value="PHP {{number_format($installation_charge_a,2)}}" readonly /></td>
                                </tr>
                                <tr>
                                    <td><b>DELIVERY CHARGE :</b></td>
                                    <td><input type="text" name="delivery_charge" class="form-control" value="PHP {{number_format($delivery_charge_a,2)}}" readonly /></td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL PRODUCT DISCOUNT :</b><br><small class="text-danger">*FOR TOTAL PRODUCT EACH DISCOUNT TOTAL</small></td>
                                    <td><input type="text" name="discount_product_quotation" class="form-control" value="PHP {{number_format($total_item_discount_a,2)}}" readonly/></td>
                                </tr>
                                <tr>
                                    <td><b>DISCOUNT :</b><br><small class="text-danger">*FOR WHOLE QUOTATION DISCOUNT</small></td>
                                    <td><input type="text" name="discount_quotation" class="form-control" value="PHP {{number_format($discount_a,2)}}" readonly /></td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL DISCOUNT :</b><br><small class="text-danger">*FOR (TOTAL PRODUCT DISCOUNT) + (DISCOUNT)</small></td>
                                    <td><input type="text" name="total_discount" class="form-control" value="PHP {{number_format($total_discount_a,2)}}" readonly /></td>
                                </tr>
                                <tr>
                                    <td><b>GRAND TOTAL :</b></td>
                                    <td><input type="text" name="grand_total" class="form-control" value="PHP {{number_format($grand_total_a,2)}}" readonly /></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div> <!------>
        </div>
    </div>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ asset('assets/js/vendors.bundle.js') }}"></script>
<script src="{{ asset('assets/js/app.bundle.js') }}"></script>
<script src="{{ asset('assets/js/notifications/sweetalert2/sweetalert2.bundle.js') }}"></script>
<script src="{{ asset('assets/js/notifications/toastr/toastr.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf_token]').attr('content') }
    });
</script>
<script>
    var formData = [];
    var confirmResult = false;
    function readURL(input,displayTo,baseurl,width = null,height = null) {
        var file = document.getElementById(input);
        if (document.getElementById(input).files.length == 0) {
            $('#' + displayTo).attr('src', baseurl);
        }
        else {
            if (file.files && file.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#' + displayTo).attr('src', e.target.result);
                    if (height != null && width != null) {
                        $('#' + displayTo).attr('height', height);
                        $('#' + displayTo).attr('width', width);
                    }
                }
                reader.readAsDataURL(file.files[0]);
            }
            else {
                $('#' + displayTo).attr('src', baseurl);
            }
        }
    }
    $(document).ready(function(){
        $("#nav_filter_input").on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $("#js-nav-menu li").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        });
      });
      function alert_message(title,text,success = 'info'){
        var classHead = 'info';
        confirmResult = false;
        if(success == 'danger'){
            classHead = 'error';
        }else if(success == 'success'){
            classHead = 'success';
        }
        Swal.fire({
            type: classHead,
            title: title,
            text: text,
            width: 600,
            padding: "3em",
            backdrop: '\n\t\t\t rgba(0,0,123,0.4)\n\t\t\t center left\n\t\t\t no-repeat\n\t\t\t'
        });
    }
    function isNumberKey(evt)
		{
			var e = evt || window.event; // for trans-browser compatibility
			var charCode = e.which || e.keyCode;                        
			if (charCode > 31 && (charCode < 46 || charCode > 57))
			return false;
			if (e.shiftKey) return false;
			return true;
		}
    function toastMessage(title,message,mode,position = 'toast-top-right'){
        //
        /**
        * MODE *
            *success
            *info
            *warning
            *error
        * POSITION CLASS *
            *toast-top-center
            *toast-top-right
            *toast-bottom-right
            *toast-bottom-left
            *toast-top-left
            *toast-top-full-width
            *toast-bottom-full-width
            *toast-top-center
            *toast-bottom-center
        */
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": position,
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": 300,
            "hideDuration": 100,
            "timeOut": 5000,
            "extendedTimeOut": 1000,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr[mode](message,title);
    }
</script>