<?php
/* Moduł do obsługi zapytań AJAX 
 * WSZYSTKO MA KOMUNIKOWAĆ SIĘ PRZEZ OBIEKTY JSON
 * 
 * 
 * */




######### includowanie plików obsługi AJAXA z modułów
if (!isset($ARG[1])) $ARG[1]='';
switch (strtolower($ARG[1])){
	case 'browser': 	require_once SITES.'browser/ajax.php'; break;
	case 'member':  	require_once SITES.'member/ajax.php'; break;
	case 'download':  	require_once SITES.'download/ajax.php'; break;
	case 'menu':  		require_once SITES.'left_menu/ajax.php'; break;
	case 'article':		require_once SITES.'article/ajax.php'; break;
	case 'infomodel':	require_once SITES.'ModelInfo/ajax.php'; break;
	case 'lastadded':	require_once SITES.'last_added/ajax.php'; break;
	case 'notifier':	require_once FORMS.'Notifier/ajax.php'; break;
	case 'login':		require_once FORMS.'login/ajax.php'; break;
	case 'admin':		require_once 'admin/ajax.php'; break;
	
	
	default:
	######## Pozostałe
	
	/* Pobranie listy komunikatów systemowych do wyświetlenia
	 *
	 * */
	if (isset($_POST['GET_MSG'])){
		die(json_encode($Msg));
	}
	
	/* Kasowanie komunikatu z listy
	 *
	 * */
	elseif (isset($_POST['ACK_MSG'])){
		if ($_POST['ACK_MSG']=='ALL'){
			$Msg = array();
		}
		
		foreach ($Msg as $Key => &$El){
			if ($El->Time==$_POST['ACK_MSG']) {
				//echo "znalazłe ".$El->Time;
				unset($Msg[$Key]);		
				//print_pretty($Msg);
			}
		}
		
		die();
	}
	
	// jeśli nie odnaleziono żadnego zapytania zwracamy NO-DATA
	die(json_encode(array("result"=>"NO-DATA")));
	

}

?>
