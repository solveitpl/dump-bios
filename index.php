<?php
/*
 index.php
 agreguje wszystkie argumenty z linii adresu i przetwarza.
 Każda połączenie z klientem przechodzi przez ten plik
 */

/* ### Ładowanie modułów ### */
require_once 'matrix.php';
require_once 'modules/includes.php';
require_once 'forms/forms.php';
// rozpoczynanie sesji
session_start();

// Logowanie główne
if (!isset($_SESSION['MAIN_LOGIN'])&&MAINTANACE)
{
	include $login->HTML_SITE['MAINTENANCE'];
	die();
}


require_once 'modules/var_pointers.php';

require_once 'modules/check_things.php';	// Test wskaźników systemu

require_once 'modules/addr_rules.inc.php';	// Obsługa rozpoznawania adresu
//if (IsLogin())
//	$User->SendNotify("Wiadomość dla ciebie","MAIN");

/* ### Wywołania zawartości portalu ### */
require_once 'theme/theme.php';
require_once SITES.'last_added/last_added.php';


RenderHeader(); // Wywołanie nagłówka

RenderTopBar();
StartBodyContainer();
	RenderLeftSideMenu();
	
	StartContent();
		// Ładowanie głównego pliku:
		if ($SITE!='') require_once $SITE;

	EndContent();
	ShowLastAddedForm();
EndBodyContainer();
RenderFooter(); // Wywołanie stopki
AddToLog("SITE", "Zakończono generowanie strony");

require_once $DebugBox->HTML_SITE['DEBUG'];



?>
