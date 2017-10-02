<?php

class Form {
	public $HTML_SITE;			// Dostępne pliki HTML do załadowania na zasadzie KLUCZ => PLIK;
	
	private $HTML_FORMS;			// tablica plików HTML ładowanych na samym wstępie;
	
	private $ModuleName;	// nazwa; stanowi jednocześnie ścieżkę do folderu.
	private $CSS;			// zawartość pliku CSS style.css
	private $JS;			// zawartość pliku script.js
	function __construct($FormName) {
		
		if (file_exists('forms/'.$FormName."/include.php"))
			{
			
			$this->ModuleName = $FormName;
			AddToLog("forms.inc.php", "Utworzyłem klasę formy $FormName ");
			
			// ładowanie zawartości CSS. Nie generujemy linków do CSS aby uniemożliwić poznanie struktury folderów
			$this->CSS = GetFileContent('forms/'.$FormName.'/style.css', $FormName);
			$_ENV['CSS']  .= $this->CSS;
			
			// ładowanie zawartości JavaScript. Nie generujemy linków do JS aby uniemożliwić poznanie struktury folderów
			$this->JS = GetFileContent('forms/'.$FormName.'/script.js',$FormName);
			$_ENV['JS']  .= $this->JS;
				
			// $HTML_FORMS zdefiniowany jest dla każdej formy
			unset($HTML_FORMS);
			unset($HTML_SITE);
			
			
			require_once $this->ModuleName."/include.php";
			// Formy zawsze ładowane są na początku strony
			if (isset($HTML_FORMS))
				{
				$this->HTML_FORMS = $HTML_FORMS;	
				while( list($key, $value) = each($this->HTML_FORMS) )
					$this->HTML_FORMS[$key] = "forms/$FormName/".$value;
					$_ENV['HTML'] = array_merge($_ENV['HTML'],$this->HTML_FORMS);
				}
			// Strony są wywoływane ręcznie prze programistę
			if (isset($HTML_SITE))
				{
				$this->HTML_SITE = $HTML_SITE;
				while( list($key, $value) = each($this->HTML_SITE) )
					$this->HTML_SITE[$key] = "forms/$FormName/".$value;
				}
			
			}	
			

		else
			print("Nie utworzyłem klasy dla formy: $FormName");
		
	}
	
	function RegisterForm() {
			print_r($this->HTML);
		
	}
}
?>
