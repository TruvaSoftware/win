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

    $mid = $query->{'mid'};

    $events = DB::get(
        'select * from smart where uid = ?', 
        array($mid)
    );
    
    foreach($events as $event){
        $dev = DB::getRow('select name,type from devices where id = ?', array($event->did));
        $event->dev_name = $dev->name;
        $event->dev_type = $dev->type; 
        if($event->sid == 0){
            $event->sen_name = null;
            $event->sen_type = null; 

        }
        else{
            $sen = DB::getRow('select name,type from devices where id = ?', array($event->sid));
            $event->sen_name = $sen->name;
            $event->sen_type = $sen->type; 
        }
        
        $details = DB::get(
            'select * from smart_det where sma = ?', 
            array($event->id)
        );
        foreach($details as $detail){

            $s_dev = DB::getRow(
                'select adi,type from teknow1_s1.tw_userdev where id = ?', 
                array($detail->smart_id)
            );
            $detail->smd_type = $s_dev->type;
            $detail->smd_name = $s_dev->adi;
        }
        $event->smarts = $details;
    }
    
    
    $arr = array('status' => 'OK', 'events' => $events);
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
