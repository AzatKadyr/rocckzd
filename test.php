<?php
require_once 'oop.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);
file_put_contents(__DIR__ . '/message.txt', print_r($data, true));



$proverka = pingCheck("https://web.umag.kz/");
if($proverka==200){
    //sendBot("Сайт работает в штатном режиме");
}else{
    if($proverka==500){
    sendBot("Неполадки на сайте, ошибка #".$proverka);
    }else{
        if($proverka==402){
        sendBot("Неполадки на сайте, ошибка #".$proverka);
        }else{
        sendBot("Сайт недоступен, неизвестная ошибка! ");
        }
    }

}

?>