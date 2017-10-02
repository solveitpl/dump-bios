<?php

function FriendlyErrorType($type)
{
	switch($type)
	{
		case E_ERROR: // 1 //
			return 'E_ERROR';
		case E_WARNING: // 2 //
			return 'E_WARNING';
		case E_PARSE: // 4 //
			return 'E_PARSE';
		case E_NOTICE: // 8 //
			return 'E_NOTICE';
		case E_CORE_ERROR: // 16 //
			return 'E_CORE_ERROR';
		case E_CORE_WARNING: // 32 //
			return 'E_CORE_WARNING';
		case E_COMPILE_ERROR: // 64 //
			return 'E_COMPILE_ERROR';
		case E_COMPILE_WARNING: // 128 //
			return 'E_COMPILE_WARNING';
		case E_USER_ERROR: // 256 //
			return 'E_USER_ERROR';
		case E_USER_WARNING: // 512 //
			return 'E_USER_WARNING';
		case E_USER_NOTICE: // 1024 //
			return 'E_USER_NOTICE';
		case E_STRICT: // 2048 //
			return 'E_STRICT';
		case E_RECOVERABLE_ERROR: // 4096 //
			return 'E_RECOVERABLE_ERROR';
		case E_DEPRECATED: // 8192 //
			return 'E_DEPRECATED';
		case E_USER_DEPRECATED: // 16384 //
			return 'E_USER_DEPRECATED';
	}
	return "";
}

class oErrorHandler
{
	var $code;
	var $message;
	var $file;
	var $line;
	var $DATA;
	function setError($number, $string, $file, $line)
	{
		$this->code = $number;
		$this->message = $string;
		$this->file = $file;
		$this->line = $line;
	//	$this->showError();
		$this->PutToDB();
	}
	
	function showError()
	{
		print 'Bład ('.$this->code.'): '.$this->message."<br />".$this->file.' ('.$this->line.')<br />';
	}
	
	function PutToDB(){
		global $User;
		global $link;
		if (IsLogin()) $UserID = $User->ID; else $UserID = 0;
		$sql = DBquery("INSERT INTO `PHPErrorsLogs`
				(`id`, `date_of`, `string`, `file`, `line`, `code`, `new`, `DUMP_DATA`, `UserID`)
				VALUES (NULL,CURRENT_TIMESTAMP,'".mysqli_real_escape_string($link, $this->message)."','".$this->file."',
				'".$this->line."','".$this->code."',1,'".$this->DATA."',$UserID)");
	}


}


// Przekierowywujemy wszystkie błędy do naszej obsługi
$errorHandler = new oErrorHandler();
set_error_handler(array(&$errorHandler, 'setError'));

?>
