<?php
session_start();
//ss
//* Подключаемые библиотеки
require_once 'db_connect.php';
include "libs/qr/qrlib.php";
require_once 'libs/Api2Pdf.php';
require_once 'libs/ApiResult.php';
require 'libs/mail/phpmailer/PHPMailerAutoload.php';
require 'libs/calendar.php';
require 'libs/pdfcrowd.php';
include ("SxGeo.php");
$SxGeo = new SxGeo('SxGeo.dat');
use Api2Pdf\Api2Pdf;
//* Подключаемые библиотеки
date_default_timezone_set('Asia/Almaty');
$datea = date("Y-m-d H:i:s");
$sysconfig = getConfig();
$token = $sysconfig[0]['telegram_token'];
$session = proverkaSession($_COOKIE['PHPSESSID']);

if ($session['status'] == "NO")
{
    session_start();
    setcookie('auth', '', time() + (-86400 * 5) , '/', 'roccc.top');
    setcookie('userid', '', time() + (-86400 * 5) , '/', 'roccc.top');
    setcookie('company', '', time() + (-86400 * 5) , '/', 'roccc.top');
}

// balans

function addPayment($companyid, $value, $type, $comment, $author)
{
    global $datea;

    $user = R::dispense('payment');
    $user->userid = "";
    $user->companyid = $companyid;
    $user->isdeleted = "0";
    $user->createtime = $datea;
    $user->value = $value;
    $user->type = $type;
    $user->comment = $comment;
    $user->author = $author;
    R::store($user);

    return null;
}

function blockedCompany($companyid, $value)
{
    global $datea;
    global $mysqli;

    $res = $mysqli->query("UPDATE company SET blocked = '1' WHERE id = '$companyid'");
    
    $user = R::dispense('nopay');
    $user->companyid = $companyid;
    $user->isdeleted = "0";
    $user->createtime = $datea;
    $user->value = $value;
    R::store($user);

    return null;
}

function spisanieBalans($company,$money)
{

 for ($i = 0;$i < sizeof($company);$i++)
    {
        if ($company[$i]['balans']>= $money)
        {
          $data = addPayment($company[$i]['id'], -$money, "outpay", "Списание за тарифный план", 1);
        }else{
            $proverka = proverkaBlock($company);
            if($proverka['status']=="OK"){
               $data = blockedCompany($company[$i]['id'], -$money); 
            }else{
              //sendemail
            }
            
        }
    }

    return $data;
}

function botDaemon()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT * 
FROM nopay
WHERE isdeleted = 0
");
    foreach ($res as $value)
    {
        $users[] = $value;
    }

   	 for ($i = 0;$i < sizeof($users);$i++)
    {
        $data = spisanieBalans($users[$i]['companyid'],$users[$i]['value']);
   }

    return null;
}

function getBalansCompany()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT 
c.*,
SUM(p.value) balans
FROM company c
LEFT JOIN payment p
ON c.id = p.companyid
GROUP BY c.id
    ");
    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users;
}

// balans
//* Данные о пользователе
function getIpInfo()
{

    $ip = $_SERVER['REMOTE_ADDR'];
    require_once 'SxGeo.php';
    // подключаем файл с базой данных городов
    $SxGeo = new SxGeo('SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY);
    $city = $SxGeo->get($ip);
    // также можно использовать следующий код
    // $SxGeo->getCity($ip);
    $data['country'] = $city['country']['iso'];
    $data['city'] = $city['city']['name_ru'];
    $data['ip'] = $_SERVER['REMOTE_ADDR'];
    $data['data'] = $city;
    return $data;
}

function getUserByTid($login)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT *
FROM users
WHERE telegram = '$login'
    ");
    foreach ($res as $value)
    {
        $users[] = $value;
    }
    if ($users == null)
    {
        $data['status'] = "NO";
    }
    else
    {
        $data['status'] = "OK";
        $data['user'] = $users[0];
    }
    return $data;
}

function proverkaBlock($companyid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT * 
FROM nopay
WHERE companyid = '$companyid'
AND isdeleted = 0
    ");
    foreach ($res as $value)
    {
        $users[] = $value;
    }
    if ($users == null)
    {
        $data['status'] = "OK";
    }
    else
    {
        $data['status'] = "NO";
        $data['user'] = $users[0];
    }
    return $data;
}


function getUserByIda($login)
{

    global $mysqli;

    $res = $mysqli->query("SELECT * FROM users ORDER BY `id` DESC LIMIT 1000");
    foreach ($res as $value)
    {
        $users[] = $value;
    }

    for ($i = 0;$i < sizeof($users);$i++)
    {
        if ($users[$i]['phone'] === $login)
        {
            return $users[$i];
        }
    }

    return null;
}

function getUserById($login)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT 
u.id user_id,
u.phone user_phone,
u.email user_email,
u.name user_name,
u.img user_img,
u.vac user_vac,
u.restid user_restid,
r.restname user_restname,
u.telegram user_telegram,
c.id user_companyid,
c.companyname user_companyname,
c.adress user_companyadresss,
c.email user_companyemail,
v.vacname user_vacname
FROM users u
LEFT JOIN rest r
ON u.restid = r.restid
LEFT JOIN company c
ON u.companyid = c.id
LEFT JOIN vac v
ON u.vac = v.id
WHERE u.id = '$login'
    ");
    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users[0];
}

function getPlanned($userid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT
DATE_FORMAT(p.plannedtime, '%d.%m') as calendar,
p.id plan_id,
p.author plan_author_id,
u.name plan_author_name,
p.auditor plan_auditor_id,
us.name plan_auditor_name,
p.createtime plan_createtime,
p.plannedtime plan_plantime,
p.reportid plan_reportid,
rrr.opendate plan_report_starttime,
rrr.closedate plan_report_closetime,
rs.statusname plan_report_status_name,
s.statusname plan_statusname,
p.status plan_status,
r.restname plan_restname,
r.adress plan_rest_adress,
r.img plan_rest_img
FROM planned p
LEFT JOIN rest r
ON p.restid = r.restid
LEFT JOIN users u
ON p.author = u.id
LEFT JOIN users us
ON p.auditor = us.id
LEFT JOIN status s
ON p.status = s.statusid
LEFT JOIN rocc rrr
ON p.reportid = rrr.id
LEFT JOIN status rs
ON rrr.status = rs.statusid
WHERE p.auditor = '$userid'
    ");
    foreach ($res as $value)
    {
        $users[] = $value;
    }

for ($i = 0;$i < sizeof($users);$i++)
    {
$events[]="";
$events= array(
	$users[$i]['calendar']   => "2222"
);
    }

    $data['spisok'] = $events;
    $data['calendar'] = $users;
    
    return $data;
}

function userInfo($userid){

$data['user'] = getUserById($userid);
$data['user_session'] = getSession($userid);
//$data['user_telegramsession'] = getTelegramSession($userid);
$data['user_rest'] = getRestUser($userid);
$data['audits_rest'] = getRoccUser($userid,"user");
if($data['user']['user_vac']=="2"){}else{
$data['audits_auditor'] = getRoccUser($userid,"rest");    
}

//if($data['user']['user_vac']=="2"){
    $data['raiting_dir'] == countProcent($user['user']['user_restid']);
//}else{
//  $data['raiting_dir'] == "0";
//}
    return $data;
}

