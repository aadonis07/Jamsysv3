<?php
/*---------------------------- MARLON--------------- */
function liquidationTypes(){
    return array(
      'S.I',
      'O.R',
      'A.R',
      'C.R',
      'D.R',
      //'EWT',
      'RETURN',
      'REFUND',
    );
}
function paymentRequestAmountLimit($type){
    $defaultLimit = array(
        'CASH' => 5000,
        'CHEQUE' => 100000,
        'PETTY-CASH' => 3000,

    );
    $selectQuery = App\PaymentLimit::where('type','=',$type)->latest()->first();
    if($selectQuery){
        return array(
            'amount' => $selectQuery->amount,
            'text' => 'Last Update: '.readableDate($selectQuery->created_at,'date')
        );
    }else{
        return array(
            'amount' => $defaultLimit[$type],
            'text' => 'Default Limit'
        );
    }
}
function generatePrNumber(){
    $timestamp = strtotime(getDatetimeNow());
    $pr_number = 'PR-'.$timestamp;
    $selectQuery = App\PaymentRequest::where('pr_number','=',$pr_number)->first();
    while($selectQuery){
        $timestamp = strtotime(getDatetimeNow());
        $pr_number = 'PR-'.$timestamp;
        $selectQuery = App\PaymentRequest::where('pr_number','=',$pr_number)->first();
        if(!$selectQuery){
            break;
        }
    }
    return $pr_number;
}
function paymentRequestCategory(){
    return array (
      'OFFICE' =>'FOR OFFICE [ Designated Department ]',
      'CLIENT' =>'FOR CLIENT [ with or w/out quotation ]',
      'SUPPLIER' =>'FOR SUPPLIER [ P.O`s ]',
    );
}
function commissionComputation($type,$totalContract,$requestCommission,$vat){
    /**
     * ZERO RATED = NO more vat deduction
     * With Mark up
     *   -- Commission is subject to vat and income tax
     *       Formula: Note: Ignore $totalContract
     *       $vat = true
     *           less 12% = $requestCommission[ markup ] / 1.12 x 12% ( .12 )
     *           Income Tax = $requestCommission[ markup ] / 1.12 x 5%
     *           Final Commission = $requestCommission[ markup ] - ( less 12% + Income Tax )
     *       $vat = false
     *           Income Tax = $requestCommission[ markup ] x 5%
     *           Final Commission = $requestCommission[ markup ] -  Income Tax
     *
     * For Regular Commission
     *    -- By percentage [ pwede i request na manual computation then yung figure na mismo ang ipapasok sa $requestCommission ]
     *        Formula: Note: Ignore $totalContract
     *        $vat = true
     *           less 12% = $requestCommission / 1.12 x 12%
     *           Final Commission = $$requestCommission  - less 12%
     *        $vat = false
     *            Final Commission = $requestCommission ( Confirm pa ito sa accounting )
     * CTD
     *     -- By scenario base
     *          formula: Note: $vat will be false here.
     *
     *
    **/
    $finalCommission = 0;
    $formula = '0';
    $note = 0;
    $legend = '';
    if($type == 'MARKUP'){
        if($vat == true){
            $formula = $requestCommission.' - ( ('.$requestCommission.'/ 1.12 x .12 ) + ( '.$requestCommission.' / 1.12 x .05 ) )';
            $lessVat = $requestCommission / 1.12;
            $lessVat = $lessVat * .12;
            $lessVat = round($lessVat,6);
            $incomeTax =  $requestCommission / 1.12;
            $incomeTax = $incomeTax * .05;
            $incomeTax = round($incomeTax,6);
            $totalDeduction = $lessVat + $incomeTax;
            $legend = 'Request Commission Amount : '.$requestCommission.'| VAT :12%   | Income Tax : 5%';
            $finalCommission = $requestCommission - $totalDeduction;
        }
        else{
            //false
            $formula = $requestCommission.' - ( '.$requestCommission.' x .05 )';
            $incomeTax = $requestCommission * .05;
            $totalDeduction = $incomeTax;
            $legend = 'Request Commission Amount : '.$requestCommission.' | Income Tax : 5%';
            $finalCommission = $requestCommission - $totalDeduction;
        }
    }elseif($type == 'REGULAR'){
        if($vat == true){
            $formula =  $requestCommission."-((".$requestCommission."/ 1.12) X 0.12)";
            $lessVat = $requestCommission / 1.12;
            $lessVat = $lessVat * .12;
            $lessVat = round($lessVat,6);
            $totalDeduction = $lessVat;
            $legend = 'Request Commission Amount : '.$requestCommission.'|  VAT :12%   | Income Tax : 5%';
            $finalCommission = $requestCommission - $totalDeduction;
        }else{
            $formula = $requestCommission.' [No Formula]';
            $finalCommission = $requestCommission;
        }
    }
    elseif($type == 'CTD'){
        if($vat == true){
            $formula = $requestCommission.' - ( ('.$requestCommission.'/ 1.12 x .12 ) + ( '.$requestCommission.' / 1.12 x .05 ) )';
            $lessVat = $requestCommission / 1.12;
            $lessVat = $lessVat * .12;
            $lessVat = round($lessVat,6);
            $incomeTax =  $requestCommission / 1.12;
            $incomeTax = $incomeTax * .05;
            $incomeTax = round($incomeTax,6);
            $totalDeduction = $lessVat + $incomeTax;
            $finalCommission = $requestCommission - $totalDeduction;
            $legend = 'Request Commission Amount : '.$requestCommission.'<br>  VAT :12%   | Income Tax : 5%';
        }
        else{
            //false
            $formula = $requestCommission.' - ( '.$requestCommission.' x .05 )';
            $incomeTax = $requestCommission * .05;
            $totalDeduction = $incomeTax;
            $legend = 'Request Commission Amount : '.$requestCommission.' | Income Tax : 5%';
            $finalCommission = $requestCommission - $totalDeduction;
        }
    }
    else{
        $formulationDetails = array(
            'success' => false,
            'type' => $type,
            'formula' => $formula,
            'note' => $legend,
            'final_commission' => $finalCommission,
            'vat'=>$vat
        );
    }
    $formulationDetails = array(
        'success' => false,
        'type' => $type,
        'formula' => $formula,
        'note' => $legend,
        'final_commission' => $finalCommission,
        'vat'=>$vat
    );
    return $formulationDetails;
}
function commissionTypes(){
    return array(
      'MARKUP'=>'With Mark up',
      'REGULAR' => 'For Regular Commission',
      'CTD' => 'Commission through Discount' // COMMISSION THROUGH DISCOUNT
    );
}
function ewtComputation($total_purchased,$discount,$is_vat,$ewt){
    /**
     * Sa part na ito, Vat Inc ay kasama na sa grand total
     *  wala pa yung computation ng 5 & 15 percent;
     */
    $total_amount = 0;
    $non_vat_value = $total_purchased;
    $ewt = ewtTypes($ewt);
    $vat = 0;
    if($is_vat == true){
        // for vat
       $vat = ( $total_purchased - $discount ) / 1.12;
       $non_vat_value = $vat;
       $vat = ( $total_purchased - $discount ) - $non_vat_value;
       $vat = round($vat,6);
    }else{
        $non_vat_value  = $non_vat_value - $discount;
    }

    $total_amount = ( $non_vat_value ) + $vat ; // total amount less vat value
    $total_amount = round($total_amount,6);

    $ewt_amount = $non_vat_value * $ewt;
    if($ewt > 0) {
        $ewt_amount = $non_vat_value * $ewt;
        $ewt_amount = round($ewt_amount,6);
    }
    $non_vat_value = round($non_vat_value,6);

    $grand_total = $total_amount - $ewt_amount;
    $grand_total = round($grand_total,6);
    return array(
      'total_purchased'=> $non_vat_value,
      'total_amount'=> $total_amount,
      'vat'=> $vat,
      'ewt_base'=> $ewt,
      'ewt_amount'=> $ewt_amount,
      'grand_total'=> $grand_total,
    );
}
function ewtTypes($key = null){
    // keys will be the percentage w/out sign.
    $types = array(
        0 => '0', // NONE
        1 => '0.01', // Goods
        2 => '0.02', // Services
        5 => '0.05',// professional or rent ( condition: annual fee is less than 720k in one year.)
        15 => '0.15'//  professional or rent ( condition: annual fee is greater or equal 720k in one year.)
    );
    if(!empty($key) || $key == '0'){
        if($key == '0'){
            $key = intval($key);
        }
       $types = $types[$key];
    }
    return $types;
}
function generatePoNumber($type = 'SUPP'){
    $timestamp = strtotime(getDatetimeNow());
    $po_number = 'JEC-'.$type.'-'.$timestamp;
    $selectQuery = App\PurchaseOrder::where('po_number','=',$po_number)->first();
    while($selectQuery){
        $timestamp = strtotime(getDatetimeNow());
        $po_number = 'JEC-'.$type.'-'.$timestamp;
        $selectQuery = App\PurchaseOrder::where('po_number','=',$po_number)->first();
        if(!$selectQuery){
            break;
        }
    }
    return $po_number;
}
function employeeAccountTypes(){ // for third party accounts credentials.
    return array(
      'SKYPE',
      'FACEBOOK',
      'GMAIL',
      'YAHOO',
      'HOTMAIL',
      'MICROSOFT',
    );
}
function qtyTypes(){ // for purchasing purposes
    /**
     *  NOTE MUST SPECIFY THE NOTES
     */
    return array (
        'QUOTATION', // list of pending quotation
        'STOCKS', // remarks field
        'IN-HOUSE' // remarks field
    );
}
function getMAcAddressExec(){
    $mac = exec('getmac');
    $mac = strtok($mac, ' ');
    return $mac;
}
function validateUserDepartment($department_code,$user_department_id){ // login purposes
    $selectQuery = App\Department::where('code','=',$department_code)->first();
    if($selectQuery){
        if($selectQuery->id == $user_department_id){
            return array(
                'success' => true,
                'message' => 'Validated'
            );
        }else{
            return array(
                'success' => false,
                'message' => 'Department Code not match to user`s department'
            );
        }
    }else{
        return array(
          'success' => false,
          'message' => 'Undefined Department code. Please check middleware.'
        );
    }
}
function showCities(){
    $selectQuery = App\City::where('is_enable','=',true)
        ->get();
    return $selectQuery;
}
function showProvince(){
    $selectQuery = App\Province::where('is_enable','=',true)
        ->get();
    return $selectQuery;
}
function showRegions(){
    $selectQuery = App\Region::where('is_enable','=',true)
        ->get();
    return $selectQuery;
}

