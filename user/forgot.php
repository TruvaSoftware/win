<?php
/*
* Teknowin Alarm System
*
* Forgot Password                          FT
*/

require_once('../../src/config.php');
include('fn_send_mail/inc.php');

$query = crypFT($_POST['q']);

if(@$_SERVER['REQUEST_METHOD']=='POST'){

    $mail      = $query->{'mail'};

    if(empty($mail)){ 
        $err = "E-Posta veya kullanıcı adı yazınız."; 
    }
    else if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {   /* E-Posta Adresi */
        
        $user =  DB::getRow('select * from users where email = ?', array($mail));
        
        if(empty($user)){
          
            $err = "Böyle bir kullanıcı bulunamadı.";
      
        }
    }
    else if(preg_match('/^\w{5,}$/', $mail)) { // \w equals "[0-9A-Za-z_]" /* Kullanıcı Adıysa */
        
        $user =  DB::getRow('select * from users where username = ?', array($mail));
        
        if(empty($user)){
          
            $err = "Böyle bir kullanıcı bulunamadı.";
      
        }
        
    }
    else{
        $err = "E-Posta veya kullanıcı adı yazınız.";
    }


    if(!isset($err)){
        $hash  = gshash(randomInt(8));
        $check = DB::getRow('select id from forgot_pass where uid = ?', array($user->id));
        if(empty($check)){
            $insert = DB::insert('insert into forgot_pass (uid, hash, time) values (?, ?, ?)', array($user->id, $hash, time()));
        }
        else{
            $update = DB::exec('update forgot_pass set hash = ? , time = ? where uid = ?', array($hash, time(), $user->id));
        }
        

        send_reset_mail($user->email, $hash, $user->name.' '.$user->lastname);
        $mail_parts = explode('@', $user->email);
        $hidden_first = '';
        for($i=0;$i<strlen($mail_parts[0]); $i++){
            $carac = '*';
            if($i==0 || $i==strlen($mail_parts[0])-1){ $carac = $mail_parts[0][$i]; }
    
            $hidden_first .=$carac;
        }
        $hidden_mail = $hidden_first.'@'.$mail_parts[1];
        $arr = array('status' => 'OK', 'message'=> 'Şifre sıfırlama e-postası, '.$hidden_mail.' adresine gönderilmiştir.');
        echo json_encode($arr);
    }
    else{
        $arr = array('status' => 'NO', 'error'=> $err);
        echo json_encode($arr);
    }
}
  

?>
