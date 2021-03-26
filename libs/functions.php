<?php
session_start();
//Подключение к бд
$mysqli = new mysqli("localhost","user5990_aza","180323","user5990_rb");
$mysqli->query("SET NAMES 'utf8'");
//Библиотеки
include "libs/qr/qrlib.php";
require_once 'db_connect.php';
require_once 'libs/Api2Pdf.php';
require_once 'libs/ApiResult.php';
use Api2Pdf\Api2Pdf;

if($_COOKIE['authsite']=="dev.rocc.kz"){

}else{
  //header('Location: /logout.php ');
}

//rocc.kz

function getRocc() {
    
  global $mysqli;
  $reportid = $_COOKIE['r_reportid'];

  $res=$mysqli->query("SELECT roccreport.restid,roccreport.date,roccreport.userid,roccreport.reportid,rocc_rest.restid,rocc_rest.restname,roccuser.userid,roccuser.name FROM roccreport,rocc_rest,roccuser WHERE roccreport.reportid=$reportid AND roccreport.restid=rocc_rest.restid AND roccreport.userid=roccuser.userid");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
}

function getRest() {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM rocc_rest");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
}

  function startReport($iduser,$restid,$date,$report)  
      {  
        $user = R::dispense('roccreport');
        $user->reportid=$report;
        $user->restid=$restid;
        $user->userid=$iduser;
        $user->date=$date;
        $user->complete="1";
        R::store($user);
        $h1 = "OK";


        setcookie('r_reportid', $report, time() + (-86400 * 5), '/', 'rocc.kz');
        setcookie('r_userid', $iduser, time() + (-86400 * 5), '/', 'rocc.kz');
        setcookie('r_restid', $restid, time() + (-86400 * 5), '/', 'rocc.kz');
        setcookie('r_date', $date, time() + (-86400 * 5), '/', 'rocc.kz');

        setcookie('r_reportid', $report, time() + (86400 * 5), '/', 'rocc.kz');
        setcookie('r_userid', $iduser, time() + (86400 * 5), '/', 'rocc.kz');
        setcookie('r_restid', $restid, time() + (86400 * 5), '/', 'rocc.kz');
        setcookie('r_date', $date, time() + (86400 * 5), '/', 'rocc.kz');
        return $user;  
}

function generate_pass($number)  
      {  
        $arr = array('1','2','3','4','5','6',  
                     '7','8','9','0');  
                      
        // Генерируем пароль для смс  
        $pass = "";  
        for($i = 0; $i < $number; $i++)  
        {  
          // Вычисляем произвольный индекс из массива  
          $index = rand(0, count($arr) - 1);  
          $pass .= $arr[$index];  
        }  
        return $pass;  
      }
      
//rocc.kz

/// Старье

 function authUser($login,$password){
        
        $user = getUserById($login);
        global $glob;
        if($user==null){
            $data['status']="NO";
            $data['message']="Пользователь не найден";
        }else{
            
            if($user['password']===$password){
                if($user['offline']==="1"){
                    $data['status']="PAID";
                    $data['message']="Аккаунт заблокирован, необходимо оплатить подписку! ";
                }else{
                  //$a = createSession($user['userid']);
                    //$c = saveHistoryUser($user['userid'],$_SERVER['HTTP_USER_AGENT'],$_SERVER['REMOTE_ADDR'],$a);
                    $data['status']="OK";
                    $data['message']="Успешно авторизованы";
                    setcookie('auth','ok',time() + (86400 * 5), '/', $glob['settingurl']);
                    setcookie('userid',$user['userid'],time() + (86400 * 5), '/', $glob['settingurl']);
                    setcookie('companyid',$user['companyid'],time() + (86400 * 5), '/', $glob['settingurl']);
                    setcookie('vac',$user['vac'],time() + (86400 * 5), '/', $glob['settingurl']);
                    setcookie('block',"1",time() + (86400 * 5), '/', $glob['settingurl']);
                }
            }else{
             $data['status']="NO";
             $data['message']="Неверный пароль";
            }
        }

        return $data;
    }

   
function getUserByIda($login){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gameuser ORDER BY `userid` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            if($users[$i]['phone']===$login){
                return $users[$i];
            }
        }

        return null;
    }

function authUserB($login,$password){
        
        global $glob;
        $user = getUserByIda($login);
        
        if($user==null){
            $data['status']="NO";
            $data['message']="Пользователь не найден";
        }else{
            
            if($user['password']===$password){
                if($user['offline']==="1"){
                    $data['status']="PAID";
                    $data['message']="Аккаунт заблокирован, необходимо оплатить подписку! ";
                }else{
                  //$a = createSession($user['userid']);
                    //$c = saveHistoryUser($user['userid'],$_SERVER['HTTP_USER_AGENT'],$_SERVER['REMOTE_ADDR'],$a);
                    $data['status']="OK";
                    $data['message']="Успешно авторизованы";
                    
                    setcookie('auth','ok',time() + (86400 * 5), '/', $glob['settingurl']);
                    setcookie('userid',$user['userid'],time() + (86400 * 5), '/', $glob['settingurl']);
                    setcookie('companyid',$user['companyid'],time() + (86400 * 5), '/', $glob['settingurl']);
                    setcookie('vac',$user['vac'],time() + (86400 * 5), '/', $glob['settingurl']);
                    setcookie('block',"1",time() + (86400 * 5), '/', $glob['settingurl']);
                }
            }else{
             $data['status']="NO";
             $data['message']="Неверный пароль";
             //$data['aza'] = $user;
             //$data['azat'] = $password;
            }
        }

        return $data;
    }

function authUserA($login,$password){
        
        global $glob;
        $user = getUserById($login);
        
        if($user==null){
            $data['status']="NO";
            $data['message']="Пользователь не найден";
        }else{
            
            if($user['password']===$password){
                if($user['offline']==="1"){
                    $data['status']="PAID";
                    $data['message']="Аккаунт заблокирован, необходимо оплатить подписку! ";
                }else{
                  //$a = createSession($user['userid']);
                    //$c = saveHistoryUser($user['userid'],$_SERVER['HTTP_USER_AGENT'],$_SERVER['REMOTE_ADDR'],$a);
                    $data['status']="OK";
                    $data['message']="Успешно авторизованы";
                    setcookie('block',"1",time() + (86400 * 5), '/', $glob['settingurl']);
                }
            }else{
             $data['status']="NO";
             $data['message']="Неверный пароль";
            }
        }

        return $data;
    }

function logOut(){
        global $glob;
                    setcookie('auth','ok',time() + (-86400 * 5), '/', $glob['settingurl']);
                    setcookie('userid',$user['userid'],time() + (-86400 * 5), '/', $glob['settingurl']);
                    setcookie('companyid',$user['companyid'],time() + (-86400 * 5), '/', $glob['settingurl']);
                    setcookie('vac',$user['vac'],time() + (-86400 * 5), '/', $glob['settingurl']);
                    setcookie('block',"1",time() + (-86400 * 5), '/', $glob['settingurl']);

        return $data['status']="OK";
    }

function blockCass(){
        
        global $glob;
                    setcookie('block',"1",time() + (-86400 * 5), '/', $glob['settingurl']);

        return $data['status']="OK";
    }

function getUserById($userid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gameuser ORDER BY `userid` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            if($users[$i]['userid']===$userid){
                return $users[$i];
            }
        }

        return null;
    }



function createSession($userid){

    $aza = generate_pass(11);
    $b = getUserById($userid);

    $book = R::load('users', $b['id']);
            $book->sessionid = $aza;
            R::store($book);

    return $aza;

    }

