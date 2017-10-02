<?php
require_once "header.php";


switch ($ARG[1])
{
	case 'Add':
		include "AddFile.php";
		break;
		
	case 'Buy':
		require_once "BuyFile.php";
		break;
		
	default:
		include 'FileBrowser.php';
		break;
}

?>