@extends ('layouts.it-department.app')
@section ('title')
    Quotation Update
@endsection
@section ('styles')
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<style>
.select2-dropdown {
  z-index: 999999;
}
#dt-quotation-products_filter{
    display:none;
}
#dt-quotation-products_length{
    display:none;
}
#dt-quotation-products_paginate{
    display:none;
}
</style>
@php 
    $savedPoint = $quotation->quote_number;
    $destination = 'assets/files/quotation_update/';
    $quotation_save = toTxtFile($destination,$savedPoint,'get');
    $sb_total = $quotation->sub_total;
    $new_installation = $quotation->sub_installation_charge;
    $new_delivery = $quotation->delivery_charge;
    $new_product_discount = $quotation->total_item_discount;
    $new_discount = $quotation->total_discount-$quotation->total_item_discount;
    $new_total_discount = $quotation->total_discount;
    $new_grand_total = $quotation->grand_total;
    $updated = '';
    $saving_mode = '';
    if($quotation_save['success'] === true){
        $updated = 'yes';
        $saving_mode = '| <b class="text-danger">NOT YET SAVED!</b>';
        $datas = $quotation_save['data'];
		$datas = json_decode($datas);
        $sb_total = $datas->sub_total;
        $new_installation = $datas->installation_charge;
        $new_delivery = $datas->delivery_charge;
        $new_product_discount = $datas->total_product_discount;
        $new_discount = $datas->discount;
        $new_total_discount = $datas->total_discount;
        $new_grand_total = $datas->grand_total;
    }
@endphp
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Quotations</li>
<li class="breadcrumb-item active">Quotation Update</li>
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
            <div class="col-lg-12 flex-fill">
                <span class="h5 mt-0">Quotation Update</span>
                <br>
                <p class="mb-0">This only to Update quotation.</p>
            </div>
        </div>
    </div>
</div>
<!-- =================================================================================================================================== -->
<div class="card-footer py-2" align="center">
    <button type="submit" onclick='confirmData()' class="btn btn-info saveQuotationBtn"> <span class="fa fa-save"></span> UPDATE QUOTATION  </button> 
    @if($updated=='yes')
     | <button type="submit" onclick='confirmClearData()' class="btn btn-danger clear"> <span class="fa fa-trash"></span> CLEAR UNSAVE PRODUCTS </button> 
    @endif
    
    @php echo $saving_mode; @endphp
