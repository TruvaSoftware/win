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
    if(isset($query->{'did'})){
        $did = $query->{'did'};
        $logs =  DB::get('select * from alarm_logs where uid = ? and did = ? ORDER BY time DESC', array($m_uid, $did));
    }
    else{
        $logs =  DB::get('select * from alarm_logs where uid = ? ORDER BY time DESC', array($m_uid));
    }
    
    if(!empty($logs)){
        
        setlocale(LC_ALL,"turkish");
        $manual_index = 0;
        $date = "";
        $last_date = strftime('%d.%m.%Y', $logs[0]->time);
        foreach($logs as $key => $lg){
            if(strftime('%d.%m.%Y', $lg->time) == $last_date){


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
                    $alarm_logs[$manual_index]['sid'] = null;
                    $alarm_logs[$manual_index]['type'] = null;
                    $alarm_logs[$manual_index]['count'] = null;
                    $manual_index +=1;
                }

                $is_exist = searchIn(strftime('%H:%M | %d.%m.%Y', $lg->time), $lg->did, $lg->sid, $alarm_logs);
                if(!is_null($is_exist)){
                    $alarm_logs[$is_exist]['count'] += 1;
                    $date = strftime('%d.%m.%Y', $lg->time);
                }else{
                    $alarm_logs[$manual_index]['time'] =  strftime('%H:%M | %d.%m.%Y', $lg->time);
                    $alarm_logs[$manual_index]['devs'] = $pname." \ ".$rname."<br>".$dev_name;
                    $alarm_logs[$manual_index]['did'] = $lg->did;
                    $alarm_logs[$manual_index]['sid'] = $lg->sid;
                    $alarm_logs[$manual_index]['type'] = $dev_type;
                    $alarm_logs[$manual_index]['count'] = 1;

                    $manual_index +=1;
                    $date = strftime('%d.%m.%Y', $lg->time);
                }

            }
            else{

                if(strftime('%d.%m.%Y', $lg->time) != $date){
                    $alarm_logs[$manual_index]['time'] =  strftime('%d.%m.%Y', $lg->time);
                    $alarm_logs[$manual_index]['devs'] = null;
                    $alarm_logs[$manual_index]['did'] = null;
                    $alarm_logs[$manual_index]['sid'] = null;
                    $alarm_logs[$manual_index]['type'] = null;
                    $alarm_logs[$manual_index]['count'] = null;
                    $manual_index +=1;
                    $date = strftime('%d.%m.%Y', $lg->time);
                }

            }


        }


    }
    else{
        $alarm_logs = null;
    }
    
    
    $arr = array('status' => 'OK', 'logs' => $alarm_logs);
    echo json_encode($arr);

    
}

    

?>
