@extends ('layouts.it-department.app')
@section ('title')
    Quotation Create
@endsection
@section('styles')
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<style>
    .select2-dropdown {
        z-index: 999999;
    }
</style>
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

$quotation_stat = '';
$delivery_stat = '';


$qexpanded = 'true';
$qcollapse = 'show';
$qcancel = '';
$qactive = '';
$bexpanded = 'false';
$bcollapse = '';
$cexpanded = 'false';
$ccollapse = '';
$ccancel = '';
$qbtncancel = 'style=display:none;';
$saveQuotation = 'style=display:none;';
$product_availability = 'false';
$wNature = '';
$qSubject = '';
$qJecamsRole = '';
$qValidDate = '';
$qClient = '';
$qBranch = '';
//---------------------
$address = '';
$region_data = '';
$cityOptions = '';
$barangayOptions = '';
$provinceOptions = '';
//---------------------
$qcontact_number = '';
$qposition = '';
$qcontact_person = '';
$qvat_type = '';
$qwarranty = '';
$qpayment_terms = '';
$qclient_details = 'style=display:none;';
$product_details = 'style=display:none;';
$terms_details = 'style=display:none;';
$bactive = 'style=display:none;';
$quotation_info_active = 'false';
if($quotation_info['success'] === true){
        $datas = $quotation_info['data'];
		$datas = json_decode($datas);
		$qexpanded = 'false';
		$qcollapse = '';
		$bexpanded = 'true';
		$quotation_info_active = 'true';
		$bcollapse = 'show';
		$qclient_details = '';
		$bactive = '';
		$qactive = 'disabled';
		$qcancel = 'style=display:none;';
		$wNature = $datas->work_nature;
		$qSubject = $datas->subject;
		$qJecamsRole = $datas->jecams_role;
		$qValidDate = $datas->validity_date;
		$qClient = $datas->client;
		$qBranch = $datas->branch_id;
		$qwarranty = $datas->warranty;
		$qcontact_number = $datas->contact_number;
		$qposition = $datas->position;
		$qcontact_person = $datas->contact_person;
		$qvat_type = $datas->vat_type;
		$qpayment_terms = $datas->payment_terms;
		$quotation_stat = '<span class="fa fa-check text-success mr-2"></span>';
		$temp_client = $datas->client;
		$data_info = 'client';
		if(!empty($qBranch)){
			$temp_client = $qBranch;
			$data_info = 'branch';
		}
		$client_data = Client($temp_client,$data_info,0);
		$provinceOptions = Client($client_data->province->region_id,'province',$client_data->province_id);
		$cityOptions = Client($client_data->province_id,'city',$client_data->city_id);
		$barangayOptions = Client($client_data->city_id,'barangay',$client_data->id );
		$address = '';
		if(!empty($client_data->complete_address)){
			$address = $client_data->complete_address;
		}

		$region_data = '';
		if(!empty($client_data->complete_address)){
			$region_data = $client_data->province->region_id;
		}
}
$delivery_modessss = '';
$tentative_date = '';
$complete_address = '';
$region = '';
$city = '';
$province = '';
$save_option = '';
$for_required = '';
if($delivery_info['success'] === true){
        $datas_delivery = $delivery_info['data'];
		$datas_delivery = json_decode($datas_delivery);
		$cexpanded = 'true';
		$ccollapse = 'show';
		$bexpanded = 'false';
		$bcollapse = '';
		$qexpanded = 'false';
		$delivery_modessss = $datas_delivery->delivery_mode;
		if($delivery_modessss=='DELIVER'){
			$for_required = 'required';
		}
		$qcollapse = '';
		$ccancel = 'style=display:none;';
		$delivery_stat = '<span class="fa fa-check text-success mr-2"></span>';
		$tentative_date = $datas_delivery->tentative_date;
		$address = $datas_delivery->complete_address;
		$region_data = encryptor('decrypt',$datas_delivery->region);
		$city_data = encryptor('decrypt',$datas_delivery->city);
		$province_data = encryptor('decrypt',$datas_delivery->province);
		$save_option = $datas_delivery->save_option;
		$product_details = '';
		
		$provinceOptions = Client($region_data,'province',$province_data);
		$cityOptions = Client($province_data,'city',$city_data );
		$barangayOptions = Client($city_data,'barangay',$qClient );
		$product_availability = 'true';
}
if($product_info['success'] === true){
	$terms_details = '';
	$product_availability = 'true';
	$saveQuotation = '';
	$qbtncancel = '';
}
$sub_total_a = 0;
$installation_charge_a = 0;
$delivery_charge_a = 0;
$total_discount_a = 0;
$discount_a = 0;
$total_item_discount_a = 0;
$grand_total_a = 0;
$amount_infos = 'false';
if($amount_info['success'] === true){
	$data_amount = $amount_info['data'];
	$data_amount = json_decode($data_amount);
	$amount_infos = 'true';
	$sub_total_a = $data_amount->sub_total;
	$installation_charge_a = $data_amount->installation_charge;
	$delivery_charge_a = $data_amount->delivery_charge;
	$total_discount_a = $data_amount->total_discount;
	$total_item_discount_a = $data_amount->total_item_discount;
	$grand_total_a = $data_amount->grand_total;

	$discount_a = $total_discount_a-$total_item_discount_a;

}
@endphp
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item">Qutoation</li>
    <li class="breadcrumb-item active">Create Quotation</li>
