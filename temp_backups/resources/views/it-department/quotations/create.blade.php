@extends ('layouts.it-department.app')
@section ('title')
    Quotation Create
@endsection
@section('styles')
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<style>
    .select2-dropdown {
        z-index: 999999;
    }
</style>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item">Qutoation</li>
    <li class="breadcrumb-item active">Create Quotation</li>
@endsection
@section('content')
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
							<div class="card">
								<div class="card-header" id="headingOne">
									<a href="javascript:void(0);" class="card-title collapsed bg-info-400 text-white" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
										Quotation Information
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
								<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample" style="">
									<div class="card-body">
										<div class="row"> <!--- START OF ROW 2 ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 1st CONTENT  ---->
												<div class="form-group">
													<label class="form-label" for="work-nature">Nature of Work</label>
													<select class="form-control" name="work-nature" required>
														<option value=""></option>
														@foreach($work_nature as $index=>$works)
														<option value="{{$index}}">{{$works}}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group">
													<label class="form-label" for="subject">Subject</label>
													<input class="form-control" placeholder="Subject" name="subject" required />
												</div>
												<div class="form-group">
													<label class="form-label" for="jecams-role">JECAMS Role</label>
													<select class="form-control" name="jecams-role" required>
														<option value=""></option>
														@foreach($roles as $indexR=>$role)
														<option value="{{$indexR}}">{{$role}}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group">
													<label class="form-label" for="validity-date">Validity Date</label>
													<input class="form-control" name="validity-date" required type="date" />
												</div>
											</div> <!--- END GRID COL 1st CONTENT  ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 2nd CONTENT  ---->
												<div class="form-group">
													<label class="form-label" for="client">Client</label>
													<select class="form-control" required name="client">
														<option value=""></option>
														@foreach($clients as $client)
														<option value="{{$client->id}}">{{$client->name}}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group" id="branch-content" style="display:none;">
													<label class="form-label" for="client">Client Branch</label>
													<select class="form-control" required name="branch">
														<option value=""></option>
													</select>
												</div>
												<div class="form-group" id="client-details-content" style="display:none;">
												<div class="input-group has-length">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text text-success">
                                                            <i class="ni ni-user fs-xl"></i> Contact Person
                                                        </span>
                                                    </div>
                                                    <input type="text" aria-label="First name" id="contact_person" class="form-control" disabled>
                                                </div>
												<div class="input-group has-length">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text text-success">
                                                            <i class="ni ni-user fs-xl"></i> Position
                                                        </span>
                                                    </div>
                                                    <input type="text" aria-label="First name" id="position" class="form-control" disabled>
                                                </div>
												<div class="input-group has-length">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text text-success">
                                                            <i class="ni ni-user fs-xl"></i> Contact Number
                                                        </span>
                                                    </div>
                                                    <input type="text" aria-label="First name" id="contact_number" class="form-control" disabled>
                                                </div>
													
												</div>
											</div> <!--- START GRID COL 2nd CONTENT  ---->
										</div> <!--- END OF ROW 2 ---->
									</div>
									<div class="card-footer py-2" align="right">
										<button class="btn btn-danger">Cancel</button>
										
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" id="headingTwo">
									<a href="javascript:void(0);" class="card-title collapsed bg-info-200 text-white" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
										Delivery and Billing Info
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
								<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample" style="">
									<div class="card-body">
										<div class="row"> <!--START ROW DELIVERY -->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 1st CONTENT DELIVERY  ---->
												<div class="form-group">
													<label>Delivery Mode</label>
													<select class="form-control" required name="delivery-mode">
														<option value=""></option>
														@foreach($delivery_modes as $index=>$delivery_mode)
														<option value="{{$index}}">{{$delivery_mode}}</option>
														@endforeach
													</select>
												</div>
											</div> <!--- END GRID COL 1st CONTENT DELIVERY  ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 2nd CONTENT DELIVERY  ---->
												<div class="form-group">
													<label>Tentative Delivery or Pickup Date</label>
													<input class="form-control" type="date" name="tentative-date" required />
												</div>
											</div> <!--- END GRID COL 2nd CONTENT DELIVERY  ---->
											<div class="col-md-12 col-lg-12 col-xl-12 col-xs-12"> <!--- START GRID COL 3rd CONTENT DELIVERY  ---->
												<hr>
												<div class="form-group">
													<label>Complete Address</label>
													<textarea class="form-control" rows="3" id="complete-address" placeholder="Building No./ Room No. / Floor No. / Barangay , Town / City , Region , Zip Code"></textarea>
												</div>
											</div> <!--- END GRID COL 3rd CONTENT DELIVERY  ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 4th CONTENT DELIVERY  ---->
												<br>
												<div class="form-group mb-2">
													<label>Region </label>
													<select class="form-control" id="select-region" required name="select-region">
														<option value=""></option>
														@foreach($regions as $region)
															<option value="{{ encryptor('encrypt',$region->id) }}">{{ $region->description }}</option>
														@endforeach
													</select>
												</div>
												<div class="form-group mb-2" >
													<label>City/Municipality </label>
													<select class="form-control" name="city-content" id="city-content">
														<option value=""></option>
													</select>
												</div>
												
											</div> <!--- END GRID COL 4th CONTENT DELIVERY  ---->
											<div class="col-md-6 col-lg-6 col-xl-6 col-xs-12"> <!--- START GRID COL 5th CONTENT DELIVERY  ---->
												<br>
												
												<div class="form-group mb-2">
													<label>Province </label>
													<select class="form-control" id="select-province" required name="select-province">
														<option value=""></option>
													</select>
												</div>
												<div class="form-group mb-2">
													<div class="frame-wrap">
														<div class="demo">
															<div class="custom-control custom-radio custom-radio-rounded">
																<input type="radio" class="custom-control-input" id="defaultUncheckedRadio" checked name="save-option" value="BILLING&SHIPPING">
																<label class="custom-control-label" for="defaultUncheckedRadio">Save for Billing and Shipping</label>
															</div>
															<div class="custom-control custom-radio custom-radio-rounded">
																<input type="radio" class="custom-control-input" id="defaultCheckedRadio" name="save-option" value="BILLING">
																<label class="custom-control-label" for="defaultCheckedRadio">Save for Billing Only</label>
															</div>
															<div class="custom-control custom-radio custom-radio-rounded">
																<input type="radio" class="custom-control-input active" id="defaultUncheckedRadio2" name="save-option" value="SHIPPING">
																<label class="custom-control-label" for="defaultUncheckedRadio2">Save for Shipping Only</label>
															</div>
														</div>
													</div>
												</div>
											</div> <!--- END GRID COL 5th CONTENT DELIVERY  ---->
										</div><!--END ROW DELIVERY -->
									</div>
									<div class="card-footer py-2" align="right">
										<button class="btn btn-danger">Cancel</button>
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" id="headingThree">
									<a href="javascript:void(0);" class="card-title collapsed bg-info-400 text-white" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
										Product Information
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
								<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample" style="">
									<div class="card-body">
									<div class="form-group" align="right">
										<button class="btn btn-success" id="add-new-product">
											<i class="fa fa-plus"></i> Add New Product
										</button>
									</div>
										<div class="table-responsive">
											<table id="dt-quotation-product" class="table table-striped table-hover w-100 dataTable dtr-inline">
												<thead class="bg-warning-500 text-center">
													<tr role="row">
														<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" >#</th>
														<th>Product Code</th>
														<th>Qty</th>
														<th>List Price</th>
														<th>Total</th>
														<th>Description</th>
														<th>Action</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" id="headingFour">
									<a href="javascript:void(0);" class="card-title collapsed bg-info-200 text-white" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
										Terms and conditions
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
								<div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample" style="">
									<div class="card-body">
									<textarea style="display:none" id="terms" name="terms">
										{{$terms}}
									</textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					</div> <!--- END FRAME WRAP---->
				</div> <!--- END PANEL CONTENT---->
			</div>
		</div>
    </div>