function getRoccUser($userid,$type)
{

    global $mysqli;

   if($type=="user"){
     $res = $mysqli->query("
SELECT r.id          rocc_id,
       r.restid,
       r.opendate    rocc_opentime,
       r.closedate   rocc_closetime,
       r.status      rocc_status,
       s.statusname  rocc_statusname,
       s.statuscss  rocc_statuscss,
       r.comment     rocc_comment,
       r.auditor     auditor_id,
       r.istelegram    rocc_telegram,
       u.name        auditor_name,
       rr.restname   rest_name,
       rr.restid     rest_id,
       rr.img        rest_img,
       rr.adress     rest_adress,
       rr.email      rest_email,
       c.companyname company_name,
       c.id          company_id,
       c.adress      company_adress,
       a.id          actionplan_id,
       a.createtime  actionplan_date,
       ss.statusname actionplan_status,
       ss.statuscss  actionplan_css,
       rt.resultname rocc_result
FROM rocc r
         LEFT JOIN users u
                   ON r.auditor = u.id
         LEFT JOIN status s
                   ON r.status = s.statusid
         LEFT JOIN rest rr
                   ON r.restid = rr.restid
         LEFT JOIN company c
                   on c.id = r.company
         LEFT JOIN actionplan a
                   ON a.reportid = r.id
         LEFT JOIN status ss
                   ON a.status = ss.statusid
         LEFT JOIN result rt
                   ON r.result = rt.id
WHERE r.isdeleted = 0
AND r.auditor = '$userid'
  ");
   }else{
 $res = $mysqli->query("
SELECT r.id          rocc_id,
       r.restid,
       r.opendate    rocc_opentime,
       r.closedate   rocc_closetime,
       r.status      rocc_status,
       s.statusname  rocc_statusname,
       s.statuscss  rocc_statuscss,
       r.comment     rocc_comment,
       r.auditor     auditor_id,
       r.istelegram    rocc_telegram,
       u.name        auditor_name,
       rr.restname   rest_name,
       rr.restid     rest_id,
       rr.img        rest_img,
       rr.adress     rest_adress,
       rr.email      rest_email,
       c.companyname company_name,
       c.id          company_id,
       c.adress      company_adress,
       a.id          actionplan_id,
       a.createtime  actionplan_date,
       ss.statusname actionplan_status,
       ss.statuscss  actionplan_css,
       rt.resultname rocc_result
FROM rocc r
         LEFT JOIN users u
                   ON r.auditor = u.id
         LEFT JOIN status s
                   ON r.status = s.statusid
         LEFT JOIN rest rr
                   ON r.restid = rr.restid
         LEFT JOIN company c
                   on c.id = r.company
         LEFT JOIN actionplan a
                   ON a.reportid = r.id
         LEFT JOIN status ss
                   ON a.status = ss.statusid
         LEFT JOIN result rt
                   ON r.result = rt.id
WHERE r.isdeleted = 0
AND r.restid = '$userid'
  ");
   }

    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function saveTelegram($userid, $telegramid, $chatid)
{

    global $mysqli;

    $res = $mysqli->query("
UPDATE users 
SET telegram = '$telegramid',
chatid = '$chatid'
WHERE id = $userid
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return null;
}

function getUser($userid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM users u
WHERE u.id = $userid
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data[0];
}

//* Данные о пользователе


//* Авторизация и сессии
function auth($userid)
{
    $user = getUser($userid);
    session_start();
    setcookie('auth', '1', time() + (-86400 * 5) , '/', 'roccc.top');
    setcookie('userid', $userid['id'], time() + (-86400 * 5) , '/', 'roccc.top');
    setcookie('company', $user['companyid'], time() + (-86400 * 5) , '/', 'roccc.top');

    setcookie('auth', '1', time() + (86400 * 5) , '/', 'roccc.top');
    setcookie('userid', $userid['id'], time() + (86400 * 5) , '/', 'roccc.top');
    setcookie('company', $user['companyid'], time() + (86400 * 5) , '/', 'roccc.top');

    $data['status'] = "OK";
    $aza = saveSession($_COOKIE['PHPSESSID'], $userid['id']);
    return $data;

}

function saveSession($sessionid, $userid)
{
    global $datea;
    $browser = getInfoBrowser();
    $infouser = getIpInfo();

    $infobrowser = $browser['name'] . " " . $browser['version'];
    $user = R::dispense('session');
    $user->sessionid = $sessionid;
    $user->userid = $userid;
    $user->browser = $infobrowser;
    $user->userip = $infouser['ip'];
    $user->country = $infouser['country'];
    $user->city = $infouser['city'];
    $user->browser = $infobrowser;
    $user->createtime = $datea;
    $user->isdeleted = "0";
    R::store($user);

    return null;
}
function proverkaSession($sessionid)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT * 
    FROM session 
    WHERE isdeleted='0'
    AND sessionid  LIKE '$sessionid'
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }
    if ($users[0] == null)
    {
        $data['status'] = "NO";
    }
    else
    {
        $data['status'] = "OK";
    }
    return $data;
}

//* Авторизация и сессии
function sendBot($message)
{
    global $token;
    //$token = '1012040761:AAHJnozJada_Z5XAypsAFl-3DYvJNJUb3HE';
    //$chatid = "430768369";
    $chatid = "-1001434933289";
    $response = array(
        'chat_id' => $chatid,
        'text' => $message
    );

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);
    return null;
}

function sendBotCorona($message)
{
    global $token;
    //$token = '1012040761:AAHJnozJada_Z5XAypsAFl-3DYvJNJUb3HE';
    $chatid = "430768369";
    //$chatid = "-1001434933289";
    $response = array(
        'chat_id' => $chatid,
        'text' => $message
    );

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);
    return null;
}

function pingCheck($url)
{
    $agent = "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0";

    // Инициализация CURL
    $ch = curl_init();

    // Установка URL
    curl_setopt($ch, CURLOPT_URL, $url);

    // Указываю USERAGENT браузера
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);

    // Header
    curl_setopt($ch, CURLOPT_NOBODY, true);

    // Редирект
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Возврат строки
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Отключение из вывода отладочной информации
    curl_setopt($ch, CURLOPT_VERBOSE, false);

    // Устанавливаю максимальное количество секунд работы
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    // Выполнение
    curl_exec($ch);

    // Получаю код HTTP ответа
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Если ответ от сервера > 200 - тогда сайт доступен
    return $httpcode;

}

function reReportid($reportid)
{
    setcookie('r_reportid', $reportid, time() + (-86400 * 5) , '/', 'roccc.top');
    setcookie('r_reportid', $reportid, time() + (86400 * 5) , '/', 'roccc.top');

    return null;
}

function generateQr($reportid)
{

    $filename = "img/qr/$reportid.png";

    QRcode::png("http://dev.roccc.top/view.php?reportid=$reportid", $filename, "H", 4, 4);

    return $filename;
}

function generatePdfB($type, $reportid)
{

    if ($type == null)
    {
        $data['status'] = "NO";
    }
    else
    {
        if ($reportid == null)
        {
            $data['status'] = "NO";
        }
        else
        {
            $output_stream = fopen("example.pdf", "wb");
            $api = new \Pdfcrowd\HtmlToPdfClient("7029677226aza", "fb6a6e192ef1c11d93e2bc2e3de59d77");
            $api->setPageSize("A4");
            $api->setOrientation("portrait");
            $api->setPageMargins('0cm', '0cm', '0cm', '0cm');
            $api->convertUrlToFile("http://dev.roccc.top/generatepdf.php?reportid=$reportid", "reports/".$reportid."/report.pdf");
        }
    }

    return $data;

}

function generatePdf($type, $reportid)
{

    if ($type == null)
    {
        $data['status'] = "NO";
    }
    else
    {
        if ($reportid == null)
        {
            $data['status'] = "NO";
        }
        else
        {
            $apiClient = new Api2Pdf('6488fb71-cc3a-4a59-a36e-e850035ba845');
            $filename = "report_" . $reportid . ".pdf";
            $apiClient->setInline(true);
            $apiClient->setFilename($filename);
            $apiClient->setOptions(['orientation' => 'portrait', 'pageSize' => 'A4', 'marginBottom' => '0', 'marginLeft' => '0', 'marginTop' => '0', 'marginRight' => '0', 'title' => $filename

            ]);
            $result = $apiClient->wkHtmlToPdfFromUrl('http://dev.roccc.top/generatepdf.php?reportid=' . $reportid);

            $data['status'] = "OK";
            $data['url'] = $result->getPdf();
            $data['server'] = savePdfServer($data['url'],$reportid);
        }
    }

    return $data;

}

