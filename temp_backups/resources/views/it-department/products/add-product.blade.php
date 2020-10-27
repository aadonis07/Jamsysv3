@extends ('layouts.it-department.app')
@section ('title')
    Add Product [ WITH VARIANTS ]
@endsection
@section('styles')
    <link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection
@section('content')
@php
    $product_name = '';
    $selected_category = old('category');
    $selected_sub_category = '';
    $keywords = '';
    $note = '';
    $description = '';
    $combos = '';
@endphp
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
                    <span class="h5 mt-0">ADD PRODUCT <text class="text-primary small">[ WITH VARIANTS ]</text></span>
                    <br>
                    <p class="mb-0">Creation of products with variants. Please fill all fields and specify the right attributes and values</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 text-right">
        <p class="text-danger m-0 mt-1">( Click here to add as raw / special product )</p>
        <a href="{{ route('product-create-raw') }}" class="btn btn-primary btn-sm ">Add  <b>[ RAW / SPECIAL ]</b> Product </a>
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
                                    <input id="product_name" type="text" class="form-control" placeholder="Product Code, Product Name with Classification..." aria-label="Username" aria-describedby="addon-wrapping-left">
                                </div>
                            </div>
                        </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="example-select">Category:</label>
                            <select class="form-control" onChange="genSubCategory(this.value)" name="category" id="category">
                                <option value="">Choose Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="example-select">Sub-Category:</label>
                            <select class="form-control" onChange="genSwatches(null,this.value)" id="sub-category">
                                <option>Choose Sub Category</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 panel-content mt-4 ">
                        <div class="alert alert-secondary fade show">
                        <h5 class="frame-heading">Multiple Select of Swatches:</h5>
                        <div class="frame-wrap" id="swatches" style="background-color: #FBFBFB;">

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
        <img class="img-fluid text-center" id="product-preview" style="witdh: 100%;" src="{{ $defaultLink }}" alt="">
        <div class="form-group ">
            <div class="custom-file">
                <input type="file" name="img" class="custom-file-input" onChange="readURL(this.id,'product-preview','{{ $defaultLink }}')" id="product-img">
                <label class="custom-file-label mt-2 bg-success text-white text-left" for="customFile">Choose file</label>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div id="panel-2" class="panel">
            <div class="panel-hdr">
                <h2>
                    Attributes and Values
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="form-group">
                        <p><b>NOTE:</b> Automatically Exclude Special Character.</p>
                        <div class="input-group">
                            <select class="form-control" id="attributes">
                                <option value="">Choose Attributes</option>
                            </select>
                            <input type="text" maxlenght="20" id="attribute_value" class="form-control" placeholder="Values" aria-label="Text input with segmented dropdown button">
                            <div class="input-group-append">
                                <button type="button" onClick="createAttributes('attributes','attribute_value')" class="btn btn-light waves-effect waves-themed">ADD</button>
                            </div>
                        </div>
                    </div>
                    <div class="frame-wrap  mb-0">
                        <table class="table table-bordered table-hover mb-0"  id="attributes-tbl">
                            <thead class="thead-themed bg-info">
                                <tr>
                                    <th class="small" class="small" width="45%">ATTRIBUTE</th>
                                    <th class="small" class="small" width="45%">VALUE</th>
                                    <th class="small" class="small" width="10%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <hr>
                        <button onclick="genCombination()" id="btn-generate-variants" type="button" class="btn btn-info waves-effect btn-block waves-themed mb-3">Generate Combinations</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div id="panel-2" class="panel">
            <div class="panel-hdr">
                <h2>
                    Variants
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <p>Possible Product Attribute combinations </p>
                    <div class="frame-wrap">
                        <div class="frame-wrap mb-0">
                            <table class="table table-bordered table-hover mb-0" id="variants-tbl">
                                <thead class="thead-themed">
                                    <tr>
                                        <th class="small text-center" width="90%">VARIANTS</th>
                                        <th class="small text-center" width="10%">DEFAULT</th>
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
<div class="row">
    <div class="col-lg-12 mt-2 panel-content">
        <div class="card-header alert alert-secondary  ">
            <h6>Description:</h6>
            <div class="card-body p-0">
                <textarea style="display:none" id="description" name="description"></textarea>
                <div id="summernote">

                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row mt-4 mb-4">
        <div class="col-lg-12  text-center">
            <button type="button" class="btn btn-success waves-effect waves-themed">RESET DETAILS</button>
            <button type="button" id="btn-add" onClick="createProduct()" class="btn btn-warning waves-effect waves-themed">ADD PRODUCT <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>
