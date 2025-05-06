<?php

$id = $_GET['id'];
$hash = md5("Tekno".$id."Win");
echo $hash;

?>
