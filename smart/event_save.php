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

    $ev_name = $query->{'event-name'};
    $dev_slct = $query->{'dev-slct'};
    $sen_slct = $query->{'sen-slct'};
    $dev_swtchs = $query->{'s-switchs'};
    
    if(empty($ev_name)){ $err['event-name'] = "Olay adı boş bırakılamaz"; }
    if(empty($dev_slct)){ $err['dev-slct'] = "Lütfen cihaz seçiniz"; }
    if(empty($dev_swtchs)){ $err['smart-switchs'] = "En az 1 adet Akıllı Cihaz seçmelisiniz."; }

    if(empty($err)){
        
        $dev = DB::getRow('select id,pid,type from devices where id = ?', array($dev_slct));

        $sma = DB::insert(
            'INSERT INTO smart (uid, name, did, sid, pid, status) VALUES (?, ?, ?, ?, ?, ?)',
            array($m_uid, $ev_name, $dev_slct, $sen_slct, $dev->pid, 1)
        );    

        if($sen_slct == 0){
            $sids = DB::get(
                'select id from devices where sub = ?', 
                array($dev_slct)
            );

            if($dev->type==8){
                array_push($sids, (object) array( 'id' => $dev->id));

            }
            else if($dev->type==0){

            }
        }
        else{
            $sids = array(); 
            array_push($sids, (object) array('id' => $sen_slct));
        }

        foreach($sids as $sid){//$sid->id;

            $switchs = explode('|', $dev_swtchs);

            foreach($switchs as $sw){
                $s_arr = explode('-', $sw);
                $s_id = $s_arr[0];
                $s_vl = $s_arr[1];

                $sm_det = DB::insert(
                    'INSERT INTO smart_det (sma, did, smart_id, data, status) VALUES (?, ?, ?, ?, ?)',
                    array($sma, $sid->id, $s_id, $s_vl, 1)
                );  
            }
        }

        $arr1 = array('status' => 'OK');
        echo json_encode($arr1);  
        
    }
    else{
        $arr = array('status' => 'NO', 'error'=> $err);
        echo json_encode($arr);
    }
    
    
    

    
    
    /*
    $dev = get('select id,pid,type from devices where id = ?', array($dev_slct));
    
    $sma = DB::insert(
        'INSERT INTO smart (uid, name, pid, status) VALUES (?, ?, ?, ?)',
        array($m_uid, $ev_name, $dev->pid, 1)
    );
    
    $switchs = explode('|', $dev_swtchs);
    
    $devss = array();
    
    if($sen_slct == "all"){
        $sids = get('select id from devices where sub = ?', array($dev_slct));
        if(empty($sids)){
            if($dev->type==8){
                // Smart Motiona ekle
                array_push($devss, $dev->id);
            }
            else{
                echo "NO";
            }
        }
        else{
            if($dev->type==8){
                // Smart Motion'u da ekle
                array_push($devss, $dev->id);
                array_push($devss, $sids);
            }
            else{
                // Alarm sensörüne ekle
                array_push($devss, $sids);
            }
        }
    }
    print_r($devss);
    
    
    foreach($switchs as $s){
        $s_arr = explode('-', $s);
        $s_id = $s_arr[0];
        $s_vl = $s_arr[1];
        $sm_det = DB::insert(
            'INSERT INTO smart_det (sma, did, smart_id, data, status) VALUES (?, ?, ?, ?)',
            array($sma, , $dev->pid, 1)
        );  
    }
    */

}

/*
*   Giriş kontrolü başarısızsa.
*/
else{
    
    $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
    echo json_encode($arr1); 
    
}

?>
