<?php

// Stałe błędów logowania
define("BAD_LOGIN_DATA", "Bad login or password");
define("USER_DISABLE", "Account is suspended");
define("TOO_MANY_TRIES", "Too many authorize tries. Wait a minute.");
define("IP_BLOCKED", "IP BANNED");

// Stałe Statusu użytkownika;
define("USER_BANNED", -1);		// użytkownik zablokowany
define("USER_NOT_CONFIRMD", 0); // użytkownik nie potwierdził rejestracji email
define("USER_NOT_ACTIVATED",1); // użytkownik nieaktywowany przez ADMINA
define("USER_ACTIVE",2);		// użytkownik w pełni potwierdzony

$USER_STATUS = array(-1=>"Zablokowany",0=>'Niezweryfikowany', 1=>'Nieaktywowany', 2=>'Aktywny');

// Stałe typów uprawnień
define("GUEST", 0);
define("USER", 5);
define("MODERATOR", 10);
define("ADMIN", 15);

// DEFINICJA STAŁYCH POLA `Source` DLA PRZYDZIELENIA PUNKTÓW
define("DAY_POINTS",0);
define("DOWNLOAD_POINTS",1);
define("BROWSER_POINTS",2);
define("DONATE_POINTS",10);
define("ADMIN_POINTS",11);

// Stałe opisują punkty z codziennego przydziału lub z zarabionych punktów użytkownika
define('NORMAL_POINT',0);
define('PRIVATE_POINT',1);





class oPoints{
	private $UserID;
	public $RegularPoints;
	public $MainPoints;
	public $EarnPoints;
	public $SpendPoints;
	public $NwstrUser;
	