@endsection
@section('content')
<div class="card-footer py-2" {{$saveQuotation}} align="center">
								<button class="btn btn-danger" {{$qbtncancel}}>Cancel</button>
								<button type="submit" onclick='confirmData()' class="btn btn-success saveQuotationBtn"> <span class="fa fa-save"></span> SAVE QUOTATION  </button>
							</div>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					Quotation Number :<span class="fw-300"><b> {{$quote_number}}</b></span>
				</h2>
				<div class="panel-toolbar">
					<button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
				</div>
			</div>
			<div class="panel-container show">
				<div class="panel-content"> <!--- START PANEL CONTENT---->
					<div class="frame-wrap mt-3 mb-0 w-100"> <!--- START FRAME WRAP---->
					
					<div class="frame-wrap w-100">
						<div class="accordion" id="accordionExample">
							<div class="card mb-2 border-bottom">
								<div class="card-header" id="headingOne">
									<a href="javascript:void(0);" {{$qactive}} class="card-title collapsed bg-white" data-toggle="collapse" data-target="#collapseOne" aria-expanded="{{$qexpanded}}" aria-controls="collapseOne">
										@php echo $quotation_stat; @endphp Quotation Information
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-minus-circle text-danger"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-plus-circle text-success"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="collapseOne" class="collapse {{$qcollapse}}" aria-labelledby="headingOne" data-parent="#accordionExample" style="">
									<div class="card-body">
									<form method="post" id="quotation-information-form" onsubmit="qInfoBtn.disabled = true;" action="{{ route('quotation-functions', ['id' => 'quotation-information']) }}">
                        				@csrf()
										<div class="row"> <!--- START OF ROW 2 ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 1st CONTENT  ---->
												<div class="form-group">
													<label class="form-label" for="work-nature">Nature of Work</label>
													<select class="form-control" name="work-nature" required>
														<option value=""></option>
														@foreach($work_nature as $index=>$works)
														@php 
															$natureMode = '';
															if($index==$wNature){
																$natureMode = 'selected';
															}
														@endphp
														<option value="{{$index}}" {{$natureMode}}>{{$works}}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group">
													<label class="form-label" for="subject">Subject</label>
													<input class="form-control" placeholder="Subject" name="subject" value="{{$qSubject}}" required />
												</div>
												<div class="form-group">
													<label class="form-label" for="jecams-role">JECAMS Role</label>
													<select class="form-control" name="jecams-role" required>
														<option value=""></option>
														@foreach($roles as $indexR=>$role)
														@php 
															$roleMode = '';
															if($indexR==$qJecamsRole){
																$roleMode = 'selected';
															}
														@endphp
														<option value="{{$indexR}}" {{$roleMode}}>{{$role}}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group">
													<label class="form-label" for="validity-date">Validity Date</label>
													<input class="form-control" name="validity-date" value="{{$qValidDate}}" required type="text" style="background: white;" data-inputmask="'mask': '9999-99-99'" placeholder="yyyy-mm-dd" />
												</div>
												<div class="form-group">
													<label class="form-label" for="warranty">Warranty</label>
													<select class="form-control" required name="warranty">
														<option value=""></option>
														@foreach($warranties as $warranty_index=>$warranty) 
															@php 
																$warranty_mode = '';
																if(!empty($qwarranty)){
																	if($qwarranty==$warranty_index){
																		$warranty_mode = 'selected';
																	}
																}else{
																	if($warranty == '1 Year'){
																		$warranty_mode = 'selected';
																	}
																}
															@endphp
															<option value="{{$warranty_index}}" {{$warranty_mode}}>{{$warranty}}</option>
														@endforeach
													</select>
												</div>
											</div> <!--- END GRID COL 1st CONTENT  ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 2nd CONTENT  ---->
												<div class="form-group">
													<label class="form-label" for="client">Client</label>
													<select class="form-control" required name="client">
														<option value=""></option>
														@foreach($clients as $client)
														@php 
															$clientMode = '';
															if($client->id==$qClient){
																$clientMode = 'selected';
															}
														@endphp
														<option value="{{$client->id}}" {{$clientMode}}>{{$client->name}}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group" id="branch-content" style="display:none;">
													<label class="form-label" for="client">Client Branch</label>
													<select class="form-control" name="branch">
														@if(empty($qBranch))
														<option value=""></option>
														@else 
															@php 
															 	echo Branches($qClient);
															@endphp 
														@endif
													</select>
												</div>
												<div class="form-group" id="client-details-content" {{$qclient_details}}>
													<div class="input-group has-length">
														<div class="input-group-prepend">
															<span class="input-group-text text-success">
																<i class="ni ni-user fs-xl"></i> Contact Person
															</span>
														</div>
														<input type="text" aria-label="First name" id="contact_person" value="{{$qcontact_person}}" name="contact_person" class="form-control" readonly>
													</div>
													<div class="input-group has-length">
														<div class="input-group-prepend">
															<span class="input-group-text text-success">
																<i class="ni ni-user fs-xl"></i> Position
															</span>
														</div>
														<input type="text" aria-label="First name" id="position" value="{{$qposition}}" name="position" class="form-control" readonly>
													</div>
													<div class="input-group has-length">
														<div class="input-group-prepend">
															<span class="input-group-text text-success">
																<i class="ni ni-user fs-xl"></i> Contact Number
															</span>
														</div>
														<input type="text" aria-label="First name" id="contact_number" value="{{$qcontact_number}}" name="contact_number" class="form-control" readonly>
													</div>
												</div>
												<div class="form-group">
													<label>Payment Terms</label>
													<select class="form-control" name="payment-terms" required>
														<option value=""></option>
														@foreach($payment_terms as $payment_term)
														@php 
															$termsMode = '';
															if($payment_term->id==$qpayment_terms){
																$termsMode = 'selected';
															}
														@endphp
														<option value="{{$payment_term->id}}" {{$termsMode}}>{{$payment_term->name}}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group">
													<label>VAT Type</label>
													<select class="form-control" name="vat-type" required>
														<option value=""></option>
														@foreach($vat_types as $index_type=>$vat_type)
														@php 
															$vatMode = '';
															if($index_type==$qvat_type){
																$vatMode = 'selected';
															}
														@endphp
														<option value="{{$index_type}}" {{$vatMode}}>{{$vat_type}}</option>
														@endforeach
													</select>
												</div>
											</div> <!--- START GRID COL 2nd CONTENT  ---->
										</div> <!--- END OF ROW 2 ---->
										</form>
									</div>
									<div class="card-footer py-2" align="right">
										<a class="btn btn-danger text-white" id="firstCancel" {{$qcancel}}>CANCEL</a>
										<button type="submit" form="quotation-information-form" id="qInfoBtn" class="btn btn-success"> NEXT <span class="fa fa-arrow-right"></span> </button>
									</div>
								</div>
							</div>
							<div class="card mb-2" {{$bactive}}>
								<div class="card-header" id="headingTwo">
									<a href="javascript:void(0);"  class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="{{$bexpanded}}" aria-controls="collapseTwo">
									@php echo $delivery_stat; @endphp Delivery and Billing Info
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-minus-circle text-danger"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-plus-circle text-success"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="collapseTwo" class="collapse {{$bcollapse}}" aria-labelledby="headingTwo" data-parent="#accordionExample" style="">
									<div class="card-body">
										<form method="post" id="delivery-information-form" onsubmit="dInfoBtn.disabled = true;" action="{{ route('quotation-functions', ['id' => 'delivery-information']) }}">
											@csrf()
										<div class="row"> <!--START ROW DELIVERY -->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 1st CONTENT DELIVERY  ---->
												<div class="form-group">
													<label>Delivery Mode</label>
													<select class="form-control" required name="delivery-mode">
														<option value=""></option>
														@foreach($delivery_modes as $index=>$delivery_mode)
														@php 
															$deliveryMode='';
															if($index==$delivery_modessss){
																$deliveryMode='selected';
															}
														@endphp
														<option value="{{$index}}" {{$deliveryMode}}>{{$delivery_mode}}</option>
														@endforeach
													</select>
												</div>
											</div> <!--- END GRID COL 1st CONTENT DELIVERY  ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 2nd CONTENT DELIVERY  ---->
												<div class="form-group">
													<label>Tentative Delivery or Pickup Date <span class="text-danger">*This can change in logistics panel for more accurate date or exact date of deliver.</span></label>
													<input class="form-control" type="text" data-inputmask="'mask': '9999-99-99'" name="tentative-date" value="{{$tentative_date}}" style="background: white;" placeholder="yyyy-mm-dd" required />
												</div>
											</div> <!--- END GRID COL 2nd CONTENT DELIVERY  ---->
											<div class="col-md-12 col-lg-12 col-xl-12 col-xs-12"> <!--- START GRID COL 3rd CONTENT DELIVERY  ---->
												<hr>
												<div class="form-group">
													<label>Complete Address</label>
													<textarea class="form-control" rows="3" {{$for_required}} id="complete-address" name="complete-address" placeholder="Building No./ Room No. / Floor No. / Barangay , Town / City , Region , Zip Code">{{$address}}</textarea>
												</div>
											</div> <!--- END GRID COL 3rd CONTENT DELIVERY  ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 4th CONTENT DELIVERY  ---->
												<br>
												<div class="form-group mb-2">
													<label>Region </label>
													<select class="form-control" id="select-region" {{$for_required}} name="select-region">
														<option value=""></option>
														@foreach($regions as $region)
															@php 
																$regionMode = '';
																if($region_data==$region->id){
																	$regionMode ='selected';
																}
															@endphp
															<option value="{{ encryptor('encrypt',$region->id) }}" {{$regionMode}}>{{ $region->description }}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group mb-2" >
													<label>City/Municipality </label>
													<select class="form-control" name="city-content" {{$for_required}} id="city-content">
														<option value=""></option>
														@php
														echo $cityOptions;
														@endphp
													</select>
												</div>
												<div class="form-group mb-2" >
													<label>Barangay </label>
													<select class="form-control" name="barangay-data" {{$for_required}} id="barangay-data">
														<option value=""></option>
														@php
														echo $barangayOptions;
														@endphp
													</select>
												</div>
											</div> <!--- END GRID COL 4th CONTENT DELIVERY  ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 5th CONTENT DELIVERY  ---->
												<br>
												
												<div class="form-group mb-2">
													<label>Province </label>
													<select class="form-control" id="select-province" {{$for_required}} name="select-province">
														<option value=""></option>
														@php
														echo $provinceOptions;
														@endphp
													</select>
												</div>
												
												<div class="form-group mb-2">
													<div class="frame-wrap">
														<div class="demo">
																@php 
																	$saveModeSB = '';
																	$saveModeB = '';
																	$saveModeS = '';
																	if($save_option=='BILLING&SHIPPING'){
																		$saveModeSB = 'checked';
																	}
																	if($save_option=='BILLING'){
																		$saveModeB = 'checked';
																	}
																	if($save_option=='SHIPPING'){
																		$saveModeS = 'checked';
																	}
																@endphp
															<div class="custom-control custom-radio custom-radio-rounded">
																<input type="radio" class="custom-control-input" {{$saveModeSB}} id="defaultUncheckedRadio" name="save-option" value="BILLING&SHIPPING">
																<label class="custom-control-label" for="defaultUncheckedRadio">Save for Billing and Shipping</label>
															</div>
															<div class="custom-control custom-radio custom-radio-rounded">
																<input type="radio" class="custom-control-input" id="defaultCheckedRadio" {{$saveModeB}} name="save-option" value="BILLING">
																<label class="custom-control-label" for="defaultCheckedRadio">Save for Billing Only</label>
															</div>
															<div class="custom-control custom-radio custom-radio-rounded">
																<input type="radio" class="custom-control-input active" id="defaultUncheckedRadio2" {{$saveModeS}} name="save-option" value="SHIPPING">
																<label class="custom-control-label" for="defaultUncheckedRadio2">Save for Shipping Only</label>
															</div>
															<div id="pickupcontent">

															</div>
														</div>
													</div>
												</div>
											</div> <!--- END GRID COL 5th CONTENT DELIVERY  ---->
										</div><!--END ROW DELIVERY -->
										</form>
									</div>
									<div class="card-footer py-2" align="right">
										<button class="btn btn-danger cancelBtn" {{$ccancel}}>Cancel</button>
										<button type="submit" form="delivery-information-form" id="dInfoBtn" class="btn btn-success"> NEXT <span class="fa fa-arrow-right"></span> </button>
									</div>
								</div>
							</div>
							<form method="post" id="save-quotation-form" action="{{ route('quotation-functions', ['id' => 'save-quotation']) }}">
								@csrf()
								<input class="form-control" value="{{$quote_number}}" name="quotation_number" type="hidden"/>
							<div class="card mb-2" {{$product_details}}>
								<div class="card-header" id="headingThree">
									<a href="javascript:void(0);" class="card-title collapsed bg-white" data-toggle="collapse" data-target="#collapseThree" aria-expanded="{{$cexpanded}}" aria-controls="collapseThree">
										Product Information
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-minus-circle text-danger"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-plus-circle text-success"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="collapseThree" class="collapse {{$ccollapse}}" aria-labelledby="headingThree" data-parent="#accordionExample" style="">
									<div class="card-body">
									<div class="form-group" align="right">
										<a class="btn btn-primary text-white" id="save-arrangement" style="display:none;">
											<i class="fa fa-save text-white"></i> SAVE ARRANGEMENT
										</a>
										<a class="btn btn-success text-white" id="add-new-product">
											<i class="fa fa-plus text-white"></i> Add New Product
										</a>
									</div>
									<div class="form-group">
										
									</div>
										<div class="table-responsive">
											<table id="dt-quotation-product" class="table table-striped table-hover w-100 dataTable dtr-inline">
												<thead class="bg-warning-500 text-center">
													<tr role="row">
														<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" >#</th>
														<th>Product Code</th>
														<th width="25%">Description</th>
														<th width="7%">Qty</th>
														<th width="15%">List Price</th>
														<th width="15%">Discount</th>
														<th>Total</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody id="page_list">
												</tbody>
											</table>
										</div>
										<div class="row">
											<div class="col-md-7">
												<div class="form-group">
													<label>Quotation Remarks <span class="text-danger">*Remarks will show on quotation printable report.</span></label>
													<textarea class="form-control" rows="4" placeholder="Remarks example: Delivery is using 2 tracks" name="quotation-remarks"></textarea>
												</div>
											</div>
											<div class="col-md-5" align="right">
												<div class="table-responsive">
													<table>
														<tfoot>
															<tr>
																<td><b>SUB TOTAL :</b></td>
																<td><input type="text" name="sub_total" class="form-control" readonly></td>
															</tr>
															<tr>
																<td><b>INSTALLATION CHARGE :</b></td>
																<td><input type="text" name="installation_charge" onkeypress="return isNumberKey(event)" class="form-control" /></td>
															</tr>
															<tr>
																<td><b>DELIVERY CHARGE :</b></td>
																<td><input type="text" name="delivery_charge" onkeypress="return isNumberKey(event)" class="form-control" /></td>
															</tr>
															<tr>
																<td><b>TOTAL PRODUCT DISCOUNT :</b><br><small class="text-danger">*FOR TOTAL PRODUCT EACH DISCOUNT TOTAL</small></td>
																<td><input type="text" name="discount_product_quotation" class="form-control"  readonly/></td>
															</tr>
															<tr>
																<td><b>DISCOUNT :</b><br><small class="text-danger">*FOR WHOLE QUOTATION DISCOUNT</small></td>
																<td><input type="text" name="discount_quotation" onkeypress="return isNumberKey(event)" class="form-control" /></td>
															</tr>
															<tr>
																<td><b>TOTAL DISCOUNT :</b><br><small class="text-danger">*FOR (TOTAL PRODUCT DISCOUNT) + (DISCOUNT)</small></td>
																<td><input type="text" name="total_discount" class="form-control" readonly /></td>
															</tr>
															<tr>
																<td><b>GRAND TOTAL :</b></td>
																<td><input type="text" name="grand_total" class="form-control" readonly /></td>
															</tr>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="card mb-2" {{$terms_details}}>
								<div class="card-header" id="headingFour">
									<a href="javascript:void(0);" class="card-title collapsed " data-toggle="collapse" data-target="#collapseFour" aria-expanded="{{$cexpanded}}" aria-controls="collapseFour">
										Terms and conditions
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-minus-circle text-danger"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-plus-circle text-success"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="collapseFour" class="collapse {{$ccollapse}}" aria-labelledby="headingFour" data-parent="#accordionExample" >
									<div class="card-body">
										<div id="termsCondition">
											@php 
												echo $terms;
												$new_data = $terms;
											@endphp
										</div>
									</div>
									<div class="card-body" style="display:none">
										<input type="hidden" name="terms" required/>
									</div>
								</div>
							</div>
							<input type="hidden" name="commission-solution" />
							<input type="hidden" name="final-commission" />
							<input type="hidden" name="commission-type" />
							<input type="hidden" name="request-si" />
							<input type="hidden" name="request-commi" />
							<input type="hidden" name="formula-legend" />
							</form>
						</div>
					</div>
					<div class="card-footer py-2" {{$saveQuotation}} align="center">
						<button class="btn btn-danger cancelBtn" {{$qbtncancel}}>Cancel</button>
						<button type="submit" onclick='confirmData()' class="btn btn-success saveQuotationBtn"> <span class="fa fa-save"></span> SAVE QUOTATION  </button>
					</div>
					</div> <!--- END FRAME WRAP---->
				</div> <!--- END PANEL CONTENT---->
			</div>
		</div>
    </div>