function getTrial(){

    $companyid = $_COOKIE['companyid'];
    $userid = $_COOKIE['userid'];
    $datea = date("Y-m-d");
    $timea = date("H:i:s");
    $aza = getTarifCompany($companyid);
    $balans = $aza['price']*14;
    $proverka = proverkaTrial($companyid);
    if($proverka['status']=="NO"){
            $data['status'] = "NO";
    }else{
            $book = R::dispense('gamestrial');
            $book->datein = $datea;
            $book->timein = $timea;
            $book->companyid = $companyid;
            $book->userid = $userid;
            
            R::store($book);

            $book = R::dispense('gamesplateji');
            $book->companyid = $companyid;
            $book->value = round($balans);
            $book->type = "in";
            $book->comment = "Демо доступ на 14 дней";
            $book->datea = $datea;
            $book->timea = $timea;
            R::store($book);

            $book = R::load('gamescompany', $companyid);
            $book->block = "2";
            R::store($book);
            $text = "Баланс пополнен на сумму ".round($balans)."тг";
            newNtf("Демо доступ","Пробный период на 14 дней активирован","Пробный период на 14 дней активирован","1",$_COOKIE['userid'],'1');
            newNtf("Демо доступ",$text,$text,"1",$_COOKIE['userid'],'1');

            $data['status'] = "OK";
    }
            

    return $data;

    }

function getTableB() {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT tablegame.id,tablegame.tablename,tablegame.status,tablegame.tarif,tablegame.sessionid,gamestatus.statusid,gamestatus.statustext,gamesession.sessionid,gamesession.comment,gamesession.datein,gamesession.timein,gamesession.dateout,gamesession.timeout,gamesession.tableid FROM tablegame,gamestatus,gamesession WHERE tablegame.sessionid=gamesession.sessionid AND tablegame.status=gamestatus.statusid");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
}

function getGlobalSetting() {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM gamessettings WHERE id=3");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data[0];
}

function proverkaTrial($companyid) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM gamestrial WHERE companyid=$companyid");
    foreach($res as $value){      
      $data[] = $value;
    }
    
    if($data==null){
      $data['status'] = "OK";
    }else{
      $data['status'] = "NO"; 
    }
    return $data; 
}


function getTable() {
    
  global $mysqli;
$companyid = $_COOKIE['companyid'];
  $res=$mysqli->query("SELECT tablegame.id,tablegame.tablename,tablegame.status,tablegame.tarif,tablegame.sessionid,gamestatus.statusid,gamestatus.statustext,gamestatus.css FROM tablegame,gamestatus WHERE tablegame.status=gamestatus.statusid AND tablegame.companyid=$companyid ORDER BY tablegame.tablename ASC ");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
}

function getCompanyAll() {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM gamescompany");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
}

function getTableId($id) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM tablegame WHERE id=$id");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data[0];
}

function getOrder($id) {
    
  if($id==null){
    return null;
  }else{
    global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM gamesession,gamespokupatel WHERE gamesession.id=$id AND gamesession.pokupatel=gamespokupatel.ctrid");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
  }
}

