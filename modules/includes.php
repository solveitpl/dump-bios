<?php
/*
 ### Includowanie niezbędnych(podstawowych) modułów
 */
require_once 'mysql.inc.php';
require_once 'site.inc.php';		// klasa obiektu strony
require_once 'users.inc.php';		// klasa obiektu użytkowników
require_once 'user_messages.php';	// Komunikaty systemowe
require_once 'errors.inc.php';		// obsługa błędów PHP

/*
 * Funkcje ładujące opcjonalne moduły
 * 
 */

class oModules{
	function LoadMailSender(){ require_once MODULES.'mail/mail_engine.php';}
}

$Modules = new oModules();
?>