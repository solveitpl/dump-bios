<?php
/* Plik nakierowuje zmienne z bardziej przyjazną nazwą na zmienne SESSION */
define("DOWNLOAD_PER_DAY",GetSettings("FREE_DOWNLOAD_PER_DAY"));

// Tablica wiadomości do pokazania
if (!isset($_SESSION['MSG_LIST']))
	$_SESSION['MSG_LIST'] = array();
$Msg = &$_SESSION['MSG_LIST'];
$User = &$_SESSION['USER'];


/* ### Jeśli jest zalogowany użytkownik ### */
if (IsLogin())
{

	$User->ReloadData();
	// zmienne JS z PHP
	$_ENV['JS'] .= "
			USER.Nick = '".$User->UserNick()."';
			USER.Perm = '".$User->Perm()."';
			";
	
	
	
}
else {
	$User = oUser::loginTry('', '');
}

	



?>