<?php
/* 
*  Teknowin Alarm System
*
*  Sensor & Siren Komutları [Mobil]
*/

require_once('../src/config.php');


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
    
    $logs =  DB::get('select * from alarm_logs where uid = ? ORDER BY time DESC', array($m_uid));
    
    foreach($logs as $key => $lg){
        $logs[$key]['time'] = strtotime($lg->time);
    }
    
    $arr = array('status' => 'OK', 'datas' => $logs);
    echo json_encode($arr);

    
}

    

?>
