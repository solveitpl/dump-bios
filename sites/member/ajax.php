<?php
/*
 * 
 * Uaktualnianie informacji użytkownika na profilu
 */
if (isset($_POST['UPDATE_USER_PROFILE'])){
	if (!IsLogin()) {
		echo json_encode(array(
				"result"=>"ERROR",
				"msg"=>"Not this way..."
		));
		StrangeEvent("Wywołanie AJAX bez uprawnień", "MEMBER_SETING");
		die();
	}

	$fields = array("city", "country", "birthday", "WantsNewsletter");

	$field = htmlspecialchars($_POST['UPDATE_USER_PROFILE']);
	$value = htmlspecialchars($_POST['value']);
	$time = intval(Decrypt($_POST['code']));
	$ID = $User->ID();

	// jeśli znacznik czasu przekroczył 2400s
	if ((time(NULL)-$time>2400) || ($ID==0)) die(json_encode(array("result"=>"ERROR", "msg"=>"Proszę odświeżyć stronę")));
	if (!in_array($field, $fields)) die(json_encode(array("result"=>"ERROR", "msg"=>"Not this way...")));
	if (($field=="birthday")&&(!validateDate($value, "Y-m-d"))) die(json_encode(array("result"=>"ERROR", "msg"=>"Niepoprawny format daty")));
	if ($field=="WantsNewsletter") $value= ($value=='true' ? 1 : 0);

	$sql = DBquery("UPDATE Users SET $field='$value' WHERE id=$ID");




	if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Błąd wewnętrzny $field $value")));



	die(json_encode(array(
			"result"=>"SUCCESS",
			"NewValue" => $value
	)));

}


/*
 * Pobranie listy artykułów danego użytkownika
 */
