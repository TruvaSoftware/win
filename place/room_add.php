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
    
$room_uid = $query->{'room-uid'};
$room_sub = $query->{'room-sub'};
$room_name = $query->{'room-name'};
    
    if(!empty($room_uid) && !empty($room_sub)){
        
        if(empty($room_name)){ $err['room-name'] = "Oda adı boş bırakılamaz"; }
        
        if(empty($err)){
            $ins = DB::insert(
                'INSERT INTO places (uid, sub, name) VALUES (?, ?, ?)',
                array($room_uid, $room_sub, $room_name)
            );
            $arr1 = array('status' => 'OK');
            echo json_encode($arr1);
        }
        else{
            $arr = array('status' => 'NO', 'error'=> $err);
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
