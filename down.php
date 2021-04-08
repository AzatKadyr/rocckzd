<?php
$table['type'] = "line";
$table['data']['labels'] = "Понедельник";
$table['data']['labels'] = "Понедельник";
$table['data']['labels'] = "Понедельник";
$table['data']['labels'] = "Понедельник";
$table['data']['datasets']['data'] = "2500";
$table['data']['datasets']['data'] = "2600";
$table['data']['datasets']['data'] = "2800";
$table['data']['datasets']['data'] = "2900";
$table['data']['datasets']['lineTension'] = 0;
$table['data']['datasets']['backgroundColor'] = "transparent";
$table['data']['datasets']['borderColor'] = "#007bff";
$table['data']['datasets']['borderWidth'] = 4;
$table['data']['datasets']['pointBackgroundColor'] = "#007bff";
$table['data']['options']['scales']['yAxes']['ticks']['beginAtZero'] = "false";
$table['data']['options']['scales']['legend']['display']= "false";

print_r();
echo json_encode($table);
?>