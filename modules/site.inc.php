<?php
// definiowane dla warstwy PHP
define("SITES","sites/");
define("FORMS","forms/");
define("MODULES","modules/");

// kupowanie plików
define("BUY_ERROR", -2);
define("NOT_ENOUGH_POINTS",-1);
define("IN_USER_STOCK",0);
define("PURCHASED", 1);

// definiowane dla warstwy htnl
define("IMAGES",BDIR."images/");
// klasa do zbierania wpisów do Debugu
class oDebug {
	public $Msg;
	public $Time;
	public $Source;

	function __construct($Source, $Time, $Msg) {
		$this->Msg = $Msg;
		$this->Time = $Time;
		$this->Source = $Source;
	}

}

/*
 * Komuniakty użytkownika do systemu
 */

class oSysInfo {
	public $Title;
	public $Msg;
	public $link;
	public $Time;
	public $Source;
	public $ElPointer;
	public $Icon;
	public $MsgID;
	public $Checked;

	function __construct($Title,$Msg, $link='', $Source='', $Pointer=0, $Time='', $Icon='default') {
		$this->Msg = $Msg;
		$this->link = $link;
		$this->Title = $Title;
		$this->Time = time(NULL);
		$this->Source = $Source;
		$this->Icon = $Icon;
	}
	
	function RenderListElement()
	{
		switch ($this->Icon)
		{
			case 'default':
				$iconSrc = IMAGES.'info2.png';
				break;
			
			case 'ICON_GOOD':
				$iconSrc = IMAGES.'point_up.png';
				break;
			
			case 'ICON_BAD':
				$iconSrc = IMAGES.'point_down.png';
				break;
						
				
			default:
				if (file_exists($this->Icon))
					$iconSrc = $this->Icon;
				else 
					$iconSrc = IMAGES.'info2.png';
		}
		
		$arg = '';
		if (!empty($this->link)) $arg='action="Navigate" arg="'.$this->link.'"';
		?>
		<div class='notify_msg_element' module='' <?= $arg?> internal="<?= $this->MsgID ?>" check="<?= $this->Checked ?>">
			<div class='info_div'></div>
			<div class='img_div'><img src='<?= $iconSrc ?>'></div>
			<div class='content_div'>
				<div class="msg_user_name"><?= $this->Title ?></div>
				<div class="msg_text"><?= $this->Msg ?></div>
			</div>
		</div>
		<?php 
	}
	
	public static function WithArray($arr){
		$Instance = new self($arr['Title'], $arr['Message'], $arr['Link'], $arr['Module'], $arr['ModulePointer'], $arr['SendTime'], $arr['Icon']);
		$Instance->MsgID = $arr['ID'];
		$Instance->Checked = $arr['Checked'];
		return $Instance;
	}
	
		

}

// Renderowanie wszystkich wiadomości systemowych
function RenderSysInfo($SysInfo){
	for ($i=0; $i<count($SysInfo); $i++)
		$SysInfo[$i]->RenderListElement();
	
}

function NotifyError($MSG){
	global $User;
	DBquery("INSERT INTO PortalError(`ID`,`MODULE`,`MSG`,`Date`,`User`) VALUES(NULL, 'DOWNLOAD', '.$MSG.', ".time(NULL).", ".$User->ID().")");
}






?>