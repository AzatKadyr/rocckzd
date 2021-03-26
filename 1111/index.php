<?php

$xml = simplexml_load_file('http://dev.rocc.kz/1111/66.xml');
//echo $xml->REST_NUMBER; //выведет 'Коля'
//echo $xml->age; //выведет 25
//echo $xml->salary; //выведет 1000
print_r($xml['sale'][0]);
?>