@extends ('layouts.purchasing-raw-department.app')
@section ('title')
    Payment Request
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
    <li class="breadcrumb-item">Payment Request</li>
    <li class="breadcrumb-item active">{{ $payment_request->pr_number }}</li>
@endsection
@section('content')
    @php
        $amountLimit = paymentRequestAmountLimit('PETTY-CASH');
        $pettyCashLimit = $amountLimit['amount'];
        // if category is supplier. get supplier details
        $supplier = '';
        $selected_supplier = null;
        $enc_payment_request_id = encryptor('encrypt',$payment_request->id);
        $selected_po = array(); // ginagamit ko lang ito kapag ang category ay SUPPLIER
        if($payment_request->category == 'SUPPLIER'){
            if(isset($payment_request->details[0])){
                $selected_supplier = '';
                $selected_supplier = encryptor('encrypt',$payment_request->details[0]->supplier_id);
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
                        <p class="mb-0">Updating payment request will validate first before process.</p>
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <div class="flex-fill">
                        <span class="h5 mt-0">PETTY CASH LIMIT [ <text class="text-danger">&#8369; {{ number_format($pettyCashLimit,2) }}</text> ]</span>
                        <p class="mb-0">{{ $amountLimit['text'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="panel-1" class="panel">
                <div class="row p-3">
                    @if($payment_request->status == 'R-DEPARTMENT')
                        <div class="col-md-7 text-danger">
                            <p>* Note: Moving this request to <b>PENDING</b> status will be no longer to modify / edit. Please double check the inputs.</p>
                        </div>
                        <div class="col-md-5 text-right">
                            <form method="post"  id="update-payment-status-form" onsubmit="$('#update-payment-status-btn').attr('disabled',true)" id="update-payment-request-status" action="{{ route('purchasing-raw-payment-request-functions',['id' => 'update-payment-request-status']) }}">
                                @csrf()
                                <input type="hidden" name="status" id="update-status" value="PENDING"/>
                                <input type="hidden" name="key" value="{{ $enc_payment_request_id }}"/>
                            </form>
                            <button type="button" onClick="updatePaymentStatus('CANCELLED')"  id="cancel-payment-status-btn" title="CANCEL PR" disabled class="btn btn-danger move-to-status-btn"><span class="fas fa-times "></span> | CANCEL</button>
                            <button type="button" onClick="updatePaymentStatus('PENDING')"  id="update-payment-status-btn" title="Update status to FOR APPROVAL" disabled class="btn btn-info move-to-status-btn">VALIDATE & MOVE TO PENDING | <span class="fas fa-arrow-right "></span></button>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                    @endif
                    <div class="col-md-6 mt-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Payment Type <text class="text-danger">* required</text></label>
                                    <select class="form-control" onChange="displayByPaymentType(this.value)" required  id="payment_type" name="payment_type">
                                        <option value="">Choose Type</option>
                                        <option {{ isSelected($payment_request->type,'CASH') }} {{ isSelected($payment_request->type,'PETTY-CASH') }} value="CASH">CASH</option>
                                        <!-- Automated na ito. -->
                                        <!--option value="PETTY-CASH">PETTY CASH</option-->
                                        <option {{ isSelected($payment_request->type,'CHEQUE') }} value="CHEQUE">CHEQUE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Category <text class="text-danger">* required</text></label>
                                    <select class="form-control" onChange="displayByCategory(this.value)" required id="category" name="category">
                                        <option value="">Choose Category</option>
                                        @foreach(paymentRequestCategory() as $index=>$category)
                                            <option {{ isSelected($payment_request->category,$index) }} value="{{ $index }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-1" id="department-section" style="display:none">
                                    <label class="form-control-plaintext">Select Department
                                        <text class="text-danger" id="department-title"></text>
                                    </label>
                                    <select required  class="form-control" id="department">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option {{ isSelected($payment_request->designated_department,$department->code) }} value="{{ $department->code }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row check-details-section" style="display:none">
                            <div class="col-md-12">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Payee
                                        <text class="text-danger" id="payee-title"></text>
                                        <text class="text-danger float-right m-0">If empty, payee = requested by</text>
                                    </label>
                                    <select data-placeholder="Select Payee"  required  class="form-control cheque-details" id="payee"></select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Cheque type</label>
                                    <select class="form-control cheque-details"  id="cheque-type" name="cheque_type">
                                        <option value="">Choose type</option>
                                        <option {{ isSelected($payment_request->cheque_type,'POST') }} value="POST">POST DATED</option>
                                        <option {{ isSelected($payment_request->cheque_type,'DATED') }} value="DATED">DATED CHEQUE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Cheque Date</label>
                                    <div class="input-group m-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                        </div>
                                        <input type="text" class="form-control datepicker" id="cheque-date" name="cheque_date" value="{{ $payment_request->cheque_date }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="cash-details-section" style="display:none">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Control Number</label>
                                    <input type="number" id="control-number" class="form-control form-control-sm cash-details" name="control_number" value="">
                                </div>
                            </div>
                            <div class="col-md-5" style="display:none">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Account Titles <text class="text-danger">* Required</text></label>
                                    <select  onChange="getParticulars(this.value)" class="form-control" id="account_titles" name="account_title">
                                        <option></option>
                                        @foreach($accountTitles as $accountTitle)
                                            <option {{ isSelected($payment_request->account_title_id,$accountTitle->id) }} value="{{ $accountTitle->id }}">{{ $accountTitle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-7" style="display:none">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Particulars
                                        <text class="text-danger">* Required</text>
                                        <text class="text-info float-right">
                                            <a onClick="showCreateParticipant()" href="javascript:;" ><span class="badge badge-info p-1"><strong> <span class="fas fa-plus"></span> | Particulars</strong></span> </a>
                                        </text>
                                    </label>
                                    <select data-placeholder="Choose Particular" onChange="$('#update-particular').val(this.value);" class="form-control"  id="particulars" name="particular">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Requested By</label>
                                    <input type="hidden" id="employee_id" value="{{ $payment_request->employee_id }}" name="employee_id"/>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <div class="custom-control custom-checkbox">
                                                    @if(!empty($payment_request->employee_id))
                                                        <input type="checkbox" checked class="custom-control-input" onChange="isEmployee(this)" id="is_employee">
                                                    @else
                                                        <input type="checkbox"  class="custom-control-input" onChange="isEmployee(this)" id="is_employee">
                                                    @endif
                                                    <label class="custom-control-label" for="is_employee">Employee</label>
                                                </div>
                                            </div>
                                        </div>
                                        @if(!empty($payment_request->employee_id))
                                            <input type="text" readonly name="requested_by" value="{{ $payment_request->requested_by }}"  id="requested_by" class="form-control" >
                                        @else
                                            <input type="text" name="requested_by" value="{{ $payment_request->request_by }}"  id="requested_by" class="form-control" >
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Request Amount <text class="text-danger">* Required</text></label>
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">&#8369;</span>
                                        </div>
                                        <input type="number" onfocusout="$('#base_request_amount').val(this.value);$('#pr-partials-tbl tbody').empty()" class="form-control" id="request_amount" name="request_amount" value="{{ $payment_request->requested_amount }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Note: <text class="text-danger">( Other purposes [ Must Based on selected particulars ] )</text></label>
                                    <textarea name="note" id="note" rows="3" class="form-control">{{ $payment_request->note }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-2">
                        <div class="check-details-section row mb-2" style="display:none">
                            <div class="col-md-12">
                                <div class="row mb-2">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <h5 class="m-0">Partial Payment Request.</h5>
                                            <p class="m-0">Create partials payment with this request.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <div class="input-group m-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">&#8369;</span>
                                                </div>
                                                <input type="number" readonly class="form-control" id="base_request_amount" value="{{ round($payment_request->requested_amount,2) }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info btn-icon" type="button" onClick="addPartials()"><span class="fas fa-plus"></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                @if($payment_request->status == 'R-DEPARTMENT')
                                   <p class="text-danger mb-1">Any Changes listed below must click <b>"Update Request"</b> button.</p>
                                @endif
                                <table id="pr-partials-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                    <thead class="bg-warning-500">
                                    <tr>
                                        <th width="40%">Amount</th>
                                        <th width="50%">Purpose</th>
                                        @if($payment_request->status == 'R-DEPARTMENT')
                                            <th width="10%">Actions</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($payment_request->is_partial == true)
                                        @foreach($payment_request->partials as $index => $partial)
                                            @php
                                                $enc_payment_request_partial_id = encryptor('encrypt',$partial->id)
                                            @endphp
                                            <tr id="partial-row-{{ $enc_payment_request_partial_id }}">
                                                <td>
                                                    &#8369; {{ number_format($partial->amount,2) }}
                                                    <input name="partials[]" type="hidden" value="{{ $partial->amount }}">
                                                    <input name="cheque_dates[]" type="hidden" value="{{ $partial->cheque_date }}">
                                                    <input name="cheque_types[]" type="hidden" value="{{ $partial->cheque_type }}">
                                                    <hr class="m-0">
                                                    <p class="m-0 text-info">{{ $partial->cheque_type }}: {{ readableDate($partial->cheque_date) }}</p>
                                                    @if($payment_request->status == 'PENDING')
                                                        <hr class="m-0 mb-1">
                                                        <span class="badge badge-info">Validating by the accounting</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $partial->purpose }}
                                                    <input name="purposes[]" type="hidden" value="{{ $partial->purpose }}">
                                                </td>
                                                @if($payment_request->status == 'R-DEPARTMENT')
                                                    <td>
                                                        <button onClick="removeRow('partial-row-{{ $enc_payment_request_partial_id }}')" class="btn btn-sm btn-danger btn-icon" onClick=""><span class="fas fa-times"></span></button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="alert alert-info client-supplier">
                            <p class="m-0"> For Supplier and Client category display in this section.</p>
                        </div>
                        <div class="row pr-clients-details" style="display:none">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Client
                                        <text class="text-danger">( <b>* Note: For Client Purpose, Fill this section. </b> )</text>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="text"  value="Please Keep Receipts or Other references for liquidatation" readonly class="form-control">
                                        <div class="input-group-append">
                                            <button type="button" onClick="selectClients()" class="btn btn-info" type="button">Select Client</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <table id="pr-clients-references" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                    <thead class="bg-warning-500">
                                    <tr>
                                        <th width="50%">Name</th>
                                        <th width="40%">Quotation</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($payment_request->category == 'CLIENT')
                                        @foreach($payment_request->details as $index=>$detail)
                                            @php
                                                $enc_client_id = encryptor('encrypt',$detail->client_id)
                                            @endphp
                                            <tr id="client-row-{{ $enc_client_id }}{{ $index }}">
                                                <td>
                                                    <input type="hidden" name="client_keys[]" value="{{ $enc_client_id }}"/>
                                                    <input type="hidden" name="names[]" value="{{ $detail->client->name }}"/>
                                                    {{ $detail->client->name }}
                                                    <hr class="m-0">
                                                    <text class="text-info">Industry: {{ $detail->client->industry->name }}</text>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="text"  value="{{ $detail->name }}" id="{{ $enc_client_id }}{{ $index }}-quotation-number" name="quotations[]" readonly class="form-control form-control-sm">
                                                        <div class="input-group-append">
                                                            <button type="button" title="Select Quotation" onClick="selectClientQuotation('{{ $enc_client_id }}','{{ $enc_client_id }}{{ $index }}')" class="btn btn-info form-control-sm" type="button"><span class="fas fa-eye"></span></button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button onClick=removeRow("client-row-{{ $enc_client_id }}{{ $index }}")  class="btn btn-sm btn-icon btn-danger"><span class="fas fa-times"></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row pr-suppliers-details" style="display:none">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Supplier
                                        <text class="text-danger">( <b>* Note: For Supplier Purpose, Fill this section. </b> )</text>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="selected-supplier" value="{{ $supplier }}"  readonly class="form-control">
                                        <div class="input-group-append">
                                            <button class="btn btn-info" type="button" disabled onClick="selectSupplier()" type="button">Select Supplier</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <table id="pr-suppliers-references" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                    <thead class="bg-warning-500">
                                    <tr>
                                        <th width="67%">PO</th>
                                        <th width="30%">G.T</th>
                                        {{--                                        <th width="3%">Action</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($payment_request->category == 'SUPPLIER')
                                        @foreach($payment_request->details as $detail)
                                            @php
                                                $enc_purchase_order_id = encryptor('decrypt',$detail->purchaseOrder->id);
                                                $paymentType = $detail->purchaseOrder->payment_type;
                                                if(!empty($detail->purchaseOrder->payment_terms)){
                                                    $paymentType .= ' [ '.$detail->purchaseOrder->payment_terms.' ] Day/s';
                                                }
                                                array_push($selected_po,$detail->purchaseOrder->po_number);
                                            @endphp
                                            <tr id="po-row-{{ $enc_purchase_order_id }}">
                                                <td>

                                                    <input type="hidden" name="keys[]" value="{{ $enc_purchase_order_id }}" />
                                                    <input type="hidden" name="po[]" value="{{ $detail->purchaseOrder->po_number }}" />
                                                    {{ $detail->purchaseOrder->po_number }}
                                                    <hr class="m-0">
                                                    <text class="text-info">P.TYPE: {{ $paymentType }}</text>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="gt[]" value="{{ $detail->purchaseOrder->grand_total }}" />
                                                    <input type="hidden" name="ewt[]" value="{{ $detail->purchaseOrder->ewt_amount }}" />
                                                    <input type="hidden" name="vat[]" value="{{ $detail->purchaseOrder->vat_amount }}" />
                                                    &#8369; {{ number_format($detail->purchaseOrder->grand_total,2) }}
                                                    <hr class="m-0">
                                                    <p class="m-0 text-info">VAT: &#8369; {{ $detail->vat_amount }}</p>
                                                    <p class="m-0 text-info">EWT: &#8369; {{ $detail->ewt_amount }}</p>
                                                </td>
                                                {{--                                                    <td>--}}
                                                {{--                                                        <button onClick=removeRow("po-row-{{ $enc_purchase_order_id }}") class="btn btn-icon btn-danger btn-sm"><span class="fas fa-times"></span></button>--}}
                                                {{--                                                    </td>--}}
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            @if($payment_request->liquidations->count() > 0)
                                <div class="col-md-12 with-terms-supplier-section mt-2" >
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <div class="form-group">
                                                <h5 class="m-0">WITH TERMS P.O.</h5>
                                                <p class="m-0">For with terms P.O, required to list Invoices per P.O</p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="pr-suppliers-invoice" class="table mt-0 table-bordered table-hover w-100 dataTable dtr-inline">
                                                <thead class="bg-warning-500">
                                                <tr>
                                                    <th width="47%">Invoice</th>
                                                    <th width="63%">Remarks</th>
                                                    {{--                                                    <th width="3%">Action</th>--}}
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($payment_request->liquidations as $index=>$liquidate)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-primary">P.O</span> | {{ $liquidate->po_number }}
                                                            <hr class="m-0">
                                                            <span class="badge badge-primary">S.I</span> | {{ $liquidate->reference_number }}
                                                            <hr class="m-0">
                                                            <span class="badge badge-primary">AMOUNT</span> |  &#8369; {{ number_format($liquidate->amount,2) }}
                                                        </td>
                                                        <td>
                                                            {{ $liquidate->remarks }}
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
                    </div>
                    <div class="col-md-12">
                        <hr class="m-2 mb-2">
                    </div>
                    <div class="col-md-6 text-danger">
                        <p>* Note: Payment status is ON <b>PENDING</b>. Modification in this request is enabled.   </p>
                    </div>
                    @if($payment_request->status == 'PENDING')
                        <div class="col-md-6 text-right">
                            <button type="button" onClick="updatePaymentStatus('CANCELLED')"  id="cancel-payment-status-btn" title="CANCEL PR" disabled class="btn btn-danger move-to-status-btn"><span class="fas fa-times "></span> | CANCEL</button>
                        </div>
                    @elseif($payment_request->status == 'R-DEPARTMENT')
                        <div class="col-md-6 text-right">
                            <button type="button" onClick="updateRequest()" id="btn-update-request" disabled class="btn btn-info btn-sm"><span class="fas fa-save"></span> | Update Request</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="employee-list-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Encoded Employee </b>
                        <small class="m-0 text-muted">
                            Please select employee listed below.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="employees-table" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                <thead class="bg-warning-500">
                                <tr>
                                    <th width="5%"></th>
                                    <th style="display:none">firstname</th>
                                    <th style="display:none">lastname</th>
                                    <th width="90%">Name</th>
                                    <th width="5%">Actions</th>
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
    <div class="modal fade" id="client-list-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Clients list </b>
                        <small class="m-0 text-muted">
                            All Clients.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="clients-table"  class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                <thead class="bg-warning-500">
                                <tr>
                                    <th width="5%"></th>
                                    <th width="90%">Name</th>
                                    <th width="5%">Actions</th>
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
    <div class="modal fade" id="supplier-list-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Supplier list </b>
                        <small class="m-0 text-muted">
                            All Suppliers.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="suppliers-table"  class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                <thead class="bg-warning-500">
                                <tr>
                                    <th width="5%"></th>
                                    <th width="93%">Name</th>
                                    <th width="2%">Actions</th>
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
    <div class="modal fade" id="client-quotations-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Client's Quotation Modal list </b>
                        <small class="m-0 text-muted">
                            List all Quotation in this client.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="client-quotations-table"  class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                <thead class="bg-warning-500">
                                <tr>
                                    <th width="5%"></th>
                                    <th style="display:none">Quotation #</th>
                                    <th width="92%">Quotation #</th>
                                    <th width="3%">Actions</th>
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
    <div class="modal fade" id="add-particular-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Create Particular </b>
                        <small class="m-0 text-muted">
                            Create particular in this account title [ <text id="account-title-text"></text> ]
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-plaintext">Participant Name</label>
                                <input type="hidden" id="account-title-key" value=""/>
                                <input class="form-control form-control-sm" value="" id="participant-name">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pt-2">
                    <button type="button" onClick="addParticipant(this.id)" id="add-participant-btn" class="btn btn-warning">Add Particular</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cancel-pr-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> CANCEL PAYMENT REQUEST <br>[ {{ $payment_request->pr_number }} ] </b>
                        <small class="m-0 text-muted">
                            Make sure this PR is cancellable. It can't be revertible.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post"  id="cancel-payment-status-form" onsubmit="$('#cancel-pr-btn').attr('disabled',true)" id="update-payment-request-status" action="{{ route('purchasing-raw-payment-request-functions',['id' => 'update-payment-request-status']) }}">
                                @csrf()
                                <div class="form-group">
                                    <input type="hidden" name="status" value="CANCELLED"/>
                                    <input type="hidden" name="key" value="{{ $enc_payment_request_id }}"/>
                                    <p class="text-danger m-0">Let the system know why you want to cancel this PR</p>
                                    @if($payment_request->is_partial == true)
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
                    <button type="submit" form="cancel-payment-status-form" id="cancel-pr-btn" class="btn btn-warning">CANCEL PR</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add-partial-request-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> Create Partial Request </b>
                        <small class="m-0 text-muted">
                            Can divide / partial if needed. Please review partial entries.
                        </small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-1">
                                <label class="form-control-plaintext">Partial Amount</label>
                                <div class="input-group m-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">&#8369;</span>
                                    </div>
                                    <input type="number" class="form-control" id="partial-amount" value="">
                                </div>
                            </div>
                            <div class="form-group mb-1">
                                <label class="form-control-plaintext">Cheque type
                                </label>
                                <select class="form-control cheque-details"  id="partial-cheque-type">
                                    <option value="">Choose type</option>
                                    <option value="POST">POST DATED</option>
                                    <option value="DATED">DATED CHEQUE</option>
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label class="form-control-plaintext">Cheque Date</label>
                                <div class="input-group m-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                    </div>
                                    <input type="text" class="form-control datepicker" id="partial-date-cheque" value="">
                                </div>
                            </div>
                            <div class="form-group mb-1">
                                <label class="form-control-plaintext">Payment Type / Purpose</label>
                                <textarea class="form-control" id="partial-purpose" name="purpose" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pt-2">
                    <button type="button" onClick="createPartial(this)" class="btn btn-warning">Add Partial</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <input id="old_pos" type="hidden" value="{{ json_encode($selected_po) }}" />
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="{{  asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    <script>
        var selected_payee = 0;
        var selected_payee_name = '';
        var selected_category = '{{ $payment_request->category }}';
        var selected_client = '';
        var selected_supplier = '{{ $selected_supplier }}';
        var selected_client_key = ''; // for row modification
        var old_pos = null; // retrieved data that need to revert if selected po has changed.
        $(function(){
            $("#account_titles").select2({
                placeholder: "Choose Account Title",
                allowClear: true,
            });
            if('{{ $payment_request->payee_id }}' != ''){
                selected_payee = '{{ $payment_request->payee_id }}';
                selected_payee_name = '{{ $payment_request->payee_name }}';
            }
            displayByPaymentType("{{ $payment_request->type }}");
            displayByCategory("{{ $payment_request->category }}");
            if('{{ $payment_request->account_title_id }}' != ''){
                $("#account_titles").select2('data', {id: '{{ $payment_request->account_title_id }}' });
                getParticulars('{{ $payment_request->account_title_id }}');
            }
            if(selected_supplier != '' || selected_supplier != 0){
                if(selected_category == 'SUPPLIER'){
                    old_pos = JSON.parse($('#old_pos').val());
                    var sum = 0;
                    $("input[name='gt[]']").each(function() {
                        sum += parseFloat($(this).val());
                    });
                    $('#request_amount').val(sum.toFixed(2));
                }
            }
            $('.move-to-status-btn').attr('disabled',false);
            $('#btn-update-request').attr('disabled',false);
        });
        function employees(){
            $('#employees-table').dataTable().fnDestroy();
            $('#employees-table').DataTable({
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('purchasing-raw-payment-request-functions',['id' => 'employee-list']) }}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(result){  // error handling
                        $('#err').html(JSON.stringify(result));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'first_name', name: 'first_name',visible: false },
                    { data: 'last_name', name: 'last_name',visible: false },
                    { data: 'name', name: 'name',orderable: false, searchable: false },
                    { data: 'actions', name: 'actions'},
                ]
            });
        }
        function payees(){
            $("#payee").select2({
                ajax:{
                    type: "POST",
                    url: "{{ route('purchasing-raw-payment-request-functions',['id' => 'payee-list']) }}",
                    //dataType: 'json',
                    delay: 250,
                    data: function(params){
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(result, params){
                        if(result.success == 1){
                            params.page = params.page || 1;
                            var data = JSON.parse(result.data);
                            return {
                                results: data,
                                pagination:
                                    {
                                        more: (params.page * 5) < data.length
                                    }
                            };
                        }
                    },
                    cache: true
                },
                initSelection : function (element, callback) {
                    if(selected_payee != '' || selected_payee != 0){
                        callback({id:selected_payee,text:selected_payee_name});
                    }
                },
                placeholder: "Select Payee",
                allowClear: true,
                //dropdownParent: $('#add-qty-modal'),
                escapeMarkup: function(markup)
                {
                    return markup;
                }, // let our custom formatter work
                minimumInputLength: 1,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        }
        function formatRepo(repo){
            if (repo.loading){
                return repo.text;
            }
            var markup = "<div class='select2-result-repository clearfix d-flex'>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title small fs-lg fw-500'> "+ repo.name + "</div>";
            if (repo.created_by.username){
                markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>Added By: " + repo.created_by.username + "</div>";
            }
            return markup;
        }
        function formatRepoSelection(repo){
            if(repo.name){
                selected_payee = repo.id;
                selected_payee_name = repo.name;
                selected_quotation = repo.id;
                return repo.name;
            }else{
                return repo.text;
            }
        }
        function displayByCategory(category){
            selected_supplier = null;
            $('.client-supplier').show();
            $('#request_amount').attr('readonly',false);
            if(category == 'SUPPLIER'){
                $('#request_amount').val('');
            }
            // departments
            $('#department-section').hide();
            $('#department').attr('required',false);
            $('#department-title').text('');
            // clients
            $('.pr-clients-details').hide();
            //$('#pr-clients-references tbody').empty();
            // suppliers
            $('.pr-suppliers-details').hide();
            //$('#pr-suppliers-references tbody').empty();
            selected_category = category;
            if(category == 'OFFICE'){
                $('#department-section').fadeIn('slow');
                $('#department').attr('required',true);
                $('#department-title').text('* required');
                $("#department").select2();
                $('.client-supplier').fadeIn('slow');
            }else{
                if(category != ''){
                    if(category == 'CLIENT'){
                        $('.pr-clients-details').fadeIn('slow');
                        $('.client-supplier').hide();
                    }
                    else if(category == 'SUPPLIER'){
                        $('#request_amount').attr('readonly',true);
                        $('.pr-suppliers-details').fadeIn('slow');
                        $('.client-supplier').hide();
                    }
                }
            }
        }
        function addParticipant(btn){
            $('#'+btn).attr('disabled',true);
            formData = new FormData();
            formData.append('key', $('#account-title-key').val());
            formData.append('name', $('#participant-name').val());
            $.ajax({
                type: "POST",
                url: "{{route('purchasing-raw-payment-request-functions',['id' => 'add-particulars'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success == 1){
                        getParticulars($('#account-title-key').val());
                        $('#account-title-key').val('');
                        $('#participant-name').val('');
                        $('#add-particular-modal').modal('hide');
                        alert_message('Add Particulars',result.message,'success');
                    }else{
                        alert_message('Add Particulars',result.message,'danger');
                    }
                    $('#'+btn).attr('disabled',false);
                },
                error: function(result){
                    alert_message('Add Particulars',result.responseText,'danger');
                    $('#'+btn).attr('disabled',false);
                }
            });
        }
        function showCreateParticipant(){
            var account_title_key = $('#account_titles').val();
            var account_title_text = $("#account_titles option:selected").text();
            $('#account-title-text').text(account_title_text);
            $('#account-title-key').val(account_title_key);
            $('#add-particular-modal').modal('show');
        }
        function suppliers(){
            $('#suppliers-table').dataTable().fnDestroy();
            $('#suppliers-table').DataTable({
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('purchasing-raw-payment-request-functions',['id' => 'supplier-list']) }}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(result){  // error handling
                        $('#err').html(JSON.stringify(result));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name'},
                    { data: 'actions', name: 'actions'},
                ]
            });
        }
        function clients(){
            $('#clients-table').dataTable().fnDestroy();
            $('#clients-table').DataTable({
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('purchasing-raw-payment-request-functions',['id' => 'clients-list']) }}", // json datasource
                    type: "POST",  // method  , by default get
                    error: function(result){  // error handling
                        $('#err').html(JSON.stringify(result));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name'},
                    { data: 'actions', name: 'actions'},
                ]
            });
        }
        function displayByPaymentType(payment_type){
            $("#payee").select2();
            $('#payee-title').text('');
            $('#payee').attr('required',false);

            $('#cash-details-section').hide();
            $('.cash-details').attr('required',false);
            $('.check-details-section').hide();
            $('.cheque-details').attr('required',false);
            if(payment_type == 'CHEQUE'){
                payees();
                $('#payee-title').text('* required');
                $('#payee').attr('required',true);

                $('.check-details-section').fadeIn('slow');
                $('.cheque-details').attr('required',true);
            }else{
                if(payment_type != ''){
                    $('#cash-details-section').fadeIn('slow');
                    $('.cash-details').attr('required',true);
                }
            }
        }
        function getParticulars(key){
            $('#update-account-title').val(key);
            if($('#particulars').data('select2')){
                $("#particulars").select2('destroy');
            }
            $('#particulars').attr('disabled',true);
            $('#particulars').empty();
            $('#particulars').append('<option value="">Loading...</option>');

            formData = new FormData();
            formData.append('key', key);
            $.ajax({
                type: "POST",
                url: "{{route('purchasing-raw-payment-request-functions',['id' => 'get-particulars'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success == 1){
                        var particulars = JSON.parse(result.data)
                        $('#particulars').append('<option value=""></option>');
                        for(i=0; i < particulars.length;i++){
                            if('{{ $payment_request->particular_id }}' == particulars[i].id ){
                                $('#particulars').append('<option selected value="'+particulars[i].id+'">'+particulars[i].name+'</option>');
                            }else{
                                $('#particulars').append('<option value="'+particulars[i].id+'">'+particulars[i].name+'</option>');
                            }
                        }
                        $("#particulars").select2({
                            placeholder: "Choose Particular",
                            allowClear: true,
                        });
                    }else{
                        alert_message('Get Particulars',result.message,'danger');
                        $('#particulars').empty();
                    }
                    $('#particulars').attr('disabled',false);
                },
                error: function(result){
                    alert_message('Get Particulars',result.responseText,'danger');
                    $('#particulars').empty();
                    $('#particulars').attr('disabled',false);
                }
            });
        }
        function isEmployee(obj){
            $('#employee_id').val('');
            $('#requested_by').val('');
            if(obj.checked){
                $('#employee-list-modal').modal('show');
                $('#requested_by').attr('readonly',true);
                employees();
            }else{
                $('#requested_by').attr('readonly',false);
            }
        }
        function selectRequestedBy(key){
            $('#employee_id').val($('#employee-key-'+key).val());
            $('#requested_by').val($('#employee-name-'+key).val());
            $('#employee-list-modal').modal('hide');
        }
        function selectClients(){
            clients();
            $('#client-list-modal').modal('show');
        }
        function selectSupplier(){
            suppliers();
            $('#supplier-list-modal').modal('show');
        }
        function addPrSupplier(key){
            selected_supplier = key;
            $('#pr-suppliers-references tbody').empty();
            $('#selected-supplier').val($('#'+key+"-supplier").val());
            $('#supplier-list-modal').modal('hide');
            toastMessage('GET P.O`s','Fetching all APPROVED and FOR-REQUEST P.O for payment..','info','toast-bottom-right');
            formData = new FormData();
            formData.append('supplier', selected_supplier);
            $.ajax({
                type: "POST",
                url: "{{ route('purchasing-raw-payment-request-functions',['id' => 'supplier-validated-po']) }}", // STATUS: APPROVED P. STATUS: FOR-REQUEST
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success == 1){
                        $('#pr-suppliers-references tbody').append(result.data);
                        var sum = 0;
                        $("input[name='gt[]']").each(function() {
                            sum += parseFloat($(this).val());
                        });
                        $('#request_amount').val(sum.toFixed(2));
                    }else{
                        alert_message('Generating supplier P.O',result.message,'danger');
                    }
                    toastr.clear();
                },
                error: function(result){
                    alert_message('Generating supplier P.O',result.responseText,'danger');
                    toastr.clear();
                }
            });
        }
        function addPrClient(key,tempKey){
            var name = $("#"+key+"-name").val();
            var industry = $("#"+key+"-industry").val();
            $('#pr-clients-references tbody').append(
                '<tr id="client-row-'+tempKey+'">' +
                '<td>' +
                '<input type="hidden" name="client_keys[]" value="'+key+'">'+
                '<input type="hidden" name="names[]" value="'+name+'">'+
                ''+name+
                '<hr class="m-0">'+
                '<text class="text-info">Industry: '+industry+'</text>'+
                '</td>'+
                '<td>'+
                '<div class="input-group input-group-sm">'+
                '<input type="text"  value="" id="'+tempKey+'-quotation-number" name="quotations[]" readonly class="form-control form-control-sm">'+
                '<div class="input-group-append">'+
                '<button type="button" title="Select Quotation" onClick=selectClientQuotation("'+key+'","'+tempKey+'") class="btn btn-info form-control-sm" type="button"><span class="fas fa-eye"></span></button>'+
                '</div>'+
                '</div>'+
                '</td>'+
                '<td>' +
                '<button onClick=removeRow("client-row-'+tempKey+'")  class="btn btn-sm btn-icon btn-danger"><span class="fas fa-times"></button>'+
                '</td>' +
                '</tr>'
            );
            $('#client-list-modal').modal('hide');
        }
        function removeRow(key){
            $('#'+key).remove();
            if(selected_category == 'SUPPLIER'){
                var sum = 0;
                $("input[name='gt[]']").each(function() {
                    sum += parseFloat($(this).val());
                });
                $('#base_request_amount').val(sum.toFixed(2));
                if(key.includes("partial-row-") == 'true'){
                    $('#pr-partials-tbl tbody').empty();
                }
                $('#request_amount').val(sum.toFixed(2));
            }
        }
        function selectClientQuotation(key,tempKey){
            selected_client = key;
            selected_client_key = tempKey;
            $('#client-quotations-table').dataTable().fnDestroy();
            $('#client-quotations-table').DataTable({
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('purchasing-raw-payment-request-functions',['id' => 'client-quotations-list']) }}", // json datasource
                    type: "POST",  // method  , by default get
                    data : { client: key },
                    error: function(result){  // error handling
                        $('#err').html(JSON.stringify(result));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'quote_number', name: 'quote_number',visible:false},
                    { data: 'name', name: 'name',orderable: false, searchable: false},
                    { data: 'actions', name: 'actions'},
                ]
            });
            $('#client-quotations-modal').modal('show');
        }
        function addPrClientQuotation(key,quote_number){
            var is_unique = true;
            $("input[name='quotations[]']").each(function() {
                if($(this).val() == quote_number){
                    is_unique = false;
                    return false;
                }
            });
            if(is_unique == true){
                $('#'+selected_client_key+"-quotation-key").val(key);
                $('#'+selected_client_key+"-quotation-number").val(quote_number);
                $('#client-quotations-modal').modal('hide');
            }else{
                alert_message('Select Quotation','Quotation is already added','danger');
            }
        }
        function updateRequest(){
            formData = new FormData();
            $('#btn-update-request').attr('disabled',true);
            confirm_message('Update Payment Request','Are you sure you want to  update this request ?' , function (confirmed) {
                if(confirmed) {
                    // standard inputs
                    var payment_type = $('#payment_type').val();
                    var category = $('#category').val();
                    formData.append('payment_request_key',"{{ $enc_payment_request_id }}");
                    formData.append('payment_type',payment_type);
                    formData.append('category',category);
                    formData.append('account_title',$('#account_titles').val());
                    formData.append('particular',$('#particulars').val());
                    formData.append('employee',$('#employee_id').val());
                    formData.append('requested_by',$('#requested_by').val());
                    formData.append('request_amount',$('#request_amount').val());
                    formData.append('note',$('#note').val());
                    if(payment_type != ''){
                        if(payment_type == 'CASH'){
                            formData.append('control_number',$('#control-number').val());
                        }else if (payment_type == 'CHEQUE'){
                            formData.append('payee_key',selected_payee);
                            formData.append('payee_name',decodeHtml(selected_payee_name));
                            formData.append('cheque_type',$('#cheque-type').val());
                            formData.append('cheque_date',$('#cheque-date').val());
                        }
                        $("input[name='partials[]']").each(function(index){
                            formData.append('partials[]',$(this).val());
                            formData.append('purposes[]',$('[name="purposes[]"]:eq('+index+')').val());
                            formData.append('cheque_dates[]',$('[name="cheque_dates[]"]:eq('+index+')').val());
                            formData.append('cheque_types[]',$('[name="cheque_types[]"]:eq('+index+')').val());
                        });
                    }
                    if(category != ''){
                        if(category == 'OFFICE'){
                            formData.append('designate_department',$('#department').val());
                        }
                        else if(category == 'CLIENT'){
                            // loop
                            //client_keys encrypted
                            //names
                            //quotation_key encrypted
                            //quotations
                            $("input[name='client_keys[]']").each(function(index) {
                                formData.append('clients_key[]',this.value);
                                formData.append('clients_names[]',$('[name="names[]"]:eq('+index+')').val());
                                formData.append('quotations[]',$('[name="quotations[]"]:eq('+index+')').val());
                            });
                        }
                        else if(category == 'SUPPLIER'){
                            $("input[name='keys[]']").each(function(index){
                                formData.append('supplier_key',selected_supplier);
                                formData.append('grand_total[]',$('[name="gt[]"]:eq('+index+')').val());
                                formData.append('purchase_order[]',$('[name="po[]"]:eq('+index+')').val());
                                formData.append('old_purchase_order[]',old_pos[index]);
                            });
                        }
                    }
                    toastMessage('Payment Request','Validate and updating request....', 'info', 'toast-bottom-right');
                    $.ajax({
                        type: "POST",
                        url: "{{route('purchasing-raw-payment-request-functions',['id' => 'update-payment-request'])}}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            if(result.success == 1){
                                location.reload();
                                alert_message('Update Payment Request',result.message,'success');
                                toastr.clear();
                            }else{
                                alert_message('Update Payment Request',result.message,'danger');
                                $('#btn-update-request').attr('disabled',false);
                                toastr.clear();
                            }
                        },
                        error: function(result){
                            alert_message('Update Payment Request',result.responseText,'danger');
                            $('#btn-update-request').attr('disabled',false);
                            toastr.clear();
                        }
                    });
                }else{
                    $('#btn-update-request').attr('disabled',false);
                    toastr.clear();
                }
            });
        }
        function updatePaymentStatus(status){
            if(status == 'CANCELLED'){
                $('#cancel-pr-modal').modal('show');
            }else if(status == 'PENDING'){
                $('#update-payment-status-btn').attr('disabled',true);
                confirm_message('Update Payment Request Status','Are you sure you want to  update this request status ?' , function (confirmed) {
                    if(confirmed) {
                        $('#update-payment-status-form').submit();
                    }else{
                        $('#update-payment-status-btn').attr('disabled',false);
                    }
                });
            }
        }
        function addPartials(){
            var requestedAmount = $('#request_amount').val();
            if(requestedAmount == '' || requestedAmount == null){
                alert_message('Create Partial Request','Request Amount field is required.','danger');
            }else{
                $('#add-partial-request-modal').modal('show');
            }
        }
        function createPartial(btn){
            $(btn).attr('disabled',true);
            var totalPartials = 0;
            formData = new FormData();
            formData.append('cheque_type',$('#partial-cheque-type').val());
            formData.append('cheque_date',$('#partial-date-cheque').val());
            formData.append('request_amount',$('#request_amount').val());
            formData.append('partial_amount',$('#partial-amount').val());
            formData.append('partial_purpose',$('#partial-purpose').val());
            $("input[name='partials[]']").each(function(){
                totalPartials += parseFloat($(this).val());
            });
            formData.append('total_partials_added',totalPartials);
            toastMessage('Create Partial Payment','Validate and creating partial request....', 'info', 'toast-bottom-right');
            $.ajax({
                type: "POST",
                url: "{{ route('purchasing-raw-payment-request-functions',['id' => 'create-partial-payment-request']) }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result){
                    if(result.success == 1){
                        $('#pr-partials-tbl tbody').append(result.data);
                        $('#add-participant-modal').modal('hide');
                        $(btn).attr('disabled',false);
                    }else{
                        alert_message('Create Partial Payment Request',result.message,'danger');
                        $(btn).attr('disabled',false);
                    }
                    toastr.clear();
                },
                error: function(result){
                    alert_message('Create Partial Payment Request',result.responseText,'danger');
                    $(btn).attr('disabled',false);
                    toastr.clear();
                }
            });
        }
        function decodeHtml(html) {
            var txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }
    </script>
@endsection
