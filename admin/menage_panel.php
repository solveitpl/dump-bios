<?php
/*
 menage_panel.php
 Plik stanowi odniesienie dla wszystkich plików do zarządzania w katalogu admin. Stałym fragmentem jest panel zakładek.
 Reszta podyktowana jest plikiem addr_rules.php
 */

/* ### Wywołania zawartości portalu ### */
require_once 'theme/theme.php';
$_ENV['CSS'] .= GetFileContent("admin/GUI/styles.css", "ADMIN");
$_ENV['JS'] .= GetFileContent("admin/GUI/script.js", "ADMIN");		
?> <script src="<?= BDIR ?>lib/ckeditor/ckeditor.js"></script> <?php 

// Zmienna do obsługi przeglądarki danych

require_once 'forms/forms.php';


RenderHeader(); // Wywołanie nagłówka

RenderTopBar();
StartBodyContainer();
	StartContent();
	// ładujemy panel zakładek
	include "GUI/tabs.php";
?>


<?php
	EndContent();
	

	EndBodyContainer();
	AddToLog("SITE", "Zakończono generowanie strony");
	
	

//require_once $DebugBox->HTML_SITE['DEBUG'];



?>