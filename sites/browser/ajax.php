<?php
include SITES."browser/header.php";

/*
 * Zmiana wytycznych do sortowania
 */
if (isset($_POST['CHANGE_SORT'])){
	switch($_POST['CHANGE_SORT']){
		case 'VERIFIED':
			$_SESSION['BROWSER_SORT'] = 'VERIFIED';
			$_SESSION['BROWSER_ORDER'] = '(`BrowserPosts`.`status`='.POST_VERIFIED.') DESC, `BrowserPosts`.`status`, SendTime DESC';
			break;
			
		case 'ACCEPTED':
			$_SESSION['BROWSER_SORT'] = 'ACCEPTED';
			$_SESSION['BROWSER_ORDER'] = '(`BrowserPosts`.`status`='.POST_ACCEPTED.') DESC, `BrowserPosts`.`status`, SendTime DESC';
			break;
        case 'REJECTED':
            $_SESSION['BROWSER_SORT'] = 'REJECTED';
            $_SESSION['BROWSER_ORDER'] = '(`BrowserPosts`.`status`='.POST_REJECTED.') DESC, `BrowserPosts`.`status`, SendTime DESC';
            break;
        case 'NEED_TO_ACCEPT':
            $_SESSION['BROWSER_SORT'] = 'NEED_TO_ACCEPT';
            $_SESSION['BROWSER_ORDER'] = '(`BrowserPosts`.`status`='.POST_NEW.') DESC, `BrowserPosts`.`status`, SendTime DESC';
            break;

			
		case 'LAST_ADDED':
			$_SESSION['BROWSER_SORT'] = 'LAST_ADDED';
			$_SESSION['BROWSER_ORDER'] = 'SendTime DESC';
			break;
			
		default:
			die(json_encode(array('result'=>'BAD-DATA', 'msg'=>'No avaliable')));
				
	}
	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Changed')));
}


/*
 * Zapisanie tytułu pliku z modułu BROWSER_FILE
 */
if (isset($_POST['SAVE_BROWSER_FILE_INFO'])){
	$FileID = intval(Decrypt($_POST['SAVE_BROWSER_FILE_INFO']));
	$marker = intval(Decrypt($_POST['marker']));
	$FileTitle = htmlspecialchars(strip_tags($_POST['FileTitle']));
	if (!IsLogin()) die(json_encode(array('result'=>'ACCESS_DENIED', 'msg'=>"Not loged in")));

	if ((time(NULL)-$marker) > 3200) die (json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>"TOO_LONG_INACTIVE")));

	$sql = DBquery("UPDATE BrowserFiles SET FileDesc ='$FileTitle' WHERE ID=$FileID AND UploaderID=".$User->ID());


	//$sql = DBquery("UPDATE MessagesSys SET Checked=1 WHERE ID=$MsgID AND UserID=".$User->ID());
	die(json_encode(array('result'=>'SUCCESS', 'msg'=>$FileID)));
}

/*
 * Usuwanie pliku tymczasowego z listy
 */
if (isset($_POST['DEL_TEMP_FILE'])){
	
	if (!IsLogin())	die(json_encode(array('result'=>'ACCESS_DENIED', 'msg'=>'Access denied')));
	$filename = $_POST['DEL_TEMP_FILE'];

	
	if (array_key_exists($filename,$_SESSION['upload_files']))
	{
		unlink($_SESSION['upload_files'][$filename]['ServerFilePath'].'/'.$filename);
		unset($_SESSION['upload_files'][$filename]);
		die(json_encode(array('result'=>'SUCCESS', 'msg'=>'')));
		
	}
	else 
		die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Internal error')));
		
}


/*
 * Ocena pliku z modułu BROWSER FILE
 */
