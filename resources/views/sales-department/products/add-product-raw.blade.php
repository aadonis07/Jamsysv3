@extends ('layouts.sales-department.app')
@section ('title')
    Add Product [ RAW / SPECIAL ] ITEM
@endsection
@section('styles')
    <link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection
@section('content')
@php
    $product_name = old('product_name');
    $selected_category = old('category');
    $selected_sub_category = old('sub_category');
    $description = old('description');
@endphp
<form class="" role="form" id="create-product-raw" onsubmit="$('#btn-add-raw').attr('disabled',true)" method="POST" action="{{ route('sales-product-functions',['id' => 'add-product-raw']) }}" enctype="multipart/form-data">
    @csrf()
    <div class="row mb-3">
        <div class="col-md-8">
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
                        <span class="h5 mt-0">ADD PRODUCT <text class="text-primary small">[ RAW / SPECIAL ] ITEM</text></span>
                        <br>
                        <p class="mb-0">Creation of products without variants. Simple mode for raw and special items.</p>
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
                                    <label class="form-label" for="example-palaceholder">Product Name:</label>
                                    <div class="input-group flex-nowrap">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-layers fs-xl"></i></span>
                                        </div>
                                        <input required id="product_name" value="{{ $product_name }}" name="product_name" type="text" class="form-control" placeholder="Product Code, Product Name with Classification...">
                                    </div>
                                </div>
                            </div>
                        <div class="col-md-5">
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
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label" for="example-select">Sub Category:</label>
                                <select class="form-control" onChange="genSwatches(null,this.value)"  name="sub_category" id="sub-category" >
                                    <option>Choose Sub Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label" for="example-select">Base Price:</label>
                                <input class="form-control" type="number" required name="base_price" value="" step=".01" min="1" value=""/>
                            </div>
                        </div>
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
                    <button onClick="$('#description').val($('#summernote').summernote('code'));" type="submit" id="btn-add-raw" form="create-product-raw"  class="btn btn-warning waves-effect waves-themed">ADD PRODUCT <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>
<input id="categories" type="hidden" value="{{json_encode($categories)}}"/>
@endsection
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
    var categories =[];
    var sub_category = [];
    var attribute = [];
    var swatch_group = [];
    var selected_category  = "{{ $selected_category }}";
    var selected_sub_category  = "{{ $selected_sub_category }}";
    var quill =null;
    $(function(){
        categories = JSON.parse($('#categories').val());
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
        categories = JSON.parse($('#categories').val());
        if(selected_sub_category != ''){
            genSubCategory(selected_category);
        }
    });
    function genSubCategory(cid){
        $('#sub-category').empty();
        $('#sub-category').append('<option value="" selected >Choose Sub Category</option>');
        var isSelectedSub = false;
        var sub_cat  = 0;
        for(i=0; i < categories.length;i++){
            if(categories[i].id == cid) {
                selected_category = cid;
                attribute = categories[i].attributes;
                sub_category = categories[i].sub_category_with_swatches;
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
