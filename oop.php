<?php
session_start();

//* Подключаемые библиотеки
require_once 'db_connect.php';
include "libs/qr/qrlib.php";
require_once 'libs/Api2Pdf.php';
require_once 'libs/ApiResult.php';
require 'libs/mail/phpmailer/PHPMailerAutoload.php';
require 'libs/pdfcrowd.php';
include ("SxGeo.php");
$SxGeo = new SxGeo('SxGeo.dat');
use Api2Pdf\Api2Pdf;
//* Подключаемые библиотеки
$datea = date("Y-m-d H:i:s");

$session = proverkaSession($_COOKIE['PHPSESSID']);

if ($session['status'] == "NO")
{
    session_start();
    setcookie('auth', '', time() + (-86400 * 5) , '/', 'rocc.kz');
    setcookie('userid', '', time() + (-86400 * 5) , '/', 'rocc.kz');
    setcookie('company', '', time() + (-86400 * 5) , '/', 'rocc.kz');
}

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
    setcookie('auth', '1', time() + (-86400 * 5) , '/', 'rocc.kz');
    setcookie('userid', $userid['id'], time() + (-86400 * 5) , '/', 'rocc.kz');
    setcookie('company', $user['companyid'], time() + (-86400 * 5) , '/', 'rocc.kz');

    setcookie('auth', '1', time() + (86400 * 5) , '/', 'rocc.kz');
    setcookie('userid', $userid['id'], time() + (86400 * 5) , '/', 'rocc.kz');
    setcookie('company', $user['companyid'], time() + (86400 * 5) , '/', 'rocc.kz');

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

    $token = '1798024308:AAEdRfCQ0w4_XmfiKQxMhaN4yUOHGEuMy1w';
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

    $token = '1798024308:AAEdRfCQ0w4_XmfiKQxMhaN4yUOHGEuMy1w';
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
    setcookie('r_reportid', $reportid, time() + (-86400 * 5) , '/', 'rocc.kz');
    setcookie('r_reportid', $reportid, time() + (86400 * 5) , '/', 'rocc.kz');

    return null;
}

function generateQr($reportid)
{

    $filename = "img/qr/$reportid.png";

    QRcode::png("http://dev.rocc.kz/view.php?reportid=$reportid", $filename, "H", 4, 4);

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
            $api->convertUrlToFile("http://dev.rocc.kz/generatepdf.php?reportid=$reportid", "reports/".$reportid."/report.pdf");
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
            $apiClient = new Api2Pdf('b48ef72f-cd91-4a99-9079-a7c9f6cb1ea2');
            $filename = "report_" . $reportid . ".pdf";
            $apiClient->setInline(true);
            $apiClient->setFilename($filename);
            $apiClient->setOptions(['orientation' => 'portrait', 'pageSize' => 'A4', 'marginBottom' => '0', 'marginLeft' => '0', 'marginTop' => '0', 'marginRight' => '0', 'title' => $filename

            ]);
            $result = $apiClient->wkHtmlToPdfFromUrl('http://dev.rocc.kz/generatepdf.php?reportid=' . $reportid);

            $data['status'] = "OK";
            $data['url'] = $result->getPdf();
        }
    }

    return $data;

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

    $token = '1616234885:AAH7j40xp-WZfFJXHtx9DmIzUSH76xEZZN8';
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

function sendEmail($reportid, $email)
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
    $mail->From = $email; // адрес почты, с которой идет отправка
    $mail->FromName = $email; // имя отправителя
    $mail->addAddress($email, 'Имя');
    //$mail->addAddress($tu, 'Имя 2');
    $mail->addCC($email);

    $mail->isHTML(true);
    $file = generatePdf("1", $reportid);
    $filename = $file['url'];
    $mail->Subject = 'Тема письма';
    $mail->Body = "Текст письма";
    $mail->AltBody = 'Azat Kadyr';
    $mail->addAttachment($filename);
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

            setcookie('r_reportid', $user['id'], time() + (-86400 * 5) , '/', 'rocc.kz');
            setcookie('r_reportid', $user['id'], time() + (86400 * 5) , '/', 'rocc.kz');
            
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

function startReportB($type, $restid, $userid, $company)
{
    global $datea;

    if (isset($restid))
    {
        if (isset($type))
        {

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

            setcookie('r_reportid', $user['id'], time() + (-86400 * 5) , '/', 'rocc.kz');
            setcookie('r_reportid', $user['id'], time() + (86400 * 5) , '/', 'rocc.kz');

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
                        $mail = sendEmail($reportid, $pr['info']['rest_email']);
                        editedRocc($reportid, $value, $result);
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

function validateRocc($reportid)
{

    global $mysqli;

    $res = $mysqli->query("
SELECT * 
FROM otkloneniya
WHERE LENGTH(mintext)<=2
AND LENGTH(fultext)<=2
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

//
?>
