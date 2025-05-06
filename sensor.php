<?php
/* 
*  Teknowin Alarm System
*
*  Sensor & Siren Komutları [Mobil]
*/

require_once('../src/config.php');

$cmd = s_POST('cmd');

if(@$_SERVER['REQUEST_METHOD']=='POST'){
    
    /* 
    *  Sensör ekle 
    */
    if($cmd == 'add-sensor'){ 
        $sid = s_POST('sid'); $typ = s_POST('type'); $mid = s_POST('mid');
        
        if(!empty($sid) && !empty($typ) && !empty($mid)){
            $dv_exist =  DB::getRow('select * from devices where did = ?', array($sid));
            if(empty($dv_exist)){
                $master = DB::getRow('select * from devices where sub = 0 and uid = ?', array($mid));

                    $ins  = DB::insert('insert into devices (sub, uid, type, did, name, status, s_time) values (?, ?, ?, ?, ?, ?, ?)', array($master->id, $mid, $typ, $sid, $dev_types[$typ].$sid, 1, 0));

                    sendCommand('n'.$cmd_types[$typ].$cmd_types2[1], $master->did, $sid);
                    ALR::status('n'.$cmd_types[$typ].$cmd_types2[1], $master->did, $sid);
                    $arr = array('status' => 'OK');
                    echo json_encode($arr);
            }
            else{
                $arr = array('status' => 'NO', 'error' => 'Bu cihaz zaten eklenmiş durumda.');
                echo json_encode($arr);
            }
            
        }
        else{
            $arr = array('status' => 'NO', 'error' => 'no-sid-type-mid');
            echo json_encode($arr);
        }
    }
    
    /* 
    *  Sensör listesi 
    */
    else if($cmd == 'get-sensors'){
        
        $mid = s_POST('mid');
        if(!empty($mid)){
            $devices = DB::get('select id,did,name,type,status from devices where not sub = 0 and uid = ?', array($mid));    
            $arr = array('status' => 'OK', 'data' => $devices);
            echo json_encode($arr);
        }
        else{
            $arr = array('status' => 'NO', 'error' => 'no-master-id');
            echo json_encode($arr);
        }
    }
    
    /* 
    *  Sensör & Siren durumunu değiştir [Aktif Pasif]
    */
    else if($cmd == 'change-status'){
        
        $id = s_POST('sid');
        $st = s_POST('st');
        $sen = DB::getRow('select * from devices where did = ?', array($id));
        //$upp  = DB::exec('update devices set status = ? where did = ?', array($st, $id));
        $master = DB::getVar('select did from devices where id = ?', array($sen->sub));
        sendCommand('c'.$cmd_types[$sen->type].$cmd_types2[$st], $master, $sen->did);

        $arr = array('status' => 'OK');
        echo json_encode($arr);
        
    }
    
    /* 
    *  Sensör & Siren adını düzenle
    */
    else if($cmd == 'dev-edit'){
        $id = s_POST('dev-id');
        $name = s_POST('dev-name');
        if(!empty($id) && !empty($name)){
            $upp  = DB::exec('update devices set name = ? where did = ?', array($name, $id));

            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }
        else{
            $arr = array('status' => 'NO', 'error' => 'no-sid-name');
            echo json_encode($arr);
        }
    }
    
    /* 
    *  Sensör & Siren uyarısını durdur
    */
    else if($cmd == 'stop-warning'){
        
        $id = s_POST('db_id');
        if(!empty($id)){
            $upp  = DB::exec('update devices set status = 1 where did = ?', array($id));
            $del = DB::exec('delete from alerts where sid = ?', array($id));

            $arr = array('status' => 'OK');
            echo json_encode($arr);
        }
        else{
            $arr = array('status' => 'NO', 'error' => 'no-did');
            echo json_encode($arr);
        }
        
    }
    
    /* 
    *  Sensör & Siren uyarılarını al, Alarm durumunu al 
    */
    else if($cmd == 'get-alerts'){  
        
        $uid = s_POST('uid');
        if(!empty($uid)){
            $did = DB::getVar('select did from devices where sub = 0 and type = 0 and uid = ?', array($uid));
            $data = DB::get('select sid from alerts where not sid = "" and ct = "" and did = ?', array($did));
            $data2 = DB::getRow('select * from alerts where sid = "" and did = ?', array($did));
            $data3 =DB::getRow('select * from alerts where not sid = "" and not ct = "" and did = ?', array($did)); 
            
            if(empty($data)){ $data = ""; }
            
            if(empty($data2)){ $data2 = ""; }
            else{ $del = DB::exec('delete from alerts where id = ?', array($data2->id)); }
        
            if(empty($data3)){ $data3 = ""; }
            else{ 
                $data3t = array('sid' => $data3->sid,'ct' => $data3->ct);
                $del = DB::exec('delete from alerts where id = ?', array($data3->id)); 
            }
        
            $arr = array('status' => 'OK', 'data' => $data, 'data2' => $data2->ct, 'data3' => $data3t);
            echo json_encode($arr);
        }
        else{
            $arr = array('status' => 'NO', 'error' => 'no-uid');
            echo json_encode($arr);
        }
        
    }    
    // Alarm Evden Çıkış Süresi
    else if($cmd == 'get-precision'){
        $sid = s_POST('sensor-id');
        
        if(!empty($sid)){
            $precision  = DB::getVar('select s_time from devices where not sub = 0 and did = ?', array($sid));
            $arr = array('status' => 'OK', 'precision' => $precision);
            echo json_encode($arr);
        }
    }
    
        
    // Alarm Evden Çıkış Süresi Değiştir
    else if($cmd == 'save-precision'){
        $sid = s_POST('sensor-id');
        $precision = s_POST('precision');
        
        if(!empty($sid)){
            if($precision>=0 && $precision<=4){
                $sensor_sub = DB::getVar('select sub from devices where did = ?', array($sid));
                $master = DB::getVar('select did from devices where sub = 0 and type = 0 and id = ?', array($sensor_sub));

                
                sendCommand('sens'.$precision, $master, $sid);
                
                //$upp  = DB::exec('update devices set s_time = ? where did = ?', array($precision, $sid));

                $arr = array('status' => 'OK');
                echo json_encode($arr);            
            }

        }
    }    
        
    // Alarm Evden Çıkış Süresi Değiştir
    else if($cmd == 'set-reference'){
        $sid = s_POST('sensor-id');
        
        if(!empty($sid)){
            
                $sensor_sub = DB::getVar('select sub from devices where did = ?', array($sid));
                $master = DB::getVar('select did from devices where sub = 0 and type = 0 and id = ?', array($sensor_sub));

                
                sendCommand('stRef', $master, $sid);
                
                //$upp  = DB::exec('update devices set s_time = ? where did = ?', array($precision, $sid));

                $arr = array('status' => 'OK');
                echo json_encode($arr);            

        }
    }
}

?>
