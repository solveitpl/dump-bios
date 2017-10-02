<?php

require_once 'functions.php';

/* Pobranie elementów MENU
 *
 */
if (isset($_POST['GET_MENU'])){
	$ID = intval($_POST['GET_MENU']);
	if (!isset($_POST['step'])) $step = 0;
	else $step = $_POST['step'];
	$menu = array();
	
	$sql =  DBquery("SELECT id, Name, subQuan FROM Categories
			LEFT JOIN (SELECT COUNT(*) AS subQuan, ParentID FROM Categories GROUP BY ParentID) AS t1 
			ON Categories.ID=t1.ParentID 
			WHERE Categories.ParentID = '$ID' ORDER BY Name DESC");
	if (($sql->num_rows==0) && ($step>5))
		$_POST['GET_MENUENTRY_DETAILS'] = $ID;
	else 
	{
		if ($sql==false)
			AddToMsgList("Internal error. It will be report to administrator.", "BAD");
		
		while ($row=dbarray($sql))
			array_push($menu, $row);
	
		die(json_encode($menu));
	}
}

/* Sprawdzanie dostępności nazwy w bazie danych
 *
 */
if (isset($_POST['CHECK_CATEGORY'])){
	// Sprawdzamy czy istnieje rodzic o takiej nazwie lub wierzymy "na słowo", że powinien mieć takkie ID
	if (intval($_POST['PREV_NAME'])==0) { // jeśli wartość nie jest typem INT
		$PREV = htmlspecialchars($_POST['PREV_NAME']);
		$sql = DBarray(DBquery("SELECT Id, Name FROM Categories WHERE Name LIKE '%$PREV%'"));
		
		if ($sql==FALSE)	// żeby nie było, że ojciec się jeszcze nie narodził a syn już po świecie chodził
			die(json_encode(array('result'=>'NO_MATCH')));
		else
			$ParentID = $sql['Id'];
	}
	else {	// w przypadku gdy zmienna ma jakąś wartośc INT
		$ParentID = intval($_POST['PREV_NAME']);
	}
	
	$NAME = htmlspecialchars($_POST['CHECK_CATEGORY']);	// konwersja nazwy na format bezpieczny
	
	// szukamy w bazie
	$matches=array();
	$sql =  DBquery("SELECT Name FROM Categories WHERE Name LIKE '%$NAME%' AND ParentID = $ParentID");
	while ($row=DBarray($sql))
		array_push($matches, $row['Name']);
	$matches_str = implode(',', $matches);
	
	die(json_encode(array('result'=>'FOUND', 'matches'=>$matches_str)));
}



/*
 * Pobranie elementów konkretnego modelu wraz z ilością wyników
 */

elseif (isset($_POST['GET_MENUENTRY_DETAILS'])){
	$ID = intval($_POST['GET_MENUENTRY_DETAILS']);

	if (!$ID) die(json_encode(array('result'=>'ERROR')));

	if (isset($_POST['filter'])) $filter = $_POST['filter']; else $filter = ''; 
	
	$menus = GetMenuDetails($ID,$filter);
		
	die(json_encode($menus));
}

// jeśli nie odnaleziono żadnego zapytania zwracamy NO-DATA
die(json_encode(array("result"=>"NO-DATA")));
?>