<?php
if (!IsAdmin()){
	StrangeEvent("Unauthoirized person try to send query to admin ajax", "SEND_NEWSLETTER", array("POST"=>$_POST, "USER"=>$User));
	die(json_encode(array("result"=>'ITS_NOT_GONNA_MAKE_IT')));
}

	
require_once 'GUI/header.php';
	
if (isset($_POST['GetTab'])){
	$content = array('html'=>'', 'css'=>'', 'js'=>'');
	switch (strtolower($_POST['GetTab'])){
		case 'general':
			ob_start();
			include 'panels/general/general.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['css'] = GetFileContent('admin/panels/general/style.css');
			die(json_encode($content));
			break;
			
		case 'users':
			ob_start();
			include 'panels/users/users.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['css'] = GetFileContent('admin/panels/users/style.css');
			$content['js'] = GetFileContent('admin/panels/users/script.js');
				
			die(json_encode($content));
			break;

		case 'menu':
			ob_start();
			include 'panels/menu/menu.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['css'] = GetFileContent('admin/panels/menu/style.css');
			$content['js'] = GetFileContent('admin/panels/menu/script.js');		
			die(json_encode($content));
			break;
					
		case 'settings':
			ob_start();
			include 'panels/settings/global_settings.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['css'] = GetFileContent('admin/panels/settings/styles.css');
			$content['js'] = GetFileContent('admin/panels/settings/script.js');
			die(json_encode($content));
			break;
		
		case 'guardian':
			ob_start();
			include 'panels/guardian/guardian.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['css'] = GetFileContent('admin/panels/guardian/styles.css');
			$content['js'] = GetFileContent('admin/panels/guardian/script.js');
			die(json_encode($content));
			break;
			
		case 'reports':
			ob_start();
			include 'panels/reports/reports.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['css'] = GetFileContent('admin/panels/reports/styles.css');
			$content['js'] = GetFileContent('admin/GUI/Chart.js');
			$content['js'] .= GetFileContent('admin/panels/reports/script.js');
			
			die(json_encode($content));
			break;
		
		case 'todothings':
			ob_start();
			include 'panels/ToDo/ToDo.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['css'] = GetFileContent('admin/panels/ToDo/styles.css');
			$content['js'] = GetFileContent('admin/panels/ToDo/script.js');
			die(json_encode($content));
			break;
			
					
			
		default:
			$content['html'] = "NO-DATA";
			die(json_encode($content));
				
	}
}

if (isset($_POST['UserDetail'])){
	$content = array('user_data'=>'', 'result'=>'OK');
	$UserID = intval($_POST['UserDetail']);
	
	//$sql = DBquery("SELECT *, '' AS Password FROM Users WHERE ID=$UserID");
	//$content['user_data'] = DBarray($sql);
	$GetUser = oUser::withID($UserID);
	$GetUser->Points->GetTotalPoint();
	$GetUser->Points->DetailPoints();
	$content['user_data'] = $GetUser;
	
	die(json_encode($content));
}

if (isset($_POST['ChangeUserData'])){
	$result['result'] = 'BAD';
	$ID = intval($_POST['ChangeUserData']);
	$field = htmlspecialchars($_POST['field']);
	$val = htmlspecialchars($_POST['val']);
	
	
	$fields = array('nick'=>'Nick','email'=>'Email','user_status'=>'Status',
			'perm_user'=>'PermLevel', 'user_status'=>'Status','birthday'=>'Birthday', 
			'city'=>'City','country'=>'Country');
	if ((!array_key_exists($field, $fields)) || ($ID==0)) die(json_encode(array('result'=>'ERROR', 'msg'=>'Błedne dane #1')));
	
	if ((($field=='user_status') || ($field=='perm_user')) && ($ID==$User->ID)) die(json_encode(array('result'=>'ERROR', 'msg'=>'You cannot change your status')));
		
	
	
	$sql = DBquery("UPDATE Users SET ".$fields[$field]."='$val' WHERE ID=$ID");
	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Bad Data #2')));
	
	
	
	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Good')));
	
	
	die(json_encode($result));
}

