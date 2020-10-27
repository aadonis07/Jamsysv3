@extends ('layouts.it-department.app')
@section ('title')
    {{ $product->product_name }} Variants
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/summernote/summernote.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('product-list') }}">Products</a></li>
    <li class="breadcrumb-item ">{{ $product->product_name }}</li>
    <li class="breadcrumb-item active">Variants</li>
@endsection
@section('content')
    <div class="row mb-3">
        <div class="col-md-10">
            <div class="d-flex flex-start w-100">
                <div class="mr-2 hidden-md-down">
                <span class="icon-stack icon-stack-lg">
                    <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                    <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
                    <i class="ni ni-blog-read icon-stack-1x opacity-100 color-white"></i>
                </span>
                </div>
                <div class="d-flex flex-fill">
                    <div class="flex-fill">
                        <span class="h5 mt-0">{{ strtoupper($product->product_name) }} <text class="text-primary small">[ {{ $product->type }} ] ITEM</text></span>
                        <br>
                        <p class="mb-0">List of {{ $product->product_name }} variants .</p>
                    </div>
                </div>
            </div>
        </div>
        @if($product->type == 'FIT-OUT')
            @php
                $parent_id = encryptor('encrypt',$product->id);
            @endphp
            <div class="col-md-2">
                <button onClick="productList()" class="btn btn-primary btn-sm btn-block">Add Product</button>
            </div>
            <div class="modal fade" id="products-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header ">
                            <h4 class="modal-title mb-2">
                                [ RAW, SUPPLY, SPECIAL ITEM ]
                                <small class="m-0 text-muted">
                                    Please select product will be added to fit-out
                                </small>
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><i class="fal fa-times"></i></span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs " role="tablist">
                                        <li class="nav-item  "><a class="nav-link active fs-sm text-primary" data-toggle="tab" href="#supply-section" role="tab">SUPPLY</a></li>
                                        <li class="nav-item  "><a class="nav-link fs-sm text-primary" data-toggle="tab" href="#raw-section" role="tab">RAW / SPECIAL ITEMS</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-12">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="supply-section" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-12 p-3">
                                                    <div class="accordion accordion-clean accordion-hover" id="add-product-supply-section">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <a href="javascript:void(0);" class="card-title" data-toggle="collapse" data-target="#select-supply-product-section" aria-expanded="true">
                                                        <span class="mr-2">
                                                            <span class="collapsed-reveal">
                                                                <i class="fal fa-minus fs-xl"></i>
                                                            </span>
                                                            <span class="collapsed-hidden">
                                                                <i class="fal fa-plus fs-xl"></i>
                                                            </span>
                                                        </span>
                                                                    <h4 class="m-0">Please Select Product <b class="text-warning">[ SUPPLY ]</b></h4>
                                                                </a>
                                                            </div>
                                                            <div id="select-supply-product-section" class="collapse show" data-parent="#add-product-supply-section">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <table id="supply-product-table" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                                                                <thead class="bg-warning-500">
                                                                                <tr>
                                                                                    <th width="5%"></th>
                                                                                    <th width="60%">Product</th>
                                                                                    <th width="30%">Category</th>
                                                                                    <th >Sub Category</th>
                                                                                    <th width="5%">Actions</th>
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
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#select-supply-variant-section" aria-expanded="false">
                                                                <span class="mr-2">
                                                                    <span class="collapsed-reveal">
                                                                        <i class="fal fa-minus fs-xl"></i>
                                                                    </span>
                                                                    <span class="collapsed-hidden">
                                                                        <i class="fal fa-plus fs-xl"></i>
                                                                    </span>
                                                                </span>
                                                                    <h4 class="m-0">Please Select <strong><text class="text-warning" id="product-name"></text></strong> Variant</h4>
                                                                </a>
                                                            </div>
                                                            <div id="select-supply-variant-section" class="collapse" data-parent="#add-product-supply-section">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <table id="supply-product-variant-table" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                                                                <thead class="bg-warning-500">
                                                                                <tr>
                                                                                    <th width="5%"></th>
                                                                                    <th width="50%">Variant</th>
                                                                                    <th width="10%">Actions</th>
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
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#supply-product-section" aria-expanded="false">
                                                                <span class="mr-2">
                                                                    <span class="collapsed-reveal">
                                                                        <i class="fal fa-minus fs-xl"></i>
                                                                    </span>
                                                                    <span class="collapsed-hidden">
                                                                        <i class="fal fa-plus fs-xl"></i>
                                                                    </span>
                                                                </span>
                                                                    <h4 class="m-0">Add to Fit-Out List</h4>
                                                                </a>
                                                            </div>
                                                            <div id="supply-product-section" class="collapse" data-parent="#add-product-supply-section">
                                                                <div class="card-body">
                                                                    <form method="post" id="add-product-supply-form" action="{{ route('product-functions',['id' => 'add-to-fitout-product']) }}" >
                                                                        @csrf()
                                                                        <div class="row">
                                                                        <div class="col-md-6">
                                                                            <input type="hidden" id="supply-key" name="key"  value="">
                                                                            <input type="hidden" id="supply-type" name="type"  value="">
                                                                            <input type="hidden" name="parent"  value="{{ $parent_id }}">
                                                                            <div class="form-group mb-1">
                                                                                <label class="form-control-plaintext">Product Name</label>
                                                                                <input id="variant-parent-name" readonly class="form-control form-control-sm"/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group mb-1">
                                                                                <label class="form-control-plaintext">Variant</label>
                                                                                <input id="variant-name" readonly class="form-control form-control-sm"/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group mb-1">
                                                                                <label class="form-control-plaintext">
                                                                                    Base Price <text class="text-danger">* Base Price is based on product. You can modify this price on the list.</text>
                                                                                </label>
                                                                                <div class="input-group input-group-sm">
                                                                                    <div class="input-group-prepend">
                                                                                        <div class="input-group-text text-dark">
                                                                                            &#8369;
                                                                                        </div>
                                                                                    </div>
                                                                                    <input  id="base-price" name="price" required type="number" step=".01" class="form-control form-control-sm"/>
                                                                                </div>
                                                                                <div class="form-group mb-1 text-right">
                                                                                    <button type="submit" form="add-product-supply-form" onClick="addTofitoutItem('add-fitout-btn')" class="add-fitout-btn btn btn-warning mt-2">Add to list</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="raw-section" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-12 p-3">
                                                    <div class="accordion accordion-clean accordion-hover" id="add-product-raw-section">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <a href="javascript:void(0);" class="card-title" data-toggle="collapse" data-target="#select-raw-product-section" aria-expanded="true">
                                                                    <span class="mr-2">
                                                                        <span class="collapsed-reveal">
                                                                            <i class="fal fa-minus fs-xl"></i>
                                                                        </span>
                                                                        <span class="collapsed-hidden">
                                                                            <i class="fal fa-plus fs-xl"></i>
                                                                        </span>
                                                                    </span>
                                                                    <h4 class="m-0">Please Select Product <b class="text-warning">[ RAW / SPECIAL ITEM ]</b></h4>
                                                                </a>
                                                            </div>
                                                            <div id="select-raw-product-section" class="collapse show" data-parent="#add-product-raw-section">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <table id="raw-product-table" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                                                                <thead class="bg-warning-500">
                                                                                <tr>
                                                                                    <th width="5%"></th>
                                                                                    <th width="50%">Product</th>
                                                                                    <th width="35%">Category</th>
                                                                                    <th >Sub Category</th>
                                                                                    <th width="10%">Actions</th>
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
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#raw-product-section" aria-expanded="false">
                                                                <span class="mr-2">
                                                                    <span class="collapsed-reveal">
                                                                        <i class="fal fa-minus fs-xl"></i>
                                                                    </span>
                                                                    <span class="collapsed-hidden">
                                                                        <i class="fal fa-plus fs-xl"></i>
                                                                    </span>
                                                                </span>
                                                                    <h4 class="m-0">Add to Fit-Out List</h4>
                                                                </a>
                                                            </div>
                                                            <div id="raw-product-section" class="collapse" data-parent="#add-product-raw-section">
                                                                <div class="card-body">
                                                                    <form method="post" id="add-product-raw-form" action="{{ route('product-functions',['id' => 'add-to-fitout-product']) }}" >
                                                                        @csrf()
                                                                        <div class="row">
                                                                            <div class="col-md-9">
                                                                                <div class="form-group mb-1">
                                                                                    <input type="hidden" id="raw-key" name="key" value="">
                                                                                    <input type="hidden" id="raw-type" name="type" value="">
                                                                                    <input type="hidden" name="parent"  value="{{ $parent_id }}">
                                                                                    <label class="form-control-plaintext">Product Name</label>
                                                                                    <input id="raw-product-name" readonly class="form-control form-control-sm"/>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <div class="form-group mb-1">
                                                                                    <label class="form-control-plaintext">
                                                                                        Base Price
                                                                                    </label>
                                                                                    <div class="input-group input-group-sm">
                                                                                        <div class="input-group-prepend">
                                                                                            <div class="input-group-text text-dark">
                                                                                                &#8369;
                                                                                            </div>
                                                                                        </div>
                                                                                        <input id="raw-base-price"  required name="price" type="number" step=".01" class="form-control form-control-sm"/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group mb-1 text-right">
                                                                                    <button type="submit" form="add-product-raw-form" class="add-fitout-btn btn btn-warning mt-2">Add to list</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
