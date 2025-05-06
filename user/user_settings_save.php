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
    

    $uid = $query->{'uid'};
    $name = $query->{'user-setting-name'};
    $lastname = $query->{'user-setting-lastname'};
    $cellphone = $query->{'user-setting-cellp'};
    $passwordChange = 0;

    if(empty($name)){ $err['user-setting-name'] = "Ad alanı boş bırakılamaz"; }
    if(empty($lastname)){ $err['user-setting-lastname'] = "Soyad alanı boş bırakılamaz"; }

    if(empty($cellphone)){ $err['user-setting-cellp'] = "Cep telefonu boş bırakılamaz"; }
    else if($error = validate_phone_number($cellphone)){
        $err['user-setting-cellp'] = $error;
    }

    if(empty($query->{'user-setting-password'}) && !empty($query->{'user-setting-repassword'})){ 
        $err['user-setting-password'] = "Şifre boş bırakılamaz"; 
    }
    else if(!empty($query->{'user-setting-password'}) && empty($query->{'user-setting-repassword'})){ 
        $err['user-setting-repassword'] = "Şifre yineleme boş bırakılamaz"; 
    }
    else if(empty($query->{'user-setting-password'}) && empty($query->{'user-setting-repassword'})){ 
        $passwordChange = 0;
    }
    else{
        if (strlen($query->{'user-setting-password'}) < 8) {
            $err['user-setting-password'] = "Şifre 8 karakterden az olamaz.";

        }
        else if($query->{'user-setting-password'} != $query->{'user-setting-repassword'}){
            $err['user-setting-repassword'] = "Şifreler eşleşmiyor"; 

        }
        $passwordChange = 1;

    }

    $user = DB::getRow('select * from users where id = ?', array($uid));

    if(!empty($user)){


            if(empty($err)){

                if($passwordChange==1){

                    $pass = $query->{'user-setting-password'};
                    $upp  = DB::exec('update users set name = ?, lastname = ?, cellphone = ?, password = ? where id = ?', array($name, $lastname, $cellphone, $pass, $uid));
                    $arr = array('status' => 'OK', 'pass_change'=>1);
                    echo json_encode($arr);
                }

                else if($passwordChange==0){

                    $upp  = DB::exec('update users set name = ?, lastname = ?, cellphone = ? where id = ?', array($name, $lastname, $cellphone, $uid));
                    $arr = array('status' => 'OK', 'pass_change'=>0);
                    echo json_encode($arr);

                }

            }
            else{
                $arr = array('status' => 'NO', 'error'=> $err);
                echo json_encode($arr);
            }


    }
    else{
        $arr = array('status' => 'NO', 'error' => 'Böyle bir kullanıcı bulunamadı.');

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
