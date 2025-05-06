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

    $did = $query->{'did'};
    
    if(!empty($did)){
        $master = DB::getRow('SELECT * FROM devices WHERE id = ?', array($did));
        
        $pname = DB::getVar('SELECT name FROM places WHERE id = ?', array($master->pid));
        $rname = DB::getVar('SELECT name FROM places WHERE id = ?', array($master->rid));

        $devices = DB::get('select id,sub,did,name,type,status from devices where not sub = 0 and sub = ?', array($did));    

        $arr1 = array(
            'status' => 'OK',
            'pid'=>$master->pid, 
            'pname'=>$pname, 
            'rid'=>$master->rid, 
            'rname'=>$rname, 
            'dname'=>$master->name, 
            'type' => $master->type, 
            'dev_status'=>$master->status, 
            'data' => $devices
        );
        echo json_encode($arr1);
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
