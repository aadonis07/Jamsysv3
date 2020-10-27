<div class="col-md-12">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item  "><a class="nav-link active fs-lg" data-toggle="tab" onClick="$('#btn-update-product').attr('disabled',false)" href="#product-info" role="tab">Product Information</a></li>
        @if($product->type != 'RAW' && $product->type != 'SPECIAL-ITEM')
            <li class="nav-item "><a class="nav-link fs-lg" data-toggle="tab" onClick="$('#btn-update-product').attr('disabled',true)" href="#variants" role="tab">Variants</a></li>
        @endif
    </ul>
</div>
<div class="col-md-12">
    <div class="tab-content">
        <div class="tab-pane fade show active" id="product-info" role="tabpanel">
            @php
                $enc_product_id = encryptor('encrypt',$product->id);
                $destination  = 'assets/img/products/'.$enc_product_id.'/';
                $defaultLink = 'http://placehold.it/754x977';
                $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                $product_name = $product->product_name;
                $selected_category = $product->category_id;
                $description = toTxtFile($destination,'description','get');
                if($description['success'] == true){
                    $description = $description['data'];
                }else{
                    $description = '';
                }
            @endphp
            <form class="" role="form" id="update-product-form" onsubmit="$('#btn-update-product').attr('disabled',true)" method="POST" action="{{ route('purchasing-raw-product-functions',['id' => 'update-product']) }}" enctype="multipart/form-data">
                @csrf()
                <div class="row p-3">
                    <div class="col-md-8">
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
                            <div class="col-lg-6">
                                <div class="form-group mb-2">
                                    <label class="form-label text-info"><b>TYPE:</b></label>
                                    <select class="form-control" name="type">
                                        <option {{ isSelected($product->type,'RAW') }} value="RAW" selected>RAW</option>
                                        <option {{ isSelected($product->type,'SPECIAL-ITEM') }} value="SPECIAL-ITEM">SPECIAL ITEM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-2">
                                    <label class="form-label" for="example-select">Base Price:</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text text-dark">
                                                &#8369;
                                            </div>
                                        </div>
                                        <input class="form-control" type="number" required name="base_price" value="{{ $product->base_price }}" step=".01" min="1" value=""/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="example-select">Category:</label>
                                    <select class="form-control" required onChange="genSubCategory(this.value)" name="category" id="category">
                                        <option value="">Choose Category</option>
                                        @foreach($categories as $category)
                                            <option {{ isSelected($selected_category,$category->id) }} value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="example-select">Sub-Category:</label>
                                    <input type="hidden" id="selected-sub-category" value="{{ $product->sub_category_id }}"/>
                                    <input type="hidden" id="selected-swatches" value="{{ $product->swatches }}"/>
                                    <input type="hidden" id="product-key" name="key" value="{{ $enc_product_id }}"/>
                                    <select class="form-control" required onChange=""  name="sub_category" id="sub-category" >
                                        <option>Choose Sub Category</option>
                                    </select>
                                </div>
                            </div>
                            @if($product->type != 'RAW' && $product->type != 'SPECIAL-ITEM')
                                <div class="col-lg-12 panel-content mt-4 ">
                                    <div class="alert alert-secondary fade show">
                                        <h5 class="frame-heading">Multiple Select of Swatches:</h5>
                                        <div class="frame-wrap" id="swatches" style="background-color: #FBFBFB;">

                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($product->type == 'RAW' || $product->type == 'SPECIAL-ITEM')
                                <div class="col-lg-12 panel-content mt-4 mb-0  m-0">
                                    <div class="form-group">
										<h5 class="frame-heading mb-1">Description:</h5>
										<p class="text-danger mt-1 mb-1">*Note: Press Shift + Enter for new line ( without line space ) </p>
                                        <textarea  style="display:none" id="description" required name="description"></textarea>
                                        <div id="summernote">
                                            @php
                                                echo html_entity_decode($description);
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4 text-center" style="background-color: #fff;">
                        <div class="row">
                            <div class="col-md-12">
                                <img class="img-fluid text-center" id="product-preview" style="witdh: 100%;" src="{{ $defaultLink }}" alt="">
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" name="img" class="custom-file-input" onChange="readURL(this.id,'product-preview','{{ $defaultLink }}')" id="product-img">
                                        <label class="custom-file-label mt-2 bg-success text-white text-left" for="customFile">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($product->type != 'RAW' && $product->type != 'SPECIAL-ITEM')
                        <div class="col-lg-12 panel-content mt-4 mb-0 ">
                            <div class="alert alert-secondary fade show">
								<h5 class="frame-heading mb-1">Description:</h5>
								<p class="text-danger mt-1 mb-1">*Note: Press Shift + Enter for new line ( without line space ) </p>
                                <textarea  style="display:none" id="description" required name="description"></textarea>
                                <div id="summernote">
                                    @php
                                        echo html_entity_decode($description);
                                    @endphp
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
        @if($product->type != 'RAW' && $product->type != 'SPECIAL-ITEM')
            <div class="tab-pane fade show" id="variants" role="tabpanel">
                <div class="row p-3">
                    <div class="col-md-12">
                        <p class="text-danger m-0 mb-1">* Note: Can't edit/update this section</p>
                        <table id="variant-tbl" class="table table-bordered w-100">
                            <thead>
                            <tr>
                                <th width="5%"></th>
                                <th width="80%">Attributes & Values</th>
                                <th width="15%">Is Default</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $index=>$variant)
                                    <tr>
                                        <td>{{ ( $index + 1 ) }}</td>
                                        <td>{{ $variant->product_name }}</td>
                                        <td>
                                            @if($variant->is_default == true)
                                                <button class="btn btn-default btn-sm btn-block" disabled>DEFAULT</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <input id="categories" type="hidden" value="{{json_encode($categories)}}"/>
</div>
