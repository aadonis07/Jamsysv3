@extends ('layouts.purchasing-raw-department.app')
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
                            <a target="_blank" href="{{ route('purchasing-raw-payment-request-pdf-print-cheque',['prid'=> $enc_payment_request_id]) }}" disabled class="btn btn-warning btn-sm"><span class="fas fa-print"></span> | PRINT CHEQUE</a>
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
                                                    <li>{{ $detail->name }} [ G.T &#8369; {{ number_format($detail->amount,2) }}  ]</li>
                                                    @else
                                                        <li>{{ $detail->name }} [ G.T &#8369; {{ number_format($detail->amount,2) }}  ]</li>
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
                                                        <p class="m-0"><b>AMOUNT:</b> &#8369; {{ number_format($partial->amount,2) }}</p>
                                                        <p class="m-0"><b>PURPOSE:</b> {{ $partial->purpose }}</p>
                                                    @endif
                                                </td>
                                                <td width="60%">
                                                    {{ $partial->status }}
                                                    @if($partial->status == 'FOR-APPROVAL')
                                                        @if($payment_request->type == 'CASH')
                                                            | <span class="badge badge-info">Waiting for accounting  response</span>
                                                        @endif
                                                        @if($payment_request->type == 'CHEQUE')
                                                            | <span class="badge badge-info">Waiting for prorietor's response</span>
                                                        @endif
                                                    @elseif($partial->status == 'APPROVED')
                                                        | <span class="badge badge-info">Waiting to release</span>
                                                    @endif
                                                    <hr class="mt-1 mb-1">
                                                    <p class="m-0"><b>LAST UPDATE:</b> {{ readableDate($partial->updated_at,'time') }}</p>
                                                    <p><b>BY:</b> {{ $partial->updatedBy->username }}</p>
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
                        @endif
                    </div>
                    @if($payment_request->status == 'REJECTED')
                        <div class="col-md-12">
                            <hr class="m-2 mb-2">
                        </div>
                        <div class="col-md-6 text-danger">
                            <p>* Note: Revert this request to <b>PENDING</b> status will be enable to modify / edit. Also enable to request again for approval.  </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <form method="post"  id="revert-payment-status-form" onsubmit="$('#update-payment-status-btn').attr('disabled',true)" id="update-payment-request-status" action="{{ route('purchasing-raw-payment-request-functions',['id' => 'update-payment-request-status']) }}">
                                @csrf()
                                <input type="hidden" name="status" value="R-DEPARTMENT"/>
                                <input type="hidden" name="key" value="{{ $enc_payment_request_id }}"/>
                            </form>
                            <button type="button" onClick="updatePaymentStatus('CANCELLED')"  id="cancel-payment-status-btn" title="CANCEL PR" disabled class="btn btn-danger move-to-status-btn"><span class="fas fa-times "></span> | CANCEL</button>
                            <button type="button" onClick="revertPaymentStatus('R-DEPARTMENT')"  id="revert-payment-status-btn" title="Revert to Pending" disabled class="btn btn-info move-to-status-btn">REVERT TO R-DEPARTMENT | <span class="fas fa-arrow-left "></span></button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    @if($payment_request->status == 'REJECTED')
        <script>
            $(function(){
                $('.move-to-status-btn').attr('disabled',false);
            });
            function revertPaymentStatus(status){
                if(status == 'R-DEPARTMENT') {
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
                if(status == 'CANCELLED' ){
                    $('#update-status').val(status);
                    $('.update-status-header-title').text(status);
                    if(status == 'REJECTED'){
                        $('.update-status-header-title').text('REJECT');
                    }
                    if(status == 'CANCELLED'){
                        $('.update-status-header-title').text('CANCEL');
                    }
                    $('#cancel-pr-modal').modal('show');
                }
            }
        </script>
    @endif
@endsection