<input id="categories" type="hidden" value="{{json_encode($categories)}}"/>
@endsection
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
    var categories =[];
    var sub_category = [];
    var attribute = [];
    var swatch_group = [];
    var selected_category  = "";
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
    });
    function genSubCategory(cid){
        $('#sub-category').empty();
        $('#sub-category').append('<option value="" selected >Choose Sub Category</option>');
        $('#attributes').empty();
        $('#variants-tbl tbody').empty();
        $('#attributes').append('<option value="" selected >Choose Attribute</option>');
        var isSelectedSub = false;
        var sub_cat  = 0;
        for(i=0; i < categories.length;i++){
            if(categories[i].id == cid) {
                selected_category = cid;
                attribute = categories[i].attributes;
                sub_category = categories[i].sub_category_with_swatches;
                for (ia = 0; ia < sub_category.length; ia++) {
                    if ("{{ strtolower(old('sub_category')) }}" == sub_category[ia].name.toLowerCase()) {
                        $('#sub-category').append('<option selected  value="' + sub_category[ia].id + '" >' + sub_category[ia].name + '</option>');
                        isSelectedSub = true;
                        sub_cat = sub_category[ia].id;
                    } else {
                        $('#sub-category').append('<option  value="' + sub_category[ia].id + '" >' + sub_category[ia].name + '</option>');
                    }
                }
                for (ia = 0; ia < attribute.length; ia++) {
                    $('#attributes').append('<option  value="' + attribute[ia].name.toUpperCase() + '" >' + attribute[ia].name + '</option>');
                }
            }
        }
        if(isSelectedSub == true){
            genSwatches(cid,sub_cat);
        }
    }
    function checkifhasinTable(attribute,value){
        var attribute_key = [];
        var attribute_value = [];
        var bool=false;
        $("input[name='attribute_key[]']").each(function() {
            attribute_key.push($(this).val());
        });
        $("input[name='attribute_value[]']").each(function() {
            attribute_value.push($(this).val());
        });
        if(inArray(attribute_key,attribute) === true && inArray(attribute_value,value) === true){
            bool=true;
        }
        return bool;
    }
    function inArray(myArray,myValue){
        var inArray = false;
        myArray.map(function(key){
            if (key.toLowerCase() === myValue.toLowerCase()){
                inArray=true;
            }
        });
        return inArray;
    }
    function createAttributes(attribute,value){
        attribute = $("#"+attribute+" option:selected");
        value = $('#'+value).val();
        var charReg = /^\s*[a-zA-Z0-9.\s&*%/]+\s*$/;
        if(value == '' || attribute.val() == 0){
            alert_message('Failed','Attribute and value is required.','danger');
        }
        else if (!charReg.test(value)) {
            alert_message('Failed','Special Character is not allowed','danger');
        }
        else if(checkifhasinTable(attribute.val(),value)==true){
            alert_message('Failed','Already exist','danger');
        }
        else{
            var d = new Date();
            var datetime = d.getTime();
            var action_btn = '<button class="btn btn-xs btn-danger" type="button" onClick= delRow("'+datetime+'")><i class="fas fa-times"></i></button>';
            attribute = attribute.text()+'' +
                '<input name="attribute_key[]" type="hidden" value="'+attribute.val()+'"/> ' +
                '<input name="attribute_description[]" type="hidden" value="'+attribute.text()+'"/>';
            value = value+'<input name="attribute_value[]" type="hidden" value="'+value+'"/>';
            $('#attributes-tbl tbody').append('<tr id="'+datetime+'"><td >'+attribute+'</td><td>'+value+'</td><td>'+action_btn+'</td></tr>');
            $('#attribute_value').focus();
        }
    }
    function genCombination(){
        $('#btn-generate-variants').attr('disabled',true);
        $('#btn-generate-variants').text('Generating..');
        var attributes_tbody = $("#attributes-tbl tbody");
        $('#variants-tbl tbody').empty();
        if (attributes_tbody.children().length == 0) {
            alert_message('Generate Combination','Attributes and Values is required.','danger');
            $('#btn-generate-variants').attr('disabled',false);
            $('#btn-generate-variants').text('Generate Combinations');
        }else{
            var attribute_keys =  $("input[name='attribute_key[]']").map(function(){return $(this).val();}).get();
            var attribute_values =   $("input[name='attribute_value[]']").map(function(){return $(this).val();}).get();
            var attribute_description =   $("input[name='attribute_description[]']").map(function(){return $(this).val();}).get();
            formData = new FormData();
            formData.append('keys', attribute_keys);
            formData.append('values', attribute_values);
            formData.append('description', attribute_description);
            $.ajax({
                type: "POST",
                url: "{{route('product-functions',['id' => 'generate-combination'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success == 1){
                        var combinations  = result.data;
                        for(i=0;i < combinations.length; i++){
                            $('#variants-tbl tbody').append(combinations[i]);
                        }
                    }else{
                        alert_message('Generate Combination',result.message,'danger');
                    }
                    $('#btn-generate-variants').attr('disabled',false);
                    $('#btn-generate-variants').text('Generate Combinations');
                },
                error: function(XMLHttpRequest, status, errorThrown){
                    console.log(JSON.stringify(XMLHttpRequest))
                    alert_message('Error','Error Occured.','error');
                    $('#btn-generate-variants').attr('disabled',false);
                    $('#btn-generate-variants').text('Generate Combinations');
                }
            });
        }
    }
    function delRow(key){
        $('#combinations tbody').empty();
        $('#'+key).remove();
    }
    function genSwatches(cid,scid){
        cid = selected_category;
        $("#swatches").empty();
        var subcat = [];
        for(i=0; i < categories.length;i++){
            if(categories[i].id == cid){
                subcat = categories[i].sub_category_with_swatches;
                for(a=0; a < subcat.length;a++){
                    if(scid ==  subcat[a].id){
                        swatch_group = subcat[a].swatches_group;
                        if(swatch_group.length > 0){
                            for(b=0; b < swatch_group.length; b++){
                                $('#swatches').append(''+'' +
                                    '<div class="custom-control custom-checkbox custom-control-inline mb-2">' +
                                        '<input type="checkbox" name="swatches[]" value="'+swatch_group[b].name+'" class="custom-control-input" id="swatch-'+swatch_group[b].name+'">' +
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
    function createProduct(){
        formData = new FormData();
        $('#btn-add').prop('disabled', true);
        $('#btn-add').html('Please wait....');
        toastMessage('Creating Product','Validating data','info','toast-bottom-right');
        $('#description').val($('#summernote').summernote('code'));
        formData.append('description',$('#description').val());
        formData.append('product_name',$('#product_name').val());
        formData.append('category',$('#category').val());
        formData.append('sub_category',$('#sub-category').val());
        $('input[name^="swatches"]').each(function() {
            if($(this).is(":checked") === true){
                formData.append('swatches[]',$(this).val());
            }
        });
        if(document.getElementById("product-img").files.length > 0) {
            formData.append('img', $('#product-img')[0].files[0]);
        }
        $('input[name^="is_default"]').each(function() {
            formData.append('is_default[]',$(this).is(":checked"));
        });
        $('input[name^="variants"]').each(function() {
            formData.append('variants[]',$(this).val());
        });
        $.ajax({
            type: "POST",
            url: "{{route('product-functions',['id' => 'create-product-with-variants'])}}",
            data: formData,
            contentType: false,
            processData: false,
            success: function (result) {
                if(result.success != '1'){
                    $('#btn-add').prop('disabled', false);
                    $('#btn-add').html('ADD PRODUCT');
                    alert_message('Failed',result.message,'danger');
                }
                else{
                    alert_message('Success',result.message,'success');
                    toastMessage('Page reload','Reloading page','info','toast-bottom-right');
                    window.location.reload();
                }
                toastr.clear();
                $('#btn-add').prop('disabled', false);
                $('#btn-add').html('ADD PRODUCT');
            },
            error: function(result){
                console.log(result.responseText);
                $('#btn-add').prop('disabled', false);
                $('#btn-add').html('ADD PRODUCT');
                alert_message('Failed',result.responseText,'danger');
                toastr.clear();
            }
        });
    }
</script>
@endsection