if (isset($_POST['GIVE_POINTS_TO_USER']))
{
	$UserID = intval($_POST['GIVE_POINTS_TO_USER']);
	$Points = intval($_POST['Points']);
	$Comm = htmlspecialchars($_POST['Comm']);
	$sql =	DBquery("INSERT INTO 
			Points (`ID`, `UserID`, `Points`, `EntryDate`, `ElementID`, `Source`, `Comment`,`MODULE`)
			VALUES(NULL, $UserID, $Points, NOW(), ".$User->ID().", 11, '$Comm', '')");

	if ($sql == false)
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Error.')));
		
	
	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Dodano')));
	
}

if (isset($_POST['DEL_ARTICLE']))
{
	$marker = $_POST['marker'];
	$art_link = htmlspecialchars($_POST['DEL_ARTICLE']);

	if (!CheckMarker($marker))
		die(json_encode(array('result'=>'BAD_TOKEN', 'msg'=>'Błąd #0')));
	$Art = DBquery("SELECT * FROM Articles WHERE link='$art_link'");
	if (!($Art->num_rows))
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Article doesnt exist')));
	
	$Art = DBarray($Art);
	$sql = DBquery("DELETE FROM Articles WHERE link='$art_link'");
	if ($sql == false)
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Error. #1')));

		
	$sql = DBquery("DELETE FROM ArticlesPoints WHERE ArticleID='".$Art['ID']."'");
	if ($sql == false)
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Error. #2')));
		
		

	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Article deleted')));

}

if (isset($_POST['DEL_FILE']))
{
	$marker = $_POST['marker'];
	$FileID = intval($_POST['DEL_FILE']);
	require_once SITES.'download/header.php';
	$File = oDFile::withID($FileID);

	if (!CheckMarker($marker))
		die(json_encode(array('result'=>'BAD_TOKEN', 'msg'=>'Error #0')));
	
	
	if ($File->ID()==0)
		die(json_encode(array('result'=>'ERROR', 'msg'=>'File doesnt exist')));

	$File->Delete();

	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'File deleted')));

}




if (isset($_POST['GET_USER_LIST']))
{
	
	$where_line='';
	$where_arr = array('ID > 0');
	$Users = array();
	if (!empty($_POST['Nick'])) array_push($where_arr, "Nick LIKE '%".htmlspecialchars($_POST['Nick'])."%'");
	if (!empty($_POST['Email'])) array_push($where_arr, "Email LIKE '%".htmlspecialchars($_POST['Email'])."%'");
	if (!empty($_POST['status']) && ($_POST['status']!=100)) array_push($where_arr, "Status='".intval($_POST['status'])."'");
	if (!empty($_POST['reg_date_from']) && !empty($_POST['reg_date_to'])) 
		array_push($where_arr, "RegisterTime>'".htmlspecialchars($_POST['reg_date_from'])." 00:00:00' AND RegisterTime<'".htmlspecialchars($_POST['reg_date_to'])." 00:00:00'");
	if (!empty($_POST['city'])) array_push($where_arr, "City LIKE '%".htmlspecialchars($_POST['city'])."%'");
	
	if (!empty($_POST['last_login_from']) && !empty($_POST['last_login_to']))
		array_push($where_arr, "LastLoginTime>'".htmlspecialchars($_POST['last_login_from'])."' AND RegisterTime<'".htmlspecialchars($_POST['last_login_to'])."'");
	
	$where_line = implode(' AND ', $where_arr);

	
	$sql = DBquery("SELECT * FROM Users WHERE $where_line");
	
	while($row = DBarray($sql))
	{
		$row['Status_str'] = $USER_STATUS[$row['Status']];
		$row['TotalPoints'] = "N/A";
		
		array_push($Users, $row);
	}
	
	
	die(json_encode(array('msg'=>$where_line, 'arr'=>$where_arr, 'Users'=>$Users)));
	
}


if (isset($_POST['SET_POST_STATUS']))
{
	$PostID = intval($_POST['SET_POST_STATUS']);
	$NewStatus = intval($_POST['NewStatus']);
	
	if ($PostID<=0) die(json_encode(array('result'=>'ERROR', 'msg'=>'Invalid Post ID')));
	
	$sql = dbquery("UPDATE BrowserPosts SET `Status`=$NewStatus WHERE `ID` = $PostID");
	
	if ($sql==false) die(json_encode(array('result'=>'ERROR', 'msg'=>'Sry...')));
	
	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Post status changed')));
	
}

