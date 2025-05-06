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
                 
            $sid = $query->{'sid'}; 
            $typ = $query->{'typ'}; 
            $cod = $query->{'cod'}; 
            $mid = $query->{'mid'};

            if(!empty($sid) && ((empty($typ) && $typ==0) || !empty($typ)) && !empty($cod) && !empty($mid)){
                                    
                $dv_exist =  DB::getRow('select * from devices where did = ?', array($sid));
                if(empty($dv_exist)){
                    $master = DB::getRow('select * from devices where sub = 0 and id = ?', array($mid));

                    $ins  = DB::insert('insert into devices (sub, uid, type, did, name, status, s_time) values (?, ?, ?, ?, ?, ?, ?)', array($master->id, $master->uid, $typ, $sid, $dev_types[$typ].$sid, 1, 0));
					
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