function replicateImage($fromImage,$destination = 'assets/',$filename){
    $pathinfo = pathinfo($fromImage);
    $extension = $pathinfo['extension'];
    //getname from url
    try{
        $width = 306;
        $height = 397;
        // success
        $img = Image::make($fromImage);
        $img->resize($width,$height);
        if(!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0775, true);
        }
        $img->save($destination.''.$filename.'.'.$extension);
        ImageOptimizer::optimize($destination.$filename.'.'.$extension);
        //copy($fromImage, $destination.''.$filename.'.'.$extension);
        return array(
            'success' => true,
            'data' => $destination.''.$filename.'.'.$extension
        );
    }
    catch(\Exception $e){
        return array(
            'success' => false,
            'data' => null
            //'message' => $e
        );
    }
}
function savePointKey(){
    $combo = array(
        getClientIP(),strtotime(getDatetimeNow()),
    );
    return encryptor('encrypt',json_encode($combo));
}
function cart(){
    $cart = Cart::getContent();
    return $cart;
}
function readableDate($datetime,$mode = 'default')
{
    if($mode == 'default'){
        return date("M d, Y", strtotime($datetime));
    }else{
        return date("D, d M Y h:i A", strtotime($datetime));
    }
}

function toTxtFile($destination,$filename,$mode,$content=null){
    $response = array(
        'success' =>false,
        'data' => ''
    );
    if($mode == 'put') {
        createDir($destination);
        $path = $destination . $filename . '.txt';
        File::put($path, $content);
        $response = array(
            'success' =>true,
            'data' => $content
        );
    }
    elseif($mode == 'get'){
        $filename = $filename.'.txt';
        $path = $destination.''.$filename;
        $content = '';
        if(file_exists($path)){
            $content = File::get($path);
            $response = array(
                'success' =>true,
                'data' => $content
            );
        }else{
            $response = array(
                'success' =>false,
                'data' => $content
            );
        }
    }
    elseif($mode == 'local-get'){
        $exists = Storage::disk('public')->exists($destination.'/'.$filename);
        if($exists === true)
        {
            $content = Storage::disk('public')->get($destination.'/'.$filename);
            $response = array(
                'success' =>true,
                'data' => $content
            );
        }
    }
    elseif($mode == 'local-put'){
        $resultCreateFile = Storage::disk('public')->put($destination.'/'.$filename,$content);
        $response = array(
            'success' =>$resultCreateFile,
            'data' => $content
        );
    }
    return $response;
}
function createDir($destination){
    if(!File::isDirectory($destination)) {
        File::makeDirectory($destination, 0775, true);
    }
}
function sendEmails($data,$sourceEmail,$header,$cc =array())
{
    $receiver = $data['receiver'];
    $name = $data['name'];
    $subject = $data['subject'];
    $response = array();
    Mail::send($data['template'],$data, function ($message)
    use ($receiver,$name,$subject,$sourceEmail,$header,$cc)
    {
        $message->from($sourceEmail,$header);
        if(count($cc) > 0){
            $message->cc($cc);
        }
        $message->to($receiver, $name)->subject($subject);
    });
    if(count(Mail::failures()) > 0){
        $response  = array(
            'success' =>false,
            'data' => Mail::failures(),
        );
    }
    else
    {
        $response  = array(
            'success' =>true,
        );
    }
    return $response;
}
function strToTitle($text){
    return ucwords(mb_strtolower($text));
}
function isExistFile($filepath){
    $path = $filepath;
    $isExist = false;
    if(file_exists($path.'.png'))
    {
        $isExist = true;
        $path =$path.'.png';
    }
    else if(file_exists($path.'.jpg'))
    {
        $isExist = true;
        $path =$path.'.jpg';
    }
    else if(file_exists($path.'.jpg'))
    {
        $isExist = true;
        $path =$path.'.jpg';
    }
    else if(file_exists($path.'.txt'))
    {
        $isExist = true;
        $path =$path.'.txt';
    }
    else if(file_exists($path.'.jpeg'))
    {
        $isExist = true;
        $path =$path.'.jpeg';
    }
    return $response = array(
        'is_exist' =>$isExist,
        'path' => $path
    );
}
function isSelected($text1,$text2,$returnText = null){
    $response = 'selected';
    if($returnText != null){
        $response = $returnText;
    }
    if($text1 == $text2){
        return $response;
    }
}
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function filePath($path){ // with filename
    $bool = false;
    if(file_exists($path.'.pdf')){
        $bool = true;
        $path = asset($path.'.pdf');
    }
    else if(file_exists($path.'.docx')){
        $bool = true;
        $path = asset($path.'.docx');
    }
    else if(file_exists($path.'.doc')){
        $bool = true;
        $path = asset($path.'.doc');
    }
    return $response = array(
        'is_exist' =>$bool,
        'path' => $path
    );
}
function imagePath($path,$baseUrl)
{
    if(file_exists($path.'.png'))
    {
        return asset($path.'.png');
    }
    else if(file_exists($path.'.svg'))
    {
        return asset($path.'.svg');
    }
    else if(file_exists($path.'.jpg'))
    {
        return asset($path.'.jpg');
    }
    else if(file_exists($path.'.jpeg'))
    {
        return asset($path.'.jpeg');
    }
    else{
        return $baseUrl;
    }
}
function pdfImage($path)
{
    if(file_exists($path.'.png'))
    {
        return public_path().'/'.$path.'.png';
    }
    else if(file_exists($path.'.svg'))
    {
        return public_path().'/'.$path.'.svg';
    }
    else if(file_exists($path.'.jpg'))
    {
        return public_path().'/'.$path.'.jpg';
    }
    else if(file_exists($path.'.jpeg'))
    {
        return public_path().'/'.$path.'.jpeg';
    }
}
function fileStorageUpload($file,$destination,$file_name,$mode,$width,$height){
    try{
        $extension = strtolower($file->getClientOriginalExtension());
        $img = Image::make($file);
        if($mode == 'resize'){
            $img->resize($width,$height);
        }
        if(!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0775, true);
        }
        $img->save($destination.$file_name.'.'.$extension);
        ImageOptimizer::optimize($destination.$file_name.'.'.$extension);
        return true;
    }
    catch(\Exception $e){
        return 'err'.$e;
    }
}
function getImages($path)
{
    $dir = $path;
    $listFiles=[];
    if (file_exists($dir)) {
        $files = \File::files($dir);
        foreach($files as $path)
        {

            $listFiles[] = pathinfo($path);
        }
    }
    return $listFiles;
}
function isMobileDev(){
    if(isset($_SERVER['HTTP_USER_AGENT']) and !empty($_SERVER['HTTP_USER_AGENT'])){
        $user_ag = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis',$user_ag)){
            return true;
        }else{
            return false;
        };
    }else{
        return false;
    };
};
function insertSwatchType($mode = null,$array = array()){
    $response = array();
    if($mode == 'bulk'){
        $insertQuery  = App\SwatchGroup::insert($array);
        $response = array(
            'success' =>$insertQuery,
            'data' => '',
        );
    }else{
        $exceptInsert = array();
        $insertQuery = new \App\SwatchGroup();
        foreach($array as $key =>$swatchInfo){
            $column_name  = $key;
            $value = $swatchInfo;
            if(!in_array($column_name, $exceptInsert)){
                $insertQuery->$column_name = trim($value);
            }
        }
        $resultInsert = $insertQuery->save();
        $response = array(
            'success' =>$resultInsert,
            'data' => $insertQuery,
        );
    }
    return $response;
}
function createTextFile($destination,$filename,$content)
{
    $resultCreateFile = Storage::disk('public')->put($destination.'/'.$filename,$content);
    return $resultCreateFile;
}