if (isset($_POST['DELETE_POST']))
{
	include SITES.'browser/header.php';
	$PostID = intval($_POST['DELETE_POST']);
	$marker = intval(Decrypt($_POST['marker']));

	if (($PostID<=0) || ($marker == 0) || ($marker!=$PostID) ) die(json_encode(array('result'=>'ERROR', 'msg'=>"Invalid Post ID $marker $PostID")));
	
	$Post = oPost::withID($PostID);
	/*for ($i=0; $i<count($Post->Files); $i++)
	{*/
		//usuwanie miniatur
        //usunięta tymczasowo pętla ze zględu na to, że według nowego widoku, w jednym poście powinna znajdować się tylko jedna miniatura
        //tymczasowo usunięte usuwanie miniatury, ze względu na to, że usuwało ikony z images/icon
    
		/*unlink($Post->Files[0]->ThumbDir.$Post->Files[0]->Miniature);*/
		unlink($Post->Files[0]->ThumbDir.$Post->Files[0]->Preview);
		unlink($Post->Files[0]->Path.$Post->Files[0]->Filename);
	/*}*/
	
	$sql = dbquery("DELETE FROM BrowserPosts WHERE `ID` = $PostID");
	if ($sql==false) die(json_encode(array('result'=>'ERROR', 'msg'=>'Error #1')));

	$sql = dbquery("DELETE FROM BrowserFiles WHERE `PostID` = $PostID");
	if ($sql==false) die(json_encode(array('result'=>'ERROR', 'msg'=>'Error #2')));
	
	$sql = dbquery("DELETE FROM BrowserPoints WHERE `PostID` = $PostID");
	if ($sql==false) die(json_encode(array('result'=>'ERROR', 'msg'=>'Error #3')));
	
	
	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Post deleted')));

}


// ZMIANA STATUSU PLIKÓW DOWNLOAD
if (isset($_POST['CHANGE_DFILE_STATUS']))
{
	
	$FileID = intval($_POST['CHANGE_DFILE_STATUS']);
	$NewStatus = intval($_POST['NewStatus']);
	$key = Decrypt($_POST['key']);
	
	if ($key!=$FileID || $FileID <=0) 
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Invalid Post ID')));

	$sql = dbquery("UPDATE UploadedFile SET `Status`=$NewStatus WHERE `ID` = $FileID");

	if ($sql==false) die(json_encode(array('result'=>'ERROR', 'msg'=>'Sry...')));

	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Post status changed')));

}


// ZMIANA STATUSU PLIKÓW DOWNLOAD
if (isset($_POST['CHANGE_ARTICLE_STATUS']))
{

	$ArtID = intval($_POST['CHANGE_ARTICLE_STATUS']);
	$NewStatus = intval($_POST['NewStatus']);
	$key = Decrypt($_POST['key']);

	if ($key!=$ArtID || $ArtID <=0)
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Invalid Post ID')));

		$sql = dbquery("UPDATE Articles SET `Status`=$NewStatus WHERE `ID` = $ArtID");

		if ($sql==false) die(json_encode(array('result'=>'ERROR', 'msg'=>'Sry...')));

		die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Article status changed')));

}


if (isset($_POST['ChangeSettValue'])){

	$result['result'] = 'BAD';
	$field = htmlspecialchars($_POST['ChangeSettValue']);
	$val = htmlspecialchars($_POST['val']);
	
	if (isset($_POST['mode'])) $mode = $_POST['mode']; else $mode='';

	switch ($mode){
		case 'ENTER_SPACING':
			$val = $val;
			break;
	}
	
	$sql = DBquery("UPDATE Settings SET value='$val' WHERE name='$field'");
	
	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Error at changing settings')));

	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Good')));


	die(json_encode($result));
}

if (isset($_POST['DelAllLogs'])){

	$result['result'] = 'BAD';
	
	if (!CheckMarker($_POST['marker'])) die(json_encode(array('result'=>'ERROR', 'msg'=>'BAD INPUT DATA')));
		
		

	$sql = DBquery("TRUNCATE internal_log");

	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #1')));

	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Logs cleared')));


	die(json_encode($result));
}

if (isset($_POST['DelAllPHPLogs'])){

	$result['result'] = 'BAD';

	if (!CheckMarker($_POST['marker'])) die(json_encode(array('result'=>'ERROR', 'msg'=>'BAD INPUT DATA')));



	$sql = DBquery("TRUNCATE PHPErrorsLogs");

	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #1')));

	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Logs cleared')));


	die(json_encode($result));
}


if (isset($_POST['DelAllAlerts'])){

	$result['result'] = 'BAD';

	if (!CheckMarker($_POST['marker'])) die(json_encode(array('result'=>'ERROR', 'msg'=>'BAD INPUT DATA')));

	$sql = DBquery("TRUNCATE StrangeEvents");

	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #1')));

	die(json_encode(array('result'=>'SUCCESS', 'msg'=>'Alerts cleared')));


	die(json_encode($result));
}

