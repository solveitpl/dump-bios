<?php
// Sprawdzanie kontrolne wskaźniki systemu


/*
 * #####################################################
 * ############### DLA ZALOGOWANYCH ####################
 * #####################################################
 */
if (IsLogin())
{


/*
 * Test czy są pobrane pliki których użytkownik nie ocenił
 */

	
	$sql = DBquery("SELECT UploadedFile.ID, UploadedFile.MODULE, UploadedFile.FileDesc, UploadedFile.Category, tFP.Points, t_total.total FROM Points 
					LEFT JOIN UploadedFile ON UploadedFile.ID = Points.ElementID
					LEFT JOIN (SELECT * FROM FilesPoints WHERE UserID=".$User->ID().") AS tFP ON Points.ElementID = tFP.FileID
					LEFT JOIN (SELECT FileID, SUM(Points) AS total FROM FilesPoints GROUP BY FileID) as t_total ON t_total.FileID=Points.ElementID 
					WHERE Points.UserID = ".$User->ID()." AND UploadedFile.UploaderID<>".$User->ID()." AND Points.ElementID <> 0 AND UploadedFile.ID > 0 AND tFP.UserID IS NULL");

	while ($row = DBarray($sql))
	if (!$row['Points'])
	{
		
		if (!defined("DOWNLOAD_DISABLED"))
			define("DOWNLOAD_DISABLED", 1);
		AddSysInfo(MakeItShort($row['FileDesc'],80), 
				"Please rate downloaded file before you download another file",
				"downloads/item/".$row['ID']);
	}

	
	//define("DOWNLOAD_DISABLED",null);
	// konfiguracja maksymalnej długości group_conca_max_len
	dbquery("SET SESSION group_concat_max_len = 10000");

	$marker = sha1(session_id().$_SERVER['REMOTE_ADDR']);
		
	// jeśli marker się nie zgadza to niszczymy sesję
	if ($User->Marker != $marker){
		StrangeEvent("Marker error", "CHECK_THINGS", array("UserData"=>$User, "CalcMarker"=>$marker, "REMOTE_ADDR"=>$_SERVER['REMOTE_ADDR'], "SESSION_ID"=>session_id()));
		session_destroy();
		AddToMsgList("Session expired...<br>Logout perform.", "INFO");
	}
	
	// jeśli czas ostaniej wizyty przekroczył dopuszczalny okres
	if ((time(NULL)-strtotime($User->LastVisit)) > $_SETTINGS['SESSION_TIMEOUT']){
		session_destroy();
		AddToMsgList("You session has timeout... Relogin please.");
	}

		
//	DBquery("SELECT * FROM ");		
		
// aktualizacja czasu ostatniej wizyty:
	DBquery("UPDATE Users SET LastLoginTime=CURRENT_TIMESTAMP WHERE ID=".$User->ID);
	
// ładujemy moduły zarezwerwowane tylko dla admina
	if (IsAdmin()){
		$pickCategory = new Form('pickCategory');
		$DataViewer = new Form('DataViewer');
	}
	
}
	
	?>
