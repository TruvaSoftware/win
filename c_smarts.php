<?php
/*
*   FT Alarm System
*
*   User Commands
*/

require_once('../src/config.php');

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
    
    $cmd = $query->{'s-cmd'};

    if(@$_SERVER['REQUEST_METHOD']=='POST'){

        if($cmd == 'login'){
            $username = $query->{'s-username'};
            $password = $query->{'s-password'};


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
            $suid = $query->{'s-uid'};

            $lamps = DB::get('select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', array($suid, "Lm"));

            foreach($lamps as $key => $dev){
                $smart_data = DB::getVar('select data from smarts where type = 0 and smart_id = ? and master_id = ?', array($dev->id, $query->{'usid'}));

                if(empty($smart_data)){
                    $dev->status = "0";
                }
                else{
                    $dev->status = $smart_data;

                }
            }
            
            $valves1 = DB::get('select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', array($suid, "Vl"));

            foreach($valves1 as $key => $dev){
                $smart_data = DB::getVar('select data from smarts where type = 1 and smart_id = ?', array($dev->id));

                if(empty($smart_data)){
                    $dev->status = "0";
                }
                else{
                    $dev->status = $smart_data;

                }
            }
            
            $valves2 = DB::get('select id,did,ch,adi from teknow1_s1.tw_userdev where user_id = ? and type = ?', array($suid, "Vl"));

            foreach($valves2 as $key => $dev){
                $smart_data = DB::getVar('select data from smarts where type = 2 and smart_id = ?', array($dev->id));

                if(empty($smart_data)){
                    $dev->status = "0";
                }
                else{
                    $dev->status = $smart_data;

                }
            }

            $arr = array('status' => 'OK', 'content' => $lamps, 'valves1' => $valves1, 'valves2' => $valves2);
            echo json_encode($arr);

        }
        else if($cmd == 'save'){
            try{
                $swcs = $query->{'list-switchs'};
                $swcs1 = $query->{'list1-switchs'};
                $swcs2 = $query->{'list2-switchs'};
                
                $switchs = explode('-', $swcs);
                $switchs1 = explode('-', $swcs1);
                $switchs2 = explode('-', $swcs2);
                
                $mid = $query->{'s-mid'};

                $del = DB::exec('delete from smarts where master_id = ?', array($mid));

                foreach($switchs as $switch){

                    if(!empty($switch)){
                        $add = DB::insert(
                            'INSERT INTO smarts (master_id, smart_id, type, data) VALUES (?, ?, ?, ?)',
                            array($mid, $switch, 0, "2")
                        );
                    }

                }

                foreach($switchs1 as $switch1){

                    if(!empty($switch1)){
                        $add = DB::insert(
                            'INSERT INTO smarts (master_id, smart_id, type, data) VALUES (?, ?, ?, ?)',
                            array($mid, $switch1, 1, "1")
                        );
                    }

                }
                foreach($switchs2 as $switch2){

                    if(!empty($switch2)){
                        $add = DB::insert(
                            'INSERT INTO smarts (master_id, smart_id, type, data) VALUES (?, ?, ?, ?)',
                            array($mid, $switch2, 2, "1")
                        );
                    }

                }
                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }
            catch(Exception $e){
                echo "Error".$e->getMessage();
            }


        }

    }
    
}

?>