if (isset($_POST['LOAD_LOGS'])){

	$LAST_ID = htmlspecialchars($_POST['LOAD_LOGS']);
	$result['result'] = 'BAD';

	$sql = DBquery("SELECT * FROM internal_log ORDER BY date_of DESC LIMIT 0,20");

	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #1')));

	$msgs = array();
	while($row = DBarray($sql))
		array_push($msgs, $row);
		
	die(json_encode(array('result'=>'SUCCESS', 'msg_count'=>count($msgs), 'msgs'=>$msgs)));


	die(json_encode($result));
}

if (isset($_POST['LOAD_PHP_LOGS'])){

	$LAST_DATE = htmlspecialchars($_POST['LOAD_PHP_LOGS']);
	$result['result'] = 'BAD';

	$sql = DBquery("SELECT PHPErrorsLogs.*, Users.Nick AS UserNick FROM PHPErrorsLogs
			LEFT JOIN Users ON Users.ID=PHPErrorsLogs.UserID
			 ORDER BY date_of DESC LIMIT 0,20");

	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #1')));

	$msgs = array();
	while($row = DBarray($sql)){
		$row['ErrStr'] = FriendlyErrorType($row['code']);
		array_push($msgs, $row);
		
	}

		die(json_encode(array('result'=>'SUCCESS', 'msg_count'=>count($msgs), 'msgs'=>$msgs)));


		die(json_encode($result));
}


if (isset($_POST['LOAD_ALERTS'])){

	$LAST_ITEM = htmlspecialchars($_POST['LOAD_ALERTS']);
	$result['result'] = 'BAD';
	$LAST_ITEM = intval(strtotime($LAST_ITEM));

	$sql = DBquery("SELECT StrangeEvents.*, Users.Nick AS UserNick FROM StrangeEvents LEFT JOIN Users on StrangeEvents.User = Users.ID ORDER BY Date DESC LIMIT 0,20");
	
	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #1')));

	$msgs = array();
	while($row = DBarray($sql))
	{
		$row['datef'] = date("Y-m-d H:i:s",$row['Date']);
		array_push($msgs, $row);
	}
		die(json_encode(array('result'=>'SUCCESS', 'msg_count'=>count($msgs), 'msgs'=>$msgs)));


		die(json_encode($result));
}

if (isset($_POST['DELETE_USER'])){

	$USER_ID = intval($_POST['DELETE_USER']);
	//$MARKER = Encrypt($string)
	if (!CheckMarker($_POST['marker'])) die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'marker_error')));
	
	$sql = DBquery("DELETE FROM Users WHERE ID=$USER_ID AND PermLevel<".ADMIN);
	if (!AffectedRows()) die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #1')));
	
	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #2')));
	
	die(json_encode(array('result'=>'SUCCESS')));


}

if (isset($_POST['GET_USER_SEARCH_WORDS'])){

	$USER_NAME = htmlspecialchars($_POST['GET_USER_SEARCH_WORDS']);
	
	$sql = DBquery("SELECT KEYWORD, Quantity FROM `SearchWords` INNER JOIN Users ON Users.ID=SearchWords.UserID WHERE Users.Nick ='$USER_NAME' ORDER BY Quantity DESC LIMIT 10");
	
	if ($sql == false)  die(json_encode(array('result'=>'INTERNAL_ERROR', 'msg'=>'Query Error #2')));

	$words = array();
	while($row=DBarray($sql)){
		array_push($words, $row);
	}
	
	die(json_encode($words));


}

// PRZESUWANIE KATEGORII MENU
if (isset($_POST['MOVE_MENU_CATEGORY'])){

	$MENU_ITEM = intval($_POST['MOVE_MENU_CATEGORY']); 
	$MOVE_TO = intval($_POST['MOVE_TO']);
	
	$CATEGORY = DBarray(DBquery("SELECT * FROM Categories WHERE ID =".$MOVE_TO));
	$ITEM = DBarray(DBquery("SELECT * FROM Categories WHERE ID =".$MENU_ITEM));
	$res='';
	
	// ustalamy na jakim levelu znajduje się przenoszony item
	$TMP = $ITEM;
	$level=0;
	while ($TMP['ParentID']!=0 && $level < 10){
		$TMP = DBarray(DBquery("SELECT * FROM Categories WHERE ID=".$TMP['ParentID']));
		$level++;
	}
	
	$level_item=$level;
	
	// ustalamy na jakim levelu znajduje się docelowa kategoria
	$TMP = $CATEGORY;
	$level=0;
	while ($TMP['ParentID']!=0 && $level < 10){
		$TMP = DBarray(DBquery("SELECT * FROM Categories WHERE ID=".$TMP['ParentID']));
		$level++;
	}
	$level_cat=$level;
	
	// Sprawdzamy czy zachodzi hierarchia kategorii
	if ($level_item != ($level_cat+1)) die(json_encode(array('result'=>'ERROR', msg=>'LEVEL MISMATCH')));
	
	DBquery("UPDATE Categories SET ParentID=$MOVE_TO WHERE ID=$MENU_ITEM");
	
	die(json_encode(array('result'=>'SUCCESS')));


}

