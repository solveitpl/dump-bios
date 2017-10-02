<?php

/*
 * Zaznaczanie odczytania wiadomosci
 */
if (isset($_POST['ACK_SYSINFO'])){
	$MsgID = intval($_POST['ACK_SYSINFO']);

	if (!IsLogin()) die();
	$sql = DBquery("UPDATE MessagesSys SET Checked=1 WHERE ID=$MsgID AND UserID=".$User->ID());
	die(json_encode(array("result"=>"SUCCESS")));
}

/*
 * Sprawdzenie czy występują nowe wiadomości
 */
elseif (isset($_POST['CHECK_NEW_MESSAGES'])){
	if (!IsLogin()) _die("Źle...");

	$msgs = array();
	$msg_pointer = htmlspecialchars($_POST['CHECK_NEW_MESSAGES']);
	if ($msg_pointer=='') die(json_encode(array("result"=>"ERROR", "msg"=>"Zły wskaźnik", "msg_pointer"=>$msg_pointer)));

	$sql=DBquery("SELECT
					IF(UserID=".$User->ID().", RecipientID, UserID) AS HimID,
					DateOF,
					Content,
					Readed,
					Users.Nick,
					Users.ID AS client_id
				FROM Messages
				INNER JOIN Users ON Messages.UserID=Users.ID
				WHERE
					RecipientID=".$User->ID()."
					AND Readed=0
					ORDER BY DateOF");

	if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"Błąd wewnętrzny", "counter"=>($sql->num_rows), "msg_pointer"=>$msg_pointer)));

	while($row=DBarray($sql))
		array_push($msgs, $row);


		die(json_encode(array(
				"result"=>"GOT_MESSAGE",
				"msgs"=>$msgs,
				"msg_count" => count($msgs),
				"format_add_time"=>date("Y-m-d h:n:s",time(NULL))


		)));

}

/*
 * Oznaczanie wiadomośći jako przeczytanej
 */
elseif (isset($_POST['MARK_MSG_AS_READED'])){
	if (!IsLogin()) _die("Źle...");
	$UserID = intval($_POST['MARK_MSG_AS_READED']);
	$sql = DBquery("UPDATE Messages SET Readed=1 WHERE UserID=$UserID");

	if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR")));

	die(json_encode(array("result"=>"SUCCES")));

}


// jeśli nie odnaleziono żadnego zapytania zwracamy NO-DATA
die(json_encode(array("result"=>"NO-DATA")));

?>