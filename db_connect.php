<?php
require "libs/rb.php";
R::setup( 'mysql:host=localhost;dbname=user5990_cp',
        'user5990_aza', '180323' );  
$conn = new PDO('mysql:host=localhost;dbname=user5990_cp', 'user5990_aza', '180323');

$mysqli = new mysqli("localhost","user5990_aza","180323","user5990_cp");
$mysqli->query("SET NAMES 'utf8'");
//session_start();
//  $mysqli = new mysqli("localhost","user5990_aza","180323","user5990_77");
//  $mysqli->query("SET NAMES 'utf8'");
?>