function savePdfServer($url,$reportid)
{	
	$datea = date("YmdHis");
    //$url = 'https://storage.googleapis.com/a2p-v2-storage/914b1c04-cf7e-48b6-b03d-6b5c3ea4f588';
    $dir = '/report/report_'.$reportid.'_'.$datea.'.pdf';
	$path = $_SERVER['DOCUMENT_ROOT'] . $dir;
	file_put_contents($path, file_get_contents($url));
	$dir = 'report/report_'.$reportid.'_'.$datea.'.pdf';
    return $dir;
}

function addMessage($chatid, $author, $message)
{
    sendMes($chatid, $message);
    global $datea;

    $user = R::dispense('telegramchat');
    $user->chatid = $chatid;
    $user->author = $author;
    $user->message = $message;
    $user->createtime = $datea;
    $user->isdeleted = "0";
    R::store($user);

    return null;
}

function sendMes($chatid, $message)
{
    global $token;
    //$token = '1012040761:AAHJnozJada_Z5XAypsAFl-3DYvJNJUb3HE';
    $response = array(
        'chat_id' => $chatid,
        'text' => $message
    );

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);

    return null;
}

function getListChat()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT * FROM telegramchat
WHERE isdeleted = 0
GROUP BY chatid DESC 
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function deleteReport($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
UPDATE rocc r
SET r.isdeleted = 1
WHERE r.id = '$reportid'
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }
    $data['status'] = "OK";
    return $data;
}

function getListChatMessage($chatid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT * FROM telegramchat
WHERE isdeleted = 0
AND chatid = '$chatid'
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function addRest($restid, $email, $restname, $restmng, $adress, $company)
{
    global $datea;

    if ($restid . length > 3)
    {
        $prover = valiDate("rest", "restid", $restid);
        if ($prover['status'] == "OK")
        {
            if ($restname . length < 3)
            {
                if($restmng . length < 3){

                    $user = R::dispense('rest');
                $user->restid = $restid;
                $user->email = $email;
                $user->restname = $restname;
                $user->restmng = $restmng;
                $user->adress = $adress;
                $user->company = $company;
                $user->isdeleted = "0";
                $user->createtime = $datea;
                R::store($user);

                $data['status'] = "OK";
                }else{
                    $data['status'] = "NO";
                    $data['message'] = "Выберите директора";
                }
            }
            else
            {
                $data['status'] = "NO";
                $data['message'] = "Ошибка в названии ресторана";
            }
        }
        else
        {
            $data['status'] = "NO";
            $data['message'] = "С указанным id ресторан уже существует";
        }
    }
    else
    {
        $data['status'] = "NO";
        $data['message'] = "Ошибка в ID ресторана";
    }

    return $data;
}

function addOtkl($mintext, $fulltext, $category, $subcategory, $restid, $reportid, $img, $origimg)
{
    global $datea;

    $user = R::dispense('otkloneniya');
    $user->mintext = $mintext;
    $user->fulltext = $fulltext;
    $user->category = $category;
    $user->subcategory = $subcategory;
    $user->createtime = $datea;
    $user->restid = $restid;
    $user->isdeleted = "0";
    $user->reportid = $reportid;
    $user->img = $img;
    $user->originalimg = $origimg;
    R::store($user);

    return $user['id'];
}

function botBot()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM ochered
WHERE isdeleted = 0
AND status = 'start'
");
    foreach ($res as $value)
    {
        $users[] = $value;
    }

   	 for ($i = 0;$i < sizeof($users);$i++)
    {
        if ($users[$i]['status'] === "start")
        {
            $data = getDannye($users[$i]['reportid']);
            sendEmail($users[$i]['reportid'], $data['rest_email'], $data['auditor_email'], $data['company_email'], $data['rocc_resultname'], $data['rest_name'], $data['dir_telegram']);
            deleteOchered($users[$i]['reportid']);
        }
   }

    return null;
}



function getDannye($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT 
r.result rocc_result_id,
res.resultname rocc_resultname,
r.restid rocc_restid,
rr.email rest_email,
rr.restname rest_name,
u.email auditor_email,
u.name auditor_name,
c.companyname company_name,
c.email company_email,
us.telegram dir_telegram
FROM rocc r
LEFT JOIN rest rr
ON r.restid = rr.restid
LEFT JOIN users u
ON r.auditor = u.id
LEFT JOIN users us
ON rr.restmng = us.id
LEFT JOIN company c
ON r.company = c.id
LEFT JOIN result res
ON r.result = res.id
WHERE r.id = $reportid
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data[0];
}

function sendEmail($reportid, $email, $auditor, $tu, $result, $restname, $chatid)
{

	$file = generatePdf("final", $reportid);
	$reportpdf = $file['server'];
	$pdf = "/".$file['server'];
	$text = "Ресторан: ".$restname.", результат аудита: ".$result;
	sendReportTelegram($chatid, $pdf, $text);
    $mail = new PHPMailer;

    $mail->isSMTP();

    $mail->Host = 'smtp.mail.ru';
    $mail->SMTPAuth = true;
    $mail->Username = "9677226@mail.ru"; // логин от вашей почты
    $mail->Password = "Umag2020!"; // пароль от почтового ящика
    $mail->SMTPSecure = 'ssl';
    $mail->Port = '465';

    $mail->CharSet = 'UTF-8';
    $mail->From = "9677226@mail.ru"; // адрес почты, с которой идет отправка
    $mail->FromName = "9677226@mail.ru"; // имя отправителя
    $mail->addAddress($email, 'Имя');
    $mail->addAddress($tu); // Email получателя
	$mail->addAddress($auditor); // Еще один email, если 
    //$mail->addAddress($tu, 'Имя 2');
    $mail->addCC($email);

    $mail->isHTML(true);
    //$file = generatePdf("1", $reportid);
    //$filename = $file['url'];
    $mail->Subject = 'Отчет проведенного аудита. Ресторан: '.$restname.' Результат аудита: '.$result;
    $mail->Body = "Отчет проведенного аудита во вложении к письму";
    $mail->AltBody = 'Azat Kadyr';
    $mail->addAttachment($reportpdf);
    if (!$mail->send())
    {
        echo 'Ошибка при отправке. Ошибка: ' . $mail->ErrorInfo;
    }
    else
    {

    }
    return null;

}

function startReport($type, $restid)
{
    global $datea;

    if (isset($restid))
    {
        if (isset($type))
        {   
            addHistory($reportid,"start",$_COOKIE['userid']);
            $user = userInfo($_COOKIE['userid']);
            if($user['user']['user_vac'] == "2"){
                
                $user = R::dispense('rocc');
            $user->company = $_COOKIE['company'];
            $user->restid = $restid;
            $user->auditor = $_COOKIE['userid'];
            $user->opendate = $datea;
            $user->status = "start";
            $user->result = "4";
            $user->comment = "";
            $user->isdeleted = "0";
            R::store($user);

            $data['status'] = "OK";
            $data['reportid'] = $user['id'];

            setcookie('r_reportid', $user['id'], time() + (-86400 * 5) , '/', 'roccc.top');
            setcookie('r_reportid', $user['id'], time() + (86400 * 5) , '/', 'roccc.top');
            
        }else{
            $data['status'] = "NO";
            $data['message'] = "Аудиты может создавать только аудиторы, ваша текущая должность ".$user['user']['user_vacname'];
        }
            

        }
        else
        {
            $data['status'] = "NO";
        }
    }
    else
    {
        $data['status'] = "NO";
    }

    return $data;

}