</div>

<!-- ======================================================================================================= -->
<div class="modal fade"  id="add-product-modal" role="dialog" >
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
			</div>
			<div class="modal-body">
			<form id="add-quotation-product-form" method="POST" onsubmit="qProdBtn.disabled = true;" action="{{ route('quotation-functions',['id' => 'create-quotation-product']) }}" enctype="multipart/form-data">
                @csrf()
				<div class="row"> <!---  ADD PRODUCT ROW START ---->
					<div class="col-md-12 col-lg-12 col-xl-12 col-sm-12 col-xs-12"> <!---  ADD PRODUCT COL 1 START ---->
					<div class="form-group">
						<div class="accordion" id="productsCollapse"> <!--- Product Table Start--->
							<div class="card" style="border: 1px #00000029 solid;">
								<div class="card-header" id="headingThree">
									<a href="javascript:void(0);" id="select-product-drop" class="card-title collapsed bg-fusion-600 text-white" data-toggle="collapse" data-target="#productsContent" aria-expanded="false" aria-controls="productsContent">
										Select Product
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-minus-circle text-white"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-plus-circle text-white"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="productsContent" class="collapse" aria-labelledby="headingThree" data-parent="#productsCollapse" style="">
									<div class="card-body">
										<div class="table-responsive">
											<table id="dt-products" class="table table-striped table-hover w-100 dataTable dtr-inline">
												<thead class="bg-warning-500 text-center">
													<tr role="row">
														<th>Image</th>
														<th>Product Code</th>
														<th>Type</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div> <!--- Product Table End--->
						</div>
						<div class="form-group">
							<label>Product</label>
							<input class="form-control" readonly required name="product-id"/>
						</div>
						<div class="form-group" id="variant-content">

						</div>
						<input class="form-control" name="variant_name" readonly type="hidden" />
					</div> <!---  ADD PRODUCT COL 1 END ---->
					<div class="col-md-12 col-lg-12 col-xl-12 col-sm-12 col-xs-12"><!---  ADD PRODUCT COL 2 START ---->
						<div class="row">
							<div class="col-md-7 col-lg-7 col-xl-7 col-sm-12 col-xs-12"> <!--- PRODUCT PRICE COL START---->
								<div class="form-group">
									<label>Description</label>
									<textarea style="display:none" id="product-description" name="product-description">
									</textarea>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-md-6">
											<div class="input-group mar-btm">
												<span class="input-group-btn">
													<button class="btn btn-default" disabled> Php</button> 
												</span> 
												<input class="form-control" name="view_amount" readonly/>
												<input class="form-control" name="product_amount" type="hidden"/>
											</div>
										</div>
										<div class="col-md-6">
											<div class="input-group mar-btm">
												<span class="input-group-btn">
													<button class="btn btn-default" disabled> QTY</button> 
												</span> 
												<input class="form-control" name="product_qty" onkeypress="return isNumberKey(event)" required />
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label>Product Price</label>
									<div class="input-group mar-btm">
										<span class="input-group-btn">
											<button class="btn btn-default" disabled> PHP</button> 
										</span> 
										<input class="form-control" name="product_price" onkeypress="return isNumberKey(event)" />
									</div>
								</div>

								<div class="form-group">
									<input class="form-control" name="fitout_id" type="hidden" />
									<div class="row" id="swatch-content">
										<div class="col-md-6">
											<label>Swatches</label>
											<select class="form-control" name="swatch">
												<option value=""></option>
											</select>
										</div>
										<div class="col-md-6" id="swatch-img">
										</div>
									</div>
								</div>
							</div><!--- PRODUCT PRICE COL END---->
							<div class="col-md-5 col-lg-5 col-xl-5 col-sm-12 col-xs-12"> <!--- IMG COL START---->
								<img class="img-fluid text-center" id="product-previewa" style="width: 754px;height:400px;border: 1px solid #0000000f;" src="http://placehold.it/754x400">
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" name="productsimg" class="custom-file-input" onChange="readURL(this.id,'product-previewa','http://placehold.it/754x400')" id="product-img">
                                        <label class="custom-file-label mt-2 bg-success text-white text-left" for="customFile">Choose file</label>
                                    </div>
                                </div>
								<div class="form-group">
									<label>Type</label>
									<input type="text" class="form-control" readonly name="type" />
								</div>
							</div> <!--- IMG COL END---->
						</div>
					</div> <!---  ADD PRODUCT COL 2 END ---->
				</div> <!---  ADD PRODUCT ROW END ---->
				<input type="hidden" class="form-control" id="product_count" name="product_count" />
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success" form="add-quotation-product-form" id="qProdBtn" >Submit</button>
				<br>
				<br>
			</div>
		</div>
	</div>
</div>
<!-- ======================================================================================================= -->
<div id="request-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Request SI / Commission ?
            <small class="m-0 text-muted">
                Please check is you want to request SI. and Check if you want to request Commission and enter the right amount.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
			<div class="form-group" align="center">
				<div class="frame-wrap">
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="defaultInline1" name="request[]" value="si">
						<label class="custom-control-label" for="defaultInline1">Request Sales Invoice (SI)</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="defaultInline2" name="request[]" value="commi">
						<label class="custom-control-label" for="defaultInline2">Request Commission</label>
					</div>
				</div>
			</div>
			<div class="form-group commi" style="display:none;">
				<label>Contract Amount</label>
				<div class="input-group mar-btm">
					<span class="input-group-btn">
						<button class="btn btn-default" disabled> Php</button> 
					</span> 
					<input class="form-control bg-white" name="contract-amount" onkeypress="return isNumberKey(event)" readonly />
				</div>
			</div>	
			<div class="form-group commi" style="display:none;">
				<label for="customControlValidation5">Commission Type</label>
				<select class="custom-select" name="commi-type" id="customControlValidation5">
					<option value=""></option>
					@foreach(commissionTypes() as $index_commi=>$commi_type)
						<option value="{{$index_commi}}">{{$commi_type}}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">
					Please select commission type.
				</div>
			</div>
			<div class="form-group commi" style="display:none;">	
				<label>Requested Commission Amount</label>
				<div class="input-group mar-btm">
					<span class="input-group-btn">
						<button class="btn btn-default" disabled> Php</button> 
					</span> 
					<input class="form-control" name="commission-amount" onkeypress="return isNumberKey(event)" />
				</div>
			</div>		
			<div class="form-group commi" style="display:none;">
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="customSwitch1" name="vatable" value="true">
					<label class="custom-control-label" for="customSwitch1">VAT</label>
				</div>
			</div>	
			<div class="form-group commi" style="display:none;">
				<label><b>Legend : </b>  <label id="legends"></label></label>
				<br>
				<label>Commission Solution</label>
				<textarea class="form-control bg-white" row="4" readonly name="solution_commi"></textarea>
				<br>
				<div class="input-group mar-btm">
					<span class="input-group-btn">
						<button class="btn btn-default" disabled> Php</button> 
					</span> 
					<input class="form-control bg-white" name="final_commission" onkeypress="return isNumberKey(event)" readonly />
					<span class="input-group-btn">
						<button class="btn btn-default" disabled> Final Commission</button> 
					</span> 
				</div>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="submitRequestBtn" >Save</button>
      </div>
    </div>
  </div>