function dummyAccountFile(){
    $randstr = str_random(4);
    $randint=  mt_rand(100000, 999999);
    return $randstr.''.$randint;
}
function getDatetimeNow(){
    $datetime = date("Y-m-d H:i:s");
    $dt = new DateTime($datetime);
    $tz = new DateTimeZone('Asia/Manila');
    $dt->setTimezone($tz);
    return $dt->format('Y-m-d H:i:s');
}
function getDateNow() {
    $datetime = date("Y-m-d H:i:s");
    $dt = new DateTime($datetime);
    $tz = new DateTimeZone('Asia/Manila');
    $dt->setTimezone($tz);
    return $dt->format('Y-m-d');
}
function getYearNow() {
    date_default_timezone_set('Asia/Manila');
    $datetime = date("Y");
    return $datetime;
}
function generate_combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0){
    $keys = array_keys($data);
    if (isset($value) === true){
        array_push($group, $value);
    }
    if ($i >= count($data)) {
        array_push($all, $group);
    } else{
        $currentKey     = $keys[$i];
        $currentElement = $data[$currentKey];
        foreach ($currentElement as $index=>$val) {
            generate_combinations($data, $all, $group, $val, $i + 1);
        }
    }
    return $all;
}
function encryptor($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'J@msyst3m-3rp@2020';
    $secret_iv = 'j3c@ms3rp1nc2018';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}
