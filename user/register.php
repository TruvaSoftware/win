<?php
/*
* Teknowin Alarm System
*
* Master User Register                          FT
*/

require_once('../../src/config.php');

$query = crypFT($_POST['q']);

if(@$_SERVER['REQUEST_METHOD']=='POST'){

    $sub        = 0;
    $mid        = 0;
    $firm_key   = $query->{'firm-key'};
    $token      = $query->{'token'};
    $code       = $query->{'code'};
    
    $name       = $query->{'reg-name'};
    $lastname   = $query->{'reg-lastname'};
    $email      = $query->{'reg-email'};
    $cellp      = $query->{'reg-cellp'};
    $username   = $query->{'reg-username'};
    $password   = $query->{'reg-password'};
    $repassword = $query->{'reg-repassword'};

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
        $ins  = DB::insert(
            'insert into users (master, email, sub, name, lastname, username, password, token_id, cellphone, log_key, firm) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            array($mid ,$email, $sub, $name, $lastname, $username, $password, $token, $cellp, $code, $firm_key));
        
        $db_check = DB::getRow('select * from users where id = ?', array($ins));
        
        $arr = array(
            'status' => 'OK', 
            'sub' => $db_check->sub, 
            'uid' => $db_check->id, 
            'mid' => $db_check->master,
            'usr' => $db_check->username,
            'pas' => $db_check->password,
            'firm' => $db_check->firm, 
            'code' => $db_check->log_key
        );
        echo json_encode($arr);
    }
    else{
        $arr = array('status' => 'NO', 'error'=> $err);
        echo json_encode($arr);
    }
}
  

?>
