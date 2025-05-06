<?php
/* 
*  Teknowin Alarm System
*
*  Sensor & Siren Komutları [Mobil]
*/

require_once('../../src/config.php');


/*
*   Gelen kodu okunabilir hale getir.
*   Kaynak: src/crypFT.php
*/
$query = crypFT($_POST['q']);

/*
*   Gelen koddan Kullanıcı ID ve Giriş Kodunu al.
*/
$usid = $query->{'usid'};
$code = $query->{'code'};

/*
*   Giriş kodu, Kullanıcı Id'yi kontrol et.
*   Kaynak: src/func.php
*/
$check = check_start($usid, $code);

/*
*   Kontol Sonuçları
*   
*   Giriş kontrolü başarılı  ## startOK = 1, masterID = "xx"
*   Giriş kontrolü başarısız ## startOK = 0, masterID = ""
*/
$strOK = $check['startOK'];
$m_uid = $check['masterID'];

/*
*   Giriş kontrolü başarılıysa.
*/
if($strOK == 1){
    
$uid = $query->{'user-id'};
    
    if(!empty($uid) && !empty($m_uid)){
        $user = DB::getRow(
            'SELECT * FROM users WHERE sub = ? AND id = ?',
            array($m_uid, $uid)
        );
        $authorities = explode(',', $user->authority);
        
        $places = DB::get(
            'SELECT * FROM places WHERE sub = 0 AND uid = ?',
            array($m_uid)
        );
        foreach($places as $key => $place){
            $is_exist = array_search($place->id, $authorities);
            if(empty($is_exist)){ $auth = 0; }
            else{ $auth = 1; }
               
            $data[$key]['id'] = $place->id;
            $data[$key]['name'] = $place->name;
            $data[$key]['authority'] = $auth;

        }
        
        
        $arr1 = array('status' => 'OK', 'places'=>$data, 'name'=> $user->name.' '.$user->lastname);
        echo json_encode($arr1);
    }

}

/*
*   Giriş kontrolü başarısızsa.
*/
else{
    
    $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
    echo json_encode($arr1); 
    
}

?>
