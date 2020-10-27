@extends ('layouts.it-department.app')
@section ('title')
    Quotation View
@endsection
@section ('styles')
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<style>
.select2-dropdown {
  z-index: 999999;
}
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Quotations</li>
<li class="breadcrumb-item active">Quotation View</li>
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
                <span class="h5 mt-0">Quotation View</span>
                <br>
                <p class="mb-0">This only to view quotation.  Click this to <a class="btn btn-sm waves-effect btn-warning text-white" data-toggle="modal" data-target="#moved-quotation-modal"><i class="far fa-arrow-alt-square-right text-white"></i> Moved Quotation</a></p>
            </div>
        </div>
    </div>
</div>
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
                <div class="row m-0"> <!---ROW 1 START ---->
                        <div class="col-md-6"> <!---COL 1 START ---->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <b>Nature of Work : </b> <span class="fw-300">{{$quotation->work_nature}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Subject : </b> <span class="fw-300">{{$quotation->subject}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>JECAMS Role : </b> <span class="fw-300">{{$quotation->jecams_role}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Validity Date : </b> <span class="fw-300">{{date('F d,Y',strtotime($quotation->validity_date))}}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <b>Client : </b> <span class="fw-300">{{$quotation->client->name}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Contact Person : </b> <span class="fw-300">{{$quotation->client->contact_person}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Position : </b> <span class="fw-300">{{$quotation->client->position}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Contact Number : </b> <span class="fw-300">{{$quotation->client->contact_numbers}}</span>
                                    </div>
                                </div>
                            </div>
                        </div> <!---COL 1 END ---->
                        <div class="col-md-6"> <!---COL 2 START ---->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <b>VAT Type : </b> <span class="fw-300">{{$quotation->vat_type}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Payment Terms : </b> <span class="fw-300">{{$quotation->terms->name}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Delivery Mode : </b> <span class="fw-300">{{$quotation->delivery_mode}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Expected Date : </b> <span class="fw-300">{{date('F d,Y',strtotime($quotation->lead_time))}}</span>
                                    </div>
                                    @if($quotation->delivery_mode=='DELIVER')
                                    @if(!empty($quotation->shipping_address))
                                    <div class="form-group">
                                        <b>Shipping Address : </b> <span class="fw-300">{{$quotation->shipping_address}}</span>
                                    </div>
                                    @endif 
                                    @if(!empty($quotation->billing_address))
                                    <div class="form-group">
                                        <b>Billing Address : </b> <span class="fw-300">{{$quotation->billing_address}}</span>
                                    </div>
                                    @endif 
                                    @endif 
                                </div>
                                @if($quotation->delivery_mode=='DELIVER')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <b>Region : </b> <span class="fw-300">{{$quotation->city->region->description}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Province : </b> <span class="fw-300">{{$quotation->province->description}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>City : </b> <span class="fw-300">{{$quotation->city->city_name}}</span>
                                    </div>
                                    <div class="form-group">
                                        <b>Barangay : </b> <span class="fw-300">{{$quotation->barangay->barangay_description}}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div> <!---COL 2 END ---->
                    </div> <!---ROW 1 END ---->
                </div><!---Panel Content END ---->
            </div>
        </div><!---Panel 1 END ---->
    </div>
    <div class="col-lg-12">
    <div id="panel-2" class="panel"> <!---Panel 2 START ---->
            <div class="panel-hdr">
                <h2>
                    Products
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content"> <!---Panel Content START ---->
                    <div class="table-responsive">
                        <table id="dt-employees-casual" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>List Price</th>
                                    <th>Discount</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotation->products as $index=>$product)
                                <tr>
                                    <td align="center">{{$index+1}}</td>
                                    <td>
                                        @php 
                                            $enc_product_id = encryptor('encrypt',$product->product_id); 
                                            $defaultLink = 'no-img';
                                            $destination  = 'assets/img/products/'.$enc_product_id.'/';
                                            $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                                            if($defaultLink=='no-img'){
                                                $enc_product_id = encryptor('encrypt',$product->product->parent_id); 
                                                $defaultLink = 'http://placehold.it/754x400';
                                                $destination  = 'assets/img/products/'.$enc_product_id.'/';
                                                $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                                            }
                                        @endphp
                                        <img src="{{$defaultLink}}" style="width:100px;height:100px;" />
                                    </td>
                                    <td>{{$product->product_name}}</td>
                                    <td>
                                        @php 
                                            if(!empty($product->product->parent_id)){
                                                $product_variants = str_replace('|','<br>',$product->product->product_name);
                                                echo $product_variants;
                                            }
                                            if($product->type=='FIT-OUT'){
                                                foreach($product->fitout_products as $fitout){
                                                    $product_variants = str_replace('v:','</b><br>',$fitout->product_name);
                                                    $product_variants = str_replace('|','<br>',$product_variants);
                                                    echo '<b>• '.$product_variants.'<br>';
                                                }
                                            }
                                            echo '<hr class="m-0">'.$product->description;

                                            $total_amount = floatval($product->total_price)-floatval($product->discount);
                                        @endphp
                                    </td>
                                    <td align="center">{{$product->qty}}</td>
                                    <td align="right">PHP {{number_format($product->base_price,2)}}</td>
                                    <td align="right">PHP {{number_format($product->discount,2)}}</td>
                                    <td align="right">PHP {{number_format($total_amount,2)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-7"></div>
                        <div class="col-md-5" align="right">
                            <div class="table-responsive">
                                <table>
                                    <tfoot>
                                        <tr>
                                            <td><b>SUB TOTAL :</b></td>
                                            <td><input type="text" name="sub_total" class="form-control" value="PHP {{number_format($quotation->sub_total,2)}}" readonly></td>
                                        </tr>
                                        <tr>
                                            <td><b>INSTALLATION CHARGE :</b></td>
                                            <td><input type="text" name="installation_charge" class="form-control" value="PHP {{number_format($quotation->installation_charge,2)}}" readonly /></td>
                                        </tr>
                                        <tr>
                                            <td><b>DELIVERY CHARGE :</b></td>
                                            <td><input type="text" name="delivery_charge" class="form-control" value="PHP {{number_format($quotation->delivery_charge,2)}}" readonly /></td>
                                        </tr>
                                        <tr>
                                            <td><b>TOTAL PRODUCT DISCOUNT :</b><br><small class="text-danger">*FOR TOTAL PRODUCT EACH DISCOUNT TOTAL</small></td>
                                            <td><input type="text" name="discount_product_quotation" class="form-control" value="PHP {{number_format($quotation->total_item_discount,2)}}" readonly/></td>
                                        </tr>
                                        <tr>
                                            <td><b>DISCOUNT :</b><br><small class="text-danger">*FOR WHOLE QUOTATION DISCOUNT</small></td>
                                            @php 
                                                $discount = $quotation->total_discount-$quotation->total_item_discount;
                                                $discount = number_format($discount,2);
                                            @endphp 
                                            <td><input type="text" name="discount_quotation" class="form-control" value="PHP {{$discount}}" readonly /></td>
                                        </tr>
                                        <tr>
                                            <td><b>TOTAL DISCOUNT :</b><br><small class="text-danger">*FOR (TOTAL PRODUCT DISCOUNT) + (DISCOUNT)</small></td>
                                            <td><input type="text" name="total_discount" class="form-control" value="PHP {{number_format($quotation->total_discount,2)}}" readonly /></td>
                                        </tr>
                                        <tr>
                                            <td><b>GRAND TOTAL :</b></td>
                                            <td><input type="text" name="grand_total" class="form-control" value="PHP {{number_format($quotation->grand_total,2)}}" readonly /></td>
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
                                    @if(!empty($quotation->request_commission))
                                    <div class="form-group">
                                        <label>Commission Requested</label>
                                        <input class="form-control" name="requested-commission" readonly value="{{$quotation->request_commission}}"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Commission Type</label>
                                        <select class="custom-select" name="commi-type" disabled>
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
                                        <textarea class="form-control"  row="5" readonly name="commission-formula">{{$quotation->commission_formula}}</textarea>
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
                </div><!---Panel Content END ---->
            </div>
        </div><!---Panel 3 END ---->
    </div>
</div>
<!-- =================================================================================== -->
<div class="modal fade" id="moved-quotation-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Move Quotation </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="move-quotation-form" method="POST" onsubmit="quotationMove.disabled = true;" action="{{ route('quotation-functions',['id' => 'move-quotation']) }}">
                @csrf()
                    <div class="form-group">
                        <div class="input-group input-group-multi-transition">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Payment Mode</span>
                            </div>
                            <select class="form-control" name="payment-mode" required>
                                <option value="">--Select Payment Mode--</option>
                                @foreach($payment_modes as $index=>$payment_mode)
                                <option value="{{$index}}" >{{$payment_mode}}</option>
                                @endforeach
                            </select>
                            <div class="input-group-prepend">
                                <span class="input-group-text">VAT Type</span>
                            </div>
                            <select class="form-control" name="vat-type" required>
                                <option value="">--Select VAT Type--</option>
                                @foreach($vat_types as $index_type=>$vat_type)
                                @php 
                                    $mode_data = '';
                                    if($index_type==$quotation->vat_type){
                                        $mode_data = 'selected';
                                    }
                                @endphp
                                <option value="{{$index_type}}" {{$mode_data}}>{{$vat_type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group input-group-multi-transition">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Delivery Mode</span>
                            </div>
                            <select class="form-control" name="delivery-mode" required>
                                <option value="">--Select Delivery Mode--</option>
                                @foreach($delivery_modes as $index_delivery=>$delivery_mode)
                                @php 
                                    $mode = '';
                                    if($index_delivery==$quotation->delivery_mode){
                                        $mode='selected';
                                    }
                                @endphp
                                <option value="{{$index_delivery}}" {{$mode}}>{{$delivery_mode}}</option>
                                @endforeach
                            </select>
                                <div class="input-group-prepend">
                                <span class="input-group-text">Tentative Date</span>
                            </div>
                            <input class="form-control" type="date" aria-label="Tentative Date" required value="{{$quotation->lead_time}}" name="tentative-date" />
                            <input type="text" class="form-control" name="processing-period" required onkeypress="return isNumberKey(event)" aria-label="Processing Period" placeholder="Processing Period">
                            <div class="input-group-append">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="check-content" style="display:none;">
                        <div class="input-group input-group-multi-transition">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Check Number</span>
                            </div>
                            <input type="text" class="form-control" name="check-number" onkeypress="return isNumberKey(event)" aria-label="Check Number" placeholder="Check Number">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Check Date</span>
                            </div>
                            <input class="form-control" type="date" aria-label="Check Date" name="check-date" />
                        </div>
                    </div>
                    <div class="form-group" id="cash-content" style="display:none;">
                        <div class="input-group input-group-multi-transition">
                            <div class="input-group-prepend">
                                <span class="input-group-text">PHP</span>
                            </div>
                            <input type="text" class="form-control" name="amount" onkeypress="return isNumberKey(event)" aria-label="Amount" placeholder="Amount">
                            <div class="input-group-prepend">
                                <span class="input-group-text">PHP</span>
                            </div>
                            <input type="text" class="form-control" name="with-held-amount" onkeypress="return isNumberKey(event)" aria-label="With Held Amount" placeholder="With Held Amount">
                        </div>
                    </div>
                    <div class="form-group" id="online-content" style="display:none;">
                        <label>Bank</label>
                        <select class="form-control" name="bank">
                            <option value=""></option>
                            @foreach($banks as $bank)
                            <option value="{{$bank->id}}">{{$bank->name}}</option>
                            @endforeach
                        </select> 
                    </div>
                    <div class="form-group" id="payment-terms-content" style="display:none;">
                        <label>Payment Terms</label>
                        <select class="form-control" name="payment-terms">
                            <option value=""></option>
                            @foreach($payment_terms as $payment_term)
                            @php 
                                $terms_mode_data = '';
                                if($payment_term->id==$quotation->quotation_term_id){
                                    $terms_mode_data = 'selected';
                                }
                            @endphp
                            <option value="{{$payment_term->id}}" {{$terms_mode_data}}>{{$payment_term->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <input class="form-control" name="qID" type="hidden" value="{{encryptor('encrypt',$quotation->id)}}" readonly/>
                    <textarea name="new-terms" style="display:none;"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" id="quotationMove" form="move-quotation-form" >Moved Quotation</button>
            </div>
        </div>
    </div>
</div>
<!-- =================================================================================== -->
@endsection
@section('scripts')
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script>
$(document).ready(function(index){
    $('select[name="payment-terms"]').select2({
		placeholder: "Select Payment Terms",
		allowClear: true,
		width:"100%"
	});
    $('select[name="bank"]').select2({
		placeholder: "Select Bank",
		allowClear: true,
		width:"100%"
	});
    
    $(document).on('change','select[name="payment-terms"]',function(){
        var new_terms = $(this).find(':selected').text();
        var payment_terms = $('#quote-terms').text();
        $('#quote-terms').text(new_terms);
        var terms_conditon = $('#terms_condition').html();
        $('textarea[name="new-terms"]').val(terms_conditon);
    });

    $(document).on('change','select[name="payment-mode"]',function(){
        var payment_mode = $(this).val();
        if(payment_mode=='CASH'){
            $('#cash-content').show();
            $('#payment-terms-content').show();
            $('input[name="amount"]').prop('required',true);
            $('input[name="with-held-amount"]').prop('required',true);
            $('select[name="payment-terms"]').prop('required',true);
        }
        if(payment_mode=='ONLINE'){
            $('#online-content').show();
            $('#payment-terms-content').show();
            $('input[name="amount"]').prop('required',true);
            $('input[name="with-held-amount"]').prop('required',true);
            $('select[name="payment-terms"]').prop('required',true);
            $('select[name="bank"]').prop('required',true);
        }
        if(payment_mode=='CHECK'){
            $('#check-content').show();
            $('#payment-terms-content').show();
            $('input[name="amount"]').prop('required',true);
            $('input[name="with-held-amount"]').prop('required',true);
            $('input[name="check-number"]').prop('required',true);
            $('input[name="check-date"]').prop('required',true);
            $('select[name="payment-terms"]').prop('required',true);
            $('select[name="bank"]').prop('required',true);
        }
        if(payment_mode=='CASH-ON-DELIVERY'){
            $('#check-content').hide();
            $('#online-content').hide();
            $('#cash-content').hide();
            $('#payment-terms-content').hide();
            $('input[name="amount"]').prop('required',false);
            $('input[name="with-held-amount"]').prop('required',false);
            $('input[name="check-number"]').prop('required',false);
            $('input[name="check-date"]').prop('required',false);
            $('select[name="payment-terms"]').prop('required',false);
            $('select[name="bank"]').prop('required',false);
        }
        if(payment_mode=='TERMS'){
            $('#check-content').hide();
            $('#online-content').hide();
            $('#cash-content').hide();
            $('#payment-terms-content').show();
            $('select[name="payment-terms"]').prop('required',true);
        }
    });
});
</script>
@endsection