<div class="row">
    <div class="col-md-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2 class="text-danger">
                    * Attributes and Values
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
            <div class="row p-5">
                <div class="col-md-12">
                    @if($product->type == 'FIT-OUT')
                        <table id="variant-tbl" class="table table-bordered w-100">
                            <thead>
                            <tr>
                                <th width="5%"></th>
                                <th width="70%">Attributes & Values</th>
                                <th width="25%">Base Price</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $index=>$variant)
                                    @php
                                        $enc_product_id = encryptor('encrypt',$variant->parent_id);
                                        $enc_variant_id = encryptor('encrypt',$variant->id);
                                    @endphp
                                    <tr>
                                        <td>{{ ( $index + 1 ) }}</td>
                                        <td>
                                            {{ $variant->product_name }}
                                            <hr class="m-0">
                                            <text class="text-primary">Remarks: {{ $variant->remarks }}</text>
                                        </td>
                                        <td>
                                            <form class="" role="form" id="update-variant-{{ $enc_variant_id }}-price-form" onsubmit="$('#{{ $enc_variant_id }}-price-btn').attr('disabled',true)" method="POST" action="{{ route('product-functions',['id' => 'update-variant-price']) }}">
                                                @csrf()
                                                <input type="hidden" value="{{ $enc_product_id }}" name="product"/>
                                                <input type="hidden" value="{{ $enc_variant_id }}" name="variant"/>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text text-dark">
                                                            &#8369;
                                                        </div>
                                                    </div>
                                                    <input type="number" class="form-control" required step=".01" name="base_price" value="{{ $variant->base_price }}" placeholder="Base Price">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary btn-icon" id="{{ $enc_variant_id }}-price-btn" type="submit" form="update-variant-{{ $enc_variant_id }}-price-form" ><i class="fal fa-edit"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                         </table>
                    @else
                        <table id="variant-tbl" class="table table-bordered w-100">
                            <thead>
                            <tr>
                                <th width="5%"></th>
                                <th width="60%">Attributes & Values</th>
                                <th width="20%">Base Price</th>
                                <th width="15%">Is Default</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $index=>$variant)
                                    @php
                                        $enc_product_id = encryptor('encrypt',$variant->parent_id);
                                        $enc_variant_id = encryptor('encrypt',$variant->id);
                                    @endphp
                                    <tr>
                                        <td>{{ ( $index + 1 ) }}</td>
                                        <td>{{ $variant->product_name }}</td>
                                        <td>
                                            <form class="" role="form" id="update-variant-{{ $enc_variant_id }}-price-form" onsubmit="$('#{{ $enc_variant_id }}-price-btn').attr('disabled',true)" method="POST" action="{{ route('product-functions',['id' => 'update-variant-price']) }}">
                                                @csrf()
                                                <input type="hidden" value="{{ $enc_product_id }}" name="product"/>
                                                <input type="hidden" value="{{ $enc_variant_id }}" name="variant"/>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text text-dark">
                                                            &#8369;
                                                        </div>
                                                    </div>
                                                    <input type="number" class="form-control" required step=".01" name="base_price" value="{{ $variant->base_price }}" placeholder="Base Price">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary btn-icon" id="{{ $enc_variant_id }}-price-btn" type="submit" form="update-variant-{{ $enc_variant_id }}-price-form" ><i class="fal fa-edit"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            @if($variant->is_default == true)
                                                <button class="btn btn-default btn-sm btn-block" disabled>DEFAULT</button>
                                            @else
                                                <form class="" role="form" id="{{ $enc_product_id }}-set-variant-default-form" onsubmit="$('#{{ $enc_variant_id }}-btn').attr('disabled',true)" method="POST" action="{{ route('product-functions',['id' => 'variant-default']) }}">
                                                    @csrf()
                                                    <input type="hidden" value="{{ $enc_product_id }}" name="product"/>
                                                    <input type="hidden" value="{{ $enc_variant_id }}" name="variant"/>
                                                </form>
                                                <button type="submit" form="{{ $enc_product_id }}-set-variant-default-form" id="{{ $enc_variant_id }}-btn" class="btn btn-success btn-xs btn-block" >SET AS DEFAULT</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div id="err"></div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.min.js"></script>
