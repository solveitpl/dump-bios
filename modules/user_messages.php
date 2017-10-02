<?php
/* Plik ten definiuje klase oraz funkcje do komnikacji portal->Użytkownik */

class oMsg {
	public  $Time;			// czas wystąpienia, jednocześnie stanowi klucz wiadomości
	public  $Type;			// Typ wiadomości BAD, GOOD, WARNING, INFO
	public  $Msg;			// treść wiadomości
	
	function __construct($_Msg, $_Type) {
		$this->Type = $_Type;
		$this->Msg =  $_Msg;
		$this->Time = microtime(NULL);
	}
}



function AddToMsgList($Msg, $Type="INFO")
{
	$NewMsg = new oMsg($Msg, $Type);
	array_push($_SESSION['MSG_LIST'], $NewMsg);
}


?>