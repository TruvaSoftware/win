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

    $suid = $query->{'s-uid'};

    $lamps = DB::get(
        'select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', 
        array($suid, "Lm")
    );

    
    $switchs = DB::get(
        'select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', 
        array($suid, "Ws")
    );

    
    $valves = DB::get(
        'select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', 
        array($suid, "Vl")
    );

    
    $arr = array('status' => 'OK', 'lamps' => $lamps, 'switchs' => $switchs, 'valves' => $valves);
    echo json_encode($arr);

}

/*
*   Giriş kontrolü başarısızsa.
*/
else{
    
    $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
    echo json_encode($arr1); 
    
}

?>