if (isset($_POST['CHANGE_MENU_TITLE'])){
	$CAT_ID = $_POST['CAT_ID'];
	$NewTitle = $_POST['NEW_TITLE'];
	
	$SQL = dbarray(DBquery("SELECT * FROM Categories WHERE ID = $CAT_ID"));
	if (empty($SQL)) die(json_encode(array('result'=>'ERROR', 'msg'=>'Category dosen\'t exist')));
	
	$SQL = DBquery("UPDATE Categories SET Name='$NewTitle' WHERE ID=$CAT_ID");
	die(json_encode(array('result'=>'SUCCESS')));
	
	
}

if (isset($_POST['DELETE_CATEGORIES'])){
	$CAT_IDs = $_POST['CAT_ID'];
	foreach ($CAT_IDs as $item) $item = intval($item);
	$sql_line = implode(' OR ID=', $CAT_IDs);
	
	
	$SQL = DBquery("DELETE FROM Categories WHERE ID=$sql_line");
	if ($SQL==false) die(json_encode(array('result'=>'ERROR', 'msg'=>'Problem with DB')));
	
	die(json_encode(array('result'=>'SUCCESS')));
	
	
}


// PRZESUWANIE ELEMENTY DO INNEJ KATEGORII
if (isset($_POST['CHANGE_ITEM_CATEGORY'])){
	
	$ITEM_ID = intval($_POST['CHANGE_ITEM_CATEGORY']);
	//$MODULE = htmlspecialchars($_POST['ITEM_MODULE']);
	$MOVE_TO = intval($_POST['NEW_CATEGORY']);
	
	$ITEM = DBarray(DBquery("SELECT * FROM BrowserPosts WHERE ID =".$ITEM_ID));
	
	if (empty($ITEM)){
		StrangeEvent("ITEM ID IS INVALID", "CHANGE_ITEM_CATEGORY", array('POST'=>$_POST, "ITEM"=>$ITEM));
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Item ID invalid')));	
	}
	
	$CATEGORY = DBarray(DBquery("SELECT * FROM Categories WHERE ID =".$ITEM['Category']));
	
	if (empty($CATEGORY)){
		StrangeEvent("CATEGORY IS INVALID", "CHANGE_ITEM_CATEGORY", array('POST'=>$_POST, "ITEM"=>$ITEM, "CATEGORY"=>$CATEGORY));
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Category is invalid')));
	}

	// ustalamy na jakim levelu znajduje się przenoszony item
	$TMP = $CATEGORY;
	$level=0;
	while ($TMP['ParentID']!=0 && $level < 10){
		$TMP = DBarray(DBquery("SELECT * FROM Categories WHERE ID=".$TMP['ParentID']));
		$level++;
	}
	
	if ($level!=3) {
		StrangeEvent("Level is not equal 3", "CHANGE_ITEM_CATEGORY", array('POST'=>$_POST, "LEVEL"=>$level, "CAT"=>$CATEGORY, "TMP"=>$TMP));
		die(json_encode(array('result'=>'ERROR', 'msg'=>'Level not match')));
	}
	
	DBquery("UPDATE BrowserPosts SET Category=$MOVE_TO WHERE ID=$ITEM_ID");
	die(json_encode(array('result'=>'SUCCESS')));
	
	
}

