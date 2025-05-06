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
    

    $uid = $query->{'user-id'};
    $name = $query->{'u-edit-name'};
    $lastname = $query->{'u-edit-lastname'};
    //$email = $query->{'u-edit-email'};
    $cellphone = $query->{'u-edit-cellp'};
    //$username = $query->{'u-edit-username'};
    $passwordChange = 0;

    if(empty($name)){ $err['u-edit-name'] = "Ad alanı boş bırakılamaz"; }
    if(empty($lastname)){ $err['u-edit-lastname'] = "Soyad alanı boş bırakılamaz"; }

    if(empty($cellphone)){ $err['u-edit-cellp'] = "Cep telefonu boş bırakılamaz"; }
    else if($error = validate_phone_number($cellphone)){
        $err['u-edit-cellp'] = $error;

    }

    if(empty($query->{'u-edit-password'}) && !empty($query->{'u-edit-repassword'})){ 
        $err['u-edit-password'] = "Şifre boş bırakılamaz"; 
    }
    else if(!empty($query->{'u-edit-password'}) && empty($query->{'u-edit-repassword'})){ 
        $err['u-edit-repassword'] = "Şifre yineleme boş bırakılamaz-".$query->{'u-edit-password'}; 
    }
    else if(empty($query->{'u-edit-password'}) && empty($query->{'u-edit-repassword'})){ 
        $passwordChange = 0;
    }
    else{
        if (strlen($query->{'u-edit-password'}) < 8) {
            $err['u-edit-password'] = "Şifre 8 karakterden az olamaz-".$query->{'u-edit-password'};

        }
        else if($query->{'u-edit-password'} != $query->{'u-edit-repassword'}){
            $err['u-edit-repassword'] = "Şifreler eşleşmiyor.-".$query->{'u-edit-password'}; 

        }
        $passwordChange = 1;

    }

    $user = DB::getRow('select * from users where master = ? and id = ?', array($m_uid, $uid));

    if(!empty($user)){


            if(empty($err)){

                if($passwordChange==1){

                    $pass = $query->{'u-edit-password'};
                    $upp  = DB::exec('update users set name = ?, lastname = ?, cellphone = ?, password = ? where master = ? and id = ?', array($name, $lastname, $cellphone, $pass,$m_uid, $uid));
                    $arr = array('status' => 'OK');
                    echo json_encode($arr);
                }

                else if($passwordChange==0){

                    $upp  = DB::exec('update users set name = ?, lastname = ?, cellphone = ? where master = ? and id = ?', array($name, $lastname, $cellphone, $m_uid, $uid));
                    $arr = array('status' => 'OK');
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
