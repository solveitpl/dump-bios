<?php
define("FILE_REJECTED", -10);
define("FILE_NEW", 0);
define("FILE_ACCEPTED", 1);
define("FILE_VERIFIED", 2);

$STATUS_LABEL = array(FILE_REJECTED=>'REJECTED', FILE_NEW=>'NEW', FILE_ACCEPTED=>'ACCEPTED', FILE_VERIFIED=>'VERIFIED');



// paginacja
define("FILES_PAGINATION",10);
define("FILES_PAGINATION_LINK_SPACING",2);


/*
 * Sprawdzenie czy użytkownik nie posiada czasem nie ocenionych plików. 
 */




class oDFile{
	public $ID;
	public $Module;
	public $FileDesc;
	public $FileDescExt;
	public $RealFileName;
	public $FilePath;
	public $Category;
	public $OS;
	public $OSver='_nie_';
	public $Manufacturer;
	public $License;
	public $UploadedTime;
	public $UploaderID;
	public $UploaderNick;
	public $Status;
	public $DownloadedCount;
	public $PointsGood;
	public $PointsBad;
	public $PointCount;
	public $PointsCost;
	public $InUserStock=FALSE;
	public $UserVoted=FALSE;
	function __construct($sql_arr=''){
		$this->SQLFill($sql_arr);
		
	}
	