elseif (isset($_POST['BROWSER_POST_VOTE'])){
	if (!IsLogin())
		die(json_encode(array("result"=>"ACCESS_DENIED", "action"=>"")));

		$ToTrashPoints = GetSettings("POST_TO_TRASH_POINTS");
		$VerificatedPoints=  GetSettings("POST_VERIFICATED_POINTS");

		$Post = intval($_POST['BROWSER_POST_VOTE']);
		$PostInfo = oPost::withID($Post);
		
		if ($PostInfo->Owner->CheckID($User->ID)&&(!IsAdmin()))
			die(json_encode(array("result"=>"OWNER_DENIED", "msg"=>"Owner can't vote for own file")));
		
		if ($PostInfo->Status!=POST_ACCEPTED)
			die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"No pointer")));


			if (!$PostInfo->InUserStock()) // jeśli nie ma tego pliku w zasobach użytkownika
				die(json_encode(array("result"=>"NOT_IN_STOCK")));

				$Point = intval($_POST['POST_POINT']);
				if (!IsAdmin()) $Point = $Point/abs($Point);
				$result = array("result"=>"UNKNOWN_ERROR");

				$sql = DBquery("SELECT BrowserPoints.*, Users.Nick AS UploaderNick, Categories.Name AS CategoryName FROM BrowserPoints
					INNER JOIN BrowserPosts ON BrowserPosts.ID = BrowserPoints.PostID
					INNER JOIN Categories ON Categories.Id = BrowserPosts.Category
					INNER JOIN Users ON BrowserPosts.UserID = Users.ID
					WHERE BrowserPoints.PostID=$Post AND BrowserPoints.UserID=".$User->ID());

				if ($sql==false)
					die(json_encode(array("result"=>"INTERNAL_ERROR")));

				$vote = dbarray($sql);
				// jeśli możemy głosować
				if (empty($vote))
				{
					$sql = DBquery("INSERT INTO BrowserPoints(`ID`, `PostID`, `UserID`, `Points`, `EntryDate`) VALUES(NULL, $Post, ".$User->ID().", $Point, NOW())");
					if ($sql==false)
					{
						$result['result'] = 'INTERNAL_ERROR';
						die(json_encode($result));
					}
					else{
						$result['result'] = 'VOTED_SUCCESS';
						}
				}
				else
				{
					if (IsAdmin())
						$sql = DBquery("UPDATE BrowserPoints SET Points=Points+$Point WHERE ID=".$vote['ID']);
					else
						$sql = DBquery("UPDATE BrowserPoints SET Points=$Point WHERE ID=".$vote['ID']);
					
					if ($sql==false)
					{
						$result['result'] = 'INTERNAL_ERROR';
						die(json_encode($result));
					}

					$result['result'] = 'VOTED_SUCCESS';
				}

					$CurrVote = DBarray(DBquery("SELECT BrowserPosts.Title, a.*, b.* FROM BrowserPosts
	LEFT JOIN (SELECT PostID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM BrowserPoints WHERE Points>0 GROUP BY PostID) as a ON BrowserPosts.ID = a.PostID
	LEFT JOIN (SELECT PostID, SUM(Points) AS PointsBad FROM BrowserPoints WHERE Points<0 GROUP BY PostID) as b ON BrowserPosts.ID=b.PostID
			 	 WHERE ID = ".$Post));

					$result['PointsGood'] = $CurrVote['PointsGood']*1;
					$result['PointsBad'] = abs($CurrVote['PointsBad']*1);
					$TotalPoints = $CurrVote['PointsGood'] + $CurrVote['PointsBad'];
				
					if ($result['PointsGood']>=$VerificatedPoints){ // jeśli ilość punktów przekroczyła próg weryfikacji
						include SITES."left_menu/functions.php";
						$result['action'] = "VERIFICATED";
						DBquery("UPDATE BrowserPosts SET Status=2 WHERE ID=$Post");
						$UploaderUser = oUser::withName($vote['UploaderNick']);
						$Price = GetSettings('BROWSER_POINTS_FOR_UPLOAD_GOOD_FILE');
						$UploaderUser->Points->AddPoint($Price, 2, $PostInfo->ID, 'Positive mark of post');
						$Link = $PostInfo->Module.'/'.FindCatLinkByID($PostInfo->Category).'?post='.$PostInfo->ID;
						$UploaderUser->SendNotify("Your post <i>\"".$PostInfo->Title."\"</i> was accepted by users. Congratulations !", "FileShare", "browser/$Link", "ICON_GOOD",$PostInfo->Module, $vote['PostID']);
							
					}

					if ($TotalPoints<=$ToTrashPoints){	// jeśli ilośc punktów spadła poniżaj zadanego progu - do kosza
						include SITES."left_menu/functions.php";
						$result['action'] = "TO_TRASH";
						DBquery("UPDATE BrowserPosts SET Status=-1 WHERE ID=$Post");
						$UploaderUser = oUser::withName($vote['UploaderNick']);
						$Link = $PostInfo->Module.'/'.FindCatLinkByID($PostInfo->Category);
						$UploaderUser->SendNotify("Unfortunately, by users votes your article<i>\"".$PostInfo->Title."\"</i> was destined to delete", "FileShare", "browser/$Link", "ICON_BAD", $PostInfo->Module, $vote['PostID']);
							
					}

 



					die(json_encode($result));
}


/*
 * Ocena pliku z modułu BROWSER FILE - DEPRECATED - STARA WERSJA
 */
elseif (isset($_POST['BROWSER_FILE_VOTE'])){
	if (!IsLogin())
		die(json_encode(array("result"=>"ACCESS_DENIED", "action"=>"")));

		$ToTrashPoints = GetSettings("FILE_TO_TRASH_VOICES");
		$VerificatedPoints=  GetSettings("FILE_VERIFICATED_POINTS");

		$file = intval($_POST['BROWSER_FILE_VOTE']);
		$FileInfo = oBFile::withID($file);

		if ($FileInfo->Status()!=FILE_ACCEPTED)
			die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Brak wskaznika")));


			if (!$FileInfo->InUserStock()) // jeśli nie ma tego pliku w zasobach użytkownika
				die(json_encode(array("result"=>"NOT_IN_STOCK")));

				$Point = intval($_POST['FILE_POINT']);
				if (!IsAdmin()) $Point = $Point/abs($Point);
				$result = array("result"=>"UNKNOWN_ERROR");
				
				$sql = DBquery("SELECT BrowserFilesPoints.*, Users.Nick AS UploaderNick, BrowserFiles.ID AS FileID2, Categories.Name AS CategoryName FROM BrowserFilesPoints
					INNER JOIN BrowserFiles ON BrowserFiles.ID = BrowserFilesPoints.FileID
					INNER JOIN Categories ON Categories.Id = BrowserFiles.Category
					INNER JOIN Users ON BrowserFiles.UploaderID = Users.ID
					WHERE FileID=".$file." AND UserID=".$User->ID());

				if ($sql==false)
					die(json_encode(array("result"=>"INTERNAL_ERROR")));

					$vote = dbarray($sql);
					// jeśli możemy głosować
				if (empty($vote))
					{
					$sql = DBquery("INSERT INTO BrowserFilesPoints(`ID`, `FileID`, `UserID`, `Points`, `EntryDate`) VALUES(NULL, $file, ".$User->ID().", $Point, NOW())");
					if ($sql==false)
					{
						$result['result'] = 'INTERNAL_ERROR';
						die(json_encode($result));
					}
					else{
						$result['result'] = 'VOTED_SUCCESS';
								
						}
					}
				else
					{
					if (IsAdmin()) // admin może głosować dowolna ilośc razy
						$sql = DBquery("UPDATE BrowserFilesPoints SET Points=Ponts+$Point WHERE ID=".$vote['ID']);
					else // użytkownik tylko raz
						$sql = DBquery("UPDATE BrowserFilesPoints SET Points=$Point WHERE ID=".$vote['ID']);
					
						$result['msg'] = "UPDATE BrowserFilesPoints SET Points=Ponts+$Point WHERE ID=".$vote['ID'];
					
					if ($sql==false)
					{
						$result['result'] = 'INTERNAL_ERROR';
						die(json_encode($result));
					}
	
					$result['result'] = 'VOTED_SUCCESS';
				}

					$CurrVote = DBarray(DBquery("SELECT BrowserFiles.FileDesc, a.*, b.* FROM BrowserFiles
	LEFT JOIN (SELECT FileID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM BrowserFilesPoints WHERE Points>0 GROUP BY FileID) as a ON BrowserFiles.ID = a.FileID
	LEFT JOIN (SELECT FileID, SUM(Points) AS PointsBad FROM BrowserFilesPoints WHERE Points<0 GROUP BY FileID) as b ON BrowserFiles.ID=b.FileID
			 	 WHERE ID = ".$file));

					$result['PointsGood'] = $CurrVote['PointsGood']*1;
					$result['PointsBad'] = abs($CurrVote['PointsBad']*1);
					$TotalPoints = $CurrVote['PointsGood'] + $CurrVote['PointsBad'];

					if ($result['PointsGood']>=$VerificatedPoints){ // jeśli ilość punktów przekroczyła próg weryfikacji
						$result['action'] = "VERIFICATED";
						DBquery("UPDATE BrowserFiles SET Status=2 WHERE ID=$file");
						$UploaderUser = oUser::withName($vote['UploaderNick']);
						$UploaderUser->SendNotify("Your file was accept by users. Congratulation !", "Download", "browser/", $vote['FileID'], "ICON_GOOD");
							
					}

					if ($TotalPoints<=$ToTrashPoints){	// jeśli ilośc punktów spadła poniżaj zadanego progu - do kosza
						$result['action'] = "TO_TRASH";
						DBquery("UPDATE BrowserFiles SET Status=-1 WHERE ID=$file");
						$UploaderUser = oUser::withName($vote['UploaderNick']);
						$UploaderUser->SendNotify("Unfortunately, by users votes your file was destined to delete.", "Download", "browser", $vote['FileID'], "ICON_BAD");
							
					}
						




					die(json_encode($result));
}

/*
 * Zmiana tytutłu postu
 */
elseif (isset($_POST['CHANGE_POST_TITLE'])){
	$PostID = intval($_POST['CHANGE_POST_TITLE']);
	$NewV = htmlspecialchars($_POST['NewV']);
	$Key = Decrypt($_POST['key']);
	if ($PostID!=$Key) 	{
		StrangeEvent("Bad mark, while changing title","BROWSER");
		die(json_encode(array("result"=>"ACCESS_DENIED", "msg"=>"Reload and try again")));
	}
	$Post = oPost::withID($PostID);
	
	if (!IsAdmin() && !($Post->Owner->CheckID($User->ID) && $Post->Status < POST_VERIFIED)) {
		die(json_encode(array("result"=>"ACCESS_DENIED", "msg"=>"Access denied")));
	}
	
	if ($Post->UpadateProp('Title', $NewV) == false)
		die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Sorry.")));
		
						 
	die(json_encode(array("result"=>"SUCCESS", "msg"=>"Changed successful")));
}


/*
 * Usuwanie pliku z postów
 */
elseif (isset($_POST['DEL_FILE'])){
	$input = explode('|', Decrypt($_POST['DEL_FILE']));
	
	if (count($input)!=2) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Bad input data")));
	
	$PostID = intval($input[0]);
	
	if (!CheckMarker($input[1],FALSE)) 	die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Marker expired")));
	
	$FileName = Decrypt($_POST['KEY']);
	//$Key = Decrypt($_POST['key']);
	if ($PostID==0) 	{
		StrangeEvent("Błędny wskaźnik na post","BROWSER_DEL_FILE");
		die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Reload and try again")));
	}
	
	$Post = oPost::withID($PostID);
	
	if (!IsAdmin() && (!$Post->Owner->CheckID($User->ID) || $Post->Status == POST_VERIFIED ) )
	{
		StrangeEvent("Usuwanie bez uprawnień","BROWSER_DEL_FILE");
		die(json_encode(array("result"=>"ACCESS_DENIED", "msg"=>"Reload and try again")));
	}
	
	$File = $Post->GetFileByName($FileName);
	if ($File == false){
		StrangeEvent("Błędna nazwa pliku w poście","BROWSER_DEL_FILE");
		die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Reload and try again")));
	}
	
	$File->DelFile($Post->ID);
	
	
	die(json_encode(array("result"=>"SUCCESS", "msg"=>"")));
	
}

elseif (isset($_POST['WHO_VOTED'])){
	$post_id = intval($_POST['WHO_VOTED']);
	if ($post_id==0) {
		StrangeEvent("Void article WHO_VOTED query", "BROWSER", array($post_id, $_POST, $_SESSION, $_SERVER));
		die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"BAD DATA")));
	}

	$sql = DBquery("SELECT Nick, BrowserPoints.Points FROM BrowserPoints INNER JOIN Users on UserID=Users.ID WHERE PostID=$post_id");

	if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"INTERNAL_ERROR")));

	$votes = array();

	while($row = DBarray($sql))
		array_push($votes, $row);

		die(json_encode(array("result"=>"SUCCESS", 'voters'=> count($votes), 'VOTES'=>$votes)));


}


// jeśli nie odnaleziono żadnego zapytania zwracamy NO-DATA
die(json_encode(array("result"=>"NO-DATA")));
?>