elseif (isset($_POST['GET_USER_ARTICLE'])){
	$User_name = htmlspecialchars($_POST['GET_USER_ARTICLE']);
	$sql = DBquery("SELECT Articles.Title, Articles.AddDateTime, Articles.SubcategoryTitle, Articles.link, c1.Name as model_name, c2.Name AS manu_name
			FROM Users INNER JOIN Articles ON Users.ID=Articles.AuthorID
			INNER JOIN Categories AS c1 ON Articles.Category=c1.Id
			INNER JOIN Categories AS c2 ON c1.ParentID = c2.Id
			WHERE Users.Nick='$User_name' ORDER BY Articles.AddDateTime DESC");
	$results = array();
	while($row = dbarray($sql))
		array_push($results, $row);
		die(json_encode($results));
}

/*
 * Pobranie listy plików danego użytkownika
 */
elseif (isset($_POST['GET_USER_FILE'])){
	require_once SITES.'download/header.php';
	$User_name = htmlspecialchars($_POST['GET_USER_FILE']);
	if (IsAdmin()) $Condition= '';
	else $Condition=' AND UploadedFile.Status ='.FILE_ACCEPTED." OR UploadedFile.Status = ".FILE_VERIFIED." OR UploadedFile.UploaderID=".$User->ID;
	
	$sql = DBquery("SELECT UploadedFile.FileDesc, FROM_UNIXTIME(UploadedFile.FileUploaded) AS upload_time,
					UploadedFile.Id AS Id, UploadedFile.Status, UploadedFile.DownloadCount, a.PointsGood, b.PointsBad
					FROM Users INNER JOIN UploadedFile ON Users.ID=UploadedFile.UploaderID
					LEFT JOIN (SELECT FileID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM FilesPoints WHERE Points>0 GROUP BY FileID) as a ON UploadedFile.ID=a.FileID
					LEFT JOIN (SELECT FileID, SUM(Points) AS PointsBad FROM FilesPoints WHERE Points<0 GROUP BY FileID) as b ON UploadedFile.ID=b.FileID
					WHERE Users.Nick='$User_name' $Condition ORDER BY UploadedFile.FileUploaded DESC");
	$results = array();
	while($row = dbarray($sql)){
		if (array_key_exists($row['Status'], $STATUS_LABEL))
			$row['StatusStr']  = $STATUS_LABEL[$row['Status']];
		array_push($results, $row);
		
	}
		die(json_encode($results));
}

/*
 * Pobranie listy postów danego użytkownika
 */
elseif (isset($_POST['GET_USER_POSTS'])){
	require_once SITES.'browser/header.php';
	$User_name = htmlspecialchars($_POST['GET_USER_POSTS']);
	
	if (IsAdmin()) $Condition= '';
	else $Condition=' AND BrowserPosts.Status ='.POST_ACCEPTED." OR BrowsersPosts.Status = ".POST_VERIFIED." OR BrowserPosts.UserID=".$User->ID;
	
	$sql = DBquery("SELECT BrowserPosts.ID,BrowserPosts.Title, BrowserPosts.Status, BrowserPosts.DownloadCount, FROM_UNIXTIME(BrowserPosts.SendTime) AS SendDate,
					a.PointsGood, b.PointsBad, Users.Nick AS UploaderNick
		FROM Users
		INNER JOIN BrowserPosts ON Users.ID = BrowserPosts.UserID
		LEFT JOIN (SELECT PostID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM BrowserPoints WHERE Points>0 GROUP BY PostID) as a ON BrowserPosts.ID=a.PostID
		LEFT JOIN (SELECT PostID, SUM(Points) AS PointsBad FROM BrowserPoints WHERE Points<0 GROUP BY PostID) as b ON BrowserPosts.ID=b.PostID
		WHERE Users.Nick='$User_name' $Condition ORDER BY BrowserPosts.SendTime DESC
		");

	$results = array();
	while($row = dbarray($sql)){
		if (array_key_exists($row['Status'], $STATUS_LABEL))
			$row['StatusStr']  = $STATUS_LABEL[$row['Status']];
			array_push($results, $row);

	}
	die(json_encode($results));
}


/*
 * Pobranie listy plików danego użytkownika
 */
elseif (isset($_POST['GET_USER_POINTS_DATA'])){
	$SourceNames = array(0=>'Daily Points', 1=>'Software', 2=>'Download file', 10=>'Donate', 11=>'Points from admin');
	$UserID = intval($_POST['GET_USER_POINTS_DATA']);
	$sql = DBquery("SELECT *
			FROM Points 
			WHERE UserID='$UserID' ORDER BY EntryDate DESC");
	$results = array();
	while($row = dbarray($sql))
	{
		$row['SrcName']=$SourceNames[$row['Source']];
		array_push($results, $row);
	}
		die(json_encode($results));
}

/*
 * Pobranie listy plików danego użytkownika
 */
elseif (isset($_POST['GET_USER_ACTIVITY'])){
	$SourceNames = array(0=>'Daily Points', 1=>'Software', 2=>'Download file', 10=>'Donate', 11=>'Points from admin');
	$UserID = intval($_POST['GET_USER_ACTIVITY']);
	$sql = DBquery("SELECT *
			FROM Points
			WHERE UserID='$UserID'");
	
	$results = array();
	while($row = dbarray($sql))
	{
		$row['SrcName']=$SourceNames[$row['Source']];
		array_push($results, $row);
	}
	die(json_encode($results));
	
	
}


/*
 * Dodanie nowej wiadomości
 */
elseif (isset($_POST['SEND_CHAT_MSG'])){
	if (!IsLogin()) _die("Bad...");
	$msg_pointer = $_POST['msg_pointer'];
	$content = htmlspecialchars($_POST['answer_content']);
	$send_data = explode("|", Decrypt($_POST['send_data']));
	$recipient_id = intval($send_data[0]);
	$send_time = $send_data[1];

	if ($recipient_id==0) die(json_encode(array("result"=>"ERROR", "msg"=>"Bad pointer", "msg_pointer"=>$msg_pointer)));

	$sql=DBquery("INSERT INTO Messages(ID, UserID, RecipientID, DateOF, Content, Readed)
			VALUES(NULL, ".$User->ID().", $recipient_id, CURRENT_TIMESTAMP(), '$content',0)");
	if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Internal error", "msg_pointer"=>$msg_pointer)));

	die(json_encode(array(
			"result"=>"SUCCESS",
			"msg"=>"Dodano",
			"msg_pointer"=>$msg_pointer,
			"add_time"=>time(NULL),
			"format_add_time"=>date("Y-m-d h:i:s",time(NULL)),


	)));

}




/*
 * Załadowanie starszych wiadomości
 */
elseif (isset($_POST['LOAD_MORE_MSGS'])){
	if (!IsLogin()) _die("Bad...");
	$msgs = array();
	$User_name = htmlspecialchars($_POST['LOAD_MORE_MSGS']);
	$ID = intval($_POST['ID']);
	if (($User_name=='') || ($ID==0)) die(json_encode(array("result"=>"ERROR", "msg"=>"Zły wskaźnik", "msg_pointer"=>$ID)));

	$recipient = oUser::withName($User_name);
	$sql = dbquery("SELECT Messages.*, t1.Nick AS AuthorNick FROM Messages
					INNER JOIN Users AS t1 ON Messages.UserID=t1.ID
					WHERE
					((UserID=".$User->ID()." AND RecipientID=".$recipient->ID().") OR
					(UserID=".$recipient->ID()." AND RecipientID=".$User->ID().")) AND
			Messages.ID < $ID
			ORDER BY DateOF DESC LIMIT 10");

	if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Internal error. Sorry. Admin got report about this", "counter"=>($sql->num_rows), "msg_pointer"=>$ID)));

	while($row=DBarray($sql))
		array_push($msgs, $row);


		die(json_encode(array(
				"result"=>"GOT_MESSAGE",
				"msgs"=>$msgs,
				"msg_count" => count($msgs)
		)));

}

elseif (isset($_POST['PICK_AVATAR'])){
	
	// Check marker
	$marker = explode('|',Decrypt($_POST['PICK_AVATAR']));
	$filename = $marker[0];
	$time = $marker[1];
	
	if (!IsLogin()){
		StrangeEvent("Annymouse user try to change avatar", "USER_SETTINGS",array("USER"=>$User, "POST"=>$_POST, "marker"=>$marker));
		die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"User not logged in")));
	}
	
	// czy czasem ktoś nas w konia nie robi
	if (!_file_exists(IMAGES."avatars/".$filename)){
		StrangeEvent("Picked avatar do not exist", "USER_SETTINGS",array("USER"=>$User, "POST"=>$_POST, "marker"=>$marker));
		die(json_encode(array("result"=>"ERROR", "msg"=>"Invalid file")));	
	}
	
	
	if ($User->SetAvatar($filename))
		die(json_encode(array("result"=>"SUCCESS")));
	else
		die(json_encode(array("result"=>"ERROR", "msg"=>$filename." ".$time)));
	
	
	
	
}




// jeśli nie odnaleziono żadnego zapytania zwracamy NO-DATA
die(json_encode(array("result"=>"NO-DATA")));
?>