<?php
//
require_once 'oop.php';
$token = '1012040761:AAHJnozJada_Z5XAypsAFl-3DYvJNJUb3HE';
$data = file_get_contents('php://input');
$data = json_decode($data, true);
//file_put_contents(__DIR__ . '/message.txt', print_r($data, true));
$chatiduser = $data['message']['from']['id'];
$user = $data['message']['from']['id'];
$text = $data['message']['text'];
$text = mb_strtolower($text);
$proverka = getUserByTid($user);
$uslovie = check_for_number($text);
$proverkarocc = findQuickRocc($proverka['user']['id']);
$reportid = $proverkarocc['user']['reportid'];
//$reportid = 888;
//$proverkarocc['status'] = "NO";
if (!empty($data['message']['text']))
{
    // addMessage($user,$user,$text);
    if ($proverka['status'] == "NO")
    {

        if ($uslovie == "true")
        {
            $pr = proverkaToken($text);
            if ($pr['status'] == "NO")
            {
                $chatid = $data['message']['chat']['id'];
                $message = "Номер не найден!";
            }
            else
            {
                $chatid = $data['message']['chat']['id'];
                $message = $proverka['user']['name'] . ", вы успешно интегрировали телеграмм";
                $data = saveTelegram($pr['user']['userid'], $user, $chatiduser);
            }
        }
        else
        {
            $chatid = $data['message']['chat']['id'];
            $message = "Ваш аккаунт не привязан, пожалуйста введите токен. Токен можете получить на сайте, следуйте инструкции по фото.";
            sendPa($chatid, "1");
        }
    }
    else
    {

        if ($text == "да")
        {
            $chatid = $data['message']['chat']['id'];

            $start = startReportB("", "", $proverka['user']['id'], $proverka['user']['companyid']);
            $message = "Аудит создан! №" . $start['reportid'] . ", загрузите фотографии";
        }
        else
        {
            if ($proverkarocc['status'] == "NO")
            {
                $chatid = $data['message']['chat']['id'];
                $message = "Создать аудит? (Формат ответ: Да)";
            }
            else
            {
                if ($text == "stop")
                {
                    $stop = endQuickAudit($reportid);
                    $chatid = $data['message']['chat']['id'];
                    $message = "Быстрый аудит завершен. Аудит можете найти в списке на сайте  http://dev.rocc.kz/index.php?menu=editrocc&id=" . $reportid;
                }
                else
                {
                    if ($text == "/logout")
                    {

                        $chatid = $data['message']['chat']['id'];
                        $message = "Выход";
                    }
                    else
                    {
                        $chatid = $data['message']['chat']['id'];
                        $message = "Текущий аудит #" . $reportid . ", загрузите фото. Для завершения аудита отправьте STOP";
                    }

                }

            }

        }
    }
}

if (!empty($data['message']['photo']))
{
    if ($proverkarocc['status'] == "NO")
    {
        $chatid = $data['message']['chat']['id'];
        $message = "Нет активных аудитов, для создания напишите Создать аудит";
    }
    else
    {
        if ($reportid == null)
        {
            $chatid = $data['message']['chat']['id'];
            $message = "Аудит не создан";
        }
        else
        {
            $photo = array_pop($data['message']['photo']);

            $ch = curl_init('https://api.telegram.org/bot' . $token . '/getFile');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'file_id' => $photo['file_id']
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $res = curl_exec($ch);
            curl_close($ch);

            $res = json_decode($res, true);
            if ($res['ok'])
            {
                $src = 'https://api.telegram.org/file/bot' . $token . '/' . $res['result']['file_path'];
                $dest = __DIR__ . '/uploads/tbot/' . $user . '-' . time() . '-' . basename($src);
                $bb = '/tbot/' . $user . '-' . time() . '-' . basename($src);
                addOtkl("", "", "9", "9", "", $reportid, $bb, $bb);
                copy($src, $dest);
            }
        }
    }
}

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

function check_for_number($str)
{
    $lenght = strlen($str);
    for ($i = 0;$i < $lenght;)
    {
        if (is_numeric($str[$i++]))
        {
            return true;
        }
    }
    return false;
}
/*
$uslovie = check_for_number($text);
      if($uslovie=="true"){
          $pr = getUserByIda($text);
              if($pr==null){
                  $chatid = $data['message']['chat']['id'];
                  $message = "Номер не найден!";
              }else{
                  $chatid = $data['message']['chat']['id'];
                  $message = "Успешно зареганы";
                  $data = saveTelegram($pr['id'],$user['id']);
              }
      }else{
      $chatid = $data['message']['chat']['id'];
      $message = "Ваш id не зареган в базе, введите ваш номер для авторизации";
      }
*/

function sendPa($chatid, $photo)
{

    $token = '1012040761:AAHJnozJada_Z5XAypsAFl-3DYvJNJUb3HE';
    $response = array(
        'chat_id' => $chatid,
        'photo' => curl_file_create(__DIR__ . '/uploads/bot/1.jpg')
    );

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendPhoto');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);
    sendPb($chatid, "3");
    return null;
}
function sendPb($chatid, $photo)
{

    $token = '1012040761:AAHJnozJada_Z5XAypsAFl-3DYvJNJUb3HE';
    $response = array(
        'chat_id' => $chatid,
        'photo' => curl_file_create(__DIR__ . '/uploads/bot/2.jpg')
    );

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendPhoto');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);
    sendPc($chatid, "3");

    return null;
}
function sendPc($chatid, $photo)
{

    $token = '1012040761:AAHJnozJada_Z5XAypsAFl-3DYvJNJUb3HE';
    $response = array(
        'chat_id' => $chatid,
        'photo' => curl_file_create(__DIR__ . '/uploads/bot/3.jpg')
    );

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendPhoto');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);

    return null;
}
?>