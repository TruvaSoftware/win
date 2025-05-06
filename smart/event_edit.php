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
    $eid = $query->{'eid'};
    $suid = $query->{'suid'};
    
        
    $devices = DB::get(
        'SELECT id,name,type FROM devices WHERE ( sub = 0 OR sub = 8 ) AND uid = ?',
        array($m_uid)
    );
    
    

    $lamps = DB::get('select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', array($suid, "Lm"));

    foreach($lamps as $key => $dev){
        $smart_data = DB::getVar('select data from smart_det where sma = ? and smart_id = ?', array($eid, $dev->id));
        if(empty($smart_data)){ $dev->on_off = "0"; }
        else{ $dev->on_off = $smart_data; }
    }
    $swchs = DB::get('select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', array($suid, "Ws"));

    foreach($swchs as $key => $dev){
        $smart_data = DB::getVar('select data from smart_det where sma = ? and smart_id = ?', array($eid, $dev->id));
        if(empty($smart_data)){ $dev->on_off = "0"; }
        else{ $dev->on_off = $smart_data; }
    }
    $valvs = DB::get('select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', array($suid, "Vl"));

    foreach($valvs as $key => $dev){
        $smart_data = DB::getVar('select data from smart_det where sma = ? and smart_id = ?', array($eid, $dev->id));
        if(empty($smart_data)){ $dev->on_off = "0"; }
        else{ $dev->on_off = $smart_data; }
    }
    
    $smarts = array('lms' => $lamps, 'wss' => $swchs, 'vls' => $valvs);
    
    
    
    $event = DB::getRow(
        'SELECT * FROM smart WHERE id = ?',
        array($eid)
    );


    $arr1 = array('status' => 'OK','devices'=>$devices, 'smarts'=>$smarts, 'event'=> $event);
    echo json_encode($arr1);

}

/*
*   Giriş kontrolü başarısızsa.
*/
else{
    
    $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
    echo json_encode($arr1); 
    
}

?>
