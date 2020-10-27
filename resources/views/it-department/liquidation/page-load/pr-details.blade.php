<div class="col-md-12 text-dark">
    <div class="row mb-2">
        <div class="col-md-7">
            <div class="form-group">
                <h5 class="m-0">NEED TO LIQUIDATE</h5>
                <p class="m-0">Amount displayed is the total need to liquidate</p>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                @php
                    $for_liquidation_amount = 0;
                    if($payment_request->is_partial == true){
                        $for_liquidation_amount = $payment_request->partials->where('status','=','RELEASED')->sum('amount');
                    }else{
                        $for_liquidation_amount = $payment_request->requested_amount;
                    }
                    $liquidated = $payment_request->liquidations->sum('amount');
                    $for_liquidation_amount -= $liquidated;
                    // kulang pa ito less din dito yung amount na naliquidate na
                @endphp
                <div class="input-group m-0">
                    <div class="input-group-prepend">
                        <span class="input-group-text">&#8369;</span>
                    </div>
                    <input type="number" readonly class="form-control" id="need_to_liquidate_amount" value="{{ round($for_liquidation_amount,2) }}">
                    <input type="hidden" readonly class="form-control" id="liquidated-amount" value="{{ round($liquidated,2) }}">
                </div>
            </div>
        </div>
        <hr class="mt-1 mb-1">
    </div>
    <div class="row" >
        <input type="hidden" id="payee_id" value="{{ $payment_request->payee_id }}"/>
        <input type="hidden" id="payee_name" value="{{ $payment_request->payee_name }}"/>
        <div class="col-md-6">
            <div class="form-group mb-1">
                <label class="form-control-plaintext p-0">Category</label>
                <input type="text" class="form-control form-control-sm" readonly id="catagory" name="category" value="{{ $payment_request->category }}"/>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-1">
                <label class="form-control-plaintext p-0">Type</label>
                <select class="form-control form-control-sm" id="type" name="type">
                    <option value="" selected>Choose Type</option>
                    @foreach(liquidationTypes() as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($payment_request->category == 'SUPPLIER')
            <div class="col-md-12">
                <div class="form-group mb-1">
                    <label class="form-control-plaintext  p-0">Choose P.O
                        <text class="text-danger text-right">* Note: Select P.O if Needed. </text>
                    </label>
                    <select class="form-control form-control-sm" id="liquidate_po_number" name="po_number">
                        <option value="">Choose P.O</option>
                        @foreach($payment_request->details as $po)
                            <option value="{{ $po->name }}">{{ $po->name }} [ G.T &#8369; {{ number_format($po->amount,2) }} ]</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        <div class="col-md-12">
            <div class="form-group mb-1">
                <label class="form-control-plaintext p-0">Payee
                    <text class="text-right text-danger">
                       *Note: Payee is based on Payment Request. You can change if needed.
                    </text>
                </label>
                <select class="form-control form-control-sm" id="payee" data-placeholder="Select Payee" name="payee">
                </select>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="form-group mb-1">
                <label class="form-control-plaintext p-0">Date Collected</label>
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-sm datepicker"  type="text" value="" id="date-collected" name="date_collected"/>
                    <div class="input-group-append">
                        <span class="input-group-text fs-xl">
                            <i class="fal fa-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="form-group mb-1">
                <label class="form-control-plaintext p-0">Reference #</label>
                <input class="form-control-sm form-control" value="" id="reference-number" name="reference_number"/>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="form-group mb-1">
                <label class="form-control-plaintext p-0">Amount</label>
                <input type="number" class="form-control-sm form-control" value="" id="amount" name="amount"/>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group mb-1">
                <label class="form-control-plaintext p-0">EWT Amount</label>
                <input type="number" class="form-control-sm form-control" value="" id="ewt-amount" name="ewt_amount"/>
            </div>
            <div class="form-group mb-1">
                <label class="form-control-plaintext p-0">Vat Amount</label>
                <input type="number" class="form-control-sm form-control" value="" id="vat-amount" name="vat_amount"/>
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group">
                <label class="form-control-plaintext p-0">Remarks</label>
                <textarea class="form-control" id="remarks" rows="4"></textarea>
            </div>
        </div>
        <div class="col-md-8 mt-2 mb-1 text-danger">
            <p class="m-0">
                *Note: Please make sure that all details are correct. Review first before hitting <b>"Liquidate"</b>
            </p>
        </div>
        <div class="col-md-4 mt-1 mb-1 text-right">
            <button type="button" onClick="createLiquidation(this)" id="liquidate-btn" class="btn btn-info btn-sm">Liquidate</button>
        </div>
        <div class="col-md-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>
                        Details <span class="fw-300"><i>{{ $payment_request->pr_number }}</i></span>
                    </h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-success btn-icon btn-xs" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse">
                            <span class="fa fa-eye"></span>
                        </button>
                    </div>
                </div>
                <div class="panel-container collapse">
                    <div class="panel-content">
                        <div class="row">
                            <div class="col-md-12">
                                @if($payment_request->category == 'CLIENT')
                                    @foreach($payment_request->details as $detail)
                                        <div class="col-md-6">
                                            <li> [ {{ $detail->name }} ] - {{ $detail->client->name }} </li>
                                        </div>
                                    @endforeach
                                @elseif($payment_request->category == 'SUPPLIER')
                                    <div class="row">
                                        @foreach($payment_request->details as $index=>$detail)
                                            @if($index == 0)
                                                <div class="col-md-12">
                                                    <p class="m-0">{{ $detail->supplier->name }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <li>{{ $detail->name }}
                                                        <ul>
                                                            <li>G.T &#8369; {{ number_format($detail->amount,2) }}</li>
                                                            <li>VAT: &#8369; {{ $detail->vat_amount }}</li>
                                                            <li>EWT: &#8369; {{ $detail->ewt_amount }}</li>
                                                        </ul>
                                                    </li>
                                                </div>
                                            @else
                                                <div class="col-md-6">
                                                    <li>{{ $detail->name }} [ G.T &#8369; {{ number_format($detail->amount,2) }}  ]
                                                        <ul>
                                                            <li>VAT: &#8369; {{ $detail->vat_amount }}</li>
                                                            <li>EWT: &#8369; {{ $detail->ewt_amount }}</li>
                                                        </ul>
                                                    </li>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <li> DEPARTMENT
                                        <ul>
                                            <li>{{ $payment_request->designated_department }}</li>
                                        </ul>
                                    </li>
                                @endif
                            </div>
                            <div class="col-md-12">
                                <p class="m-0 text-muted"><b>PARTIALS</b></p>
                                <hr class="mt-1 mb-1">
                            </div>
                            @if($payment_request->is_partial == true)
                                @foreach($payment_request->partials as $partial)
                                    @php
                                        $enc_partial_payment_request_id = encryptor('encrypt',$partial->id);
                                    @endphp
                                    <div class="col-md-6">
                                        <p class="m-0 text-info"><b>STATUS:</b> {{ $partial->status }}</p>
                                        <p class="m-0"><b>BANK:</b> {{ $partial->bank }}</p>
                                        <p class="m-0"><b>CHEQUE #:</b> {{ $partial->cheque_number }}</p>
                                        <p class="m-0"><b>CHEQUE DATE:</b> {{ readableDate($partial->cheque_date) }}</p>
                                        <p class="m-0"><b>AMOUNT:</b> &#8369; {{ number_format($partial->amount,2) }}</p>
                                        <p class="m-0"><b>PURPOSE:</b> {{ $partial->purpose }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



