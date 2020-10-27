@extends ('layouts.purchasing-raw-department.app')
@section ('title')
  Supplier | {{ strtoupper($supplier->name) }}
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item ">
    <a class="text-info" href="{{ route('purchasing-raw-suppliers') }}">Suppliers</a>
</li>
<li class="breadcrumb-item ">{{ $supplier->name }}</li>
<li class="breadcrumb-item ">Supplier Products</li>
@endsection

@section('content')
@php
    $enc_supplier_id = encryptor('encrypt',$supplier->id);
@endphp
<div class="row mb-3 ">
    <div class="col-lg-10 d-flex flex-start w-100">
        <div class="mr-2 hidden-md-down">
            <span class="icon-stack icon-stack-lg">
                <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
                <i class="ni ni-settings icon-stack-1x opacity-100 color-white" style="font-size: 14px; margin-bottom: 2px;"></i>
            </span>
        </div>
        <div class="d-flex flex-fill">
            <div class="flex-fill">
                <span class="h5 mt-0">{{ strtoupper($supplier->name) }} PRODUCTS </span>
                <br>
                <p class="mb-0">Duplicate entry is not allowed in this section.</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group" align="right">
            <button class="btn btn-primary" onClick="showProducts()"><span class="fa fa-plus"></span> Add New Product</button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="dt-raw-products" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th width="5%">No</th>
                                    <th width="20%">Code [ SUPPLIER ]</th>
                                    <th width="30%">Name</th>
                                    <th width="15%">Price</th>
                                    <th width="20%">Added By</th>
                                    <th width="10%" >Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot class="thead-themed">
                                <tr class="text-center">
                                    <th width="5%">No</th>
                                    <th width="20%">Code [ SUPPLIER ]</th>
                                    <th width="30%">Name</th>
                                    <th width="15%">Price</th>
                                    <th width="20%">Added By</th>
                                    <th width="10%" >Action</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="add-supplier-products-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Add Product to Supplier
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
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
                                    <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#add-to-supplier-raw-section" aria-expanded="false">
                                                                <span class="mr-2">
                                                                    <span class="collapsed-reveal">
                                                                        <i class="fal fa-minus fs-xl"></i>
                                                                    </span>
                                                                    <span class="collapsed-hidden">
                                                                        <i class="fal fa-plus fs-xl"></i>
                                                                    </span>
                                                                </span>
                                        <h4 class="m-0">Add to Supplier</h4>
                                    </a>
                                </div>
                                <div id="add-to-supplier-raw-section" class="collapse" data-parent="#add-product-raw-section">
                                    <div class="card-body">
                                        <form method="post" id="add-product-raw-form" action="{{ route('purchasing-raw-supplier-functions',['id' => 'add-product']) }}" enctype="multipart/form-data">
                                            @csrf()
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group mb-1">
                                                        <label class="form-control-plaintext">Supplier Code</label>
                                                        <input required name="supplier_code" placeholder="Enter Supplier Code" class="form-control form-control-sm"/>
                                                        <input required id="raw-supplier-key" name="supplier" type="hidden"/>
                                                        <input required id="raw-product-key" name="product" type="hidden"/>
                                                        <input required id="raw-product-type" name="type" type="hidden"/>
                                                    </div>
                                                    <div class="form-group mb-1">
                                                        <label class="form-control-plaintext">Product Name</label>
                                                        <input id="raw-product-name" readonly class="form-control form-control-sm"/>
                                                    </div>
                                                    <div class="form-group mb-1">
                                                        <label class="form-control-plaintext">Price</label>
                                                        <input placeholder="Enter Price" name="price" type="number" step=".01" class="form-control form-control-sm"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="form-group">
                                                        <label class="form-control-plaintext">Note ( <text class="text-primary">Optional</text> ):</label>
                                                        <textarea class="form-control" rows="5" name="note"></textarea>
                                                    </div>
                                                    <div class="form-group text-right">
                                                        <button type="submit" form="add-product-raw-form" class="btn btn-warning">Save Product</button>
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
{{--====================================================================================--}}
<div class="modal fade" id="update-supplier-products-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Update Product for {{ $supplier->name }}
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="POST" id="update-supplier-product-form" action="{{ route('purchasing-raw-supplier-functions',['id' => 'update-supplier-product']) }}" onsubmit="$('#update-product-supplier-btn').attr('disabled',true)">
                    @csrf()
                      <div class="row" id="">
                          <div class="col-md-12" id="update-supplier-product-content">

                          </div>
                      </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" disabled id="update-product-supplier-btn" form="update-supplier-product-form" class="btn btn-warning">Update</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="supplier-product-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mb-0">
                <h5 class="modal-title"><b> <i class="ni ni-calendar"></i> History Logs </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="feedback"></div>
                    <div class="col-md-12 text-center" id="logs-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="err"></div>
