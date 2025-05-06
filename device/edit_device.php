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
            
            $id = $query->{'dev-id'};   $name = $query->{'dev-name'};
            
            if(!empty($id) && !empty($name)){
                
                $dev = DB::getRow('select * from devices where id = ?', array($id));
                
                if($dev->uid == $m_uid){
                    
                    $upp  = DB::exec('update devices set name = ? where id = ?', array($name, $id));

                    if($dev->sub==0){
                        $mid = $dev->id;
                    }
                    else{
                        $mid = $dev->sub;
                    }
                    
                    $arr = array('status' => 'OK', 'id' => $mid, 'pid' => $dev->pid, 'rid'=>$dev->rid);
                    echo json_encode($arr);
                    
                }
                else{

                    $arr = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem');
                    echo json_encode($arr); 

                }

            }
            else{
                $arr = array('status' => 'NO', 'error' => 'no-sid-name');
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