function titlecase($text)
{
    return ucwords(strtolower($text));
}
function AdminUser()
{
    $selectQuery =  Auth::user();
    return $selectQuery;
}
function checkloginAuthentication(){ // user authentication
    if (Auth::check()){
        $indicator = Auth::user()->indicator;
        return $indicator;
    }else{
        return 'false';
    }
}
function getAllImages($path)
{
    $dir = $path;
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $listFiles=[];
    $files = \File::files($dir);
    foreach($files as $path)
    {

        $listFiles[] = pathinfo($path);
    }

    return $listFiles;
}
function swatchesCategory(){
    $categories = [
        'FABRIC'=>'Fabric',
        'LAMINATES'=>'Laminates',
        'WINDOW BLINDS'=>'Window Blinds',
        'LEATHERRETTE'=>'Leatherette',
        'EDGEBAND'=>'Edgeband',
        'CARPET'=>'Carpet',
        'GRANITE'=>'Granite',
        'PARTITION FABRIC'=>'Partition Fabric',
        'HARDWOOD'=>'Hardwood',
        'DAMANTEX'=>'Damatex'
    ];

    return $categories;
}

function Branches($id){
    $selectQuery =  App\CompanyBranch::where('client_id','=',$id)->get();
    $option = '<option value=""></option>';
    foreach($selectQuery as $branch){
        $option .= '<option value="'.$branch->id.'">'.$branch->name.'</option>';
    }
    return $option;
}

