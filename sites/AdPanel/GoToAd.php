<?php
include "header.php";

$ID=intval($ARG[1]);
$STAMP = intval($ARG[2]);
$FunnyMarker = ($STAMP-pow($ID, 3))/$ID;


if ($ID==0 || $STAMP==0) _die("Błąd");

if (!CheckMarker($FunnyMarker, FALSE)) 
{
	StrangeEvent("Błąd przy wywołaniu linku reklamy");
	_die('Internal ERROR');
}

$Ad = oAd::withID($ID);
$Ad->IncValue(FALSE,TRUE);

header("Location: ".$Ad->Link);

?>