@extends ('layouts.it-department.app')
@section ('title')
    Payment Request
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item">Payment Request</li>
    <li class="breadcrumb-item active">{{ $payment_request->pr_number }}</li>
@endsection
@section('content')
    @php
        $enc_payment_request_id = encryptor('encrypt',$payment_request->id);
        $isPartial = $payment_request->is_partial;
        if($payment_request->category == 'SUPPLIER'){
            if(isset($payment_request->details[0])){
                $selected_supplier = '';
                $selected_supplier = encryptor('decrypt',$payment_request->details[0]->supplier_id);
                $supplier = $payment_request->details[0]->supplier->department->code.' | '.$payment_request->details[0]->supplier->name;
            }
        }
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
                        <span class="h5 mt-0">[ <text class="text-info">Payment Request</text> ] {{ $payment_request->pr_number }} </span>
                        <p class="mb-0">Requested By: {{ $payment_request->requested_by }}</p>
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <div class="flex-fill">
                        <span class="h5 mt-0 text-info"><b>{{ $payment_request->status }} <text class="text-dark" title="Last Update Date">
                            @if($payment_request->status == 'APPROVED')
                                [ {{ readableDate($payment_request->approved_date) }} ]
                            @else
                                [ {{ readableDate($payment_request->updated_at) }} ]
                            @endif
                        </text></b></span>
                        <p class="mb-0">Created By: {{ $payment_request->createdBy->username }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="panel-1" class="panel">
                <div class="row p-3">
                    @if($payment_request->status == 'RELEASED' && $payment_request->type == 'CHEQUE' && $payment_request->is_partial == false)
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('payment-request-pdf-print-cheque',['prid'=> $enc_payment_request_id]) }}" disabled class="btn btn-warning btn-sm"><span class="fas fa-print"></span> | PRINT CHEQUE</a>
                            <hr class="mt-2 mb-2">
                        </div>
                    @endif
                    <div class="col-md-7">
                        <p class="m-0"><b>CATEGORY: {{ $payment_request->category }}
                            @if($payment_request->category == 'OFFICE')
                                [ {{ $payment_request->designated_department }} ]
                            @endif
                        </b></p>
                        <p class="m-0"><b>PAYMENT TYPE: {{ $payment_request->type }}</b></p>
                    </div>
                    <div class="col-md-5 text-right">
                        <h5 class="text-danger m-0"><b>REQUEST <text class="text-dark" title="CHEQUE TYPE">[ {{ $payment_request->cheque_type }} ]</text> : &#8369; {{ number_format($payment_request->requested_amount,2) }}</b></h5>
                        @if($payment_request->type == 'CHEQUE')
                            <h6 class="m-0 text-dark"><b>PAYEE: {{ $payment_request->payee_name  }}</b></h6>
                        @endif
                    </div>
                    <div class="col-md-12 mb-3 mt-2">
                        <hr class="m-0">
                    </div>
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($payment_request->accountTitle->name))
                                    <p class="m-0"><b>ACCOUNT TITLE: {{ $payment_request->accountTitle->name }}</b></p>
                                @else
                                    <p class="m-0"><b>ACCOUNT TITLE: ---</b></p>
                                @endif
                                @if(isset($payment_request->accountTitleParticular->name))
                                    <p class="m-0"><b>PAYMENT TYPE: {{ $payment_request->accountTitleParticular->name }}</b></p>
                                @else
                                    <p class="m-0"><b>PAYMENT TYPE: ---</b></p>
                                @endif
                                @if($payment_request->status == 'RELEASED')
                                    <hr class="mt-1 mb-1">
                                    @if($payment_request->type == 'CHEQUE')
                                        <p class="m-0"><b>CHEQUE #: {{ $payment_request->cheque_number }}</b></p>
                                        <p class="m-0"><b>CHEQUE DATE: {{ $payment_request->cheque_date }}</b></p>
                                    @else
                                        <p class="m-0"><b>VOUCHER #: {{ $payment_request->voucher_number }}</b></p>
                                    @endif
                                @endif
                                @if($payment_request->category == 'CLIENT')
                                    <hr class="mt-1 mb-1">
                                    <ul>
                                        @foreach($payment_request->details as $detail)
                                            <li> [ {{ $detail->name }} ] - {{ $detail->client->name }} </li>
                                        @endforeach
                                    </ul>
                                @elseif($payment_request->category == 'SUPPLIER')
                                    <hr class="mt-1 mb-1">
                                    @foreach($payment_request->details as $index=>$detail)
                                        @if($index == 0)
                                            <li> {{ $detail->supplier->name }}
                                                <ul>
                                                    <li>{{ $detail->name }} [ G.T &#8369; {{ number_format($detail->amount,2) }}  ]
                                                        <ul>
                                                            <li>VAT: &#8369; {{ $detail->vat_amount }}</li>
                                                            <li>EWT: &#8369; {{ $detail->ewt_amount }}</li>
                                                        </ul>
                                                    </li>
                                        @else
                                            <li>{{ $detail->name }} [ G.T &#8369; {{ number_format($detail->amount,2) }}  ]
                                                <ul>
                                                    <li>VAT: &#8369; {{ $detail->vat_amount }}</li>
                                                    <li>EWT: &#8369; {{ $detail->ewt_amount }}</li>
                                                </ul>
                                            </li>
                                        @endif
                                   @endforeach
                                            </ul>
                                        </li>
                                @endif
                            </div>
                            @if($payment_request->is_partial == true)
                                <div class="col-md-12">
                                    <p class="m-0"><b>PARTIAL PAYMENT STATUS</b></p>
                                    <table class="table">
                                        @foreach($payment_request->partials as $partial)
                                            @php
                                                $enc_partial_payment_request_id = encryptor('encrypt',$partial->id);
                                            @endphp
                                            <tr>
                                                <td width="40%">
                                                    @if($partial->status == 'RELEASED')
                                                        <p class="m-0"><b>BANK:</b> {{ $partial->bank }}</p>
                                                        <p class="m-0"><b>CHEQUE #:</b> {{ $partial->cheque_number }}</p>
                                                        <p class="m-0"><b>CHEQUE DATE:</b> {{ readableDate($partial->cheque_date) }}</p>
                                                        <p class="m-0"><b>AMOUNT:</b> &#8369; {{ number_format($partial->amount,2) }}</p>
                                                        <p class="m-0"><b>PURPOSE:</b> {{ $partial->purpose }}</p>
                                                    @else
                                                        <input type="hidden" value="{{ $partial->cheque_date }}" id="{{ $enc_partial_payment_request_id }}-cheque-date"/>
                                                        <p class="m-0"><b>CHEQUE DATE:</b> {{ readableDate($partial->cheque_date) }}</p>
                                                        <p class="m-0"><b>AMOUNT:</b> &#8369; {{ number_format($partial->amount,2) }}</p>
                                                        <p class="m-0"><b>PURPOSE:</b> {{ $partial->purpose }}</p>
                                                    @endif
                                                </td>
                                                <td width="60%">
                                                    {{ $partial->status }}
                                                    @if($partial->status == 'PENDING')
                                                        | <button type="button" onClick="updatePartialPaymentRequestStatus('{{ $enc_partial_payment_request_id }}')" id="update-partial-payment-status-btn-{{ $enc_partial_payment_request_id }}" disabled class="btn btn-info btn-xs move-to-status-btn">FOR APPROVAL</button>
                                                        <form method="post"  id="update-partial-payment-status-form-{{ $enc_partial_payment_request_id }}" onsubmit="$('#update-partial-payment-status-btn-{{ $enc_partial_payment_request_id }}').attr('disabled',true)" id="update-payment-request-status" action="{{ route('payment-request-functions',['id' => 'update-partial-payment-request-status']) }}">
                                                            @csrf()
                                                            <input type="hidden" name="status" value="FOR-APPROVAL"/>
                                                            <input type="hidden" name="payment_request_key" value="{{ $enc_payment_request_id }}"/>
                                                            <input type="hidden" name="selected_partials" value="{{ $enc_partial_payment_request_id }}"/>
                                                        </form>
                                                    @elseif($partial->status == 'FOR-APPROVAL')
                                                        <button type="button" onClick="updatePartialPaymentRequestStatus('{{ $enc_partial_payment_request_id }}')" id="update-partial-payment-status-btn-{{ $enc_partial_payment_request_id }}" disabled class="btn btn-success btn-xs move-to-status-btn"><span class="fas fa-check"></span> | APPROVED</button>
                                                        <form method="post"  id="update-partial-payment-status-form-{{ $enc_partial_payment_request_id }}" onsubmit="$('#update-partial-payment-status-btn-{{ $enc_partial_payment_request_id }}').attr('disabled',true)" id="update-payment-request-status" action="{{ route('payment-request-functions',['id' => 'update-partial-payment-request-status']) }}">
                                                            @csrf()
                                                            <input type="hidden" name="status" value="APPROVED"/>
                                                            <input type="hidden" name="payment_request_key" value="{{ $enc_payment_request_id }}"/>
                                                            <input type="hidden" name="selected_partials" value="{{ $enc_partial_payment_request_id }}"/>
                                                        </form>
                                                    @elseif($partial->status == 'APPROVED')
                                                        | <button type="button" onClick="approvedPartialPaymentRequest('{{ $enc_partial_payment_request_id }}')" id="update-partial-payment-status-btn-{{ $enc_partial_payment_request_id }}" disabled class="btn btn-success btn-xs move-to-status-btn">RELEASED | <span class="fas fa-check"></span> </button>
                                                    @elseif($partial->status == 'RELEASED')
                                                        | <a target="_blank" href="{{ route('payment-request-pdf-print-cheque',['prid'=> $enc_payment_request_id,'pprid' => $enc_partial_payment_request_id ]) }}" disabled class="btn btn-warning btn-xs"><span class="fas fa-print"></span> | PRINT CHEQUE</a>
                                                    @endif
                                                    <hr class="mt-1 mb-1">
                                                    <p class="m-0"><b>LAST UPDATE:</b> {{ readableDate($partial->updated_at,'time') }}</p>
                                                    <p class="m-0"><b>BY:</b> {{ $partial->updatedBy->username }}</p>
                                                    @if($partial->status == 'RELEASED')
                                                        <p><b>Remarks:</b> {{ $partial->remarks }}</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-5 text-right">
                        <div class="form-group mb-1">
                            <textarea class="form-control" rows="4" disabled>{{ $payment_request->note }}</textarea>
                        </div>
                        @if($payment_request->status == 'CANCELLED' || $payment_request->status == 'REJECTED' || $payment_request->status == 'VOID')
                            <div class="form-group  mt-3 mb-1 text-left">
                                Remarks <text class="text-danger"><b>{{ strToTitle($payment_request->status) }} By {{ $payment_request->updatedBy->username }}</b></text>
                                <textarea class="form-control" rows="4" disabled>{{ $payment_request->remarks }}</textarea>
                            </div>
                        @elseif($payment_request->status == 'APPROVED')
                            <div class="form-group  mt-3 mb-1 ">
                                <h6 class="text-success"><i>{{ strToTitle($payment_request->status) }} By {{ $payment_request->approved_by }}</i></h6>
                            </div>
                        @elseif($payment_request->status == 'RELEASED')
                            <div class="form-group  mt-3 mb-1 text-left">
                                Remarks <text class="text-success"><b>{{ strToTitle($payment_request->status) }} By {{ $payment_request->updatedBy->username }}</b></text>
                                <textarea class="form-control" rows="4" disabled>{{ $payment_request->remarks }}</textarea>
                            </div>
                            @if($payment_request->category == 'SUPPLIER')
                                <div class="row text-left">
                                    <div class="col-md-12 mt-2">
                                        <p class="m-0 mb-1"><b>EWT COLLECTIBLES</b></p>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th width="35%">P.O</th>
                                                    <th width="40%">EWT & VAT</th>
                                                    <th width="25%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($payment_request->details as $index=>$detail)
                                                    <tr>
                                                        <td>
                                                            {{ $detail->name }}
                                                        </td>
                                                        <td>
                                                            <form method="post" id="add-liquidation-{{ $detail->id }}-form" action="{{ route('payment-request-functions',['id' => 'ewt-liquidation']) }}">
                                                                @csrf()
                                                                <input type="hidden" value="{{ $enc_payment_request_id }}" name="payment_request_key"/>
                                                                <input type="hidden" value="{{ $detail->ewt_amount }}" name="ewt"/>
                                                                <input type="hidden" value="{{ $detail->vat_amount }}" name="vat"/>
                                                                <input type="hidden" value="{{ $detail->supplier_id }}" name="supplier_key"/>
                                                                <input type="hidden"value="{{ $payment_request->payee_id }}" name="payee_key"/>
                                                                <input type="hidden" value="{{ $payment_request->payee_name }}" name="payee_name"/>
                                                                <input type="hidden" value="{{ $detail->name }}" name="po_number"/>
                                                                <input type="hidden" value="EWT" name="type"/>
                                                                <input type="hidden" value="SUPPLIER" name="category"/>
                                                            </form>
                                                            <li>G.T &#8369; {{ number_format($detail->amount,2) }}</li>
                                                            <li>VAT: &#8369; {{ number_format($detail->vat_amount,2) }}</li>
                                                            <li>EWT: &#8369; {{ number_format($detail->ewt_amount,2) }}</li>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $isLiquidated = $payment_request->liquidations->where('type','=','EWT')
                                                                                                ->where('supplier_id','=',$detail->supplier_id)
                                                                                                ->where('po_number','=',$detail->name)
                                                                                                ->first();
                                                            @endphp
                                                            @if(!$isLiquidated)
                                                                <button type="submit" form="add-liquidation-{{ $detail->id }}-form" class="btn btn-info btn-xs">Liquidate</button>
                                                            @else
                                                                <span class="badge badge-success">Liquidated</span>
                                                                <hr class="m-0 mt-1">
                                                                {{ readableDate($isLiquidated->date_collected) }}

                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    @if($payment_request->status == 'FOR-APPROVAL')
                        <div class="col-md-12">
                            <hr class="m-2 mb-2">
                        </div>
                        <div class="col-md-7 text-danger">
                            <p class="m-0">
                                * Note: FOR <b>VOID / CANCELLED</b> and  <b>Category is Supplier</b>, It will revert the P.O payment status to <b>FOR-REQUEST</b>.
                                Can create other Payment request for particular/s P.O in supplier.
                            </p>
                        </div>
                        <div class="col-md-5 text-right">
                            <form method="post"  id="update-payment-status-form" onsubmit="$('#update-payment-status-btn').attr('disabled',true)" id="update-payment-request-status" action="{{ route('payment-request-functions',['id' => 'update-payment-request-status']) }}">
                                @csrf()
                                <input type="hidden" name="status" value="APPROVED"/>
                                <input type="hidden" name="key" value="{{ $enc_payment_request_id }}"/>
                            </form>
                            <button type="button" onClick="updatePaymentStatus('VOID')"  id="cancel-payment-status-btn" title="VOID PR" disabled class="btn btn-danger move-to-status-btn"><span class="fas fa-times "></span> | VOID</button>
                            <button type="button" onClick="updatePaymentStatus('REJECTED')"  id="cancel-payment-status-btn" title="REJECT PR" disabled class="btn btn-danger move-to-status-btn"><span class="fas fa-times "></span> | REJECTED</button>
                            <button type="button" onClick="updatePaymentStatus('APPROVED')"  id="update-payment-status-btn" title="APPROVE PR" disabled class="btn btn-success move-to-status-btn"><span class="fas fa-check "></span>  | APPROVED</button>
                        </div>
                    @elseif($payment_request->status == 'REJECTED')
                        <div class="col-md-12">
                            <hr class="m-2 mb-2">
                        </div>
                        <div class="col-md-6 text-danger">
                            <p>* Note: Revert this request to <b>PENDING</b> status will be enable to modify / edit. Also enable to request again for approval.  </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <form method="post"  id="revert-payment-status-form" onsubmit="$('#update-payment-status-btn').attr('disabled',true)" id="update-payment-request-status" action="{{ route('payment-request-functions',['id' => 'update-payment-request-status']) }}">
                                @csrf()
                                <input type="hidden" name="status" value="PENDING"/>
                                <input type="hidden" name="key" value="{{ $enc_payment_request_id }}"/>
                            </form>
                            <button type="button" onClick="updatePaymentStatus('CANCELLED')"  id="cancel-payment-status-btn" title="CANCEL PR" disabled class="btn btn-danger move-to-status-btn"><span class="fas fa-times "></span> | CANCEL</button>
                            <button type="button" onClick="revertPaymentStatus('PENDING')"  id="revert-payment-status-btn" title="Revert to Pending" disabled class="btn btn-info move-to-status-btn">REVERT TO PENDING | <span class="fas fa-arrow-left "></span></button>
                        </div>
                    @elseif($payment_request->status == 'APPROVED')
                            <div class="col-md-12">
                                <hr class="m-2 mb-2">
                            </div>
                            <div class="col-md-7 text-danger">
                                <p class="m-0">
                                    * Note: FOR <b>VOID</b> and  <b>Category is Supplier</b>, It will revert the P.O payment status to <b>FOR-REQUEST</b>.
                                    Can create other Payment request for particular/s P.O in supplier.
                                </p>
                            </div>
                            <div class="col-md-5 text-right">
                            <hr class="m-2 mb-2">
                            <button type="button" onClick="updatePaymentStatus('VOID')"  id="cancel-payment-status-btn" title="VOID PR" disabled class="btn btn-danger move-to-status-btn"><span class="fas fa-times "></span> | VOID</button>
                            <button type="button" onClick="releasePR()" title="RELEASE REQUEST" disabled class="btn btn-success move-to-status-btn">RELEASED | <span class="fas fa-check "></span></button>
                        </div>
                    @elseif($payment_request->status == 'RELEASED')
                        @if($payment_request->category == 'SUPPLIER')
                            @if($payment_request->is_partial == true)
                                @if($payment_request->partials->whereNotIn('status',['RELEASED'])->count() < 1)
                                    <div class="col-md-12 text-right">
                                        <hr class="m-2 mb-2">
                                        <a  href="javascript:;" disabled class="btn btn-warning move-to-status-btn">EWT [ 2307 ] | <span class="fas fa-print "></span></a>
                                    </div>
                                @endif
                            @else
                                <div class="col-md-12 text-right">
                                    <hr class="m-2 mb-2">
                                    <a  href="javascript:;" disabled class="btn btn-warning move-to-status-btn">EWT [ 2307 ] | <span class="fas fa-print "></span></a>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @if($payment_request->liquidations->count() > 0)
            <div class="col-md-12">
                <div id="panel-1" class="panel">
                    <div class="row p-3">
                        <div class="col-md-12 with-terms-supplier-section mt-2" >
                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="form-group mb-1">
                                        <h5 class="m-0">LIQUIDATION</h5>
                                        <p class="m-0">List of liquidated data base on this payment request</p>
                                    </div>
                                    <hr class="mt-2 mb-2">
                                </div>
                                @foreach($payment_request->liquidations as $index=>$liquidate)
                                    <div class="col-md-4 mb-2">
                                        <div class="panel-container">
                                            <div class="panel-content m-0 p-0">
                                                <div class="panel-tag text-dark">
                                                    <section class="m-0 mb-1">
                                                        <p class="text-danger m-0 small"><i>Created at: {{ readableDate($liquidate->created_at) }}</i></p>
                                                        <p class="text-danger m-0 small"><i>Created By: {{ $liquidate->createdBy->username }}</i></p>
                                                    </section>
                                                    <p class="m-0"><b>TYPE:</b> {{ $liquidate->type }}</p>
                                                    <p class="m-0"><b>REFERENCE #:</b> {{ $liquidate->reference_number }}</p>
                                                    <p class="m-0"><b>AMOUNT #:</b> &#8369; {{ number_format($liquidate->amount,2) }}
                                                        | EWT: &#8369; {{ number_format($liquidate->ewt_amount,2) }}
                                                        | VAT: &#8369; {{ number_format($liquidate->vat_amount,2) }}
                                                    </p>
                                                    <p class="m-0"><b>P.O #:</b> {{ $liquidate->po_number }}</p>
                                                    <p class="m-0"><b>REMARKS:</b> {{ $liquidate->remarks }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="modal fade" id="release-pr-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> RELEASE PAYMENT REQUEST <br>[ {{ $payment_request->pr_number }} ] </b>
                        <small class="m-0 text-muted">
                            Please input required fields to complete the transaction
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    @if($payment_request->is_partial == true)
                        <form method="post" id="release-payment-form" onsubmit="$('#release-payment-id').attr('disabled',true)"  action="{{ route('payment-request-functions',['id' => 'update-partial-payment-request-status']) }}">
                            @csrf()
                            <div class="row">
                                <input type="hidden" name="payment_request_key" value="{{ $enc_payment_request_id }}"/>
                                <input type="hidden" name="status" value="RELEASED"/>
                                <div class="col-md-12">
                                    @foreach($payment_request->partials as $partial)
                                        @php
                                            $enc_payment_request_partial_id = encryptor('encrypt',$partial->id);
                                            $classColor = 'alert-primary'; // kapag hindi approved ang staus iba ang kulay.
                                            if($partial->status == 'APPROVED'){
                                                $classColor = '';
                                            }
                                        @endphp
                                        <div class="row {{ $classColor }} ">
                                            <div class="col-md-5">
                                                @if($partial->status == 'APPROVED')
                                                    <div class="form-group mb-1">
                                                        <div class="custom-control custom-checkbox">
                                                            <input onChange="isReleased(this,'{{ $enc_payment_request_partial_id }}')" value="{{ $enc_payment_request_partial_id }}"  id="released-partial-checkbox-{{ $enc_payment_request_partial_id }}"  type="checkbox" class="custom-control-input" name="selected_partials[]" >
                                                            <label class="custom-control-label" for="released-partial-checkbox-{{ $enc_payment_request_partial_id }}"> Released ?</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-1">
                                                        <select disabled name="bank[]" class="{{ $enc_payment_request_partial_id }}-inputs form-control form-control-sm">
                                                            <option value="">Choose Bank</option>
                                                            @foreach($banks as $bank)
                                                                <option value="{{ strtoupper($bank->name) }}">{{ strtoupper($bank->name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                                <div class="form-group mb-0">
                                                    <p class="m-0 mt-1">AMOUNT: &#8369; {{ number_format($partial->amount,2) }}</p>
                                                    <p class="m-0">STATUS: {{ $partial->status }}</p>
                                                    <p class="m-0">PURPOSE: {{ $partial->purpose }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                @if($partial->status == 'APPROVED')
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-group mb-1">
                                                                <label class="form-control-plaintext p-0">Cheque Date</label>
                                                                <div class="input-group">
                                                                    <input type="text" value="{{ $partial->cheque_date }}" class="{{ $enc_payment_request_partial_id }}-inputs form-control form-control-sm datepicker" disabled name="cheque_date[]"  placeholder="Select date">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="form-group mb-1">
                                                                <label class="form-control-plaintext p-0">Cheque #</label>
                                                                    <input class="form-control form-control-sm {{ $enc_payment_request_partial_id }}-inputs" type="number" disabled name="cheque_number[]" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0">
                                                                <label class="form-control-plaintext p-0 ">Remarks <text class="text-danger">( Optional )</text></label>
                                                                <textarea class="form-control {{ $enc_payment_request_partial_id }}-inputs" rows="1" disabled name="remarks[]"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-12">
                                                <hr class="mt-1 mb-1">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    @else
                        <form method="post" id="release-payment-form" onsubmit="$('#release-payment-id').attr('disabled',true)"  action="{{ route('payment-request-functions',['id' => 'update-payment-request-status']) }}">
                            @csrf()
                            <div class="row">
                                <input type="hidden" name="key" value="{{ $enc_payment_request_id }}"/>
                                <input type="hidden" name="status" value="RELEASED"/>
                                @if($payment_request->type == 'CHEQUE')
                                    <div class="col-md-4">
                                        <div class="form-group mb-1">
                                            <label class="form-control-plaintext">Bank</label>
                                            <select required name="bank" class="form-control">
                                                <option value="">Choose Bank</option>
                                                @foreach($banks as $bank)
                                                    <option value="{{ strtoupper($bank->name) }}">{{ strtoupper($bank->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-1">
                                            <label class="form-control-plaintext">Cheque Date</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker" required name="cheque_date" required placeholder="Select date" id="">
                                                <div class="input-group-append">
                                                    <span class="input-group-text fs-xl">
                                                        <i class="fal fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-1">
                                            <label class="form-control-plaintext">Cheque #</label>
                                            <input class="form-control" type="number" required name="cheque_number" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-1">
                                            <label class="form-control-plaintext">Remarks <text class="text-danger">( Optional )</text></label>
                                            <textarea class="form-control" rows="2" name="remarks"></textarea>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <div class="form-group mb-1">
                                            <label class="form-control-plaintext">Voucher #</label>
                                            <input class="form-control" required name="voucher_number" value="">
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="form-control-plaintext">Remarks <text class="text-danger">( Optional )</text></label>
                                            <textarea class="form-control" rows="2" name="remarks"></textarea>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </form>
                    @endif
                </div>
                <div class="modal-footer pt-2">
                    <div class="row mb-0">
                        <div class="col-md-7">
                            <p class="text-danger">* Note: Please double check the fields before trigger the "RELEASED" button</p>
                        </div>
                        <div class="col-md-5 text-right">
                            <button type="submit" form="release-payment-form" id="release-payment-id"  class="btn btn-warning">RELEASED</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cancel-pr-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> <text class="update-status-header-title"></text> PAYMENT REQUEST <br>[ {{ $payment_request->pr_number }} ] </b>
                        <small class="m-0 text-muted">
                            Please double check the status before update
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post"  id="cancel-payment-status-form" onsubmit="$('#cancel-pr-btn').attr('disabled',true)" id="update-payment-request-status" action="{{ route('payment-request-functions',['id' => 'update-payment-request-status']) }}">
                                @csrf()
                                <div class="form-group">
                                    <input type="hidden" name="status" id="update-status" required value=""/>
                                    <input type="hidden" name="key"  required value="{{ $enc_payment_request_id }}"/>
                                    <p class="text-primary m-0">Let the system know why you want to <text class="update-status-header-title"></text> this PR.</p>
                                    @if($isPartial == true)
                                        <p class="text-danger m-0"><strong>* NOTE: VOID / CANCELLED P.R will affect all partial</strong>.
                                        </p>
                                    @endif
                                    <textarea name="remarks" class="form-control" required></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pt-2">
                    <button type="submit" form="cancel-payment-status-form" id="cancel-pr-btn" class="btn btn-warning"><text class="update-status-header-title"></text> PR</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="approve-pr-partial-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> <text class="update-status-header-title"></text> PARTIALS PAYMENT REQUEST <br>[ {{ $payment_request->pr_number }} ] </b>
                        <small class="m-0 text-muted">
                            Please double check the status before update
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    @if($payment_request->status == 'FOR-APPROVAL' && $payment_request->is_partial == true)
                        <form method="post"  id="update-partial-payment-status-form" onsubmit="$('#approved-pr-btn').attr('disabled',true)" id="update-payment-request-status" action="{{ route('payment-request-functions',['id' => 'update-partial-payment-request-status']) }}">
                            @csrf()
                            <div class="row">
                                <input type="hidden" name="status" required value="APPROVED"/>
                                <input type="hidden" name="payment_request_key" value="{{ $enc_payment_request_id }}"/>
                                <div class="col-md-12">
                                    <table id="pr-partials-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                        <thead class="bg-warning-500">
                                        <tr>
                                            <th width="40%">Amount</th>
                                            <th width="50%">Purpose</th>
                                            <th width="10%">APPROVED</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($payment_request->partials as $index => $partial)
                                            @php
                                                $enc_payment_request_partial_id = encryptor('encrypt',$partial->id)
                                            @endphp
                                            <tr id="partial-row-{{ $enc_payment_request_partial_id }}">
                                                <td>
                                                    &#8369; {{ number_format($partial->amount,2) }}
                                                    <hr class="m-0">
                                                    <text class="text-info">{{ $partial->cheque_type }}: {{ readableDate($partial->cheque_date) }}</text>
                                                    <hr class="m-0">
                                                    <text class="text-info">Created: {{ $partial->createdBy->username }}</text>
                                                </td>
                                                <td>
                                                    {{ $partial->purpose }}
                                                </td>
                                                <td class="text-center">
                                                    @if($partial->status == 'FOR-APPROVAL')
                                                        <div class="custom-control custom-checkbox">
                                                            <input  value="{{ $enc_payment_request_partial_id }}"  id="partial-checkbox-{{ $enc_payment_request_partial_id }}"  type="checkbox" class="custom-control-input" name="selected_partials[]" >
                                                            <label class="custom-control-label" for="partial-checkbox-{{ $enc_payment_request_partial_id }}">&nbsp;</label>
                                                        </div>
                                                    @else
                                                        {{ $partial->status }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    @endif
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <p class="text-danger m-0">No Confirmation message. Please double check selected partial/s</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" form="update-partial-payment-status-form" id="approved-pr-btn" class="btn btn-warning">APPROVED P.R</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="released-pr-partial-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> <text class="update-status-header-title"></text> RELEASE PARTIAL PAYMENT REQUEST <br>[ {{ $payment_request->pr_number }} ] </b>
                        <small class="m-0 text-muted">
                            Please double check the status before update
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    <form method="post" id="release-partial-payment-form" onsubmit="$('#released-partial-payment-id').attr('disabled',true)"  action="{{ route('payment-request-functions',['id' => 'update-partial-payment-request-status']) }}">
                        @csrf()
                        <div class="row">
                            <input type="hidden" name="payment_request_key" value="{{ $enc_payment_request_id }}"/>
                            <input type="hidden" id="released-partial-payment-key" name="selected_partials" value=""/>
                            <input type="hidden" name="status" value="RELEASED"/>
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Bank</label>
                                    <select required name="bank" class="form-control form-control-sm">
                                        <option value="">Choose Bank</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ strtoupper($bank->name) }}">{{ strtoupper($bank->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Cheque Date</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" required name="cheque_date" readonly required placeholder="Select date" id="partial-cheque-date">
                                        <div class="input-group-append">
                                            <span class="input-group-text fs-xl">
                                                <i class="fal fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Cheque #</label>
                                    <input class="form-control" type="number" required name="cheque_number" value="">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Remarks <text class="text-danger">( Optional )</text></label>
                                    <textarea class="form-control" rows="2" name="remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer pt-2">
                    <button type="submit" form="release-partial-payment-form" id="released-partial-payment-id" class="btn btn-warning">RELEASED PARTIAL P.R</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script>
        var controls = {
            leftArrow: '<i class="fal fa-angle-left" style="font-size: 1.25rem"></i>',
            rightArrow: '<i class="fal fa-angle-right" style="font-size: 1.25rem"></i>'
        }
        $(function(){
           $('.move-to-status-btn').attr('disabled',false);
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                orientation: "bottom left",
                templates: controls
            });
        });
        function revertPaymentStatus(status){
            if(status == 'PENDING') {
                $('#update-payment-status-btn').attr('disabled', true);
                confirm_message('Update Payment Request Status', 'Are you sure you want to  revert this request status ?', function (confirmed) {
                    if (confirmed) {
                        $('#revert-payment-status-form').submit();
                    } else {
                        $('#update-payment-status-btn').attr('disabled', false);
                    }
                });
            }
        }
        function updatePaymentStatus(status){
            if(status == 'REJECTED' || status == 'VOID' || status == 'CANCELLED' ){
                $('#update-status').val(status);
                $('.update-status-header-title').text(status);
                if(status == 'REJECTED'){
                    $('.update-status-header-title').text('REJECT');
                }
                if(status == 'CANCELLED'){
                    $('.update-status-header-title').text('CANCEL');
                }
                $('#cancel-pr-modal').modal('show');
            }else if(status == 'APPROVED'){
                $('#update-payment-status-btn').attr('disabled',true);
                if('{{ $isPartial }}' != 1) {
                    confirm_message('Revert Payment Request Status', 'Are you sure you want to  update this request status ?', function (confirmed) {
                        if (confirmed) {
                            $('#update-payment-status-form').submit();
                        } else {
                            $('#update-payment-status-btn').attr('disabled', false);
                        }
                    });
                }else{
                    $('#approve-pr-partial-modal').modal('show');
                    $('#update-payment-status-btn').attr('disabled',false);

                }
            }
        }
        function releasePR(){
            $('#release-pr-modal').modal('show');
        }
    </script>
    @if($payment_request->is_partial == true)
        <script>
            function updatePartialPaymentRequestStatus(key){
                $('#update-partial-payment-status-btn-'+key).attr('disabled',true);
                confirm_message('Partial Payment Request Status','Are you sure you want to  update this partial request status ?' , function (confirmed) {
                    if(confirmed) {
                        $('#update-partial-payment-status-form-'+key).submit();
                    }else{
                        $('#update-partial-payment-status-btn-'+key).attr('disabled',false);
                    }
                });
            }
            function isReleased(obj,key){
                if(obj.checked){
                    $('.'+key+"-inputs").attr('disabled',false);
                    $('.'+key+"-inputs").attr('required',true);
                }else{
                    $('.'+key+"-inputs").attr('disabled',true);
                    $('.'+key+"-inputs").attr('required',false);
                }
            }
            function approvedPartialPaymentRequest(key){
                $('#released-partial-payment-key').val(key);
                $('#partial-cheque-date').val($('#'+key+"-cheque-date").val());
                $('#released-pr-partial-modal').modal('show');
            }
        </script>
    @endif
@endsection
