<?php
/*
* Teknowin Alarm System
*
* Master Device Commands
*/

require_once('../src/config.php');

$cmd = s_POST('cmd');

if(@$_SERVER['REQUEST_METHOD']=='POST'){

    // Alarm Mod Kurulumu
    if($cmd == 'set-mode'){
        $mid = s_POST('mid');
        $mode = s_POST('mode');
        
        if(!empty($mid) && !empty($mode)){
            $master_id  = DB::getVar('select did from devices where sub = 0 and uid = ?', array($mid));
            sendCommand($mode_codes[$mode], $master_id, "");
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }
    }

    // Alarm Evden Çıkış Süresi
    else if($cmd == 'get-out-time'){
        $uid = s_POST('md-uid');
        
        if(!empty($uid)){
            $device  = DB::getRow('select * from devices where sub = 0 and uid = ?', array($uid));
            $arr = array('status' => 'OK','did' => $device->id , 'time' => $device->s_time);
            echo json_encode($arr);
        }
    }
    
    // Alarm Evden Çıkış Süresi Değiştir
    else if($cmd == 'set-out-time'){
        $id = s_POST('db_did');
        $time = s_POST('time');
        
        if(!empty($id) && !empty($time)){
            $master = DB::getVar('select did from devices where id = ?', array($id));
            sendCommand('sTime', $master, $time);
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }
    }
    
    // Ana Cihaza Bağlı Tüm Sensörleri Sustur
    else if($cmd == 'stop-all-alarms'){
        $mid = s_POST('mid');
        
        if(!empty($mid)){
            $master = DB::getVar('select did from devices where sub = 0 and uid = ?', array($mid));
            sendCommand('aOff', $master, '');
            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }
    }
    
    // Alarm Durumunu Al
    else if($cmd == 'get-alarm-status'){
        $uid = s_POST('md-uid');
        
        if(!empty($uid)){
            $status = DB::getVar('select status from devices where sub = 0 and uid = ?', array($uid));
            $arr = array('status' => 'OK','mode' => $status);
            echo json_encode($arr);
        }
    }
    
    // Alarmı Sessize Al
    else if($cmd == 'mute-alarm'){
        $mid = s_POST('mid');
        $master = DB::getVar('select did from devices where sub = 0 and uid = ?', array($mid));
        $del = DB::exec('delete from alerts where did = ?', array($master));
        sendCommand('mute', $master, "");
        $arr = array('status' => 'OK');
        echo json_encode($arr);
        
    }
}

?>