function sendMail($email,$message,$subject)
{

    $mail = new PHPMailer;

    $mail->isSMTP();

    $mail->Host = 'smtp.mail.ru';
    $mail->SMTPAuth = true;
    $mail->Username = "9677226@mail.ru"; // логин от вашей почты
    $mail->Password = "Umag2020!"; // пароль от почтового ящика
    $mail->SMTPSecure = 'ssl';
    $mail->Port = '465';

    $mail->CharSet = 'UTF-8';
    $mail->From = "9677226@mail.ru"; // адрес почты, с которой идет отправка
    $mail->FromName = "9677226@mail.ru"; // имя отправителя
    $mail->addAddress($email, 'Имя');

    $mail->isHTML(true);
    //$file = generatePdf("1", $reportid);
    //$filename = $file['url'];
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = 'Azat Kadyr';
    if (!$mail->send())
    {
        echo 'Ошибка при отправке. Ошибка: ' . $mail->ErrorInfo;
    }
    else
    {

    }
    return null;

}

function startReportB($type, $restid, $userid, $company)
{
    global $datea;

    if (isset($restid))
    {
        if (isset($type))
        {
            addHistory($reportid,$type,$userid);
            $user = R::dispense('rocc');
            $user->company = $company;
            $user->restid = $restid;
            $user->auditor = $userid;
            $user->opendate = $datea;
            $user->status = "start";
            $user->result = "14";
            $user->comment = "";
            $user->isdeleted = "0";
            $user->istelegram = "1";
            R::store($user);

            $data['status'] = "OK";
            $data['reportid'] = $user['id'];

            $userb = R::dispense('quickrocc');
            $userb->userid = $userid;
            $userb->opendate = $datea;
            $userb->status = "start";
            $userb->isdeleted = "0";
            $userb->istelegram = "1";
            $userb->reportid = $user['id'];
            R::store($userb);

            setcookie('r_reportid', $user['id'], time() + (-86400 * 5) , '/', 'roccc.top');
            setcookie('r_reportid', $user['id'], time() + (86400 * 5) , '/', 'roccc.top');

        }
        else
        {
            $data['status'] = "NO";
        }
    }
    else
    {
        $data['status'] = "NO";
    }

    return $data;

}

/*function createRocc($userid,$restid,$status,$comment){

   try {
       
        global $datea;
        global $conn;

        $sql = "
            INSERT INTO rocc (auditora, restid, opendate, status, comment)
            VALUES ('$userid', '$restid', '$datea', '$status', '$comment', '0')
        ";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $value){      
      $data[] = $value;
    }
        
    }catch (PDOException $e){
        echo "Error!: " . $e->getMessage() . "<br/>";
    }
    return $data;
}*/

function getAll()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT r.id          rocc_id,
       r.restid,
       r.opendate    rocc_opentime,
       r.closedate   rocc_closetime,
       r.status      rocc_status,
       s.statusname  rocc_statusname,
       s.statuscss  rocc_statuscss,
       r.comment     rocc_comment,
       r.auditor     auditor_id,
       u.name        auditor_name,
       rr.restname   rest_name,
       rr.restid     rest_id,
       rr.adress     rest_adress,
       rr.email     rest_email,
       rr.img        rest_img,
       c.companyname company_name,
       c.id          company_id,
       c.adress      company_adress,
       a.id          actionplan_id,
       a.createtime  actionplan_date,
       ss.statusname actionplan_status,
       ss.statuscss  actionplan_css
FROM rocc r
         LEFT JOIN users u
                   ON r.auditor = u.id
         LEFT JOIN status s
                   ON r.status = s.statusid
         LEFT JOIN rest rr
                   ON r.restid = rr.restid
         LEFT JOIN company c
                   on c.id = r.company
         LEFT JOIN actionplan a
                   ON a.reportid = r.id
         LEFT JOIN status ss
                   ON a.status = ss.statusid
WHERE r.isdeleted = 0
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function countLevel($reportid, $cat, $subcat)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT 
COUNT(o.id) count_level
FROM otkloneniya o
WHERE o.category = $cat
AND o.subcategory = $subcat
AND o.reportid = $reportid
AND o.isdeleted = 0
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data[0]['count_level'];
}

function countReport($reportid)
{

    $data['pb']['l1'] = countLevel($reportid, "1", "2");
    $data['pb']['l3'] = countLevel($reportid, "1", "3");

    $data['sb']['l1'] = countLevel($reportid, "4", "5");
    $data['sb']['l2'] = countLevel($reportid, "4", "6");
    $data['sb']['l3'] = countLevel($reportid, "4", "7");

    $data['ls']['l1'] = countLevel($reportid, "8", "11");
    $data['ls']['l2'] = countLevel($reportid, "8", "12");
    $data['ls']['l3'] = countLevel($reportid, "8", "13");

    if ($data['pb']['l3'] > 0)
    {
        $data['pb']['result'] = "Underperfoming";
        $data['pb']['result_css'] = "danger";
    }
    else
    {
        if ($data['pb']['l1'] > 9)
        {
            $data['pb']['result'] = "Underperfoming";
            $data['pb']['result_css'] = "danger";
        }
        else
        {
            $data['pb']['result'] = "At standart";
            $data['pb']['result_css'] = "success";
        }
    }

    if ($data['sb']['l3'] > 0)
    {
        $data['sb']['result'] = "Underperfoming";
        $data['sb']['result_css'] = "danger";
    }
    else
    {
        if ($data['sb']['l2'] >= 4)
        {
            $data['sb']['result'] = "Underperfoming";
            $data['sb']['result_css'] = "danger";
        }
        else
        {
            if ($data['sb']['l1'] >= 15)
            {
                $data['sb']['result'] = "Underperfoming";
                $data['sb']['result_css'] = "danger";
            }
            else
            {
                if ($data['sb']['l2'] > 2)
                {
                    $data['sb']['result'] = "Marginal";
                    $data['sb']['result_css'] = "warning";
                }
                else
                {
                    if ($data['sb']['l1'] > 10)
                    {
                        $data['sb']['result'] = "Marginal";
                        $data['sb']['result_css'] = "warning";
                    }
                    else
                    {
                        if ($data['sb']['l1'] < 10)
                        {
                            $data['sb']['result'] = "At standart";
                            $data['sb']['result_css'] = "success";
                        }
                    }
                }
            }
        }
    }

    if ($data['ls']['l3'] > 0)
    {
        $data['ls']['result'] = "Underperfoming";
        $data['ls']['result_css'] = "danger";
    }
    else
    {
        if ($data['ls']['l2'] >= 3)
        {
            $data['ls']['result'] = "Underperfoming";
            $data['ls']['result_css'] = "danger";
        }
        else
        {
            if ($data['ls']['l1'] > 5)
            {
                $data['ls']['result'] = "Underperfoming";
                $data['ls']['result_css'] = "danger";
            }
            else
            {
                if ($data['ls']['l2'] >= 2)
                {
                    $data['ls']['result'] = "Marginal";
                    $data['ls']['result_css'] = "warning";
                }
                else
                {
                    if ($data['ls']['l1'] >= 5)
                    {
                        $data['ls']['result'] = "Marginal";
                        $data['ls']['result_css'] = "warning";
                    }
                    else
                    {
                        if ($data['ls']['l1'] < 5)
                        {
                            $data['ls']['result'] = "At standart";
                            $data['ls']['result_css'] = "success";
                        }
                    }
                }
            }
        }
    }

    if ($data['pb']['result'] == "Underperfoming")
    {
        //proval
        $data['obw_result'] = "Underperfoming";
        $data['id_result'] = "2";
    }
    else
    {
        if ($data['sb']['result'] == "Underperfoming")
        {
            //proval
            $data['obw_result'] = "Underperfoming";
            $data['id_result'] = "2";
        }
        else
        {
            if ($data['ls']['result'] == "Underperfoming")
            {
                //proval
                $data['obw_result'] = "Underperfoming";
                $data['id_result'] = "2";
            }
            else
            {
                if ($data['pb']['result'] == "Marginal")
                {
                    //marginal
                    $data['obw_result'] = "Marginal";
                    $data['id_result'] = "3";
                }
                else
                {
                    if ($data['sb']['result'] == "Marginal")
                    {
                        //marginal
                        $data['obw_result'] = "Marginal";
                        $data['id_result'] = "3";
                    }
                    else
                    {
                        if ($data['ls']['result'] == "Marginal")
                        {
                            //marginal
                            $data['obw_result'] = "Marginal";
                            $data['id_result'] = "3";
                        }
                        else
                        {
                            //At standart
                            $data['obw_result'] = "At standart";
                            $data['id_result'] = "1";
                        }
                    }
                }
            }
        }
    }

    return $data;
}

