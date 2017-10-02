<?php


switch (strtolower($ARG[1])){
	case 'settings':
		include "settings.php";
		break;
	default:
		include 'user_profile.php';
}



?>
