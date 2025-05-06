<?php
/* 
*  Teknowin Alarm System
*
*  Sensor & Siren Komutları [Mobil]
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
    
    /*
    *   Gelen koddan komut bilgisi al.
    */
    $cmd = $query->{'cmd'};

    if(@$_SERVER['REQUEST_METHOD']=='POST'){

        /* 
        *  SM Siren Ekle ekle 
        */
        if($cmd == 'add-sm-siren'){
            
            $sid = $query->{'sid'}; $typ = $query->{'type'}; $mid = $query->{'mid'};

            if(!empty($sid) && !empty($typ) && !empty($m_uid) && !empty($mid)){
                                    
                $dv_exist =  DB::getRow('select * from devices where did = ?', array($sid));
                if(empty($dv_exist)){
                    $master = DB::getRow('select * from devices where sub = 0 and uid = ?', array($mid));

                    $ins  = DB::insert('insert into devices (sub, uid, type, did, name, status, s_time) values (?, ?, ?, ?, ?, ?, ?)', array($master->id, $mid, $typ, $sid, $dev_types[$typ].$sid, 1, 0));

                    sendCommand('srn', $master->did, $sid);
                    ALR::status('srn', $master->did, $sid);
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
        *  Sensör ekle 
        */
        if($cmd == 'add-sensor'){
            
            $sid = $query->{'sid'}; $typ = $query->{'type'}; $mid = $query->{'mid'};

            if(!empty($sid) && !empty($typ) && !empty($m_uid) && !empty($mid)){
                                    
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

            if(!empty($m_uid)){
                $devices = DB::get('select id,did,name,type,status from devices where not sub = 0 and uid = ?', array($m_uid));    
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

            $id = $query->{'sid'};
            $st = $query->{'st'};
            $sen = DB::getRow('select * from devices where not sub = 0 and did = ?', array($id));
            
            if($sen->uid == $m_uid){
                
                $master = DB::getVar('select did from devices where id = ?', array($sen->sub));
                sendCommand('c'.$cmd_types[$sen->type].$cmd_types2[$st], $master, $sen->did);

                $arr = array('status' => 'OK');
                echo json_encode($arr);
                
            }
            else{
                    
                $arr = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
                echo json_encode($arr); 
                
            }

        }

        /* 
        *  Sensör & Siren adını düzenle
        */
        else if($cmd == 'dev-edit'){
            
            $id = $query->{'dev-id'};   $name = $query->{'dev-name'};
            
            if(!empty($id) && !empty($name)){
                
                $sen = DB::getRow('select * from devices where did = ?', array($id));
                
                if($sen->uid == $m_uid){
                    
                    $upp  = DB::exec('update devices set name = ? where did = ?', array($name, $id));

                    $arr = array('status' => 'OK');
                    echo json_encode($arr);
                    
                }
                else{

                    $arr = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
                    echo json_encode($arr); 

                }

            }
            else{
                $arr = array('status' => 'NO', 'error' => 'no-sid-name');
                echo json_encode($arr);
            }
        }

        /* 
        *  Sensör & Siren uyarılarını al, Alarm durumunu al 
        */
        else if($cmd == 'get-alerts'){

            if(!empty($m_uid)){
                $dev = DB::getRow('select did,status from devices where sub = 0 and type = 0 and uid = ?', array($m_uid));
                $did = $dev->did;
                $data = DB::get('select sid from alerts where type = 2 and did = ?', array($did));
                $data2 = DB::getRow('select * from alerts where type = 1 and did = ?', array($did));
                $data3 = DB::getRow('select * from alerts where type = 3 and did = ?', array($did)); 

                if(empty($data)){ $data = ""; }

                if(empty($data2)){ $data2 = ""; }
                else{ $del = DB::exec('delete from alerts where id = ?', array($data2->id)); }

                if(empty($data3)){ $data3 = ""; }
                else{ 
                    $data3t = array('sid' => $data3->sid,'ct' => $data3->ct);
                    $del = DB::exec('delete from alerts where id = ?', array($data3->id)); 
                }

                $arr = array('status' => 'OK', 'data' => $data, 'data2' => $data2->ct, 'data3' => $data3t, 'dev_status' => $dev->status);
                echo json_encode($arr);
            }
            else{
                $arr = array('status' => 'NO', 'error' => 'no-uid');
                echo json_encode($arr);
            }

        }    
        // Alarm Evden Çıkış Süresi
        else if($cmd == 'get-precision'){
            $sid = $query->{'sensor-id'};

            if(!empty($sid)){
                
                $precision  = DB::getRow('select s_time,uid from devices where not sub = 0 and did = ?', array($sid));
                
                if($precision->uid == $m_uid){
                    
                    $arr = array('status' => 'OK', 'precision' => $precision->s_time);
                    echo json_encode($arr);
                    
                }
                else{
                    $arr = array('status' => 'NO', 'error' => 'Yetkisiz İşlem');
                    echo json_encode($arr);
                }
            }
        }


        // Alarm Evden Çıkış Süresi Değiştir
        else if($cmd == 'save-precision'){
            
            $sid = $query->{'sensor-id'};   $precision = $query->{'precision'};

            if(!empty($sid)){
                if($precision>=0 && $precision<=4){
                    
                    $sensor = DB::getRow('select sub,uid from devices where did = ?', array($sid));
                    
                    if($sensor->uid == $m_uid){
                        
                        $master = DB::getVar('select did from devices where sub = 0 and type = 0 and id = ?', array($sensor->sub));

                        sendCommand('sens'.$precision, $master, $sid);

                        $arr = array('status' => 'OK');
                        echo json_encode($arr);  
                        
                    }
                    else{
                        $arr = array('status' => 'NO', 'error' => 'Yetkisiz İşlem');
                        echo json_encode($arr);
                    }
          
                }

            }
        }    

    }
    
    
    
}

/*
*   Giriş kontrolü başarısızsa.
*/
else{
    $cmd = $_POST['cmd'];
    
    if($cmd == 'dev-edit'){

        $id = $_POST['dev-id'];   $name = $_POST['dev-name'];

        if(!empty($id) && !empty($name)){

            $sen = DB::getRow('select * from devices where did = ?', array($id));

                $upp  = DB::exec('update devices set name = ? where did = ?', array($name, $id));

                $arr = array('status' => 'OK');
                echo json_encode($arr);


        }
        else{
            $arr = array('status' => 'NO', 'error' => 'no-sid-name');
            echo json_encode($arr);
        }
    }
    else{
        $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
        echo json_encode($arr1); 
    }
    
    

}

?>
