<?php


if (!isset($ARG[1])) $ARG[1]='';
if (!isset($ARG[2])) $ARG[2]='';
if (!isset($ARG[3])) $ARG[3]=0;

define("FILE_VERIFICATED_POINTS",GetSettings("FILE_VERIFICATED_POINTS"));

require_once 'header.php'; // deklaracje klasy itp

switch($ARG[1]){
	
	case 'Add':
		include 'AddFile.php';
		break;
	case 'StartDownload':
		include 'downloadFile.php';
		break;
	case 'GetFile':
		include 'GetFile.php';
		break;

	default:
		include 'FileLists.php';

}