function updateBd($id,$type,$sessionid){

    if($type=="start"){

        $book = R::load('tablegame', $id);
            $book->sessionid = $sessionid;
            $book->status = "2";
            R::store($book);

    }

    if($type=="end"){
      
        $book = R::load('tablegame', $id);
            $book->sessionid = "";
            $book->status = "1";
            R::store($book);

    }

    return null;

    }


    function startArenda($id,$datestart,$timestart,$comment,$pokupatel){

      if($pokupatel==null){
        $pokupatel = 1;
      }
      $aza = getTableId($id);
      if($aza['status']=="2"){
        $data['status'] = "NO";
            $data['message'] = "Стол занят";
      }else{
        list($a, $b, $c) = split('[/.-]', $datestart);
      list($d, $e, $f) = split('[:.-]', $timestart);
      $g = 0;
      $timer = $a.",".$b.",".$c.",".$d.",".$e.",".$f.",".$g;
      $smenaid = getSmenaId($_COOKIE['companyid']);

        $book = R::dispense('gamesession');
            $book->datein = $datestart;
            $book->timein = $timestart;
            $book->tableid = $id;
            $book->comment = $comment;
            $book->smenaid = $smenaid['id'];
            $book->status = "1";
            $book->payed = "1";
            $book->timer = $timer;
            $book->pokupatel = $pokupatel;
            $book->companyid = $_COOKIE['companyid'];

            R::store($book);

            updateBd($id,"start",$book['id']);
            $data['status'] = "OK";
            $data['message'] = "Успешно";
      }
            
    return $data;

    }

    function addCart($orderid,$productid,$sum,$value,$desc,$vol,$pokupatel,$img){
    $order = getOrder($orderid);
    if($order[0]['payed']=="2"){
        $data['status'] = "NO";
    }else{
       $smenaid = getSmenaId($_COOKIE['companyid']);
        $book = R::dispense('gamescart');
            $book->orderid = $orderid;
            $book->smenaid = $smenaid['id'];
            $book->productid = $productid;
            $book->vol = $vol;
            $book->sum = $sum;
            $book->value = $value;
            $book->desct = $desc;
            $book->payed = "1";
            $book->pokupatel = $pokupatel;
            $book->img = $img;

            R::store($book);
            $data['status'] = "OK"; 
    }
    

    return $data;

    }
        function endArenda($id,$tableid){

      global $datea;
      global $timea;

        $book = R::load('gamesession', $id);
            $book->dateout = $datea;
            $book->timeout = $timea;
            $book->status = "2";
            $book->payed = "1";

            R::store($book);
            //$aza = getFinal($id,$tableid);
            updateBd($tableid,"end",$id);

    return $data;

    }

    function savePay($id,$tableid,$type,$sum,$pokupatelid){

      global $datea;
      global $timea;
      global $settingsite;
      $companyid = $_COOKIE['companyid'];

      $smenaid = getSmenaId($_COOKIE['companyid']);
        

            $book = R::dispense('payed');
            $book->tableid = $tableid;
            $book->gamesessionid = $id;
            $book->typepay = $type;
            $book->smenaid = $smenaid['id'];
            $book->sum = $sum;
            $book->companyid = $companyid;
            $book->datepay = $datea;
            $book->timepay = $timea;
            $book->cashierid = $_COOKIE['userid'];
            $book->ctragentid = $pokupatelid;
            R::store($book);

      if($type=="3"){
            $book = R::dispense('gamesdolg');
            $book->tableid = $tableid;
            $book->gamesessionid = $id;
            $book->typepay = $type;
            $book->smenaid = $smenaid['id'];
            $book->companyid = $companyid;
            $book->sum = "-".$sum;
            $book->datepay = $datea;
            $book->timepay = $timea;
            $book->cashierid = $_COOKIE['userid'];
            $book->ctragentid = $pokupatelid;
            R::store($book);
      }
            $value = "";
            
            if($settingsite['cashback']=="1"){
              $procent = $settingsite['casbackprocent'];
            $kf = $procent/100;
            $value = $sum*$kf;

            $book = R::dispense('gamescashback');
            $book->tableid = $tableid;
            $book->gamesessionid = $id;
            $book->typepay = "13";
            $book->smenaid = $smenaid['id'];
            $book->companyid = $companyid;
            $book->sum = $value;
            $book->datepay = $datea;
            $book->timepay = $timea;
            $book->cashierid = $_COOKIE['userid'];
            $book->ctragentid = $pokupatelid;
            R::store($book);
            }
            $text = "Вам начислено ".$value." бонусов";
            sendWh('77029677226',$text);

        $book = R::load('gamesession', $id);
        $book->payed = "2";
        $book->cashback = $value;
        R::store($book);

            $data['status'] = "OK";
            $data['message'] = "Оплата проведена";

    return $data;

    }

    function savePayDolg($type,$sum,$pokupatelid){

      global $datea;
      global $timea;
      global $settingsite;
      $companyid = $_COOKIE['companyid'];

      $smenaid = getSmenaId($_COOKIE['companyid']);
      $agent = getBalansCtrid($pokupatelid);
      $dolg = $agent['dolg'];
      $str = preg_replace('~^\-~', '', $dolg);
      if($sum > $str){
       $data['status'] = "NO";
            $data['message'] = "Сумма оплаты не можеть быть больше суммы долга!";
          }else{

             $prihodnal = "0";
      $prihodbeznal = "0";
      if($type=="1"){
        $prihodnal = "1";
      }
      if($type=="2"){
        $prihodbeznal = "1";
      }
            $book = R::dispense('gamesdolg');
            $book->tableid = $tableid;
            $book->gamesessionid = $id;
            $book->typepay = "11";
            $book->smenaid = $smenaid['id'];
            $book->sum = $sum;
            $book->datepay = $datea;
            $book->timepay = $timea;
            $book->cashierid = $_COOKIE['userid'];
            $book->ctragentid = $pokupatelid;
            $book->prihodnal = $prihodnal;
            $book->prihodbeznal = $prihodbeznal;
            $book->companyid = $companyid;
            R::store($book);

            $data['status'] = "OK";
            $data['message'] = "Оплата проведена";
            
          }

    return $data;

    }

    function getFinal($id,$tableid){

        $aza = endArenda($id,$tableid);
        $ab = getOrder($id);
        $bb = getTableId($tableid);
      $start_date = new DateTime($ab[0]['datein']." ".$ab[0]['timein']);
      $since_start = $start_date->diff(new DateTime($ab[0]['dateout']." ".$ab[0]['timeout']));
      
      $hour = $since_start->h;
      $minut = $since_start->i;
      //echo $since_start->s.' seconds<br>';
      $minutes = $since_start->days * 24 * 60;
      $minutes += $since_start->h * 60;
      $minutes += $since_start->i;
      if($minutes==0){
        $minutes = 1;
      }
      $money = $minutes*$bb['tarif'];

      $data['hours'] = $hour.":".$minut;
      $data['minutes'] = $minutes;
      $data['money'] = ceil($money/60);
      $data['tarif'] = $bb['tarif'];
      $data['datefinal'] = $ab[0]['dateout']." ".$ab[0]['timeout'];

      $img = "http://new.rocc.kz/img/png/product.png";
      $desc = "Аренда стола №".$tableid."";
      addCart($id,$tableid,$data['tarif'],$data['money'],$desc,$data['hours'],'1',$img);

      return $data;


    }
    
    function getSum($id,$tableid){

        $ab = getOrder($id);
        $bb = getTableId($tableid);
      $start_date = new DateTime($ab[0]['datein']." ".$ab[0]['timein']);
      $since_start = $start_date->diff(new DateTime($ab[0]['dateout']." ".$ab[0]['timeout']));
      
      $hour = $since_start->h;
      $minut = $since_start->i;
      //echo $since_start->s.' seconds<br>';
      $minutes = $since_start->days * 24 * 60;
      $minutes += $since_start->h * 60;
      $minutes += $since_start->i;
      if($minutes==0){
        $minutes = 1;
      }
      $money = $minutes*$bb['tarif'];

      $data['hours'] = $hour.":".$minut;
      $data['minutes'] = $minutes;
      $data['money'] = ceil($money/"60");
      $data['tarif'] = $bb['tarif'];
      $data['datefinal'] = $ab[0]['dateout']." ".$ab[0]['timeout'];

      return $data;


    }

    function getSumOrd($id,$tableid){

        $ab = getOrder($id);
        $bb = getTableId($tableid);
        //date_default_timezone_set('Asia/Almaty');
          $datea = $ab[0]['dateout'];
          $timea = $ab[0]['timeout'];

      $start_date = new DateTime($ab[0]['datein']." ".$ab[0]['timein']);
      $since_start = $start_date->diff(new DateTime($datea." ".$timea));
      
      $hour = $since_start->h;
      $minut = $since_start->i;
      //echo $since_start->s.' seconds<br>';
      $minutes = $since_start->days * 24 * 60;
      $minutes += $since_start->h * 60;
      $minutes += $since_start->i;
      if($minutes==0){
        $minutes = 1;
      }
      $money = $minutes*$bb['tarif'];

      $data['hours'] = $hour.":".$minut;
      $data['minutes'] = $minutes;
      $data['money'] = ceil($money/"60");
      $data['tarif'] = $bb['tarif'];

      return $data;


    }

    function getSpisok($datefilter) {
    
  global $mysqli;
  $companyid = $_COOKIE['companyid'];
  $res=$mysqli->query("SELECT gamesession.id,gamesession.tableid,gamesession.datein,gamesession.timein,gamesession.status,tablegame.id,tablegame.tablename,payed.gamesessionid,payed.typepay,payed.sum,payed.datepay,payed.timepay,gamestatus1.statusid,gamestatus1.statustext,gamestatus1.css,typepay.typeid,typepay.typename FROM gamesession,tablegame,payed,gamestatus1,typepay WHERE payed.companyid=$companyid AND gamesession.tableid=tablegame.id AND gamesession.status=gamestatus1.statusid AND gamesession.id=payed.gamesessionid AND payed.typepay=typepay.typeid AND gamesession.status='2' AND gamesession.datein BETWEEN '$datefilter' AND '$datefilter'");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
}

  function getSpisokSum($datefilter) {
  global $mysqli;
$companyid = $_COOKIE['companyid'];

  $res=$mysqli->query("SELECT sum(payed.sum),gamesession.id,gamesession.tableid,gamesession.datein,gamesession.timein,gamesession.status,tablegame.id,tablegame.tablename,payed.gamesessionid,payed.typepay,payed.sum,payed.datepay,payed.timepay,gamestatus1.statusid,gamestatus1.statustext,gamestatus1.css,typepay.typeid,typepay.typename FROM gamesession,tablegame,payed,gamestatus1,typepay WHERE payed.companyid=$companyid AND gamesession.tableid=tablegame.id AND gamesession.status=gamestatus1.statusid AND gamesession.id=payed.gamesessionid AND payed.typepay=typepay.typeid AND gamesession.status='2' AND gamesession.datein BETWEEN '$datefilter' AND '$datefilter'");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data[0]['sum(payed.sum)'];;
}

function getValue($datefilter) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT sum(value) FROM roccpayuser WHERE userid=$userid");
    foreach($res as $value){      
      $data[] = $value;
    }
    
    $a = $data[0]['sum(value)'];
    if($a.length==0){
      $b = "0";
    }else{
      $b = $a;
    }
    return $b;
}

function getCompany($companyid) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM gameuser WHERE companyid=$companyid");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
}

function getAllUser() {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM gameuser");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data;
}

function getSetting($companyid) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM gamesettings WHERE companyid=$companyid");
    foreach($res as $value){      
      $data[] = $value;
    }

    return $data[0];
}

    function updSetting($id,$sitename,$url,$auth,$cashback,$procent){

      $book = R::load('gamesettings', $id);
            $book->sitename = $sitename;
            $book->settingurl = $url;
            $book->auth = $auth;
            $book->cashback = $cashback;
            $book->casbackprocent = $procent;
      R::store($book);

      $data['status'] = "OK";

      return $data;

    }

function getTarifCompany($companyid) {

  global $mysqli;
    
  $res=$mysqli->query("SELECT * FROM gamescompany WHERE companyid=$companyid");
    foreach($res as $value){      
      $data[] = $value;
    }

  $res=$mysqli->query("SELECT * FROM gamestarif");
    foreach($res as $value){      
      $tarif[] = $value;
    }

  $res=$mysqli->query("SELECT COUNT(*) FROM tablegame WHERE companyid=$companyid");
    foreach($res as $value){      
      $tables[] = $value;
    }

    $res=$mysqli->query("SELECT COUNT(*) FROM gameuser WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

    $res=$mysqli->query("SELECT sum(value) FROM gamesplateji WHERE companyid=$companyid");
    foreach($res as $value){      
      $balns[] = $value;
    }
    
    $a = $balns[0]['sum(value)'];
    if($a.length==0){
      $balans = "0";
    }else{
      $balans = $a;
    }

    $info['id'] = $data[0]['id'];
    $info['block'] = $data[0]['block'];
    $info['tarifusers'] = $data[0]['users'];
    $info['tariftables'] = $data[0]['tables'];
    $info['companyname'] = $data[0]['companyname'];
    $info['faktusers'] = $users[0]['COUNT(*)'];
    $info['fakttables'] = $tables[0]['COUNT(*)'];
    $a = $info['tarifusers'] * $tarif[0]['users'];
    $b = $info['tariftables'] * $tarif[0]['tables'];
    $info['price'] = $a + $b;
    $info['balans'] = $balans;

    return $info;
}

function getPlateji() {

  $companyid = $_COOKIE['companyid'];
  global $mysqli;
    

  $res=$mysqli->query("SELECT * FROM gamesplateji WHERE companyid=$companyid  ORDER BY `id` DESC LIMIT 1000");
    foreach($res as $value){      
      $data[] = $value;
    }
   
    return $data;
}

function getCompanyPlatej(){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamescompany");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            $aza = getTarifCompany($users[$i]['companyid']);
            $spisanie = getSpisanie($users[$i]['companyid'],$aza['price']);
            
        }

        return null;
    }

