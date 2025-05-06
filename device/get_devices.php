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
$p_id = $query->{'pid'};
$r_id = $query->{'rid'};
    
    if(!empty($p_id) && !empty($r_id)){
        $masters = DB::get(
            'SELECT * FROM devices WHERE ( sub = 0 OR sub = 8 ) AND pid = ? AND rid = ?',
            array($p_id, $r_id)
        );
        foreach($masters as $key => $master){
            $slaves = DB::get(
                'SELECT id,name,type FROM devices WHERE sub = ?',
                array($master->id)
            );
            $data[$key]['id'] = $master->id;
            $data[$key]['name'] = $master->name;
            $data[$key]['type'] = $master->type;
            $data[$key]['sensors'] = $slaves;
            
            
        }
        
        $pname = DB::getVar(
            'SELECT name FROM places WHERE id = ?',
            array($p_id)
        );
        
        $rname = DB::getVar(
            'SELECT name FROM places WHERE id = ?',
            array($r_id)
        );
        
        $arr1 = array('status' => 'OK','pname'=>$pname, 'rname'=>$rname, 'devices'=>$data);
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