@endsection

@section('scripts')
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
       $(function(){
              $('#dt-raw-products').DataTable({
                   "pageLength": 100,
                   "processing": true,
                   "serverSide": true,
                   "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                   "ajax":{
                       url :"{{ route('purchasing-raw-supplier-functions',['id' => 'supplier-raw-products']) }}", // json datasource
                       type: "POST",  // method  , by default get
                       data : { supplier: "{{ $enc_supplier_id }}" },
                       error: function(result){  // error handling
                           $('#err').html(JSON.stringify(result));
                       }
                   },
                   columns: [
                       { data: 'DT_RowIndex',orderable: false, searchable: false },
                       { data: 'code', name: 'code'},
                       { data: 'product.product_name', name: 'product.product_name'},
                       { data: 'price', name: 'price'},
                       { data: 'created_by.username', name: 'createdBy.username'},
                       { data: 'actions', name: 'actions'},
                   ]
             });

        });
       function showProducts(){
           var keys = {
               supplier_id: '{{ $enc_supplier_id }}',
           };
           url = "{{ route('purchasing-raw-supplier-functions',['id' => 'raw-and-special-item-product-list']) }}";
           rawProducts('raw-product-table',url,keys);
           $('#add-supplier-products-modal').modal('show');
       }
        function updateSupplierProduct(id){
            $('#update-product-supplier-btn').attr('disabled',true);
            var path = '{{ route("purchasing-raw-supplier-product-content") }}?id='+id;
            $('#update-supplier-product-content').html('' +
                '<div class="loading mt-6 mb-6 text-center">'+
                    '<div class="spinner-grow text-secondary" style="width: 4rem; height: 4rem;" role="status">'+
                        '<span class="sr-only">Loading...</span>'+
                    '</div>'+
                    '<br>'+
                    '<br>'+
                '</div>'+
                '');
            //
            $("#update-supplier-product-content").load(path, function () {
                $('#update-product-supplier-btn').attr('disabled',false);
            });
            $('#update-supplier-products-modal').modal('show');
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
       function addToRawSupplier(key){
           $('.raw-products').removeClass('bg-primary-50');
           $('#raw-product-row-'+key).addClass('bg-primary-50');
           $('#raw-product-name').val($('#raw-product-name-'+key).val());
           $('#raw-product-type').val($('#raw-product-type-'+key).val());
           $('#raw-supplier-key').val("{{ $enc_supplier_id }}");
           $('#raw-product-key').val(key);
           $('#add-to-supplier-raw-section').collapse('show');
       }
        function logsModal(key){
            var url = "{{ route('purchasing-raw-supplier-product-logs-details') }}";
            $('#logs-content').html('');
            $('#logs-content').html('' +
                '<div class="loading mt-6 mb-6">'+
                    '<div class="spinner-grow text-secondary" style="width: 4rem; height: 4rem;" role="status">'+
                        '<span class="sr-only">Loading...</span>'+
                    '</div>'+
                    '<br>'+
                    '<br>'+
                '</div>'+
            '');
            $("#logs-content").load(url+"?spid="+key, function () {
                var data = { 'key': key }
                $("#dt-supplier-product-logs").dataTable().fnDestroy();
                $('#dt-supplier-product-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('purchasing-raw-supplier-functions',['id' => 'logs-supplier-products-details']) }}", // json datasource
                        type: "POST",  // method  , by default get
                        data : data,
                        error: function(result){  // error handling
                            $('#err').html(JSON.stringify(result));
                            $('#feedback').html(JSON.stringify(result));
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex',orderable: false, searchable: false },
                        { data: 'auditable_type', name: 'auditable_type', visible: false},
                        { data: 'user.username', name: 'user.username', visible: false},
                        { data: 'source_model', name: 'source_model',orderable: false, searchable: false},
                        { data: 'event', name: 'event'},
                        { data: 'old_values', name: 'old_values'},
                        { data: 'new_values', name: 'new_values'},
                    ]
                });
            });
            $('#supplier-product-logs-modal').modal('show');
        }
    </script>

@endsection
