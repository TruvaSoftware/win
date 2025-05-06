<?php
/*
* FT Alarm System
*
* User Commands
*/

require_once('../src/config.php');

$islem = s_POST('log-cmd');

if(@$_SERVER['REQUEST_METHOD']=='POST'){
    if($islem == 'first-login'){
        $username = s_POST('username');
        $password = s_POST('password');
        $log_key = s_POST('log-key');
        $firm_key = s_POST('log-firm-key');
        
        // Geçici
        if(empty($firm_key)){ $firm_key = "tekron"; }
        
        $db_check  = DB::getRow('select * from users where username = ? and firm = ?', array($username, $firm_key));
        
        if(empty($db_check)){
            $arr = array('status' => 'NO', 'error' => 'Böyle bir kullanıcı bulunamadı.');
            echo json_encode($arr);
        }
        else{
            if($db_check->password != $password){
                // Wrong Password
                $arr = array('status' => 'NO', 'error' => 'Şifre yanlış, tekrar deneyiniz.');
                echo json_encode($arr);
            }
            else{
                if($db_check->log_key != $log_key){
                    // Wrong Log Key
                    $arr = array('status' => 'NO');
                    echo json_encode($arr);
                }else{
                    // True Log Key
                    $arr = array('status' => 'OK');
                    echo json_encode($arr);
                }
            }
        }
    }
    else if($islem == 'login'){
        $username = s_POST('username');
        $password = s_POST('password');
        $log_key = s_POST('log-key');
        $log_tk = s_POST('log-token');
        $log_firm_key = s_POST('log-firm-key');
        
        // Geçici
        if(empty($log_firm_key)){ $log_firm_key = "tekron"; }
        
        $db_check  = DB::getRow('select * from users where username = ? and firm = ?', array($username, $log_firm_key));
        
        if(empty($db_check)){
            // User not registered

            // Check QR Possibility
            $qr_parts = explode('_', $username);
            
            if($qr_parts[0] == "master"){
                $hash = md5("Tekno".$qr_parts[1]."Win");
                
                if($password == $hash){
                    $ins  = DB::insert('insert into users (master, email, sub, name, lastname, username, password, cellphone, firm) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', array(0, "", 0, "", "", $username, $password, 0, $log_firm_key));
                    $ins2  = DB::insert('insert into devices (sub, uid, type, did, name, status, s_time) values (?, ?, ?, ?, ?, ?, ?)', array(0, $ins, 0, $qr_parts[1],$username, 23, 30));
                    
                    $arr = array('status' => 'OK', 'sub' => 0, 'uid' => $ins, 'mid' => 0, 'firm' => $log_firm_key);
                    echo json_encode($arr);
                }
                else{
                    $arr = array('status' => 'NO', 'error' => 'Böyle bir ana cihaz bulunamadı.');
                    echo json_encode($arr);
                }
            }
            else{
                $arr = array('status' => 'NO', 'error' => 'Böyle bir kullanıcı bulunamadı.');
                echo json_encode($arr);
            }
        }
        else{
            
            if($db_check->password != $password){
                // Wrong Password
                $arr = array('status' => 'NO', 'error' => 'Şifre yanlış, tekrar deneyiniz.');
                echo json_encode($arr);
            }
            else{
                // True Password
                if(empty($log_tk)){
                    $upp  = DB::exec('update users set log_key = ? where id = ?', array($log_key, $db_check->id));
                }
                else{
                    $upp  = DB::exec('update users set log_key = ?,token_id = ? where id = ?', array($log_key, $log_tk, $db_check->id));
                }
                $arr = array('status' => 'OK', 'sub' => $db_check->sub, 'uid' => $db_check->id, 'mid' => $db_check->master, 'firm' => $db_check->firm);
                echo json_encode($arr);
            }
            
        }
        
    }
    else if($islem == 'user-register'){
        
        $sub =      s_POST('user-sub');
        $name =     s_POST('reg-name');
        $lastname = s_POST('reg-lastname');
        $email =    s_POST('reg-email');
        $cellp =    s_POST('reg-cellp');
        $username = s_POST('reg-username');
        $password = s_POST('reg-password');
        $firm_key = s_POST('reg-firm-key');
        
        // Geçici
        if(empty($firm_key)){ $firm_key = "tekron"; }
        
        $ins  = DB::insert('insert into users (master, email, sub, name, lastname, username, password, cellphone, firm) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', array($sub, $email, $sub, $name, $lastname, $username, $password, $cellp, $firm_key));
        
        $arr = array('status' => 'OK');
        echo json_encode($arr);
        
    }
    else if($islem == 'master-user-register'){
        
        $sub =      s_POST('user-sub');
        $name =     s_POST('reg-name');
        $lastname = s_POST('reg-lastname');
        $email =    s_POST('reg-email');
        $cellp =    s_POST('reg-cellp');
        $username = s_POST('reg-username');
        $password = s_POST('reg-password');
        $mid =      s_POST('master-id');
        $firm_key = s_POST('mreg-firm-key');
        
        // Geçici
        if(empty($firm_key)){ $firm_key = "tekron"; }

        $ins  = DB::insert('insert into users (master, email, sub, name, lastname, username, password, cellphone, firm) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', array($mid ,$email, 0, $name, $lastname, $username, $password, $cellp, $firm_key));
        
        $arr = array('status' => 'OK');
        echo json_encode($arr);
        
    }
    else if($islem == 'get-users'){
        $uid =      s_POST('uid');
        $u_type =   s_POST('utype');
        $sub_arr = array(0 => ' not sub = 0 and', 1 => '');
        $users = DB::get('select id,sub,name,lastname,username from users where'.$sub_arr[$u_type].' master = ?', array($uid));
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
        $mid = s_POST('mid');
        $db_check = DB::getRow('select * from users where sub = 0 and master = ?', array($mid));
        
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
        $uid = s_POST('uid');
        
        $user = DB::getRow('select * from users where id = ?', array($uid));
        
        $arr = array('status' => 'OK', 'name' => $user->name, 'lastname' => $user->lastname, 'username' => $user->username, 'mail' => $user->email, 'cellphone' => $user->cellphone);
        echo json_encode($arr);
        
    }
    else if($islem == 'user-edit'){
        $uid = s_POST('user-id');
        $name = s_POST('u-edit-name');
        $lastname = s_POST('u-edit-lastname');
        $email = s_POST('u-edit-email');
        $cellphone = s_POST('u-edit-cellp');
        $username = s_POST('u-edit-username');
        
        if(isset($_POST['u-edit-password'])){
            $upp  = DB::exec('update users set name = ?, lastname = ?, username = ?, cellphone = ?, email = ?, password = ? where id = ?', array($name, $lastname, $username, $cellphone, $email, $_POST['u-edit-password'], $uid));
        }
        else{
            try{
                $upp  = DB::exec('update users set name = ?, lastname = ?, username = ?, cellphone = ?, email = ? where id = ?', array($name, $lastname, $username, $cellphone, $email, $uid));
                $arr = array('status' => 'OK');
            }
            catch(Exception $e){
                $arr = array('status' => 'NO', 'error' => 'Kaydedilemedi');
            }
            
        }
        echo json_encode($arr);
        
    }
}

?>
