<?php
/*
$image = "records/4071318052/1591888081.jpg";
$a = getimagesize($image);
echo "Genişlik:".$a[0]."<br>";
echo "Yükseklik:".$a[1];

$devs = glob("records/*");
foreach($devs as $key => $dev){
    $devs[$key] = explode("/", $dev)[1];
}
echo json_encode($devs);


unlink('deneme.jpg');


$pics = glob("records/818214396/*");
$count = count($pics);

if($count>30){
    for($i=0;$i<$count-30;$i++){
        unlink($pics[$i]);
    }
}
*/

if (file_exists("../../records/4071318052/1592493123.jpg")) {
    echo "var";
}
else
{
    echo "yok";
}
?>