<?php
/*---------------------------- MARLON--------------- */
function employeeAccountTypes(){ // for third party accounts credentials.
    return array(
      'SKYPE',
      'FACEBOOK',
      'GMAIL',
      'ERP',
      'YAHOO',
      'HOTMAIL',
      'MICROSOFT',
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
