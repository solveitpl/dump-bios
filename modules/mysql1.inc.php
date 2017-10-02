<?php
//Dane do łączenia się z bazą MySQL
$_SYSTEM['mysql_host'] = 'localhost';
$_SYSTEM['mysql_user'] = 'bodzix_BIOS';
$_SYSTEM['mysql_pass'] = 'dumpbios';
$_SYSTEM['mysql_database'] = 'bodzix_BIOS';
$_SYSTEM['MYSQL_ENCODING'] = "utf8";


// sprawdzenie poprawności danych
IF ((empty($_SYSTEM['mysql_host']))||(empty($_SYSTEM['mysql_user']))||(empty($_SYSTEM['mysql_database'])))
	AddToLog("mysql.inc.php", "Bad data in config file");

// Ustanawianie połączenia z bazą danych
global $link;
$link = mysqli_connect($_SYSTEM['mysql_host'], $_SYSTEM['mysql_user'], $_SYSTEM['mysql_pass']) 
	or AddToLog("mysql.inc.php","Cannot connect. Komunikat: " . mysql_error());
// wybieranie bazy danych
mysqli_select_db($link, $_SYSTEM['mysql_database']) or AddToLog("mysql.inc.php","We cant choose database:(");
mysqli_query($link, "SET NAMES '".$_SYSTEM['MYSQL_ENCODING']."'");

/* ### funkcja do wywoływania zapytania. Walidacja danych pozostawiona w gestii programisty przed wywołaniem zapytania */
function DBquery($sql)
{
//	echo $sql."<br>";
	global $link;
	$result = mysqli_query($link, $sql);
	if ($result == false)
	{
		$ERROR_SQL = "INSERT INTO internal_log (`query`, `result`) VALUES('".htmlspecialchars($sql, ENT_QUOTES)."', '".htmlspecialchars(mysqli_error($link), ENT_QUOTES)."')";
		
		mysqli_query($link, $ERROR_SQL);
		AddToLog("MYSQL", htmlspecialchars(mysqli_error($link), ENT_QUOTES));
	}

	return $result;

}

/* ### funkcja do tablicownia wyników SQL */
function DBarray($query) 
{
	$arr = mysqli_fetch_array($query, MYSQLI_ASSOC);
	return $arr;
}

/* ### Funkcja pobiera ostatnio dodany ID z AUTO_INCREMENT */
function DBlastID()
{
	global $link;
	$result=mysqli_insert_id($link);
	return $result;
}


function GetSettings($KEY){
	$sql = dbarray(dbquery("SELECT VALUE FROM Settings WHERE NAME='$KEY'"));
	if (isset($sql['VALUE']))
		return $sql['VALUE'];
	else 
		return "Błąd";
}


?>