function getSpisanie($companyid,$sum){

      global $datea;
      global $timea;
      $b = "30";
      $value = $sum / $b;

      $aza = getTarifCompany($companyid);
      if($aza['balans']<$value){

      $book = R::load('gamescompany', $aza['id']);
            $book->block = "1";
      R::store($book);

      }else{

      $book = R::dispense('gamesplateji');
            $book->companyid = $companyid;
            $book->value = "-".round($value);
            $book->type = "out";
            $book->comment = "Списание за тарифный план";
            $book->datea = $datea;
            $book->timea = $timea;
            R::store($book);

            updSystem($companyid);
            $full = "С вашего баланса была списана сумма ".round($value)."тг";
              global $mysqli;

              $res=$mysqli->query("SELECT * FROM gameuser WHERE companyid=$companyid");
              foreach($res as $value){      
                $users[] = $value;
              }

        for($i=0;$i<sizeof($users);$i++){
            newNtf("Системное уведомление","списана сумма за тарифный план",$full,"1",$users[$i]['userid'],'1');
        }

      }
            

            
    return null;

    }

    function proverkaCompany($companyid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamescompany WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

      if($users[0]['block']=="1"){
        $data = "NO";
      }else{
        $data = "OK";
      }
        return $data;
    }

    function updSystem($companyid){
        
    $data = getTarifCompany($companyid);

    $book = R::load('gamescompany', $data['id']);
            $book->block = "2";
      R::store($book);

    return $data;
    }

    function getIdCompany($companyid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamescompany WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $data[0]['id'];
    }

    function getCart($orderid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT orderid,smenaid,productid,sum,payed,desct,img,SUM(vol),SUM(value) FROM gamescart WHERE orderid=$orderid GROUP BY productid");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users;
    }

    function getFinalCart($orderid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT SUM(value) FROM gamescart WHERE orderid=$orderid");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users[0]['SUM(value)'];
    }

    function newSmena($companyid){

      //date_default_timezone_set('Asia/Almaty');
          $datea = date("Y-m-d");
          $timea = date("H:i:s");
      
      $book = R::dispense('gamessmena');
            $book->companyid = $companyid;
            $book->status= "1";
            $book->datea = $datea;
            $book->timea = $timea;
            R::store($book);

            return $book['id'];
      }


      function addCompany($companyname,$tables,$users,$username,$password,$phone){

            $usersd = getUserByIda($phone);
            
            if($usersd==null){
            $book = R::dispense('gamescompany');
            $book->companyid = "";
            $book->companyname= $companyname;
            $book->tables = $tables;
            $book->users = $users;
            $book->block = "2";
            R::store($book);
           $a = $book['id'];

           $book = R::load('gamescompany', $a);
           $book->companyid = $book['id'];
            R::store($book);


            $book = R::dispense('gamesettings');
            $book->sitename = $companyname;
            $book->auth= "1";
            $book->companyid = $a;
            $book->cashback = "2";
            R::store($book);

           $text = "Вы успешно зарегистрированы. ID: ".$a." Ваш пароль: ".$password." new.rocc.kz";
           addUser($username,$password,$a,$phone);
           sendSMS($phone, $text);

            $data['status']="OK";
            $data['message']="Успешно добавлено!";

            }else{
                $data['status']="NO";
                $data['message']="Пользователь с данным номером уже зарегистрирован";
            }

            return $data;
      }


  function getSmenaId($companyid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamessmena WHERE status='1' ORDER BY `id` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            if($users[$i]['companyid']===$companyid){
                return $users[$i];
            }
        }

        return null;
    }
    
    function getSmena($smenaid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamessmena ORDER BY `id` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            if($users[$i]['id']===$smenaid){
                return $users[$i];
            }
        }

        return null;
    }
    
    function getDatda($type,$id){
        
    global $mysqli;

    if($type="1"){
      $res=$mysqli->query("SELECT * FROM typepay ORDER BY `typeid` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            if($users[$i]['typeid']===$id){
                return $users[$i];
            }
        }
    }

        return null;
    }

    function proverkaSmena($companyid){
        
    $proverka = getSmenaId($companyid);
    if($proverka['id']==null){
       $data['smenaid'] = newSmena($companyid);
    }else{
       $data['smenaid'] = $proverka['id'];
    }
    return $data;
    }

  function getX($smenaid,$type) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT sum(sum) FROM payed WHERE smenaid=$smenaid AND typepay=$type");
    foreach($res as $value){      
      $data[] = $value;
    }
    
    $a = $data[0]['sum(sum)'];
    if($a.length==0){
      $b = "0";
    }else{
      $b = $a;
    }
    return $b;
}

function getDolgNal($smenaid) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT sum(sum) FROM gamesdolg WHERE smenaid=$smenaid AND prihodnal=1");
    foreach($res as $value){      
      $data[] = $value;
    }
    
    $a = $data[0]['sum(sum)'];
    if($a.length==0){
      $b = "0";
    }else{
      $b = $a;
    }
    return $b;
}

function getDolgBezNal($smenaid) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT sum(sum) FROM gamesdolg WHERE smenaid=$smenaid AND prihodbeznal=1");
    foreach($res as $value){      
      $data[] = $value;
    }
    
    $a = $data[0]['sum(sum)'];
    if($a.length==0){
      $b = "0";
    }else{
      $b = $a;
    }
    return $b;
}

function getVl($smenaid,$type) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT sum(sum) FROM vlozhenie WHERE smenaid=$smenaid AND typepay=$type");
    foreach($res as $value){      
      $data[] = $value;
    }
    
    $a = $data[0]['sum(sum)'];
    if($a.length==0){
      $b = "0";
    }else{
      $b = $a;
    }
    return $b;
}