if (isset($_POST['SEND_NEWSTLETTER'])){
	
	$Modules->LoadMailSender();
	$RECIPIENT = isset($_POST['EMAIL_ADDR']) ? htmlspecialchars($_POST['EMAIL_ADDR']) : FALSE; // if this field is empty then send to everyone
	$SUBJECT = htmlspecialchars($_POST['EMAIL_SUBJECT']);
	$MAIL_CONTENT = $_POST['EMAIL_CONTENT'];
	$mail->Subject    = $SUBJECT;
	$mail->msgHTML($MAIL_CONTENT);
	
	if ($RECIPIENT==FALSE){
		set_time_limit(300); // change if defined time is not enough to send all emails
		$sql = DBquery("SELECT * FROM Users WHERE WantsNewsletter=1");
		// save data in a session to check a progress
		$_SESSION['MAIL_TASK_PROGRESS']['TOTAL_TO_SEND'] = $sql->num_rows;
		$_SESSION['MAIL_TASK_PROGRESS']['TIMESTAMP'] = time(NULL);
		$_SESSION['MAIL_TASK_PROGRESS']['CURRENT_ITERATION'] = 0;
		$_SESSION['MAIL_TASK_PROGRESS']['CURRENT_MAIL'] = '';
		
		$i=0;
		session_write_close();
		while($row=DBarray($sql)){
			session_start();
			$mail->addAddress($row['Email'], $row['Nick']);
			$_SESSION['MAIL_TASK_PROGRESS']['CURRENT_MAIL'] = $row['Email'];
			$mail->Send();
			$mail->clearAddresses();
			$_SESSION['MAIL_TASK_PROGRESS']['CURRENT_ITERATION'] = ++$i;
			session_write_close();
//			usleep(200*1000);
		}
		session_start();
		
	}
	else{
		SetSettings("LAST_NEWS_TEST_MAIL", $RECIPIENT);
		$mail->AddAddress($RECIPIENT, "USER");
		$mail->Send();	// wysłanie maila
		
	}
	// delete all info about sending
	$_SESSION['MAIL_TASK_PROGRESS']['TOTAL_TO_SEND'] = 0;
	$_SESSION['MAIL_TASK_PROGRESS']['TIMESTAMP'] = time(NULL);
	$_SESSION['MAIL_TASK_PROGRESS']['CURRENT_ITERATION'] = 0;
	$_SESSION['MAIL_TASK_PROGRESS']['CURRENT_MAIL'] = 'EOF';
	die(json_encode(array('result'=>'SUCCESS')));
	//$mail->clearAddresses();
	
	
}

if (isset($_POST['SENDING_NEWSLETTER_CHECK_PRG'])){
	if ($_SESSION['MAIL_TASK_PROGRESS']['CURRENT_MAIL']!='EOF')
		die(json_encode(array('result'=>'SUCCESS', 'MAIL_TASK'=>$_SESSION['MAIL_TASK_PROGRESS'], 'now'=>time(NULL))));
	else
		die(json_encode(array('result'=>'EOF')));
		
}


if (isset($_POST['LOAD_NEWSLETTER'])){
	$newsltr = intval($_POST['LOAD_NEWSLETTER']);
	
	$sql = DBquery("SELECT * FROM `Newsletters` WHERE ID=$newsltr");
	
	
	if ($sql->num_rows){
		$newsletter = DBarray($sql);
		die(json_encode(array('result'=>'SUCCESS', 'MAIL'=>$newsletter)));
		
	}
		else
			die(json_encode(array('result'=>'ERROR', 'msg'=>'NO_SUCH_ID')));
			
}

if (isset($_POST['SAVE_NEWSLETTER'])){
	$newsltr = intval($_POST['EDITED_ID']);
	$SUBJECT = htmlspecialchars($_POST['SUBJECT']);
	$CONTENT = urldecode($_POST['CONTENT']);
	
	$query = "INSERT INTO `Newsletters`(ID, Title, Content, AuthorID, SendDateTime) VALUES ($newsltr, '$SUBJECT', '$CONTENT', ".$User->ID.", CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE Title='$SUBJECT', Content='$CONTENT', AuthorID=".$User->ID.", SendDateTime=CURRENT_TIMESTAMP";
	$sql = DBquery($query);
	
	if ($sql!=FALSE)
			die(json_encode(array('result'=>'SUCCESS', "INSERTED"=>AffectedRows($sql))));
		else
			die(json_encode(array('result'=>'ERROR', 'msg'=>'QUERY ERROR')));
			
}

if (isset($_POST['DELETE_NEWSLETTER'])){
	$newsltr = intval($_POST['DELETE_NEWSLETTER']);
	
	$sql = DBquery("DELETE FROM `Newsletters` WHERE ID=$newsltr");
	
	if ($sql!=FALSE)
			die(json_encode(array('result'=>'SUCCESS')));
		else
			die(json_encode(array('result'=>'ERROR', 'msg'=>'NO_SUCH_ID')));
			
}

die(json_encode(array("result"=>'NO-DATA')));

