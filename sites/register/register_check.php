<?php
// Analiza danych wejściowych

if (!isset($_POST['SubmitForm']))
	_die("Nothing to do...", "REGISTER");

	// Walidacja adresu email
$Email = htmlspecialchars($_POST['email']);

$IS_EMAIL_EXIST = dbarray(DBquery("SELECT COUNT(*) AS DATA FROM Users WHERE Email='$Email'"));

if (!(filter_var($Email, FILTER_VALIDATE_EMAIL)) || $IS_EMAIL_EXIST['DATA'])
	_die ("Email exists in our database or it has incorrect format", "REGISTER");
	
// Walidacja haseł
$password = $_POST['password'];
$password_retype = $_POST['password_retype'];
if (($password!=$password_retype)||(strlen($password)<6))
	_die("Password is too short...", "REGISTER");
$password = md5($password);

// Walidacja nicku
$Nick = htmlspecialchars($_POST['Nick']);
$IS_NICK_EXIST = dbarray(DBquery("SELECT COUNT(*) AS DATA FROM Users WHERE Nick='$Nick'"));
if ( (strlen($Nick)<2) || $IS_NICK_EXIST['DATA'])
	_die("Nick is to short or it already exist", "REGISTER");



$City = htmlspecialchars($_POST['City']);
$Country = htmlspecialchars($_POST['Country']);

$Birthday = $_POST['Bday_year']."-".sprintf('%02d', $_POST['Bday_month'])."-".sprintf('%02d', $_POST['Bday_day']);
if (!checkdate($_POST['Bday_month'], $_POST['Bday_day'], $_POST['Bday_year']))
	_die("Format daty niepoprawny...");

$Newsletter = $_POST['NewsLetter']=="on" ? 1 : 0;
// Jeśli przeszło wszystkie test, dodajemy do bazy
$UserADD = DBquery("INSERT INTO Users(`ID`, `Nick`, `Password`, `Email`, `Status`, `PermLevel`, `PermArray`, `Birthday`, `City`, `Country`, `WantsNewsletter`, `LastIP`, `Marker`, `LastLoginTime`, `RegisterTime`)
		VALUES(NULL, '$Nick', '$password', '$Email', 0, ".USER.",'', '$Birthday', '$City', '$Country', $Newsletter, '".$_SERVER['REMOTE_ADDR']."', '',  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");

$Modules->LoadMailSender();
$mail->Subject    = "Dump Bios registration"; // zmienna $mail jest tworzona i wstępnie konfigurowana z funkcji LoadMailSender
$Token = md5($Email);
include 'mails/activate.php'; // pobieranie treści maila.
$mail->msgHTML($EMAIL_CONTENT);
$mail->AddAddress($Email, $Nick);
$mail->Send();	// wysłanie maila


// Jeśli udało się zapisać w bazie
if ($UserADD==TRUE)
	require_once 'register_succes.php';
else
	require_once 'register_failed.php';

?>
