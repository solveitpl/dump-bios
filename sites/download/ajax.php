<?php
include 'header.php';
/*
 * Głosowanie na plik
 */
if (isset($_POST['FILE_VOTE'])){

	if (!IsAuth())
		die(json_encode(array("result"=>"ACCESS_DENIED", "action"=>"")));

		$ToTrashPoints = GetSettings("FILE_TO_TRASH_VOICES");
		$VerificatedPoints=  GetSettings("FILE_VERIFICATED_POINTS");
		$file = intval($_POST['FILE_VOTE']);
		$FileInfo = oDFile::withID($file);

		if ($FileInfo->Status()!=FILE_ACCEPTED)
			die(json_encode(array("result"=>"ERROR", "msg"=>"File need to be accepted by admin before voting")));
		
		if (!IsAdmin() && $User->CheckID($FileInfo->UploaderID))
			die(json_encode(array("result"=>"YOUR_FILE", "msg"=>"You cannot vote for your own file")));
			

			if (!$FileInfo->InUserStock()) // jeśli nie ma tego pliku w zasobach użytkownika
				die(json_encode(array("result"=>"NOT_IN_STOCK")));

				$Point = intval($_POST['FILE_POINT']);
				if (!IsAdmin()) $Point = $Point/abs($Point);
				$result = array("result"=>"UNKNOWN_ERROR");

				$sql = DBquery("SELECT FilesPoints.*, Users.Nick AS UploaderNick, UploadedFile.ID AS FileID2, Categories.Name AS CategoryName FROM FilesPoints
					INNER JOIN UploadedFile ON UploadedFile.ID = FilesPoints.FileID
					LEFT JOIN Users ON UploadedFile.UploaderID = Users.ID
					LEFT JOIN Categories ON Categories.Id = UploadedFile.Category
					WHERE FileID=".$file." AND UserID=".$User->ID());

				if ($sql==false)
					die(json_encode(array("result"=>"INTERNAL_ERROR")));

					$vote = dbarray($sql);
					// jeśli możemy głosować
					if (empty($vote))
					{
						$sql = DBquery("INSERT INTO FilesPoints(`ID`, `FileID`, `UserID`, `Points`, `EntryDate`) VALUES(NULL, $file, ".$User->ID().", $Point, NOW())");
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
						if ($Point==$vote['Points'] && (!IsAdmin())) die(json_encode(array("result"=>"VOTED_ALREADY")));
						
						if (IsAdmin())
							$sql = DBquery("UPDATE FilesPoints SET Points=`Points`+$Point WHERE ID=".$vote['ID']);
						else		
							$sql = DBquery("UPDATE FilesPoints SET Points=$Point WHERE ID=".$vote['ID']);
						
						if ($sql==false)
						{
							$result['result'] = 'INTERNAL_ERROR';
							die(json_encode($result));
						}

						$result['result'] = 'VOTED_SUCCESS';
					}

					$CurrVote = DBarray(DBquery("SELECT UploadedFile.FileDesc, a.*, b.* FROM UploadedFile
	LEFT JOIN (SELECT FileID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM FilesPoints WHERE Points>0 GROUP BY FileID) as a ON UploadedFile.ID = a.FileID
	LEFT JOIN (SELECT FileID, SUM(Points) AS PointsBad FROM FilesPoints WHERE Points<0 GROUP BY FileID) as b ON UploadedFile.ID=b.FileID
			 	 WHERE ID = ".$file));


					$result['PointsGood'] = $CurrVote['PointsGood']*1;
					$result['PointsBad'] = abs($CurrVote['PointsBad']*1);
					$TotalPoints = $CurrVote['PointsGood'] + $CurrVote['PointsBad'];

					if ($result['PointsGood']>=$VerificatedPoints){ // jeśli ilość punktów przekroczyła próg weryfikacji
						$result['action'] = "VERIFICATED";
						DBquery("UPDATE UploadedFile SET Status=2 WHERE ID=$file");
						$UploaderUser = oUser::withName($vote['UploaderNick']);
						$UploaderUser->SendNotify("Your file: <i>\"".$FileInfo->FileDesc."\"</i> has been veryfied. Congratulation !.", "SOFTWARE", "downloads/item/".$vote['FileID'], "ICON_GOOD", $FileInfo->Module, $vote['FileID']);
						
					}

					if ($TotalPoints<=$ToTrashPoints){	// jeśli ilośc punktów spadła poniżaj zadanego progu - do kosza
						$result['action'] = "TO_TRASH";
						DBquery("UPDATE UploadedFile SET Status=-1 WHERE ID=$file");
						$UploaderUser = oUser::withName($vote['UploaderNick']);
						$UploaderUser->SendNotify("Your file <i>\"".$FileInfo->FileDesc."\"</i> has been moved to trash. Maybe next time", "SOFTWARE", "", "ICON_BAD", $FileInfo->Module, $vote['FileID']);
							
					}






					die(json_encode($result));
}

/*
 * UPDATE informacji o pliku zaraz po załadowaniu
 */
elseif (isset($_POST['SAVE_FILEINFO'])){
	die();
	$FileDesc = htmlspecialchars(strip_tags($_POST['FileDesc']));
	$FileLicense = htmlspecialchars(strip_tags($_POST['FileLicense']));
	if (isset($_POST['OS']))
		$FileOS = htmlspecialchars(implode(',', $_POST['OS']));
		else $FileOS = '';
		$FileDescEX = htmlspecialchars($_POST['FileDescExt']);
		$FileID = intval(Decrypt($_POST['fileID']));
		$marker = Decrypt($_POST['marker']);


		if ($FileDesc=='')
		{
			StrangeEvent("Użytkownik nie podał nazwy pliku, mimo tego wysłano zapytanie zapisu do bazy", "DOWNLOAD");
			die(json_encode(array("result"=>"ERROR", "msg"=>"Nie podano nazwy pliku...")));
		}

		if ($FileID==0)
		{
			StrangeEvent("Użytkownik nie otrzymał zwrotnego ID pliku", "DOWNLOAD");
			die(json_encode(array("result"=>"ERROR", "msg"=>"Błędny wskaźnik pliku", "marker"=>$marker)));
		}

		$sql = DBquery("UPDATE UploadedFile SET FileDesc='$FileDesc', FileDescExt='$FileDescEX', License='$FileLicense',
				OS='$FileOS' WHERE ID=$FileID");

		if ($sql==false)
		{
			echo json_encode(array("result"=>"ERROR", "msg"=>"Błąd wewnętrzny"));
			StrangeEvent("Nie udało się zaktualizować wpisu nowego pliku: <br>SQL: UPDATE UploadedFile SET FileDesc='$FileDesc', FileDescExt='$FileDescEX', License='$FileLicense',
					OS='$FileOS' WHERE ID=$FileID", "DOWNLOAD");
			die();
		}



		die(json_encode(array("result"=>"SUCCESS", "msg"=>"")));


}
/*
 * Pobranie listy podobnych plików w kategorii
 */
elseif (isset($_POST['GET_FILELIST'])){
	$FileCategory = intval($_POST['CATEGORY']);
	$Module = strtoupper(htmlspecialchars($_POST['MODULE']));
	$Name = htmlspecialchars($_POST['Name']);
	$sql = DBquery("SELECT UploadedFile.Id, FileDesc, Users.Nick AS UserNick FROM UploadedFile LEFT JOIN Users ON UploadedFile.UploaderID=Users.ID
			WHERE FileDesc LIKE '%$Name%'");
	// Category=$FileCategory AND MODULE='$Module' AND
	$results = array();
	while($row = dbarray($sql))
		array_push($results, $row);
		die(json_encode($results));
}

/*
 * USUWANIE PLIKU
 * Tylko admin lub uploader jeśli plik jeszcze nie jest zweryfikowany
 */
elseif (isset($_POST['DELETE_DOWNLOAD_FILE'])){
	$input = Decrypt($_POST['DELETE_DOWNLOAD_FILE']);
	$arr = explode('|', $input);
	$ID = intval($arr[0]);
	$filename = $arr[1];
	
	$File = oDFile::withID($ID);
	
	if (IsAdmin() && $File->Status()!=FILE_REJECTED)
	{
		$File->Status = FILE_REJECTED;
		$File->UpdateDB();
		die(json_encode(array("result"=>"SUCCESS", "status"=>FILE_REJECTED)));
	}
	elseif  (IsAdmin() && $File->Status()==FILE_REJECTED)
	{
		// usuwamy plik trwale
		$File->Delete();
		die(json_encode(array("result"=>"SUCCESS", "status"=>-100)));
		
	}
	elseif  ($User->CheckID($File->UploaderID) && $File->Status!=FILE_VERIFIED)
	{
		// przenosimy do plik do kosza, admin zdecyduje
		$File->Status = FILE_REJECTED;
		$File->UpdateDB();
		die(json_encode(array("result"=>"SUCCESS", "status"=>FILE_REJECTED)));
	}
		
	die(json_encode(array("result"=>"NO-ACTION")));
	
}

/*
 * Kto głosował na dany plik
 */


elseif (isset($_POST['WHO_VOTED'])){
	$file_id = intval($_POST['WHO_VOTED']);
	if ($file_id==0) {
		StrangeEvent("Void article WHO_VOTED query", "ARTICLES", array($file_id, $_POST, $_SESSION, $_SERVER));
		die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"BAD DATA")));
	}

	$sql = DBquery("SELECT Nick, FilesPoints.Points FROM FilesPoints INNER JOIN Users on UserID=Users.ID WHERE FileID=$file_id");

	if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"INTERNAL_ERROR")));

	$votes = array();

	while($row = DBarray($sql))
		array_push($votes, $row);
	
		die(json_encode(array("result"=>"SUCCESS", 'voters'=> count($votes), 'VOTES'=>$votes)));


}


// jeśli nie odnaleziono żadnego zapytania zwracamy NO-DATA
die(json_encode(array("result"=>"NO-DATA")));

?>