<?php

namespace App\Http\Controllers\PurchasingSupply;

use App\Http\Controllers\Controller;
use App\Supplier;
use App\SupplierProduct;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use Cart;
use Validator;
use PDF;
use App\PurchaseOrder;
use App\PurchaseOrderDetail;
use App\Product;
use App\Quotation;
use App\QuotationProduct;
use Yajra\DataTables\Facades\DataTables;
use App\Department;
use App\PaymentRequestDetail;

class PurchaseOrderController extends Controller
{
    function showQuotationProductAdded(Request $request){
        $data = $request->all();
        $resultHtml = '';
        if( ( isset($data['qpid']) && !empty($data['qpid']) ) && isset($data['qid']) && !empty($data['qid']) ){
            $selectPurchaseQuery = PurchaseOrderDetail::with('purchaseOrder')->where('product_id','=',$data['qpid'])
                ->where('quotation_id','=',$data['qid'])
                ->whereNotNull('parent_id')
                ->where('type','=','SUPPLY')
                ->whereHas('purchaseOrder',function($q){
                    $q->whereNotIn('status',['REJECTED','CANCELLED']);
                })
                ->get();
            if($selectPurchaseQuery){
                $poQty = 0;
                $maxQty = 0;
                $quoteQty = 0;
                $pos = '';
                $selectQuotationProduct = QuotationProduct::find($data['qpid']);
                if($selectQuotationProduct){
                    $quoteQty = $selectQuotationProduct->qty;
                }
                foreach($selectPurchaseQuery as $po){
                    $poQty += $po->qty;
                    $pos .='
                        <tr>
                            <td>
                                '.$po->purchaseOrder->po_number.'
                                <hr class="m-0">
                                STATUS: <span class="badge badge-primary">'.$po->purchaseOrder->status.'</span>
                            </td>
                            <td>
                                '.number_format($po->qty).'
                            </td>
                        </tr>
                    ';
                }
                $maxQty = $quoteQty - $poQty;
                $resultHtml = '
                    <div class="col-md-12">
                        <div class="row  mt-2  text-center">
                            <div class="col-md-4 text-dark" title="QUOTATION PRODUCTS">Q.P: <b>'.$quoteQty.'</b></div>
                            <div class="col-md-4 text-success" title="PURCHASE ORDER">P.O: <b>'.$poQty.'</b></div>
                            <div class="col-md-4 text-warning" title="NEED TO P.O">NEED: <b>'.$maxQty.'</b></div>
                        </div>
                        <input type="hidden" id="max_qty" value="'.$maxQty.'">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="90%">PO #</th>
                                    <th width="10%">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                '.$pos.'
                            </tbody>
                        </table>
                    </div>
                ';
            }else{
                $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger p-1">
                        <p class="m-0 ">Unable to find details. Please try again.</p>
                    </div>
                </div>
            ';
            }
        }else{
            $resultHtml = '
                <div class="col-md-12">
                    <div class="alert alert-danger p-1">
                        <p class="m-0 ">Unable to fetch parameters. Please try again.</p>
                    </div>
                </div>
            ';
        }
        return $resultHtml;
    }
    function pdfPODetails(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['po']) && !empty($data['po'])){
            $po_number = $data['po'];
            $selectQuery = PurchaseOrder::with('supplier')
                ->with('department')
                ->with('products')
                ->with('createdBy')
                ->whereNotIn('status',['IN-PROGRESS','PENDING'])
                ->where('po_number','=',$po_number)
                ->first();
            $pdf = PDF::loadView('purchasing-supply-department.purchasing.pdf.po-details', array('data' => $selectQuery))
                ->setPaper('letter', 'portrait')
                ->stream();
            return $pdf;
        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find P.O Please try again');
        }
        return back();
    }
    function showPOdetails(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['poid']) && !empty($data['poid'])){
            $purchase_order_id = encryptor('decrypt',$data['poid']);
            $selectQuery = PurchaseOrder::with('supplier')
                ->with('department')
                ->with('products')
                ->whereNotIn('status',['IN-PROGRESS'])
                //->where('payment_status','=','FOR-REQUEST')
                ->where('id','=',$purchase_order_id)
                ->first();
            if($selectQuery){
                $selectDepartmentQuery = Department::all();
                return view('purchasing-supply-department.purchasing.details')
                    ->with('admin_menu','PURCHASING')
                    ->with('admin_sub_menu','LIST')
                    ->with('departments',$selectDepartmentQuery)
                    ->with('purchaseOrder',$selectQuery)
                    ->with('user',$user);
            }else{
                Session::flash('success',0);
                Session::flash('message','Unable to find PENDING P.O Please try again');
            }
        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find PENDING P.O Please try again');
        }
        return back();
    }
    function showUpdatePO(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['sid']) && !empty($data['sid'])){
            $supplier_id = encryptor('decrypt',$data['sid']);
            $selectQuery = Supplier::with('industry')
                ->with('cityProvince')
                ->with(array('inProgressPurchaseOrder'=>function($query){
                    $query->where('status','=','IN-PROGRESS');
                }))
                ->whereHas('inProgressPurchaseOrder',function($q){
                    $q->where('status','=','IN-PROGRESS');
                })
                ->where('id','=',$supplier_id)
                ->first();
            if($selectQuery){
                $selectDepartmentQuery = Department::all();
                return view('purchasing-supply-department.purchasing.update-p-o')
                    ->with('admin_menu','PURCHASING')
                    ->with('admin_sub_menu','LIST')
                    ->with('supplier',$selectQuery)
                    ->with('departments',$selectDepartmentQuery)
                    ->with('user',$user);
            }else{
                Session::flash('success',0);
                Session::flash('message','Unable to find supplier. Please try again');
            }
        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find supplier. Please try again');
        }
        return back();
    }
    function showIndex(){
        $user = Auth::user();
        return view('purchasing-supply-department.purchasing.index')
            ->with('admin_menu','PURCHASING')
            ->with('admin_sub_menu','LIST')
            ->with('user',$user);
    }
    function showCreatePO(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['sid']) && !empty($data['sid'])){
            /**
             * PENDING
             *  Anticipate is supplier is exist on pending p.o.
             *  If exist, redirect to p.o details to add item
             *  Create lock p.o module. In this sections, no one can add item on this p.o else if head unlock this p.o to enable add item.
             *
             * REMINDER
             * Suppliers can view all by departments ( EG. PUR-RM ( all suppliers in raw can view ) )
             */
            $supplier_id = encryptor('decrypt',$data['sid']);
            $selectQuery = Supplier::with('industry')
                ->with('cityProvince')
                ->find($supplier_id);
            if($selectQuery){
                $isExist = PurchaseOrder::where('supplier_id','=',$supplier_id)
                    ->where('status','=','IN-PROGRESS')
                    ->first();
                $selectDepartmentQuery = Department::all();
                if($isExist){
                    return redirect(route('purchasing-supply-supplier-update-p-o',['sid' => $data['sid']]));
                }else{
                    return view('purchasing-supply-department.purchasing.create-p-o')
                        ->with('admin_menu','PURCHASING')
                        ->with('admin_sub_menu','CREATE-P-O')
                        ->with('supplier',$selectQuery)
                        ->with('departments',$selectDepartmentQuery)
                        ->with('user',$user);
                }
            }else{
                Session::flash('success',0);
                Session::flash('message','Unable to find supplier. Please try again');
            }
        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find supplier. Please try again');
        }
        return back();
    }
    function showSuppliers(){
        $user = Auth::user();
        return view('purchasing-supply-department.purchasing.supplier-list')
            ->with('admin_menu','PURCHASING')
            ->with('admin_sub_menu','CREATE-P-O')
            ->with('user',$user);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'quotation-products'){
                // kailangan mai filter ito kung supply or raw, special item
                $selectQuery = QuotationProduct::with('fitout_products')->with('product')
                    ->whereIn('type',['SUPPLY','COMBINATION']) // for now supply na muna. Kapag naka designate na ito sa per department mababago to.
                    ->where('quotation_id','=',$data['quotation']);
                return Datatables::eloquent($selectQuery)
                    ->editColumn('qty', function($selectQuery) use($data){
                        $quotation_product_id = $selectQuery->id;
                        $returnValue = '';
                        $maxQty = $selectQuery->qty;
                        $pos = '';
                        if($selectQuery->type == 'SUPPLY'){
                            $selectPurchaseQuery = PurchaseOrderDetail::with('purchaseOrder')->where('product_id','=',$quotation_product_id)
                                ->where('quotation_id','=',$data['quotation'])
                                ->where('type','=','SUPPLY')
                                ->whereHas('purchaseOrder',function($q){
                                    $q->whereNotIn('status',['REJECTED','CANCELLED']);
                                })
                                ->get();
                            if($selectPurchaseQuery){
                                foreach($selectPurchaseQuery as $index=>$po){
                                    $pos .= $po->purchaseOrder->po_number;
                                    if( ( ( $index + 1 ) > 0 ) && ( ( $index + 1 ) != count($selectPurchaseQuery) ) ){
                                        $pos .= ' | ';
                                    }
                                }
                                $maxQty -= $selectPurchaseQuery->sum('qty');
                            }
                            $returnValue = $maxQty;
                            //$returnValue .= '<hr class="m-0">';
                            if($maxQty > 0){
                                $returnValue .= '<text title="PO`s: '.$pos.'" class="text-dark"> [ -'.$selectPurchaseQuery->sum('qty').' ]</text>';
                            }else{
                                $returnValue .= '<text title="PO`s: '.$pos.'" class="text-dark">[ '.$selectPurchaseQuery->sum('qty').' ]</text>';
                            }
                        }
                        return $returnValue;
                    })
                    ->editColumn('product_name', function($selectQuery) use($data){
                        $quotation_product_id = $selectQuery->id;
                        $maxQty = $selectQuery->qty;
                        // check if already added  or on process qty
                        if($selectQuery->type == 'SUPPLY'){
                            $selectPurchaseQuery = PurchaseOrderDetail::where('product_id','=',$quotation_product_id)
                                    ->where('quotation_id','=',$data['quotation'])
                                    ->where('type','=','SUPPLY')
                                    ->whereHas('purchaseOrder',function($q){
                                        $q->whereNotIn('status',['REJECTED','CANCELLED']);
                                    })
                                    ->sum('qty');
                            if($selectPurchaseQuery){
                                $maxQty -= $selectPurchaseQuery;
                            }
                        }
                        if($maxQty > 0){
                            $returnValue = '
                                <div class="custom-control custom-radio">
                                    <input  required onChange=designatedQty("'.$quotation_product_id.'","'.$maxQty.'","'.$selectQuery->type.'") value="'.$selectQuery->product_name.'" type="radio" class="custom-control-input quotation-products" name="quotation_product_name" id="quotation-product-'.$quotation_product_id.'">
                                    <label class="custom-control-label" for="quotation-product-'.$quotation_product_id.'">Qty Designated </label>
                                </div>
                            ';
                        }else{
                            $returnValue = '
                                <div class="custom-control custom-radio">
                                    <input  required onChange=$(this).prop("checked",false) value="'.$selectQuery->product_name.'" type="radio" class="custom-control-input quotation-products" name="quotation_product_name" id="quotation-product-'.$quotation_product_id.'">
                                    <label class="custom-control-label text-danger" for="quotation-product-'.$quotation_product_id.'">Already added all needed qty </label>
                                </div>
                            ';
                        }
                        $returnValue .= '<span class="badge badge-primary">'.$selectQuery->type.'</span> | '.$selectQuery->product_name;
                        // not sure sa logic na ito dapat mauna ang fitout parent para i check kung yung child is fitout group or just an item.
                        if(isset($selectQuery->fitOutParent)){
                            $returnValue .= '<hr class="m-0">';
                            $returnValue .= '<text class="text-muted small">'.$selectQuery->fitOutParent->product_name.'</text>';
                        }elseif(isset($selectQuery->product)){
                            $returnValue .= '<hr class="m-0">';
                            $returnValue .= '<text class="text-muted small">'.$selectQuery->product->product_name.'</text>';
                        }
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $returnValue = '<button type="button" class="btn btn-primary btn-sm">source</button>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);

            }
            elseif($postMode == 'approved-accounting-quotations') {
                $quotation_number = $data['q'];
                $selectQuery = Quotation::with('client')->with('userWithEmployeeDetails')
                    ->where('quote_number','LIKE','%'.$quotation_number.'%')
                    ->where('status','=','APPROVED-ACCOUNTING')
                    ->get();
                $quotations = array();
                $quotations = array();
                $destination = 'assets/img/employee/profile/';
                foreach($selectQuery as $quotation){
                    $filename = $quotation->userWithEmployeeDetails->employee->employee_num;
                    $imagePath = imagePath($destination.''.$filename,'//via.placeholder.com/400X400');
                    $quote = array();
                    $quote['id'] = $quotation->id;
                    $quote['quote_number'] = $quotation->quote_number;
                    $quote['work_nature'] = $quotation->work_nature;
                    $quote['client'] = $quotation->client->name;
                    $quote['user_image_url'] = $imagePath;
                    array_push($quotations,$quote);
                }
                return array('success' => 1, 'message' =>'Generate','data' => json_encode($quotations));
            }
            elseif($postMode == 'for-approval-po-list') {
                /**
                 * NOTE: MUST DETAILED IN WHERE CLAUSE.
                 */
                $selectQuery = PurchaseOrder::with('supplier')->with('department')
                    ->where('type','=','SUPPLY')
                    ->where('status','=','FOR-APPROVAL')
                    ->where('payment_status','=','FOR-REQUEST')
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('purchasing-supply-p-o-details',['poid' => $enc_purchase_order_id]);
                        $poPrint = route('purchasing-supply-pdf-p-o-details',['po' => $selectQuery->po_number]);
                        $returnValue = '<a class="btn btn-primary btn-xs" title="View Details" href="'.$poUpdate.'"><span class="fas fa-eye "></span></a>';
                        $returnValue .= '&nbsp;<a target="_blank" class="btn btn-warning btn-xs" title="Print Details [ Internal use ]" href="'.$poPrint.'"><span class="fas fa-print "></span></a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> By: '.$selectQuery->createdBy->username.'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'approved-po-list') {
                /**
                 * NOTE: MUST DETAILED IN WHERE CLAUSE.
                 */
                $selectQuery = PurchaseOrder::with('supplier')->with('department')
                    ->where('type','=','SUPPLY')
                    ->where('status','=','APPROVED')
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $payment_request_url = 'javascript:;';
                        $pr_number = '';
                        $paymentRequestDetailQuery = PaymentRequestDetail::with('request')
                                                                ->where('name','=',$selectQuery->po_number)
                                                                ->where('supplier_id','=',$selectQuery->supplier_id)
                                                                ->whereHas('request',function($q1){
                                                                    $q1->where('status','!=','CANCELLED');
                                                                    // hindi ko sinama ang rejected. Kase na rerevert yun.
                                                                })
                                                                ->first();
                        if($paymentRequestDetailQuery){
                            $pr_number = '[ '.$paymentRequestDetailQuery->request->pr_number.' ]';
                            $enc_payment_request_id = encryptor('encrypt',$paymentRequestDetailQuery->payment_request_id);
                            $payment_request_url = route('purchasing-supply-payment-request-update',['prid' => $enc_payment_request_id]);
                        }
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('purchasing-supply-p-o-details',['poid' => $enc_purchase_order_id]);
                        $poPrint = route('purchasing-supply-pdf-p-o-details',['po' => $selectQuery->po_number]);
                        $returnValue = '<a class="btn btn-info btn-sm btn-icon" title="View Details" href="'.$poUpdate.'"><span class="fas fa-eye "></span></a>';
                        $returnValue .= '&nbsp;<a target="_blank" class="btn btn-info btn-sm btn-icon" title="Print Details" href="'.$poPrint.'"><span class="fas fa-print "></span></a>';
                        if($selectQuery->payment_status != 'FOR-REQUEST'){
                            $returnValue .= '&nbsp;<a href="'.$payment_request_url.'" target="_blank" class="btn btn-info btn-sm btn-icon" title="Payment Request '.$pr_number.'" ><span class="fas fa-money-bill "></span></a>';
                        }
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> By: '.$selectQuery->createdBy->username.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('updated_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->updated_at,'time');
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> By: '.$selectQuery->approved_by.'</text>';
                        return $returnValue;
                    })
                    ->setRowClass(function ($selectQuery) {
                        if($selectQuery->payment_status == 'FOR-REQUEST'){
                            return 'alert-primary';
                        }elseif($selectQuery->payment_status == 'REQUESTED'){
                            return 'alert-info';
                        }
                        elseif($selectQuery->payment_status == 'PARTIAL'){
                            return 'alert-warning';
                        }
                        elseif($selectQuery->payment_status == 'COMPLETED'){
                            return 'alert-success';
                        }
                    })
                    ->setRowAttr([
                        'title' => function($selectQuery) {
                            return 'PAYMENT STATUS: '.$selectQuery->payment_status;
                        },
                    ])
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'completed-po-list') {
                /**
                 * NOTE: MUST DETAILED IN WHERE CLAUSE.
                 */
                $selectQuery = PurchaseOrder::with('supplier')->with('department')
                    ->where('type','=','SUPPLY')
                    ->where('status','=','COMPLETED')
                    ->where('payment_status','=','COMPLETED')
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('purchasing-supply-p-o-details',['poid' => $enc_purchase_order_id]);
                        $poPrint = route('purchasing-supply-pdf-p-o-details',['po' => $selectQuery->po_number]);
                        $returnValue = '<a class="btn btn-primary btn-xs" title="View Details" href="'.$poUpdate.'"><span class="fas fa-eye "></span></a>';
                        $returnValue .= '&nbsp;<a target="_blank" class="btn btn-warning btn-xs" title="Print Details [ Internal use ]" href="'.$poPrint.'"><span class="fas fa-print "></span></a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> By: '.$selectQuery->createdBy->username.'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'cancelled-po-list') {
                /**
                 * NOTE: MUST DETAILED IN WHERE CLAUSE.
                 */
                $selectQuery = PurchaseOrder::with('supplier')->with('department')
                    ->where('type','=','SUPPLY')
                    ->whereIn('status',['CANCELLED','REJECTED'])
                    //->where('payment_status','=','COMPLETED') // for review pa ito
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('purchasing-supply-p-o-details',['poid' => $enc_purchase_order_id]);
                        $poPrint = route('purchasing-supply-pdf-p-o-details',['po' => $selectQuery->po_number]);
                        $returnValue = '<a class="btn btn-primary btn-xs" title="View Details" href="'.$poUpdate.'"><span class="fas fa-eye "></span></a>';
                        $returnValue .= '&nbsp;<a target="_blank" class="btn btn-warning btn-xs" title="Print Details [ Internal use ]" href="'.$poPrint.'"><span class="fas fa-print "></span></a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> By: '.$selectQuery->createdBy->username.'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'pending-po-list') {
                /**
                 * NOTE: MUST DETAILED IN WHERE CLAUSE.
                 */
                $selectQuery = PurchaseOrder::with('supplier')->with('department')
                    ->where('type','=','SUPPLY')
                    ->where('status','=','PENDING')
                    ->where('payment_status','=','FOR-REQUEST')
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('purchasing-supply-p-o-details',['poid' => $enc_purchase_order_id]);
                        $returnValue = '<a class="btn btn-primary btn-xs" title="Update Prices" href="'.$poUpdate.'"><span class="fas fa-edit "></span></a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> By: '.$selectQuery->createdBy->username.'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'in-progress-po-list') {
                $selectQuery = PurchaseOrder::with('supplier')->with('department')
                    ->where('type','=','SUPPLY')
                    ->where('status','=','IN-PROGRESS')
                    ->where('payment_status','=','FOR-REQUEST')
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_supplier_id = encryptor('encrypt',$selectQuery->supplier_id);
                        $poUpdate = route('purchasing-supply-supplier-update-p-o',['sid' => $enc_supplier_id]);
                        $returnValue = '<a class="btn btn-primary btn-block btn-xs" title="Modify this P.O" href="'.$poUpdate.'"><span class="fas fa-edit "></span> | Modify</a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-muted"> By: '.$selectQuery->createdBy->username.'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'compute-ewt') {
                $purchase_order_id = encryptor('decrypt',$data['purchase_order_id']);
                $total_purchased = $data['total_purchased']; // total_purchased
                $vat = $data['vat'];
                $vat = filter_var($vat, FILTER_VALIDATE_BOOLEAN);
                $ewt = $data['ewt'];
                $discount = $data['discount'];
                $formulated = ewtComputation($total_purchased,$discount,$vat,$ewt);
                // always save computed ewt
                $selectQuery = PurchaseOrder::find($purchase_order_id);
                if($selectQuery){
                    $selectQuery->ewt = ewtTypes($ewt);
                    $selectQuery->ewt_amount = $formulated['ewt_amount'];
                    $selectQuery->discount = $discount;
                    $selectQuery->vat_amount = $formulated['vat'];
                    $selectQuery->grand_total = $formulated['grand_total'];
                    $selectQuery->save();
                }
                return array('success' => 1, 'message' =>'Computation done.','data' => $formulated);
            }
            elseif($postMode == 'add-product-to-po') {
                $purchase_order_id = encryptor('decrypt',$data['purchase_order_key']);
                $supplier_id = encryptor('decrypt',$data['supplier_key']);
                $supplier_product = $data['key'];
                $selectQuery = PurchaseOrder::find($purchase_order_id);
                if($selectQuery){
                    if(Cart::session('PUR-'.$supplier_id)->get($supplier_product)) {
                        $product_detail = array();
                        $total_ordered = 0;
                        $cartDetails = Cart::session('PUR-'.$supplier_id)->get($supplier_product);
                        // parent
                        $product_detail['product_id'] = $cartDetails->id; // supplier code
                        $product_detail['img'] = $cartDetails->attributes['img-data']; // supplier code
                        $product_detail['name'] = $cartDetails->name; // supplier code
                        $product_detail['price'] = $cartDetails->price;
                        $product_detail['qty'] = $cartDetails->quantity;
                        $product_detail['total_price'] = $cartDetails->quantity * $cartDetails->price;
                        $total_ordered = $product_detail['total_price'];
                        $product_detail['type'] = $cartDetails->attributes['type'];
                        $product_detail['description'] = $cartDetails->attributes['description'];
                        if($cartDetails->attributes['type'] == 'SUPPLY'){
                            $variantDescriptions = $cartDetails->attributes['variant_description'];
                            $variantDescriptions = explode('|',$variantDescriptions);
                            $htmlDescription = '';
                            foreach($variantDescriptions as $variantDescription){
                                $htmlDescription .= '<p style="margin:0px;">'.$variantDescription.'</p>';
                            }
                            $product_detail['description'] = $htmlDescription.'
                                        <p style="margin:0px;">Other Description</p>'.html_entity_decode($cartDetails->attributes['description']);
                            $product_detail['description']  = htmlentities($product_detail['description']);
                        }
                        $product_detail['details'] = $cartDetails->attributes['details'];
                        // saving items
                        DB::beginTransaction();
                        try {
                            DB::transaction(function () use ($data,$selectQuery,$supplier_id,$user,$product_detail,$total_ordered) {
                                $selectQuery->total_ordered += $total_ordered;
                                $selectQuery->save();
                                $insertParentQuery = new PurchaseOrderDetail();
                                $insertParentQuery->purchase_order_id  = $selectQuery->id;
                                $insertParentQuery->supplier_id  = $selectQuery->supplier_id;
                                $insertParentQuery->product_id  = $product_detail['product_id'];
                                $insertParentQuery->name  = $product_detail['name'];
                                $insertParentQuery->type  = $product_detail['type'];
                                $insertParentQuery->description  = $product_detail['description'];
                                $insertParentQuery->qty  = $product_detail['qty'];
                                $insertParentQuery->price  = $product_detail['price'];
                                $insertParentQuery->total_price  = $product_detail['total_price'];
                                $insertParentQuery->img = $product_detail['img'];
                                $insertParentQuery->created_at = $selectQuery->created_at;
                                $insertParentQuery->updated_at = $selectQuery->updated_at;
                                $insertParentQuery->created_by = $user->id;
                                $insertParentQuery->updated_by = $user->id;
                                $insertParentQuery->save();
                                //insert child
                                $childs = array();
                                foreach($product_detail['details'] as $details){
                                    $child = array();
                                    if($details['type'] == 'QUOTATION'){
                                        $child['product_id'] = $details['quotation_product_id'];
                                        $child['quotation_id'] = $details['quotation_id'];
                                        $child['quotation_product_name'] = $details['quotation_product_name'];
                                        $child['designated_department'] = null;
                                    }else{
                                        $child['product_id'] = $insertParentQuery->product_id;
                                        $child['quotation_id'] = null;
                                        $child['quotation_product_name'] = null;
                                        $child['designated_department'] = $details['department'];
                                    }
                                    $child['purchase_order_id'] = $selectQuery->id;
                                    $child['supplier_id'] = $insertParentQuery->supplier_id;
                                    $child['parent_id'] = $insertParentQuery->id;
                                    $child['name'] = $details['name'];
                                    $child['type'] = $insertParentQuery->type;
                                    $child['qty'] = $details['qty'];
                                    $child['remarks'] = $details['remarks'];
                                    $child['created_at'] = $insertParentQuery->created_at;
                                    $child['updated_at'] = $insertParentQuery->updated_at;
                                    $child['created_by'] = $user->id;
                                    $child['updated_by'] = $user->id;
                                    array_push($childs,$child);
                                }
                                PurchaseOrderDetail::insert($childs);
                                DB::commit();
                            });
                            // removing
                            Cart::session('PUR-'.$supplier_id)->remove($supplier_product);
                            return array('success' => 1, 'message' =>'Item Added. Page will reload');
                        }
                        catch (QueryException $exception) {
                            DB::rollback();
                            return array('success' => 0, 'message' =>$exception->errorInfo[2]);
                            return back()->withInput($data);
                        }

                    }else{
                        return array('success' => 0, 'message' =>'Unable to find details to be added. Please try again');
                    }
                }else{
                    return array('success' => 0, 'message' =>'Unable to find PO details. Please try again');
                }
            }
            elseif($postMode == 'save-p-o') {
                $attributes = [
                    'supplier_products' => 'Supplier Products',
                ];
                $rules = [
                    'supplier_products' => 'required',
                ];
                $validator = Validator::make($data,$rules,[],$attributes);
                if($validator->fails()){
                    return array('success' => 0, 'message' => implode(',',$validator->errors()->all()));
                }else{
                    $supplier_id = encryptor('decrypt',$data['supplier']);

                    $isExist = PurchaseOrder::where('supplier_id','=',$supplier_id)
                        ->where('status','=','IN-PROGRESS')
                        ->first();
                    if($isExist){
                        // get details and insert only the items
                        return array('success' => 0, 'message' =>'Someone created this supplier. Please reload the page to redirect in edit section.');
                    }else{
                        /**
                         *  Algorithm in creating new p.o in cart details.
                         *  1. Loop first the cart content based on checked row
                         *  2. Prepare array for product details with detailed quantity
                         *     In description key, if has variant, make append to description before other description content
                         *  3. insert Into PurchaseOrder
                         *       generatePoNumber function validate also if already exist in p.o
                         *  4. insert product parent
                         *  5. insert qty details.
                         *  6. Remove selected row in cart.
                         *  DB::transaction function allows to execute multiple queries. If one of them fail to execute, it returns error
                         *  and rolling back the inserted data.
                         */
                        //generate PO number and save PO and Items
                        $supplier_products = $data['supplier_products'];
                        $product_details = array();
                        $total_ordered = 0;
                        foreach($supplier_products as $supplier_product){
                            $product_detail = array();
                            if(Cart::session('PUR-'.$supplier_id)->get($supplier_product)) {
                                $cartDetails = Cart::session('PUR-'.$supplier_id)->get($supplier_product);
                                // parent
                                $product_detail['product_id'] = $cartDetails->id; // supplier code
                                $product_detail['img'] = $cartDetails->attributes['img-data']; // supplier code
                                $product_detail['name'] = $cartDetails->name; // supplier code
                                $product_detail['price'] = $cartDetails->price;
                                $product_detail['qty'] = $cartDetails->quantity;
                                $product_detail['total_price'] = $cartDetails->quantity * $cartDetails->price;
                                $total_ordered += $product_detail['total_price'];
                                $product_detail['type'] = $cartDetails->attributes['type'];
                                $product_detail['description'] = $cartDetails->attributes['description'];
                                if($cartDetails->attributes['type'] == 'SUPPLY'){
                                    $variantDescriptions = $cartDetails->attributes['variant_description'];
                                    $variantDescriptions = explode('|',$variantDescriptions);
                                    $htmlDescription = '';
                                    foreach($variantDescriptions as $variantDescription){
                                        $htmlDescription .= '<p style="margin:0px;">'.$variantDescription.'</p>';
                                    }
                                    $product_detail['description'] = $htmlDescription.'
                                        <p style="margin:0px;">Other Description</p>'.html_entity_decode($cartDetails->attributes['description']);
                                    $product_detail['description']  = htmlentities($product_detail['description']);
                                }
                                $product_detail['details'] = $cartDetails->attributes['details'];
                                array_push($product_details,$product_detail);
                            }
                        }
                        DB::beginTransaction();
                        try {
                            DB::transaction(function () use ($data,$supplier_id,$user,$product_details,$total_ordered) {
                                $selectQuery = Supplier::find($supplier_id);
                                $po_number = generatePoNumber(); // default encapse SUPPLY
                                $insertQuery = new PurchaseOrder();
                                $insertQuery->po_number = $po_number;
                                $insertQuery->payment_type = $selectQuery->payment_type;
                                $insertQuery->payment_terms = $selectQuery->payment_terms;
                                $insertQuery->supplier_id = $supplier_id;
                                $insertQuery->department_id = $user->department_id;
                                $insertQuery->total_ordered = $total_ordered;
                                $insertQuery->created_at = getDatetimeNow();
                                $insertQuery->updated_at = getDatetimeNow();
                                $insertQuery->created_by = $user->id;
                                $insertQuery->updated_by = $user->id;
                                $insertQuery->save();
                                // insert product with detailed qty
                                foreach($product_details as $product_detail){
                                    $insertParentQuery = new PurchaseOrderDetail();
                                    $insertParentQuery->purchase_order_id  = $insertQuery->id;
                                    $insertParentQuery->supplier_id  = $insertQuery->supplier_id;
                                    $insertParentQuery->product_id  = $product_detail['product_id'];
                                    $insertParentQuery->name  = $product_detail['name'];
                                    $insertParentQuery->type  = $product_detail['type'];
                                    $insertParentQuery->description  = $product_detail['description'];
                                    $insertParentQuery->qty  = $product_detail['qty'];
                                    $insertParentQuery->price  = $product_detail['price'];
                                    $insertParentQuery->total_price  = $product_detail['total_price'];
                                    $insertParentQuery->img = $product_detail['img'];
                                    $insertParentQuery->created_at = $insertQuery->created_at;
                                    $insertParentQuery->updated_at = $insertQuery->updated_at;
                                    $insertParentQuery->created_by = $user->id;
                                    $insertParentQuery->updated_by = $user->id;
                                    $insertParentQuery->save();
                                    //insert child
                                    $childs = array();
                                    foreach($product_detail['details'] as $details){
                                        $child = array();
                                        if($details['type'] == 'QUOTATION'){
                                            $child['product_id'] = $details['quotation_product_id'];
                                            $child['quotation_id'] = $details['quotation_id'];
                                            $child['quotation_product_name'] = $details['quotation_product_name'];
                                            $child['designated_department'] = null;
                                        }else{
                                            $child['product_id'] = $insertParentQuery->product_id;
                                            $child['quotation_id'] = null;
                                            $child['quotation_product_name'] = null;
                                            $child['designated_department'] = $details['department'];
                                        }
                                        $child['purchase_order_id'] = $insertQuery->id;
                                        $child['supplier_id'] = $insertParentQuery->supplier_id;
                                        $child['parent_id'] = $insertParentQuery->id;
                                        $child['name'] = $details['name'];
                                        $child['type'] = $insertParentQuery->type;
                                        $child['qty'] = $details['qty'];
                                        $child['remarks'] = $details['remarks'];
                                        $child['created_at'] = $insertParentQuery->created_at;
                                        $child['updated_at'] = $insertParentQuery->updated_at;
                                        $child['created_by'] = $user->id;
                                        $child['updated_by'] = $user->id;
                                        array_push($childs,$child);
                                    }
                                    PurchaseOrderDetail::insert($childs);
                                }
                                DB::commit();
                            });
                            // removing
                            foreach($supplier_products as $supplier_product){
                                if(Cart::session('PUR-'.$supplier_id)->get($supplier_product)) {
                                    Cart::session('PUR-'.$supplier_id)->remove($supplier_product);
                                }
                            }
                            return array('success' => 1, 'message' =>'PO Created. IN-PROGRESS Status, anyone can add more items. Page reload');
                        }
                        catch (QueryException $exception) {
                            DB::rollback();
                            return array('success' => 0, 'message' =>$exception->errorInfo[2]);
                            return back()->withInput($data);
                        }
                    }
                }
            }
            elseif($postMode == 'remove-product-to-cart') {
                $supplier_id = encryptor('decrypt',$data['supplier_key']);
                if($data['mode'] == 'CART'){
                    $cart_key = $data['key'];
                    Cart::session('PUR-'.$supplier_id)->remove($cart_key);
                    $cart = Cart::session('PUR-'.$supplier_id)->getContent();
                    $subTotal = Cart::session('PUR-'.$supplier_id)->getSubTotal();
                    return array(
                        'success' => 1,
                        'message' => 'Product remove on list.',
                        'cart_total' => $cart->count(),
                        'sub_total' => number_format($subTotal,2)
                    );
                }else{
                    $purchase_order_product_id = $data['key'];
                    $purchase_order_id = $data['po_key'];
                    $selectQuery = PurchaseOrderDetail::with('details')
                        ->where('purchase_order_id','=',$purchase_order_id)
                        ->where('supplier_id','=',$supplier_id)
                        ->where('id','=',$purchase_order_product_id)
                        ->whereNull('parent_id')
                        ->first();
                    if($selectQuery){
                        if($selectQuery->details()->forceDelete()){
                            $selectQuery->forceDelete();
                            $selectPOQuery = PurchaseOrder::with('products')->find($purchase_order_id);
                            if($selectPOQuery->products->sum('total_price') < 1){
                                /**
                                 * Kapag wala ng item sa PO automatik ma reremove na din.
                                 *
                                 */
                                $selectPOQuery->forceDelete();
                                return array('success' => 1,'is_reload' => true, 'message' => 'Product remove on list. PO # also remove.');
                            }else{
                                $selectPOQuery->total_ordered = $selectPOQuery->products->sum('total_price');
                                $selectPOQuery->updated_at = getDatetimeNow();
                                $selectPOQuery->updated_by = $user->id;
                                $selectPOQuery->save();
                                return array('success' => 1, 'message' => 'Product remove on list.');
                            }
                        }else{
                            return array('success' => 0, 'message' => 'Unable to Remove item. Please try again');
                        }
                    }else{
                        return array('success' => 0, 'message' => 'Unable to remove product. Please try again');
                    }
                }
            }
            elseif($postMode == 'add-po-product') {
                $supplier_product_id = encryptor('decrypt',$data['supplier_product']);
                $selectQuery = SupplierProduct::find($supplier_product_id);
                $enc_product_id = '';
                if($selectQuery){
                    $supplier_id = $selectQuery->supplier_id;
                    if(Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)) {
                        // already added
                        return array('success' => 0, 'message' => 'Already Added');
                    }
                    else{
                        $enc_supplier_id = encryptor('encrypt',$selectQuery->supplier_id);
                        $attributes = array();
                        $htmlData = '';
                        if($selectQuery->type == 'RAW' || $selectQuery->type == 'SPECIAL-ITEM' ){
                            $enc_product_id = encryptor('encrypt',$selectQuery->product_id);
                            $destination  = 'assets/img/products/'.$enc_product_id.'/';
                            $defaultLink = 'http://placehold.it/754x977';
                            $img = $destination.''.$enc_product_id; // save to db
                            $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                            $description = toTxtFile($destination,'description','get');
                            if($description['success'] == true){
                                $description = $description['data'];
                            }else{
                                $description = '';
                            }
                            $attributes['status'] = 'PENDING'; // kapag pending ito, walang quotation ang na i-add. Wag idisplay ang status kapag pending. Else update qty base sa total quotations qty na ni add
                            $attributes['type'] = $selectQuery->type;
                            $attributes['supplier'] = $enc_supplier_id;
                            $attributes['product_id'] = $enc_product_id;
                            $attributes['product_name'] = $selectQuery->product->product_name;
                            $attributes['base_price'] = $selectQuery->product->base_price;
                            $attributes['description'] = $description;
                            $attributes['image'] = $defaultLink;
                            $attributes['img-data'] = $img;
                            $attributes['details'] = array();

                            //html format
                            $htmlData .= '
                                <td>
                                    <img src="'.$defaultLink.'" class="img-fluid"/>
                                </td>
                                <td>
                                    <span class="badge badge-primary">'.$selectQuery->type.'</span> | '.$selectQuery->code.'
                                    <hr class="m-0">
                                    <text class="text-dark">Date Created: [ SAVE P.O FIRST ]</text>
                                    <hr class="m-0">
                                    <button  onClick=removeProduct("'.$supplier_product_id.'","remove-item-btn","CART") class="remove-item-btn btn btn-xs btn-danger pull-right mt-1"> <span class="fas fa-times"></span> |  Remove </button>
                                </td>
                                <td>
                                    '.html_entity_decode($description).'
                                </td>
                                <td>
                                    <div id="product-info-'.$supplier_product_id.'" style="display:none">
                                        <div class="form-group mb-1">
                                            <input name="supplier_key" type="hidden" value="'.$enc_supplier_id.'"/>
                                            <input name="supplier_product_key" type="hidden" value="'.$supplier_product_id.'"/>
                                            <input name="product_key" type="hidden" value="'.$enc_product_id.'"/>
                                            <label class="form-control-plaintext">Product</label>
                                            <textarea disabled class="form-control" rows="4">'.$selectQuery->type.' '.$selectQuery->product->product_name.'</textarea>
                                        </div>
                                    </div>
                                    <b class="text-dark">TOTAL: 0 </b>
                                    <button  onClick=addQtyModal("'.$supplier_product_id.'") class="btn btn-xs btn-primary float-right ">Add</button>
                                    <hr class="m-0 mt-2">
                                    <text class="text-danger">Add QTY base on quotation</text>
                                </td>
                                <td>
                                    &#8369; '.number_format($selectQuery->price).'
                                </td>
                                <td>
                                    <p><b class="">&#8369; 0.00 </b></p>
                                </td>
                            ';
                        }
                        else{
                            //combination // customized // supply
                            // $selectQuery->product_id = variant_id;
                            $selectProductQuery = Product::where('id','=',$selectQuery->product_id)
                                ->whereNotNull('parent_id')
                                ->first();
                            if($selectProductQuery){
                                $enc_product_id = encryptor('encrypt',$selectProductQuery->parent_id);
                                $destination  = 'assets/img/products/'.$enc_product_id.'/';
                                $defaultLink = 'http://placehold.it/754x977';
                                $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                                $img = $destination.''.$enc_product_id; // save to db
                                $description = toTxtFile($destination,'description','get');
                                if($description['success'] == true){
                                    $description = $description['data'];
                                }else{
                                    $description = '';
                                }
                                $attributes['status'] = 'PENDING'; // kapag pending ito, walang quotation ang na i-add. Wag idisplay ang status kapag pending. Else update qty base sa total quotations qty na ni add
                                $attributes['type'] = $selectQuery->type;
                                $attributes['supplier'] = $enc_supplier_id;
                                $attributes['product_id'] = $enc_product_id;
                                $attributes['product_name'] = $selectQuery->variant->parent->product_name;
                                $attributes['variant_description'] = $selectQuery->variant->product_name;
                                $attributes['base_price'] = $selectQuery->variant->base_price;
                                $attributes['description'] = $description;
                                $attributes['image'] = $defaultLink;
                                $attributes['img-data'] = $img;
                                $attributes['details'] = array();

                                //html format
                                $htmlData .= '
                                <td>
                                    <img src="'.$defaultLink.'" class="img-fluid"/>
                                </td>
                                <td>
                                    <span class="badge badge-primary">'.$selectQuery->type.'</span> | '.$selectQuery->code.'
                                    <hr class="m-0">
                                    <text class="text-dark">Date Created: [ SAVE P.O FIRST ]</text>
                                    <hr class="m-0">
                                    <button  onClick=removeProduct("'.$supplier_product_id.'","remove-item-btn","CART") class="remove-item-btn btn btn-xs btn-danger pull-right mt-1"> <span class="fas fa-times"></span> |  Remove </button>
                                </td>
                                <td>
                                    <p class="m-0">'.$selectQuery->variant->product_name.'</p>
                                    <hr class="m-0">
                                    '.html_entity_decode($description).'
                                </td>
                                <td>
                                    <div id="product-info-'.$supplier_product_id.'" style="display:none">
                                        <div class="form-group mb-1">
                                            <input name="supplier_key" type="hidden" value="'.$enc_supplier_id.'"/>
                                            <input name="supplier_product_key" type="hidden" value="'.$supplier_product_id.'"/>
                                            <input name="product_key" type="hidden" value="'.$enc_product_id.'"/>
                                            <label class="form-control-plaintext">Product</label>
                                            <textarea disabled class="form-control" rows="4">'.$selectQuery->type.' '.$selectQuery->variant->parent->product_name.' &#13;&#10;'.$selectQuery->variant->product_name.'</textarea>
                                        </div>
                                    </div>
                                    <b class="text-dark">TOTAL: 0 </b>
                                    <button  onClick="addQtyModal('.$supplier_product_id.')" class="btn btn-xs btn-primary float-right ">Add</button>
                                    <hr class="m-0 mt-2">
                                    <text class="text-danger">Add QTY base on quotation</text>
                                </td>
                                <td>
                                    &#8369; '.number_format($selectQuery->price).'
                                </td>
                                <td>
                                    <p><b class="">&#8369; 0.00 </b></p>
                                </td>';
                            }
                            else{
                                return array('success' => 0, 'message' => 'Unable to find product. Please try again');
                            }
                        } // end else
                        $htmlData = '<tr id="row-'.$supplier_product_id.'">'.$htmlData.'</tr>';
                        $supplierProduct = array(
                            'id' => $supplier_product_id, // standard key
                            'name' => $selectQuery->code, // supplier product code
                            'price' => $selectQuery->price, // standard key
                            'quantity' => 1, // standard key
                            'attributes' => $attributes
                        );
                        Cart::session('PUR-'.$supplier_id)->add($supplierProduct);
                        $cart = Cart::session('PUR-'.$supplier_id)->getContent();
                        $subTotal = Cart::session('PUR-'.$supplier_id)->getSubTotal();
                        return array(
                            'success' => 1,
                            'message' =>'',
                            'cart_total' => $cart->count(),
                            'sub_total' => number_format($subTotal,2),
                            'data' => $htmlData
                        );
                    }
                }else{
                    return array('success' => 0, 'message' => 'Unable to find product. Please try again');
                }
            }
            elseif($postMode == 'supplier-raw-products') {
                $supplier_id = encryptor('decrypt',$data['supplier']);
                $selectQuery = SupplierProduct::with('product')
                    ->where('supplier_id','=',$supplier_id)
                    ->whereIn('type',['RAW','SPECIAL-ITEM'])
                    ->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery){
                        $enc_supplier_product__id = encryptor('encrypt',$selectQuery->id);
                        if(Cart::session('PUR-'.$selectQuery->supplier_id)->get($selectQuery->id)) {
                            $returnValue = '<text class="text-success">Already Added</text>';
                        }else{
                            $returnValue = '<button onClick=addPOProduct("'.$enc_supplier_product__id.'","raw-add-po-product") class="raw-add-po-product btn btn-primary btn-xs">Add to P.O</button>';
                        }
                        return $returnValue;
                    })
                    ->editColumn('code', function($selectQuery) {
                        $returnValue = $selectQuery->code;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text title="ERP data" class="text-dark"><span class="badge badge-primary">'.$selectQuery->product->type.'</span>: '.$selectQuery->product->product_name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('price', function($selectQuery) {
                        $returnValue = '&#8369; '.number_format($selectQuery->price,2);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-dark">SYSTEM: &#8369; '.number_format($selectQuery->product->base_price,2).'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'supplier-supply-products') {
                $supplier_id = encryptor('decrypt',$data['supplier']);
                $selectQuery = SupplierProduct::with('variant')
                    ->with('variant.parent')
                    ->where('supplier_id','=',$supplier_id)
                    ->where('type','=','SUPPLY')
                    ->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery){
                        $enc_supplier_product__id = encryptor('encrypt',$selectQuery->id);
                        if(Cart::session('PUR-'.$selectQuery->supplier_id)->get($selectQuery->id)){
                            $returnValue = '<text class="text-success">Already Added</text>';
                        }
                        else{
                            // kaya ko siya nilagay dito para kapag may laman siya basket no need ko query
                            $isExist = PurchaseOrderDetail::where('supplier_id','=',$selectQuery->supplier_id)
                                ->where('product_id',$selectQuery->id)
                                ->whereNull('parent_id')
                                ->whereHas('purchaseOrder',function($q){
                                    $q->where('status','=','IN-PROGRESS');
                                })
                                ->first();
                            if($isExist){
                                $returnValue = '<text class="text-success">Already Added</text>';
                            }else{
                                $returnValue = '<button onClick=addPOProduct("'.$enc_supplier_product__id.'","supply-add-po-product") class="supply-add-po-product btn btn-primary btn-xs">Add to P.O</button>';
                            }
                        }
                        return $returnValue;
                    })
                    ->editColumn('code', function($selectQuery) {
                        $returnValue = $selectQuery->code;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text title="ERP data" class="text-dark"><span class="badge badge-primary">'.$selectQuery->variant->parent->type.'</span>: '.$selectQuery->variant->parent->product_name.'</text>';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text title="ERP data" class="text-dark"><span class="badge badge-primary">VARIANT</span>: '.$selectQuery->variant->product_name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('variant.parent.product_name', function($selectQuery) {
                        return $selectQuery->variant->parent->product_name;
                    })
                    ->editColumn('price', function($selectQuery) {
                        $returnValue = '&#8369; '.number_format($selectQuery->price,2);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-dark">SYSTEM: &#8369; '.number_format($selectQuery->variant->base_price,2).'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            elseif($postMode == 'supplier-list') {
                // display list by user and department id
                $selectQuery = Supplier::where('department_id','=',$user->department_id)
                    ->orderBy('created_at');
                return Datatables::eloquent($selectQuery)
                    ->addColumn('actions', function($selectQuery){
                        $enc_supplier_id = encryptor('encrypt',$selectQuery->id);
                        $returnValue = '';
                        $isExist = PurchaseOrder::where('supplier_id','=',$selectQuery->id)
                            ->where('status','=','IN-PROGRESS')
                            ->first();
                        if($isExist){
                            $poUrl = route('purchasing-supply-supplier-update-p-o', ['sid' => $enc_supplier_id]);
                            $returnValue = '<a title="UPDATE P.O" href="' . $poUrl . '" class="btn btn-icon btn-info  btn-sm waves-effect waves-themed mb-1" data-toggle="tooltip" data-placement="top" title="Update" data-original-title="UPDATE">
                                            <span class="fas fa-edit"></span>
                                        </a>&nbsp;';
                        }else{
                            if($selectQuery->supplyProducts->count() > 0 ) { // for supply supplier
                                $poUrl = route('purchasing-supply-supplier-create-p-o', ['sid' => $enc_supplier_id]);
                                $returnValue = '<a title="CREATE P.O" href="' . $poUrl . '" class="btn btn-info btn-icon  btn-sm waves-effect waves-themed mb-1" data-toggle="tooltip" data-placement="top" title="Update" data-original-title="UPDATE">
                                            <span class="fas fa-plus"></span>
                                        </a>&nbsp;';
                            }
                            else{
                                $returnValue = '<b class="text-muted">No products</b>';
                            }
                        }

                        return $returnValue;
                    })
                    ->editColumn('name', function($selectQuery) {
                        $returnValue = $selectQuery->name;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text title="'.$selectQuery->tin_number.'" class="small text-dark mb-1" style="font-size:12px;">CODE: <b>'.$selectQuery->code.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('contact_person', function($selectQuery) {
                        $returnValue = $selectQuery->contact_person;
                        $returnValue .='<hr class="m-0 mt-1">';
                        $returnValue .= '<text title="'.$selectQuery->email.'" class="small text-dark" style="font-size:12px;"><span class="fas fa-envelope"></span>: <b>'.$selectQuery->email.'</b></text>';
                        return $returnValue;
                    })
                    ->editColumn('contact_number', function($selectQuery) {
                        $contact_numbers = explode(',',$selectQuery->contact_number);
                        $returnValue = '';
                        if($contact_numbers){
                            foreach($contact_numbers as $contact_number){
                                $returnValue .='<span title="'.$contact_number.'" class="badge badge-primary">'.$contact_number.'</span> &nbsp;';
                            }
                        }
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode == 'po-update-status'){
                $purchase_order_id = encryptor('decrypt',$data['key']);
                $updateQuery = PurchaseOrder::find($purchase_order_id);;
                if($updateQuery){
                    $updateQuery->status = $data['status'];
                    $updateQuery->updated_at = getDatetimeNow();
                    $updateQuery->updated_by = $user->id;
                    if($data['status'] == 'PENDING'){
                        $updateQuery->remarks = 'REVERT TO PENDING by'.$user->username;
                    }elseif($data['status'] == 'CANCELLED'){
                        $updateQuery->remarks = 'MOVE TO CANCELLED by'.$user->username;
                    }
                    if($updateQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'PO: '.$updateQuery->po_number.' has been '.$updateQuery->status.' as of '.readableDate($updateQuery->updated_at,'time'));
                        return redirect(route('purchasing-supply-list'));
                    }
                    else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update PO details. Please try again');
                    }
                }else{
                    Session::flash('success', 0);
                    Session::flash('message', 'Unable to find PO details. Please try again');
                }
                return back();
            }
            elseif($postMode == 'update-po-payment-type'){
                $purchase_order_id = encryptor('decrypt',$data['purchase_order_key']);
                $selectQuery = PurchaseOrder::find($purchase_order_id);
                if($selectQuery){
                    $selectQuery->payment_type = $data['payment_type'];
                    $selectQuery->payment_terms = $data['payment_terms'];
                    $selectQuery->timestamps = false; // ginawa ko ito para ma retain yung date ng updated_at. APPROVED / CANCELLED need real date triggered
                    if($selectQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'Payment type updated');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update payment type. Please try again');
                    }
                }else{
                    Session::flash('success', 0);
                    Session::flash('message', 'Unable to update payment type. Please try again');
                }
                return back();
            }
            elseif($postMode == 'update-price-product'){
                $supplier_id = encryptor('decrypt',$data['supplier_key']);
                if(isset($data['purchase_order'])){
                    $purchase_order = encryptor('decrypt',$data['purchase_order']);
                    $purchase_order_product_key = $data['purchase_order_product_key'];
                    $selectPOQuery = PurchaseOrder::with('products')->find($purchase_order);
                    $product = $selectPOQuery->products->where('id','=',$purchase_order_product_key)->first();
                    if($product){
                        $product->price = $data['price'];
                        $product->total_price= $product->price * $product->qty;
                        $product->updated_at = getDatetimeNow();
                        $product->updated_by = $user->id;
                        if($product->save()){
                            $selectPOQuery->total_ordered = $selectPOQuery->products->sum('total_price');
                            $selectPOQuery->updated_at = getDatetimeNow();
                            $selectPOQuery->updated_by = $user->id;
                            $selectPOQuery->save();
                            Session::flash('success',1);
                            Session::flash('message', 'Product Price Updated');
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update price. Please try again');
                    }
                    return back();
                }else{
                    $supplier_product_id = $data['supplier_product_key'];
                    if(Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)) {
                        Cart::session('PUR-'.$supplier_id)->update($supplier_product_id,array(
                            'price' => $data['price']
                        ));
                        Session::flash('success',1);
                        Session::flash('message', 'Product Price Updated');
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update price. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'remove-qty-product'){
                $supplier_id = encryptor('decrypt',$data['supplier_key']);
                if(isset($data['purchase_order'])){
                    DB::beginTransaction();
                    try {
                        DB::transaction(function () use ($data, $supplier_id, $user) {
                            $product_detail_id = $data['detail_id'];
                            $purchase_order = $data['purchase_order'];
                            $purchase_order_product_key = $data['purchase_order_product_key'];
                            $selectQuery = PurchaseOrderDetail::where('purchase_order_id','=',$purchase_order)
                                ->where('parent_id','=',$purchase_order_product_key)
                                ->whereNotNull('parent_id')
                                ->where('id','=',$product_detail_id)
                                ->first();
                            if($selectQuery){
                                $selectQuery->forceDelete();
                                $selectPOQuery = PurchaseOrder::with('products')
                                    ->find($purchase_order);
                                $product = $selectPOQuery->products->where('id','=',$purchase_order_product_key)
                                    ->whereNull('parent_id')
                                    ->first();
                                if($product){
                                    $product->qty = $product->details->sum('qty');
                                    $product->total_price = $product->price * $product->qty;
                                    $product->updated_at = getDatetimeNow();
                                    $product->updated_by = $user->id;
                                    $product->save();

                                    $selectPOQuery->total_ordered = $selectPOQuery->products->sum('total_price');
                                    $selectPOQuery->updated_at = getDatetimeNow();
                                    $selectPOQuery->updated_by = $user->id;
                                    $selectPOQuery->save();
                                }
                            }

                            DB::commit();
                        });
                        Session::flash('success', 1);
                        Session::flash('message', 'Detailed Quantity Removed');
                    }
                    catch (QueryException $exception) {
                        DB::rollback();
                        Session::flash('success', 0);
                        Session::flash('message',$exception->errorInfo[2]);
                    }

                    return back();
                }
                else{
                    $supplier_product_id = $data['supplier_product_key'];
                    $temp_key = $data['temp_key'];
                    if(Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)) {
                        $attributes = Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)->attributes;
                        $details = Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)->attributes['details'];
                        $is_find = false; //track key
                        $sumQty = 0;
                        $newDetails = array();
                        foreach ($details as $detail){
                            if($detail['temp_key'] == $temp_key){
                                $detail['qty'] = $data['qty'];
                                $is_find = true;
                            }else{
                                $sumQty += $detail['qty'];
                                array_push($newDetails,$detail);
                            }
                        }
                        $details = $newDetails;
                        if($is_find == true){
                            $attributes['details'] = $details;
                            if(count($details) < 1){
                                $attributes['status'] = 'PENDING';
                                $sumQty = 1;
                            }
                            $update = Cart::session('PUR-'.$supplier_id)->update($supplier_product_id,array(
                                'attributes' => $attributes,
                                'quantity' => array(
                                    'relative' => false,
                                    'value' => $sumQty
                                ),
                            ));
                            Session::flash('success', 1);
                            Session::flash('message', 'Detailed Quantity Removed');
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'Unable to track data. Please try again');
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to remove qty. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'update-qty-product'){
                $supplier_id = encryptor('decrypt',$data['supplier_key']);
                if(isset($data['purchase_order'])){
                    $selectQuery = PurchaseOrderDetail::where('purchase_order_id','=',$data['purchase_order'])
                        ->where('id','=',$data['detail_id'])
                        ->where('parent_id','=',$data['purchase_order_product_key'])
                        ->first();
                    if($selectQuery){
                        $selectQuery->qty = $data['qty'];
                        $selectQuery->updated_at = getDatetimeNow();
                        $selectQuery->updated_by = $user->id;
                        if($selectQuery->save()){
                            $selectQuery = PurchaseOrderDetail::with('details')->find($data['purchase_order_product_key']);
                            $selectQuery->qty = $selectQuery->details->sum('qty');
                            $selectQuery->total_price = $selectQuery->price * $selectQuery->qty;
                            $selectQuery->updated_at = getDatetimeNow();
                            $selectQuery->updated_by = $user->id;
                            $selectQuery->save();
                            $selectPOQuery = PurchaseOrder::with('products')
                                ->find($data['purchase_order']);
                            $selectPOQuery->total_ordered = $selectPOQuery->products->sum('total_price');
                            $selectPOQuery->updated_at = getDatetimeNow();
                            $selectPOQuery->updated_by = $user->id;
                            $selectPOQuery->save();

                            Session::flash('success', 1);
                            Session::flash('message', 'Detail Quantity updated');
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'Unable to update detail quantity. Please try again');
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to find detail quantity. Please try again');
                    }
                }else{
                    $supplier_product_id = $data['supplier_product_key'];
                    $temp_key = $data['temp_key'];
                    if(Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)) {
                        $attributes = Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)->attributes;
                        $details = Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)->attributes['details'];
                        $is_find = false; //track key
                        $sumQty = 0;
                        $newDetails = array();
                        foreach ($details as $detail){
                            if($detail['temp_key'] == $temp_key){
                                $detail['qty'] = $data['qty'];
                                $is_find = true;
                            }
                            $sumQty += $detail['qty'];
                            array_push($newDetails,$detail);
                        }
                        $details = $newDetails;
                        if($is_find == true){
                            $attributes['details'] = $details;
                            $update = Cart::session('PUR-'.$supplier_id)->update($supplier_product_id,array(
                                'attributes' => $attributes,
                                'quantity' => array(
                                    'relative' => false,
                                    'value' => $sumQty
                                ),
                            ));
                            Session::flash('success', 1);
                            Session::flash('message', 'Detailed Quantity updated');
                        }else{
                            Session::flash('success', 0);
                            Session::flash('message', 'Unable to track data. Please try again');
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update qty. Please try again');
                    }
                }
                return back();
            }
            elseif($postMode == 'po-move-to-pending'){
                $purchase_order_id = encryptor('decrypt',$data['purchase_order_key']);
                $selectQuery = PurchaseOrder::find($purchase_order_id);
                if($selectQuery){
                    $selectQuery->status = 'PENDING';
                    $selectQuery->updated_at = getDatetimeNow();
                    $selectQuery->updated_by = $user->id;
                    if($selectQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'PO: '.$selectQuery->po_number.' is moved to PENDING');
                        return redirect(route('purchasing-supply-list'));
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update P.O. Please try again');
                    }
                }else{
                    Session::flash('success', 0);
                    Session::flash('message', 'Unable to find P.O. Please try again');
                }
                return back();
            }
            elseif($postMode == 'po-move-to-for-approval'){
                // notification for the approver
                $purchase_order_id = encryptor('decrypt',$data['purchase_order_key']);
                $selectQuery = PurchaseOrder::find($purchase_order_id);
                if($selectQuery){
                    $selectQuery->status = 'FOR-APPROVAL';
                    $selectQuery->updated_at = getDatetimeNow();
                    $selectQuery->updated_by = $user->id;
                    if($selectQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'PO: '.$selectQuery->po_number.' is moved to FOR APPROVAL');
                        return redirect(route('purchasing-supply-list'));
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to update P.O. Please try again');
                    }
                }else{
                    Session::flash('success', 0);
                    Session::flash('message', 'Unable to find P.O. Please try again');
                }
                return back();
            }
            elseif($postMode == 'add-qty-product'){
                $supplier_id = encryptor('decrypt',$data['supplier_key']);
                if(isset($data['purchase_order'])){
                    $purchase_order_id = encryptor('decrypt',$data['purchase_order']);
                    $purchase_order_product_key = $data['purchase_order_product_key'];
                    /**
                     *  Validation for adding details in purchase item
                     *  validate if exist type except for quotation
                     */
                    $selectQuery = PurchaseOrderDetail::where('purchase_order_id','=',$purchase_order_id)
                        ->whereNotNull('parent_id')
                        ->get();
                    if($selectQuery){
                        $name = '';
                        $is_validated = 1;
                        if($data['type'] == 'STOCKS'){
                            $name = 'FOR STOCKS';
                        }elseif($data['type'] == 'IN-HOUSE'){
                            $name = 'FOR IN-HOUSE';
                        }else{
                            // quotations  client name & quotation_id
                            $name = $data['quotation_details']; // need to finalize
                        }
                        foreach ($selectQuery as $detail){
                            if($data['type'] == 'QUOTATION'){
                                if( ( $data['quotation_key'] == $detail->quotation_id ) && $data['quotation_product_key'] == $detail->product_id ){
                                    $is_validated = 0;
                                    break;
                                }
                            }else{
                                if( $name == $detail->name ){
                                    $is_validated = 0;
                                    break;
                                }
                            }
                        }
                        if($is_validated == 0){
                            Session::flash('success', 0);
                            if($data['type'] != 'QUOTATION') {
                                Session::flash('message', 'Type: ' . $data['type'] . ' already added. Please update qty for additional.');
                            }else{
                                Session::flash('message', 'Quotation Product is already added. Please update qty for additional');
                            }
                        }else{
                            DB::beginTransaction();
                            try {
                                DB::transaction(function () use ($data, $supplier_id, $user,$name,$purchase_order_product_key,$purchase_order_id) {
                                    $insertQuery = new PurchaseOrderDetail();
                                    if($data['type'] == 'QUOTATION') {
                                        $insertQuery->quotation_id = $data['quotation_key'];
                                        $insertQuery->quotation_product_name = $data['quotation_product_name'];
                                        $insertQuery->product_id = $data['quotation_product_key'];
                                    }else{
                                        $insertQuery->designated_department = $data['department'];
                                    }
                                    $insertQuery->supplier_id = $supplier_id;
                                    $insertQuery->purchase_order_id = $purchase_order_id;
                                    $insertQuery->name = $name;
                                    $insertQuery->type = $data['purchase_order_type'];
                                    $insertQuery->parent_id = $purchase_order_product_key;
                                    $insertQuery->qty = $data['qty'];
                                    $insertQuery->remarks = $data['remarks'];
                                    $insertQuery->created_at = getDatetimeNow();
                                    $insertQuery->updated_at = getDatetimeNow();
                                    $insertQuery->created_by = $user->id;
                                    $insertQuery->updated_by = $user->id;
                                    if($insertQuery->save()){
                                        //update total quantity and total price
                                        $selectQuery = PurchaseOrderDetail::with('details')
                                            ->where('purchase_order_id',$purchase_order_id)
                                            ->where('id','=',$purchase_order_product_key)
                                            ->whereNull('parent_id')
                                            ->first();
                                        $selectQuery->qty = $selectQuery->details->sum('qty');
                                        $selectQuery->total_price = $selectQuery->price * $selectQuery->qty;
                                        $selectQuery->updated_at = getDatetimeNow();
                                        $selectQuery->updated_by = $user->id;
                                        $selectQuery->save();
                                        // update total ordered.
                                        $selectPOQuery = PurchaseOrder::with('products')
                                            ->find($purchase_order_id);
                                        $selectPOQuery->total_ordered = $selectPOQuery->products->sum('total_price');
                                        $selectPOQuery->save();
                                    }
                                    DB::commit();
                                });
                                Session::flash('success', 1);
                                Session::flash('message', 'Qty Added to item');
                            }
                            catch (QueryException $exception) {
                                DB::rollback();
                                Session::flash('success', 0);
                                Session::flash('message',$exception->errorInfo[2]);
                            }
                        }
                    }else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to find product. Please try again');
                    }
                    return back();

                }else{
                    $supplier_product_id = $data['supplier_product_key'];
                    if(Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)) {
                        /**
                         *  Validation for adding details in cart
                         *  validate if exist supplier_product_id
                         *  validate if exist type except for quotation
                         */
                        // retrieving data from existing attributes details
                        $attributes = Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)->attributes;
                        $details = Cart::session('PUR-'.$supplier_id)->get($supplier_product_id)->attributes['details'];
                        //validating if type and summation of qty
                        $sum_qty = 0;
                        $is_validated = 1;
                        foreach ($details as $detail){
                            if($data['type'] != 'QUOTATION') {
                                if ($detail['type'] == $data['type']) {
                                    $is_validated = 0;
                                    break;
                                    // hindi na tutuloy basahin ang codes downwards kase nag return na agad kapag break.
                                }
                            }else{
                                // kung quotation siya, validate ang quotation_key at quotation product id
                                if( ( $data['quotation_key'] == $detail['quotation_id'] ) && $data['quotation_product_key'] == $detail['quotation_product_id'] ){
                                    $is_validated = 0;
                                    break;
                                }
                            }
                            $sum_qty += $detail['qty'];
                        }
                        if($is_validated == false){
                            Session::flash('success', 0);
                            if($data['type'] != 'QUOTATION') {
                                Session::flash('message', 'Type: ' . $data['type'] . ' already added. Please update qty for additional.');
                            }else{
                                Session::flash('message', 'Quotation Product is already added. Please update qty for additional');
                            }
                        }else{
                            // for naming
                            $name = '';
                            if($data['type'] == 'STOCKS'){
                                $name = 'FOR STOCKS';
                            }elseif($data['type'] == 'IN-HOUSE'){
                                $name = 'FOR IN-HOUSE';
                            }else{
                                // quotations  client name & quotation_id
                                $name = $data['quotation_details']; // need to finalize
                            }
                            // -- end
                            $addDetails = array(
                                'temp_key' => strtotime(getDatetimeNow()),
                                'name' => $name,
                                'type' => $data['type'],
                                'qty' => $data['qty'],
                                'quotation_id' => '', // for QUOTATIONS TYPE
                                'quotation_product_id' =>'', // for QUOTATIONS TYPE
                                'quotation_product_name' =>'', // for QUOTATIONS TYPE
                                'department' =>'', // for QUOTATIONS TYPE
                                'remarks' =>$data['remarks']
                            );
                            if($data['type'] == 'QUOTATION') {
                                $addDetails['quotation_id'] = $data['quotation_key']; // for QUOTATIONS TYPE
                                $addDetails['quotation_product_id'] = $data['quotation_product_key']; // for QUOTATIONS TYPE
                                $addDetails['quotation_product_name'] = $data['quotation_product_name']; // for QUOTATIONS TYPE
                            }else{
                                $addDetails['department'] = $data['department'];
                            }
                            $sum_qty += $data['qty'];
                            array_push($details,$addDetails);
                            $attributes['status'] = 'ACTIVE';
                            $attributes['details'] = $details;
                            $update = Cart::session('PUR-'.$supplier_id)->update($supplier_product_id,array(
                                'attributes' => $attributes,
                                'quantity' => array(
                                    'relative' => false,
                                    'value' => $sum_qty
                                ),
                            ));
                            Session::flash('success', 1);
                            Session::flash('message', 'Qty Added to item');
                        }
                    }
                    else{
                        Session::flash('success', 0);
                        Session::flash('message', 'Unable to find item. Please try again');
                    }
                }
                return back();
            }
            else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