function Client($id,$status,$temp_id){
    if($status=='client'){
        $selectQuery = App\Client::with('province')->where('id','=',$id)->first();
    }
    if($status=='branch'){
        $selectQuery = App\CompanyBranch::with('province')->where('id','=',$id)->first();
    }
    if($status=='province'){
        $provinceQuery = App\Province::where('region_id','=',$id)->get();
        $selectQuery = '';
        foreach($provinceQuery as $province){
            $mode = '';
            if($province->id==$temp_id){
                $mode='selected';
            }
            $selectQuery .= '<option value="'.encryptor('encrypt',$province->id).'" data-charge="'.$province->delivery_charge.'" '.$mode.'>'.$province->description.'</option>';
        }
    }
    if($status=='city'){
        $cityQuery = App\City::where('province_id','=',$id)->get();
        $selectQuery = '';
        foreach($cityQuery as $city){
            $cmode = '';
            if($city->id==$temp_id){
                $cmode='selected';
            }
            $selectQuery .= '<option value="'.encryptor('encrypt',$city->id).'" data-charge="'.$city->delivery_charge.'" '.$cmode.'>'.$city->city_name.'</option>';
        }
    }
    if($status=='barangay'){
        $selectClient = App\Client::find($temp_id);
        $barangayQuery = App\Barangay::where('city_id','=',$id)->get();
        $selectQuery = '';
        foreach($barangayQuery as $barangay){
            $bmode = '';
            if(!empty($selectClient->barangay_id)){
                if($barangay->id==$selectClient->barangay_id){
                    $bmode='selected';
                }
            }
            $selectQuery .= '<option value="'.encryptor('encrypt',$barangay->id).'" data-charge="'.$barangay->additional_charge.'" '.$bmode.'>'.$barangay->barangay_description.'</option>';
        }
    }
    if($status=='barangay_update'){
        $barangayQuery = App\Barangay::where('city_id','=',$id)->get();
        $selectQuery = '';
        foreach($barangayQuery as $barangay){
            $bmode = '';
            if($barangay->id==$temp_id){
                $bmode='selected';
            }
            $selectQuery .= '<option value="'.encryptor('encrypt',$barangay->id).'" data-charge="'.$barangay->additional_charge.'" '.$bmode.'>'.$barangay->barangay_description.'</option>';
        }
    }
    if($status=='barangay_query'){
        $dec_id = encryptor('decrypt',$id);
        $barangayQuery = App\Barangay::find($dec_id);

        return $barangayQuery;
    }
    if($status=='everything'){
        $cityQuery = App\City::where('id','=',$id)->with('region')->with('province')->first();
        $selectQuery = $cityQuery;
    }
    return $selectQuery;
}
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
function quotationProductCount($id){
    $countQuery = App\QuotationProduct::where('quotation_id','=',$id)->whereNull('cancelled_date')->whereNull('parent_id')->count(); 

    return $countQuery;
}
function jrProductCount($id){
    $countQuery = App\QuotationProduct::select('quotation_id')->where('quotation_id','=',$id)
                                        ->whereNull('cancelled_date')
                                        ->whereIn('type',['COMBINATION','FIT-OUT','CUSTOMIZED'])
                                        ->count();
    return $countQuery;
}
function countStatusQuotation($class,$status){
    $user =  Auth::user();
    if($user->position_id==7){
        $countQuery = App\Quotation::where('status','=',$status)
        ->where('user_id','=',$user->id)
        ->count();
    }elseif($user->position_id==8){
        $countQuery = App\Quotation::where('status','=',$status)
        ->where('team_id','=',$user->team_id)
        ->count();
    }else{
        $countQuery = App\Quotation::where('status','=',$status)
        ->count();
    }
    

    $returnHtml = '';
    if($countQuery!=0){
        $returnHtml = '<span class="badge bg-'.$class.'-500 ml-2">'.$countQuery.'</span>';
    }
    return $returnHtml;
}
function fetchProduct($id){
    $selectQuery = App\Product::select('id','product_name','parent_id')->find($id);

    return $selectQuery;
}
function temporaryQuotationTotal($id,$total_discount,$installation_fee,$delivery_fee){
    $selectQuery = App\Quotation::with('update_products')->find($id);
    $sub_total = 0;
    $grand_total = 0;
    $total_product_discount = 0;
    foreach($selectQuery->update_products as $product){
        $sub_total = floatval($sub_total)+floatval($product->total_price);
        $total_product_discount = floatval($total_product_discount)+floatval($product->discount);
    }
    $dicount_data = floatval($total_discount) + floatval($total_product_discount);
    $grand_total = floatval($sub_total)+floatval($installation_fee)+floatval($delivery_fee)-floatval($dicount_data);
    $returnArray = array(
        'sub_total'=>$sub_total,
        'grand_total'=>$grand_total,
        'total_product_discount'=>$total_product_discount
    );
    return $returnArray;
}
function jobRequestCount($id){
    $selectQuery = App\JobRequestProduct::where('job_request_id','=',$id)->whereIn('status',['PENDING','ACCOMPLISHED','ONGOING','FOR-PRODUCTION','ON-HOLD'])->count();
    return $selectQuery;
}
function checkRedundant($id,$quotation_id){
    $selectQuery = App\QuotationProduct::where('quotation_id','=',$quotation_id)->where('product_id','=',$id)->whereNull('cancelled_date')->where('remarks','!=','DELETED')->count();

    return $selectQuery;
}
function countJrProducts($id,$type){
    $selectQuery = App\JobRequestProduct::where('job_request_id','=',$id)->where('type','=',$type)->count();

    return $selectQuery;
}



