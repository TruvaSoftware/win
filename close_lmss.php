<?php
require_once('../src/config.php');
if(@$_SERVER['REQUEST_METHOD']=='POST'){
    if($_POST['key']=="*?Tek20no19Win?*"){
        $did = "6ee5aa";
        $uid = DB::getVar('select uid from devices where did = ?', array($did));
                
        $smarts = DB::get('select * from smarts where master_id = ?', array($uid));
        
        foreach($smarts as $s){
            $s_dev =  DB::getRow('select * from teknow1_s1.tw_userdev where id = ?', array($s->smart_id));
            $ch = (int)$s_dev->ch;
            for($i=1;$i<=$ch;$i++){
                $ins  = DB::insert('insert into teknow1_s1.tw_status (did, udid, ch, status) values (?, ?, ?, ?)', array($s_dev->did, 0, (int)$i, 1));

            }
        }
    }
    
}

?>
