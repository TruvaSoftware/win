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

    if(@$_SERVER['REQUEST_METHOD']=='POST'){

        if(!empty($m_uid)){
            $did = $query->{'did'};
            $opt = $query->{'options'};
            
            if(!empty($did) && !empty($opt)){
                
                $master = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));

                sendCommand('sNotf', $master, $opt);
                $arr = array('status' => 'OK');
                echo json_encode($arr);
                
            }
            
        }
        else{
            $arr = array('status' => 'NO', 'error' => 'no-uid');
            echo json_encode($arr);
        }


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
