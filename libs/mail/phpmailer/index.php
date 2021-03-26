<?php 

require 'phpmailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->isSMTP();

$mail->Host = 'smtp.mail.ru';
$mail->SMTPAuth = true;
$mail->Username = '9677226@mail.ru'; // логин от вашей почты
$mail->Password = 'Welcome1!'; // пароль от почтового ящика
$mail->SMTPSecure = 'ssl';
$mail->Port = '465';

$mail->CharSet = 'UTF-8';
$mail->From = '9677226@mail.ru'; // адрес почты, с которой идет отправка
$mail->FromName = '9677226@mail.ru'; // имя отправителя
$mail->addAddress('9677226@mail.ru', 'Имя');
$mail->addAddress('9677226@mail.ru', 'Имя 2');
$mail->addCC('9677226@mail.ru');

$mail->isHTML(true);
$aza2 = trim(strip_tags($_POST["name"]));

$mail->Subject = 'Уведомление о регистрации нарушения';
$mail->Body = "Уважаемый(-ая) $aza2\n";
$mail->AltBody = 'Привет, мир! Это альтернативное письмо';
$mail->addAttachment();
// $mail->SMTPDebug = 1;

if( $mail->send() ){
    echo 'Письмо отправлено';
}else{
    echo 'Письмо не может быть отправлено. ';
    echo 'Ошибка: ' . $mail->ErrorInfo;
}