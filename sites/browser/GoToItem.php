<?php

/**
 * Plik ten odnajduje dany post i przekierowywuje bezpośrednio na jego link
 */

require_once 'header.php';
require_once SITES.'left_menu/functions.php';
$PostID = intval($ARG[2]);
//$Post = oPost::withID($PostID);
if ($PostID<1) _die('BAD-DATA');
$Post = DBarray(DBquery("SELECT Category, MODULE FROM BrowserPosts WHERE ID=$PostID"));
if (empty($Post)) _die("This posts do not exists...");
$Path = FindCatLinkByID($Post['Category']);

//echo $Post['MODULE'].'/'.$Path.'item/'.$PostID;
header("Location: ".BDIR."Browser/".$Post['MODULE'].'/'.$Path.'item/'.$PostID);
die();
	
?>