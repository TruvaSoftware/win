<?php
/*
* Teknowin Alarm System
*
* Master Device Commands
*/

require_once('../src/config.php');

$query = crypFT($_POST['q']);

$usid = $query->{'usid'};
$code = $query->{'code'};

$check = check_start($usid, $code);

$strOK = $check['startOK'];
$m_uid = $check['masterID'];

if($strOK == 1){
    
    $cmd = $query->{'cmd'};
    
    if(@$_SERVER['REQUEST_METHOD']=='POST'){

        // Alarm Mod Kurulumu
        if($cmd == 'set-mode'){
            
            $mode = $query->{'mode'};
            if(!empty($m_uid) && !empty($mode)){
                $master_id  = DB::getVar('select did from devices where sub = 0 and uid = ?', array($m_uid));
                sendCommand($mode_codes[$mode], $master_id, "");
                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }
            
        }

        // Alarm Evden Çıkış Süresi
        else if($cmd == 'get-out-time'){

            if(!empty($m_uid)){
                
                $device  = DB::getRow('select * from devices where sub = 0 and uid = ?', array($m_uid));
                $arr = array('status' => 'OK','did' => $device->id , 'time' => $device->s_time);
                echo json_encode($arr);
                
            }
        }

        // Alarm Evden Çıkış Süresi Değiştir
        else if($cmd == 'set-out-time'){
            $time = $query->{'time'};

            if(!empty($time) && !empty($m_uid)){
                
                $master = DB::getVar('select did from devices where sub = 0 and uid = ?', array($m_uid));

                sendCommand('sTime', $master, $time);
                $arr = array('status' => 'OK');
                echo json_encode($arr);
                
            }
        }

        // Ana Cihaza Bağlı Tüm Sensörleri Sustur
        else if($cmd == 'stop-all-alarms'){

            if(!empty($m_uid)){
                $master = DB::getVar('select did from devices where sub = 0 and uid = ?', array($m_uid));
                sendCommand('aOff', $master, '');
                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }
        }

        // Alarm Durumunu Al
        else if($cmd == 'get-alarm-status'){

            if(!empty($m_uid)){
                $status = DB::getVar('select status from devices where sub = 0 and uid = ?', array($m_uid));
                $arr = array('status' => 'OK','mode' => $status);
                echo json_encode($arr);
            }
        }

        // Alarmı Sessize Al
        else if($cmd == 'mute-alarm'){

            $master = DB::getVar('select did from devices where sub = 0 and uid = ?', array($m_uid));
            $del = DB::exec('delete from alerts where did = ?', array($master));
            sendCommand('mute', $master, "");
            $arr = array('status' => 'OK');
            echo json_encode($arr);

        }

        // Alarmı Sessize Al
        else if($cmd == 'set-active'){

            if(!empty($m_uid)){
                $master_id  = DB::getVar('select did from devices where sub = 0 and uid = ?', array($m_uid));
                sendCommand("deviceOn", $master_id, "");
                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }

        }
        // Alarmı Sessize Al
        else if($cmd == 'set-passive'){

            if(!empty($m_uid)){
                $master_id  = DB::getVar('select did from devices where sub = 0 and uid = ?', array($m_uid));
                sendCommand("deviceOff", $master_id, "");
                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }

        }
        // Alarmı Sessize Al
        else if($cmd == 'stop-alarm'){

            if(!empty($m_uid)){
                $master_id  = DB::getVar('select did from devices where sub = 0 and uid = ?', array($m_uid));
                sendCommand("deviceOff", $master_id, "");
                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }

        }
        // Alarmı Sessize Al
        else if($cmd == 'set-mute'){

            if(!empty($m_uid)){
                $master_id  = DB::getVar('select did from devices where sub = 0 and uid = ?', array($m_uid));
                sendCommand("deviceMute", $master_id, "");
                $arr = array('status' => 'OK');
                echo json_encode($arr);
            }

        }

    }

}
else{
    $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
    echo json_encode($arr1); 
}
?>
