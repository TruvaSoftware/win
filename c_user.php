<?php
/*
* FT Alarm System
*
* User Commands
*/

require_once('../src/config.php');

$query = crypFT($_POST['q']);

$usid = $query->{'usid'};
$code = $query->{'code'};

$check = check_start($usid, $code);

$strOK = $check['startOK'];
$m_uid = $check['masterID'];

if($strOK == 1){

    $islem = $query->{'log-cmd'};

    if(@$_SERVER['REQUEST_METHOD']=='POST'){

        if($islem == 'user-register'){
            
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

                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }
            else{
                $arr = array('status' => 'NO', 'error'=> $err);
                echo json_encode($arr);
            }
            


        }
        else if($islem == 'master-user-register'){

            $sub        = 0;
            $name       = $query->{'reg-name'};
            $lastname   = $query->{'reg-lastname'};
            $email      = $query->{'reg-email'};
            $cellp      = $query->{'reg-cellp'};
            $username   = $query->{'reg-username'};
            $password   = $query->{'reg-password'};
            $firm_key   = $query->{'mreg-firm-key'};
            $mid        = $query->{'master-id'};
            $repassword   = $query->{'reg-repassword'};

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
                $ins  = DB::insert('insert into users (master, email, sub, name, lastname, username, password, cellphone, firm) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', array($mid ,$email, 0, $name, $lastname, $username, $password, $cellp, $firm_key));

                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }
            else{
                $arr = array('status' => 'NO', 'error'=> $err);
                echo json_encode($arr);
            }


        }
        else if($islem == 'get-users'){

            $u_type =   $query->{'utype'};
            $sub_arr = array(0 => ' not sub = 0 and', 1 => '');
            $users = DB::get('select id,sub,name,lastname,username from users where'.$sub_arr[$u_type].' master = ?', array($m_uid));
            $count = 0;
            foreach($users as $u){
                if($u->sub=="0"){ $user_type = "admin"; }
                else{ $user_type = "user"; }
                $data[$count] = array('u_type'=> $user_type,'userid'=>$u->id, 'name'=>$u->name, 'lastname'=>$u->lastname,'username'=>$u->username);
                $count++;
            }

            $arr = array('status' => 'OK', 'data' => $data);
            echo json_encode($arr);

        }
        else if($islem == 'isExistMasterUser'){
            
            $db_check = DB::getRow('select * from users where sub = 0 and master = ?', array($m_uid));

            if(empty($db_check)){
                $arr = array('status' => 'NO');
                echo json_encode($arr);
            }
            else{
                $arr = array('status' => 'YES');
                echo json_encode($arr);
            }

        }
        else if($islem == 'get-user-info'){
            $uid = $query->{'uid'};

            $user = DB::getRow('select * from users where master = ? and id = ?', array($m_uid, $uid));

            $arr = array('status' => 'OK', 'name' => $user->name, 'lastname' => $user->lastname, 'username' => $user->username, 'mail' => $user->email, 'cellphone' => $user->cellphone);
            echo json_encode($arr);
                
        }
        else if($islem == 'user-edit'){
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
    }
  
}
else{
    $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
    echo json_encode($arr1); 
}

?>
