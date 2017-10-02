<?php
// ładowanie bibliotek klasy
require_once 'PHPMailerAutoload.php';

/*
 * DANE DOSTĘPOWE MAILA
 */
$MAIL_HOST = "mail.bodzix.pl";
$MAIL_PORT = 587;
$MAIL_USER = "test@bodzix.pl";
$MAIL_PASSWORD = "123456";
$MAIL_FROM_TITLE = "DUMP BIOS";

$MAIL_FROM = 'bj@bodzix.pl';
$MAIL_FROM_USER = "DUMP BIOS";
/*
 * Inicjalizacja
 */
global $mail;
$mail = new PHPMailer(true);

$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
$mail->IsHTML(true);
$mail->Debugoutput = 'html';
$mail->Host = $MAIL_HOST;
$mail->Port = 587;
$mail->CharSet = 'UTF-8';
$mail->SMTPAuth = true;
$mail->Username = $MAIL_USER;
$mail->Password = $MAIL_PASSWORD;
$mail->SMTPSecure = 'tls';
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->setFrom($MAIL_FROM, $MAIL_FROM_USER);
$mail->AltBody = 'To wiadomość w formacie HTML';



?>