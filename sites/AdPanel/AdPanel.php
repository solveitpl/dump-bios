<?php
if (!IsLogin()) 
	require 'InfoSite.php';
else
	{
		require_once 'header.php';
		switch ($ARG[1]){
			case "Edit":
				include 'MenageAd.php';
				break;
			default:
				include 'ads.php';
				break;
		}
	}
?>