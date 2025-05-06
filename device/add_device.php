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

    if(@$_SERVER['REQUEST_METHOD']=='POST'){

        if(!empty($m_uid)){
            
            $default_status = array( 0 => 23, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 11, 9 => 11, 10 => 1 );
            $default_s_time = array( 0 => 15, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 1, 9 => 1, 10 => 0 );
            $default_dt1 = array( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 1, 9 => 1, 10 => 0 );
            
            $did = $query->{'did'}; 
            $typ = $query->{'typ'}; 
            $cod = $query->{'cod'}; 
            $pid = $query->{'pid'};
            $rid = $query->{'rid'};
            $mid = $query->{'mid'};
            $nme = $query->{'nme'};

            if(!empty($did) && ((empty($typ) && $typ==0) || !empty($typ)) && !empty($cod) && !empty($pid)){
                                
                $hash = md5("Tekno".$did."Win".$did."2019");
                
                if($cod == $hash){
                    
                    $dv_exist =  DB::getRow('select * from devices where did = ?', array($did));
                    if(empty($dv_exist)){

                        $ins  = DB::insert(
                            'insert into devices (sub, uid, pid, rid, type, did, name, status, s_time, dt1) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                            array($mid, $m_uid, $pid, $rid, $typ, $did, $nme, $default_status[$typ], $default_s_time[$typ], $default_dt1[$typ])
                        );
                        
                        if($typ!=0 && $typ!=8 && $typ!=9){
                            $master_did = DB::getVar('select did from devices where sub = 0 and id = ?', array($mid));
                            sendCommand('n'.$cmd_types[$typ].$cmd_types2[1], $master_did, $did);
                        }
                        
                        $arr = array('status' => 'OK');
                        echo json_encode($arr);

                    }
                    else{

                        $arr = array('status' => 'NO', 'error' => 'Bu cihaz zaten eklenmiş durumda.');
                        echo json_encode($arr);
                    }
                    
                }
                
            }
            else{
                
                $arr = array('status' => 'NO');
                echo json_encode($arr);
                
            }
            
                
        }
        else{
            $arr = array('status' => 'NO', 'error' => 'no-uid');
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
