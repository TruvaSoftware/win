<?php
/*
* FT Alarm System
*
* Logins
*/

require_once('../../src/config.php');

$query = crypFT($_POST['q']);

//$query->{'usid'};

if(@$_SERVER['REQUEST_METHOD']=='POST'){

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

?>
