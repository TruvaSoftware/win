<?php

require_once('../src/config.php');

$ins1 = DB::insert('insert into users (email, name, lastname, username, password, birth, cellphone) values (?, ?, ?, ?, ?, ?, ?)', array('fatih@tekin.com', 'Fatih', 'Tekin', 'ftekin', '1234abcd', '1991-05-28', '5444444444'));


?>
