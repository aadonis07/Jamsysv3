@extends ('layouts.sales-department.app')
@section ('title')
    Add Product [ FIT-OUT ] ITEMS
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('content')
    @php
        $product_name = old('product_name');
        $selected_category = old('category');
        $selected_sub_category = old('sub_category');
        $description = old('description');
        $cart = Cart::getContent();
        $totalBasePrice = Cart::getSubTotal();
    @endphp
    <form class="" role="form" id="create-product-fitout" onsubmit="$('#btn-add-fitout').attr('disabled',true)" method="POST" action="{{ route('sales-product-functions',['id' => 'add-product-fitout']) }}" enctype="multipart/form-data">
        @csrf()
        <input id="categories" type="hidden" value="{{json_encode($categories)}}"/>
        <div class="row mb-3">
            <div class="col-md-12">
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
                            <span class="h5 mt-0">ADD PRODUCT <text class="text-primary small">[ FIT-OUT ] ITEMS</text></span>
                            <br>
                            <p class="mb-0">Fit-Out Products is composed of RAW & SUPPLY products.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div id="panel-4" class="panel">
                    <div class="panel-hdr">
                        <h2 class="text-danger">
                            * All Field are Required
                        </h2>
                        <div class="panel-toolbar">
                            <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                            <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                            <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                        </div>
                    </div>
                    <div class="row p-5">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 mb-4">
                                    <div class="form-group">
                                        <label class="form-label" for="example-palaceholder">Product Name :</label>
                                        <div class="input-group flex-nowrap">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-layers fs-xl"></i></span>
                                            </div>
                                            <input required id="product_name" value="{{ $product_name }}" name="product_name" type="text" class="form-control" placeholder="Product Code, Product Name ">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="example-select">Category:</label>
                                        <select class="form-control" onChange="genSubCategory(this.value)" name="category" id="category">
                                            <option value="">Choose Category</option>
                                            @foreach($categories as $category)
                                                <option {{ isSelected($selected_category,$category->id) }} value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="example-select">Sub Category:</label>
                                        <select class="form-control" onChange="genSwatches(null,this.value)"  name="sub_category" id="sub-category" >
                                            <option>Choose Sub Category</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="example-select">
                                            Base Price:
                                            <text class="text-danger ">* Note: Total base price in the list. Editable via quotation.</text>
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text text-dark">
                                                    &#8369;
                                                </div>
                                            </div>
                                            <input id="total-base-price"  readonly name="base_price" value="" type="number" step=".01" class="form-control form-control-sm"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <h5 class="mb-0">Products</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" onClick="productList()" class="btn-block btn btn-primary btn-xs">Add Product</button>
                                        </div>
                                        <div class="col-md-12">
                                            <hr class="mt-2">
                                            <table id="fitout-products-tbl" class="table table-bordered w-100">
                                                <thead class="bg-warning-500">
                                                    <tr>
                                                        <th width="55%">Name</th>
                                                        <th width="30%">Base Price</th>
                                                        <th width="15%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($cart as $item)
                                                        <tr id="row-{{ $item->id }}">
                                                            <td>
                                                                <input type="hidden" name="types[]" value="{{ $item->attributes['type'] }}"/>
                                                                <input type="hidden" name="keys[]" value="{{ $item->id }}"/>
                                                                <input type="hidden" name="names[]" value="{{ $item->name }}"/>
                                                                {{ $item->name }}
                                                                <hr class="m-0">
                                                                <text class="text-primary small"><b>TYPE: {{ $item->attributes['type'] }}</b></text>
                                                            </td>
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text text-dark">
                                                                            &#8369;
                                                                        </div>
                                                                    </div>
                                                                    <input  name="prices[]" required value="{{ $item->price }}" type="number" step=".01" class="form-control form-control-sm"/>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button id="removebtn-{{ $item->id }}" onClick='removeinRow("{{ $item->id }}",this.id)' class="btn btn-xs btn-danger">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="example-select">Base Price:</label>
                                        <input class="form-control" type="number" required name="base_price" value="" step=".01" min="1" value=""/>
                                    </div>
                                </div>
                                -->
                                <!--
                                <div class="col-lg-12 panel-content mt-4 mb-0 ">
                                    <div class="alert alert-secondary fade show">
                                        <h5 class="frame-heading mb-1">Description:</h5>
                                        <p class="text-danger mt-1 mb-1">*Note: Press Shift + Enter for new line ( without line space ) </p>
                                        <textarea  style="display:none" id="description" required name="description"></textarea>
                                        <div id="summernote">
                                            {!! $description !!}
                                        </div>
                                    </div>
                                </div>
                                -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4 text-center" style="background-color: #fff;">
                @php
                    $defaultLink = 'http://placehold.it/754x977';
                @endphp
                <div class="row">
                    <div class="col-md-12">
                        <img class="img-fluid text-center" id="product-preview" style="witdh: 100%;" src="{{ $defaultLink }}" alt="">
                        <div class="form-group ">
                            <div class="custom-file">
                                <input required type="file" name="img" class="custom-file-input" onChange="readURL(this.id,'product-preview','{{ $defaultLink }}')" id="product-img">
                                <label class="custom-file-label mt-2 bg-success text-white text-left" for="customFile">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-4 text-center">
                        <hr>
                        <button type="button" onClick="window.location.reload()" class="btn btn-success waves-effect waves-themed">RESET DETAILS</button>
                        <button  type="submit" id="btn-add-fitout" form="create-product-fitout"  class="btn btn-warning waves-effect waves-themed">ADD PRODUCT <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
                                                            <div class="row">
                                                                <div class="col-md-6">
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
                                                                            <input readonly  id="base-price" name="price" type="number" step=".01" class="form-control form-control-sm"/>
                                                                        </div>
                                                                        <div class="form-group mb-1 text-right">
                                                                            <button type="button" onClick="addTofitoutItem('add-fitout-btn')" class="add-fitout-btn btn btn-warning mt-2">Add to list</button>
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
                                                            <div class="row">
                                                                <div class="col-md-9">
                                                                    <div class="form-group mb-1">
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
                                                                            <input readonly  id="raw-base-price" name="price" type="number" step=".01" class="form-control form-control-sm"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group mb-1 text-right">
                                                                        <button type="button" onClick="addTofitoutItem('add-fitout-btn')" class="add-fitout-btn btn btn-warning mt-2">Add to list</button>
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
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        var categories =[];
        var sub_category = [];
        var selected_category  = "{{ $selected_category }}";
        var selected_sub_category  = "{{ $selected_sub_category }}";
        var selected_product = 0;
        $(function (){
            categories = JSON.parse($('#categories').val());
            if(selected_sub_category != ''){
                genSubCategory(selected_category);
            }
        });
        function removeinRow(key,btn){
            $('#'+btn).attr('disabled',true);
            $('#'+btn).text('removing..');
            formData = new FormData();
            formData.append('key', key);
            $.ajax({
                type: "POST",
                url: "{{route('sales-product-functions',['id' => 'remove-to-fit-out-list'])}}",
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
        function addTofitoutItem(btn){
            // selected_product depends on type.
            $('.'+btn).attr('disabled',true);
            $('.'+btn).text('Adding..');
            var type = $('#product-type-'+selected_product).val();
            formData = new FormData();
            formData.append('key', selected_product);
            formData.append('type', type);
            $.ajax({
                type: "POST",
                url: "{{route('sales-product-functions',['id' => 'add-to-fit-out-list'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success == 1){
                        $('#fitout-products-tbl').append(result.data);
                        //$('#total-base-price').val(result.sub_total);
                        if(type == 'SUPPLY'){

                        }
                        else if(type == 'RAW' || type == 'SPECIAL-ITEM'){

                        }
                        $('#products-modal').modal('hide');
                    }else{
                        alert_message('Add to Fit-Out list',result.message,'danger');
                    }
                    $('.'+btn).attr('disabled',false);
                    $('.'+btn).text('Add to list');
                },
                error: function(XMLHttpRequest, status, errorThrown){
                    console.log(JSON.stringify(XMLHttpRequest))
                    alert_message('Error','Error Occured.','error');
                    $('.'+btn).attr('disabled',false);
                    $('.'+btn).text('Add to list');
                }
            });
        }
        function productList(){
            //supply items
            var url = "{{ route('sales-product-functions',['id' => 'supply-product-list']) }}";
            $('#select-supply-product-section').collapse('show');
            var keys = {
                type: 'SUPPLY',
            };
            supplyProducts('supply-product-table',url,keys);
            $('#products-modal').modal('show');
            //raw items
            url = "{{ route('sales-product-functions',['id' => 'raw-product-list']) }}";
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
            var url = "{{ route('sales-product-functions',['id' => 'supply-product-variant-list']) }}";
            $('.products').removeClass('bg-primary-50');
            $('#product-row-'+key).addClass('bg-primary-50');
            $('#product-name').text($('#product-name-'+key).val());
            var keys = {
                type: 'SUPPLY',
                product: key,
            };
            supplyProducts('supply-product-variant-table',url,keys,'VARIANT');
            $('#select-supply-variant-section').collapse('show')
            //
        }
        function addSupplyToFitOutProduct(key){
            selected_product = key; // variant
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
            $('.raw-products').removeClass('bg-primary-50');
            $('#raw-product-row-'+key).addClass('bg-primary-50');
            $('#raw-product-name').val($('#raw-product-name-'+key).val());
            $('#raw-base-price').val($('#raw-product-base-price-'+key).val());
            $('#raw-product-section').collapse('show');
        }
        function genSubCategory(cid){
            $('#sub-category').empty();
            $('#sub-category').append('<option value="" selected >Choose Sub Category</option>');
            var isSelectedSub = false;
            var sub_cat  = 0;
            for(i=0; i < categories.length;i++){
                if(categories[i].id == cid) {
                    selected_category = cid;
                    sub_category = categories[i].sub_categories;
                    for (ia = 0; ia < sub_category.length; ia++) {
                        if (selected_sub_category == sub_category[ia].id) {
                            $('#sub-category').append('<option selected  value="' + sub_category[ia].id + '" >' + sub_category[ia].name + '</option>');
                            isSelectedSub = true;
                            sub_cat = sub_category[ia].id;
                        } else {
                            $('#sub-category').append('<option  value="' + sub_category[ia].id + '" >' + sub_category[ia].name + '</option>');
                        }
                    }
                }
            }
        }
    </script>
@endsection
