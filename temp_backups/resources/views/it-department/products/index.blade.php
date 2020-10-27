@extends ('layouts.it-department.app')
@section ('title')
    Products
@endsection
@section('styles')
    <link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item">Products</li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div id="panel-1" class="panel">
            <div class="row p-3">
                <div class="col-md-8">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item  "><a class="nav-link active fs-lg text-primary" data-toggle="tab" href="#for-approval-tab" role="tab">FOR APPROVAL</a></li>
                        <li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#approved-tab" role="tab">APPROVED</a></li>
                        <li class="nav-item "><a class="nav-link fs-lg text-danger" data-toggle="tab" href="#declined-tab" role="tab">DECLINED</a></li>
                    </ul>
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ route('product-create')  }}" class="btn btn-primary">Create Product</a>
                </div>
                <div class="col-md-12 p-3">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="for-approval-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 p-2">
                                    <table id="for-approval-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                        <thead class="bg-warning-500">
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="25%">Name</th>
                                                <th>Type</th>
                                                <th width="20%">Sub Category</th>
                                                <th>Category</th>
                                                <th width="15%">Swatches</th>
                                                <th width="20%">Added By</th>
                                                <th>Updated By</th>
                                                <th>Date Added</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="approved-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 p-2">
                                    <table id="approved-tbl" class="table table-bordered w-100">
                                        <thead class="bg-warning-500">
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="25%">Name</th>
                                            <th>Type</th>
                                            <th width="20%">Sub Category</th>
                                            <th>Category</th>
                                            <th width="15%">Swatches</th>
                                            <th width="20%">Added By</th>
                                            <th>Updated By</th>
                                            <th>Date Added</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="declined-tab" role="tabpanel">
                            <div class="col-md-12 p-2">
                                <table id="declined-tbl" class="table table-bordered w-100">
                                    <thead class="bg-warning-500">
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="30%">Name</th>
                                            <th>Type</th>
                                            <th width="25%">Sub Category</th>
                                            <th>Category</th>
                                            <th width="20%">Swatches</th>
                                            <th width="20%">Added By</th>
                                            <th>Updated By</th>
                                            <th>Date Added</th>
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
        </div>
    </div>
</div>
<div class="modal fade" id="product-details-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Product Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="product-details">

                </div>
            </div>
            <div class="modal-footer">
                <button onClick="$('#description').val($('#summernote').summernote('code'));" type="submit" form="update-product-form" id="btn-update-product" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="product-status-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Product Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-product-status-form" role="form" onsubmit="$('#update-product-status-btn').prop('disabled',true); return true;" method="POST"  action="{{route('product-functions',['id' => 'update-product-status'])}}">
                @csrf()
                    <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-1">
                            <label class="form-control-plaintext">Product:</label>
                            <input type="hidden" id="product-key" name="product_key" value="">
                            <input readonly class="form-control" id="product-name-status" />
                        </div>
                        <div class="form-group mb-1">
                            <label class="form-control-plaintext">Update Status to:</label>
                            <input readonly class="form-control" id="product-status" name="product_status"/>
                        </div>
                        <div class="form-group mb-1">
                            <label class="form-control-plaintext">Remarks</label>
                            <textarea  class="form-control" required  rows="2" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="update-product-status-form" id="update-product-status-btn" class="btn btn-primary">Save changes</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="swatch-details-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Swatch Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row" id="swatch-details">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="err"></div>
