<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use File;
use Validator;
use Session;
use Hash;
use Crypt;
use App\Client;
use App\Region;
use App\Province;
use App\City;
use App\CompanyBranch;
use App\Product;
use App\Swatch;

class QuotationController extends Controller
{
    public function create(){
        $work_nature = [
            'FURNITURE'=>'Furniture',
            'FITOUT'=>'Fit-out'
        ];
        $roles=[
            "GENERAL-CONTRACTOR"=>"General Contractor",
            "SUB-CON"=>"Sub-con",
            "SUPPLIER"=>"Supplier"
        ];
        $delivery_modes=[
            'PICK-UP'=>'Pick-up',
            'DELIVER'=>'Deliver'
        ];

        $user = Auth::user();
        $clients = Client::where('user_id','=',$user->id)->get();
        $regions = showRegions();

        $dateToday = date("Hymds");
        $milliseconds = round(microtime(true) * 1000);
        $newstring = substr($milliseconds, -3);
        $quote_number = $newstring . '' . $dateToday;

        $destination = 'assets/files/quotation_terms/';
        $filename = 'terms';
        $terms = toTxtFile($destination,$filename,'get');
        

        return view('it-department.quotations.create')
             ->with('admin_menu','QUOTATION')
             ->with('admin_sub_menu','CREATE-QUOTATION')
             ->with('work_nature',$work_nature)
             ->with('regions', $regions)
             ->with('roles',$roles)
             ->with('clients',$clients)
             ->with('delivery_modes',$delivery_modes)
             ->with('quote_number',$quote_number)
             ->with('terms',$terms['data']);
    }
    function showFunctions(Request $request,$postMode = null){
        $data = $request->all();
        $user = Auth::user();
        if ($request->ajax()){
            if($postMode=='get-branches'){
                $selectQuery = Client::where('id','=',$data['id'])->with('companyBranches')->with('province')->first();
                if(count($selectQuery->companyBranches)==0){
                    $enc_region_id = encryptor('encrypt', $selectQuery->province->region_id);
                    $returnArray = [
                        'client_data'=>$selectQuery,
                        'status'=>'no-branches',
                        'region_id'=>$enc_region_id
                    ];
                    return $returnArray;
                }else{
                    $returnHtml = '<option value=""></option>';
                    foreach($selectQuery->companyBranches as $branch){
                        $returnHtml .= '<option value="'.$branch->id.'">'.$branch->name.'</option>';
                    } 
                    $returnArray = [
                        'client_data'=>$returnHtml,
                        'status'=>'with-branches'
                    ];

                    return $returnArray;
                }
            }elseif($postMode=='get-address'){
                $selectProvincesQuery = Province::where([['region_id', '=', $data['region_id']], ['is_enable','=',true]])
                                ->orderBy('description', 'ASC')
                                ->get();
                $provinceContent = '';
                    foreach($selectProvincesQuery as $province) {
                        $modeProvince = '';
                        if($province->id==$data['province_id']){
                            $modeProvince = 'selected';
                        }
                        $enc_province_id = encryptor('encrypt', $province->id);
                        $provinceContent .= '<option value="'.$enc_province_id.'" '.$modeProvince.'>'.$province->description.'</option>';
                    }
                
                $selectCitiesQuery = City::where([['province_id', '=', $data['province_id']], ['is_enable','=',true]])
                                        ->orderBy('city_name', 'ASC')
                                        ->get();
                $cityContent = '';
			    	foreach($selectCitiesQuery as $city) {
                        $modeCity = '';
                        if($city->id==$data['city_id']){
                            $modeCity = 'selected';
                        }
                        $enc_city_id = encryptor('encrypt', $city->id);
			    		$cityContent .= '<option value="'.$enc_city_id.'" '.$modeCity.'>'.$city->city_name.'</option>';
                    }
                
			    $returnHtml = [
                    'province'=>$provinceContent,
                    'city'=>$cityContent
                ];

                return $returnHtml;
            }elseif($postMode=='fetch-branch-details'){
                $selectQuery = CompanyBranch::where('id','=',$data['id'])->with('province')->first();
                $enc_region_id = encryptor('encrypt', $selectQuery->province->region_id);
                $returnData = [
                    'client_data'=>$selectQuery,
                    'region'=>$enc_region_id
                ];
                return $returnData;
            }elseif($postMode=='product-list-serverside'){
                $selectQuery = Product::where('status','=','APPROVED')
                                      ->where('archive','=',false)
                                      ->whereNull('parent_id')
                                      ->orderBy('product_name','DESC');
                return Datatables::eloquent($selectQuery)
                ->addColumn('image', function($selectQuery) use($user) {
                    $returnValue = '<div align="center">';
                    $enc_product_id = encryptor('encrypt',$selectQuery->id);
                    $destination  = 'assets/img/products/'.$enc_product_id.'/';
                    $defaultLink = 'http://placehold.it/754x977';
                    $defaultLink = imagePath($destination.''.$enc_product_id,$defaultLink);
                    $returnValue .= '<img class="img-fluid text-center" id="product-preview" style="width: 56px;height:56px;" src="'.$defaultLink.'" alt="">';
                    $returnValue .= '</div>';
                    return $returnValue; 
                })
                ->addColumn('actions', function($selectQuery) use($user) {
                    $enc_id = encryptor('encrypt',$selectQuery->id);
                    $returnValue = '<div align="center">';
                    $returnValue .= '<a class="btn btn-success text-white product" data-id="'.$enc_id.'" ><span>SELECT</span></a>';
                    $returnValue .= '</div>';
                    return $returnValue; 
                })
                ->smart(true)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
            }elseif($postMode=='fetch-product-details'){
                $id = encryptor('decrypt',$data['id']);
                $selectQuery = Product::with('variants')->with('subCategoryWithCategory')->find($id);

                $variants = '';
                foreach($selectQuery->variants as $index=>$variant){
                    $variants .= '<tr>';
                        $variants .= '<td>
                        <div class="custom-control custom-radio custom-radio-rounded">
                        <input type="radio" class="custom-control-input" id="variant'.$index.'" name="variant" value="'.$variant->id.'" />
                        <label class="custom-control-label" for="variant'.$index.'">'.$variant->product_name.'<label>
                        </div></td>';
                    $variants .= '</tr>';
                }

                $variants_content = '
                            <label>Select Variant <a class="btn btn-xs btn-info text-white" id="add-variant"><span class="text-white fa fa-plus"></span> Add Variant</a></label>
                            <br>
                            <table class="table table-striped" id="dt-variant">
                                <thead class="bg-info-200 text-center">
                                    <tr>
                                        <th>Attributes & Values</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$variants.'
                                </tbody>
                             </table>
                            ';
                $defaultLink = 'http://placehold.it/754x400';
                $destination  = 'assets/img/products/'.$data['id'].'/';
                $defaultLink = imagePath($destination.''.$data['id'],$defaultLink);
                $description_data='';
                $description = toTxtFile($destination,'description','get');
                if($description['success'] == true){
                    $description_data = $description['data'];
                }
                $swatches = Swatch::where('category','=',$selectQuery->swatches)->get();
                $swatches_content = '<option value=""></option>';
                foreach($swatches as $swwatch){
                    $swatches_content .= '<option value="'.$swwatch->id.'">'.$swwatch->name.'</option>';
                }
                $returnArray = [
                    'variant'=>$variants_content,
                    'product_name'=>$selectQuery->product_name,
                    'product_img'=>$defaultLink,
                    'product_type'=>$selectQuery->type,
                    'description'=>$description_data,
                    'swatches_data'=>$swatches_content
                ];

                return $returnArray;
            }elseif($postMode=='fetch-swatch-details'){
                $swatches = Swatch::find($data['id']);
                $destination = 'assets/img/swatches/';
                $filename = encryptor('encrypt',$swatches->id);
                $defaultLink = 'http://placehold.it/400x400';
                $defaultLink = imagePath($destination.''.$filename,$defaultLink);

                $returnHtml = '<img class="img-fluid text-center" style="width: 100px;height:100px;" src="'.$defaultLink.'">';

                return $returnHtml;
            }elseif($postMode=='fetch-variant-details'){
                $selectQuery = Product::find($data['id']);
                $arrayReturn = [
                    'price'=>$selectQuery->base_price,
                    'display_price'=>number_format($selectQuery->base_price,2),
                    'product_type'=>$selectQuery->type
                ];
                return $arrayReturn;
            }else{
                return array('success' => 0, 'message' => 'Undefined Method');
            }
        }else{
            Session::flash('success', 0);
            Session::flash('message', 'Undefined method please try again');
            return back();
        }
    }
}
