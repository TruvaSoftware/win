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
        
    $place_uid = $query->{'place-uid'};
    $place_name = $query->{'place-name'};
    
    if(!empty($place_uid)){
        
        if(empty($place_name)){ $err['place-name'] = "Yer adı boş bırakılamaz"; }
        
        if(empty($err)){
              
            $ins = DB::insert(
                'INSERT INTO places (uid, sub, name) VALUES (?, ?, ?)',
                array($place_uid, 0, $place_name)
            );

            if($m_uid != $usid){
                $user_auth = DB::getVar('select authority from users where id = ?', array($usid));
                if(!empty($user_auth)){
                    $upp  = DB::exec('update users set authority = ? where id = ?', array($user_auth.','.$ins, $usid));
                }
                else{
                    $upp  = DB::exec('update users set authority = ? where id = ?', array('a,'.$ins, $usid));
                }
            }

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
