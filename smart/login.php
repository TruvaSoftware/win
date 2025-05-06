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

    $username = $query->{'s-username'};
    $password = $query->{'s-password'};


    $db_check  = DB::getRow('select * from teknow1_user.tw_user where user = ?', array($username));

    if(empty($db_check)){

        $err = array('status' => 'NO', 'error' => 'Böyle bir kullanıcı bulunamadı.');
        echo json_encode($err);

    }elseif($db_check->status == 0){

        $err = array('status' => 'NO', 'error' => 'Böyle bir kullanıcı bulunamadı.');
        echo json_encode($err);

    }
    else{

        if(password_verify($password, $db_check->pass)){
            $suid = $db_check->id;

            if($db_check->sub!=0){
                $suid = $db_check->sub;
            }

            $arr = array('status' => 'OK', 'suid' => $suid);
            echo json_encode($arr);
        }else{
            $err = array('status' => 'NO', 'error'=>'Şifre hatalı.');
            echo json_encode($err);
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
