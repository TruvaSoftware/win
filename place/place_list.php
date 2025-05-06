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

    if($m_uid == $usid){

        $mid = $query->{'mid'};
        
        if(!empty($mid)){
            $places = DB::get(
                'SELECT * FROM places WHERE sub = 0 AND uid = ?',
                array($mid)
            );
            foreach($places as $key => $place){
                $rooms = DB::get(
                    'SELECT id,name FROM places WHERE sub = ?',
                    array($place->id)
                );
                $alarms = DB::get(
                    'SELECT id,name FROM devices WHERE sub = 0 and rid = 0 and pid = ?',
                    array($place->id)
                );
                $data[$key]['id'] = $place->id;
                $data[$key]['name'] = $place->name;
                $data[$key]['rooms'] = $rooms;
                $data[$key]['alarms'] = $alarms;

            }


            $arr1 = array('status' => 'OK', 'places'=>$data);
            echo json_encode($arr1);
        }
    }
    else{
        $mid = $query->{'mid'};
        $auths = DB::getVar('select authority from users where not master = 0 and id = ?',array($usid));
        $auth_s = explode(',', $auths);
        unset($auth_s[0]);
        if(!empty($auth_s)){
            $q_auth = "";
            foreach($auth_s as $key => $aut){
                if($key == 1){
                    $q_auth .= ' id = '.$aut;
                }
                else{
                    $q_auth .= ' or id = '.$aut;
                }

            }

            $places = DB::get(
                'SELECT * FROM places WHERE sub = 0 AND ('.$q_auth.') AND uid = ?',
                array($mid)
            );
            
            foreach($places as $key => $place){
                $rooms = DB::get(
                    'SELECT id,name FROM places WHERE sub = ?',
                    array($place->id)
                );
                $alarms = DB::get(
                    'SELECT id,name FROM devices WHERE sub = 0 and rid = 0 and pid = ?',
                    array($place->id)
                );
                $data[$key]['id'] = $place->id;
                $data[$key]['name'] = $place->name;
                $data[$key]['rooms'] = $rooms;
                $data[$key]['alarms'] = $alarms;

            }


            $arr1 = array('status' => 'OK', 'places'=>$data);
            echo json_encode($arr1);
            
        }else{
            $arr1 = array('status' => 'OK', 'places'=>null);
            echo json_encode($arr1);
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