</div>
<!-- =================================================================================================================================== -->
<form method="post" id="quotation-update-form" action="{{ route('quotation-functions', ['id' => 'quotation-update']) }}">
@csrf()
<input class="form-control" value="{{encryptor('encrypt',$quotation->id)}}" name="quotationId" type="hidden" />
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel"> <!---Panel 1 START ---->
            <div class="panel-hdr">
                <h2>
                    Quotation Number : <span class="fw-300"><i>{{$quotation->quote_number}}</i> | <b class="text-danger"> STATUS : </b> {{$quotation->status}}</span> 
                </h2>
               
            </div>
            <div class="panel-container show">
                <div class="panel-content"> <!---Panel Content START ---->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Work Nature</label>
                                <select class="form-control" name="work-nature" required>
                                    <option value=""></option>
                                    @foreach($work_nature as $index=>$works)
                                    @php 
                                        $natureMode = '';
                                        if($index==$quotation->work_nature){
                                            $natureMode = 'selected';
                                        }
                                    @endphp
                                    <option value="{{$index}}" {{$natureMode}}>{{$works}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>JECAMS Role</label>
                                <select class="form-control" name="jecams-role" required>
                                    <option value=""></option>
                                    @foreach($roles as $indexR=>$role)
                                    @php 
                                        $roleMode = '';
                                        if($indexR==$quotation->jecams_role){
                                            $roleMode = 'selected';
                                        }
                                    @endphp
                                    <option value="{{$indexR}}" {{$roleMode}}>{{$role}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Client</label>
                                <select class="form-control" required name="client">
                                    <option value=""></option>
                                    @foreach($clients as $client)
                                    @php 
                                        $clientMode = '';
                                        if($client->id==$quotation->client_id){
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
                                        @php 
                                            echo Branches($quotation->client_id);
                                        @endphp 
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Delivery Mode</label>
                                <select class="form-control" required name="delivery-mode">
                                    <option value=""></option>
                                    @foreach($delivery_modes as $index=>$delivery_mode)
                                    @php 
                                        $deliveryMode='';
                                        if($index==$quotation->delivery_mode){
                                            $deliveryMode='selected';
                                        }
                                    @endphp
                                    <option value="{{$index}}" {{$deliveryMode}}>{{$delivery_mode}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tentative Delivery or Pickup Date </label>
                                <input class="form-control" type="text" data-inputmask="'mask': '9999-99-99'" name="tentative-date" value="{{$quotation->lead_time}}" style="background: white;" placeholder="yyyy-mm-dd" required />
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label></label>
                                <div class="input-group mb-4 input-group-multi-transition">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Subject</span>
                                    </div>
                                        <input class="form-control" name="subject" value="{{$quotation->subject}}" required type="text" placeholder="subject" />
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Validity Date</span>
                                    </div>
                                        <input class="form-control" name="validity-date" value="{{$quotation->validity_date}}" required type="text" style="background: white;" data-inputmask="'mask': '9999-99-99'" placeholder="yyyy-mm-dd" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Payment Terms</label>
                                        <select class="form-control" name="payment-terms" required>
                                            <option value=""></option>
                                            @foreach($payment_terms as $payment_term)
                                            @php 
                                                $termsMode = '';
                                                if($payment_term->id==$quotation->quotation_term_id){
                                                    $termsMode = 'selected';
                                                }
                                            @endphp
                                            <option value="{{$payment_term->id}}" {{$termsMode}}>{{$payment_term->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>VAT Type</label>
                                        <select class="form-control" name="vat-type" required>
                                            <option value=""></option>
                                            @foreach($vat_types as $index_type=>$vat_type)
                                            @php 
                                                $vatMode = '';
                                                if($index_type==$quotation->vat_type){
                                                    $vatMode = 'selected';
                                                }
                                            @endphp
                                            <option value="{{$index_type}}" {{$vatMode}}>{{$vat_type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label></label>
                                <div class="input-group mb-4 input-group-multi-transition">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Contact Person</span>
                                    </div>
                                        <input class="form-control" value="{{$quotation->client->contact_person}}" type="text" style="background:white;"  readonly id="contact_person"/>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Position</span>
                                    </div>
                                        <input class="form-control" value="{{$quotation->client->position}}" style="background:white;"  type="text" readonly id="position" />
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Contact Number</span>
                                    </div>
                                        <input class="form-control" value="{{$quotation->client->contact_numbers}}" style="background:white;"  type="text" readonly id="contact_number" />
                                </div>
                            </div>
                            
                            
                            <div class="form-group deliver-content" @if($quotation->delivery_mode!='DELIVER') style="display:none;" @endif>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Shipping Address</label>
                                        <textarea class="form-control" rows="5" id="shipping-address" name="shipping-address">{{$quotation->shipping_address}}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Billing Address</label>
                                        <textarea class="form-control" rows="5" id="billing-address" name="billing-address">{{$quotation->billing_address}}</textarea>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="form-group deliver-content" @if($quotation->delivery_mode!='DELIVER') style="display:none;" @endif>
                                <div class="row">
                                    <div class="col-md-6">
                                       <label>Region</label>
                                       <select class="form-control" id="select-region" name="select-region">
                                            <option value=""></option>
                                            @foreach($regions as $region)
                                                @php 
                                                $regionMode = '';
                                                if(!empty($quotation->city->region_id)){
                                                    if($quotation->city->region_id==$region->id){
                                                        $regionMode ='selected';
                                                    }
                                                }
                                                @endphp
                                                <option value="{{ encryptor('encrypt',$region->id) }}" {{$regionMode}}>{{ $region->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Province</label>
                                        <select class="form-control" id="select-province" name="select-province">
                                            <option value=""></option>
                                            @php 
                                            if(!empty($quotation->city->region_id)){
                                                echo Client($quotation->city->region_id,'province',$quotation->province_id);
                                            }
                                            @endphp
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group deliver-content" @if($quotation->delivery_mode!='DELIVER') style="display:none;" @endif>
                                <div class="row">
                                    <div class="col-md-6">
                                       <label>City</label>
                                       <select class="form-control" name="city-content" id="city-content">
                                            <option value=""></option>
                                            @php 
                                            if(!empty($quotation->city->region_id)){
                                                echo Client($quotation->province_id,'city',$quotation->city_id );
                                            }
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Barangay</label>
                                        <select class="form-control" name="barangay-data" id="barangay-data">
                                            <option value=""></option>
                                            @php 
                                            if(!empty($quotation->city->region_id)){
                                                echo Client($quotation->city_id,'barangay_update',$quotation->barangay_id );
                                            }
                                            @endphp
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- =========== -->
                        </div>
                    </div>
                </div><!---Panel Content END ---->
            </div>
        </div><!---Panel 1 END ---->
    </div>
    <div class="col-lg-12">
    <div id="panel-2" class="panel"> <!---Panel 2 START ---->
            <div class="panel-hdr">
                <h2>
                    Products - <span class="text-danger"> <b> Note: </b> If there is product to be added first add the products you need before update (qty,price and discount)</span>
                </h2>
                <div class="form-group" align="right">
                    <a class="btn btn-success text-white" id="add-new-product">
                        <i class="fa fa-plus text-white"></i> Add New Product
                    </a>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content"> <!---Panel Content START ---->
                    <div class="table-responsive">
                        <table id="dt-quotation-products" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th width="7%">Qty</th>
                                    <th>List Price</th>
                                    <th>Discount</th>
                                    <th>Total Price</th>
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
                                <textarea class="form-control" rows="4" placeholder="Remarks example: Delivery is using 2 tracks" name="quotation-remarks">{{$quotation->remarks}}</textarea>
                            </div>
                        
                        </div>
                        <div class="col-md-5" align="right">
                            <div class="table-responsive">
                                <table>
                                    <tfoot>
                                        <tr>
                                            <td><b>SUB TOTAL :</b></td>
                                            <td><input type="text" name="sub_total" class="form-control" value="{{number_format((float)$sub_to,2, '.', '')}}" readonly></td>
                                        </tr>
                                        <tr>
                                            <td><b>INSTALLATION CHARGE :</b></td>
                                            <td><input type="text" name="installation_charge" class="form-control" value="{{number_format((float)$quotation->installation_charge,2, '.', '')}}" /></td>
                                        </tr>
                                        <tr>
                                            <td><b>DELIVERY CHARGE :</b></td>
                                            <td><input type="text" name="delivery_charge" class="form-control" value="{{number_format((float)$quotation->delivery_charge,2, '.', '')}}" /></td>
                                        </tr>
                                        <tr>
                                            <td><b>TOTAL PRODUCT DISCOUNT :</b><br><small class="text-danger">*FOR TOTAL PRODUCT EACH DISCOUNT TOTAL</small></td>
                                            <td><input type="text" name="discount_product_quotation" class="form-control" value="{{number_format((float)$quotation->total_item_discount,2, '.', '')}}" readonly/></td>
                                        </tr>
                                        <tr>
                                            <td><b>DISCOUNT :</b><br><small class="text-danger">*FOR WHOLE QUOTATION DISCOUNT</small></td>
                                            @php 
                                                $discount = $quotation->total_discount-$quotation->total_item_discount;
                                                $discount = number_format((float)$discount,2, '.', '');
                                            @endphp 
                                            <td><input type="text" name="discount_quotation" class="form-control" value="{{$discount}}"  /></td>
                                        </tr>
                                        <tr>
                                            <td><b>TOTAL DISCOUNT :</b><br><small class="text-danger">*FOR (TOTAL PRODUCT DISCOUNT) + (DISCOUNT)</small></td>
                                            <td><input type="text" name="total_discount" class="form-control" value="{{number_format((float)$quotation->total_discount,2, '.', '')}}" readonly /></td>
                                        </tr>
                                        <tr>
                                            <td><b>GRAND TOTAL :</b></td>
                                            <td><input type="text" name="grand_total_temp" class="form-control" value="{{number_format((float)$quotation->grand_total,2)}}" readonly />
                                            <input type="hidden" name="grand_total" class="form-control" value="{{number_format((float)$quotation->grand_total,2, '.', '')}}" readonly />
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!---Panel Content END ---->
            </div>
        </div><!---Panel 2 END ---->
    </div>
    <div class="col-lg-12">
        <div id="panel-3" class="panel"> <!---Panel 3 START ---->
            <div class="panel-hdr">
                <h2>
                    Commission and Sales Invoice Request
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    @if((!empty($quotation->is_requested_si))||(!empty($quotation->request_commission)))
                    <div class="form-group" align="center">
                        <div class="frame-wrap">
                        @if(empty($quotation->is_requested_si))
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="defaultInline1" name="request[]" value="si">
                                <label class="custom-control-label" for="defaultInline1">Request Sales Invoice (SI)</label>
                            </div>
                        @endif
                        @if(empty($quotation->request_commission))
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="defaultInline2" name="request[]" value="commi">
                                <label class="custom-control-label" for="defaultInline2">Request Commission</label>
                            </div>
                        @endif
                        </div>
                    </div>
                    @endif
                    @if(!empty($quotation->request_commission))
                    <div class="form-group">
                        <label>Commission Requested</label>
                        <input class="form-control" name="requested-commission" value="{{$quotation->request_commission}}"/>
                    </div>
                    <div class="form-group">
                        <label>Commission Type</label>
                        <select class="custom-select" name="commi-type">
                            <option value=""></option>
                            @foreach(commissionTypes() as $index_commi=>$commi_type)
                                @php 
                                    $commi_type_mode = '';
                                    if($index_commi==$quotation->commission_type){
                                        $commi_type_mode = 'selected'; 
                                    }
                                @endphp
                                <option value="{{$index_commi}}" {{$commi_type_mode}}>{{$commi_type}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Commission Formula</label>
                        <textarea class="form-control" row="5" readonly name="commission-formula">{{$quotation->commission_formula}}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Commission Total</label>
                        <input class="form-control" name="final-commission" value="{{$quotation->final_commission}}" readonly/>
                    </div>
                    @endif
                </div>
            </div>
        </div><!---Panel 3 END ---->
    </div>
    <div class="col-lg-12">
        <div id="panel-3" class="panel"> <!---Panel 3 START ---->
            <div class="panel-hdr">
                <h2>
                    Terms and Condition
                </h2>
            </div>
            <div class="panel-container show">
            <input type="hidden" name="terms" required/>
             <!---Panel Content START ----> <div class="panel-content" id="terms_condition"> 
                   @php 
                        $destination_terms = 'assets/files/quotation_num/';
                        $filename_terms = $quotation->quote_number;
                        $terms = toTxtFile($destination_terms,$filename_terms,'get');
                        if($terms['success'] === true){
                            $datas = $terms['data'];
                            $datas = json_decode($datas);
                            echo $datas->terms;
                        }
                   @endphp 
                   <input class="form-control" name="terms_condition" type="hidden" />
                </div><!---Panel Content END ---->
            </div>
        </div><!---Panel 3 END ---->
    </div>
</div>
</form>
<!-- ====================================================================================== -->
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
			<form id="add-quotation-product-form" method="POST" onsubmit="qProdBtn.disabled = true;" action="{{ route('quotation-functions',['id' => 'update-quotationProduct']) }}" enctype="multipart/form-data">
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

                                    <input type="hidden" class="form-control" readonly name="quotationId" value="{{encryptor('encrypt',$quotation->id)}}" />
                                    <input type="hidden" class="form-control" readonly name="installation_data" />
                                    <input type="hidden" class="form-control" readonly name="delivery_data" />
                                    <input type="hidden" class="form-control" readonly name="discount_data" />
                                    <input type="hidden" class="form-control" readonly name="product_discount_data" />
                                    <input type="hidden" class="form-control" readonly name="total_discount_data" />
								</div>
							</div> <!--- IMG COL END---->
						</div>
					</div> <!---  ADD PRODUCT COL 2 END ---->
				</div> <!---  ADD PRODUCT ROW END ---->
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
<div class="card-footer py-2" align="center">
    <button type="submit" onclick='confirmData()' class="btn btn-info saveQuotationBtn"> <span class="fa fa-save"></span> UPDATE QUOTATION  </button> 
    @if($updated=='yes')
     | <button type="submit" onclick='confirmClearData()' class="btn btn-danger clear"> <span class="fa fa-trash"></span> CLEAR UNSAVE PRODUCTS </button> 
    @endif
    @php echo $saving_mode; @endphp
</div>
<!-- ======================================================================================================= -->
<form method="post" id="quotation-clear-form" action="{{ route('quotation-functions', ['id' => 'quotation-clear']) }}">
@csrf()
<input type="hidden" class="form-control" value="{{encryptor('encrypt',$quotation->id)}}" name="quoteId" />
</form>
<!-- ======================================================================================================= -->
<div id="reason-delete" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Reason to Cancel
            <small class="m-0 text-muted">
                Please input a valid reason.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
            <div class="form-group" align="center" id="on-process-content">
            
            </div>
            <div class="form-group">
                <label>Specify the reason :</label>
                <textarea class="form-control" rows="5" name="reason-remarks" required></textarea>
            </div>
            <input class="form-control" type="hidden" name="quotationProdId" required />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="reasonSubmitBtn" >Submit</button>
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
$("#page_list").sortable();
function confirmData(){
    $('.saveQuotationBtn').prop('disabled', true);
	Swal.fire({
		title: 'Confirm Save',
		text: "Are you sure you want to save this data ?",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		confirmButtonText: 'Yes!, Save this quotation.'
	}).then((result) => {
		if (result.value) {
            toastMessage('Success','Your quotation is saving. please wait.','success','toast-top-right');
			$('#quotation-update-form').submit();
			$('.saveQuotationBtn').prop('disabled', false);
		}else{
			$('.saveQuotationBtn').prop('disabled', false);
		}
	});
}
function confirmClearData(){
    $('.clear').prop('disabled', true);
	Swal.fire({
		title: 'Confirm Clear',
		text: "Are you sure you want to clear this data ?",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		confirmButtonText: 'Yes!, Clear Unsave Products.'
	}).then((result) => {
		if (result.value) {
            toastMessage('Success','Your quotation is saving. please wait.','success','toast-top-right');
			$('#quotation-clear-form').submit();
			$('.clear').prop('disabled', false);
		}else{
			$('.clear').prop('disabled', false);
		}
	});
}
$(document).ready(function(index){
  
    var terms_temp = document.getElementById('terms_condition').innerHTML;
    var new_temp = terms_temp;
    $('input[name="terms_condition"]').val(new_temp);
    $('#dt-quotation-products').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 200,
		"lengthMenu": [[200, 250, 300, 350], [200, 250, 300, 350]],
        "ajax":{
            url :"{{ route('quotation-functions',['id' => 'quotation-products-serverside']) }}",
            type: "POST",  
            data: {id:"{{$quotation->id}}"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'DT_RowIndex',orderable: false, searchable: false },
            { data: 'image', name: 'image',orderable: false, searchable: false},
            { data: 'product_name', name: 'product_name',orderable: false},
            { data: 'description', name: 'description',orderable: false},
            { data: 'qty', name: 'qty',orderable: false},
            { data: 'base_price', name: 'base_price',orderable: false},
            { data: 'discount', name: 'discount',orderable: false},
            { data: 'total_amount', name: 'total_amount',orderable: false},
            { data: 'actions', name: 'actions',orderable: false, searchable: false},
        ]
    });
    $(document).on('click','#add-new-product',function(){
		var nature = $('select[name="work-nature"]').val();
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
        var installation = $('input[name="installation_charge"]').val();
        var delivery = $('input[name="delivery_charge"]').val();
        var discount = $('input[name="discount_quotation"]').val();
        var prod_discount = $('input[name="discount_product_quotation"]').val();
        var total_discount = $('input[name="total_discount"]').val();

        var all_discount = $('input[name="product_discount[]"]').map(function(){return $(this).val();}).get();
        var all_total = $('input[name="product_total[]"]').map(function(){return $(this).val();}).get();
        var all_qty = $('input[name="product_qty[]"]').map(function(){return $(this).val();}).get();
        var all_price = $('input[name="product_price[]"]').map(function(){return $(this).val();}).get();
        var all_id = $('input[name="product_id[]"]').map(function(){return $(this).val();}).get();

        $('input[name="installation_data"]').val(installation);
        $('input[name="delivery_data"]').val(delivery);
        $('input[name="discount_data"]').val(discount);
        $('input[name="product_discount_data"]').val(prod_discount);
        $('input[name="total_discount_data"]').val(total_discount);
      
		$('#add-product-modal').modal('show');
	});

    if("{{$updated}}"=='yes'){
        $('input[name="sub_total"]').val('{{$sb_total}}');
        $('input[name="installation_charge"]').val('{{$new_installation}}');
        $('input[name="delivery_charge"]').val('{{$new_delivery}}');
        $('input[name="discount_product_quotation"]').val('{{$new_product_discount}}');
        $('input[name="discount_quotation"]').val('{{$new_discount}}');
        $('input[name="total_discount"]').val('{{$new_total_discount}}');
        $('input[name="grand_total"]').val('{{$new_grand_total}}');
        $('input[name="grand_total_temp"]').val('{{number_format($new_grand_total,2)}}');
    }
    
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
    $('select[name="commi-type"]').select2({
		placeholder: "Select Commission Type",
		allowClear: true,
		width:"100%"
	});
    
	$('select[name="client"]').select2({
		placeholder: "Select Client",
		allowClear: true,
		width:"100%"
	});
	$('select[name="branch"]').select2({
		placeholder: "Select Branch",
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
    $(document).on('click','#arrange-products',function(){
        $('#arrange-products-modal').modal('show');
    });
    $(document).on('change','select[name="payment-terms"]',function(){
        var new_terms = $(this).find(':selected').text();
        var payment_terms = $('.quote-terms').text();
        $('.quote-terms').text(new_terms);
        var terms_temp = document.getElementById('terms_condition').innerHTML;
        var new_temp = terms_temp;
        $('input[name="terms_condition"]').val(new_temp);
    });
    $(document).on('change','select[name="delivery-mode"]',function(){
        var mode = $(this).val();
       if(mode=='PICK-UP'){
            $('.deliver-content').hide();
       }else{
            $('.deliver-content').show();
       }
    });
	$(document).on('change','select[name="vat-type"]',function(){
        var new_vat = $(this).find(':selected').text();
        $('.vat-type-content').text(new_vat);
        var terms_temp = document.getElementById('terms_condition').innerHTML;
        var new_temp = terms_temp;
        $('input[name="terms_condition"]').val(new_temp);
    });
    $(document).on('change','select[name="select-region"]',function(){
		var id = $(this).val();
		$.post("{{ route('supplier-functions', ['id' => 'fetch-provinces']) }}",
		{id: id,},
		function(data){
			$('select[name="select-province"]').html(data);
		});
        $('select[name="select-province"]').val('').trigger('change');
        $('select[name="city-content"]').val('').trigger('change');
        $('select[name="barangay-data"]').val('').trigger('change');
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
    $(document).on('change','input[name="installation_charge"]',function(){
        var sub_total = $('input[name="sub_total"]').val();
        var installation_charge = $(this).val();
        var delivery_charge = $('input[name="delivery_charge"]').val();
        var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
        var discount_quotation = $('input[name="discount_quotation"]').val();
        var total_discount = $('input[name="total_discount"]').val();
        var grand_total = $('input[name="grand_total"]').val();

        var solution = parseFloat(sub_total)+parseFloat(installation_charge)+parseFloat(delivery_charge)-parseFloat(total_discount);
        $('input[name="grand_total_temp"]').val(formatMoney(solution));
        $('input[name="grand_total"]').val(solution);
    });
    $(document).on('change','input[name="delivery_charge"]',function(){
        var sub_total = $('input[name="sub_total"]').val();
        var installation_charge = $('input[name="installation_charge"]').val();
        if($.trim(installation_charge)){

        }else{
            installation_charge = 0;
        }
        var delivery_charge = $(this).val();
        var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
        if($.trim(discount_product_quotation)){

        }else{
            discount_product_quotation = 0;
        }
        var discount_quotation = $('input[name="discount_quotation"]').val();
        if($.trim(discount_quotation)){

        }else{
            discount_quotation = 0;
        }
        var total_discount = $('input[name="total_discount"]').val();
        if($.trim(total_discount)){

        }else{
            total_discount = 0;
        }
        var grand_total = $('input[name="grand_total"]').val();
      
        var solution = parseFloat(sub_total)+parseFloat(installation_charge)+parseFloat(delivery_charge)-parseFloat(total_discount);
       
        $('input[name="grand_total_temp"]').val(formatMoney(solution));
        $('input[name="grand_total"]').val(solution);
    });

    $(document).on('change','input[name="discount_quotation"]',function(){
        var sub_total = $('input[name="sub_total"]').val();
        var installation_charge = $('input[name="installation_charge"]').val();
        var delivery_charge = $('input[name="delivery_charge"]').val();
        var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
        var discount_quotation = $(this).val();
        var total_discount = parseFloat(discount_product_quotation)+parseFloat(discount_quotation);
        $('input[name="total_discount"]').val(total_discount);
        var grand_total = $('input[name="grand_total"]').val();

        var solution = parseFloat(sub_total)+parseFloat(installation_charge)+parseFloat(delivery_charge)-parseFloat(total_discount);
        $('input[name="grand_total_temp"]').val(formatMoney(solution));
        $('input[name="grand_total"]').val(solution);
    });

    $(document).on('change','input[name="product_qty[]"]',function(){
        $('#add-new-product').hide();
        var order = $(this).data('order');
        var qty = $(this).val();
        var price = $('#price'+order).val();
        var discount = $('#discount'+order).val();
        if($.trim(discount)){}else{discount = 0;}
        if($.trim(qty)){}else{qty = 0;}
        if($.trim(price)){}else{price = 0;}
        var solution = parseFloat(qty)*parseFloat(price)-parseFloat(discount);
        
        $('#itotal'+order).val(formatMoney(solution));
        $('#total'+order).val(solution);

        var all_discount = $('input[name="product_discount[]"]').map(function(){return $(this).val();}).get();
        var all_total = $('input[name="product_total[]"]').map(function(){return $(this).val();}).get();
        
        var sub_total = 0;
        var discount_product_quotation = 0;
        for(var i=0;i<all_total.length;i++){
            sub_total =  parseFloat(sub_total)+parseFloat(all_total[i])+parseFloat(all_discount[i]);
            discount_product_quotation =  parseFloat(discount_product_quotation)+parseFloat(all_discount[i]);
        }
        $('input[name="sub_total"]').val(sub_total);
        $('input[name="discount_product_quotation"]').val(discount_product_quotation);
        var installation_charge = $('input[name="installation_charge"]').val();
        var delivery_charge = $('input[name="delivery_charge"]').val();
        var discount_quotation = $('input[name="discount_quotation"]').val();
        var total_discount = parseFloat(discount_product_quotation)+parseFloat(discount_quotation);
        $('input[name="total_discount"]').val(total_discount);
        var solution = parseFloat(sub_total)+parseFloat(installation_charge)+parseFloat(delivery_charge)-parseFloat(total_discount);
        $('input[name="grand_total_temp"]').val(formatMoney(solution));
        $('input[name="grand_total"]').val(solution);
    });

    $(document).on('change','input[name="product_price[]"]',function(){
        $('#add-new-product').hide();
        var order = $(this).data('order');
        var qty = $('#qty'+order).val();
        var price = $(this).val();
        var discount = $('#discount'+order).val();
        if($.trim(discount)){}else{discount = 0;}
        if($.trim(qty)){}else{qty = 0;}
        if($.trim(price)){}else{price = 0;}
        var solution = parseFloat(qty)*parseFloat(price)-parseFloat(discount);
        
        $('#itotal'+order).val(formatMoney(solution));
        $('#total'+order).val(solution);

        var all_discount = $('input[name="product_discount[]"]').map(function(){return $(this).val();}).get();
        var all_total = $('input[name="product_total[]"]').map(function(){return $(this).val();}).get();
        
        var sub_total = 0;
        var discount_product_quotation = 0;
        for(var i=0;i<all_total.length;i++){
            sub_total =  parseFloat(sub_total)+parseFloat(all_total[i])+parseFloat(all_discount[i]);
            discount_product_quotation =  parseFloat(discount_product_quotation)+parseFloat(all_discount[i]);
        }
        $('input[name="sub_total"]').val(sub_total);
        $('input[name="discount_product_quotation"]').val(discount_product_quotation);
        var installation_charge = $('input[name="installation_charge"]').val();
        var delivery_charge = $('input[name="delivery_charge"]').val();
        var discount_quotation = $('input[name="discount_quotation"]').val();
        var total_discount = parseFloat(discount_product_quotation)+parseFloat(discount_quotation);
        $('input[name="total_discount"]').val(total_discount);
        var solution = parseFloat(sub_total)+parseFloat(installation_charge)+parseFloat(delivery_charge)-parseFloat(total_discount);
        $('input[name="grand_total_temp"]').val(formatMoney(solution));
        $('input[name="grand_total"]').val(solution);
    });

    $(document).on('change','input[name="product_discount[]"]',function(){
        $('#add-new-product').hide();
        var order = $(this).data('order');
        var qty = $('#qty'+order).val();
        var price = $('#price'+order).val();
        var discount = $(this).val();
        if($.trim(discount)){}else{discount = 0;}
        if($.trim(qty)){}else{qty = 0;}
        if($.trim(price)){}else{price = 0;}
        var solution = parseFloat(qty)*parseFloat(price)-parseFloat(discount);
        
        $('#itotal'+order).val(formatMoney(solution));
        $('#total'+order).val(solution);

        var all_discount = $('input[name="product_discount[]"]').map(function(){return $(this).val();}).get();
        var all_total = $('input[name="product_total[]"]').map(function(){return $(this).val();}).get();
        
        var sub_total = 0;
        var discount_product_quotation = 0;
        for(var i=0;i<all_total.length;i++){
            sub_total =  parseFloat(sub_total)+parseFloat(all_total[i])+parseFloat(all_discount[i]);
            discount_product_quotation =  parseFloat(discount_product_quotation)+parseFloat(all_discount[i]);
        }
        $('input[name="sub_total"]').val(sub_total);
        $('input[name="discount_product_quotation"]').val(discount_product_quotation);
        var installation_charge = $('input[name="installation_charge"]').val();
        var delivery_charge = $('input[name="delivery_charge"]').val();
        var discount_quotation = $('input[name="discount_quotation"]').val();
        var total_discount = parseFloat(discount_product_quotation)+parseFloat(discount_quotation);
        $('input[name="total_discount"]').val(total_discount);
        var solution = parseFloat(sub_total)+parseFloat(installation_charge)+parseFloat(delivery_charge)-parseFloat(total_discount);
        $('input[name="grand_total_temp"]').val(formatMoney(solution));
        $('input[name="grand_total"]').val(solution);
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
				lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']]
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
    $(document).on('click','.cancel-product-reason',function(){
        var id = $(this).data('id');
        $('input[name="quotationProdId"]').val(id);
        $('#on-process-content').html('');
        $('#on-process-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
        var path = '{{route("quotation-product-process")}}?id='+id;
        $('#on-process-content').load(path);
        $('#reason-delete').modal('show');
    });
    $(document).on('click','.cancel-product',function(){
        $('.cancel-product').prop('disabled', true);
        var id = $(this).data('id');
        var sub_total = $('input[name="sub_total"]').val();
        var installation_charge = $('input[name="installation_charge"]').val();
        var delivery_charge = $('input[name="delivery_charge"]').val();
        var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
        var discount_quotation = $('input[name="discount_quotation"]').val();
        var total_discount = $('input[name="total_discount"]').val();
        var grand_total = $('input[name="grand_total"]').val();
        var quote_number = "{{$quotation->quote_number}}";
        Swal.fire({
            title: 'Confirm Save',
            text: "Are you sure you want to delete this product ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes!, Delete This Product.'
        }).then((result) => {
            if (result.value) {
                toastMessage('Success','Product Deleted','success','toast-top-right');
                $.post("{{ route('quotation-functions', ['id' => 'delete-product-update']) }}",
                {id: id,quote_number:quote_number,sub_total:sub_total,installation_charge:installation_charge,delivery_charge:delivery_charge,discount_product_quotation:discount_product_quotation,discount_quotation:discount_quotation,total_discount:total_discount,grand_total:grand_total},
                function(data){
                    console.log(data);
                    $('.cancel-product').prop('disabled', false);
                     $('input[name="sub_total"]').val(data.sub_total);
                     $('input[name="installation_charge"]').val(data.installation_charge);
                     $('input[name="delivery_charge"]').val(data.delivery_charge);
                     $('input[name="discount_product_quotation"]').val(data.total_product_discount);
                     $('input[name="discount_quotation"]').val(data.discount);
                     $('input[name="total_discount"]').val(data.total_discount);
                     $('input[name="grand_total"]').val(data.grand_total);
                     $('input[name="grand_total_temp"]').val(data.temp_grand_total);

                        $("#dt-quotation-products").dataTable().fnDestroy();
                        $('#dt-quotation-products').DataTable({
                            "processing": true,
                            "serverSide": true,
                            "pageLength": 200,
                            "lengthMenu": [[200, 250, 300, 350], [200, 250, 300, 350]],
                            "ajax":{
                                url :"{{ route('quotation-functions',['id' => 'quotation-products-serverside']) }}",
                                type: "POST",  
                                data: {id:"{{$quotation->id}}"},
                                "processing": true,
                                "serverSide": true,
                                error: function(datass){  // error handling
                                    $('#err').html(JSON.stringify(datass));
                                }
                            },
                            columns: [
                                { data: 'DT_RowIndex',orderable: false, searchable: false },
                                { data: 'image', name: 'image',orderable: false, searchable: false},
                                { data: 'product_name', name: 'product_name',orderable: false},
                                { data: 'description', name: 'description',orderable: false},
                                { data: 'qty', name: 'qty',orderable: false},
                                { data: 'base_price', name: 'base_price',orderable: false},
                                { data: 'discount', name: 'discount',orderable: false},
                                { data: 'total_amount', name: 'total_amount',orderable: false},
                                { data: 'actions', name: 'actions',orderable: false, searchable: false},
                            ]
                        });
                });
            }else{
                $('.cancel-product').prop('disabled', false);
            }
        });
    });
    $(document).on('click','#reasonSubmitBtn',function(){
        $('#reasonSubmitBtn').prop('disabled', true);
        var id = $('input[name="quotationProdId"]').val();
        var sub_total = $('input[name="sub_total"]').val();
        var installation_charge = $('input[name="installation_charge"]').val();
        var delivery_charge = $('input[name="delivery_charge"]').val();
        var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
        var discount_quotation = $('input[name="discount_quotation"]').val();
        var total_discount = $('input[name="total_discount"]').val();
        var grand_total = $('input[name="grand_total"]').val();
        var quote_number = "{{$quotation->quote_number}}";
        var reason = $('textarea[name="reason-remarks"]').val();
        Swal.fire({
            title: 'Confirm Save',
            text: "Are you sure you want to delete this product ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes!, Delete This Product.'
        }).then((result) => {
            if (result.value) {
                toastMessage('Success','Product Deleted','success','toast-top-right');
                $.post("{{ route('quotation-functions', ['id' => 'delete-product-update']) }}",
                {id: id,reason:reason,quote_number:quote_number,sub_total:sub_total,installation_charge:installation_charge,delivery_charge:delivery_charge,discount_product_quotation:discount_product_quotation,discount_quotation:discount_quotation,total_discount:total_discount,grand_total:grand_total},
                function(data){
                    console.log(data);
                    $('#reasonSubmitBtn').prop('disabled', false);
                     $('input[name="sub_total"]').val(data.sub_total);
                     $('input[name="installation_charge"]').val(data.installation_charge);
                     $('input[name="delivery_charge"]').val(data.delivery_charge);
                     $('input[name="discount_product_quotation"]').val(data.total_product_discount);
                     $('input[name="discount_quotation"]').val(data.discount);
                     $('input[name="total_discount"]').val(data.total_discount);
                     $('input[name="grand_total"]').val(data.grand_total);
                     $('input[name="grand_total_temp"]').val(data.temp_grand_total);

                        $("#dt-quotation-products").dataTable().fnDestroy();
                        $('#dt-quotation-products').DataTable({
                            "processing": true,
                            "serverSide": true,
                            "pageLength": 200,
                            "lengthMenu": [[200, 250, 300, 350], [200, 250, 300, 350]],
                            "ajax":{
                                url :"{{ route('quotation-functions',['id' => 'quotation-products-serverside']) }}",
                                type: "POST",  
                                data: {id:"{{$quotation->id}}"},
                                "processing": true,
                                "serverSide": true,
                                error: function(datass){  // error handling
                                    $('#err').html(JSON.stringify(datass));
                                }
                            },
                            columns: [
                                { data: 'DT_RowIndex',orderable: false, searchable: false },
                                { data: 'image', name: 'image',orderable: false, searchable: false},
                                { data: 'product_name', name: 'product_name',orderable: false},
                                { data: 'description', name: 'description',orderable: false},
                                { data: 'qty', name: 'qty',orderable: false},
                                { data: 'base_price', name: 'base_price',orderable: false},
                                { data: 'discount', name: 'discount',orderable: false},
                                { data: 'total_amount', name: 'total_amount',orderable: false},
                                { data: 'actions', name: 'actions',orderable: false, searchable: false},
                            ]
                        });
                });
            }else{
                $('#reasonSubmitBtn').prop('disabled', false);
            }
        });
    });
    $(document).on('click','.revert-product',function(){
        $('.revert-product').prop('disabled', true);
        var id = $(this).data('id');
        var sub_total = $('input[name="sub_total"]').val();
        var installation_charge = $('input[name="installation_charge"]').val();
        var delivery_charge = $('input[name="delivery_charge"]').val();
        var discount_product_quotation = $('input[name="discount_product_quotation"]').val();
        var discount_quotation = $('input[name="discount_quotation"]').val();
        var total_discount = $('input[name="total_discount"]').val();
        var grand_total = $('input[name="grand_total"]').val();
        var quote_number = "{{$quotation->quote_number}}";
        Swal.fire({
            title: 'Confirm Save',
            text: "Are you sure you want to revert this product ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3387dd',
            confirmButtonText: 'Yes!, Revert This Product.'
        }).then((result) => {
            if (result.value) {
                toastMessage('Success','Product Reverting','success','toast-top-right');
                $.post("{{ route('quotation-functions', ['id' => 'revert-product-update']) }}",
                {id: id,quote_number:quote_number,sub_total:sub_total,installation_charge:installation_charge,delivery_charge:delivery_charge,discount_product_quotation:discount_product_quotation,discount_quotation:discount_quotation,total_discount:total_discount,grand_total:grand_total},
                function(data){
                    $('.revert-product').prop('disabled', false);
                     $('input[name="sub_total"]').val(data.sub_total);
                     $('input[name="installation_charge"]').val(data.installation_charge);
                     $('input[name="delivery_charge"]').val(data.delivery_charge);
                     $('input[name="discount_product_quotation"]').val(data.total_product_discount);
                     $('input[name="discount_quotation"]').val(data.discount);
                     $('input[name="total_discount"]').val(data.total_discount);
                     $('input[name="grand_total"]').val(data.grand_total);
                     $('input[name="grand_total_temp"]').val(data.temp_grand_total);

                        $("#dt-quotation-products").dataTable().fnDestroy();
                        $('#dt-quotation-products').DataTable({
                            "processing": true,
                            "serverSide": true,
                            "pageLength": 200,
                            "lengthMenu": [[200, 250, 300, 350], [200, 250, 300, 350]],
                            "ajax":{
                                url :"{{ route('quotation-functions',['id' => 'quotation-products-serverside']) }}",
                                type: "POST",  
                                data: {id:"{{$quotation->id}}"},
                                "processing": true,
                                "serverSide": true,
                                error: function(datass){  // error handling
                                    $('#err').html(JSON.stringify(datass));
                                }
                            },
                            columns: [
                                { data: 'DT_RowIndex',orderable: false, searchable: false },
                                { data: 'image', name: 'image',orderable: false, searchable: false},
                                { data: 'product_name', name: 'product_name',orderable: false},
                                { data: 'description', name: 'description',orderable: false},
                                { data: 'qty', name: 'qty',orderable: false},
                                { data: 'base_price', name: 'base_price',orderable: false},
                                { data: 'discount', name: 'discount',orderable: false},
                                { data: 'total_amount', name: 'total_amount',orderable: false},
                                { data: 'actions', name: 'actions',orderable: false, searchable: false},
                            ]
                        });
                });
            }else{
                $('.revert-product').prop('disabled', false);
            }
        });
    });
});
</script>
@endsection