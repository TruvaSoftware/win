<?php
header("Cache-Control: private"); // HTTP/1.1
header("Content-type: text/plain;charset=utf-8");
header("Content-Length: 100");
date_default_timezone_set('Europe/Istanbul');
setlocale(LC_MONETARY, 'tr_TR');

if($_GET['key'] == "*Tekno*?0?*Win*"){
    
    define('MSSQL_HOST',    'localhost');
    define('MSSQL_DB',      'teknow1_alarm');
    define('MSSQL_USER',    'teknow1_user');
    define('MSSQL_PASS',    'xomv3uDh');
    
    include '../src/db.php';

    $chk = "ss";
    while(!empty($chk)) {
        $result  =  rand(100000000, 999999999);
        $chk = DB::getVar(
            'select id from id_pool where sensor_id = ?', 
            array($result)
            );
    }
    $ins  = DB::insert(
        'insert into id_pool (sensor_id) values (?)', 
        array($result)
        );

    echo 'sid'.$result."\n";
    
}
else{
    
    echo "Hata: Anahtar kodu olmadan işlem yapılamaz.";
    
}

?>
