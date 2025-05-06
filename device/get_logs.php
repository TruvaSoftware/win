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

    function searchIn($p_time, $p_did, $p_sid, $arr){

        foreach($arr as $key => $a){
            if($a['time'] == $p_time && $a['did'] == $p_did && $a['sid'] == $p_sid){
                return $key;
            }
        }

    }
    if(isset($query->{'did'}) && $query->{'did'} !=0){
        $did = $query->{'did'};
        $logs =  DB::get('select * from alarm_logs where uid = ? and did = ? ORDER BY time DESC', array($m_uid, $did));
    }
    else{
        $logs =  DB::get('select * from alarm_logs where uid = ? ORDER BY time DESC', array($m_uid));
    }
    
    setlocale(LC_ALL,"turkish");
    $manual_index = 0;
    $date = "";
    foreach($logs as $key => $lg){
        $dev = DB::getRow('select * from devices where id = ?', array($lg->did));

        if($lg->sid == 0){
            $dev_name = $dev->name;
            $dev_type = 3;
        }
        else{
            $sen = DB::getRow('select * from devices where id = ?', array($lg->sid));
            $dev_name = $dev->name." \ ".$sen->name;
            $dev_type = $sen->type;
        }

        $pname = DB::getVar('select name from places where id = ?', array($dev->pid));
        $rname = DB::getVar('select name from places where id = ?', array($dev->rid));

        if(strftime('%d.%m.%Y', $lg->time) != $date){
            $alarm_logs[$manual_index]['time'] =  strftime('%d.%m.%Y', $lg->time);
            $alarm_logs[$manual_index]['devs'] = null;
            $alarm_logs[$manual_index]['did'] = null;
            $alarm_logs[$manual_index]['devid'] = null;
            $alarm_logs[$manual_index]['sid'] = null;
            $alarm_logs[$manual_index]['type'] = null;
            $alarm_logs[$manual_index]['count'] = null;
            $manual_index +=1;
        }

        $is_exist = searchIn(strftime('%H:%M | %d.%m.%Y', $lg->time), $lg->did, $lg->sid, $alarm_logs);
        
        if($dev->type == 9){
            
            if (file_exists("../../records/".$dev->did."/".$lg->time.".jpg")) {
                $pic_time = $lg->time;
            }
            else
            {
                $pic_time = 0;
            }           
            
        }
        
        if(!is_null($is_exist)){
            if($dev->type == 9 && $pic_time !=0){
                array_push($alarm_logs[$is_exist]['pics'], $pic_time);
                array_push($alarm_logs[$is_exist]['ptimes'], strftime("%d.%m.%Y - %H:%M:%S",$pic_time));
                
            }
            $alarm_logs[$is_exist]['count'] += 1;
            $date = strftime('%d.%m.%Y', $lg->time);
        }else{
            if($dev->type == 9){
                $alarm_logs[$manual_index]['time'] =  strftime('%H:%M | %d.%m.%Y', $lg->time);
                $alarm_logs[$manual_index]['devs'] = $pname." \ ".$rname."<br>".$dev_name;
                $alarm_logs[$manual_index]['did'] = $lg->did;
                $alarm_logs[$manual_index]['devid'] = $dev->did;
                $alarm_logs[$manual_index]['sid'] = $lg->sid;
                $alarm_logs[$manual_index]['type'] = $dev_type;
                if($pic_time !=0){
                    $alarm_logs[$manual_index]['pics'] = array($pic_time);
                    $alarm_logs[$manual_index]['ptimes'] = array(strftime("%d.%m.%Y - %H:%M:%S",$pic_time));
                }
                $alarm_logs[$manual_index]['count'] = 1;
            }
            else{
                $alarm_logs[$manual_index]['time'] =  strftime('%H:%M | %d.%m.%Y', $lg->time);
                $alarm_logs[$manual_index]['devs'] = $pname." \ ".$rname."<br>".$dev_name;
                $alarm_logs[$manual_index]['did'] = $lg->did;
                $alarm_logs[$manual_index]['devid'] = $dev->did;
                $alarm_logs[$manual_index]['sid'] = $lg->sid;
                $alarm_logs[$manual_index]['type'] = $dev_type;
                $alarm_logs[$manual_index]['count'] = 1;
            }
            

            $manual_index +=1;
            $date = strftime('%d.%m.%Y', $lg->time);
        }



    }


    
    $arr = array('status' => 'OK', 'logs' => $alarm_logs);
    echo json_encode($arr);

    
}

    

?>