/*function countReport($reportid) {
    
$data['pb']['l1'] = countLevel($reportid,"1","2");
$data['pb']['l3'] = countLevel($reportid,"1","3");

$data['sb']['l1'] = countLevel($reportid,"4","5");
$data['sb']['l2'] = countLevel($reportid,"4","6");
$data['sb']['l3'] = countLevel($reportid,"4","7");

$data['ls']['l1'] = countLevel($reportid,"8","11");
$data['ls']['l2'] = countLevel($reportid,"8","12");
$data['ls']['l3'] = countLevel($reportid,"8","13");

if($data['pb']['l3']>0){
    $data['pb']['result'] = "Proval";
}else{
    if($data['pb']['l1']>10){
    $data['pb']['result'] = "Proval";   
    }else{
        $data['pb']['result'] = "At standart"; 
    }
}

if($data['sb']['l3']>0){
    $data['sb']['result'] = "Proval";
}else{
    if($data['sb']['l2']>5){
    $data['sb']['result'] = "Proval";   
    }else{
        if($data['sb']['l1']>12){
            $data['sb']['result'] = "Proval"; 
        }else{
            $data['sb']['result'] = "At standart"; 
        }
    }
}

if($data['ls']['l3']>0){
    $data['ls']['result'] = "Proval";
}else{
    if($data['ls']['l2']>4){
    $data['ls']['result'] = "Proval";   
    }else{
        $data['ls']['result'] = "At standart"; 
    }
}
*/

/*
function countReport($reportid) {
    
$data['pb']['l1'] = countLevel($reportid,"1","2");
$data['pb']['l3'] = countLevel($reportid,"1","3");

$data['sb']['l1'] = countLevel($reportid,"4","5");
$data['sb']['l2'] = countLevel($reportid,"4","6");
$data['sb']['l3'] = countLevel($reportid,"4","7");

$data['ls']['l1'] = countLevel($reportid,"8","11");
$data['ls']['l2'] = countLevel($reportid,"8","12");
$data['ls']['l3'] = countLevel($reportid,"8","13");

if($data['pb']['l3']>0){
    $data['pb']['result'] = "Proval";
}else{
    if($data['pb']['l1']>10){
    $data['pb']['result'] = "Proval";   
    }else{
        $data['pb']['result'] = "At standart"; 
    }
}

if($data['sb']['l1']<10){
    //at stan
    if($data['sb']['l2']>2){
        //marginal
        if($data['sb']['l2']){
            
        }
    }else{
        if($data['sb']['l1']>10){
            
        }
    }
}else{
    //proval
}

    return $data;
}*/

function proverkaRocc($reportid, $value, $result)
{

    $pr = getRocc($reportid);
    
    if ($value == "start")
    {

        if ($pr['info']['actionplan_id'] == null)
        {   
            $user = userInfo($_COOKIE['userid']);
            if($user['user']['user_vac'] == "2"){
                editedRocc($reportid, $value, "14");
                addHistory($reportid,"restart",$_COOKIE['userid']);
                deleteOchered($reportid);
            $data['status'] = "OK";
        }else{
            $data['status'] = "NO";
            $data['message'] = "Аудиты может создавать только аудиторы, ваша текущая должность ".$user['user']['user_vacname'];
        }
        }
        else
        {
            $data['status'] = "NO";
            $data['message'] = "По данному аудиту заполнен Acplan, невозможно отменить";
        }
    }

    if ($value == "final")
    {
        if ($pr['otkl'] == null)
        {
            $data['status'] = "NO";
            $data['message'] = "Нельзя завершить аудит без отклонений";
        }
        else
        {
            $two = validateRocc($reportid);
            if ($two['status'] == "NO")
            {
                $data = $two;
            }
            else
            {   
                $a = validateRoccCategory($reportid);
                if($a['status'] == "NO"){
                        $data = $a;
                }else{
                    $b = validateRoccSubCategory($reportid);
                    if($b['status']=="NO"){
                        $data = $b;
                    }else{
                        //$mail = sendEmail($reportid, $pr['info']['rest_email']);
                        editedRocc($reportid, $value, $result);
                        addHistory($reportid,"final",$_COOKIE['userid']);
                        endQuickRocc($reportid);
                        addOchered($reportid);
                        $data['status'] = "OK";
                    }
                    
                }
            }
        }
    }

    return $data;
}

function editedRocc($reportid, $value, $result)
{
    
    global $mysqli;

    $res = $mysqli->query("
  UPDATE rocc 
SET status = '$value',
result = '$result'
WHERE id = $reportid");
    foreach ($res as $value)
    {
        $data[] = $value;
    }
    $data['status'] = "OK";
    return $data;
}

function getAllCompany()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM company c
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function valiDate($bdname, $row, $valued)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT
COUNT(id) vol
FROM $bdname
WHERE $row = $valued
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }
    if ($data[0]['vol'] == 0)
    {
        $datab['status'] = "OK";
    }
    else
    {
        $datab['status'] = "NO";
    }

    return $datab;
}

function getAllUser()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM users u
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

/*function findQuickRocc($login){
    

    global $mysqli;

    $res=$mysqli->query("SELECT * FROM quickrocc WHERE status = 'start' ORDER BY `id` DESC LIMIT 1000");
    foreach($res as $value){      
      $users[] = $value;
    }

        for($i=0;$i<sizeof($users);$i++){
            if($users[$i]['userid']===$login){
                $data['status'] = "OK";
                $data['user'] = $users[$i];
            }else{
                $data['status'] = "NO";
            }
        }

        return $data;
    }*/

function findQuickRocc($login)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT *
FROM quickrocc
WHERE status = 'start'
AND userid = '$login'
    ");
    foreach ($res as $value)
    {
        $users[] = $value;
    }
    if ($users == null)
    {
        $data['status'] = "NO";
    }
    else
    {
        $data['status'] = "OK";
        $data['user'] = $users[0];
    }
    return $data;
}

function endQuickRocc($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
UPDATE quickrocc 
SET status = 'final' 
WHERE reportid='$reportid'
    ");

    return null;
}
/*

*/

