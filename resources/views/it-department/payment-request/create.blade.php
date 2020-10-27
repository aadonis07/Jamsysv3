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
    <li class="breadcrumb-item active">List</li>
@endsection
@section('content')
    @php
        $amountLimit = paymentRequestAmountLimit('PETTY-CASH');
        $pettyCashLimit = $amountLimit['amount'];
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
                        <span class="h5 mt-0">Create Payment Request</span>
                        <p class="mb-0">Creating payment request will validate first before process.</p>
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
                    <div class="col-md-6 mt-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Payment Type <text class="text-danger">* required</text></label>
                                    <select class="form-control" onChange="displayByPaymentType(this.value)" required  id="payment_type" name="payment_type">
                                        <option value="">Choose Type</option>
                                        <option value="CASH">CASH</option>
                                        <!-- Automated na ito. -->
                                        <!--option value="PETTY-CASH">PETTY CASH</option-->
                                        <option value="CHEQUE">CHEQUE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-plaintext">Category <text class="text-danger">* required</text></label>
                                    <select class="form-control" onChange="displayByCategory(this.value)" required id="category" name="category">
                                        <option value="">Choose Category</option>
                                        @foreach(paymentRequestCategory() as $index=>$category)
                                            <option value="{{ $index }}">{{ $category }}</option>
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
                                            <option value="{{ $department->code }}">{{ $department->name }}</option>
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
                            <div class="col-md-7">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Cheque type
                                        <text class="text-danger float-right m-0">Ignore if Partials</text>
                                    </label>
                                    <select class="form-control cheque-details"  id="cheque-type" name="cheque_type">
                                        <option value="">Choose type</option>
                                        <option value="POST">POST DATED</option>
                                        <option value="DATED">DATED CHEQUE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Cheque Date</label>
                                    <div class="input-group m-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                        </div>
                                        <input type="text" class="form-control datepicker" id="cheque-date" name="cheque_date" value="">
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
                            <div class="col-md-5">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Account Titles <text class="text-danger">* Required</text></label>
                                    <select  onChange="getParticulars(this.value)" class="form-control" id="account_titles" name="account_title">
                                        <option></option>
                                        @foreach($accountTitles as $accountTitle)
                                            <option value="{{ $accountTitle->id }}">{{ $accountTitle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Particulars
                                        <text class="text-danger">* Required</text>
                                        <text class="text-info float-right">
                                            <a onClick="showCreateParticipant()" href="javascript:;" ><span class="badge badge-info p-1"><strong> <span class="fas fa-plus"></span> | Particulars</strong></span> </a>
                                        </text>
                                    </label>
                                    <select data-placeholder="Choose Particular" class="form-control"  id="particulars" name="particular">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Requested By</label>
                                    <input type="hidden" id="employee_id" name="employee_id"/>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" onChange="isEmployee(this)" id="is_employee">
                                                    <label class="custom-control-label" for="is_employee">Employee</label>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="text" name="requested_by"  id="requested_by" class="form-control" >
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
                                        <input type="number" onfocusout="$('#base_request_amount').val(this.value);$('#pr-partials-tbl tbody').empty()" class="form-control" id="request_amount" name="request_amount" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-1">
                                    <label class="form-control-plaintext">Note: <text class="text-danger">( Other purposes [ Must Based on selected particulars ] )</text></label>
                                    <textarea name="note" id="note" rows="3" class="form-control"></textarea>
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
                                                <input type="number" readonly class="form-control" id="base_request_amount" value="">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info btn-icon" type="button" onClick="addPartials()"><span class="fas fa-plus"></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table id="pr-partials-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                    <thead class="bg-warning-500">
                                    <tr>
                                        <th width="30%">Amount</th>
                                        <th width="60%">Purpose</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>

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
                                        <input type="text" id="selected-supplier" readonly class="form-control">
                                        <div class="input-group-append">
                                            <button class="btn btn-info" type="button" onClick="selectSupplier()" type="button">Select Supplier</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <table id="pr-suppliers-references" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                    <thead class="bg-warning-500">
                                    <tr>
                                        <th width="60%">PO</th>
                                        <th width="47%">G.T</th>
                                        <th width="3%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 with-terms-supplier-section mt-2" style="display:none">
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
                                                <th width="60%">Remarks</th>
                                                <th width="3%">Action</th>
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
                    <div class="col-md-12  text-right">
                        <hr class="">
                    </div>
                    <div class="col-md-8">
                        <p class="text-danger">* Note: <b>FOR SUPPLIER</b> category, Selected P.O's will also update their payment status [ REQUESTED ]</p>
                    </div>
                    <div class="col-md-4 text-right">
                        <button type="button" onClick="createRequest()" id="btn-create-request" disabled class="btn btn-info"><span class="fas fa-save"></span> | Create Request</button>
                    </div>
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
                                    <th width="55%">Name</th>
                                    <th width="40%">Actions</th>
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
    <div class="modal fade" id="add-participant-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                <label class="form-control-plaintext">Particular Name</label>
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
    <div class="modal fade" id="add-supplier-invoice-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0" >
                    <h5 class="modal-title  mb-0">
                        <b> ADD INVOICE [ S.I ] </b>
                        <small class="m-0 text-muted">
                            For with terms, required to complete their S.I's
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
                                <label class="form-control-plaintext p-0">P.O #</label>
                                <input  type="hidden" id="invoice-po-key" class="form-control" readonly/>
                                <input type="hidden" id="invoice-po-supplier" class="form-control" readonly/>
                                <input type="text" id="invoice-po-number" class="form-control" readonly/>
                            </div>
                            <div class="form-group mb-1">
                                <label class="form-control-plaintext p-0">P.O GRAND TOTAL</label>
                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">&#8369;</span>
                                    </div>
                                    <input type="number" id="invoice-po-grand-total" class="form-control" readonly/>
                                </div>
                            </div>
                            <div class="form-group mb-1">
                                <label class="form-control-plaintext p-0">Reference #</label>
                                <input type="text" class="form-control" maxlength="50" id="reference-number"/>
                            </div>
                            <div class="form-group mb-1">
                                <label class="form-control-plaintext p-0">Amount</label>
                                <input type="number" class="form-control" id="invoice-amount"/>
                            </div>
                            <div class="form-group mb-1">
                                <label class="form-control-plaintext p-0">Remarks</label>
                                <textarea class="form-control" rows="2" id="remarks"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pt-2">
                    <button type="button" onClick="addPoInvoice(this)" id="add-po-invoice-btn" class="btn btn-warning">Add Invoice</button>
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
        var selected_payee = 0;
        var selected_payee_name = '';
        var selected_category = '';
        var selected_client = '';
        var selected_supplier = null;
        var selected_client_key = ''; // for row modification
        var controls = {
            leftArrow: '<i class="fal fa-angle-left" style="font-size: 1.25rem"></i>',
            rightArrow: '<i class="fal fa-angle-right" style="font-size: 1.25rem"></i>'
        }
        $(function(){
            $("#payee").select2();
            $("#account_titles").select2({
                placeholder: "Choose Account Title",
                allowClear: true,
            });
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                orientation: "bottom left",
                templates: controls
            });
            $('#btn-create-request').attr('disabled',false);
        });
        function employees(){
            $('#employees-table').dataTable().fnDestroy();
            $('#employees-table').DataTable({
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('payment-request-functions',['id' => 'employee-list']) }}", // json datasource
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
            var selected_payee = 0;
            var selected_payee_name = '';
            $("#payee").select2({
                ajax:{
                    type: "POST",
                    url: "{{ route('payment-request-functions',['id' => 'payee-list']) }}",
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
                                        more: (params.page * 5) < data.length
                                    }
                            };
                        }
                    },
                    cache: true
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
            $('#request_amount').val('');
            $('#request_amount').attr('readonly',false);
            // departments
            $('#department-section').hide();
            $('#department').attr('required',false);
            $('#department-title').text('');
            // clients
            $('.pr-clients-details').hide();
            $('#pr-clients-references tbody').empty();
            // suppliers
            $('.pr-suppliers-details').hide();
            $('#pr-suppliers-references tbody').empty();
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
                url: "{{route('payment-request-functions',['id' => 'add-particulars'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success == 1){
                        getParticulars($('#account-title-key').val());
                        $('#account-title-key').val('');
                        $('#participant-name').val('');
                        $('#add-participant-modal').modal('hide');
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
            $('#add-participant-modal').modal('show');
        }
        function suppliers(){
            $('#suppliers-table').dataTable().fnDestroy();
            $('#suppliers-table').DataTable({
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('payment-request-functions',['id' => 'supplier-list']) }}", // json datasource
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
                    url :"{{ route('payment-request-functions',['id' => 'clients-list']) }}", // json datasource
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
                url: "{{route('payment-request-functions',['id' => 'get-particulars'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success == 1){
                        var particulars = JSON.parse(result.data)
                        $('#particulars').append('<option value=""></option>');
                        for(i=0; i < particulars.length;i++){
                            $('#particulars').append('<option value="'+particulars[i].id+'">'+particulars[i].name+'</option>');
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
            $('.with-terms-supplier-section').hide();
            $('#pr-suppliers-references tbody').empty();
            $('#selected-supplier').val($('#'+key+"-supplier").val());
            $('#supplier-list-modal').modal('hide');
            $('#base_request_amount').val('');
            $('#pr-partials-tbl tbody').empty()
            toastMessage('GET P.O`s','Fetching all APPROVED and FOR-REQUEST P.O for payment..','info','toast-bottom-right');
            formData = new FormData();
            if($('#'+key+'-with-terms'). is(":checked")){
                formData.append('payment_method','WITH-TERMS');
            }
            if($('#'+key+'-with-vat'). is(":checked")){
                formData.append('is_vat',true);
            }
            formData.append('supplier', selected_supplier);
            $.ajax({
                type: "POST",
                url: "{{ route('payment-request-functions',['id' => 'supplier-validated-po']) }}", // STATUS: APPROVED P. STATUS: FOR-REQUEST
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
                        if($('#'+key+'-with-terms'). is(":checked")) {
                            $('.with-terms-supplier-section').fadeIn('slow');
                        }
                        $('#request_amount').val(sum.toFixed(2));
                        $('#base_request_amount').val(sum.toFixed(2));
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
                if(key.includes("po-row-") == true){
                    var sum = 0;
                    $("input[name='gt[]']").each(function () {
                        sum += parseFloat($(this).val());
                    });
                    $('#request_amount').val(sum.toFixed(2));
                    if (key.includes("partial-row-") == 'true') {
                        $('#pr-partials-tbl tbody').empty();
                    }
                    $('#base_request_amount').val(sum.toFixed(2));
                    //removing also po invoices
                    $('.' + key).remove();
                }
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
                    url :"{{ route('payment-request-functions',['id' => 'client-quotations-list']) }}", // json datasource
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
        function createRequest(){
            formData = new FormData();
            $('#btn-create-request').attr('disabled',true);
            confirm_message('Create Payment Request','Are you sure you want to  create this request ?' , function (confirmed) {
                    if(confirmed) {
                        // standard inputs
                        var payment_type = $('#payment_type').val();
                        var category = $('#category').val();
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
                                formData.append('payee_key',$('#payee').val());
                                formData.append('payee_name',selected_payee_name);
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
                                    formData.append('vat[]',$('[name="vat[]"]:eq('+index+')').val());
                                    formData.append('ewt[]',$('[name="ewt[]"]:eq('+index+')').val());
                                });
                                if($('input').is('[name="invoice_po[]"]')){
                                    $("input[name='invoice_po[]']").each(function(index) {
                                        formData.append('invoice_po[]',this.value);
                                        formData.append('invoice_po_number[]',$('[name="invoice_po_number[]"]:eq('+index+')').val());
                                        formData.append('invoice_amount[]',$('[name="invoice_amount[]"]:eq('+index+')').val());
                                        formData.append('invoice_remarks[]',$('[name="invoice_remarks[]"]:eq('+index+')').val());
                                        formData.append('invoice_reference_number[]',$('[name="invoice_reference_number[]"]:eq('+index+')').val());
                                    });
                                }
                            }
                        }
                        toastMessage('Payment Request','Validate and creating request....', 'info', 'toast-bottom-right');
                        $.ajax({
                            type: "POST",
                            url: "{{route('payment-request-functions',['id' => 'create-payment-request'])}}",
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function (result) {
                                if(result.success == 1){
                                    location.reload();
                                    alert_message('Create Payment Request',result.message,'success');
                                    toastr.clear();
                                }else{
                                    alert_message('Create Payment Request',result.message,'danger');
                                    $('#btn-create-request').attr('disabled',false);
                                    toastr.clear();
                                }
                            },
                            error: function(result){
                                alert_message('Create Payment Request',result.responseText,'danger');
                                $('#btn-create-request').attr('disabled',false);
                                toastr.clear();
                            }
                        });
                    }else{
                        $('#btn-create-request').attr('disabled',false);
                        toastr.clear();
                    }
                });
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
                url: "{{ route('payment-request-functions',['id' => 'create-partial-payment-request']) }}",
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
        function addInvoice(purchase_order_key){
            $('#invoice-po-key').val(purchase_order_key);
            $('#invoice-po-supplier').val($('#invoice-po-supplier-'+purchase_order_key).val());
            $('#invoice-po-grand-total').val($('#po-grand-total-'+purchase_order_key).val());
            $('#invoice-po-number').val($('#po-number-'+purchase_order_key).val());
            $('#add-supplier-invoice-modal').modal('show');
        }
        function addPoInvoice(btn){
            $(btn).attr('disabled',true);
            formData = new FormData();
            var key = $('#invoice-po-key').val();
            var total_invoice_amount = 0; // per p.o
            $("input[name='"+key+"_amount[]']").each(function(){
                total_invoice_amount += parseFloat($(this).val());
            });
            formData.append('total_invoice_amount',total_invoice_amount); // total amount visible in the table
            formData.append('po_key',$('#invoice-po-key').val());
            formData.append('supplier_id',$('#invoice-po-supplier').val());
            formData.append('po_grand_total',$('#invoice-po-grand-total').val());
            formData.append('po_number',$('#invoice-po-number').val());
            formData.append('reference_number',$('#reference-number').val());
            formData.append('invoice_amount',$('#invoice-amount').val());
            formData.append('remarks',$('#remarks').val());
            $.ajax({
                type: "POST",
                url: "{{ route('payment-request-functions',['id' => 'add-invoice-details']) }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result){
                    if(result.success == 1){
                        $('#pr-suppliers-invoice tbody').append(result.data);
                        $(btn).attr('disabled',false);
                    }else{
                        alert_message('Add Invoice details',result.message,'danger');
                        $(btn).attr('disabled',false);
                    }
                },
                error: function(result){
                    alert_message('Add Invoice details',result.responseText,'danger');
                    $(btn).attr('disabled',false);
                }
            });
        }
    </script>
@endsection
