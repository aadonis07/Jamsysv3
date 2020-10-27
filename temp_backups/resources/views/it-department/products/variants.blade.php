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
                        <span class="h5 mt-0">{{ strtoupper($product->product_name) }} <text class="text-primary small">[ {{ $product->type }} ] ITEM</text></span>
                        <br>
                        <p class="mb-0">List of {{ $product->product_name }} variants .</p>
                    </div>
                </div>
            </div>
        </div>
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
                            @php
                                $enc_product_id = encryptor('encrypt',$variant->parent_id);
                                $enc_variant_id = encryptor('encrypt',$variant->id);
                            @endphp
                            <tr>
                                <td>{{ ( $index + 1 ) }}</td>
                                <td>{{ $variant->product_name }}</td>
                                <td>
                                    @if($variant->is_default == true)
                                        <button class="btn btn-default btn-sm btn-block" disabled>DEFAULT</button>
                                    @else
                                        <form class="" role="form" id="set-variant-default-form" onsubmit="$('#{{ $enc_variant_id }}-btn').attr('disabled',true)" method="POST" action="{{ route('product-functions',['id' => 'variant-default']) }}">
                                            @csrf()
                                            <input type="hidden" value="{{ $enc_product_id }}" name="product"/>
                                            <input type="hidden" value="{{ $enc_variant_id }}" name="variant"/>
                                        </form>
                                        <button type="submit" form="set-variant-default-form" id="{{ $enc_variant_id }}-btn" class="btn btn-success btn-xs btn-block" >SET AS DEFAULT</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
<script>

</script>
@endsection
