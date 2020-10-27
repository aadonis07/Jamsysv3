@extends ('layouts.proprietor-department.app')
@section ('title')
    Purchasing | {{ $purchaseOrder->po_number }} | Details
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item "><a href="{{ route('proprietor-purchasing-list') }}">Purchasing</a></li>
    <li  class="breadcrumb-item active"> {{ $purchaseOrder->po_number }}</li>
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
                        <span class="h5 mt-0">[ <text class="text-muted">Purchase Order</text> ] {{ $purchaseOrder->po_number }}</span>
                        <p class="mb-0"><b>{{ $purchaseOrder->status }}</b> P.O: Waiting to Proprietor's approval</p>
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <!-- Galing sa quotation to. Didisplay dito mga items na nasa quotation na merong supplier. -->
                    <p class="h5 mb-0">Status:
                        <b class="text-muted"><b>{{ $purchaseOrder->status }}</b></b>
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
                                    @if($purchaseOrder->status == 'APPROVED')
                                        <hr class="m-0">
                                        <p class="mb-0 text-success"><b>APPROVED BY: {{ $purchaseOrder->approved_by }}</b></p>
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
                                        <hr class="m-0 mt-2">
                                        <p class="m-0">Detailed</p>
                                        <ul>
                                            @foreach($product->details as $detail)
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
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        &#8369; {{ number_format($product->price,2) }}
                                    </td>
                                    <td>
                                        <input type="hidden" name="item_total_price[]" value="{{ $product->total_price }}"/>
                                        <p><b class="">&#8369; {{ number_format(($product->total_price),2)  }} </b></p>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                @php
                                    // default is 1
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
                                            <input id="input-discount" readonly name="discount" type="number" class="form-control" value="{{ round($discount,2) }}">
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
                                                    @if($is_vat == true)
                                                        VAT
                                                    @else
                                                        NONE VAT
                                                    @endif
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
                                            <input disabled type="text" class="form-control" value="{{ $ewt }} %">
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
                    @if($purchaseOrder->status == 'FOR-APPROVAL')
                        <div class="col-md-8">
                            <div class="alert alert-info p-2 text-center">
                                <p class="text-danger m-0">
                                    <strong>Waiting for Approval</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <button onClick="updatePOStat('APPROVED')" class="btn btn-success btn-sm"><span class="fas fa-check"></span> | APPROVED</button>
                            <button onClick="updatePOStat('REJECTED')" class="btn btn-danger btn-sm"><span class="fas fa-times"></span> | REJECT</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div id="err"></div>
    @if($purchaseOrder->status == 'FOR-APPROVAL')
        <div class="modal fade" id="update-p-o-status-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title  mb-0">
                            <b> Update P.O </b>
                            <small class="m-0 text-muted">
                                Confirmation update. Please review first before update
                            </small>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body pt-0 pb-0 mt-0 mb-1">
                        <form  id="update-po-status-form" action="{{route('proprietor-purchasing-functions',['id' => 'po-update-status'])}}" method="POST">
                            @csrf()
                            <div class="row mt-0">
                                <div class="col-md-12 pt-0 mt-0">
                                    <div class="form-group mb-1">
                                        <input type="hidden" value="{{ $enc_purchase_order }}" name="key"/>
                                        <label class="form-control-plaintext">Status</label>
                                        <select required class="form-control" name="status" id="status">
                                            <option value="">Choose Status</option>
                                            <option value="APPROVED">APPROVED</option>
                                            <option value="REJECTED">REJECT</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="form-control-plaintext">Remarks <text class="text-danger">( Optional )</text></label>
                                        <textarea rows="3" name="remarks" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group mb-1">
                                        <p class="text-danger m-0 mb-1">*NOTE: By clicking "UPDATE" button, all information in this P.O are reviewed and verified by this user.</p>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" required name="update_aggreement" id="update-aggreement">
                                            <label class="custom-control-label " for="update-aggreement">I Have read the Note before clicking "UPDATE" button.</label>
                                        </div>
                                    </div>
                                    <hr class="m-0">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" form="update-po-status-form" class="btn btn-primary btn-sm">Update</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('scripts')
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    @if($purchaseOrder->status == 'FOR-APPROVAL')
        <script>
            function updatePOStat(status = null){
                $('#status').val(null);
                if(status == 'APPROVED'){
                    $('#status').val('APPROVED');
                    $("#status option[value*='REJECTED']").prop('disabled',true);
                }else{
                    $("#status option[value*='APPROVED']").prop('disabled',true);
                    $('#status').val(status);
                }
                $('#update-p-o-status-modal').modal('show');
            }
        </script>
    @endif
@endsection
