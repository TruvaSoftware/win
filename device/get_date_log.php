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

    $query_date = strtotime($query->{'date'});
    $date_stop =$query_date+86400;
    
    $logs =  DB::get('select * from alarm_logs where uid = ? and time >= ? and time < ? ORDER BY time DESC', array($m_uid,$query_date,$date_stop));
    setlocale(LC_ALL,"turkish");
    $manual_index = 0;
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

        $is_exist = searchIn(strftime('%H:%M | %d.%m.%Y', $lg->time), $lg->did, $lg->sid, $alarm_logs);
        if(!is_null($is_exist)){
            $alarm_logs[$is_exist]['count'] += 1;
        }else{
            $alarm_logs[$manual_index]['time'] =  strftime('%H:%M | %d.%m.%Y', $lg->time);
            $alarm_logs[$manual_index]['devs'] = $pname." \ ".$rname."<br>".$dev_name;
            $alarm_logs[$manual_index]['did'] = $lg->did;
            $alarm_logs[$manual_index]['sid'] = $lg->sid;
            $alarm_logs[$manual_index]['type'] = $dev_type;
            $alarm_logs[$manual_index]['count'] = 1;

            $manual_index +=1;
        }

        


    }
    
    $arr = array('status' => 'OK', 'logs' => $alarm_logs);
    echo json_encode($arr);
}

    

?>
