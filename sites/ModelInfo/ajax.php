<?php
	if (!IsMod()) die(json_encode(array("result"=>"ACCESS_DENIED")));
	
	/*
	 * USUWANIE WYBRANYCH REVISIONS
	 */
	if (isset($_POST['DeleteRevItem'])){
		$CheckData = explode('|',Decrypt($_POST['marker']));
		
		if (!CheckMarker($CheckData[1],FALSE)) die(json_encode(array("result"=>"ACCESS_DENIED", "msg"=>"#1" )));
		
		foreach($_POST['EntriesCheck'] as &$item)
		{
			$item = intval($item);
		}
		
		$CatID = intval($_POST['CategoryID']);
		$DelLine = "CategoryID=$CatID AND (id=";
		$DelLine .= implode(' OR id=', $_POST['EntriesCheck']);
		$DelLine .= ")";
		$query_line = "DELETE FROM ModelInfoChanges WHERE $DelLine";
		$sql = DBquery($query_line);
		
		if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR")));
		else die(json_encode(array("result"=>"SUCCESS", "items"=>$_POST['EntriesCheck'])));
		
		
	}
	
	/*
	 * USUWANIE WSZYSTKICH REWIZJI DANYCH KATEGORII
	 */
	if (isset($_POST['DeleteAllItems'])){
		
		$CheckData = explode('|',Decrypt($_POST['marker']));
		if (!CheckMarker($CheckData[1],FALSE)) die(json_encode(array("result"=>"ACCESS_DENIED", "msg"=>"#1" )));
		
		$query_line = "DELETE FROM ModelInfoChanges WHERE CategoryID=".intval($CheckData[0]);
		$sql = DBquery($query_line);
	
		if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR")));
		else die(json_encode(array("result"=>"SUCCESS")));
	
	
	}
	
	/*
	 * Akceptowanie wybranej rewizji
	 */
	if (isset($_POST['AcceptItem'])){
		include 'header.php';
		$CheckData = explode('|',Decrypt($_POST['marker']));
		if (!CheckMarker($CheckData[1],FALSE)) die(json_encode(array("result"=>"ACCESS_DENIED", "msg"=>"#1" )));
		$ID = intval($_POST['AcceptItem']);
		
		if ($ID==0) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"BAD_DATA" )));
		
		$Info = oModelInfo::withRevID($ID);
		$Info->Accept();
		$Info->DeleteRev();
		//print_pretty($Info);
		
		if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR")));
		else die(json_encode(array("result"=>"SUCCESS")));
	
	
	}
	
die(json_encode(array("result"=>"NO-DATA")));
?>