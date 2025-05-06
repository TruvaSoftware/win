<?php
/*
* FT Alarm System
*
* Logins
*/

require_once('../src/config.php');

$query = crypFT($_POST['q']);

//$query->{'usid'};
$islem = $query->{'log-cmd'};

if(@$_SERVER['REQUEST_METHOD']=='POST'){
    if($islem == 'first-login'){
        $username = $query->{'username'};
        $password = $query->{'password'};
        $log_key =  $query->{'log-key'};
        $firm_key = $query->{'log-firm-key'};
        
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
                    
                    if($db_check->master==0){
                        $us_parts = explode('_', $db_check->username);
                        $master_type = $us_parts[0];
                    }
                    else{
                        $master_username  = DB::getVar('select username from users where id = ?', array($db_check->master));
                        $us_parts = explode('_', $master_username);
                        $master_type = $us_parts[0];
                    }
                    
                    $arr = array('status' => 'OK', 'master_type' => $master_type );
                    echo json_encode($arr);
                }
            }
        }
    }
    else if($islem == 'login'){
        $username       = $query->{'username'};
        $password       = $query->{'password'};
        $log_key        = $query->{'log-key'};
        $log_tk         = $query->{'log-token'};
        $log_firm_key   = $query->{'log-firm-key'};
        
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
                    $ins  = DB::insert('insert into users (master, email, sub, name, lastname, username, password, cellphone, log_key, firm) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(0, "", 0, "", "", $username, $password, 0, $log_key, $log_firm_key));
                    $ins2  = DB::insert('insert into devices (sub, uid, type, did, name, status, s_time) values (?, ?, ?, ?, ?, ?, ?)', array(0, $ins, 0, $qr_parts[1],$username, 23, 30));
                    
                    $master_type = "master";
                    
                    $arr = array('status' => 'OK', 'sub' => 0, 'uid' => $ins, 'mid' => 0, 'firm' => $log_firm_key, 'master_type' => $master_type);
                    echo json_encode($arr);
                }
                else{
                    $arr = array('status' => 'NO', 'error' => 'Böyle bir ana cihaz bulunamadı.');
                    echo json_encode($arr);
                }
            }
            else if($qr_parts[0] == "motion"){
                $hash = md5("Tekno".$qr_parts[1]."Win");
                
                if($password == $hash){
                    $ins  = DB::insert('insert into users (master, email, sub, name, lastname, username, password, cellphone, log_key, firm) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(0, "", 0, "", "", $username, $password, 0, $log_key, $log_firm_key));
                    
                    $ins1  = DB::insert('insert into devices (sub, uid, type, did, name, status, s_time) values (?, ?, ?, ?, ?, ?, ?)', array(0, $ins, 0, $qr_parts[1],$username, 23, 30));
                    
                    $master_type = "motion";
                    
                    $arr = array('status' => 'OK', 'sub' => 0, 'uid' => $ins, 'mid' => 0, 'firm' => $log_firm_key, 'master_type' => $master_type);
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
                
                if($db_check->master==0){
                    $us_parts = explode('_', $db_check->username);
                    $master_type = $us_parts[0];
                }
                else{
                    $master_username  = DB::getVar('select username from users where id = ?', array($db_check->master));
                    $us_parts = explode('_', $master_username);
                    $master_type = $us_parts[0];
                }
                
                if(empty($log_tk)){
                    $upp  = DB::exec('update users set log_key = ? where id = ?', array($log_key, $db_check->id));
                }
                else{
                    $upp  = DB::exec('update users set log_key = ?,token_id = ? where id = ?', array($log_key, $log_tk, $db_check->id));
                }
                $arr = array('status' => 'OK', 'sub' => $db_check->sub, 'uid' => $db_check->id, 'mid' => $db_check->master, 'firm' => $db_check->firm, 'master_type' => $master_type);
                echo json_encode($arr);
            }
            
        }
        
    }
    
}

?>
