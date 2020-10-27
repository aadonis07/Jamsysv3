<?php

namespace App\Http\Controllers\Proprietor;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use Validator;
use PDF;
use App\PurchaseOrder;
use App\PurchaseOrderDetail;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
{
    function showPOdetails(Request $request){
        $data = $request->all();
        $user = Auth::user();
        if(isset($data['poid']) && !empty($data['poid'])){
            $purchase_order_id = encryptor('decrypt',$data['poid']);
            $selectQuery = PurchaseOrder::with('supplier')
                ->with('department')
                ->with('products')
                //->whereIn('status',['FOR-APPROVAL'])
                //->where('payment_status','=','FOR-REQUEST')
                ->where('id','=',$purchase_order_id)
                ->first();
            if($selectQuery){
                return view('proprietor-department.purchasing.details')
                    ->with('admin_menu','PURCHASING')
                    ->with('admin_sub_menu','LIST')
                    ->with('purchaseOrder',$selectQuery)
                    ->with('user',$user);
            }else{
                Session::flash('success',0);
                Session::flash('message','Unable to find FOR APPROVAL P.O Please try again');
            }
        }else{
            Session::flash('success',0);
            Session::flash('message','Unable to find FOR APPROVAL P.O Please try again');
        }
        return back();
    }
    function showIndex(){
        $user = Auth::user();
        return view('proprietor-department.purchasing.index')
            ->with('admin_menu','PURCHASING')
            ->with('admin_sub_menu','LIST')
            ->with('user',$user);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode == 'for-approval-po-list') {
                /**
                 * NOTE: MUST DETAILED IN WHERE CLAUSE.
                 */
                $selectQuery = PurchaseOrder::with('supplier')->with('department')
                    ->where('status','=','FOR-APPROVAL')
                    ->where('payment_status','=','FOR-REQUEST')
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('proprietor-p-o-details',['poid' => $enc_purchase_order_id]);
                        $returnValue = '<a class="btn btn-primary btn-xs" title="View Details" href="'.$poUpdate.'"><span class="fas fa-eye "></span></a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> By: '.$selectQuery->createdBy->username.'</text>';
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
                    ->where('status','=','APPROVED')
                    ->where('payment_status','=','FOR-REQUEST')
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('proprietor-p-o-details',['poid' => $enc_purchase_order_id]);
                        $returnValue = '<a class="btn btn-primary btn-xs" title="View Details" href="'.$poUpdate.'"><span class="fas fa-eye "></span></a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> By: '.$selectQuery->createdBy->username.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('updated_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->updated_at,'time');
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> By: '.$selectQuery->approved_by.'</text>';
                        return $returnValue;
                    })
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
                    ->where('status','=','COMPLETED')
                    ->where('payment_status','=','COMPLETED')
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('proprietor-p-o-details',['poid' => $enc_purchase_order_id]);
                        $returnValue = '<a class="btn btn-primary btn-xs" title="View Details" href="'.$poUpdate.'"><span class="fas fa-eye "></span></a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> By: '.$selectQuery->createdBy->username.'</text>';
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
                    ->whereIn('status',['CANCELLED','REJECTED'])
                    //->where('payment_status','=','COMPLETED') // for review pa ito
                    ->orderBy('created_by','DESC');
                return Datatables::eloquent($selectQuery)
                    ->editColumn('grand_total', function($selectQuery){
                        $returnValue = 'G.T: &#8369; '.number_format($selectQuery->grand_total,2).'';
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> T.P: &#8369; '.number_format($selectQuery->total_ordered,2).'</text>';
                        return $returnValue;
                    })
                    ->addColumn('actions', function($selectQuery){
                        $enc_purchase_order_id = encryptor('encrypt',$selectQuery->id);
                        $poUpdate = route('proprietor-p-o-details',['poid' => $enc_purchase_order_id]);
                        $returnValue = '<a class="btn btn-primary btn-xs" title="View Details" href="'.$poUpdate.'"><span class="fas fa-eye "></span></a>';
                        return $returnValue;
                    })
                    ->editColumn('po_number', function($selectQuery) {
                        $returnValue =  '<span class="badge badge-primary">'.$selectQuery->department->code.'</span> | '.$selectQuery->po_number;
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> Supplier: [ <b>'.$selectQuery->type.'</b> ] '.$selectQuery->supplier->name.'</text>';
                        return $returnValue;
                    })
                    ->editColumn('created_at', function($selectQuery) {
                        $returnValue =  readableDate($selectQuery->created_at);
                        $returnValue .= '<hr class="m-0">';
                        $returnValue .= '<text class="text-info"> By: '.$selectQuery->createdBy->username.'</text>';
                        return $returnValue;
                    })
                    ->smart(true)
                    ->escapeColumns([])
                    ->addIndexColumn()
                    ->make(true);
            }
            else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            if($postMode == 'po-update-status'){
                $purchase_order_id = encryptor('decrypt',$data['key']);
                $updateQuery = PurchaseOrder::find($purchase_order_id);
                if($updateQuery){
                    $updateQuery->status = $data['status'];
                    $updateQuery->updated_at = getDatetimeNow();
                    $updateQuery->updated_by = $user->id;
                    $updateQuery->remarks = $data['remarks'];
                    if($data['status'] == 'APPROVED'){
                        $updateQuery->approved_by = $user->username;
                    }
                    if($updateQuery->save()){
                        Session::flash('success', 1);
                        Session::flash('message', 'PO: '.$updateQuery->po_number.' has been '.$updateQuery->status.' as of '.readableDate($updateQuery->updated_at,'time'));
                        return redirect(route('proprietor-purchasing-list'));
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
            }else{
                Session::flash('success', 0);
                Session::flash('message', 'Undefined method please try again');
                return back();
            }
        }
    }
}