</div>
<!-- ======================================================================================================= -->
@endsection
@section('scripts')
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/inputmask/inputmask.bundle.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script>
$(function(){
	$('#terms').summernote({
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'clear']],
			['para', ['ul', 'ol', 'paragraph']],
			//['table', ['table']],
			//['view', ['fullscreen', 'codeview', 'help']]
		],
		height:400
	});
	
});
function confirmData(){
	var contract_amount = $('input[name="grand_total"]').val();
	$('input[name="contract-amount"]').val(contract_amount);
	$('#request-modal').modal('show');
	
}
$(document).ready(function(index){
	$('input[name="validity-date"]').keyup(function() {
        $(this).attr('val', '');
    });
    $('input[name="tentative-date"]').keyup(function() {
        $(this).attr('val', '');
    });
	$('input[name="validity-date"]').inputmask();
	$('input[name="tentative-date"]').inputmask();
	var date = new Date();
	date.setDate(date.getDate() - 1);
	$('input[name="validity-date"]')
		.datepicker({
		format: 'yyyy-mm-dd',
		startDate: '+20d',
	});
	var date = new Date();
	date.setDate(date.getDate() - 1);
	$('input[name="tentative-date"]')
		.datepicker({
		format: 'yyyy-mm-dd',
		startDate: '+20d',
	});

	if("{{$amount_infos}}"=='true'){
		$('input[name="sub_total"]').val({{$sub_total_a}});
		$('input[name="installation_charge"]').val({{$installation_charge_a}});
		$('input[name="delivery_charge"]').val({{$delivery_charge_a}});
		$('input[name="discount_product_quotation"]').val({{$total_item_discount_a}});
		$('input[name="discount_quotation"]').val({{$discount_a}});
		$('input[name="total_discount"]').val({{$total_discount_a}});
		$('input[name="grand_total"]').val({{$grand_total_a}});
	}
	
	if("{{$product_availability}}"=='true'){
		var product_path = "{{route('quotation-products')}}";
		$('#page_list').load(product_path,function(){
			var subtotal = $('#temp_sub').val();
			if($.trim(subtotal)){
				
			}else{
				subtotal=0;
			}
			$('input[name="sub_total"]').val(subtotal);
			var city_charge = $('#city-content').find(':selected').data('charge');
			var province_charge = $('#select-province').find(':selected').data('charge');
			var charge = 0;
			if($.trim(city_charge)){
				$('input[name="delivery_charge"]').val(parseFloat(city_charge).toFixed(2));
				charge = $('input[name="delivery_charge"]').val();
			}else{
				if($.trim(city_charge)){
					$('input[name="delivery_charge"]').val(parseFloat(province_charge).toFixed(2));
					charge = $('input[name="delivery_charge"]').val();
				}
			}
			var installation = $('input[name="installation_charge"]').val();
			if(installation==''){
				installation = 0
			}
			var discount_total = $('input[name="total_discount"]').val();
			if(discount_total==''){
				discount_total = 0;
			}
			var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
			if(discount_product_quotation==''){
				discount_product_quotation = 0;
			}
			var delivery_charge = $('input[name="delivery_charge"]').val();
			if(delivery_charge==''){
				delivery_charge = 0;
			}
			var sub_total = $('input[name="sub_total"]').val();

			var grand_total = parseFloat(sub_total)+parseFloat(delivery_charge)+parseFloat(installation)-parseFloat(discount_total);
			$('input[name="grand_total"]').val(grand_total);

			$.post("{{ route('quotation-functions', ['id' => 'quotation-amount-info']) }}",
			{installation: installation,delivery_charge:delivery_charge,sub_total:sub_total,discount_total:discount_total,grand_total:grand_total,discount_product_total:discount_product_quotation},
			function(data){
				// console.log(data);
			});
		});
	}
	$('select[name="payment-terms"]').select2({
		placeholder: "Select Payment Terms",
		allowClear: true,
		width:"100%"
	});
    $('select[name="vat-type"]').select2({
		placeholder: "Select VAT Type",
		allowClear: true,
		width:"100%"
	});
	$('select[name="work-nature"]').select2({
		placeholder: "Select Nature of Work",
		allowClear: true,
		width:"100%"
	});
	$('select[name="jecams-role"]').select2({
		placeholder: "Select JECAMS Role",
		allowClear: true,
		width:"100%"
	});
	$('select[name="client"]').select2({
		placeholder: "Select Client",
		allowClear: true,
		width:"100%"
	});
	$('select[name="commi-type"]').select2({
		placeholder: "Select Commission Type",
		allowClear: true,
		width:"100%"
	});
	$('select[name="branch"]').select2({
		placeholder: "Select Branch",
		allowClear: true,
		width:"100%"
	});
	$('select[name="warranty"]').select2({
		placeholder: "Select Warranty",
		allowClear: true,
		width:"100%"
	});
	$('select[name="delivery-mode"]').select2({
		placeholder: "Select Delivery Mode",
		allowClear: true,
		width:"100%"
	});
	$('select[name="select-region"]').select2({
		placeholder: "Select Region",
		allowClear: true,
		width:"100%"
	});
	$('select[name="select-province"]').select2({
		placeholder: "Select Province",
		allowClear: true,
		width:"100%"
	});
	$('select[name="city-content"]').select2({
		placeholder: "Select City",
		allowClear: true,
		width:"100%"
	});
	$('select[name="barangay-data"]').select2({
		placeholder: "Select Barangay",
		allowClear: true,
		width:"100%"
	});
	$('select[name="swatch"]').select2({
		placeholder: "Select Swatch",
		allowClear: true,
		width:"100%"
	});

	$('#product-description').summernote({
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'clear']],
			['para', ['ul', 'ol', 'paragraph']],
		],
		height:300
	});
	if("{{$quotation_info_active }}"=='true'){
		var new_terms = $('select[name="payment-terms"]').find(':selected').text();
		var new_vat = $('select[name="vat-type"]').find(':selected').text();
		var new_warranty = $('select[name="warranty"]').find(':selected').val();
		$('.quote-terms').text(new_terms);
		$('.vat-type-content').text(new_vat);
		$('#warranty').text(new_warranty);
		if(new_warranty=='No'){
			$('.warranty-content').hide();
			$('#inclusions').text('VI. INCLUSIONS');
			$('#limitation').text('VII.  LIMITATIONS');
			$('#penalty').text('VIII.  PENALTY');
			$('#addendum').text('IX. ADDENDUM, SUPERSESSION and AMENDMENT');
			$('#disclosure').text('X.  NON - DISCLOSURE');
		}else{
			$('.warranty-content').show();
			$('#inclusions').text('VII. INCLUSIONS');
			$('#limitation').text('VIII.  LIMITATIONS');
			$('#penalty').text('IX.  PENALTY');
			$('#addendum').text('X. ADDENDUM, SUPERSESSION and AMENDMENT');
			$('#disclosure').text('XI.  NON - DISCLOSURE');
		}
		var terms_temp = document.getElementById('termsCondition').innerHTML;
		var new_temp = terms_temp;
		$('input[name="terms"]').val(new_temp);
	}
	$(document).on('click','input[name="request[]"]',function(){
		var request_type = $(this).val();
		if ($(this).is(':checked')) {
			if(request_type=='commi'){
				$('.commi').show();
			}
			if(request_type=='si'){
				$('input[name="request-si"]').val('true');
			}
		}else{
			if(request_type=='commi'){
				$('.commi').hide();
			}
			if(request_type=='si'){
				$('input[name="request-si"]').val('false');
			}
		}
	});
	$(document).on('click','#submitRequestBtn',function(){
		if($('#defaultInline1').is(':checked')){
			$('input[name="request-si"]').val('true');
		}else{
			$('input[name="request-si"]').val('false');
		}
		if($('#defaultInline2').is(':checked')){
			var commision_solution = $('textarea[name="solution_commi"]').val();
			var final_commission = $('input[name="final_commission"]').val();
			var commi_type = $('select[name="commi-type"]').find(':selected').val();
			var amount_requested = $('input[name="commission-amount"]').val();

			if($.trim(commi_type)){
				$('select[name="commi-type"]').removeClass('is-invalid');
				$('select[name="commi-type"]').addClass('is-valid');
				if($.trim(amount_requested)){
					$('input[name="commission-amount"]').removeClass('is-invalid');
					$('input[name="commission-amount"]').addClass('is-valid');
					if($.trim(commision_solution)){
						if($.trim(final_commission)){
							$('input[name="final_commission"]').removeClass('is-invalid');
							$('textarea[name="solution_commi"]').removeClass('is-invalid');
							$('input[name="final_commission"]').addClass('is-valid');
							$('textarea[name="solution_commi"]').addClass('is-valid');
							$('#submitRequestBtn').prop('disabled', true);
								Swal.fire({
									title: 'Confirm Save',
									text: "Are you sure you want to save this data ?",
									type: 'warning',
									showCancelButton: true,
									confirmButtonColor: '#d33',
									confirmButtonText: 'Yes!, Save this quotation.'
								}).then((result) => {
									if (result.value) {
										var path = "{{route('quotation-preview')}}";
										window.open(path,'popUpWindow','height=700,width=1000,left=100,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes');
										toastMessage('Success','Your quotation is saving. please wait.','success','toast-top-right');
										setTimeout(function(){ 
											$('#save-quotation-form').submit();
										}, 5000);
										$('#submitRequestBtn').prop('disabled', false);
									}else{
										$('#submitRequestBtn').prop('disabled', false);
									}
								});
						}else{
							$('input[name="final_commission"]').addClass('is-invalid');
							$('textarea[name="solution_commi"]').addClass('is-invalid');
						}
					}else{
						$('input[name="final_commission"]').addClass('is-invalid');
						$('textarea[name="solution_commi"]').addClass('is-invalid');
					}
				}else{
					$('input[name="commission-amount"]').addClass('is-invalid');
				}
			}else{
				$('select[name="commi-type"]').addClass('is-invalid');
			}
		}else{
			$('textarea[name="solution_commi"]').val('');
			$('input[name="final_commission"]').val('');
			$('input[name="commission-solution"]').val('');
			$('input[name="final-commission"]').val('');
			$('input[name="commission-type"]').val('');
			$('input[name="formula-legend"]').val('');
		}
		
	});

	$(document).on('change','select[name="commi-type"]',function(){
		var commi_type = $(this).find(':selected').val();
		var amount_requested = $('input[name="commission-amount"]').val();
		var vat_data = 0;
		var contract_amount = $('input[name="contract-amount"]').val();
		if($.trim(amount_requested)){
			$('input[name="commission-amount"]').removeClass('is-invalid');
			$('input[name="commission-amount"]').addClass('is-valid');
			if($.trim(commi_type)){
				$('select[name="commi-type"]').removeClass('is-invalid');
				$('select[name="commi-type"]').addClass('is-valid');
				if($('input[name="vatable"]').is(':checked')){
					vat_data = 1;
				}
				$.post("{{ route('quotation-functions', ['id' => 'compute-commission']) }}",
                {commi_type: commi_type,amount_requested:amount_requested,vat_data:vat_data,contract_amount:contract_amount},
                function(data){
					$('textarea[name="solution_commi"]').val(data['formula']);
					$('input[name="final_commission"]').val(data['final_commission']);
					$('input[name="commission-solution"]').val(data['formula']);
					$('input[name="final-commission"]').val(data['final_commission']);
					$('input[name="commission-type"]').val(data['type']);
					$('input[name="request-commi"]').val(amount_requested);
					$('input[name="formula-legend"]').val(data['note']);
					$('#legends').text(data['note']);
				});
			}else{
				$('select[name="commi-type"]').addClass('is-invalid');
			}
		}else{
			$('input[name="commission-amount"]').addClass('is-invalid');
		}
	});
	$(document).on('change','input[name="commission-amount"]',function(){
		var commi_type = $('select[name="commi-type"]').find(':selected').val();
		var amount_requested = $(this).val();
		var vat_data = 0;
		var contract_amount = $('input[name="contract-amount"]').val();
		if($.trim(amount_requested)){
			$('input[name="commission-amount"]').removeClass('is-invalid');
			$('input[name="commission-amount"]').addClass('is-valid');
			if($.trim(commi_type)){
				$('select[name="commi-type"]').removeClass('is-invalid');
				$('select[name="commi-type"]').addClass('is-valid');
				if($('input[name="vatable"]').is(':checked')){
					vat_data = 1;
				}
				$.post("{{ route('quotation-functions', ['id' => 'compute-commission']) }}",
                {commi_type: commi_type,amount_requested:amount_requested,vat_data:vat_data,contract_amount:contract_amount},
                function(data){
					$('textarea[name="solution_commi"]').val(data['formula']);
					$('input[name="final_commission"]').val(data['final_commission']);
					$('input[name="commission-solution"]').val(data['formula']);
					$('input[name="final-commission"]').val(data['final_commission']);
					$('input[name="commission-type"]').val(data['type']);
					$('input[name="request-commi"]').val(amount_requested);
					$('input[name="formula-legend"]').val(data['note']);
					$('#legends').text(data['note']);
				});
			}else{
				$('select[name="commi-type"]').addClass('is-invalid');
			}
		}else{
			$('input[name="commission-amount"]').addClass('is-invalid');
		}
	});

	$(document).on('click','input[name="vatable"]',function(){
		var commi_type = $('select[name="commi-type"]').find(':selected').val();
		var amount_requested = $('input[name="commission-amount"]').val();
		var vat_data = 0;
		var contract_amount = $('input[name="contract-amount"]').val();
		if($.trim(amount_requested)){
			if($.trim(commi_type)){
				if($('input[name="vatable"]').is(':checked')){
					vat_data = 1;
				}
				$.post("{{ route('quotation-functions', ['id' => 'compute-commission']) }}",
                {commi_type: commi_type,amount_requested:amount_requested,vat_data:vat_data,contract_amount:contract_amount},
                function(data){
					$('textarea[name="solution_commi"]').val(data['formula']);
					$('input[name="final_commission"]').val(data['final_commission']);
					$('input[name="commission-solution"]').val(data['formula']);
					$('input[name="final-commission"]').val(data['final_commission']);
					$('input[name="commission-type"]').val(data['type']);
					$('input[name="formula-legend"]').val(data['note']);
					$('#legends').text(data['note']);
				});
			}else{
				$('select[name="commi-type"]').addClass('is-invalid');
			}
		}else{
			$('input[name="commission-amount"]').addClass('is-invalid');
		}
	});

	$(document).on('change','select[name="payment-terms"]',function(){
        var new_terms = $(this).find(':selected').text();
        var payment_terms = $('.quote-terms').text();
        $('.quote-terms').text(new_terms);
    });
	$(document).on('change','select[name="warranty"]',function(){
		var warranty = $(this).find(':selected').val();
		if(warranty=='No'){
			$('.warranty-content').hide();
			$('#inclusions').text('VI. INCLUSIONS');
			$('#limitation').text('VII.  LIMITATIONS');
			$('#penalty').text('VIII.  PENALTY');
			$('#addendum').text('IX. ADDENDUM, SUPERSESSION and AMENDMENT');
			$('#disclosure').text('X.  NON - DISCLOSURE');
		}else{
			$('.warranty-content').show();
			$('#inclusions').text('VII. INCLUSIONS');
			$('#limitation').text('VIII.  LIMITATIONS');
			$('#penalty').text('IX.  PENALTY');
			$('#addendum').text('X. ADDENDUM, SUPERSESSION and AMENDMENT');
			$('#disclosure').text('XI.  NON - DISCLOSURE');
		}
		$('#warranty').text(warranty);
	});
	$(document).on('change','select[name="vat-type"]',function(){
        var new_vat = $(this).find(':selected').text();
        $('.vat-type-content').text(new_vat);
    });

	$(document).on('change','select[name="client"]',function(){
		var id = $(this).val();
		$.post("{{ route('quotation-functions', ['id' => 'get-branches']) }}",
		{id: id,},
		function(data){
			if(data.status!='no-branches'){
				$('#branch-content').show();
				$('select[name="branch"]').html(data.client_branches);
			}else{
				$('#branch-content').hide();
			}
				$('select[name="branch"]').prop('required',false);
				$('#complete-address').val(data.client_data.complete_address);
				$('select[name="select-region"]').val(data.region_id).trigger('change');
				$('#client-details-content').show();
				$('#contact_person').val(data.client_data.contact_person);
				$('#position').val(data.client_data.position);
				$('#contact_number').val(data.client_data.contact_numbers);
				$.post("{{ route('quotation-functions', ['id' => 'get-address']) }}",
				{"province_id": data.client_data.province_id,"city_id":data.client_data.city_id,"region_id":data.client_data.province.region_id},
				function(data_new){
					$('#city-content').html(data_new.city);
					$('select[name="select-province"]').html(data_new.province);
					$('select[name="barangay-data"]').html(data_new.barangay);
				});
		});
	});
	$(document).on('change','select[name="select-region"]',function(){
		var id = $(this).val();
		$.post("{{ route('supplier-functions', ['id' => 'fetch-provinces']) }}",
		{id: id,},
		function(data){
			$('select[name="select-province"]').html(data);
		});
	});
	$(document).on('change','select[name="select-province"]',function(){
		var id = $(this).val();
		$.post("{{ route('supplier-functions', ['id' => 'fetch-cities']) }}",
		{id: id,},
		function(data){
			$('select[name="city-content"]').html(data);
		});
	});
	$(document).on('change','select[name="city-content"]',function(){
		var id = $(this).val();
		$.post("{{ route('quotation-functions', ['id' => 'fetch-barangays']) }}",
		{id: id,},
		function(data){
			$('select[name="barangay-data"]').html(data);
		});
	});
	$(document).on('change','select[name="branch"]',function(){
		var id = $(this).val();
		$.post("{{ route('quotation-functions', ['id' => 'fetch-branch-details']) }}",
		{id: id,},
		function(data){
			$('#complete-address').val(data.client_data.complete_address);
			$('select[name="select-region"]').val(data.region).trigger('change');
			$('#client-details-content').show();
			$('#contact_person').val(data.client_data.contact_person);
			$('#position').val(data.client_data.position);
			$('#contact_number').val(data.client_data.contact_numbers);
			$.post("{{ route('quotation-functions', ['id' => 'get-address']) }}",
			{"province_id": data.client_data.province_id,"city_id":data.client_data.city_id,"region_id":data.client_data.province.region_id},
			function(data_new){
				$('#city-content').html(data_new.city);
				$('select[name="select-province"]').html(data_new.province);
				$('select[name="barangay-data"]').html(data_new.barangay);
			});
		});
	});
	$(document).on('click','#add-new-product',function(){
		var nature = "{{$wNature}}";
		$("#dt-products").dataTable().fnDestroy();
		$('#dt-products').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax":{
				url :"{{ route('quotation-functions',['id' => 'product-list-serverside']) }}",
				type: "POST",  
				data : {nature:nature},
				"pageLength": 100,
				"processing": true,
				"serverSide": true,
				"lengthMenu": [[5, 10, 15, 20], [5, 10, 15, 20]],
				error: function(data){  // error handling
					$('#err').html(JSON.stringify(data));
				}
			},
			columns: [
				{ data: 'image', name: 'image',orderable: false, searchable: false},
				{ data: 'product_name', name: 'product_name'},
				{ data: 'type', name: 'type'},
				{ data: 'actions', name: 'actions',orderable: false, searchable: false},
			]
		});
		var timestamp = Date.now();
		// var product_count = $('.qprod_order').length+1;
		$('#product_count').val(timestamp);
		$('#add-product-modal').modal('show');
	});
	
	$(document).on('click','.product',function(){
		$('#variant-content').html('');
		$('#swatch-img').html('');
		$('input[name="view_amount"]').val('');
		$('input[name="product_amount"]').val('');
		$('input[name="product_price"]').val('');
		$('input[name="type"]').val('');
		$('#productsContent').removeClass('show');
		$('#select-product-drop').attr('aria-expanded',false);
		var id = $(this).data('id');
		$.post("{{ route('quotation-functions', ['id' => 'fetch-product-details']) }}",
		{id: id,},
		function(data){
			if(data.product_type=='RAW'||data.product_type=='FIT-OUT'||data.product_type=='SPECIAL-ITEM'){
				$('#swatch-content').hide();
				if(data.product_type=='FIT-OUT'){
					$('#variant-content').html(data.variant);
					$('input[name="fitout_id"]').val(id);
				}else{
					$('#variant-content').html('<input class="form-control" name="variant" type="hidden" />');
					$('input[name="variant"]').val(id);
					$('input[name="variant_name"]').val(data.product_name);
				}
			}else{
				$('#variant-content').html(data.variant);
				$('#swatch-content').show();
			}
			$('input[name="product-id"]').val(data.product_name);
			$('input[name="type"]').val(data.product_type);
			$('#product-previewa').attr('src',data.product_img);
			$('select[name="swatch"]').html(data.swatches_data);
			if(data.swatches_data=='no-swatch'){
				$('#swatch-content').hide();
			}else{
				$('#swatch-content').show();
			}
			$("#product-description").summernote('code',data.description);
			$('input[name="view_amount"]').val(data.display_price);
			$('input[name="product_amount"]').val(data.price);
			$('input[name="product_price"]').val(data.price);
			if(data.product_type!='RAW'){
				if(data.product_type!='SPECIAL-ITEM'){
					$('input[name="variant_name"]').val(data.variant_name);
				}
			}
			
			$('#dt-variant').DataTable( {
				pageLength : 5,
				lengthMenu: [[5, 10, 20, -1], [5, 10, 20, -1]],
				"aaSorting": []
			});
		});
	});

	$(document).on('change','select[name="swatch"]',function(){
		var id = $(this).val();
		$.post("{{ route('quotation-functions', ['id' => 'fetch-swatch-details']) }}",
		{id: id,},
		function(data){
			$('#swatch-img').html(data);
		});
	});
	$(document).on('click','input[name="variant"]',function(){
		var id = $(this).val();
		var variant_n = $(this).data('variant_name');
		$('input[name="variant_name"]').val(variant_n);
		$.post("{{ route('quotation-functions', ['id' => 'fetch-variant-details']) }}",
		{id: id,},
		function(data){
			$('input[name="view_amount"]').val(data.display_price);
			$('input[name="product_amount"]').val(data.price);
			$('input[name="product_price"]').val(data.price);
			$('input[name="type"]').val(data.product_type);
			if(data.product_img!='no-img'){
				$('#product-previewa').attr('src',data.product_img);
			}
		});
	});
	$(document).on('click','.add-variant',function(){
		var id = $(this).data('id');
		var change_value = $(this).data('new_value');
		var product = $(this).data('product');
		var product_enc = $(this).data('product_enc');
		var category = $(this).data('category');
		$('input[name="variant"]').prop('disabled', true);
		$('.add-variant').prop('disabled', true);
		$('#update'+id).html('<td colspan="2">'+
							'For : '+change_value+
							'<div class="input-group alert alert-primary mb-4 input-group-multi-transition">'+
							'<select class="form-control" id="attibute-option"></select>'+
							'<input type="text" maxlength="50" class="form-control" id="attribute-value" placeholder="Value">'+
							'<input type="hidden" maxlength="3" id="product-id" value="'+product+'">'+
							'<input type="hidden" id="value-added" value="'+change_value+'">'+
							'<input type="text" maxlength="50" onkeypress="return isNumberKey(event)" class="form-control" id="base-price" placeholder="Base Price">'+
							'<button type="button" class="btn btn-dark waves-themed waves-effect waves-themed" id="submitAttrBtn" data-id="'+product+'">ADD ATTRIBUTE <i class="fas fa-arrow-right"></i>'+
							'<button type="button" class="btn btn-danger waves-themed waves-effect waves-themed cancel-attribute" data-id="'+product_enc+'"> CANCEL <i class="fas fa-times"></i>'+
							'</div>'
							+'</td>');
			$.post("{{ route('quotation-functions', ['id' => 'fetch_attribute']) }}",
            {
                id: category,
            },
            function(data){
                $('#attibute-option').html(data);
            });

			$('#attibute-option').select2({
				placeholder: "Select Attribute",
				allowClear: true,
				width:"100%"
			});
	});

	$(document).on('click','#submitAttrBtn',function(){
		var attribute = $('#attibute-option').val();
		var attribute_value = $('#attribute-value').val();
		var price = $('#base-price').val();
		var product_id = $('#product-id').val();
		var value_added = $('#value-added').val();
		if($.trim(attribute)){
			if($.trim(attribute_value)){
				$('#attribute-value').removeClass('is-invalid');
				$('#attribute-value').addClass('is-valid');
				if($.trim(price)){
					$('#base-price').removeClass('is-invalid');
					$('#base-price').addClass('is-valid');
					$.post("{{ route('quotation-functions', ['id' => 'create-attribute']) }}",
					{
						attribute: attribute,
						attribute_value:attribute_value,
						price:price,
						product_id:product_id,
						value_added:value_added
					},
					function(data){
						$.post("{{ route('quotation-functions', ['id' => 'fetch-product-details']) }}",
						{id: data,},
						function(data_new){
							$('input[name="view_amount"]').val('');
							$('input[name="product_amount"]').val('');
							$('input[name="product_price"]').val('');
							$('input[name="type"]').val('');
							$('#variant-content').html(data_new.variant);
							$('#dt-variant').DataTable( {
								pageLength : 5,
								lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']]
							});
						});

					});
				}else{
					$('#base-price').addClass('is-invalid');
				}
			}else{
				$('#attribute-value').addClass('is-invalid');
			}
		}else{
			alert_message("Failed","Attribute is required",'danger');
		}
		
		
	});

	$(document).on('click','.cancel-attribute',function(){
		var id = $(this).data('id');
		$.post("{{ route('quotation-functions', ['id' => 'fetch-product-details']) }}",
		{id: id,},
		function(data_new){
			$('input[name="view_amount"]').val('');
			$('input[name="product_amount"]').val('');
			$('input[name="product_price"]').val('');
			$('input[name="type"]').val('');
			$('#variant-content').html(data_new.variant);
			$('#dt-variant').DataTable( {
				pageLength : 5,
				lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']]
			});
		});
	});
	
	$(document).on('click','input[name="variant[]"]',function(){
		var id = $(this).val();
		var price = $('#variant_total_price'+id).val();
		var count = $(this).data('count');
		var variant_name = $(this).data('variant_name');
		var total_price = $('input[name="product_price"]').val();
		if ($(this).is(':checked')) {
			var addition = parseFloat(price)+parseFloat(total_price);
			$('input[name="view_amount"]').val(addition.toFixed(2));
			$('input[name="product_price"]').val(addition);
			$('input[name="product_amount"]').val(addition);
			$('#variantQty'+id).prop('disabled',false);
			$('#variantQty'+id).val(1);
			$('#variant_total_price'+id).addClass('variant_total_price');
			$('#variant_total_price'+id).addClass('compute'+count);
			$('#variant_n'+count).prop('checked',true);
		}else{
			$('#variant_total_price'+id).removeClass('variant_total_price');
			$('#variant_total_price'+id).removeClass('compute'+count);
			$('#variantQty'+id).prop('disabled',true);
			$('#variantQty'+id).val(1);
			var subtract = parseFloat(total_price)-parseFloat(price);
			$('input[name="view_amount"]').val(subtract.toFixed(2));
			$('input[name="product_price"]').val(subtract);
			$('input[name="product_amount"]').val(subtract);
			$('#variant_n'+count).prop('checked',false);
		}
	});

	$(document).on('change','input[name="variant_qty[]"]',function(){
		toastMessage('Success','Compation is saving..','success','toast-bottom-right');
		var qty = $(this).val();
		var price = $(this).data('price');
		var id = $(this).data('id');
		var total_price = $('input[name="product_amount"]').val(); //display
		var total_amount = $('input[name="product_price"]').val();
		var multiply = parseFloat(price)*parseFloat(qty);
		$('#variant_total_price'+id).val(multiply);
		var price_list = $('.variant_total_price').length;
		var x = 1;
		var grand_total= 0;	
		while(x<=price_list){
			var new_data = $('.compute'+x).val();
			grand_total = grand_total+parseFloat(new_data);
			x++;
		}

		$('input[name="view_amount"]').val(grand_total.toFixed(2));
		$('input[name="product_price"]').val(grand_total);
		$('input[name="product_amount"]').val(grand_total);
	});

	$("#page_list").sortable({
        update  : function(event, ui) {
          $("#save-arrangement").show();
          var id_array = new Array();
             $('#page_list tr').each(function(){
                id_array.push($(this).attr("id"));
             });
			 $(document).on('click','#save-arrangement',function(){
				toastMessage('Success','Arrangement is Saving..','success','toast-bottom-right');
				$("#save-arrangement").prop('disabled',true);
				$.post("{{ route('quotation-functions', ['id' => 'save-arrangement']) }}",
				{id_array: id_array,},
				function(success){
					$("#save-arrangement").prop('disabled',false);
					$("#save-arrangement").hide();
					var product_path = "{{route('quotation-products')}}";
					$('#page_list').load(product_path);
				});
			});
        }
    });

	$(document).on('change','input[name="installation_charge"]',function(){
		toastMessage('Success','Compation is saving..','success','toast-bottom-right');
		var installation = $('input[name="installation_charge"]').val();
		if(installation==''){
			installation = 0;
		}
		var delivery_charge = $('input[name="delivery_charge"]').val();
		if(delivery_charge==''){
			delivery_charge = 0;
		}
		var sub_total = $('input[name="sub_total"]').val();
		var discount_total = $('input[name="total_discount"]').val();
		if(discount_total==''){
			discount_total = 0;
		}
		var solution = parseFloat(delivery_charge)+parseFloat(installation)+ parseFloat(sub_total)-parseFloat(discount_total);
		$('input[name="grand_total"]').val(solution);

		var grand_total = $('input[name="grand_total"]').val();
		var discount_product_total = $('input[name="discount_product_quotation"]').val();
		if(discount_product_total==''){
			discount_product_total = 0;
		}
		$.post("{{ route('quotation-functions', ['id' => 'quotation-amount-info']) }}",
		{installation: installation,delivery_charge:delivery_charge,sub_total:sub_total,discount_total:discount_total,grand_total:grand_total,discount_product_total:discount_product_total},
		function(data){
			// console.log(data);
		});
	});
	$(document).on('change','input[name="delivery_charge"]',function(){
		toastMessage('Success','Compation is saving..','success','toast-bottom-right');
		var delivery_charge = $('input[name="delivery_charge"]').val();
		if(delivery_charge==''){
			delivery_charge = 0;
		}
		var installation = $('input[name="installation_charge"]').val();
		if(installation==''){
			installation = 0;
		}
		var sub_total = $('input[name="sub_total"]').val();
		var discount_total = $('input[name="total_discount"]').val();
		if(discount_total==''){
			discount_total = 0;
		}
		var solution = parseFloat(installation)+parseFloat(delivery_charge)+parseFloat(sub_total)-parseFloat(discount_total);
		$('input[name="grand_total"]').val(solution);
		var grand_total = $('input[name="grand_total"]').val();
		var discount_product_total = $('input[name="discount_product_quotation"]').val();
		if(discount_product_total==''){
			discount_product_total = 0;
		}
		$.post("{{ route('quotation-functions', ['id' => 'quotation-amount-info']) }}",
		{installation: installation,delivery_charge:delivery_charge,sub_total:sub_total,discount_total:discount_total,grand_total:grand_total,discount_product_total:discount_product_total},
		function(data){
			// console.log(data);
		});
	});
	$(document).on('change','input[name="discount_quotation"]',function(){
		toastMessage('Success','Compation is saving..','success','toast-bottom-right');
		var discount_quotation = $('input[name="discount_quotation"]').val();
		var discount_product_total = $('input[name="discount_product_quotation"]').val();
		if(discount_product_total==''){
			discount_product_total = 0;
		}
		if(discount_quotation==''){
			discount_quotation = 0;
		}
		var discount_total = parseFloat(discount_quotation)+parseFloat(discount_product_total);
		$('input[name="total_discount"]').val(discount_total);
		var delivery_charge = $('input[name="delivery_charge"]').val();
		if(delivery_charge==''){
			delivery_charge = 0;
		}
		var installation = $('input[name="installation_charge"]').val();
		if(installation==''){
			installation = 0;
		}
		var sub_total = $('input[name="sub_total"]').val();
		var solution = parseFloat(installation)+parseFloat(delivery_charge)+parseFloat(sub_total)-parseFloat(discount_total);
		$('input[name="grand_total"]').val(solution);
		
		var grand_total = $('input[name="grand_total"]').val();
		$.post("{{ route('quotation-functions', ['id' => 'quotation-amount-info']) }}",
		{installation: installation,delivery_charge:delivery_charge,sub_total:sub_total,discount_total:discount_total,grand_total:grand_total,discount_product_total:discount_product_total},
		function(data){
			// console.log(data);
		});
	});
	$(document).on('change','input[name="discountprod[]"]',function(){
		var discount = $(this).val();
		toastMessage('Success','Compation is saving..','success','toast-bottom-right');
		if($.trim(discount)){

		}else{
			discount=0;
		}
		var id = $(this).data('order');
		
		var product_qty_data = $('#qty'+id).val();
		var product_list_price_data = $('#plc'+id).val();
		var product_total_price = parseFloat(product_qty_data)*parseFloat(product_list_price_data);
		
		var product_discounts = $('input[name="discountprod[]"]').map(function(){return $(this).val();}).get();
		var product_solution_discount = parseFloat(product_total_price)-parseFloat(discount);
		$('#total_product_price'+id).text(product_solution_discount);
		$('#tpc'+id).val(product_solution_discount);

		var discount_product_total = 0;
		for(var i=0;i<product_discounts.length;i++){
			var discount_per = 0;
			if($.trim(product_discounts[i])){
				discount_per = product_discounts[i];
			}
			discount_product_total = parseFloat(discount_product_total)+parseFloat(discount_per);
		}
		$('input[name="discount_product_quotation"]').val(discount_product_total);
		var delivery_charge = $('input[name="delivery_charge"]').val();
		if(delivery_charge==''){
			delivery_charge = 0;
		}
		var installation = $('input[name="installation_charge"]').val();
		if(installation==''){
			installation = 0;
		}
		var discount_quotation = $('input[name="discount_quotation"]').val();
		if(discount_quotation==''){
			discount_quotation = 0;
		}
		var sub_total = $('input[name="sub_total"]').val();
		if($.trim(sub_total)){

		}else{
			sub_total=0;
		}

		var solution_total_discount = parseFloat(discount_product_total)+parseFloat(discount_quotation);
		$('input[name="total_discount"]').val(solution_total_discount);

		var solution = parseFloat(sub_total)+parseFloat(delivery_charge)+parseFloat(installation)-parseFloat(solution_total_discount);
		$('input[name="grand_total"]').val(solution);


		$.post("{{ route('quotation-functions', ['id' => 'quotation-amount-info']) }}",
		{installation: installation,
		delivery_charge:delivery_charge,sub_total:sub_total,
		discount_total:solution_total_discount,grand_total:solution,
		discount_product_total:discount_product_total},
		function(data){
			// console.log(data);
			var variant_id = $('input[name="variant_id[]"]').map(function(){return $(this).val();}).get();
			var variant_name = $('input[name="variant_name[]"]').map(function(){return $(this).val();}).get();
			var variant_qty = $('input[name="variant_qty[]"]').map(function(){return $(this).val();}).get();
			var product_type = $('input[name="product_type[]"]').map(function(){return $(this).val();}).get();
			var description = $('textarea[name="description[]"]').map(function(){return $(this).val();}).get();
			var product_qty = $('input[name="product_qty[]"]').map(function(){return $(this).val();}).get();
			var product_list_price = $('input[name="product_list_price[]"]').map(function(){return $(this).val();}).get();
			var total_product_price = $('input[name="total_product_price[]"]').map(function(){return $(this).val();}).get();
			var order_tbl = $('input[name="order_tbl[]"]').map(function(){return $(this).val();}).get();
			var order = $('input[name="order[]"]').map(function(){return $(this).val();}).get();
			var variants_data = $('input[name="variants_data[]"]').map(function(){return $(this).val();}).get();
			var swatches = $('input[name="swatches[]"]').map(function(){return $(this).val();}).get();
			var fitout_id = $('input[name="fitout_id[]"]').map(function(){return $(this).val();}).get();
			// console.log(variant_qty);
			var variant_type = $('input[name="variant_type[]"]').map(function(){return $(this).val();}).get();
			$.post("{{ route('quotation-functions', ['id' => 'quotation-products-temporary']) }}",
			{	
				order: order,
				product_id:variant_name,
				variant_id:variant_id,
				variant_name:variants_data,
				qty:product_qty,
				variant_type:variant_type,
				price:product_list_price,
				product_type:product_type,
				discount:product_discounts,
				description:description,
				total_amount:total_product_price,
				order_tbl:order_tbl,
				swatches:swatches,
				variant_qty:variant_qty,
				fitout_id:fitout_id
			},
			function(data_new){
				console.log(data_new);
			});
		});
	});

	$(document).on('change','input[name="product_qty[]"]',function(){
		var qty = $(this).val();
		toastMessage('Success','Compation is saving..','success','toast-bottom-right');
		if($.trim(qty)){

		}else{
			qty=0
		}
		var id = $(this).data('id');
		var list_price = $('#plc'+id).val();
		if($.trim(list_price)){

		}else{
			list_price=0;
		}
		var discount = $('#dis'+id).val();
		if($.trim(discount)){

		}else{
			discount=0;
		}
		var total_price_solution = parseFloat(qty)*parseFloat(list_price)-parseFloat(discount);
		$('#total_product_price'+id).text(total_price_solution);
		$('#tpc'+id).val(total_price_solution);

		var product_qty = $('input[name="product_qty[]"]').map(function(){return $(this).val();}).get();
		var product_list_price = $('input[name="product_list_price[]"]').map(function(){return $(this).val();}).get();
		var sub_total_temp = 0;
		for(var i=0;i<product_qty.length;i++){
			var temp_qty = 0;
			var temp_total_price = 0;
			if($.trim(product_qty[i])){
				temp_qty = product_qty[i];
			}
			if($.trim(product_list_price[i])){
				temp_total_price= product_list_price[i];
			}
			var temp_price_qty =  parseFloat(temp_qty)*parseFloat(temp_total_price);
			sub_total_temp = parseFloat(sub_total_temp)+parseFloat(temp_price_qty);
		}
		
		$('input[name="sub_total"]').val(sub_total_temp);
		var delivery_charge = $('input[name="delivery_charge"]').val();
		if(delivery_charge==''){
			delivery_charge = 0;
		}
		var installation = $('input[name="installation_charge"]').val();
		if(installation==''){
			installation = 0;
		}
		var total_discount = $('input[name="total_discount"]').val();
		if(total_discount==''){
			total_discount = 0;
		}
		var sub_total = $('input[name="sub_total"]').val();
		if($.trim(sub_total)){

		}else{
			sub_total=0;
		}
		var discount_quotation = $('input[name="discount_quotation"]').val();
		if(discount_quotation==''){
			discount_quotation = 0;
		}
		var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
		if(discount_product_quotation==''){
			discount_product_quotation = 0;
		}
		var solution = parseFloat(sub_total)+parseFloat(delivery_charge)+parseFloat(installation)-parseFloat(total_discount);
		$('input[name="grand_total"]').val(solution);

		$.post("{{ route('quotation-functions', ['id' => 'quotation-amount-info']) }}",
		{installation: installation,
		delivery_charge:delivery_charge,sub_total:sub_total,
		discount_total:total_discount,
		grand_total:solution,
		discount_product_total:discount_product_quotation},
		function(data){
			var variant_id = $('input[name="variant_id[]"]').map(function(){return $(this).val();}).get();
			var variant_name = $('input[name="variant_name[]"]').map(function(){return $(this).val();}).get();
			var product_type = $('input[name="product_type[]"]').map(function(){return $(this).val();}).get();
			var description = $('textarea[name="description[]"]').map(function(){return $(this).val();}).get();
			var product_qty = $('input[name="product_qty[]"]').map(function(){return $(this).val();}).get();
			var product_list_price = $('input[name="product_list_price[]"]').map(function(){return $(this).val();}).get();
			var total_product_price = $('input[name="total_product_price[]"]').map(function(){return $(this).val();}).get();
			var order_tbl = $('input[name="order_tbl[]"]').map(function(){return $(this).val();}).get();
			var order = $('input[name="order[]"]').map(function(){return $(this).val();}).get();
			var variants_data = $('input[name="variants_data[]"]').map(function(){return $(this).val();}).get();
			var swatches = $('input[name="swatches[]"]').map(function(){return $(this).val();}).get();
			var product_discounts = $('input[name="discountprod[]"]').map(function(){return $(this).val();}).get();
			var variant_qty = $('input[name="variant_qty[]"]').map(function(){return $(this).val();}).get();
			var variant_type = $('input[name="variant_type[]"]').map(function(){return $(this).val();}).get();
			var fitout_id = $('input[name="fitout_id[]"]').map(function(){return $(this).val();}).get();
			$.post("{{ route('quotation-functions', ['id' => 'quotation-products-temporary']) }}",
			{	
				order: order,
				product_id:variant_name,
				variant_id:variant_id,
				variant_name:variants_data,
				variant_type:variant_type,
				qty:product_qty,
				price:product_list_price,
				product_type:product_type,
				discount:product_discounts,
				description:description,
				total_amount:total_product_price,
				order_tbl:order_tbl,
				swatches:swatches,
				variant_qty:variant_qty,
				fitout_id:fitout_id
			},
			function(data_new){
				// console.log(data_new);
			});
		});

	});

	$(document).on('change','input[name="product_list_price[]"]',function(){
		var list_price = $(this).val();
		toastMessage('Success','Compation is saving..','success','toast-bottom-right');
		if($.trim(list_price)){

		}else{
			list_price=0
		}
		var id = $(this).data('order');
		var qty = $('#qty'+id).val();
		if($.trim(qty)){

		}else{
			qty=0
		}
		var discount = $('#dis'+id).val();
		if($.trim(discount)){

		}else{
			discount=0;
		}
		var total_price_solution = parseFloat(qty)*parseFloat(list_price)-parseFloat(discount);
		$('#total_product_price'+id).text(total_price_solution);
		$('#tpc'+id).val(total_price_solution);

		var product_qty = $('input[name="product_qty[]"]').map(function(){return $(this).val();}).get();
		var product_list_price = $('input[name="product_list_price[]"]').map(function(){return $(this).val();}).get();
		var sub_total_temp = 0;
		for(var i=0;i<product_qty.length;i++){
			var temp_qty = 0;
			var temp_total_price = 0;
			if($.trim(product_qty[i])){
				temp_qty = product_qty[i];
			}
			if($.trim(product_list_price[i])){
				temp_total_price= product_list_price[i];
			}
			var temp_price_qty =  parseFloat(temp_qty)*parseFloat(temp_total_price);
			sub_total_temp = parseFloat(sub_total_temp)+parseFloat(temp_price_qty);
		}
		
		$('input[name="sub_total"]').val(sub_total_temp);
		var delivery_charge = $('input[name="delivery_charge"]').val();
		if(delivery_charge==''){
			delivery_charge = 0;
		}
		var installation = $('input[name="installation_charge"]').val();
		if(installation==''){
			installation = 0;
		}
		var total_discount = $('input[name="total_discount"]').val();
		if(total_discount==''){
			total_discount = 0;
		}
		var sub_total = $('input[name="sub_total"]').val();
		if($.trim(sub_total)){

		}else{
			sub_total=0;
		}
		var discount_quotation = $('input[name="discount_quotation"]').val();
		if(discount_quotation==''){
			discount_quotation = 0;
		}
		var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
		if(discount_product_quotation==''){
			discount_product_quotation = 0;
		}
		var solution = parseFloat(sub_total)+parseFloat(delivery_charge)+parseFloat(installation)-parseFloat(total_discount);
		$('input[name="grand_total"]').val(solution);

		$.post("{{ route('quotation-functions', ['id' => 'quotation-amount-info']) }}",
		{installation: installation,
		delivery_charge:delivery_charge,sub_total:sub_total,
		discount_total:total_discount,
		grand_total:solution,
		discount_product_total:discount_product_quotation},
		function(data){
			var variant_id = $('input[name="variant_id[]"]').map(function(){return $(this).val();}).get();
			var variant_name = $('input[name="variant_name[]"]').map(function(){return $(this).val();}).get();
			var variant_qty = $('input[name="variant_qty[]"]').map(function(){return $(this).val();}).get();
			var variant_type = $('input[name="variant_type[]"]').map(function(){return $(this).val();}).get();
			var product_type = $('input[name="product_type[]"]').map(function(){return $(this).val();}).get();
			var description = $('textarea[name="description[]"]').map(function(){return $(this).val();}).get();
			var product_qty = $('input[name="product_qty[]"]').map(function(){return $(this).val();}).get();
			var product_list_price = $('input[name="product_list_price[]"]').map(function(){return $(this).val();}).get();
			var total_product_price = $('input[name="total_product_price[]"]').map(function(){return $(this).val();}).get();
			var order_tbl = $('input[name="order_tbl[]"]').map(function(){return $(this).val();}).get();
			var order = $('input[name="order[]"]').map(function(){return $(this).val();}).get();
			var variants_data = $('input[name="variants_data[]"]').map(function(){return $(this).val();}).get();
			var swatches = $('input[name="swatches[]"]').map(function(){return $(this).val();}).get();
			var product_discounts = $('input[name="discountprod[]"]').map(function(){return $(this).val();}).get();
			var fitout_id = $('input[name="fitout_id[]"]').map(function(){return $(this).val();}).get();
			$.post("{{ route('quotation-functions', ['id' => 'quotation-products-temporary']) }}",
			{	
				order: order,
				variant_type:variant_type,
				product_id:variant_name,
				variant_id:variant_id,
				variant_name:variants_data,
				qty:product_qty,
				price:product_list_price,
				product_type:product_type,
				discount:product_discounts,
				description:description,
				total_amount:total_product_price,
				order_tbl:order_tbl,
				swatches:swatches,
				variant_qty:variant_qty,
				fitout_id:fitout_id
			},
			function(data_new){
				// console.log(data_new);
			});
		});
	});
	$(document).on('click','.delete-product',function(){
		var id = $(this).data('productid');
		$('.delete-product').prop('disabled', true);
		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes!, Delete This Product'
		}).then((result) => {
			if (result.value) {
				var total_price = $('#tpc'+id).val();
				if($.trim(total_price)){

				}else{
					total_price=0;
				}
				var discount = $('#dis'+id).val();
				if($.trim(discount)){

				}else{
					discount=0;
				}
				var delivery_charge = $('input[name="delivery_charge"]').val();
				if(delivery_charge==''){
					delivery_charge = 0;
				}
				var installation = $('input[name="installation_charge"]').val();
				if(installation==''){
					installation = 0;
				}
				var sub_total = $('input[name="sub_total"]').val();
				if($.trim(sub_total)){
					sub_total = parseFloat(sub_total)-(parseFloat(total_price)+parseFloat(discount));
					$('input[name="sub_total"]').val(sub_total);
				}else{
					sub_total=0;
				}
				var discount_quotation = $('input[name="discount_quotation"]').val();
				if(discount_quotation==''){
					discount_quotation = 0;
				}
				var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
				if(discount_product_quotation==''){
					discount_product_quotation = 0;
				}else{
					discount_product_quotation = parseFloat(discount_product_quotation)-parseFloat(discount);
					$('input[name="discount_product_quotation"]').val(discount_product_quotation);
				}
				var total_discount = $('input[name="total_discount"]').val();
				if(total_discount==''){
					total_discount = 0;
				}else{
					total_discount = parseFloat(total_discount)-parseFloat(discount);
					$('input[name="total_discount"]').val(total_discount);
				
				}
				var solution = parseFloat(sub_total)+parseFloat(delivery_charge)+parseFloat(installation)-parseFloat(total_discount);
				$('input[name="grand_total"]').val(solution);

				$.post("{{ route('quotation-functions', ['id' => 'quotation-amount-info']) }}",
				{installation: installation,
					delivery_charge:delivery_charge,sub_total:sub_total,
					discount_total:total_discount,
					grand_total:solution,
					discount_product_total:discount_product_quotation},
				function(data){
					$('.productId'+id).remove();
					var variant_id = $('input[name="variant_id[]"]').map(function(){return $(this).val();}).get();
					var variant_name = $('input[name="variant_name[]"]').map(function(){return $(this).val();}).get();
					var product_type = $('input[name="product_type[]"]').map(function(){return $(this).val();}).get();
					var description = $('textarea[name="description[]"]').map(function(){return $(this).val();}).get();
					var product_qty = $('input[name="product_qty[]"]').map(function(){return $(this).val();}).get();
					var product_list_price = $('input[name="product_list_price[]"]').map(function(){return $(this).val();}).get();
					var total_product_price = $('input[name="total_product_price[]"]').map(function(){return $(this).val();}).get();
					var order_tbl = $('input[name="order_tbl[]"]').map(function(){return $(this).val();}).get();
					var order = $('input[name="order[]"]').map(function(){return $(this).val();}).get();
					var variants_data = $('input[name="variants_data[]"]').map(function(){return $(this).val();}).get();
					var swatches = $('input[name="swatches[]"]').map(function(){return $(this).val();}).get();
					var product_discounts = $('input[name="discountprod[]"]').map(function(){return $(this).val();}).get();
					var variant_qty = $('input[name="variant_qty[]"]').map(function(){return $(this).val();}).get();
					var variant_type = $('input[name="variant_type[]"]').map(function(){return $(this).val();}).get();
					var fitout_id = $('input[name="fitout_id[]"]').map(function(){return $(this).val();}).get();
					$.post("{{ route('quotation-functions', ['id' => 'quotation-products-temporary']) }}",
					{	
						order: order,
						product_id:variant_name,
						variant_id:variant_id,
						variant_name:variants_data,
						qty:product_qty,
						variant_type:variant_type,
						price:product_list_price,
						product_type:product_type,
						discount:product_discounts,
						description:description,
						total_amount:total_product_price,
						order_tbl:order_tbl,
						swatches:swatches,
						variant_qty:variant_qty,
						fitout_id:fitout_id
					},
					function(data_new){
						$('.delete-product').prop('disabled', false);
						var product_path = "{{route('quotation-products')}}";
						$('#page_list').load(product_path);
					});
				});
			}else{
				$('.delete-product').prop('disabled', false);
			}
		});
	});
	$(document).on('click','#firstCancel',function(){
		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes!, Cancel This Quotation.'
		}).then((result) => {
			if (result.value) {
				location.reload();
			}
		});
	});
	$(document).on('click','.cancelBtn',function(){
		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes!, Cancel This Quotation.'
		}).then((result) => {
			if (result.value) {
				var id = 1;
				$.post("{{ route('quotation-functions', ['id' => 'quotation-cancel']) }}",
				{	
					id:id
				},
				function(data_new){
					location.reload();
				});
			}
		});
	});

	$(document).on('change','select[name="delivery-mode"]',function(){
		if($(this).val()=='PICK-UP'){
			$('input[name="tentative-date"]').val('');
			console.log('wow');
			var region = $('select[name="select-region"]').val();
			var province =$('select[name="select-province"]').val();
			var city = $('select[name="city-content"]').val();
			var address = $('textarea[name="complete-address"]').val();
			var save_option = $('input[name="save-option"]:checked').val();
			$('#pickupcontent').html('<input type="hidden" name="save-option" value="'+save_option+'"/>'+
									'<input type="hidden" name="select-region" value="'+region+'"/>'+
									'<input type="hidden" name="select-province" value="'+province+'"/>'+
									'<input type="hidden" name="city-content" value="'+city+'"/>'+
									'<input type="hidden" name="barangay-data" value="'+city+'"/>'+
									'<input type="hidden" name="complete-address" value="'+address+'"/>');
			$('select[name="select-region"]').prop('disabled',true);
			$('select[name="select-province"]').prop('disabled',true);
			$('select[name="city-content"]').prop('disabled',true);
			$('select[name="barangay-data"]').prop('disabled',true);
			$('textarea[name="complete-address"]').prop('readonly',true);
			$('input[name="save-option"]').prop('disabled',true);

			$('select[name="select-region"]').prop('required',false);
			$('select[name="select-province"]').prop('required',false);
			$('select[name="city-content"]').prop('required',false);
			$('textarea[name="complete-address"]').prop('required',false);
			$('select[name="barangay-data"]').prop('required',false);
		}else{
			$('input[name="tentative-date"]').val('');
			$('select[name="select-region"]').prop('disabled',false);
			$('select[name="select-province"]').prop('disabled',false);
			$('select[name="city-content"]').prop('disabled',false);
			$('textarea[name="complete-address"]').prop('readonly',false);
			$('input[name="save-option"]').prop('disabled',false);
			$('select[name="barangay-data"]').prop('disabled',false);

			$('select[name="select-region"]').prop('required',true);
			$('select[name="select-province"]').prop('required',true);
			$('select[name="city-content"]').prop('required',true);
			$('select[name="barangay-data"]').prop('required',true);
			$('textarea[name="complete-address"]').prop('required',true);

			$('#pickupcontent').html('');
		}
	});
});
</script>
@endsection
