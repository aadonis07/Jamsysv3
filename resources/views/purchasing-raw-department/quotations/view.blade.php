@extends ('layouts.purchasing-raw-department.app')
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
                <p class="mb-0">This only to view quotation.  </p>
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
                                    <th>Action</th>
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
                                        @endphp
                                    </td>
                                    <td align="center">{{$product->qty}}</td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div><!---Panel Content END ---->
            </div>
        </div><!---Panel 2 END ---->
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

@endsection
@section('scripts')
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script>
$(document).ready(function(index){
   
});
</script>
@endsection