function getRashod($smenaid,$type) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT sum(sum) FROM rashod WHERE smenaid=$smenaid AND typepay=$type");
    foreach($res as $value){      
      $data[] = $value;
    }
    
    $a = $data[0]['sum(sum)'];
    if($a.length==0){
      $b = "0";
    }else{
      $b = $a;
    }
    return $b;
}
  
  function getReportPdf($smenaid,$companyid) {
    
  //  date_default_timezone_set('Asia/Almaty');
          $datea = date("d.m.Y");
          $timea = date("H:i:s");
    $nal = getX($smenaid,"1");
    $beznal = getX($smenaid,"2");
    $dolg = getX($smenaid,"3");
    $vlozhenie = getVl($smenaid,"5");
    $izyatie = getRashod($smenaid,"6");
    $rashod = getRashod($smenaid,"10");
    $smena = getSmenaId($_COOKIE['companyid']);
    $indolgnal = getDolgNal($smenaid);
    $indolgbeznal = getDolgBezNal($smenaid);
    $date['date'] = $datea;
    $date['time'] = $timea;

    $data['nal'] = $nal;
    $data['beznal'] = $beznal;
    $data['dolg'] = $dolg;
    $data['prihod'] = $nal+$beznal+$dolg;
    $data['vlozhenie'] = $vlozhenie;
    $data['prihodnal'] = $nal+$vlozhenie;
    $data['rashodobw'] = $izyatie+$rashod;
    $data['izyatie'] = $izyatie;
    $data['rashod'] = $rashod;
    $data['ostatok'] = $data['prihodnal']-$data['rashodobw'];
    $data['smena'] = $smena;
    $data['indolgnal'] = $indolgnal;
    $data['indolgbeznal'] = $indolgbeznal;
    $data['date'] = $date;

    return $data;


  }
  
  function getReport($smenaid) {
    
    //date_default_timezone_set('Asia/Almaty');
          $datea = date("d.m.Y");
          $timea = date("H:i:s");
    $nal = getX($smenaid,"1");
    $beznal = getX($smenaid,"2");
    $dolg = getX($smenaid,"3");
    $vlozhenie = getVl($smenaid,"5");
    $izyatie = getRashod($smenaid,"6");
    $rashod = getRashod($smenaid,"10");
    $smena = getSmenaId($_COOKIE['companyid']);
    $indolgnal = getDolgNal($smenaid);
    $indolgbeznal = getDolgBezNal($smenaid);
    $date['date'] = $datea;
    $date['time'] = $timea;

    $data['nal'] = $nal;
    $data['beznal'] = $beznal;
    $data['dolg'] = $dolg;
    $data['prihod'] = $nal+$beznal+$dolg;
    $data['vlozhenie'] = $vlozhenie;
    $data['prihodnal'] = $nal+$vlozhenie;
    $data['rashodobw'] = $izyatie+$rashod;
    $data['izyatie'] = $izyatie;
    $data['rashod'] = $rashod;
    $data['ostatok'] = $data['prihodnal']-$data['rashodobw'];
    $data['smena'] = $smena;
    $data['indolgnal'] = $indolgnal;
    $data['indolgbeznal'] = $indolgbeznal;
    $data['date'] = $date;

    return $data;

  }

  function getXr($smenaid,$type) {
    
  global $mysqli;
    
  $res=$mysqli->query("SELECT sum(payed.sum),payed.tableid,tablegame.id,payed.smenaid,payed.tableid,tablegame.tablename FROM payed,tablegame WHERE payed.tableid=tablegame.id AND payed.smenaid=$smenaid AND payed.tableid=$type");
    foreach($res as $value){      
      $data[] = $value;
    }
    
    $a = $data[0]['sum(sum)'];
    if($a.length==0){
      $b = "0";
    }else{
      $b = $a;
    }
    return $data;
}

function addTable($tablename,$tarif){

      global $datea;
      global $timea;
      $companyid = $_COOKIE['companyid'];

      $aza = getTarifCompany($companyid);
      if($aza['fakttables']>$aza['tariftables']){
        $data['status']="NO";
        $data['message']="Превышено максимальное количество, поменяйте тарифный план";
      }else{

      $book = R::dispense('tablegame');
            $book->tablename = $tablename;
            $book->status = "1";
            $book->tarif = $tarif;
            $book->companyid = $companyid;
            R::store($book);
              $data['status']="OK";
        $data['message']="Успешно добавлено!";
      }
            

            
    return $data;

    }

