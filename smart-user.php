<?php
/*
* FT Alarm System
*
* User Commands
*/

require_once('../src/config.php');

$cmd = s_POST('s-cmd');

if(@$_SERVER['REQUEST_METHOD']=='POST'){

    if($cmd == 'login'){
        $username = s_POST('s-username');
        $password = s_POST('s-password');

        
        $db_check  = DB::getRow('select * from teknow1_user.tw_user where user = ?', array($username));
        
        if(empty($db_check)){
            
            $err = array('status' => 'NO', 'error' => 'Böyle bir kullanıcı bulunamadı.');
            echo json_encode($err);

        }elseif($db_check->status == 0){
            
            $err = array('status' => 'NO', 'error' => 'Böyle bir kullanıcı bulunamadı.');
            echo json_encode($err);
            
        }
        else{
            
            if(password_verify($password, $db_check->pass)){
                $suid = $db_check->id;
                    
                if($db_check->sub!=0){
                    $suid = $db_check->sub;
                }
                
                $arr = array('status' => 'OK', 'suid' => $suid);
                echo json_encode($arr);
            }else{
                $err = array('status' => 'NO', 'error'=>'Şifre hatalı.');
                echo json_encode($err);
            }

        }
        
    }
    else if($cmd == 'get-devices'){
        $suid = s_POST('s-uid');
        
        $devices = DB::get('select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', array($suid, "Lm"));

        foreach($devices as $key => $dev){
            $smart_data = DB::getVar('select data from smarts where smart_id = ?', array($dev->id));

            if(empty($smart_data)){
                $dev->status = "0";
            }
            else{
                $dev->status = $smart_data;

            }
        }
        
        $arr = array('status' => 'OK', 'content' => $devices);
        echo json_encode($arr);

    }
    else if($cmd == 'save'){
        $switchs = $_POST['list-switchs'];
        $mid = s_POST('s-mid');
        
        $del = DB::exec('delete from smarts where master_id = ?', array($mid));

        foreach($switchs as $key => $switch){
            
            $add = DB::insert(
                'INSERT INTO smarts (master_id, smart_id, data) VALUES (?, ?, ?)',
                array($mid, $switch, "2")
            );
            

        }

        $arr = array('status' => 'OK');
        echo json_encode($arr);

    }
    
}

?>
