<?php
require_once 'oop.php';
$companyid = $_COOKIE['company'];
$file_get = $_SERVER["DOCUMENT_ROOT"] . "/modules/log/get.log";
$file_post = $_SERVER["DOCUMENT_ROOT"] . "/modules/log/post.log";

if (!empty($_GET))
{
    $fw = fopen($file_get, "a");
    fwrite($fw, "GET " . var_export($_GET, true));
    fclose($fw);
}

if (!empty($_POST))
{
    $fw = fopen($file_post, "a");
    fwrite($fw, "POST " . var_export($_POST, true));
    fclose($fw);
}

if ($_GET['type'] == "generatepdf")
{
    $data = generatePdf($_GET['typepdf'], $_GET['reportid']);
    echo json_encode($data);
}

if ($_GET['type'] == "getlastotkl")
{
    $data = getLastOtkl();
    echo json_encode($data);
}

if ($_GET['type'] == "getuserinfo")
{
    $data = userInfo($_GET['userid']);
    echo json_encode($data);
}

if ($_GET['type'] == "getplanned")
{
    $data = getPlanned('1');
print_r($data);

}

if ($_GET['type'] == "getmon")
{
    $data = getMon();
    echo json_encode($data);
}

if ($_GET['type'] == "getrestcompany")
{
    global $companyid;
    $data = getCompanyInfo($companyid, $_GET['typed'], $_GET['datea'], $_GET['dateb']);
    // print_r($data);
    echo json_encode($data);
}

if ($_GET['type'] == "getspisok")
{
    $data = getSpisok();
    echo json_encode($data);
}

if ($_GET['type'] == "getlistchatmessage")
{
    $data = getListChatMessage($_GET['chatid']);
    echo json_encode($data);
}

if ($_GET['type'] == "getlistchat")
{
    $data = getListChat();
    echo json_encode($data);
}

if ($_GET['type'] == "sendmessagechat")
{
    $data = addMessage($_GET['chatid'], $_GET['author'], $_GET['message']);
    echo json_encode($data);
}

if ($_GET['type'] == "azatt")
{
    $data = spisanieBalans("1","5000");
    print_r($aza);
    //echo json_encode($data);
    
}

if ($_GET['type'] == "deletesession")
{
    $data = deleteSession($_GET['id']);
    echo json_encode($data);
}

if ($_GET['type'] == "getlevel")
{
    $data = getLevel($_GET['id']);
    echo json_encode($data);
    //print_r($data);
    
}



if ($_GET['type'] == "deletereport")
{
    global $token;
    $data = deleteReport($_GET['reportid']);
    echo json_encode($data);
    //print_r($data);
    //echo $token;
}


if ($_GET['type'] == "getaza")
{
    global $token;
    $data = getConfig();
    //echo json_encode($data);
    //print_r($data);
    echo $token;
}

if ($_GET['type'] == "getuseredit")
{
    $data = getEdit();
    echo json_encode($data);
    //print_r($data);
}

if ($_GET['type'] == "clearotkl")
{
    $data = clearOtkl($_GET['reportid']);
    echo json_encode($data);
   // print_r($data);
}

if ($_GET['type'] == "bottt")
{
    $data = botBot();
    //echo json_encode($data);
    print_r($data);
}

if ($_GET['type'] == "generatetoken")
{
    $data = generateToken($_COOKIE['userid']);
    echo json_encode($data);
    // print_r($data);
    
}

if ($_GET['type'] == "countreport")
{
    $data = countReport($_GET['reportid']);
    echo json_encode($data);
    // print_r($data);
    
}

if ($_GET['type'] == "countreportbb")
{
    $data = countReport($_GET['reportid']);
    echo json_encode($data);
    // print_r($data);
    
}

if ($_GET['type'] == "saveresttelegram")
{
    $data = addRestQuickRocc($_GET['reportid'], $_GET['restid']);
    echo json_encode($data);
}

if ($_GET['type'] == "getsubcat")
{
    $data = getSubcat($_GET['otklid'], $_GET['catid']);
    //print_r($data);
    echo json_encode($data);
}

if ($_GET['type'] == "saveotkl")
{
    $data = saveOtkl($_GET['id'], $_GET['mintext'], $_GET['fulltext']);
    echo json_encode($data);
}

if ($_GET['type'] == "saveotkllevel")
{
    $data = saveOtklLevel($_GET['id'], $_GET['cat'], $_GET['subcat']);
    echo json_encode($data);
}

if ($_GET['type'] == "getrocc")
{
    $data = getRocc($_GET['reportid']);
    echo json_encode($data);
}

if ($_GET['type'] == "getuserlist")
{
    $data = getAllUser();
    echo json_encode($data);
}

if ($_GET['type'] == "getimgrest")
{
    $data = getRoccImg($_GET['restid']);
    echo json_encode($data);
}

if ($_GET['type'] == "addrest")
{
    global $companyid;
    $data = addRest($_GET['restid'], $_GET['email'], $_GET['restname'], $_GET['restmng'], $_GET['adress'], $companyid);
    echo json_encode($data);
}

if ($_GET['type'] == "getlistdir")
{
    global $companyid;
    $data = getListDir($companyid);
    echo json_encode($data);
}

if ($_GET['type'] == "startrocc")
{
    $data = startReport($_GET['typerocc'], $_GET['restid']);
    echo json_encode($data);
}

if ($_GET['type'] == "proverka")
{
    $data = getCountNezaversh($_GET['restid']);
    echo json_encode($data);
}

if ($_GET['type'] == "auth")
{
    $data = auth($_GET['userid']);
    echo json_encode($data);
}

if ($_GET['type'] == "editrocc")
{
    $data = proverkaRocc($_GET['reportid'], $_GET['value'], $_GET['result']);
    echo json_encode($data);
}

if ($_GET['type'] == "coronovirus")
{

    $doc = file_get_contents('https://www.zakon.kz/zakon_cache/main_cache/coronavirus.json');
    $array = json_decode($doc, true);
    print_r($array);

}

/*
if(isset($_POST['startrocc'])){
$iduser = $_COOKIE['userid'];
$restid = $_POST['restid'];
$date = $_POST['date'];
$report = generate_pass(5);
$start = startReport($iduser,$restid,$date,$report);
$doc = file_get_contents('http://api.rocc.kz/api.php?type=qr&reportid='.$report);
header("Location: /2.php");
}

if($_GET['type']=="getrocc"){
    $data = getRocc();
    echo json_encode($data);
}*/


if(isset($_FILES['file'])) {
      // проверяем, можно ли загружать изображение
      $check = can_upload($_FILES['file']);
    
      if($check === true){
        // загружаем изображение на сервер
        make_upload($_FILES['file']);
        header('Location: /index.php?menu=my');
      }
      else{
        // выводим сообщение об ошибке
        echo "<strong>$check</strong>";  
      }
    }
    
?>
