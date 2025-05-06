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
            
            if($m_uid == $usid){ /* Sorgu yapan kullanıcı Ana Kullanıcıysa */
                
                $devices = DB::get(
                    'select id,did,type,name,status,pid,rid from devices where sub = 0 and (type = 0 or type = 8 or type = 9) and uid = ?', 
                    array($m_uid)
                );
                
                foreach($devices as $key => $dev){
                    $pname = DB::getVar('select name from places where id = ?', array($dev->pid));
                    $rname = DB::getVar('select name from places where id = ?', array($dev->rid));
                    $s_alerts = null;
                    
                    
                    if($dev->type == 8 || $dev->type == 9){
                        if($dev->status == 11){
                            $s_alerts = DB::get('select id,sid from alerts where type = 2 and did = ?', array($dev->did));
                        }
                        else{
                            $s_alerts = null;
                        }
                    }
                    else{
                        $s_alerts = DB::get('select id,sid from alerts where type = 2 and did = ?', array($dev->did));
                    }
                    
                    $s_chnges = DB::getRow('select id,sid,ct from alerts where type = 3 and did = ?', array($dev->did)); 
                    $m_chnges = DB::getRow('select * from alerts where type = 1 and did = ?', array($dev->did));
                    if($dev->type == 9){
                        $instant_photo = DB::getRow('select * from alerts where type = 4 and did = ?', array($dev->did));
                    }
                    if(empty($s_alerts)){$s_alerts=null;}
                    else{
                        foreach($s_alerts as $key2 => $s_al){
                            $dv = DB::getRow('select id,type,name from devices where did = ?', array($s_al->sid));

                            $s_alerts[$key2]->db_id = $s_al->id;
                            $s_alerts[$key2]->id = $dv->id;
                            $s_alerts[$key2]->type = $dv->type;
                            $s_alerts[$key2]->name = $dv->name;
                        }
                    }
                    if(empty($s_chnges)){$s_chnges=null;}
                    else{
                        $del = DB::exec('delete from alerts where id = ?', array($s_chnges->id)); 
                    }
                    if(empty($m_chnges)){$m_chnges=null;}
                    else{ $del = DB::exec('delete from alerts where id = ?', array($m_chnges->id)); }

                    $devices[$key]->pname = $pname;
                    $devices[$key]->rname = $rname;
                    $devices[$key]->s_alerts = $s_alerts;
                    $devices[$key]->s_chnges = $s_chnges;
                    $devices[$key]->m_chnges = $m_chnges->ct;
                    if($dev->type == 9){
                        if(empty($instant_photo)){$devices[$key]->ins_photo=null;}
                        else{
                            $devices[$key]->ins_photo=array(
                                'photo'=>$instant_photo->ct, 
                                'time'=>strftime("%d.%m.%Y - %H:%M:%S",explode(".", $instant_photo->ct)[0])
                            );
                            
                            $del = DB::exec('delete from alerts where id = ?', array($instant_photo->id)); 
                        }
                    }
                      
                }
                
                $arr = array('status' => 'OK', 'datas' => $devices);
                echo json_encode($arr);
                
            }
            else{ /* Sorgu yapan kullanıcı Alt Kullanıcıysa */
                
                $auths = DB::getVar('select authority from users where not master = 0 and id = ?',array($usid));
                $auth_s = explode(',', $auths);
                if(!empty($auth_s)){
                    
                    unset($auth_s[0]);
                    $q_auth = "";
                    foreach($auth_s as $key => $aut){
                        if($key == 1){
                            $q_auth .= ' pid = '.$aut;
                        }
                        else{
                            $q_auth .= ' or pid = '.$aut;
                        }

                    }
                    
                    $devices = DB::get(
                        'select id,did,type,name,status,pid,rid from devices where sub = 0 and (type = 0 or type = 8) and ('.$q_auth.') and uid = ?', 
                        array($m_uid)
                    );
                    
                    foreach($devices as $key => $dev){
                        $pname = DB::getVar('select name from places where id = ?', array($dev->pid));
                        $rname = DB::getVar('select name from places where id = ?', array($dev->rid));

                        $s_alerts = DB::get('select sid from alerts where type = 2 and did = ?', array($dev->did));
                        $s_chnges = DB::getRow('select id,sid,ct from alerts where type = 3 and did = ?', array($dev->did)); 
                        $m_chnges = DB::getRow('select * from alerts where type = 1 and did = ?', array($dev->did));

                        if(empty($s_alerts)){$s_alerts=null;}
                        else{
                            foreach($s_alerts as $key2 => $s_al){
                                $dv = DB::getRow('select id,type,name from devices where did = ?', array($s_al->sid));

                                $s_alerts[$key2]->id = $dv->id;
                                $s_alerts[$key2]->type = $dv->type;
                                $s_alerts[$key2]->name = $dv->name;
                            }
                        }
                        if(empty($s_chnges)){$s_chnges=null;}
                        else{
                            $del = DB::exec('delete from alerts where id = ?', array($s_chnges->id)); 
                        }
                        if(empty($m_chnges)){$m_chnges=null;}
                        else{ $del = DB::exec('delete from alerts where id = ?', array($m_chnges->id)); }

                        $devices[$key]->pname = $pname;
                        $devices[$key]->rname = $rname;
                        $devices[$key]->s_alerts = $s_alerts;
                        $devices[$key]->s_chnges = $s_chnges;
                        $devices[$key]->m_chnges = $m_chnges->ct;

                    }

                    $arr = array('status' => 'OK', 'datas' => $devices);
                    echo json_encode($arr);

                    
                }


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

    $arr1 = array('status' => 'NO', 'msg' => 'Yetkisiz İşlem', 'logout' => true);
    echo json_encode($arr1); 

}

?>