<script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
@if($product->type == 'FIT-OUT')
    <script>
        var product_id = "{{ encryptor('encrypt',$product->id) }}";
        var selected_product = 0;
        function removeinRow(key,btn){
            $('#'+btn).attr('disabled',true);
            $('#'+btn).text('removing..');
            formData = new FormData();
            formData.append('key', key);
            $.ajax({
                type: "POST",
                url: "{{route('product-functions',['id' => 'remove-to-fit-out-list'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success == 1){
                        //$('#total-base-price').val(result.sub_total);
                        $('#row-'+key).remove();

                    }else{
                        alert_message('Remove to Fit-Out list',result.message,'danger');
                    }
                    $('#'+btn).attr('disabled',false);
                    $('#'+btn).text('Remove');
                },
                error: function(XMLHttpRequest, status, errorThrown){
                    console.log(JSON.stringify(XMLHttpRequest))
                    alert_message('Error','Error Occured.','error');
                    $('#'+btn).attr('disabled',false);
                    $('#'+btn).text('Remove');
                }
            });
        }
        function productList(){
            //supply items
            var url = "{{ route('product-functions',['id' => 'supply-product-list']) }}";
            $('#select-supply-product-section').collapse('show');
            $('#select-raw-product-section').collapse('show');
            var keys = {
                key: product_id,
                type: 'SUPPLY',
            };
            supplyProducts('supply-product-table',url,keys);
            $('#products-modal').modal('show');
            //raw items
            keys = {
                parent: product_id
            };
            url = "{{ route('product-functions',['id' => 'raw-product-list']) }}";
            rawProducts('raw-product-table',url,keys);
        }
        function supplyProducts(table,url,keys = [],mode = 'PARENT'){
            $('#'+table).dataTable().fnDestroy();
            var cols = [];
            if(mode == 'VARIANT'){
                cols = [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'product_name', name: 'product_name'},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false}
                ];
            }else{
                cols = [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'product_name', name: 'product_name'},
                    { data: 'sub_category_with_category.name', name: 'subCategoryWithCategory.name'},
                    { data: 'sub_category_with_category.category.name', name: 'subCategoryWithCategory.category.name',visible:false},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false}
                ];
            }
            $('#'+table).DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :url, // json datasource
                    data: keys,
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: cols
            });
        }
        function rawProducts(table,url,keys = []){
            $('#'+table).dataTable().fnDestroy();
            $('#'+table).DataTable({
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :url, // json datasource
                    data: keys,
                    type: "POST",  // method  , by default get
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'product_name', name: 'product_name'},
                    { data: 'sub_category_with_category.name', name: 'subCategoryWithCategory.name'},
                    { data: 'sub_category_with_category.category.name', name: 'subCategoryWithCategory.category.name',visible:false},
                    { data: 'actions', name: 'actions',orderable: false, searchable: false}
                ]
            });
        }
        function supplyProductVariants(key){
            var url = "{{ route('product-functions',['id' => 'supply-product-variant-list']) }}";
            $('.products').removeClass('bg-primary-50');
            $('#product-row-'+key).addClass('bg-primary-50');
            $('#product-name').text($('#product-name-'+key).val());
            var keys = {
                type: 'SUPPLY',
                product: key,
                parent: product_id,
            };
            supplyProducts('supply-product-variant-table',url,keys,'VARIANT');
            $('#select-supply-variant-section').collapse('show')
            //
        }
        function addSupplyToFitOutProduct(key){
            selected_product = key; // variant
            $('#supply-key').val(selected_product);
            $('#supply-type').val($('#product-type-'+key).val());
            $('.variants').removeClass('bg-primary-50');
            $('#variant-row-'+key).addClass('bg-primary-50');
            $('#product-name').text($('#product-name-'+key).val());
            $('#variant-parent-name').val($('#product-name-'+key).val());
            $('#variant-name').val($('#variant-name-'+key).val());
            $('#base-price').val($('#base-price-'+key).val());
            $('#supply-product-section').collapse('show');
        }
        function addRawToFitOutProduct(key){
            selected_product = key; // variant
            $('#raw-key').val(selected_product);
            $('#raw-type').val($('#product-type-'+key).val());
            $('.raw-products').removeClass('bg-primary-50');
            $('#raw-product-row-'+key).addClass('bg-primary-50');
            $('#raw-product-name').val($('#raw-product-name-'+key).val());
            $('#raw-base-price').val($('#raw-product-base-price-'+key).val());
            $('#raw-product-section').collapse('show');
        }
    </script>
@endif
@endsection