function getBalans(){
        
$doc = file_get_contents('https://smsc.kz/sys/balance.php?login=AzatKadyr1&psw=180323&fmt=3');
$array = json_decode($doc, true);
        return $array;

    }
    
  function addMoney($smenaid,$type,$sum,$comment){

      global $datea;
      global $timea;
      $companyid = $_COOKIE['companyid'];
      $cashierid = $_COOKIE['userid'];

      if($type=="5"){
      $book = R::dispense('vlozhenie');
      $book->smenaid = $smenaid;
            $book->typepay= "5";
            $book->sum = $sum;
            $book->cashierid = $cashierid;
            $book->companyid = $companyid;
            $book->comment = $comment;
            $book->datepay = $datea;
            $book->timepay = $timea;
            R::store($book);
      }

      if($type=="6"){
      $book = R::dispense('rashod');
      $book->smenaid = $smenaid;
            $book->typepay= "6";
            $book->sum = $sum;
            $book->cashierid = $cashierid;
            $book->companyid = $companyid;
            $book->comment = $comment;
            $book->datepay = $datea;
            $book->timepay = $timea;
            R::store($book);
      }

            if($type=="10"){
      $book = R::dispense('rashod');
      $book->smenaid = $smenaid;
            $book->typepay= "10";
            $book->sum = $sum;
            $book->cashierid = $cashierid;
            $book->companyid = $companyid;
            $book->comment = $comment;
            $book->datepay = $datea;
            $book->timepay = $timea;
            R::store($book);
      }

  $data['status'] = "OK"; 

    return $data;

    }

    function generateCheck($smenaid,$companyid) {
    
      
      $apiClient = new Api2Pdf('19a076dd-ae30-4b9a-a900-ab83a080fd53');

      $apiClient->setInline(true);
      $apiClient->setFilename('check.pdf');
      $apiClient->setOptions(
          [
              'orientation' => 'portrait', 
              'pageSize'=> 'A5'
          ]
      );
      $result = $apiClient->wkHtmlToPdfFromUrl('http://kl.rocc.kz/check.php?smenaid='.$smenaid.'&companyid='.$companyid);

      //echo $result->getPdf();

    return $result->getPdf();

  }

  function generateZzCheck($smenaid,$companyid) {
    
      
      $apiClient = new Api2Pdf('19a076dd-ae30-4b9a-a900-ab83a080fd53');

      $apiClient->setInline(true);
      $apiClient->setFilename('zcheck.pdf');
      $apiClient->setOptions(
          [
              'orientation' => 'portrait', 
              'pageSize'=> 'A5'
          ]
      );
      $result = $apiClient->wkHtmlToPdfFromUrl('http://kl.rocc.kz/z.php?smenaid='.$smenaid.'&companyid='.$companyid);

      //echo $result->getPdf();

    return $result->getPdf();

  }

  function getColZakaz($smenaid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamesession WHERE smenaid=$smenaid");
    foreach($res as $value){      
      $users[] = $value;
    }
        $data = sizeof($users);
        return $data;
    }

  function getOtkrZakaz($smenaid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamesession WHERE smenaid=$smenaid ORDER BY `id` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            if($users[$i]['status']==="1"){
                return $users[$i];
            }
        }

        return null;
    }

    function lasttimeUser($userid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gameslastuser WHERE userid=$userid ORDER BY `id` DESC LIMIT 1");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users[0];
    }

    function lastUser(){

        $dateab = date("Y-m-d");
        $timeab = date("H:i:s");

        if(isset($_COOKIE['userid'])){
                        $book = R::dispense('gameslastuser');
                        $book->userid = $_COOKIE['userid'];
                        $book->lastdate= $dateab;
                        $book->lasttime = $timeab;
                        R::store($book);
        }
                  return null;      

    }

    function addProduct($productname,$productid,$prodcena,$zakupcena,$productimg){

        global $datea;
        global $timea;
        $aza = "123";
        $proverka = proverkaBarcode($productid);
        if($proverka['status']=="OK"){
           if($productname.length>$aza.length){
            if($productid.length>3){
                $prid = preg_replace("/[^0-9]/", '', $productid);
                $productid = $prid;
                $prce = preg_replace("/[^0-9]/", '', $prodcena);
                $prodcena = $prce;
                $zakupcen = preg_replace("/[^0-9]/", '', $zakupcena);
                $zakupcena = $zakupcen;
                if($prodcena.length>3){
                    if($zakupcena.length>3){

                        $book = R::dispense('gamesproduct');
                        $book->productname = $productname;
                        $book->productsum= $prodcena;
                        $book->zakupcena = $zakupcena;
                        $book->productid = $productid;
                        $book->companyid = $_COOKIE['companyid'];
                        $book->productimg = $productimg;
                        $book->datepay = $datea;
                        $book->timepay = $timea;
                        R::store($book);

                        $data['status'] = "OK";
                        $data['message'] = "Товар создан!";

                    }else{
                        $data['status'] = "NO";
                        $data['message'] = "Ошибка в закупочной цене";
                    }
                }else{
                    $data['status'] = "NO";
                $data['message'] = "Ошибка в продажной цене";
                }
            }else{
                $data['status'] = "NO";
                $data['message'] = "Штрих код товара должно быть больше 3-х символов";
            }
        }else{
            $data['status'] = "NO";
            $data['message'] = "Название товара должно быть больше 3-х символов";
        }
        }else{
                        $data['status'] = "NO";
                        $data['message'] = "Товар с данным штрих кодом уже имеется в базе";
        }

        return $data;

    }

    function getOplZakaz($smenaid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamesession WHERE smenaid=$smenaid ORDER BY `id` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            if($users[$i]['payed']==="1"){
                return $users[$i];
            }
        }

        return null;
    }

    function proverkaBarcode($productid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamesproduct WHERE productid=$productid ORDER BY `id` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

    if($users[0]==null){
      $data['status']="OK";
    }else{
      $data['status']="NO";
      $data['product']=$users;
    }

        return $data;
    }

    function sendSmsAll($users,$text){
        
        $dateab = date("Y-m-d");
        $timeab = date("H:i:s");
        global $smsall;
        if($smsall=="1"){
            if($users=="all"){
            $ctragent = getAllCtragentss($_COOKIE['companyid']);
            $smscount = sizeof($ctragent);
            $cena = $smscount*10;

            $book = R::dispense('gamesplateji');
            $book->companyid = $_COOKIE['companyid'];
            $book->value = "-".$cena;
            $book->type = "out";
            $book->comment = "Оплата смс рассылок";
            $book->datea = $dateab;
            $book->timea = $timeab;
            R::store($book);
            $des = "С вашего баланса списана сумма ".$cena."тг";
            newNtf('Смс рассылка',$des,$des,'1',$_COOKIE['userid'],'1');
            for($i=0;$i<sizeof($ctragent);$i++){
                $data = sendSMS($ctragent[$i]['ctrphone'], $text);

                $book = R::dispense('gamessmshistory');
                $book->companyid = $_COOKIE['companyid'];
                $book->userid = $_COOKIE['userid'];
                $book->smstext = $text;
                $book->smsto = $ctragent[$i]['ctrphone'];
                $book->datea = $dateab;
                $book->timea = $timeab;
                R::store($book);
            }
        }else{
                $cena = "10";
            $book = R::dispense('gamesplateji');
            $book->companyid = $_COOKIE['companyid'];
            $book->value = "-".$cena;
            $book->type = "out";
            $book->comment = "Оплата смс рассылок";
            $book->datea = $dateab;
            $book->timea = $timeab;
            R::store($book);

                $book = R::dispense('gamessmshistory');
                $book->companyid = $_COOKIE['companyid'];
                $book->userid = $_COOKIE['userid'];
                $book->smstext = $text;
                $book->smsto = $users;
                $book->datea = $dateab;
                $book->timea = $timeab;
                R::store($book);

            $des = "С вашего баланса списана сумма ".$cena."тг";
            newNtf('Смс рассылка',$des,$des,'1',$_COOKIE['userid'],'1');
            newNtf('Смс рассылка',' Смс успешно отправлено! ',' Смс успешно отправлено! ','1',$_COOKIE['userid'],'1');

            $data = sendSMS($users, $text);

            $data['status'] = "OK";
            $data['message'] = "Успешно отправлено";
        }
        $data['status'] = "OK";
        $data['message'] = "Успешно отправлено";
         newNtf('Смс рассылка','Рассылка успешно завершена','Рассылка успешно завершена','1',$_COOKIE['userid'],'1');
        }else{
        $data['status'] = "NO";
        $data['message'] = "СМС рассылка временно отключена, обратитесь к администратору!";
        }

        return $data;
    }


  function endSmena($endsum) {
   
      global $datea;
      global $timea;
      $companyid = $_COOKIE['companyid'];
      $cashierid = $_COOKIE['userid'];
    $proverka = getSmenaId($companyid);
   
    $prover = getColZakaz($proverka['id']);
    $prover1 = getOtkrZakaz($proverka['id']);
    $prover2 = getOplZakaz($proverka['id']);
    if($prover>0){
      if($prover1==null){
        if($prover2==null){
             $aza = getReport($proverka['id']);
    if($endsum==null){
      $endsum = 0;
    }
    if($proverka['id']==null){
       $data['status'] = "NO";
       $data['message'] = "У вас нет открытых смен!";
    }else{
      $qr = generateQrZ($proverka['id'],$companyid);
       $book = R::load('gamessmena', $proverka['id']);
            $book->status = "2";
            $book->dateb = $datea;
            $book->timeb = $timea;
            $book->endsum = $endsum;
            $book->qr = $qr;
      R::store($book);
      $raznica = $endsum - $aza['ostatok'];
      $data['status'] = "OK";
      $data['message'] = "Смена закрыта!";
      $data['data'] = $aza;
      $data['raznica'] = $raznica;
      $data['endsum'] = $endsum;
      $bb = generateZzCheck($proverka['id'],$companyid);
      
      $data['check'] = $bb;
      $data['qr'] = $qr;
    }
        }else{
          $data['info'] = $prover2;
          $data['status'] = "NO";
          $data['message'] = "У вас есть неоплаченные заказы, проверьте в разделе 'Все заказы' ";
        }
   
      }else{
        $data['info'] = $prover1;
        $data['status'] = "NO";
        $data['message'] = "У вас есть незавершенные заказы, проверьте в разделе 'Все заказы' ";
      }
    }else{

       $data['status'] = "NO";
       $data['message'] = "У вас нет заказов за смену";
    
    }
      
    return $data;
    
    

  }

  function generateQrZ($smenaid,$companyid){

$filename = "img/z/$smenaid.png";

QRcode::png("http://kl.rocc.kz/view.php?smenaid=$smenaid&companyid=$companyid", $filename, "H", 4, 4);

        return $filename;
    }


    function getHistoryAll($type){
        
    global $mysqli;
    $companyid = $_COOKIE['companyid'];
    $proverka = getSmenaId($companyid);
    $smenaid = $proverka['id'];

    if($type=="1"){

    $res=$mysqli->query("SELECT * FROM gamesession,gamestatus1 WHERE gamesession.smenaid=$smenaid AND gamesession.payed=gamestatus1.statusid");
    foreach($res as $value){      
      $users[] = $value;
    }
//все
    }
    
    if($type=="2"){

    $res=$mysqli->query("SELECT * FROM gamesession,gamestatus1,payed WHERE gamesession.smenaid=$smenaid AND gamesession.payed=gamestatus1.statusid AND payed.gamesessionid=gamesession.id");
    foreach($res as $value){      
      $users[] = $value;
    }
//оплаченные    
    }

    if($type=="3"){
    $a = "1";
    $res=$mysqli->query("SELECT * FROM gamesession WHERE gamesession.smenaid=$smenaid AND gamesession.status=1");
    foreach($res as $value){      
      $users[] = $value;
    }
//незавершенные
    }

    if($type=="4"){
    $a = "1";
    $res=$mysqli->query("SELECT * FROM gamesession WHERE gamesession.smenaid=$smenaid AND gamesession.payed=1");
    foreach($res as $value){      
      $users[] = $value;
    }
//неоплаченные
    }

    if($type=="5"){
    $a = "1";
    $res=$mysqli->query("SELECT * FROM gamesession WHERE gamesession.smenaid=$smenaid AND gamesession.status=2");
    foreach($res as $value){      
      $users[] = $value;
    }
//завершенные
    }

    return $users;

    }

    function deleteTable($id){

      $cat = R::load('tablegame', $id);
      R::trash($cat);

        return null;
    }


  function saveYandex($sum,$plansum,$companyid){

      global $datea;
      global $timea;
      $rub = 5;
      $value = $plansum * $rub;
      
      $book = R::dispense('gamesplateji');
            $book->companyid = $companyid;
            $book->value = $value;
            $book->type = "in";
            $book->comment = "Пополнение через Яндекс Деньги";
            $book->datea = $datea;
            $book->timea = $timea;
            R::store($book);
            
            $short ="Пополнение баланса на сумму ".$value." тг";
            $full = "Пополнение баланса на сумму ".$value." тг";

               global $mysqli;

              $res=$mysqli->query("SELECT * FROM gameuser WHERE companyid=$companyid");
              foreach($res as $value){      
                $users[] = $value;
              }

        for($i=0;$i<sizeof($users);$i++){
            newNtf("Системное уведомление",$short,$full,"1",$users[$i]['userid'],'1');
        }
    return null;

    }

    function addCtrAgentSystem($ctrname,$ctrphone){

      global $datea;
      global $timea;
      $companyid = $_COOKIE['companyid'];

      try {

        $conn = new PDO('mysql:host=localhost;dbname=user5990_77', 'user5990_aza', '180323');

        $sql = "
            INSERT INTO gamespokupatel (ctrname, companyid, ctrphone, ctrstatus)
            VALUES (:g_ctrname, :g_companyid, :g_ctrphone, :g_ctrstatus)
        ";
        $query = $conn->prepare($sql);

        $query->execute(array('g_ctrname'=>$ctrname, 'g_companyid'=>$companyid,
                'g_ctrphone'=>$ctrphone, 'g_ctrstatus'=>2));


    }catch (PDOException $e){
        echo "Error!: " . $e->getMessage() . "<br/>";
    }
            
    return null;

    }

      function addCtrAgent($ctrname,$ctrphone){

      global $datea;
      global $timea;
      $companyid = $_COOKIE['companyid'];

      try {

        $conn = new PDO('mysql:host=localhost;dbname=user5990_77', 'user5990_aza', '180323');

        $sql = "
            INSERT INTO gamespokupatel (ctrname, companyid, ctrphone, ctrstatus)
            VALUES (:g_ctrname, :g_companyid, :g_ctrphone, :g_ctrstatus)
        ";
        $query = $conn->prepare($sql);

        $query->execute(array('g_ctrname'=>$ctrname, 'g_companyid'=>$companyid,
                'g_ctrphone'=>$ctrphone, 'g_ctrstatus'=>1));


    }catch (PDOException $e){
        echo "Error!: " . $e->getMessage() . "<br/>";
    }
            
    return null;

    }
  
   function addUser($username,$password,$companyid,$phone){

      global $datea;
      global $timea;

      try {

        $conn = new PDO('mysql:host=localhost;dbname=user5990_77', 'user5990_aza', '180323');

        $sql = "
            INSERT INTO gameuser (username, password, companyid, vac, phone)
            VALUES (:g_username, :g_password, :g_companyid, :g_vac, :g_phone)
        ";
        $query = $conn->prepare($sql);

        $query->execute(array('g_username'=>$username, 'g_password'=>$password,
                'g_companyid'=>$companyid, 'g_vac'=>2, 'g_phone'=>$phone));


    }catch (PDOException $e){
        echo "Error!: " . $e->getMessage() . "<br/>";
    }
            
    return null;

    }

  function getAllCtragents($companyid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamespokupatel WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users;
    }

    function getAllCtragentss($companyid,$type){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamespokupatel WHERE companyid=$companyid AND ctrstatus=1");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users;
    }

  function getCtrZakazy($ctrid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamesdolg,typepay WHERE gamesdolg.ctragentid=$ctrid AND gamesdolg.typepay=typepay.typeid");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users;
    }

    function getCtrCashback($ctrid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamescashback,typepay WHERE ctragentid=$ctrid AND gamescashback.typepay=typepay.typeid");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users;
    }


  function getInfoCtragents($ctrid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamespokupatel WHERE ctrid=$ctrid");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users[0];
    }