	public static function withID($_ID){
		$instance = new self();
		$ID = intval($_ID);
		global $User;
		
		if ($ID==0) {
			AddToMsgList("Błąd wewnętrzny","BAD");
		}
		
		$sql_arr = dbarray(DBquery("SELECT UploadedFile.*, t_Points.InUserStock, t_Votes.UserVoted FROM `UploadedFile` 
							LEFT JOIN (SELECT ElementID, UserID AS InUserStock, Points FROM Points WHERE UserID=".$User->ID().") AS t_Points ON t_Points.ElementID=UploadedFile.ID 
							LEFT JOIN (SELECT Points AS UserVoted, FileID FROM FilesPoints WHERE UserID=".$User->ID().") AS t_Votes ON UploadedFile.ID=t_Votes.FileID
							WHERE UploadedFile.ID=$ID"));
		
		$instance->SQLFill($sql_arr);
		return $instance;
	}
	
	public static function _blank(){
		$instance = new self();
		$instance->ID = 0;
		$instance->OS = array();
		return $instance;
	}
	
	function SQLFill($sql_arr){
		
		if (!is_array($sql_arr)) return;	// jeżeli nie przekazono tablicy tworzymy pustą klasę
		
		if (isset($sql_arr['ID'])) $this->ID = intval($sql_arr['ID']);
		if (isset($sql_arr['MODULE'])) $this->Module = $sql_arr['MODULE'];
		if (isset($sql_arr['FileDesc'])) $this->FileDesc = $sql_arr['FileDesc'];
		if (isset($sql_arr['RealFileName'])) $this->RealFileName = $sql_arr['RealFileName'];
		if (isset($sql_arr['ServerFilePath'])) $this->FilePath = $sql_arr['ServerFilePath'];
		if (isset($sql_arr['Category'])) $this->Category = $sql_arr['Category'];
		if (isset($sql_arr['OS'])) $this->OS = explode(';',$sql_arr['OS']);
		

		if (isset($sql_arr['OSBit'])) {
			$this->OSver = $sql_arr['OSBit'];
			
		}
		
		
		if (isset($sql_arr['Manufacturer'])) $this->Manufacturer = $sql_arr['Manufacturer'];
		if (isset($sql_arr['License'])) $this->License = $sql_arr['License'];
		if (isset($sql_arr['UploaderID'])) $this->UploaderID = $sql_arr['UploaderID'];
		if (isset($sql_arr['UploaderNick'])) $this->UploaderNick = $sql_arr['UploaderNick'];
		
		if (isset($sql_arr['FileDescExt'])) $this->FileDescExt = $sql_arr['FileDescExt'];
		if (isset($sql_arr['FileUploaded'])) $this->UploadedTime = $sql_arr['FileUploaded'];
		if (isset($sql_arr['Status'])) $this->Status = $sql_arr['Status'];
		if (isset($sql_arr['DownloadCount'])) $this->DownloadedCount = $sql_arr['DownloadCount'];
		if (isset($sql_arr['PointsGood'])) $this->PointsGood = $sql_arr['PointsGood'];
		if (isset($sql_arr['PointsBad'])) $this->PointsBad = $sql_arr['PointsBad'];
		
		if (isset($sql_arr['PointCount'])) $this->PointCount = $sql_arr['PointCount'];
		$this->PointsBad = intval($this->PointsBad);
		$this->PointsGood = intval($this->PointsGood);
		
		$this->PointCount = intval($this->PointCount);
		
		if (isset($sql_arr['PointsCost'])) $this->PointsCost = $sql_arr['PointsCost'];
		if (isset($sql_arr['InUserStock'])) $this->InUserStock = $sql_arr['InUserStock'];
		if (isset($sql_arr['UserVoted'])) $this->UserVoted = $sql_arr['UserVoted'];
	
		if (($this->ID==0))
		{
			AddToMsgList("Błąd wewnętrzny","BAD");
			unset($this);
		}
		
	}
	
	function ID(){return $this->ID;}
	function GetModule(){return $this->Module;}
	function FileDesc(){return $this->FileDesc;}
	function GetDesc(){return $this->FileDescExt;}
	function GetRealFileName(){return $this->RealFileName;}
	function GetPath(){return $this->FilePath;}
	function GetCategory(){return $this->Category;}
	function OS(){return $this->OS;}
	function GetManufacturer(){return $this->Manufacturer;}
	function Licence(){return $this->License;}
	function UploaderID(){return $this->UploaderID;}
	function Status(){return $this->Status;}
	function DownloadedCount(){return $this->DownloadedCount;}
	function UploaderNick(){return $this->UploaderNick;}
	function UploadedTime(){return $this->UploadedTime;}
	function PointsGood(){return $this->PointsGood;}
	function PointsBad(){return $this->PointsBad;}
	function PointCount(){return $this->PointCount;}
	function PointsCost(){return $this->PointsCost;}
	function InUserStock(){return $this->InUserStock||IsAdmin();}
	function UserVoted(){return $this->UserVoted;}
	
	
	function IncCount(){
		DBquery("UPDATE UploadedFile SET `DownloadCount`=`DownloadCount`+1 WHERE ID=".$this->ID());
	}
	
	function Delete(){
		
		if (file_exists($this->FilePath.'/'.$this->RealFileName))
		unlink($this->FilePath.'/'.$this->RealFileName);
		$sql = DBquery("DELETE FROM Points WHERE ElementID=".$this->ID." AND MODULE='SOFTWARE'");
		$sql = DBquery("DELETE FROM FilesPoints WHERE FileID=".$this->ID());
		$sql = DBquery("DELETE FROM UploadedFile WHERE ID=".$this->ID());
		return 0;
	}
	
	function UpdateDB(){
		$sql = DBquery("UPDATE UploadedFile SET FileDesc='".$this->FileDesc."', FileDescExt='".$this->FileDescExt."',
				OS='".implode(';',$this->OS)."', OSBit='".$this->OSver."', Status=".$this->Status.", License='".$this->License."' WHERE ID='".$this->ID."' ");
	}
	
	function Buy()
	{
		global $User;
		if (!IsLogin())	
		{
			StrangeEvent("Nie wiadomo jak ale anonim chciał kupić plik", "BUY_FILE");
			return BUY_ERROR;
		}
		
		if (!IsAuth())
		{
			StrangeEvent("NIeutoryzowany uzykownik chciał kupić plik", "BUY_FILE", array($User, $_SESSION,  $_SERVER));
			return BUY_ERROR;
		}
		
		if (IsAdmin()) return IN_USER_STOCK;
		
		if ($this->InUserStock()) return IN_USER_STOCK; // jeśli użytkownik już kupił ten plik;

		if ($this->PointsCost > $User->Points->TotalPoints()) return NOT_ENOUGH_POINTS;
		
		$Price = $User->Points->CheckCredit($this->PointsCost);
		$sql = $sql2 = TRUE;
		if ($Price['Regular']) // jeśli pobieramy coś z codziennego przydziału
			$sql = DBquery("INSERT INTO Points (`ID`, `UserID`, `Points`, `EntryDate`, `ElementID`, `Source`, `Comment`, `MODULE`, `Cash`)
					VALUES (NULL, ".$User->ID().", ".$Price['Regular']*(-1).", CURRENT_TIMESTAMP(),".$this->ID().",".DOWNLOAD_POINTS.",'Zakup w ramach codziennego przydziału', 'SOFTWARE', ".NORMAL_POINT.")");
		if ($Price['Points']) // jeśli pobieramy coś z codziennego przydziału
			$sql2 = DBquery("INSERT INTO Points (`ID`, `UserID`, `Points`, `EntryDate`, `ElementID`, `Source`, `Comment`, `MODULE`, `Cash`)
					VALUES (NULL, ".$User->ID().", ".$Price['Points']*(-1).", CURRENT_TIMESTAMP(),".$this->ID().",".DOWNLOAD_POINTS.",'Zakup w ramach codziennego przydziału', 'SOFTWARE', ".PRIVATE_POINT.")");
			
			
		if ($sql||$sql2) return PURCHASED;
		
		
	}
	

}
?>