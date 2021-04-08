<?php
require_once 'oop.php';

$current = "new";
//$current = "old";
$offline = true;
if($offline==false){
    echo "На сайте ведутся технические работы";
}else{
    if($current=="new"){
    require_once 'new.php';
}else{
    require_once 'old.php';
}
}
?>
 