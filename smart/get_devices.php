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

    if($m_uid == $usid){
        
        $masters = DB::get(
            'SELECT id,name,type FROM devices WHERE ( sub = 0 OR sub = 8 ) AND uid = ?',
            array($usid)
        );
        
        $arr1 = array('status' => 'OK','devices'=>$masters);
        echo json_encode($arr1);
    }
    else{
        $masters = DB::get(
            'SELECT id,name,type FROM devices WHERE ( sub = 0 OR sub = 8 ) AND uid = ?',
            array($m_uid)
        );
        
        $arr1 = array('status' => 'OK','devices'=>$masters);
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