@endsection
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script>
    var categories = [];
    var sub_category = [];
    var swatch_group = [];
    var selected_swatches = '';
    var selected_category  = "";
    var selected_sub_category  = "";
    var type  = "";

    $(function (){
        $('#for-approval-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "ajax":{
                url :"{{route('product-functions',['id' => 'forapproval-product-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                error: function(data){  // error handling
                    $('#err').html(JSON.stringify(data));
                }
            },
            columns: [
                { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                { data: 'product_name', name: 'product_name'},
                { data: 'type', name: 'type',visible: false},
                { data: 'sub_category_with_category.name', name: 'subCategoryWithCategory.name'},
                { data: 'sub_category_with_category.category.name', name: 'subCategoryWithCategory.category.name',visible:false},
                { data: 'swatches', name: 'swatches'},
                { data: 'created_by.username', name: 'createdBy.username'},
                { data: 'updated_by.username', name: 'updatedBy.username',visible: false},
                { data: 'created_at', name: 'created_at',visible:false},
                { data: 'actions', name: 'actions',orderable: false, searchable: false}]
        });
        $('#approved-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "ajax":{
                url :"{{route('product-functions',['id' => 'approved-product-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                error: function(data){  // error handling
                    $('#err').html(JSON.stringify(data));
                }
            },
            columns: [
                { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                { data: 'product_name', name: 'product_name'},
                { data: 'type', name: 'type',visible: false},
                { data: 'sub_category_with_category.name', name: 'subCategoryWithCategory.name'},
                { data: 'sub_category_with_category.category.name', name: 'subCategoryWithCategory.category.name',visible:false},
                { data: 'swatches', name: 'swatches'},
                { data: 'created_by.username', name: 'createdBy.username'},
                { data: 'updated_by.username', name: 'updatedBy.username',visible: false},
                { data: 'created_at', name: 'created_at',visible:false},
                { data: 'actions', name: 'actions',orderable: false, searchable: false}]
        });
        $('#declined-tbl').DataTable({
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "ajax":{
                url :"{{route('product-functions',['id' => 'declined-product-list'])}}", // json datasource
                type: "POST",  // method  , by default get
                error: function(data){  // error handling
                    $('#err').html(JSON.stringify(data));
                }
            },
            columns: [
                { data: 'DT_RowIndex',name:'DT_RowIndex', orderable: false, searchable: false},
                { data: 'product_name', name: 'product_name'},
                { data: 'type', name: 'type',visible: false},
                { data: 'sub_category_with_category.name', name: 'subCategoryWithCategory.name'},
                { data: 'sub_category_with_category.category.name', name: 'subCategoryWithCategory.category.name',visible:false},
                { data: 'swatches', name: 'swatches'},
                { data: 'created_by.username', name: 'createdBy.username'},
                { data: 'updated_by.username', name: 'updatedBy.username',visible: false},
                { data: 'created_at', name: 'created_at',visible:false}]
        });
    })
    function viewSwatch(key){
        var url = "{{ route('product-swatch-details') }}";
        var swatch = $('#'+key).val();
        $('#swatch-details').html('');
        $('#swatch-details').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
        $("#swatch-details").load(url+"?swatch="+swatch, function(responseTxt, statusTxt, jqXHR){

        });
        $('#swatch-details-modal').modal('show');

    }
    function updateProduct(key){
        var url = '{{ route('product-update-details') }}';
        $('#product-details').html('');
        $('#product-details').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
        '');
        $("#product-details").load(url+"?pid="+key, function(responseTxt, statusTxt, jqXHR){
            categories = JSON.parse($('#categories').val());
            selected_category = $('#category').val();
            selected_sub_category = $('#selected-sub-category').val();
            selected_swatches = $('#selected-swatches').val();
            type = $('#type').val();
            //convert to array
            selected_swatches = selected_swatches.split(',');
            if(selected_sub_category != ''){
                genSubCategory(selected_category);
            }
            $('#summernote').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    //['table', ['table']],
                    //['view', ['fullscreen', 'codeview', 'help']]
                ],
                height:150
            });
        });
        $('#product-details-modal').modal('show');
    }
    function updateProductStatus(key,status){
        $('#product-name-status').val($('#'+key+"-product-name").val());
        $('#product-status').val(status);
        $('#product-key').val(key);
        console.log(key);
        $('#product-status-modal').modal('show');
    }
    function genSubCategory(cid){
        $('#sub-category').empty();
        $('#sub-category').append('<option value="" selected >Choose Sub Category</option>');
        var isSelectedSub = false;
        for(i=0; i < categories.length;i++){
            if(categories[i].id == cid) {
                selected_category = cid;
                attribute = categories[i].attributes;
                sub_category = categories[i].sub_category_with_swatches;
                for (ia = 0; ia < sub_category.length; ia++) {
                    if (selected_sub_category == sub_category[ia].id) {
                        $('#sub-category').append('<option selected  value="' + sub_category[ia].id + '" >' + sub_category[ia].name + '</option>');
                        isSelectedSub = true;
                    } else {
                        $('#sub-category').append('<option  value="' + sub_category[ia].id + '" >' + sub_category[ia].name + '</option>');
                    }
                }
            }
        }
        if(isSelectedSub == true && ( type != 'RAW' || type != 'SPECIAL ITEM') ){
            genSwatches(cid,selected_sub_category);
        }
    }
    function genSwatches(cid,scid){
        cid = selected_category;
        $("#swatches").empty();
        var isSelected = '';
        var subcat = [];
        for(i=0; i < categories.length;i++){
            if(categories[i].id == cid){
                subcat = categories[i].sub_category_with_swatches;
                for(a=0; a < subcat.length;a++){
                    if(scid ==  subcat[a].id){
                        swatch_group = subcat[a].swatches_group;
                        if(swatch_group.length > 0){
                            for(b=0; b < swatch_group.length; b++){
                                isSelected = '';
                                if(selected_swatches.includes(swatch_group[b].name) == true){
                                    isSelected = 'checked';
                                }
                                $('#swatches').append(''+'' +
                                    '<div class="custom-control custom-checkbox custom-control-inline mb-2">' +
                                    '<input type="checkbox" '+isSelected+' name="swatches[]" value="'+swatch_group[b].name+'" class="custom-control-input" id="swatch-'+swatch_group[b].name+'">' +
                                    '<label class="custom-control-label" for="swatch-'+swatch_group[b].name+'">'+swatch_group[b].name+'</label>' +
                                    '</div>'
                                );
                            }
                        }
                    }
                }
            }
        }
    }
</script>
@if(Session::has('update-product'))
    <script>
        $(function(){
            updateProduct("{{ Session::get('update-product') }}")
        })
    </script>
@endif
@endsection