function getPokupatelPoId($id){

    $data['info'] = getInfoCtragents($id);
    $data['balans'] = getBalansCtrid($id);
    $data['dolgzakazy'] = getCtrZakazy($id);
    $data['zakazycashback'] = getCtrCashback($id);

    return $data;
}

function deletePokupatel($id){

    $agent = getPokupatelPoId($id);
    $data['aza'] = $agent;

    if($agent['balans']['dolg']>=0){

      try {

        $conn = new PDO('mysql:host=localhost;dbname=user5990_77', 'user5990_aza', '180323');

        $sql = "
               DELETE FROM gamespokupatel
               WHERE ctrid = :g_ctrid
        ";

        $query = $conn->prepare($sql);
        $query->execute(array('g_ctrid' => $id));


    }catch (PDOException $e){
        echo "Error!: " . $e->getMessage() . "<br/>";
    }

      $data['status'] = "OK";
      $data['message'] = "Покупатель успешно удален";
    }else{
      $data['status'] = "NO";
      $data['message'] = "Покупатель имеет отрицательный баланс";
    }

    return $data;
}

function getProducts(){
        
    global $mysqli;
    $companyid = $_COOKIE['companyid'];

    $res=$mysqli->query("SELECT * FROM gamesproduct WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users;
    }

function getProductId($id){
        
    global $mysqli;
    $companyid = $_COOKIE['companyid'];

    $res=$mysqli->query("SELECT * FROM gamesproduct WHERE companyid=$companyid AND id=$id");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users[0];
    }

function deleteProduct($id){
        
    $product = getProductId($id);
    $ostatok = getSklad($product['productid']);
    if($ostatok['ostatok']==0){
      $data['status'] = "OK";
      $data['ostatok'] = $ostatok;

      $cat = R::load('gamesproduct', $id);
      R::trash($cat);
       
    }else{
      $data['status'] = "NO";
      $data['message'] = "Невозможно удалить товар у которого есть остаток в системе, необходимо сделать списание/оприходование!";
      $data['ostatok'] = $ostatok;
    }
        return $data;
    }

function getBalansCtrid($ctrid) {
    
 if($ctrid=="1"){
  $info = null;
 }else{
   global $mysqli;
    
  $res=$mysqli->query("SELECT sum(sum) FROM gamescashback WHERE ctragentid=$ctrid");
    foreach($res as $value){      
      $data[] = $value;
    }
  
  $res=$mysqli->query("SELECT sum(sum) FROM gamesdolg WHERE ctragentid=$ctrid");
    foreach($res as $value){      
      $datab[] = $value;
    }

    $cahsbacka = $data[0]['sum(sum)'];
    if($cahsbacka.length==0){
      $cahsback = "0";
    }else{
      $cahsback = $cahsbacka;
    }

    $dolga = $datab[0]['sum(sum)'];
    if($dolga.length==0){
      $dolg = "0";
    }else{
      $dolg = $dolga;
    }

    $info['bonusy'] = $cahsback;
    $info['dolg'] = $dolg;
 }

    return $info;
}


function getSklad($productid) {
    
   $companyid = $_COOKIE['companyid'];
   global $mysqli;
    
  $res=$mysqli->query("SELECT sum(sum) FROM gamespriemka WHERE companyid=$companyid AND productid=$productid");
    foreach($res as $value){      
      $data[] = $value;
    }

    $one = $data[0]['sum(sum)'];
    if($one.length==0){
      $aza = "0";
    }else{
      $aza = $one;
    }
  
  $res=$mysqli->query("SELECT sum(vol) FROM gamescart WHERE productid=$productid");
    foreach($res as $value){      
      $azat[] = $value;
    }

    $two = $azat[0]['sum(vol)'];
    if($two.length==0){
      $ab = "0";
    }else{
      $ab = $two;
    }

    $info['priemka'] = $aza;
    $info['prodano'] = $ab;
    $info['ostatok'] = $aza - $ab;

    return $info;
}

 function deleteBdAll($password){

     $companyid = $_COOKIE['companyid'];
     global $mysqli;

     if($password=="180323"){
     $table = getTable();
     for($i=0;$i<sizeof($table);$i++){
            $cat = R::load('tablegame', $table[$i]['id']);
            R::trash($cat);
        }

    

    $res=$mysqli->query("SELECT * FROM gamessmena WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

    for($i=0;$i<sizeof($users);$i++){
            $cat = R::load('gamessmena', $users[$i]['id']);
            R::trash($cat);
        }

    $res=$mysqli->query("SELECT * FROM gamespokupatel WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

    for($i=0;$i<sizeof($users);$i++){
            $id = $users[$i]['ctrid'];
            R::exec('DELETE FROM `gamespokupatel` WHERE `ctrid` = :id', array(
    ':id' => $id
));
        }

    $res=$mysqli->query("SELECT * FROM gamescashback WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

    for($i=0;$i<sizeof($users);$i++){
            $cat = R::load('gamescashback', $users[$i]['id']);
            R::trash($cat);
        }

    $res=$mysqli->query("SELECT * FROM payed WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

    for($i=0;$i<sizeof($users);$i++){
            $cat = R::load('payed', $users[$i]['id']);
            R::trash($cat);
        }

    $res=$mysqli->query("SELECT * FROM gamesdolg WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

    for($i=0;$i<sizeof($users);$i++){
            $cat = R::load('gamesdolg', $users[$i]['id']);
            R::trash($cat);
        }

    $res=$mysqli->query("SELECT * FROM gamesession WHERE companyid=$companyid");
    foreach($res as $value){      
      $users[] = $value;
    }

    for($i=0;$i<sizeof($users);$i++){
            $cat = R::load('gamesdolg', $users[$i]['id']);
            R::trash($cat);
        }
      //R::wipe('tablegame');
      //R::wipe('gamessmena');
     // R::wipe('gamespokupatel');
      //R::wipe('gamesession');
     // R::wipe('gamescashback');
      //R::wipe('payed');
      //R::wipe('gamesdolg');
      addCtrAgentSystem('Не выбран','');
      newNtf("Система","Обнуление ваших данных успешно завершено","Обнуление ваших данных успешно завершено","1",$_COOKIE['userid'],'1');

          $data['status'] = "OK";
          $data['message'] = "Обнулено!";

    }else{
          $data['status'] = "NO";
          $data['message'] = "Неверный пароль!";
      
    }
return $data;
    }

      function getNotifications($userid){
        
    global $mysqli;

    $res=$mysqli->query("SELECT * FROM gamesusernotf WHERE userid=$userid ORDER BY id DESC LIMIT 10");
    foreach($res as $value){      
      $users[] = $value;
    }

        return $users;
    }


    function getIin($uid){
        
      $ch = curl_init("https://www.sberbank.kz/crediting/api/personal_data/950603300848"); // such as http://example.com/example.xml
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      $data = curl_exec($ch);
      curl_close($ch);
      $array = json_decode($data, true);

      //$data = file_get_contents('https://www.sberbank.kz/crediting/api/personal_data/950603300848');
      //$array = json_decode($data, true);

        return $data;
    }


  function showDate( $date ) // $date --> время в формате Unix time
{
 $stf = 0;
 $cur_time = time();
 $diff = $cur_time - $date;
  
 $seconds = array( 'секунда', 'секунды', 'секунд' );
 $minutes = array( 'минута', 'минуты', 'минут' );
 $hours = array( 'час', 'часа', 'часов' );
 $days = array( 'день', 'дня', 'дней' );
 $weeks = array( 'неделя', 'недели', 'недель' );
 $months = array( 'месяц', 'месяца', 'месяцев' );
 $years = array( 'год', 'года', 'лет' );
 $decades = array( 'десятилетие', 'десятилетия', 'десятилетий' );
  
 $phrase = array( $seconds, $minutes, $hours, $days, $weeks, $months, $years, $decades );
 $length = array( 1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600 );
  
 for ( $i = sizeof( $length ) - 1; ( $i >= 0 ) && ( ( $no = $diff / $length[ $i ] ) <= 1 ); $i -- ) {
 ;
 }
 if ( $i < 0 ) {
 $i = 0;
 }
 $_time = $cur_time - ( $diff % $length[ $i ] );
 $no = floor( $no );
 $value = sprintf( "%d %s ", $no, getPhrase( $no, $phrase[ $i ] ) );
  
 if ( ( $stf == 1 ) && ( $i >= 1 ) && ( ( $cur_time - $_time ) > 0 ) ) {
 $value .= time_ago( $_time );
 }
  
 return $value . ' назад';
}
  
function getPhrase( $number, $titles ) {
 $cases = array( 2, 0, 1, 1, 1, 2 );
  
 return $titles[ ( $number % 100 > 4 && $number % 100 < 20 ) ? 2 : $cases[ min( $number % 10, 5 ) ] ];
} 

function newNtf($ntfname,$ntfdesc,$ntffull,$ntfcat,$ntfuserid,$type){

     // date_default_timezone_set('Asia/Almaty');
          $datea = date("Y-m-d");
          $timea = date("H:i:s");
      $datab = $datea." ".$timea;
      if($type=="1"){
        $book = R::dispense('gamesusernotf');
            $book->userid = $ntfuserid;
            $book->ntfname= $ntfname;
            $book->category = $ntfcat;
            $book->desca = $ntfdesc;
            $book->fulltexta = $ntffull;
            $book->datetime = $datab;
            R::store($book);

            return null;
      }else{
         global $mysqli;

    $res=$mysqli->query("SELECT * FROM gameuser ORDER BY `userid` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            newNtf($ntfname,$ntfdesc,$ntffull,$ntfcat,$users[$i]['userid'],'1');
        }
      }
    
    }

    function sendWh($phone,$text){

      $data = [
            'phone' => $phone, // Телефон получателя
            'body' => $text, // Сообщение
        ];
        $json = json_encode($data); // Закодируем данные в JSON
        // URL для запроса POST /message
        $token = ' jb8nphlcx67z0ub2';
        $instanceId = '212632';
        $url = 'https://eu163.chat-api.com/instance212632/sendMessage?token=jb8nphlcx67z0ub2';
        // Сформируем контекст обычного POST-запроса
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $json
            ]
        ]);
        // Отправим запрос
        $result = file_get_contents($url, false, $options);
        
        return $result;
    }

    function sendWhFile($phone,$text){

      $data = [
            'phone' => $phone, // Телефон получателя
            'body' => $text, // Сообщение
        ];
        $json = json_encode($data); // Закодируем данные в JSON
        // URL для запроса POST /message
        $token = ' jb8nphlcx67z0ub2';
        $instanceId = '212632';
        $url = 'https://eu163.chat-api.com/instance212632/sendLink?token=jb8nphlcx67z0ub2';
        // Сформируем контекст обычного POST-запроса
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $json
            ]
        ]);
        // Отправим запрос
        $result = file_get_contents($url, false, $options);
        
        return $result;
    }


    function sendSMS($phone, $text) {
    
    $login = 'AzatKadyr1';
    $password = '180323';
    
    $url = 'https://smsc.kz/sys/send.php';
    $data = array(
        'phones'   => $phone,
        'mes'      => $text,
        'login'    => $login,
        'psw'      => $password
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    /*
    if($result[0] == 'accepted') {
        return true;
    } else {
        return false;
    }
    */
}


function sendPush() {
    
    $login = 'AzatKadyr1';
    $password = '180323';
    
    $url = 'https://api.sendpulse.com/push/tasks';
    $title = "Тестовая";
    $webid = "68222";
    $body = "тестовая рассылка";
    $ttl = "86400";
    $data = array(
        'title'   => $title,
        'website_id'      => $webid,
        'body'    => $body,
        'ttl'      => $ttl
     );

    $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    /*
    if($result[0] == 'accepted') {
        return true;
    } else {
        return false;
    }
    */

    return $result;
}

function can_upload($file){
  // если имя пустое, значит файл не выбран
    if($file['name'] == '')
    return 'Вы не выбрали файл.';
  
  /* если размер файла 0, значит его не пропустили настройки 
  сервера из-за того, что он слишком большой */
  if($file['size'] == 0)
    return 'Файл слишком большой.';
  
  // разбиваем имя файла по точке и получаем массив
  $getMime = explode('.', $file['name']);
  // нас интересует последний элемент массива - расширение
  $mime = strtolower(end($getMime));
  // объявим массив допустимых расширений
  $types = array('jpg', 'png', 'gif', 'bmp', 'jpeg');
  
  // если расширение не входит в список допустимых - return
  if(!in_array($mime, $types))
    return 'Недопустимый тип файла.';
  
  return true;
  }
  
  function make_upload($file,$id){  
  // формируем уникальное имя картинки: случайное число и name
  $name = mt_rand(0, 10000) . $file['name'];
  copy($file['tmp_name'], 'img/' . $name);
  $book = R::load('gamesproduct', $id);
          $book->productimg = "img/".$name;
          R::store($book);
  }
?>