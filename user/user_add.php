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
            
            $sub        = $m_uid;
            $name       = $query->{'reg-name'};
            $lastname   = $query->{'reg-lastname'};
            $email      = $query->{'reg-email'};
            $cellp      = $query->{'reg-cellp'};
            $username   = $query->{'reg-username'};
            $password   = $query->{'reg-password'};
            $repassword   = $query->{'reg-repassword'};
            $firm_key   = $query->{'reg-firm-key'};

            // Geçici
            if(empty($firm_key)){ $firm_key = "tekron"; }

            if(empty($name)){ $err['reg-name'] = "Ad alanı boş bırakılamaz"; }
            if(empty($lastname)){ $err['reg-lastname'] = "Soyad alanı boş bırakılamaz"; }
            
            if(empty($email)){ $err['reg-email'] = "E-Posta adresi boş bırakılamaz"; }
            else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              $err['reg-email'] = "E-Posta adresi hatalı.";
            }
            else if(check_mail_exist($email)){
              $err['reg-email'] = "Bu E-Posta adresi sistemde kayıtlı.";
            }
            
            if(empty($cellp)){ $err['reg-cellp'] = "Cep telefonu boş bırakılamaz"; }
            else if($error = validate_phone_number($cellp)){
                $err['reg-cellp'] = $error;
                
            }
            if(empty($username)){ $err['reg-username'] = "Kullanıcı adı boş bırakılamaz"; }
            else if(!preg_match('/^\w{5,}$/', $username)) { // \w equals "[0-9A-Za-z_]"
                $err['reg-username'] = "Kullanıcı adı '0-9', 'A-Z', 'a-z' ve '_' karakterlerinden ve en az 5 karakterden oluşmalıdır.";
            }
            else if(check_username_exist($username)){
                $err['reg-username'] = "Bu kullanıcı adı kullanılıyor, başka bir kullanıcı adı seçiniz.";
            }
            if(empty($password)){ $err['reg-password'] = "Şifre boş bırakılamaz"; }
            else if (strlen($password) < 8) {
                $err['reg-password'] = "Şifre 8 karakterden az olamaz";
            }
            if(empty($repassword)){ $err['reg-repassword'] = "Şifre yineleme boş bırakılamaz"; }
            else if($password != $repassword){
                $err['reg-repassword'] = "Şifreler eşleşmiyor."; 
            }
            
            if(empty($err)){
                $ins  = DB::insert('insert into users (master, email, sub, name, lastname, username, password, cellphone, firm) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', array($sub, $email, $sub, $name, $lastname, $username, $password, $cellp, $firm_key));

                $arr = array('status' => 'OK', 'uid' => $ins);
                echo json_encode($arr);
            }
            else{
                $arr = array('status' => 'NO', 'error'=> $err);
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
