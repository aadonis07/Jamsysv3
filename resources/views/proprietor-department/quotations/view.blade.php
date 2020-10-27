@extends ('layouts.proprietor-department.app')
@section ('title')
    Quotation List
@endsection
@section('styles')

@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item ">Quotation</li>
    <li class="breadcrumb-item ">List</li>
    <li class="breadcrumb-item active">{{ $quotation->quote_number }}</li>
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
                <div class="col-md-7">
                    <div class="flex-fill">
                        <span class="h5 mt-0">QUOTATION [ <text class="text-info">{{ $quotation->quote_number }}</text> ] DETAILS</span>
                    <p class="mb-0">Quotation details.</p>
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <p class="h5 mb-0">STATUS:
                        <b class="text-success">{{ $quotation->status }}</b>
                    </p>
                    <p class="mb-0">Created By: {{ $quotation->createdBy->username }} @ {{ readableDate($quotation->created_at)  }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="panel-1" class="panel">
                <div class="row p-3">
                    <div class="col-md-12">
                        <div class=" mb-3 alert alert-primary alert-dismissible">
                            <div class="row">
                                <div class="col-md-3">
                                    <span class="h5 mb-0">Quotation</span>
                                    <p class="mb-0"><text class="text-dark"><b>[ {{ $quotation->quote_number }} ]</b></text> {{ $quotation->subject }}</p>
                                    <p class="mb-0">WORK NATURE: {{ $quotation->work_nature }}</p>
                                    <p class="">AGENT: {{ $quotation->agent->user->username }}</p>
                                    <span class="h5 mb-0">Role:</span>
                                    <p class="mb-0">J. ROLE: {{ $quotation->jecams_role }}</p>
                                    <p class="">VALIDITY: {{ readableDate($quotation->validity_date) }}</p>
                                </div>
                                <div class="col-md-3">
                                    <span class="h5 mb-0">Client's Information</span>
                                    <p class="mb-0">NAME: {{ $quotation->client->name }}</p>
                                    <p class="">TIN: {{ $quotation->client->tin_number }}</p>
                                    <span class="h5 mb-0">Contact Details:</span>
                                    <p class="mb-0">C. PERSON: {{ $quotation->client->contact_person }}</p>
                                    <p class="mb-0">C. NUMBER: {{ $quotation->client->contact_numbers }}</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="h5 mb-0">Payments & Delivery</span>
                                            <p class="mb-0">VAT TYPE: {{ $quotation->vat_type }}</p>
                                            @if($quotation->terms->name)
                                                <p class="mb-0">P. TERMS: {{ $quotation->terms->name }}</p>
                                            @else
                                                <p class="mb-0">P. TERMS: ---</p>
                                            @endif
                                            <p class="">D. MODE: {{ $quotation->delivery_mode }} @ {{ readableDate($quotation->lead_time) }}</p>
                                        </div>
                                        <div class="col-md-5">
                                            @if($quotation->billing_address == $quotation->shipping_address)
                                                <span class="h5 mb-0">Billing & Shipping</span>
                                                <p class="mb-0">{{ $quotation->shipping_address }}</p>
                                            @else
                                                <p class="mb-0">SHIPPING: {{ $quotation->shipping_address }}</p>
                                                <p class="mb-0">BILLING: {{ $quotation->shipping_address }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table id="po-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                            <tr role="row">
                                <th width="5%">No</th>
                                <th width="15%">Image</th>
                                <th width="20%">Name</th>
                                <th width="20%">Description</th>
                                <th width="10%">Qty</th>
                                <th width="10%">List Price</th>
                                <th width="10%">Discount</th>
                                <th width="10%">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($quotation->products as $index=>$product)
                                <tr>
                                    <td align="center">{{$index+1}}</td>
                                    <td align="center">
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
                    </div>
                    @if((!empty($quotation->request_commission)) || (!empty($quotation->is_requested_si)))
                        <div class="col-lg-12">
                            <div id="panel-3" class="panel"> <!---Panel 3 START ---->
                                <div class="panel-hdr">
                                    <h2>
                                        Commission and Sales Invoice Request
                                    </h2>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content">
                                        @if(!empty($quotation->is_requested_si))     
                                            <div class="alert alert-success">
                                                <strong>Sales Invoice is already Requested</strong> This indicate that your sales invoice request is in request panel of Accounting.
                                            </div>
                                        @endif               
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
                        @endif
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
            </div>

        </div>
    </div>
@endsection
@section('scripts')

@endsection
