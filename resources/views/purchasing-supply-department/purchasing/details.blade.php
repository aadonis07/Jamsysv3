@extends ('layouts.purchasing-supply-department.app')
@section ('title')
    Purchasing | {{ $purchaseOrder->supplier->name }} | Update P.O Price
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item "><a href="{{ route('purchasing-supply-list') }}">Purchasing</a></li>
    <li  class="breadcrumb-item "> <a href="{{ route('purchasing-supply-supplier-list') }}">Suppliers</a></li>
    <li title="{{ $purchaseOrder->supplier->name }}" class="breadcrumb-item ">{{ $purchaseOrder->supplier->name }}</li>
    <li class="breadcrumb-item active">Update P.O Price</li>
@endsection
@section('content')
    @php
        $supplier = $purchaseOrder->supplier;
        $enc_supplier_id = encryptor('encrypt',$supplier->id);
        $enc_purchase_order = encryptor('encrypt',$purchaseOrder->id);
        $total_purchased = 0;
    @endphp
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
                        <span class="h5 mt-0">[ <text class="text-dark">Purchase Order</text> ] <text class="text-muted">{{ $purchaseOrder->po_number }}</text></span>
                        @if($purchaseOrder->status == 'PENDING')
                            <p class="mb-0"><b>{{ $purchaseOrder->status }}</b> P.O: Can modify only prices in this P.O</p>
                        @elseif($purchaseOrder->status == 'FOR-APPROVAL')
                            <p class="mb-0"><b>{{ $purchaseOrder->status }}</b> P.O: Waiting to approve by proprietor</p>
                        @else
                            <p class="mb-0">Purchaser Order details below.</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <!-- Galing sa quotation to. Didisplay dito mga items na nasa quotation na merong supplier. -->
                    <p class="h5 mb-0">Status:
                        <b class="text-primary"><b>{{ $purchaseOrder->status }}</b></b>
                    </p>
                    <p class="h5 mb-0">Added Items:
                        <b class="text-danger">[ {{ $purchaseOrder->products->count()  }} ]</b>
                    </p>
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
                                <div class="col-md-5">
                                    <span class="h5 mb-0">Supplier's Information</span>
                                    <p class="mb-0"><text class="text-dark"><b>[ {{ $supplier->tin_number }} ]</b></text> {{ $supplier->name }}</p>
                                    <p class="text-dark">Industry: {{ $supplier->industry->name }}</p>
                                    <span class="h5 mb-0">Contact</span>
                                    <p class="mb-0">{{ $supplier->contact_person }}</p>
                                    <p class="mb-0">{{ $supplier->email }}</p>
                                    <p class="mb-0">{{ implode(' | ',explode(',',$supplier->contact_number)) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <span class="h5 mb-0">Address</span>
                                    <p class="mb-0">{{ $supplier->cityProvince->city_name }}</p>
                                    <p class="text-dark mb-1">Province: {{ $supplier->cityProvince->province->description }}</p>
                                    <p class="mb-0">{{ $supplier->complete_address }}</p>
                                </div>
                                <div class="col-md-3 text-right">
                                    <p class="mb-0  ">P.O #: <text class="text-dark fw-700"><b>{{ $purchaseOrder->po_number }} &nbsp;&nbsp;</b></text></p>
                                    <p class="mb-0">Date Created: <text class="text-danger">{{ readableDate(getDatetimeNow()) }}</text></p>
                                    <p class="mb-0">Prepare By: </p>
                                    <p class="mb-0"><i class="text-dark">{{  $purchaseOrder->createdBy->username }}</i></p>
                                    <hr class="m-0">
                                    @if($purchaseOrder->status == 'APPROVED')
                                        <hr class="m-0">
                                        <p class="mb-0 text-success"><b>APPROVED BY: {{ $purchaseOrder->approved_by }}</b></p>
                                        <div class="alert alert-warning mt-1 p-2">
                                            <p class="m-0"> Payment Type
                                            <p class="m-1"><b>
                                                    {{ $purchaseOrder->payment_type }}
                                                    @if(!empty($purchaseOrder->payment_terms))
                                                        [ {{ $purchaseOrder->payment_terms }} ] Day/s
                                                    @endif
                                            </b></p>
                                            @if($purchaseOrder->payment_status == 'FOR-REQUEST')
                                                <p class="m-0">
                                                    <a href="javascript:;" onClick="updatePaymentType()" title="Update payment type."><span class="badge badge-info"><span class="fas fa-edit"></span>| Update</span></a>
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                    @if($purchaseOrder->status == 'CANCELLED')
                                        <hr class="m-0">
                                        <p class="mb-0 text-danger">Cancelled By: {{ $purchaseOrder->approved_by }}</p>
                                        <p class="mb-0 text-danger">Remarks: <br>
                                            <i>{{ $purchaseOrder->remarks }}</i>
                                        </p>
                                    @endif
                                    @if($purchaseOrder->status == 'REJECTED')
                                        <hr class="m-0">
                                        <p class="mb-0 text-danger">Rejected By: {{ $purchaseOrder->updatedBy->username }}</p>
                                        <p class="mb-0 text-danger">Remarks: <br>
                                            <i>{{ $purchaseOrder->remarks }}</i>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table id="po-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                            <tr role="row">
                                <th width="5%"></th>
                                <th width="20%">Product</th>
                                <th width="20%">Description</th>
                                <th width="20%">Quantity</th>
                                <th width="10%">Unit Price</th>
                                <th width="10%">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($purchaseOrder->products as $product)
                                <tr id="row-{{ $product->id }}">
                                    @php
                                        $defaultLink = 'http://placehold.it/754x977';
                                        $defaultLink = imagePath($product->img,$defaultLink);
                                        $total_purchased += $product->total_price;
                                    @endphp
                                    <td>
                                        <img src="{{ $defaultLink }}" class="img-fluid"/>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $product->type }}</span> | {{ $product->name }}
                                        <hr class="m-0">
                                        <text class="text-dark">Date Created: {{ readableDate($product->created_at) }}</text>
                                        <br>
                                        <text class="text-dark">Created By: {{ $product->createdBy->username }}</text>
                                        <p class="text-dark m-0">Last Update: {{ $product->updatedBy->username }}</p>
                                    </td>
                                    <td>
                                        {!! html_entity_decode($product->description) !!}
                                    </td>
                                    <td>
                                        <div id="product-info-{{ $product->id }}" style="display:none">
                                            <div class="form-group mb-1">
                                                <input name="purchase_order" type="hidden" value="{{ $enc_purchase_order }}"/>
                                                <input name="supplier_key" type="hidden" value="{{ $enc_supplier_id }}"/>
                                                <input name="purchase_order_type" type="hidden" value="{{ $product->type }}"/>
                                                <input name="purchase_order_product_key" type="hidden" value="{{ $product->id }}"/>
                                                <label class="form-control-plaintext">Product</label>
                                                <textarea disabled class="form-control" rows="2">{{ $product->type }} {{ $product->name }}</textarea>
                                            </div>
                                        </div>
                                        <b class="text-dark">TOTAL: {{ number_format($product->qty) }} </b>
                                        @if($purchaseOrder->status == 'PENDING')
                                            <button onClick="addQtyModal('{{ $product->id }}')" class="btn btn-xs btn-primary float-right ">Add</button>
                                        @endif
                                        <hr class="m-0 mt-2">
                                        <p class="m-0">Detailed</p>
                                        <ul>
                                            @foreach($product->details as $detail)
                                                @if($purchaseOrder->status == 'PENDING')
                                                    <div id="qty-detail-{{ $detail->id }}" style="display:none">
                                                        <div class="col-md-12 mt-0">
                                                            <div class="form-group">
                                                                <div class="input-group input-group-sm">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text text-dark">
                                                                            Quantity
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="purchase_order" value="{{ $product->purchase_order_id }}">
                                                                    <input type="hidden" name="detail_id" value="{{ $detail->id }}">
                                                                    <input type="hidden" name="purchase_order_product_key" value="{{ $product->id }}">
                                                                    <input type="hidden" name="supplier_key" value="{{ $enc_supplier_id }}">
                                                                    @if($detail->type == 'SUPPLY' && !empty($detail->quotation_id))
                                                                        <input name="qty"  onkeydown="return event.key != 'Enter';" required type="number" value="{{ round($detail->qty) }}" min="1" class="form-control form-control-sm update-qty-{{ $detail->id }}"/>
                                                                    @else
                                                                        <input name="qty"  required type="number" value="{{ round($detail->qty) }}" min="1" class="form-control form-control-sm "/>
                                                                    @endif
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon"  type="submit" form="update-qty-product-form" ><i class="fal fa-edit"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($purchaseOrder->status == 'PENDING')
                                                    @if(!empty($detail->quotation_id))
                                                        <li class="small"> [ <a onClick=updateQty("{{ $detail->id }}","{{ $detail->type }}","{{ $detail->product_id }}","{{ $detail->quotation_id }}") href="javascript:;" href="javascript:;">{{ number_format($detail->qty )}} qty</a> ] {{ $detail->name }}
                                                            <ul>
                                                                <li>{{ $detail->quotation_product_name }}</li>
                                                            </ul>
                                                        </li>
                                                    @else
                                                        <li> [ <a onClick=updateQty("{{ $detail->id }}") href="javascript:;" href="javascript:;">{{ number_format($detail->qty )}} qty</a> ] {{ $detail->name }}
                                                            <ul>
                                                                <li>{{ $detail->designated_department }}</li>
                                                            </ul>
                                                        </li>
                                                    @endif
                                                @else
                                                    @if(!empty($detail->quotation_id))
                                                        <li class="small"> [ {{ number_format($detail->qty )}} qty ] {{ $detail->name }}
                                                            <ul>
                                                                <li>{{ $detail->quotation_product_name }}</li>
                                                            </ul>
                                                        </li>
                                                    @else
                                                        <li> [ {{ number_format($detail->qty )}} qty ] {{ $detail->name }}
                                                            <ul>
                                                                <li>{{ $detail->designated_department }}</li>
                                                            </ul>
                                                        </li>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @if($purchaseOrder->status == 'PENDING')
                                            <form method="post" id="update-price-product-form-{{ $product->id }}" action="{{ route('purchasing-supply-purchasing-functions',['id' => 'update-price-product']) }}">
                                                @csrf()
                                                <input type="hidden" name="purchase_order" value="{{ $enc_purchase_order }}">
                                                <input type="hidden" name="purchase_order_product_key" value="{{ $product->id }}">
                                                <input type="hidden" name="supplier_key" value="{{ $enc_supplier_id }}">
                                                <div class="form-group">
                                                    <input type="number" value="{{ $product->price }}" name="price" required class="form-control input-sm" placeholder="Quantity">
                                                    <button type="submit" form="update-price-product-form-{{ $product->id }}" class="btn btn-primary btn-xs btn-block">Update</button>
                                                </div>
                                            </form>
                                        @else
                                            &#8369; {{ number_format($product->price,2) }}
                                        @endif
                                    </td>
                                    <td>
                                        <input type="hidden" name="item_total_price[]" value="{{ $product->total_price }}"/>
                                        <p><b class="">&#8369; {{ number_format(($product->total_price),2)  }} </b></p>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                @php
                                    $discount = $purchaseOrder->discount;
                                    $vat_amount = $purchaseOrder->vat_amount;
                                    $is_vat = false;
                                    if($vat_amount > 0){
                                        $is_vat = true;
                                    }
                                    $non_vat_value = $purchaseOrder->total_ordered - $discount - $vat_amount;
                                    $total_amount = ( $non_vat_value ) + $vat_amount;
                                    $ewt = $purchaseOrder->ewt * 100;
                                    $ewt_amount =  $purchaseOrder->ewt_amount;
                                    $grand_total =  $purchaseOrder->grand_total;
                                @endphp
                                <td colspan="4" class="text-right p-1">
                                    <h6 class="text-dark m-0"><b>TOTAL PURCHASED ( NON-VAT ): </b></h6>
                                    <p class="text-danger m-0"><i>On-Queue Items is not included. Please Save it first</i></p>
                                    <p class="text-muted m-0">F: [ <b>Sum of Total Price /  VAT</b> ]</p>
                                </td>
                                <td colspan="2"  class="p-1 align-middle">
                                    <div class="form-group form-group-sm">
                                        <input type="number" readonly id="total_purchased" value="{{ $non_vat_value }}" name="total_purchased" {{ isSelected($supplier->vatable,1,'checked') }} class="form-control form-control-sm input-sm">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right p-1 align-middle">
                                    <h6 class="text-dark m-0"><b>DISCOUNT: </b></h6>
                                    <p class="text-danger m-0">Ignore this field if not applicable</p>
                                </td>
                                <td colspan="2" class="p-1 align-middle">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    &#8369;
                                                </span>
                                            </div>
                                            @if($purchaseOrder->status == 'PENDING')
                                                <input id="input-discount" onfocusout="compute('save-po-btn')" name="discount" type="number" class="form-control" value="{{ round($discount,2) }}">
                                            @else
                                                <input id="input-discount" readonly name="discount" type="number" class="form-control" value="{{ round($discount,2) }}">
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right p-1 align-middle">
                                    <p class="text-danger p-0 m-0"><i>Check if vatable amount.</i></p>
                                    <text class="text-muted">F: [ <b>( T. Purchased - Discount ) - Total Purchased [ NON-VAT ] )</b> ]</text>
                                </td>
                                <td colspan="2" class="p-1 align-middle">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <div class="custom-control custom-checkbox">
                                                        @if($purchaseOrder->status == 'PENDING')
                                                            <input type="checkbox" onClick="compute('save-po-btn')"  {{ isSelected($is_vat,true,'checked') }} class="custom-control-input input-sm" id="is_vat">
                                                        @else
                                                            <input type="checkbox"  disabled  {{ isSelected($is_vat,true,'checked') }} class="custom-control-input input-sm" id="is_vat">
                                                        @endif
                                                        <label class="custom-control-label" for="is_vat">VAT</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="number" readonly id="vat_amount"  name="vat_amount" value="{{ round($vat_amount,6) }}" class="form-control form-control-sm input-sm">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right p-1 align-middle">
                                    <h6 class="text-dark m-0"><b>TOTAL AMOUNT: </b></h6>
                                    <p class="text-muted m-0">F: [ <b>( T. Purchased [ NON - VAT ] - Discount ) + VAT</b> ]</p>
                                </td>
                                <td colspan="2" class="p-1 align-middle">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    &#8369;
                                                </span>
                                            </div>
                                            <input readonly id="total_amount" name="total_amount" type="number" class="form-control" value="{{ $total_amount }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right p-1 align-middle">
                                    <p class="text-danger m-0"><i> 1% for goods , 2% for services.</i></p>
                                    <p class="text-danger m-0"><i> Please Ignore some data displayed if not applicable</i></p>
                                    <p class="text-muted m-0"> F: [ <b>Total Purchased [ NON-VAT ] * EWT</b> ] </p>
                                </td>
                                <td colspan="2" class="p-1 align-middle">
                                    <div class="form-group mb-1">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        EWT
                                                    </span>
                                            </div>
                                            @if($purchaseOrder->status == 'PENDING')
                                                <select name="ewt_type" onChange="compute('save-po-btn')" id="ewt_type" class="form-control-sm form-control">
                                                    @foreach(ewtTypes() as $index=>$ewtType)
                                                        <option {{ isSelected($ewt,$index) }} value="{{ $index }}">{{ $index }} %</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input disabled type="text" class="form-control" value="{{ $ewt }} %">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    &#8369;
                                                </span>
                                            </div>
                                            <input readonly id="ewt_amount" name="ewt" type="number" class="form-control" value="{{ $ewt_amount }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right p-1 align-middle">
                                    <h6 class="text-dark m-0"><b>GRAND TOTAL: </b></h6>
                                    <p class="text-muted m-0">F: [ <b>T. Amount - EWT Value</b> ]</p>
                                </td>
                                <td colspan="2" class="p-1 align-middle">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    &#8369;
                                                </span>
                                            </div>
                                            <input readonly id="grand_total" name="grand_total" type="number" class="form-control" value="{{ round($grand_total,6) }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    @if($purchaseOrder->status == 'PENDING')
                        <div class="col-md-9">
                            <div class="alert alert-info p-2">
                                <p class="text-danger m-0">
                                    Moving this P.O to <b>FOR APPROVAL</b> will enable to view Printable P.O ( PDF Format ) for <b>INTERNAL USE</b> purposes.
                                </p>
                                <p class="text-danger m-0"><b>INTERNAL USE</b> - Temporary P.O Docs for the prorietor's approval.</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-right">
                            <form class="" role="form" id="move-to-for-approval-form" onsubmit="$('#save-po-btn').attr('disabled',true)" method="POST" action="{{ route('purchasing-supply-purchasing-functions',['id' => 'po-move-to-for-approval']) }}">
                                @csrf()
                                <input type="hidden" name="purchase_order_key" value="{{ $enc_purchase_order }}" />
                            </form>
                            <button onClick="confirmStatusMove()" id="save-po-btn" class="btn btn-sm btn-info " disabled> <li class="fas fa-arrow-right "></li> | MOVE: FOR APPROVAL</button>
                        </div>
                    @elseif($purchaseOrder->status == 'FOR-APPROVAL')
                        <div class="col-md-10">
                            <div class="alert alert-info p-2 text-center">
                                <p class="text-danger m-0">
                                    <strong>Waiting for Approval</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-2 ">
                            <a target="_blank" href="{{ route('purchasing-supply-pdf-p-o-details',['po' => $purchaseOrder->po_number]) }}" class="btn btn-warning btn-block btn-sm"><span class="fas fa-print"></span> | PRINT [ Internal ]</a>
                        </div>
                    @elseif($purchaseOrder->status == 'APPROVED')
                        <div class="col-md-10">
                            <div class="alert alert-success p-2 text-center">
                                <p class="text-danger m-0">
                                    <strong>P.O {{ $purchaseOrder->po_number }} APPROVED</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-2 ">
                            <a target="_blank" href="{{ route('purchasing-supply-pdf-p-o-details',['po' => $purchaseOrder->po_number]) }}" class="btn btn-warning btn-block btn-sm"><span class="fas fa-print"></span> | PRINT P.O</a>
                        </div>
                    @elseif($purchaseOrder->status == 'REJECTED')
                        <div class="col-md-8">
                            <div class="alert alert-success p-2 text-center">
                                <p class="text-danger m-0">
                                    <strong>P.O {{ $purchaseOrder->po_number }} is REJECTED. Can Revert to PENDING or can Totally CANCELLED</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <form  id="update-po-status-form" action="{{ route('purchasing-supply-purchasing-functions',['id' => 'po-update-status']) }}" method="POST">
                                @csrf()
                                <div class="row mt-0">
                                    <div class="col-md-12 pt-0 mt-0">
                                        <div class="form-group mb-1">
                                            <input type="hidden" value="{{ $enc_purchase_order }}" name="key"/>
                                            <input type="hidden" id="update-status" value="" name="status"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <button type="button" onClick="updatePOStatus('PENDING')" class="btn btn-info btn-sm rejected-btn"><span class="fas fa-redo"></span> | PENDING</button>
                            <button type="button" onclick="updatePOStatus('CANCELLED')" class="btn btn-danger btn-sm rejected-btn"><span class="fas fa-times"></span> | CANCELLED</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="update-detailed-qty-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Update Quantity </b>
                        <small class="m-0 text-muted">
                            Quantity field is required.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <form method="post" id="update-qty-product-form" action="{{ route('purchasing-supply-purchasing-functions',['id' => 'update-qty-product']) }}">
                        @csrf()
                        <div class="row update-qty-detail">

                        </div>
                    </form>
                    <div class="row mt-2" id="added-qty" style="display:none">
                        TOTAL PURCHASED ( NON-VAT ):
                    </div>
                </div>
                <div class="modal-footer ">
                    <button type="button"  class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="err"></div>
    <div class="modal fade" id="add-qty-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Add Quantity </b>
                        <small class="m-0 text-muted">
                            Please Specify the details in adding quantity. FOR DETAILING PURPOSES
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <form method="post" id="add-qty-product-form" onSubmit="$('#add-btn-qty').attr('disabled',true)" action="{{ route('purchasing-supply-purchasing-functions',['id' => 'add-qty-product']) }}" enctype="multipart/form-data">
                        @csrf()
                        <div class="row mt-0">
                            <div class="col-md-12" id="product-info">

                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Select Type</label>
                                    <select  required onChange="displayByType(this.value)" class="form-control" name="type">
                                        @foreach(qtyTypes() as $index=>$type)
                                            <option {{ isSelected($type,'QUOTATION') }} value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Quantity</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">QTY</span>
                                        </div>
                                        <input type="number" name="qty" required class="form-control" placeholder="Quantity">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-1" id="department-section" style="display:none">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Designation <text class="text-danger">*Note: Please department needs this item.</text> </label>
                                    <select class="form-control departments-details"  name="department">
                                        <option value="">Please Choose Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->code }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3" id="quotation-section">
                                <input type="hidden" class="quotation-details" name="quotation_details" id="quotation-details" value=""/>
                                <input type="hidden" class="quotation-details" name="quotation_key" id="quotation-key" value=""/>
                                <input type="hidden" class="quotation-details" name="quotation_product_key" id="quotation-product-key" value=""/>
                                <div class="form-group mb-1">
                                    <label class="form-label" for="quotations">Select Quotation</label>
                                    <select data-placeholder="Select Quotation" required  class="form-control quotation-products" id="quotations"></select>
                                </div>
                                <p class="text-danger mt-0 mb-2">*Note: Please select quotation product for qty reference.</p>
                                <table id="quotation-products-table" class="table table-bordered table-hover w-100  dtr-inline ">
                                    <thead class="bg-warning-500">
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="91%">Product</th>
                                        <th width="4%">QTY</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Remarks</label>
                                    <textarea required name="remarks" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer pt-2">
                    <button type="submit" id="add-btn-qty" form="add-qty-product-form" class="btn btn-warning">Add Quantity</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @if($purchaseOrder->payment_status == 'FOR-REQUEST')
        <div class="modal fade" id="update-payment-status-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-0" >
                        <h5 class="modal-title  mb-0">
                            <b> Update Payment type </b>
                            <small class="m-0 text-muted">
                                Can update payment type before payment request
                            </small>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body pt-0">
                        <form method="post" id="update-payment-type-form" onSubmit="$('#update-payment-status-btn').attr('disabled',true)" action="{{ route('purchasing-supply-purchasing-functions',['id' => 'update-po-payment-type']) }}">
                            @csrf()
                            <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Payment Type</label>
                                    <input type="hidden" value="{{ $enc_purchase_order }}" name="purchase_order_key"/>
                                    <select onChange="isTerms(this.value)"  required class="form-control-sm form-control" name="payment_type">
                                        <option value="">Choose type</option>
                                        <option {{ isSelected($purchaseOrder->payment_type,'COD') }} value="COD">COD</option>
                                        <option {{ isSelected($purchaseOrder->payment_type,'DATED-CHECKS') }} value="DATED-CHECKS">DATED-CHECKS</option>
                                        <option {{ isSelected($purchaseOrder->payment_type,'WITH-TERMS') }} value="WITH-TERMS">WITH-TERMS</option>
                                    </select>
                                </div>
                                <div class="form-group" id="payment_terms-section" style="display:none">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"># of Days</span>
                                        </div>
                                        <input type="number" name="payment_terms" value="{{ $purchaseOrder->payment_terms }}" id="payment_terms" class="form-control" placeholder="Days">
                                    </div>
                                </div>
                                <hr>
                                <p class="text-danger">*Note: Please double check the selected payment type before hitting the "UPDATE" button</p>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="modal-footer pt-2">
                        <button type="submit" id="update-payment-status-btn" form="update-payment-type-form" class="btn btn-warning">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('scripts')
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    @if($purchaseOrder->status == 'PENDING')
        <script>
            var selected_quotation = 0;
            var selected_work_nature = '';
            var selected_quotation_product = '';
            $(function(){
                compute('save-po-btn');
            });
            function addQtyModal(key){
                onProcessQuotations();
                $('#product-info').html($('#product-info-'+key).html());
                $('#add-qty-modal').modal('show');
            }
            function updateQty(key,type = null,quote_product_key = null,quote_id = null){
                $('.update-qty-detail').html($('#qty-detail-'+key).html());
                $('#added-qty').hide();
                if(type == 'SUPPLY' && quote_id != null){
                    var url = "{{ route('purchasing-supply-quotation-product-added-qty') }}";
                    $('.update-qty-btn-'+key).attr('disabled',true);
                    $('#added-qty').fadeIn('slow');
                    $('#added-qty').html('' +
                        '<div class="col-md-12 mt-4">'+
                        '    <div class="d-flex justify-content-center">'+
                        '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                        '            <span class="sr-only">Loading...</span>'+
                        '        </div>'+
                        '    </div>'+
                        '</div>'+
                        '');
                    $("#added-qty").load(url+"?qpid="+quote_product_key+"&&qid="+quote_id, function(responseTxt, statusTxt, jqXHR){
                        var qtyAvailable = $('#max_qty').val();
                        if(qtyAvailable > 0){
                            $('.update-qty-btn-'+key).attr('disabled',false);
                            $('.update-qty-'+key).attr('max',qtyAvailable); // input
                        }
                    });
                }
                $('#update-detailed-qty-modal').modal('show');
            }
            function confirmStatusMove(){
                confirm_message('Move P.O','Are you sure you want to  move this P.O ?' , function (confirmed) {
                    if(confirmed){
                        $('#move-to-for-approval-form').submit();
                    }
                });
            }
            function compute(btn){
                $('#'+btn).attr('disabled',true);
                var total_purchased  = 0;
                var is_vat = false;
                var total_amount = 0;
                var ewt = $('#ewt_type').val();
                var discount = $('#input-discount').val();
                if(discount != '' || discount > 0  ){
                    discount = parseFloat(discount);
                }else{
                    discount = 0;
                    $('#input-discount').val(0);
                }
                if($('#is_vat').is(":checked")){
                    is_vat = true;
                }
                //sum item_total_price
                $('input[name^="item_total_price"]').each(function() {
                    total_purchased += parseFloat($(this).val());
                });
                // compute ewt and display grand total, ewt value,vat value
                ewtFormulation(total_purchased,discount,is_vat,ewt,btn);
            }
            function ewtFormulation(total_purchased,discount,is_vat,ewt,btn){
                formData = new FormData();
                formData.append('purchase_order_id', '{{ $enc_purchase_order }}');
                formData.append('total_purchased', total_purchased);
                formData.append('discount', discount);
                formData.append('vat', is_vat);
                formData.append('ewt', ewt);
                toastMessage('EWT Formulation','Computing. Please wait..','info','toast-bottom-right');
                $.ajax({
                    type: "POST",
                    url: "{{route('purchasing-supply-purchasing-functions',['id' => 'compute-ewt'])}}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (result) {
                        if(result.success == 0){
                            alert_message('EWT Formulation Failed',result.message,'DIALOG','danger');
                            toastr.clear();
                        }else{
                            $('#total_purchased').val(result.data.total_purchased);
                            $('#total_amount').val(result.data.total_amount);
                            $('#vat_amount').val(result.data.vat);
                            $('#ewt_amount').val(result.data.ewt_amount);
                            $('#grand_total').val(result.data.grand_total);
                        }
                        $('#'+btn).attr('disabled',false);
                        toastr.clear();
                    },
                    error: function(result){
                        alert_message('EWT Formulation Failed',result.responseText,'DIALOG','danger');
                        $('#'+btn).attr('disabled',false);
                        toastr.clear();
                    }
                });
            }
            function displayByType(type){
                if(type == 'QUOTATION'){
                    $('.quotation-products').attr('required',true);
                    $('#quotation-section').fadeIn('slow');
                    $('.departments-details').attr('required',false);
                    $('#department-section').fadeOut('slow');
                }else{
                    $('.quotation-products').attr('required',false);
                    $('#quotation-section').fadeOut('slow');
                    $('.departments-details').attr('required',true);
                    $('#department-section').fadeIn('slow');
                }
            }
            function onProcessQuotations(){
                selected_quotation = 0;
                selected_work_nature = '';
                selected_quotation_product = '';
                $('.quotation-details').val('');
                $("#quotations").select2({
                    ajax:{
                        type: "POST",
                        url: "{{ route('purchasing-supply-purchasing-functions',['id' => 'approved-accounting-quotations']) }}",
                        //dataType: 'json',
                        delay: 250,
                        data: function(params){
                            return {
                                q: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function(result, params)
                        {
                            if(result.success == 1){
                                params.page = params.page || 1;
                                var data = JSON.parse(result.data);
                                return {
                                    results: data,
                                    pagination:
                                        {
                                            more: (params.page * 30) < data.length
                                        }
                                };
                            }
                        },
                        cache: true
                    },
                    placeholder: "Select Quotation",
                    allowClear: true,
                    dropdownParent: $('#add-qty-modal'),
                    escapeMarkup: function(markup)
                    {
                        return markup;
                    }, // let our custom formatter work
                    minimumInputLength: 1,
                    templateResult: formatRepo,
                    templateSelection: formatRepoSelection
                });
            }

            function formatRepo(repo)
            {
                if (repo.loading){
                    return repo.text;
                }
                var markup = "<div class='select2-result-repository clearfix d-flex'>" +
                    "<div class='select2-result-repository__avatar mr-2'><img src='" + repo.user_image_url + "' class='width-2 height-2 mt-1 rounded' /></div>" +
                    "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title fs-lg fw-500'>["+ repo.work_nature +"] " + repo.quote_number + "</div>";
                if (repo.client)
                {
                    markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>CLIENT: " + repo.client + "</div>";
                }
                return markup;
            }
            function formatRepoSelection(repo){
                if(repo.quote_number){
                    selected_quotation = repo.id;
                    selected_work_nature = repo.work_nature;
                    $('#quotation-details').val(repo.quote_number+"-"+repo.client);
                    $('#quotation-key').val(repo.id);
                    quotationProducts(selected_quotation);
                    return repo.quote_number+"-"+repo.client;
                }else{
                    return repo.text;
                }
            }
            function quotationProducts(quotation){
                $('#quotation-products-table').dataTable().fnDestroy();
                $('#quotation-products-table').DataTable({
                    "pageLength": 10,
                    "processing": true,
                    "serverSide": true,
                    "lengthMenu": [[10,50, 100, 150, 250], [10,50, 100, 150, 250]],
                    "ajax":{
                        url :"{{ route('purchasing-supply-purchasing-functions',['id' => 'quotation-products']) }}", // json datasource
                        type: "POST",  // method  , by default get
                        data : { quotation: quotation,work_nature: selected_work_nature },
                        error: function(result){  // error handling
                            $('#err').html(JSON.stringify(result));
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex',orderable: false, searchable: false },
                        { data: 'product_name', name: 'product_name' },
                        { data: 'qty', name: 'qty'},
                    ]
                });
            }
            function designatedQty(key,maxQty,type){
                // For quotation only. Limit qty added
                $("#quotation-product-key").val(key);
                if(type == 'SUPPLY'){
                    $('#add-detailed-qty').attr('max',maxQty);
                }
            }
        </script>
    @elseif($purchaseOrder->status == 'REJECTED')
        <script>
            function updatePOStatus(status){
                $('.rejected-btn').attr('disabled',true);
                $('#update-status').val(status);
                var confirmTitle = '';
                var confirmMessage = '';
                if(status == 'PENDING'){
                    confirmTitle = 'REVERT TO PENDING THIS P.O ?';
                    confirmMessage = "Can modify pricing and quantities";
                }else if(status == 'CANCELLED'){
                    confirmTitle = 'CANCELLED THIS P.O ?';
                    confirmMessage = "Cannot revert this P.O.";
                }
                confirm_message(confirmTitle,confirmMessage, function (confirmed) {
                    if(confirmed){
                        $('#update-po-status-form').submit();
                    }else{
                        $('.rejected-btn').attr('disabled',false);
                    }
                });
            }
        </script>
    @endif
    @if($purchaseOrder->payment_status == 'FOR-REQUEST')
        <script>
            $(function(){
                isTerms("{{ $purchaseOrder->payment_type }}");
            });
            function updatePaymentType(){
                $('#update-payment-status-modal').modal('show');
            }
            function isTerms(value){
                $('#payment_terms-section').hide();
                $('#payment_terms').attr('required',false);
                $('#payment_terms').val(null);
                if(value == 'WITH-TERMS'){
                    $('#payment_terms-section').fadeIn('slow');
                    $('#payment_terms').attr('required',true);
                    $('#payment_terms').val('{{ $purchaseOrder->payment_terms }}');
                }
            }
        </script>
    @endif
@endsection
