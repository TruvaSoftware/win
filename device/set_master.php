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
    
    $cmd = $query->{'cmd'};

    // Alarm Mod Kurulumu
    if($cmd == 'set-mode'){

        $mode = $query->{'mode'};
        $did = $query->{'did'};
        
        if(!empty($m_uid) && !empty($did) && !empty($mode)){
            $m_did  = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));
            
            sendCommand($mode_codes[$mode], $m_did, "");
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }

    }
    // Ana Cihaza Bağlı Tüm Sensörleri Sustur
    else if($cmd == 'set-stop'){
                
        $did = $query->{'did'};

        if(!empty($m_uid) && !empty($did)){
            $m_did = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));
            sendCommand('aOff', $m_did, '');
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }
    }
    
    // Alarmı Sessize Al
    else if($cmd == 'set-mute'){
    
        $did = $query->{'did'};
        if(!empty($m_uid) && !empty($did)){
            
            $m_did = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));
            $del = DB::exec('delete from alerts where did = ?', array($m_did));
            sendCommand('mute', $m_did, "");
            $arr = array('status' => 'OK');
            echo json_encode($arr);
            
        }

    }
    
    // Alarmı Aktif Et
    else if($cmd == 'sm-active'){
        
        $did = $query->{'did'};

        if(!empty($m_uid) && !empty($did)){
            $m_did  = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));
            
            $upp  = DB::exec(
                'update devices set status = ? where sub = 0 and did = ?', 
                array('11' ,$m_did)
            );
            ALR::clear($m_did);
            ALR::mode($m_did, "11");
            
            sendCommand("onDev", $m_did, "");
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }

    }
    // Alarmı Sessize Al
    else if($cmd == 'sm-passive'){
        $did = $query->{'did'};

        if(!empty($m_uid) && !empty($did)){
            $m_did  = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));
            
            $aup  = DB::exec(
                'update devices set status = 1 where sub != 0 and status = 2 and sub = ?', 
                array($did)
            );
            $aup2  = DB::exec(
                'update devices set status = ? where sub = 0 and did = ?', 
                array('12' ,$m_did)
            );
            ALR::clear($m_did);
            ALR::mode($m_did, "12");
            
            sendCommand("offDev", $m_did, "");
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }

    }
    // Alarmı Sessize Al
    else if($cmd == 'sm-stop'){
        $did = $query->{'did'};

        if(!empty($m_uid) && !empty($did)){
            $m_did  = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));
            
            $aup  = DB::exec(
                'update devices set status = 1 where sub != 0 and status = 2 and sub = ?', 
                array($did)
            );
            $aup2  = DB::exec(
                'update devices set status = ? where sub = 0 and did = ?', 
                array('12' ,$m_did)
            );
            ALR::clear($m_did);
            ALR::mode($m_did, "12");
            
            sendCommand("offDev", $m_did, "");
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }

    }
    // Alarmı Sessize Al
    else if($cmd == 'sm-mute'){
        $did = $query->{'did'};

        if(!empty($m_uid) && !empty($did)){
            $m_did  = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));
            $mup = DB::exec(
                'update devices set status = 1 where sub != 0 and status = 2 and sub = ?', 
                array($did)
            );
            ALR::clear($m_did);
            ALR::mode($m_did, "13");
            
            sendCommand("muteDev", $m_did, "");
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }

    }
    // Alarmı Sessize Al
    else if($cmd == 'smc-take-photo'){
        $did = $query->{'did'};

        if(!empty($m_uid) && !empty($did)){
            $m_did  = DB::getVar('select did from devices where sub = 0 and id = ?', array($did));


            sendCommand("takePhoto", $m_did, "");
            $arr = array('status' => 'OK');
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