function getListDir($companyid)
{

    global $mysqli;

    $res = $mysqli->query("
  SELECT 
u.id dir_id,
u.name dir_name,
u.phone dir_phone,
u.email dir_email,
v.vacname dir_vac
FROM users u
LEFT JOIN vac v
ON u.vac = v.id
WHERE u.vac = 2
AND u.companyid = $companyid
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function getlevel($otklid)
{

    global $mysqli;

    $res = $mysqli->query("
  SELECT 
o.category otkl_catid,
o.subcategory otkl_subcatid,
l.name otkl_catname,
ll.name otkl_subcatname
FROM otkloneniya o
LEFT JOIN level l
ON o.category = l.id
LEFT JOIN level ll
ON o.subcategory = ll.id
WHERE o.id = $otklid;
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }
    $spisok = getSpisoklevel();
    $level['spisok'] = $spisok;
    $level['main'] = $data;
    return $level;
}

function getSpisoklevel()
{

    global $mysqli;

    $res = $mysqli->query("
 SELECT 
l.name cat_name,
l.id cat_id
FROM level l
WHERE l.type='parent'
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function getSubcat($otklid, $catid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT 
l.id subcat_id,
l.name subcat_name
FROM level l
WHERE l.parentid = $catid
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    $cat = getlevel($otklid);
    $subcat['cat'] = $cat;
    $subcat['spisok'] = $data;

    return $subcat;
}

function getCountNezaversh($restid)
{

    global $mysqli;
    $userid = $_COOKIE['userid'];

    $res = $mysqli->query("
  SELECT COUNT(r.id) count
  FROM rocc r
  WHERE auditor = $userid
  AND status = 'start'
  AND restid = $restid
  AND isdeleted = 0
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data[0];
}

function getCompanyInfo($companyid, $typed, $datea, $dateb)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM company c
WHERE c.id = $companyid
  ");
    foreach ($res as $value)
    {
        $company[] = $value;
    }

    $resa = $mysqli->query("
SELECT r.*,
u.name dir_name,
u.email dir_email
FROM rest r
LEFT JOIN users u
ON u.id = r.restmng
WHERE r.company = $companyid
AND r.isdeleted = '0';
  ");
    foreach ($resa as $value)
    {
        $rest[] = $value;
    }
    $rocc = getRoccCompany($companyid, $typed, $datea, $dateb);
    $dir = getListDir($companyid);

    $data['company_info'] = $company[0];
    $data['all_rest_company'] = $rest;
    $data['count_rest'] = sizeof($rest);
    $data['all_audits_company'] = $rocc;
    $data['all_dir_company'] = $dir;
    $data['count_all_audits'] = sizeof($rocc);
    return $data;
}

function getRoccCompany($companyid, $type, $datea, $dateb)
{

    global $mysqli;

    if ($type == "all")
    {
        $res = $mysqli->query("
SELECT r.id          rocc_id,
       r.restid,
       r.opendate    rocc_opentime,
       r.closedate   rocc_closetime,
       r.status      rocc_status,
       s.statusname  rocc_statusname,
       s.statuscss  rocc_statuscss,
       r.comment     rocc_comment,
       r.auditor     auditor_id,
       r.istelegram    rocc_telegram,
       u.name        auditor_name,
       rr.restname   rest_name,
       rr.restid     rest_id,
       rr.img        rest_img,
       rr.adress     rest_adress,
       rr.email      rest_email,
       c.companyname company_name,
       c.id          company_id,
       c.adress      company_adress,
       a.id          actionplan_id,
       a.createtime  actionplan_date,
       ss.statusname actionplan_status,
       ss.statuscss  actionplan_css,
       rt.resultname rocc_result
FROM rocc r
         LEFT JOIN users u
                   ON r.auditor = u.id
         LEFT JOIN status s
                   ON r.status = s.statusid
         LEFT JOIN rest rr
                   ON r.restid = rr.restid
         LEFT JOIN company c
                   on c.id = r.company
         LEFT JOIN actionplan a
                   ON a.reportid = r.id
         LEFT JOIN status ss
                   ON a.status = ss.statusid
         LEFT JOIN result rt
                   ON r.result = rt.id
WHERE r.isdeleted = 0
AND r.company = '$companyid'
AND r.opendate BETWEEN '$datea' AND '$dateb'
ORDER BY r.status DESC
  ");
    }
    else
    {
        if ($type == "telegram")
        {
            $res = $mysqli->query("
SELECT r.id          rocc_id,
       r.restid,
       r.opendate    rocc_opentime,
       r.closedate   rocc_closetime,
       r.status      rocc_status,
       s.statusname  rocc_statusname,
       s.statuscss  rocc_statuscss,
       r.comment     rocc_comment,
       r.auditor     auditor_id,
       r.istelegram    rocc_telegram,
       u.name        auditor_name,
       rr.restname   rest_name,
       rr.restid     rest_id,
       rr.img        rest_img,
       rr.adress     rest_adress,
       rr.email      rest_email,
       c.companyname company_name,
       c.id          company_id,
       c.adress      company_adress,
       a.id          actionplan_id,
       a.createtime  actionplan_date,
       ss.statusname actionplan_status,
       ss.statuscss  actionplan_css,
       rt.resultname rocc_result
FROM rocc r
         LEFT JOIN users u
                   ON r.auditor = u.id
         LEFT JOIN status s
                   ON r.status = s.statusid
         LEFT JOIN rest rr
                   ON r.restid = rr.restid
         LEFT JOIN company c
                   on c.id = r.company
         LEFT JOIN actionplan a
                   ON a.reportid = r.id
         LEFT JOIN status ss
                   ON a.status = ss.statusid
         LEFT JOIN result rt
                   ON r.result = rt.id
WHERE r.isdeleted = 0
AND r.company = '$companyid'
AND r.istelegram = '1'
AND r.opendate BETWEEN '$datea' AND '$dateb'
ORDER BY r.status DESC
  ");
        }
        else
        {
            $res = $mysqli->query("
SELECT r.id          rocc_id,
       r.restid,
       r.opendate    rocc_opentime,
       r.closedate   rocc_closetime,
       r.status      rocc_status,
       s.statusname  rocc_statusname,
       s.statuscss  rocc_statuscss,
       r.comment     rocc_comment,
       r.auditor     auditor_id,
       r.istelegram    rocc_telegram,
       u.name        auditor_name,
       rr.restname   rest_name,
       rr.restid     rest_id,
       rr.img        rest_img,
       rr.adress     rest_adress,
       rr.email      rest_email,
       c.companyname company_name,
       c.id          company_id,
       c.adress      company_adress,
       a.id          actionplan_id,
       a.createtime  actionplan_date,
       ss.statusname actionplan_status,
       ss.statuscss  actionplan_css,
       rt.resultname rocc_result
FROM rocc r
         LEFT JOIN users u
                   ON r.auditor = u.id
         LEFT JOIN status s
                   ON r.status = s.statusid
         LEFT JOIN rest rr
                   ON r.restid = rr.restid
         LEFT JOIN company c
                   on c.id = r.company
         LEFT JOIN actionplan a
                   ON a.reportid = r.id
         LEFT JOIN status ss
                   ON a.status = ss.statusid
         LEFT JOIN result rt
                   ON r.result = rt.id
WHERE r.isdeleted = 0
AND r.company = '$companyid'
AND r.status = '$type'
AND r.opendate BETWEEN '$datea' AND '$dateb'
ORDER BY r.status DESC
  ");
        }
    }
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function getSpisok()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM status
WHERE visible = 'create';
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function saveOtkl($id, $mintext, $fulltext)
{

    global $mysqli;
    global $datea;
    $res = $mysqli->query("
UPDATE otkloneniya
SET mintext = '$mintext', 
fultext = '$fulltext',
edittime = '$datea'
WHERE otkloneniya.id = '$id';
");

    $data['status'] = "OK";

    return $data;
}

function saveOtklLevel($id, $cat, $subcat)
{

    global $mysqli;
    global $datea;
    $res = $mysqli->query("
UPDATE otkloneniya
SET category = '$cat',
subcategory = '$subcat',
edittime = '$datea'
WHERE otkloneniya.id = '$id';
");

    $data['status'] = "OK";

    return $data;
}

function getOtkloneniya($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT 
o.id otkl_id,
o.mintext otkl_mintext,
o.fultext otkl_fulltext,
o.category otkl_cat,
l.name otkl_catname,
l.img otkl_img_cat,
o.subcategory otkl_subcat,
ll.name otkl_subcatname,
ll.img otkl_img_subcat,
o.reportid otkl_reportid,
o.img otkl_img
FROM otkloneniya o
LEFT JOIN level l
ON o.category = l.id
LEFT JOIN level ll
ON o.subcategory = ll.id
WHERE o.reportid = $reportid
AND o.isdeleted = 0;
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function getRoccId($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT r.id          rocc_id,
       r.restid,
       r.opendate    rocc_opentime,
       r.closedate   rocc_closetime,
       r.status      rocc_status,
       s.statusname  rocc_statusname,
       s.statuscss  rocc_statuscss,
       r.comment     rocc_comment,
       r.auditor     auditor_id,
       u.name        auditor_name,
       rr.restname   rest_name,
       rr.restid     rest_id,
       rr.adress     rest_adress,
       rr.img        rest_img,
       rr.email      rest_email,
       c.companyname company_name,
       c.id          company_id,
       c.adress      company_adress,
       a.id          actionplan_id,
       a.createtime  actionplan_date,
       ss.statusname actionplan_status,
       ss.statuscss  actionplan_css
FROM rocc r
         LEFT JOIN users u
                   ON r.auditor = u.id
         LEFT JOIN status s
                   ON r.status = s.statusid
         LEFT JOIN rest rr
                   ON r.restid = rr.restid
         LEFT JOIN company c
                   on c.id = r.company
         LEFT JOIN actionplan a
                   ON a.reportid = r.id
         LEFT JOIN status ss
                   ON a.status = ss.statusid
WHERE r.isdeleted = 0
AND r.id = $reportid
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function endQuickAudit($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
UPDATE quickrocc 
SET status = 'final'
WHERE reportid = $reportid;
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return null;
}

function addRestQuickRocc($reportid, $restid)
{

    global $mysqli;

    $res = $mysqli->query("
UPDATE rocc 
SET restid = '$restid'
WHERE id = $reportid;
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    $data['status'] = "OK";
    return $data;
}

function getRoccImg($restid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT 
o.img otkl_img
FROM otkloneniya o
LEFT JOIN rocc r
ON o.reportid = r.id
WHERE r.status = 'final'
AND o.restid = $restid
  ");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function getRocc($reportid)
{

    $info = getRoccId($reportid);
    $otkl = getOtkloneniya($reportid);
    $data['otkl'] = getOtkloneniya($reportid);
    $data['info'] = $info[0];

    return $data;
}

function getSession($userid)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT
s.id ss_id,
s.userid ss_userid,
s.createtime ss_date,
s.browser ss_browser,
s.userip ss_userip,
s.country ss_country,
s.city ss_city,
u.phone ss_phone,
u.name ss_username,
u.vac ss_vacid,
v.vacname ss_vac
FROM session s
LEFT JOIN users u
ON s.userid = u.id
LEFT JOIN vac v
ON u.vac = v.id
WHERE s.userid = '$userid'
AND s.isdeleted = '0'
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users;
}

function getTelegramSession($userid)
{
//ne dorabotan
    global $mysqli;

    $res = $mysqli->query("
    SELECT
s.id ss_id,
s.userid ss_userid,
s.createtime ss_date,
s.browser ss_browser,
s.userip ss_userip,
s.country ss_country,
s.city ss_city,
u.phone ss_phone,
u.name ss_username,
u.vac ss_vacid,
v.vacname ss_vac
FROM session s
LEFT JOIN users u
ON s.userid = u.id
LEFT JOIN vac v
ON u.vac = v.id
WHERE s.userid = '$userid'
AND s.isdeleted = '0'
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users;
}

function deleteOchered($reportid)

{
//ne dorabotan
    global $mysqli;
    global $datea;

    $res = $mysqli->query("
UPDATE ochered 
SET status = 'final',
finaltime = '$datea'
WHERE reportid = '$reportid'
AND status = 'start' 
AND isdeleted = '0'
    ");

    return null;
}

function deleteSession($id)
{

    global $mysqli;

    $res = $mysqli->query("
    UPDATE session SET isdeleted = '1' WHERE id = '$id'
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return null;
}

function proverkaRoccId($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT * 
    FROM otkloneniya 
    WHERE reportid=$reportid
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    for ($i = 0;$i < sizeof($users);$i++)
    {
        $data = proverkaText($users[$i]['id']);
        if ($data['status'] == "NO")
        {
            return $data;
        }
        else
        {
            return $data;
        }
    }

    return null;
}

function proverkaRoccIdd($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT * 
    FROM otkloneniya 
    WHERE reportid=$reportid
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }
    $aza = 0;
    for ($i = 0;$i < sizeof($users);$i++)
    {
        $data = proverkaText($users[$i]['id']);
        if ($data['status'] == "NO")
        {
            $aza = + 1;
        }
        else
        {
        }
    }

    return $aza;
}

function countResult($restid, $result)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT 
COUNT(id) count_rocc
FROM rocc
WHERE result='$result'
AND isdeleted='0'
AND restid='$restid'
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users[0]['count_rocc'];
}

function countProcent($restid)
{

    $a = countResult($restid, "1");
    $m = countResult($restid, "3");
    $u = countResult($restid, "2");
    $obw = $a + $m + $u;
    $sum = $a + $m + $u;

    $data['count_a'] = $a;
    $data['percent_a'] = ($a / $sum) * 100;

    $data['count_m'] = $m;
    $data['percent_m'] = ($m / $sum) * 100;

    $data['count_u'] = $u;
    $data['percent_u'] = ($u / $sum) * 100;

    $data['obw'] = $obw;

    return $data;
}

function generate_pass($login, $number)
{
    $arr = array(
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0'
    );

    // Генерируем пароль для смс
    $pass = "";
    for ($i = 0;$i < $number;$i++)
    {
        // Вычисляем произвольный индекс из массива
        $index = rand(0, count($arr) - 1);
        $pass .= $arr[$index];
    }

    $data = $login . "" . $pass;
    return $data;
}

function proverkaToken($token)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT * 
    FROM telegramtoken 
    WHERE token=$token
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }
    if ($users == null)
    {
        $data['status'] = "NO";
        $data['message'] = "Токен не найден";
    }
    else
    {
        $data['status'] = "OK";
        $data['message'] = "Успешно";
        $data['user'] = $users[0];
    }
    return $data;
}

function getRestUser($userid)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT *
FROM rest
WHERE restmng = '$userid'
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users;
}

function getConfig()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT 
s.url site_url,
s.offline site_offline,
s.tlgrmtoken telegram_token
FROM sys_config s
WHERE s.id = 1
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users;

}

function validateRocc($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT * 
FROM otkloneniya
WHERE LENGTH(mintext)<=2
AND LENGTH(fultext)<=2
AND reportid = '$reportid'
AND isdeleted = 0
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    if($users==null){
        $data['status'] = "OK";
    }else{
        $data['status'] = "NO";
        $data['message'] = "Произошла ошибка, в аудите имеются отклонения с пустыми комментариями";
    }

    return $data;

}

function validateRoccCategory($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM otkloneniya
WHERE category = '9'
AND subcategory = '9'
AND reportid = '$reportid'
AND isdeleted = 0
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    if($users==null){
        $data['status'] = "OK";
    }else{
        $data['status'] = "NO";
        $data['message'] = "Произошла ошибка, в аудите имеются отклонения с невыбранными категориями";
    }

    return $data;

}

function validateRoccSubCategory($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM otkloneniya
WHERE subcategory = '9'
AND reportid = '$reportid'
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    if($users==null){
        $data['status'] = "OK";
    }else{
        $data['status'] = "NO";
        $data['message'] = "Произошла ошибка, в аудите имеются отклонения с невыбранными подкатегориями";
    }

    return $data;

}

function clearOtkl($reportid)
{
    global $datea;
    global $mysqli;

    $res = $mysqli->query("
UPDATE otkloneniya
SET isdeleted = '1',
deletetime = '$datea'
WHERE reportid = '$reportid'
    ");
    
    $data['status'] = "OK";
    return $data;

}

function saveToken($userid, $token)

{
    global $datea;

    $user = R::dispense('telegramtoken');
    $user->userid = $userid;
    $user->createtime = $datea;
    $user->token = $token;
    $user->isdeleted = "0";
    R::store($user);

    return null;

}


function addOchered($reportid)

{
    global $datea;
    $userid = $_COOKIE['userid'];

    $user = R::dispense('ochered');
    $user->userid = $userid;
    $user->createtime = $datea;
    $user->reportid = $reportid;
    $user->status = "start";
    $user->isdeleted = "0";
    R::store($user);

    return null;

}

function addHistory($reportid,$type)

{
    global $datea;
    $userid = $_COOKIE['userid'];

    $user = R::dispense('history');
    $user->userid = $userid;
    $user->createtime = $datea;
    $user->reportid = $reportid;
    $user->type = $type;
    $user->isdeleted = "0";
    R::store($user);

    return null;

}

function generateToken($userid)
{

    global $mysqli;
    global $datea;

    $token = generate_pass($userid, "4");
    $proverka = proverkaToken($token);

    if ($proverka['status'] == "NO")
    {

        $data['status'] = "OK";
        $data['message'] = "Токен успешно создан";
        $data['token'] = $token;
        $savetoken = saveToken($userid, $token);

    }
    else
    {
        $data['status'] = "NO";
        $data['message'] = "Данный токен недействителен";
    }

    return $data;
}

function proverkaText($otklid)
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT * 
    FROM otkloneniya 
    WHERE id=$otklid
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    if (mb_strlen($users[0]['mintext']) < 3)
    {
        $data['status'] = "NO";
        $data['otklid'] = $users[0]['id'];
        $data['mintext'] = $users[0]['mintext'];
        $data['message'] = "Ошибка в кратком содержании отклонения";
    }
    else
    {
        if (mb_strlen($users[0]['fultext']) < 3)
        {
            $data['status'] = "NO";
            $data['otklid'] = $users[0]['id'];
            $data['mintext'] = $users[0]['mintext'];
            $data['message'] = "Ошибка в полном содержании отклонения";
        }
        else
        {
            if ($users[0]['category'] == 9)
            {
                $data['status'] = "NO";
                $data['otklid'] = $users[0]['id'];
                $data['mintext'] = $users[0]['mintext'];
                $data['message'] = "Не выбрана категория отклонения";
            }
            else
            {
                if ($users[0]['subcategory'] == 9)
                {
                    $data['status'] = "NO";
                    $data['otklid'] = $users[0]['id'];
                    $data['mintext'] = $users[0]['mintext'];
                    $data['message'] = "Не выбрана подкатегория отклонения";
                }
                else
                {
                    $data['status'] = "OK";
                }
            }
        }
    }
    return $data;

}

function getInfoBrowser()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    preg_match("/(MSIE|Opera|Firefox|Chrome|Version)(?:\/| )([0-9.]+)/", $agent, $bInfo);
    $browserInfo = array();
    $browserInfo['name'] = ($bInfo[1] == "Version") ? "Safari" : $bInfo[1];
    $browserInfo['version'] = $bInfo[2];
    return $browserInfo;
}

function sendReportTelegram($chatid, $file, $text)
{

    //$token = '1012040761:AAHJnozJada_Z5XAypsAFl-3DYvJNJUb3HE';
    global $token;
    $response = array(
        'chat_id' => $chatid,
        'document' => curl_file_create(__DIR__ . $file),
        'text' => 'Текст'
    );

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendDocument');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);

    $response = array(
        'chat_id' => $chatid,
        'text' => $text
    );

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);

    return null;
}

//upload photo

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
  
  function make_upload($file){  
    // формируем уникальное имя картинки: случайное число и name
    $name = mt_rand(0, 10000) . $file['name'];
    copy($file['tmp_name'], 'uploads/img/' . $name);
    editProfilePhoto($_COOKIE['userid'], $name);
  }
  
  function editProfilePhoto($userid, $img)
{

    global $mysqli;

    $res = $mysqli->query("
UPDATE users
SET img = '$img'
WHERE id = '$userid'
    ");


    return null;
}

function getVac()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT * FROM vac
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function getRestCom($companyid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT *
FROM rest
WHERE company = '$companyid'
");
    foreach ($res as $value)
    {
        $data[] = $value;
    }

    return $data;
}

function getEdit()
{
  $companyid = $_COOKIE['company'];
  $data['vac'] = getVac();
  $data['rest'] = getRestCom($companyid);

    return $data;
}
//

//admin

function getMon()
{

    global $mysqli;

    $res = $mysqli->query("
    SELECT 
		o.id	server_id,
        o.status server_statusid,
        DATE_FORMAT(o.createtime, '%d.%m.%Y %T') server_datea,
        DATE_FORMAT(o.finaltime, '%d.%m.%Y %T') server_finaltime,
        sts.statusname server_status,
        sts.statuscss server_status_css,
		r.id          rocc_id,
       r.restid,
       r.opendate    rocc_opentime,
       r.closedate   rocc_closetime,
       r.status      rocc_status,
       s.statusname  rocc_statusname,
       s.statuscss  rocc_statuscss,
       r.comment     rocc_comment,
       r.auditor     auditor_id,
       r.istelegram    rocc_telegram,
       u.name        auditor_name,
       rr.restname   rest_name,
       rr.restid     rest_id,
       rr.img        rest_img,
       rr.adress     rest_adress,
       rr.email      rest_email,
       c.companyname company_name,
       c.id          company_id,
       c.adress      company_adress,
       a.id          actionplan_id,
       a.createtime  actionplan_date,
       ss.statusname actionplan_status,
       ss.statuscss  actionplan_css,
       rt.resultname rocc_result
FROM ochered o
		LEFT JOIN rocc r
					ON o.reportid = r.id
		LEFT JOIN users u
                   ON r.auditor = u.id
         LEFT JOIN status s
                   ON r.status = s.statusid
         LEFT JOIN rest rr
                   ON r.restid = rr.restid
         LEFT JOIN company c
                   on c.id = r.company
         LEFT JOIN actionplan a
                   ON a.reportid = r.id
         LEFT JOIN status ss
                   ON a.status = ss.statusid
         LEFT JOIN result rt
                   ON r.result = rt.id
         LEFT JOIN status sts
         			ON o.status = sts.statusid
         ORDER BY o.id DESC
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users;
}

function getLastOtkl()
{

    global $mysqli;

    $res = $mysqli->query("
SELECT
o.id otkl_id,
o.mintext otkl_mintext,
o.category otkl_catid,
o.subcategory otkl_subcatid,
l.name otkl_category,
ll.name otkl_subcategory,
ll.css otkl_css
FROM otkloneniya o
LEFT JOIN level l
ON o.category = l.id
LEFT JOIN level ll
ON o.subcategory = ll.id
WHERE isdeleted = 0
ORDER BY o.id DESC LIMIT 5
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users;
}

function getOtklId($otklid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT
o.id otkl_id,
o.mintext otkl_mintext,
o.category otkl_catid,
o.subcategory otkl_subcatid,
o.img otkl_img,
o.fultext otkl_fulltext,
o.createtime otkl_date,
l.name otkl_category,
ll.name otkl_subcategory,
ll.css otkl_css,
rs.restname otkl_restname,
us.name otkl_auditor
FROM otkloneniya o
LEFT JOIN level l
ON o.category = l.id
LEFT JOIN level ll
ON o.subcategory = ll.id
LEFT JOIN rocc rr
ON o.reportid = rr.id
LEFT JOIN rest rs
ON rr.restid = rs.restid
LEFT JOIN users us
ON rr.auditor = us.id
WHERE o.id = '$otklid'
    ");

    foreach ($res as $value)
    {
        $users[] = $value;
    }

    return $users;
}


//admin
?>
