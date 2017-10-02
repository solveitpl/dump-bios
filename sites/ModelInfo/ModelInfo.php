<?php
include "header.php";

switch(strtolower($ARG[1])){
	case 'edit':
		include 'EditInfo.php';
		break;
	default:
		include 'ShowInfo.php';
}
?>