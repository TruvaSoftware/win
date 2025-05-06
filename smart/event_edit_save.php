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

    $event_id = $query->{'eid'};
    $ev_name = $query->{'event-name'};
    $dev_slct = $query->{'dev-slct'};
    $sen_slct = $query->{'sen-slct'};
    $dev_swtchs = $query->{'s-switchs'};
        
    if(empty($ev_name)){ $err['event-edit-name'] = "Olay adı boş bırakılamaz"; }
    if(empty($dev_slct)){ $err['dev-edit-slct'] = "Lütfen cihaz seçiniz"; }
    if(empty($dev_swtchs)){ $err['smart-edit-switchs'] = "En az 1 adet Akıllı Cihaz seçmelisiniz."; }
    
    
    
    if(empty($err)){
        
        $dev = DB::getRow('select id,pid,type from devices where id = ?', array($dev_slct));

        $upp  = DB::exec(
            'update smart set name = ?, did = ?, sid = ?, pid = ? where id = ?', 
            array($ev_name, $dev_slct, $sen_slct, $dev->pid, $event_id)
        );

        $event_status = DB::getVar('select status from smart where id = ?', array($event_id));

        $del2 = DB::exec('delete from smart_det where sma = ?', array($event_id));



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
                    array($event_id, $sid->id, $s_id, $s_vl, $event_status)
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
    
    


}

/*
*   Giriş kontrolü başarısızsa.
*/
else{
    
    $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
    echo json_encode($arr1); 
    
}

?>
