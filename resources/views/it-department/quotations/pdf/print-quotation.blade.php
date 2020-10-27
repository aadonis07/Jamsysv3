<html>	
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>QUOTATION | {{ $data->quote_number }}</title>

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
	 	Q {{ $data->quote_number }} &nbsp; &nbsp; | &nbsp; &nbsp; Please review our Terms and Conditions at the last page.
		<div class="pagenum-container" align="right" style="padding-top: -15px;"><span class="pagenum"></span></div>
	</footer>

	<table border="0" style="width: 100%; font-family:Helvetica; padding-top: 10px;">
		<tr>
			<td width="37%" align="left" style="position: absolute;">
				<img src="{{  public_path().'/assets/img/pdf-format/logo.png' }}" style="width: 50px; height: 50px; padding-bottom: -10px;">
				<span style="font-size:22px; vertical-align: middle;"> | Quotation</span>
			</td> 
			<td width="33%" align="left" style="position: absolute;">
				<font style="font-size:15px;">PCAB Accredited Contractor</font>
			</td>
			<td width="30%" align="left" style="position: absolute;">
				<font style="font-size:15px;">www.jecams.com.ph</font>
			</td>
		</tr>
	</table>

	<table border="0" style="width: 100%; font-family:Helvetica; padding-top: 10px;">
		<tr>
			<td width="39%" align="left" style="padding-right:10px; padding-bottom:-50px;position: absolute;">
				<font style="font-size:13px;">From:</font>
				<font style="font-size:10px;">
					<br><b>JECAMS INC.</b><br>
					Main: 3 Queen St.Forest Hills, Novaliches Quezon City 1117
					BGC: 6/F, Icon Plaza, 26th St. Bonifacio Global City, Taguig City
					Makati: Ground Floor Erechem Bldg V.A Rufino Corner Salcedo St., Legaspi
					Village, Makati City<br>
					<b>Tel:</b> 358.8149 / 921.1033 <br>
					<b>Prepared By:</b> {{ $data->user->employee->first_name }} {{ $data->user->employee->last_name }}<br>
				</font> 
			</td> 
			<td width="31%" align="left" style="padding-left:10px; padding-bottom:-50px;position: absolute;">
				<font style="font-size:13px;">To:</font>
				<font style="font-size:10px;">
					<br><b style="font-size: 11px;">{{ $data->client->name }}</b><br>
					Contact person: {{ $data->client->contact_person }}<br>
					Phone: {{ $data->client->contact_numbers }}<br>
					Email: {{ $data->client->emails }}<br>
					Address: {{ $data->client->complete_address }}<br>
				</font> 
			</td>
			<td width="30%" align="left" style="padding-left:10px; padding-bottom:-50px;word-break: break-all;">
				<font style="font-size:10px;word-break: break-all;">
					<b>Quotation No.:</b> {{ $data->quote_number }} <br>  
					<b>Date Created:</b> {{ date('F d, Y h:m A', strtotime($data->created_at)) }} <br>
					<b>Valid Till:</b> {{ date('F d, Y', strtotime($data->validity_date)) }} <br>
					<b>Bill To:</b> {{ $data->billing_address }} <br>
					<b>Ship To:</b> {{ $data->shipping_address }} <br>
				 </font>  
			</td>
		</tr>
	</table>

    <table border="0" class="page-break" style="border-collapse:collapse; font-size:10px; font-family:Helvetica; width:100%; margin-right:1cm; margin-top: 1cm;">
		<tr>
			<th align="left" style="font-size:12px;"><b>#</b><br/><br/></th> 
			<th align="center" style="font-size:12px;"><b>Product</b> <br/><br/></th>
			<th align="left" style="font-size:12px;"><b>Description</b><br/><br/></th>
			<th align="center" style="font-size:12px;"><b>Qty</b><br/> <br/></th>
			<th align="right" style="font-size:12px;"><b>List Price</b><br/><br/></th>
			<th align="right" style="font-size:12px;"><b>Discount</b><br/><br/></th>
			<th align="right" style="font-size:12px; "> <b>Total</b><br/><br/></th>
		</tr>
		@foreach($data->products as $index => $quote_prod)
			@php 
				if($quote_prod->product->parent_id == NULL) {
					$enc_product_id = encryptor('encrypt',$quote_prod->product_id);
	                $destination  = 'assets/img/products/'.$enc_product_id.'/';
	                $defaultLink = pdfImage($destination.''.$enc_product_id);
				} else {
					$enc_product_id = encryptor('encrypt',$quote_prod->product->parent_id);
                    $destination  = 'assets/img/products/'.$enc_product_id.'/';
                    $defaultLink = pdfImage($destination.''.$enc_product_id);
				}
            @endphp
			<tr>
				<td width="5%" align="left">{{ $index+1 }}</td>
				<td width="20%" align="center" style="padding-bottom: 20px;">
					<img class="img-responsive" src="{{ $defaultLink }}" width="80" height="95"><br>
					<b>{{ $quote_prod->product_name }}</b>
				</td>
				<td width="25%" align="left" style="word-wrap:break-word; padding-bottom: 20px; font-size:10px !important;">
				    @php
	                    if(!empty($quote_prod->product->parent_id)){
	                        $product_variants = str_replace('|','<br>',$quote_prod->product->product_name);
	                        echo $product_variants;
	                    }
	                    if($quote_prod->type=='FIT-OUT'){
	                        foreach($quote_prod->fitout_products as $fitout){
	                            $product_variants = str_replace('<b>v:','</b><br>',$fitout->product_name);
	                            $product_variants = str_replace('|','<br>',$product_variants);
	                            echo '<b>• '.$product_variants.'</b><br>';
	                        }
	                    }
	                    echo $quote_prod->description;
	                @endphp
				</td>
				<td width="11%" align="center">{{ $quote_prod->qty }}</td>
				<td width="13%" align="right"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($quote_prod->base_price,2) }}</td> 
				<td width="13%" align="right"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($quote_prod->discount,2) }}</td> 
				<td width="13%" align="right"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($quote_prod->total_amount,2) }}</td>
			</tr>
		@endforeach
		<tr>
			<td align="right" colspan="6"><b>Subtotal:</b></td>
			<td style="text-align:right;"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($data->sub_total,2) }} </td>
		</tr>
		<tr>
			<td align="right" colspan="6"><b>Total Discount:</b></td>
			<td style="text-align:right;">
				@php
	        		$total_discount = number_format($data->total_discount,2);
					echo '<span style="font-family: DejaVu Sans;">&#8369;</span>'.$total_discount;
				@endphp
			</td>
		</tr>
		<tr>
			<td align="right" colspan="6"><b>Delivery charge:</b></td>
			<td style="text-align:right;"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($data->delivery_charge,2) }} </td>
		</tr>
		<tr>
			<td align="right" colspan="6"><b>Installation charge:</b></td>
			<td style="text-align:right;"><span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($data->installation_charge,2) }} </td>
		</tr>
		<tr>
			<td align="right" colspan="6" style="font-size: 13px; font-weight: bold;">GRAND TOTAL:</td>
			<td style="text-align:right; font-size: 13px; font-weight: bold;"> <span style="font-family: DejaVu Sans;">&#8369;</span> {{ number_format($data->grand_total,2) }} </td>
		</tr>
    </table>

	<div style="font-size: 11px; font-family:Helvetica; padding-top: -5px;">
		<p style="font-size:12px;"> <b> Terms and Conditions </b> </p>
		@php 
            $destination_terms = 'assets/files/quotation_num/';
            $filename_terms = $data->quote_number;
            $terms = toTxtFile($destination_terms,$filename_terms,'get');
            if($terms['success'] === true) {
                $datas = $terms['data'];
                $datas = json_decode($datas);
                echo $datas->terms;
            }
       @endphp
	</div>

</body>
</html>