</div>
<!-- ======================================================================================================= -->
<div class="modal fade modal-fullscreen example-modal-fullscreen"  id="add-product-modal" role="dialog" >
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="row"> <!---  ADD PRODUCT ROW START ---->
					<div class="col-md-4 col-lg-4 col-xl-4 col-sm-12 col-xs-12"> <!---  ADD PRODUCT COL 1 START ---->
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
							<input class="form-control" readonly name="product-id"/>
						</div>
						<div class="form-group" id="variant-content">

						</div>
					</div> <!---  ADD PRODUCT COL 1 END ---->
					<div class="col-md-8 col-lg-8 col-xl-8 col-sm-12 col-xs-12"><!---  ADD PRODUCT COL 2 START ---->
						<div class="row">
							<div class="col-md-8 col-lg-8 col-xl-8 col-sm-12 col-xs-12"> <!--- PRODUCT PRICE COL START---->
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
												<input class="form-control" name="product_qty" type="number"  step="any" />
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
										<input class="form-control" name="product_price" type="number"  step="any" />
									</div>
								</div>
								<div class="form-group">
									<div class="row">
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
							<div class="col-md-4 col-lg-4 col-xl-4 col-sm-12 col-xs-12"> <!--- IMG COL START---->
								<img class="img-fluid text-center" id="product-previewa" style="width: 754px;height:400px;border: 1px solid #0000000f;" src="http://placehold.it/754x400">
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" name="img" class="custom-file-input" onChange="readURL(this.id,'product-previewa','http://placehold.it/754x400')" id="product-img">
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
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<br>
				<br>
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
	$('#product-description').summernote({
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'clear']],
			['para', ['ul', 'ol', 'paragraph']],
			//['table', ['table']],
			//['view', ['fullscreen', 'codeview', 'help']]
		],
		height:300
	});
});
$(document).ready(function(index){
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
	$('select[name="swatch"]').select2({
		placeholder: "Select Swatch",
		allowClear: true,
		width:"100%"
	});

	$(document).on('change','select[name="client"]',function(){
		var id = $(this).val();
		$.post("{{ route('quotation-functions', ['id' => 'get-branches']) }}",
		{id: id,},
		function(data){
			if(data.status!='no-branches'){
				$('#branch-content').show();
				$('#client-details-content').hide();
				$('select[name="branch"]').prop('required',true);
				$('select[name="branch"]').html(data.client_data);
				$('#complete-address').val('');
				
				$('select[name="select-region"]').val('').trigger('change');
				$('select[name="city-content"]').val('').trigger('change');
				$('select[name="select-province"]').val('').trigger('change');
				
			}else{
				$('select[name="branch"]').prop('required',false);
				$('#branch-content').hide();
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
				});
			}
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
			});
		});
	});
	$(document).on('click','#add-new-product',function(){
		$("#dt-products").dataTable().fnDestroy();
		$('#dt-products').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax":{
				url :"{{ route('quotation-functions',['id' => 'product-list-serverside']) }}",
				type: "POST",  
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
				{ data: 'actions', name: 'actions',orderable: false, searchable: false},
			]
		});
		$('#add-product-modal').modal('show');
	});
	
	$(document).on('click','.product',function(){
		$('#variant-content').html('');
		$('input[name="view_amount"]').val('');
		$('input[name="product_amount"]').val('');
		$('input[name="product_price"]').val('');
		$('input[name="product_price"]').val('');
		$('input[name="type"]').val('');
		$('#productsContent').removeClass('show');
		$('#select-product-drop').attr('aria-expanded',false);
		var id = $(this).data('id');
		$.post("{{ route('quotation-functions', ['id' => 'fetch-product-details']) }}",
		{id: id,},
		function(data){
			$('#variant-content').html(data.variant);
			$('input[name="product-id"]').val(data.product_name);
			$('input[name="type"]').val(data.product_type);
			$('#product-previewa').attr('src',data.product_img);
			$('select[name="swatch"]').html(data.swatches_data);
			$("#product-description").summernote("code", data.description);
			
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
		$.post("{{ route('quotation-functions', ['id' => 'fetch-variant-details']) }}",
		{id: id,},
		function(data){
			$('input[name="view_amount"]').val(data.display_price);
			$('input[name="product_amount"]').val(data.price);
			$('input[name="product_price"]').val(data.price);
			$('input[name="product_price"]').val(data.price);
			$('input[name="type"]').val(data.product_type);
		});
	});
});
</script>
@endsection
