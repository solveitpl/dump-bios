<?php
/*
 * Zarządzanie reklamami
 * Klasy i funkcje z tym związane
 * 
 */




// Status reklamy
define("AD_NOT_ACTIVE",0);
define("AD_ACTIVE",1);
define("AD_EXPIRED",2);
define("AD_PENDING",3);

$STATUS_LABEL = array(AD_NOT_ACTIVE=>'Not active', AD_ACTIVE=>'Active', AD_EXPIRED=>'Expired', AD_PENDING=>"Pending");


// TRYBY REKLAMY
define("DISPLAY_LIMITED",0);
define("TIME_LIMITED",1);
define("MONTHLY_SUB",2);

$MODE_LABEL = array(DISPLAY_LIMITED=>'Display limited', TIME_LIMITED=>'Time limited', MONTHLY_SUB=>'Monthly subscription');




class oAd{
	public  $ID;
	public  $ImagePath;
	public	$ImageSX;
	public	$ImageSY;
	public 	$Displayed=0;
	public	$DisplayLimit;
	public 	$Clicked;
	public	$Description;
	public 	$AddTime;
	public	$ExpiredTime;
	public  $Mode=0;
	public	$Dimensions=0;
	public	$Status;
	public	$Link='';
	public	$Advertiser;
	public	$LastDisplayed;
	
	function __construct($sql_arr=''){
		$this->SQLFill($sql_arr);
		

	}

	public static function withID($_ID){
	
		$instance = new self();
		$ID = intval($_ID);
		
		if ($ID==0) {
			$instance = new self();
			$instance->ID = 0;
			$instance->Status = 0;
			$instance->Mode = 0;
			$instance->ExpiredTime = time(NULL);
			return $instance;
			AddToLog("Returned empty structure","AD_HEADER");
				
		}

		$sql_arr = dbarray(DBquery("SELECT * FROM SiteAds WHERE ID=$ID"));

		$instance->SQLFill($sql_arr);
		
		$instance->Clicked = intval($instance->Clicked);
		
		return $instance;
	}

	public static function _blank(){
		$instance = new self();
		$instance->ID = 0;
		$instance->Status = 0;
		$instance->Mode = 0;
		$instance->ExpiredTime = time(NULL);
		return $instance;
	}

	function SQLFill($sql_arr){

		if (!is_array($sql_arr)) return;	// jeżeli nie przekazono tablicy tworzymy pustą klasę

		if (isset($sql_arr['ID'])) $this->ID = intval($sql_arr['ID']);
		if (isset($sql_arr['ImagePath'])) $this->ImagePath = htmlspecialchars($sql_arr['ImagePath']);
		if (isset($sql_arr['Dimensions'])) $this->Dimensions = htmlspecialchars($sql_arr['Dimensions']);
		if (isset($sql_arr['Displayed'])) $this->Displayed = intval($sql_arr['Displayed']);
		if (isset($sql_arr['DisplayLimit'])) $this->DisplayLimit = intval($sql_arr['DisplayLimit']);
		if (isset($sql_arr['Clicked'])) $this->Clicked = intval($sql_arr['Clicked']);
		if (isset($sql_arr['Description'])) $this->Description = htmlspecialchars($sql_arr['Description']);
		if (isset($sql_arr['AddTime'])) $this->AddTime = intval($sql_arr['AddTime']);
		if (isset($sql_arr['ExpiredTime'])) $this->ExpiredTime = intval($sql_arr['ExpiredTime']);
		if (isset($sql_arr['Mode'])) $this->Mode = intval($sql_arr['Mode']);
		if (isset($sql_arr['Status'])) $this->Status = intval($sql_arr['Status']);
		if (isset($sql_arr['Link'])) $this->Link = htmlspecialchars($sql_arr['Link']);
		if (isset($sql_arr['Advertiser'])) $this->Advertiser = oUser::withID($sql_arr['Advertiser']);
		if (isset($sql_arr['LastDisplayed'])) $this->LastDisplayed = $sql_arr['LastDisplayed'];
		
		if (($this->ImagePath!='') && (file_exists($this->ImagePath)) && is_file($this->ImagePath) && exif_imagetype($this->ImagePath)) // jeśli jest przypisant jakiś plik
		{
				$im = imagecreatefromfile($this->ImagePath);
				// Set the margins for the stamp and get the height/width of the stamp image
				$this->ImageSX = imagesx($im);
				$this->ImageSY = imagesy($im);
			
		}

		if (($this->ID==0))
		{
			AddToMsgList("INTERNAL_ERROR #2","BAD");
			AddToLog("Bad ID","AD_HEADER");
			unset($this);
		}
		

	}
	
	function GetImgDim(){
		if (($this->ImagePath!='') && (file_exists($this->ImagePath)) && is_file($this->ImagePath) && exif_imagetype($this->ImagePath)) // jeśli jest przypisant jakiś plik
		{
			$im = imagecreatefromfile($this->ImagePath);
			// Set the margins for the stamp and get the height/width of the stamp image
			$this->ImageSX = imagesx($im);
			$this->ImageSY = imagesy($im);
				
		}
		
	}
	
	function isExpired(){
		switch ($this->Mode){
			case DISPLAY_LIMITED:
				return ($this->Displayed > $this->DisplayLimit);
				break;
				
			case TIME_LIMITED:
				return ($this->ExpiredTime < time(NULL));
				
				break;
				
			case MONTHLY_SUB:
				return true;
				break;
		}
	}
	
	function CheckDim($x,$y){
		return (($this->ImageSX==$x) && ($this->ImageSY==$y));
	}
	
	function InternalLink(){
		return BDIR."ads_go/".$this->ID."/".(time(NULL)*$this->ID+pow($this->ID,3));
	}


	function IncValue($Display=TRUE, $Click=FALSE){
		if (!($Display||$Click)) return 0; // coś musi być TRUE
		
		$VALUES = '`ID`=`ID`'; // bezpiecznik;
		if ($Display) $VALUES = '`Displayed`=`Displayed`+1';
		elseif ($Click) $VALUES = '`Clicked`=`Clicked`+1';
		
		DBquery("UPDATE SiteAds SET $VALUES WHERE ID=".$this->ID);
	}

	function Delete(){
		return 0;
	}
	

	function UpdateDB(){
		$sql = DBquery("UPDATE SiteAds SET ImagePath='".$this->ImagePath."', Displayed=".$this->Displayed.",
				Clicked=".$this->Clicked.", Description='".$this->Description."', ExpiredTime='".$this->ExpiredTime."',
				Mode=".$this->Mode.", Status=".$this->Status.", Link='".$this->Link."', LastDisplayed=".$this->LastDisplayed." WHERE ID='".$this->ID."' ");
	}


}

function GetAd($x, $y){
	$sql = dbarray(dbquery("SELECT * FROM SiteAds WHERE Dimensions='100x500' AND Status=".AD_ACTIVE." ORDER BY LastDisplayed ASC LIMIT 1"));
	$Ad = new oAd($sql);
	$Ad->Displayed++;
	$Ad->LastDisplayed = time(NULL);

	switch($Ad->Mode){
		case DISPLAY_LIMITED:
			if ($Ad->Displayed > $Ad->DisplayLimit) $Ad->Status = AD_EXPIRED;
			break;
		case TIME_LIMITED:
			if ($Ad->ExpiredTime < time(NULL)) $Ad->Status = AD_EXPIRED;
			break;
	}

	$Ad->UpdateDB();

	return $Ad;

}


?>