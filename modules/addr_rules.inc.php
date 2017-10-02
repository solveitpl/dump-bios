<?php
/* Pierwszy poziom agregacji linii adresu */
/* $SITE - zmienna która określa który plik ma być załadowany koniec końców*/

switch (strtolower($ARG[0])){
	case "query": // jeśli zapytanie dotyczny jQuery
		include "modules/ajax.php";
		die();
		break;
	case "logout":	/* Wylogowanie */
		$SITE = $login->HTML_SITE['LOGOUT'];
		session_destroy();
		header("Location: ".BDIR);
		break;
		
	case "login":	/* Logowanie */
		/* Próba utworzenia  użytkownika*/
		if (isset($_POST['login'])&&isset($_POST['password']))
		{
			$User = oUser::loginTry($_POST['login'], $_POST['password']);
		}
		
		header("Location: ".BDIR); // Przekierowanie na stronę główną po próbie logowania
		break;
	
	case "menagepanel":	// Panel admina !
		if (isAdmin())
			include 'admin/menage_panel.php';
		die();
		break;
		
		
	case "myaccount":
		break;
		
	case "article":
		//Ładowanie formularza komentarzy
		new Form('comments');
		// Ładowanie głównej zawartości
		$SITE = SITES."article/article.php";
		$_ENV['CSS'] .= GetFileContent(SITES."article/article.css", "ARTICLES");
		$_ENV['JS'] .= GetFileContent(SITES."article/article.js", "ARTICLES");		
		break;
	
	case "downloads":
		//Ładowanie formularza komentarzy
		new Form('comments');
		$SITE = SITES."download/download.php";
		$_ENV['CSS'] .= GetFileContent(SITES."download/download.css", "ARTICLES");
		$_ENV['JS'] .= GetFileContent(SITES."download/download.js", "ARTICLES");
		
		break;
		
	case 'getfile':
		include SITES."download/GetFile.php";
		die();
		break;
		
	case "search":
		$SITE = SITES."search/search.php";
		$_ENV['CSS'] .= GetFileContent(SITES."search/search.css", "SEARCH");
		$_ENV['JS'] .= GetFileContent(SITES."search/search.js", "SEARCH");
		
		break;
			
		
	case "register":
		$SITE = SITES."register/register.php";
		// załadowanie plików stylu i JavaScript
		$_ENV['CSS'] .= GetFileContent(SITES."register/register.css", "REGISTER");
		$_ENV['JS'] .= GetFileContent(SITES."register/register.js", "REGISTER");
		
		break;
	
	case "member":
		$SITE = SITES."member/member.php";
		// załadowanie plików stylu i JavaScript
		$_ENV['CSS'] .= GetFileContent(SITES."member/member.css", "REGISTER");
		$_ENV['JS'] .= GetFileContent(SITES."member/member.js", "REGISTER");

		break;
				
	
	case "upload":
		$SITE = "upload/FileNotFound.php";
		// załadowanie plików stylu i JavaScript
		break;
		
	case "uploadfile":
		if (isset($ARG[1]) && (strtolower($ARG[1])=='userimg'))
			include 'upload/user_avatar.php';
		else
			include "upload/upload.php";
		die();
		break;
		
	case "user":
			include MODULES."get_user_avatar.php";	
			die();
		break;
	
	case "rules":
			$SITE = SITES."rules/rules.php";
		break;
	
	case "restorepassword":
		$SITE = $login->HTML_SITE['PASSWD_RESTORE'];
		break;
		
	case "contact":
		header("Location: ".BDIR."member/Admin/SendMessage");
		break;
	
	case "info":
		$SITE = SITES."ModelInfo/ModelInfo.php";
		$_ENV['CSS'] .= GetFileContent(SITES."ModelInfo/ModelInfo.css", "MODEL_INFO");
		$_ENV['JS'] .= GetFileContent(SITES."ModelInfo/ModelInfo.js", "MODEL_INFO");
		break;
		

	case "browser":
		// Jeśli trzeba znaleźć dany post w stosie
		if (isset($ARG[1]) && $ARG[1]=='GoTo') include SITES."browser/GoToItem.php";
		$SITE = SITES."browser/browser.php";
		$_ENV['CSS'] .= GetFileContent(SITES."browser/browser.css", "BROWSER");
		$_ENV['JS'] .= GetFileContent(SITES."browser/browser.js", "BROWSER");
		break;
	
	case "getfiledemo":
		include SITES."browser/GetFileDemo.php";
		die();
		break;
	
	case "addmodel":
		$SITE = SITES.'left_menu/AddModel.php';
		break;
		
	case "adspanel":
		//Ładowanie formularza komentarzy
		new Form('comments');
		$_ENV['CSS'] .= GetFileContent(SITES."AdPanel/AdPanel.css", "BROWSER");
		$_ENV['JS'] .= GetFileContent(SITES."AdPanel/AdPanel.js", "BROWSER");
		$SITE = SITES."AdPanel/AdPanel.php";
		break;
		
	case "ads_go":
		// kliknięto reklamę.
		include SITES."AdPanel/GoToAd.php";
		die();
		break;
	
	case "donate":
		// jeśli wywołanie to cURL z DotPay
		if (isset($ARG[1]) && $ARG[1]=='handle') {
			include SITES."payments/payments_handle.php";
			die();
		}
		elseif (isset($ARG[1]) && $ARG[1]=='paypal') {
			include SITES."payments/payments_paypal.php";
			die();
		}
		
		
		// moduł płatności
		$_ENV['CSS'] .= GetFileContent(SITES."payments/styles.css", "DONATE");
		$_ENV['JS'] .= GetFileContent(SITES."payments/script.js", "DONATE");
		$SITE =  SITES."payments/payment_panel.php";
		
		break;
		
		
		
	default:
		$SITE =  SITES."homepage/main.php";
		$_ENV['CSS'] .= GetFileContent(SITES."homepage/main.css", "HOMEPAGE");
		$_ENV['JS'] .= GetFileContent(SITES."homepage/main.js", "HOMEPAGE");
		
	
}




?>