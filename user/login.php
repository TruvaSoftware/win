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

    $username       = $query->{'username'};
    $password       = $query->{'password'};
    $log_key        = $query->{'log-key'};
    $log_tk         = $query->{'log-token'};
    $log_firm_key   = $query->{'log-firm-key'};

    // Geçici
    if(empty($log_firm_key)){ $log_firm_key = "tekron"; }

    $db_check  = DB::getRow(
        'select * from users where username = ? and firm = ?', 
        array(
            $username, 
            $log_firm_key
        )
    );

    if(empty($db_check)){
        // User not registered
        $arr = array(
            'status' => 'NO', 
            'error' => 'Böyle bir kullanıcı bulunamadı.'
        );
        echo json_encode($arr);
    }
    else{

        if($db_check->password != $password){
            // Wrong Password
            $arr = array(
                'status' => 'NO', 
                'error' => 'Şifre yanlış, tekrar deneyiniz.'
            );
            echo json_encode($arr);
        }
        else{
            // True Password

            if(empty($log_tk)){
                $upp  = DB::exec(
                    'update users set log_key = ? where id = ?',
                    array(
                        $log_key, 
                        $db_check->id
                    )
                );
            }
            else{
                $upp  = DB::exec(
                    'update users set log_key = ?,token_id = ? where id = ?', 
                    array(
                        $log_key, 
                        $log_tk, 
                        $db_check->id
                    )
                );
            }

            $arr = array(
                'status' => 'OK', 
                'sub' => $db_check->sub, 
                'uid' => $db_check->id, 
                'mid' => $db_check->master, 
                'firm' => $db_check->firm
            );
            echo json_encode($arr);
        }

    }
        
    
}

?>
