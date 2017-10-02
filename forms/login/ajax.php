<?php
/*
 * Resetowanie hasła
 */
if (isset($_POST['USER_PASSWORD_RESET'])){
	// zabezpieczyć przed spamquery
	$input_value = htmlspecialchars($_POST['USER_PASSWORD_RESET']);
	$marker = intval(Decrypt($_POST['MARKER']));

	if ((time(NULL)-$marker) > 2400) // jeśli znacznik czasu został sztucznie zmodyfikowany
	{
		echo json_encode(array("result"=>"ERROR", "msg"=>"Unfortunately, something goes wrong....<br>  Admin will know about it.<br>Try again later.<br>Sorry"));
		StrangeEvent("Nieprawidłowy znacznik czasu podczas próby resetu hasła dla użytkownika $input_value","PASSWD_RESET");
		die();
	}

	// w zależności czy użytkownik podał Email czy Nick (a może szyfrować w md5 ? wtedy nic nie przejdzie)
	if (filter_var($input_value, FILTER_VALIDATE_EMAIL)) $sql=DBquery("SELECT * FROM Users WHERE Email='$input_value'");
	else 										   $sql=DBquery("SELECT * FROM Users WHERE Nick='$input_value'");

	// jeśli wystąpił błąd przy wywołaniu zapytania:
	if ($sql==FALSE) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"")));

	// jeśli brak wyników
	if ($sql->num_rows==0) die(json_encode(array("result"=>"ERROR", "msg"=>"User doesnt exist!")));

	if ($sql->num_rows > 1) // jeśli wykryto niejednoznaczność (więcej niż jeden
	{
		StrangeEvent("Ambiguity, at password reset $input_value","PASSWD_RESET");
		die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"")));
	}

	$UserToRestore = new oUser(DBarray($sql));
	$TOKEN_LIVE_TIME = GetSettings("TOKEN_LIVE_TIME");
	$Token = md5($User->UserNick().time(NULL));

	// sprawdzamy czy dla tego użytkownika istnieje już aktywny token.
	$sql = DBquery("SELECT * FROM `UserPasswordRestore` WHERE UserID=".$UserToRestore->ID()." AND TokenExpired > ".time(NULL));
	if ($sql->num_rows > 0)
		die(json_encode(array("result"=>"ERROR", "msg"=>"User has active token. Use it or wait for expire.")));


		// dodajemy token do bazy
		$sql = DBquery("INSERT INTO `UserPasswordRestore`(`ID`, `Status`, `Token`, `UserID`, `TokenCreated`, `TokenExpired`)
				VALUES (NULL, 0, '$Token', ".$UserToRestore->ID().", ".time(NULL).", ".(time(NULL)+$TOKEN_LIVE_TIME).")");
		if ($sql==FALSE)
		{
			StrangeEvent("Error with adding token","PASSWD_RESTORE");
			die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Internal error. Admin will know about it")));
		}


		$Modules->LoadMailSender();
		$mail->Subject    = "Reset password"; // zmienna $mail jest tworzona i wstępnie konfigurowana z funkcji LoadMailSender
		include $login->HTML_SITE['EMAIL_TOKEN_LINK']; // pobieranie treści maila.
		$mail->msgHTML($EMAIL_CONTENT);
		$mail->AddAddress($UserToRestore->Email(), $UserToRestore->UserNick());
		$mail->Send();	// wysłanie maila




		die(json_encode(array("result"=>"SUCCESS", "msg"=>"")));
}

/* Sprawdzanie dostępności email w bazie użytkowników */
if (isset($_POST['CHECK_EMAIL_ADDRESS']))
{
	$Email = htmlspecialchars($_POST['CHECK_EMAIL_ADDRESS']);

	if (!(filter_var($Email, FILTER_VALIDATE_EMAIL)))
		$result['DATA']= 'BAD'; // jeśli login nie jest mailem to błąd
		else
		{

			$search = dbarray(DBquery("SELECT COUNT(*) AS DATA FROM Users WHERE Email='$Email'"));
			$result  = $search;
		}
		die(json_encode($result));

}

/* Sprawdzanie dostępności email w bazie użytkowników */
elseif (isset($_POST['CHECK_NICK']))
{
	$Nick = htmlspecialchars($_POST['CHECK_NICK']);
	$search = dbarray(DBquery("SELECT COUNT(*) AS DATA FROM Users WHERE Nick='$Nick'"));
	$result  = $search;

	die(json_encode($result));

}

// jeśli nie odnaleziono żadnego zapytania zwracamy NO-DATA
die(json_encode(array("result"=>"NO-DATA")));
?>