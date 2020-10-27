<html>    
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PO | {{ $data->po_number }}
        @if($data->status == 'FOR-APPROVAL')
            [ INTERNAL PRINT ]
        @endif
    </title>

    <style>
        @page {
            margin: 50px 25px;
        }
        
        footer .pagenum:before {
              content: counter(page);
        }
        
        header {
            position: fixed;
        }
       
        footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 15px;

            /** Extra personal styles **/
            text-align: left;
            line-height: 15px;
            font-family: Helvetica;
            font-size:11px;
        }

        #header-date{
            margin-top:-40px;
            text-align:right;
            font-family: Helvetica;
            font-size:12px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
    
</head>
<body>

    <header>
        <div><p id="header-date">{{ date('F d, Y h:m A', strtotime(getDateTimeNow())) }}</p></div>
    </header>

    <footer>
        PO {{ $data->po_number }}
        <div class="pagenum-container" align="right" style="padding-top: -15px;"><span class="pagenum"></span></div>
    </footer>

    <table border="0" style="width: 100%; font-family:Helvetica; padding-top: 10px;">
        <tr>
            <td width="37%" align="left" style="position: absolute;">
                <img src="{{  public_path().'/assets/img/pdf-format/logo.png' }}" style="width: 50px; height: 50px; padding-bottom: -10px;">
                <span style="font-size:22px; vertical-align: middle;"> | Purchase Order</span>
            </td> 
            <td width="33%" align="left" style="position: absolute;">
                <font style="font-size:15px;">PCAB Accredited Contractor</font>
            </td>
            <td width="30%" align="center" style="position: absolute;">
                <font style="font-size:15px;">www.jecams.com.ph</font>
            </td>
        </tr>
    </table>

    <table border="0" style="width: 100%; font-family:Helvetica; padding-top: 10px; padding-bottom: 10px;">
        <tr>
            <td width="39%" align="left" style="padding-right:10px; padding-bottom:-50px;position: absolute;">
                <font style="font-size:13px;">From:</font>
                <font style="font-size:10px;">
                    <br><b>JECAMS INC.</b><br>
                    Main: 3 Queen St.Forest Hills, Novaliches Quezon City 1117 <br>
                    Tel: 358.8149 / 921.1033 <br>
                </font> 
            </td> 
            <td width="31%" align="left" style="padding-bottom:-50px;position: absolute;">
                <font style="font-size:13px;">To:</font>
                <font style="font-size:10px;">
                    <br><b style="font-size: 11px;">{{ $data->supplier->name }}</b><br>
                    Address: {{ $data->supplier->complete_address }}<br>
                    Contact person: {{ $data->supplier->contact_person }}<br>
                    Email: {{ $data->supplier->email }}<br>
                    Phone: {{ $data->supplier->contact_number }}<br>
                    TIN Number: {{ $data->supplier->tin_number }}<br>
                </font> 
            </td>
            <td width="30%" align="left" style="padding-left:10px; padding-bottom:-50px;word-break: break-all;">
                <div align="center" style="height: 80px; width: 80px; border-style: dashed; margin: auto;"></div>
                <font style="font-size:10px;word-break: break-all;">
                    <b>Purchase Order:</b> {{ $data->po_number }} <br>  
                    <b>Date Created:</b> {{ readableDate($data->created_at) }} <br>
                 </font>  
            </td>
        </tr>
    </table>

    @if($data->status == 'FOR-APPROVAL')
        <div style="padding-top: 0px;">
            <p style="font-size: 17px; font-family:Helvetica; text-align: center;">Internal Use - For Approval</p>
        </div>
    @endif

    @php
        $total_purchased = 0;
        if($data->status == 'FOR-APPROVAL') {
            $page_break = "";
            $padding_top = "";
        } else {
            $page_break = "page-break";
            $padding_top = "padding-top: 0px;";
        }
    @endphp
    <table border="0" class="{{ $page_break }}" style="border-collapse:collapse; font-size:11px; font-family:Helvetica; width:100%; margin-right:1cm; margin-top: 1cm; {{ $padding_top  }}">
        <thead>
            <tr>
                <th align="left" style="font-size:12px;"><b>#</b><br/><br/></th> 
                <th align="center" style="font-size:12px;"><b>Product</b> <br/><br/></th>
                <th align="left" style="font-size:12px;"><b>Description</b><br/><br/></th>
                @if($data->status == 'FOR-APPROVAL')
                    <th align="center" style="font-size:12px;"><b>Designation</b><br/><br/></th>
                @endif
                <th align="center" style="font-size:12px;"><b>Qty</b><br/><br/></th>
                <th align="right" style="font-size:12px;"><b>List Price</b><br/><br/></th>
                <th align="right" style="font-size:12px; "> <b>Total</b><br/><br/></th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->products as $index => $po_prod)
                @php
                    $defaultLink = pdfImage($po_prod->img);
                    $total_purchased += $po_prod->total_price;
                @endphp
                <tr>
                    <td width="5%" align="left">{{ $index+1 }}</td>
                    <td @if($data->status == 'FOR-APPROVAL') width="18%" @else width="23%" @endif align="center" style="padding-bottom: 20px;">
                        <img class="img-responsive" src="{{ $defaultLink }}" width="80" height="95"><br>
                        <b>{{ $po_prod->name }}</b>
                    </td>
                    <td width="27%" align="left" style="word-wrap:break-word; padding-bottom: 20px; font-size:11px !important;">
                        {!! html_entity_decode($po_prod->description) !!}
                    </td>
                    @if($data->status == 'FOR-APPROVAL')
                        <td width="15%" align="left" style="font-size:10px !important;">
                            <ul>
                                @foreach($po_prod->details as $detail)
                                    <li>[ QTY: {{ number_format($detail->qty) }} ] {{ $detail->name }}</li>
                                @endforeach
                            </ul>
                        </td>
                    @endif
                    <td @if($data->status == 'FOR-APPROVAL') width="10%" @else width="13%" @endif align="center">{{ number_format($po_prod->qty) }}</td>
                    <td @if($data->status == 'FOR-APPROVAL') width="12%" @else width="16%" @endif align="right"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($po_prod->price,2) }}</td>
                    <td @if($data->status == 'FOR-APPROVAL') width="13%" @else width="16%" @endif align="right"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($po_prod->total_price,2) }}</td>
                </tr>
            @endforeach
        </tbody>
        @php
            $discount = $data->discount;
            $total_purchased = $data->total_ordered - $data->vat_amount;
            $vat_amount = $data->vat_amount;
            $total_amount = $data->total_ordered;
            $ewt_type = $data->ewt * 100;
            $ewt_amount =  $data->ewt_amount;
            $grand_total =  $data->grand_total;
            if($data->status == 'FOR-APPROVAL'){
                $colspan = 6;
            } else {
                $colspan = 5;
            }
        @endphp
        <tfoot>
            @if($discount != 0)
                <tr>
                    <td align="right" colspan="{{ $colspan }}"><b>Discount:</b></td>
                    <td style="text-align:right;"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($discount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td align="right" colspan="{{ $colspan }}"><b>Total Purchased:</b></td>
                <td style="text-align:right;"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($total_purchased, 2) }}</td>
            </tr>
            @if($vat_amount != 0)
                 <tr>
                    <td align="right" colspan="{{ $colspan }}"><b>12% Vat:</b></td>
                    <td style="text-align:right;"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($vat_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td align="right" colspan="{{ $colspan }}"><b>Total Amount:</b></td>
                <td style="text-align:right;"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($total_amount, 2) }}</td>
            </tr>
            @if($ewt_amount != 0)
                 <tr>
                    <td align="right" colspan="{{ $colspan }}"><b>{{ $ewt_type }}% EWT:</b></td>
                    <td style="text-align:right;"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($ewt_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td align="right" colspan="{{ $colspan }}" style="font-size: 13px; font-weight: bold;">GRAND TOTAL:</td>
                <td style="text-align:right; font-size: 13px; font-weight: bold;"> <span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($grand_total,2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($data->status == 'APPROVED')
        <div style="font-size: 11px; font-family:Helvetica; padding-top: -5px;">
            <p style="font-size:12px;"><b>Terms and Conditions</b></p>
            @php
                $po_term_destination = 'assets/files/purchase_order_terms/';
                $po_term_filename = 'terms';
                $po_terms = toTxtFile($po_term_destination,$po_term_filename,'get');
                if($po_terms['success'] === true) {
                    $datas = $po_terms['data'];
                    echo $datas;
                }
           @endphp
        </div>
    @endif
</body>
</html>