	function __construct($ID, $UserType){
		$this->UserID = $ID;
		$this->NwstrUser = $UserType;
	
	}
	function RegularPoints() {return $this->RegularPoints;}
	function MainPoints() {return $this->MainPoints;}
	function TotalPoints() {return ($this->RegularPoints+$this->MainPoints);}
	function DetailPoints(){
		$sql3 = DBarray(
				DBquery("SELECT * FROM
							(SELECT SUM(Points) AS EarnPoints, UserID FROM Points WHERE Points > 0 AND UserID=".$this->UserID.") AS t1 INNER JOIN
							(SELECT SUM(Points) AS SpendPoints, UserID FROM Points WHERE Points < 0 AND UserID=".$this->UserID.") AS t2
							")
				);
		
		$this->EarnPoints = abs(intval($sql3['EarnPoints']));
		$this->SpendPoints = abs(intval($sql3['SpendPoints']));
		
	}
	
	function AddPoint($Points, $Source, $Element, $Comment='', $Cash=1){
		$sql = DBquery("INSERT INTO Points (`ID`, `UserID`, `Points`, `EntryDate`, `ElementID`, `Source`, `Comment`, `MODULE`, `Cash`)
						VALUES(NULL, ".$this->UserID.", $Points, CURRENT_TIMESTAMP, $Element, $Source, '$Comment', '', $Cash);			
				");
		if ($sql==false)
		{
		NotifyError("User didn't get his points ($Points, $Source, $Element). MySQL error.");
		}
		
		return $sql;
	}
	
	function GetTotalPoint() {
		global $_SETTINGS;
		/*
		 * Source
		 * 0 - codzienny przydział
		 * 1 - Plik
		 * 2 - BrowserPoints
		 * 
		 * 10 - Donate
		 * 11 - przydzielone przez Admina
		 */
		$sql = dbarray(DBquery("SELECT SUM(Points) AS Points, UserID FROM Points WHERE Cash=".PRIVATE_POINT." AND UserID=".$this->UserID));
		$sql2 = DBarray(DBquery("SELECT SUM(Points) AS RegularPoints, UserID FROM Points WHERE Cash=".NORMAL_POINT." AND `EntryDate` >= CURDATE() AND UserID=".$this->UserID));
	
		
		
		
		if (!empty($sql))
			$this->MainPoints = intval($sql['Points']);
		else
			$this->MainPoints = 0;
		
		//AddToMsgList("Ueee ".$this->NwstrUser);
		if ($this->NwstrUser) $DAY_PER_DAY = $_SETTINGS['NORMAL_POINT_PLUS']; else $DAY_PER_DAY = $_SETTINGS['NORMAL_POINT'];
		//AddToMsgList($_SETTINGS['NORMAL_POINT']." ".$_SETTINGS['NORMAL_POINT_PLUS']." ".$DAY_PER_DAY);
		$this->RegularPoints = $DAY_PER_DAY+intval($sql2['RegularPoints']);
		
	
	}
	
	
	function CheckCredit($cost){
		if ($cost>($this->TotalPoints())) return NOT_ENOUGH_POINTS;
	
		$RegularPointCost = $this->RegularPoints >= $cost ? $cost : $this->RegularPoints;
		$Points = $cost==$RegularPointCost ? 0 : $cost-$RegularPointCost;
		
		return array("Regular"=>$RegularPointCost, "Points"=>$Points);
	}
	
	
}




class oUser {
	public $ID;			// ID 
	public $Nick;			// Nick
	public $Email;			// login, Email
	public $Status;		// Status użytkownika.
	public $PermLevel;		// Poziom uprawnień;
	public $PermArray;		// Tablica ze specyfikacją uprawnień
	
	// Dane portalowe
	public $NewsLetter;
	public $Birthday;
	public $City;
	public $Country;
	public $LastVisit;
	public $RegisterDate;
	public $LastIP;
	public $Points;
	
	public $Avatar;
	public $Marker; // zakodowane IP i php_session_id
	
	function __construct($sql_data=''){
		if (is_array($sql_data))
			$this->SqlFill($sql_data);
	}
	
	public static function loginTry($login_, $password_) { // Konstruktor. Wywołany przy próbie logowania
		$instance = new self();
		if (($login_=='')&&($password_=='')) // jeśli nie ma podanych danych, tworzymy konto gościa
		{
			$instance->Nick = 'Guest';
			$instance->ID = 0;
			$instance->PermLevel = 0;
			$_SESSION['USER'] = $instance;
			unset($_SESSION['IS_LOGIN']);
			return $instance;
		}
		$login = addslashes($login_);
		if (!(filter_var($login, FILTER_VALIDATE_EMAIL))) 
		{
			AddToMsgList("User name is your e-amil adress!" , "BAD");
		}
		
		$password = md5($password_);
		
		$request = DBarray(DBquery("SELECT * FROM Users WHERE Email='$login' AND Password='$password'"));
		
		if ($request==FALSE) 
			{
			AddToMsgList("User doesnt exist" , "BAD");
			return BAD_LOGIN_DATA;
			}
		
		$SecureMarker = sha1(session_id().$_SERVER['REMOTE_ADDR']); // tworzymy marker który posłuży jako zabezpieczenie przed przejęciem sesji

		/* Logowanie poprawne. Przypisujemy wartości */
		$instance->SqlFill($request);
		//define("DOWNLOAD_PER_DAY", GetSettings('DOWNLOAD_PER_DAY'));
	
		// ładujemy marker i IP do DB
		DBquery("UPDATE Users SET LastIP='".$_SERVER['REMOTE_ADDR']."', `LastLoginTime`=CURRENT_TIMESTAMP(), Marker='$SecureMarker' WHERE ID=".$instance->ID);
		
		$instance->Points->GetTotalPoint();
		$_SESSION['USER'] = $instance; 
		$_SESSION['IS_LOGIN'] = TRUE;
		
		/* Jeśli status nie pozwala na pełne korzystanie ze strony, wyświetl stosowny komunikat */
		switch ($instance->Status){
			case USER_BANNED: 
				AddToMsgList("Your account was blocked, You cant use all site !", "BAD");
				break;
			case USER_NOT_CONFIRMD:
				AddToMsgList("You didnt confirm your account at e-mail. You cant use all site.", "WARNING");
				break;
			case USER_NOT_ACTIVATED:
				AddToMsgList("Your account is waiting for active by Admin. You cant use all site. Please, be patient:)", "INFO");
				
		}
		return $instance;
		//Przeładowanie serwisu
		header("Location: ".BDIR);
	}

	function UserNick() 		{ return ($this->Nick);}
	function ID() 				{ return ($this->ID); }
	function CheckID($cID)		{ return intval($this->ID==$cID);}
	function Perm() 			{ return ($this->PermLevel); }
	function CheckPerm($Perm) 	{ return intval($Perm<=($this->PermLevel)); }
	function CheckStatus($STATUS=USER_ACTIVE)		{ return ($this->Status==$STATUS);}
	function Email()			{ return ($this->Email); }
	function City()				{ return ($this->City); }
	function Country()			{ return ($this->Country); }
	function BirthDay()			{ return ($this->Birthday); }
	function LastVisit()		{ return ($this->LastVisit);}
	function RegisterDate()		{ return ($this->RegisterDate);}
	function Newsletter()		{ return ($this->NewsLetter); }
	function Status()			{ return ($this->Status);	}
	function LogOut()			{ unset($this); }
	
	
	
	
	// jeśli nie jest zalogowany to tworzymy wydmuszkę klasy
	public static function CreateBlank(){		
		$instance = new self();
		$instance->Nick = 'Guest';
		$instance->PermLevel = 0;

		return $instance;
		
	}
	
	function ReloadData() {
		if (!($this->ID)) return -1;
		$UserData = dbarray(DBquery("SELECT * FROM Users WHERE ID=".$this->ID));
		
		// Jeśli nie ma takiego użytkownika w bazie -> przerywamy sesję
		if (empty($UserData)) 
			{
			_die("No User ID", "LOGIN");
			session_destroy();
			}
		
		$this->SqlFill($UserData);
		$this->Points->GetTotalPoint();
		$_SESSION['USER'] = $this;
	}
	
	function SqlFill($request){
		$this->ID = $request['ID'];
		$this->Nick = $request['Nick'];
		$this->Email = $request['Email'];
		$this->Status = $request['Status'];
		$this->PermLevel = $request['PermLevel'];
		$this->PermArray = $request['PermArray'];
		$this->RegisterDate = $request['RegisterTime'];
		$this->LastVisit = $request['LastLoginTime'];
		$this->LastIP = $request['LastIP'];
		
		$this->Birthday = $request['Birthday'];
		$this->City = $request['City'];
		$this->Country = $request['Country'];
		$this->NewsLetter = $request['WantsNewsletter'];
		$this->Marker = $request['Marker'];
		$this->Avatar = $request['Avatar'];
		$this->Points = new oPoints($this->ID, $this->NewsLetter);
	
	}
	
	public static function withName($user_name){
		$sql = dbarray(dbquery("SELECT * FROM Users WHERE Nick='$user_name'"));
		if (empty($sql)) return BAD_LOGIN_DATA;
		
		
		
		$instance = new self();
		$instance->SqlFill($sql);
		
		return $instance;
		
		
	}
	
	public static function withID($UserID){
		$sql = dbarray(dbquery("SELECT * FROM Users WHERE ID='$UserID'"));
		if (empty($sql)) return BAD_LOGIN_DATA;
	
	
	
		$instance = new self();
		$instance->SqlFill($sql);
	
		return $instance;
	
	
	}

	// Ustawianie avatara
	function SetAvatar($src){
		$sql = DBquery("UPDATE Users SET Avatar='$src' WHERE ID=".$this->ID);
		
		return $sql;
	}
	
	// Wysyłanie wiadomości. Informacje pojawi się w zakładce "Wiadomości Systemowe"
	function SendNotify($MsgTxt, $Title, $Link='', $Icon="default", $Module='', $ModulePointer=0) { 
		$MsgTxt = MakeItShort($MsgTxt, 200);
		
		$sql = DBquery("INSERT INTO MessagesSys (`ID`, `UserID`, `Title`, `Message`, `Module`, `ModulePointer`, `Icon`, `SendTime`, Link)
									VALUES(NULL, ".$this->ID().", '$Title', '$MsgTxt', '$Module', $ModulePointer, '$Icon', ".time(NULL).", '$Link')");
		
		return $sql;
		
	}
	
	
	
}

/* Funkcja do sprawdzenia czy użytkownik jest zalogowany */
function IsLogin(){
	return isset($_SESSION['IS_LOGIN']);
}



function IsAdmin(){
	global $User;
	return $User->CheckPerm(ADMIN);
}

function IsMod(){
	global $User;
	return $User->CheckPerm(MODERATOR);
}

function IsUser(){
	global $User;
	return $User->CheckPerm(USER);
}

function IsAuth($Status=USER_ACTIVE){
	global $User;
	return $User->CheckStatus($Status);
}




?>
