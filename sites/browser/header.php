<?php
define("POST_REJECTED", -1);
define("POST_NEW", 0);
define("POST_ACCEPTED", 1);
define("POST_VERIFIED", 2);

$STATUS_LABEL = array(POST_REJECTED => 'REJECTED', POST_NEW=> 'NEW', POST_ACCEPTED=>'ACCEPTED', POST_VERIFIED=>'VERIFIED');

if (!isset($_SESSION['BROWSER_SORT'])) {
	$_SESSION['BROWSER_SORT']='LAST_ADDED';
	$_SESSION['BROWSER_ORDER'] = 'SendTime DESC';

}

// USTAWIENIA MINITATUR
define("IMG_THUMB_HEIGHT",100);
define("IMG_PREVIEW_HEIGHT",450);

// ilość postów na stronę
define("POST_PAGINATION",5);
define("POSTS_PAGINATION_LINK_SPACING",2);

require_once MODULES."image_processing.php";



/*height
 * Definija dozwolonych typów plików dla danego działu
 */
global $DIV_SETTINGS;
$DIV_SETTINGS = array(
	'images' => array(
		'file_type' => ".jpg,.jpeg,.png,.gif,.tiff,.raw",
		'action' => 'view'
	),
	
	'schematics' => array(
		'file_type' => ".pdf",
		'action' => 'view'
	),
		
	'boardview' => array(
		'file_type' => ".dwg,.dxf,.zip",
		'action' => 'download'
	),
		
	'bios' => array(
		'file_type' => ".bin,.rom",
		'action' => 'download'
			
		),
	'kbc-ec' => array(				
		'file_type' => ".bin,.rom",
		'action' => 'download'
	)

		
	);

// zwraca ustawienia działu w postaci tablicy
function ExplodeDivAttr($div, $attr){
	global $DIV_SETTINGS;
	return explode(',', $DIV_SETTINGS[$div][$attr]);
}

/*
 * Ilość postów w zależności od kategorii i modułu
 */
function GetPostCount($Category, $Module='ALL')
{
	if (!IsAdmin())
		$STATUS_LINE = " AND Status > ".POST_NEW;
	else $STATUS_LINE = '';
	
	if ($Module!='ALL')
		$MODULE_LINE = " AND MODULE='$Module'";
	
	$sql = dbarray(DBquery("SELECT COUNT(*) AS post_quantity FROM `BrowserPosts`
		WHERE Category = $Category $MODULE_LINE $STATUS_LINE
		"));

	return $sql['post_quantity'];
}



/*
 * DEFINICJA KLAS
 */


class oPPoints{
	public $Good;
	public $Bad;

}

class oPFile{
	public $Filename ='';
	public $Path='';
	public $ThumbDir='';
	public $Miniature='';
	public $Preview='';
	public $Title='';
	public $Mode='VIEW';
	public  $PostID;
	
	function __construct($file, $path, oPost $parent){
		
		$pathifo = pathinfo($file);
		if (!file_exists($path."/".$file)){
			//throw new Exception( "File not exist. Aborting");
			
			return -1;
		}
		
		global $DIV_SETTINGS;
		$this->Filename = $file;	
		$this->Path = $path;
		
		if ($this->Path[strlen($this->Path)-1]!='/')
			$this->Path .= '/';
		$_Parent = $parent->ID;
		
		
		// określanie typu pliku
		
		$extension = ".".strtolower($pathifo['extension']);
		//print_pretty(ExplodeDivAttr('images','file_type'));
		
		// jeśli to plik graficzny
		if ( in_array($extension, ExplodeDivAttr('images', 'file_type')) || in_array($extension, ExplodeDivAttr('schematics', 'file_type')))
		{
			$this->Mode = 'VIEW';
			// katalog miniatur	
			$this->ThumbDir = 'upload/thumb/'.$parent->Category.'/'.$parent->Module.'/';
			if (!file_exists($this->ThumbDir))
				mkdir($this->ThumbDir, 0777, true);
			chmod($this->ThumbDir, 0777);
			
			// opracowanie nazw dla miniatur
			$this->Miniature = FilenameNewExt($this->Filename, "_min.png");
			if (!file_exists($this->ThumbDir.$this->Miniature))
			{
				
				CreateSmall($this->Path.$this->Filename, $this->ThumbDir.$this->Miniature, IMG_THUMB_HEIGHT);
				chmod($this->ThumbDir.$this->Miniature, 0755);
			}
			
			$this->Preview = FilenameNewExt($this->Filename, "_med.png");
			if (!file_exists($this->ThumbDir.$this->Preview)) {
				CreateSmall($this->Path.$this->Filename, $this->ThumbDir.$this->Preview, IMG_PREVIEW_HEIGHT);
				chmod($this->ThumbDir.$this->Miniature, 0755);
			}
		}
		else
		{
			$this->Mode = 'DOWNLOAD';
				
			$ext = pathinfo(strtolower($this->Filename), PATHINFO_EXTENSION);
			$this->ThumbDir = "images/icon/";
			if (file_exists("images/icon/$ext.png"))
				$this->Miniature = "$ext.png";
			else
				$this->Miniature = "file.png";
			
			$this->Title = pathinfo($this->Filename, PATHINFO_FILENAME);
			
			
		
			
		}
		
		$this->Mode = 'DOWNLOAD';
		
		
		
	}
	
	
	function DelFile($PostID=0){
		switch($this->Mode){
			case 'VIEW':
				if (file_exists($this->ThumbDir.$this->Miniature))
					unlink($this->ThumbDir.$this->Miniature);
				
				if (file_exists($this->ThumbDir.$this->Preview))	
					unlink($this->ThumbDir.$this->Preview);
				
				break;
			case 'DOWNLOAD':
				break;
		}
		
		if (file_exists($this->Path.$this->Filename))
			unlink($this->Path.$this->Filename);
		
		DBquery("DELETE FROM BrowserFiles WHERE RealFileName='".$this->Filename."' AND PostID=".$PostID);
		
	}
}



class oPost{
	public	$ID;
	public	$Title;
	public	$Module;
	public	$Category;
	public	$SendTime;
	public	$Status;
	public	$DownloadCount;
	
	
	public	$Files=array();
	public	$Points;
	public	$Owner;
	
	public	$PointsCost = 0;
	public	$UserVoted=FALSE;
	
	private $InUserStock=FALSE;

	function __construct($sql_arr=''){
		
		if ($sql_arr=='') return 0;
		$this->SQLFill($sql_arr);

	}

	public static function withID($_ID){
		$instance = new self(null);
		global $User;
		$ID = intval($_ID);
		
		
		
		if ($ID==0) {
			AddToMsgList("Błąd wewnętrzny. Brak wskaźnika postu","BAD");
		
		}
		
		
		$UserID = 0;
		if (IsLogin()) $UserID = $User->ID();
		$sql_arr = dbarray(DBquery("SELECT BrowserPosts.*, a.PointsGood, b.PointsBad, t_Points.InUserStock, t_Votes.UserVoted, Users.Nick AS UploaderNick, t_files.Files, t_files.Paths FROM `BrowserPosts`
							LEFT JOIN Users ON Users.ID = BrowserPosts.UserID
							LEFT JOIN (SELECT PostID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM BrowserPoints WHERE Points>0 GROUP BY PostID) as a ON BrowserPosts.ID=a.PostID 
							LEFT JOIN (SELECT PostID, SUM(Points) AS PointsBad FROM BrowserPoints WHERE Points<0 GROUP BY PostID) as b ON BrowserPosts.ID=b.PostID 
							LEFT JOIN (SELECT ElementID, UserID AS InUserStock, Points FROM Points WHERE UserID=".$UserID.") AS t_Points ON t_Points.ElementID=BrowserPosts.ID 
							LEFT JOIN (SELECT Points AS UserVoted, PostID FROM BrowserPoints WHERE UserID=".$UserID.") AS t_Votes ON BrowserPosts.ID=t_Votes.PostID
							LEFT JOIN (SELECT GROUP_CONCAT(RealFileName SEPARATOR '|') AS Files , GROUP_CONCAT(ServerFilePath SEPARATOR '|') AS Paths , PostID FROM BrowserFiles GROUP BY PostID) as t_files ON t_files.PostID = BrowserPosts.ID
							WHERE BrowserPosts.ID=$ID"));
		$instance->SQLFill($sql_arr);
		return $instance;
	}

	function SQLFill($sql_arr){

		if (!is_array($sql_arr)) return;	// jeżeli nie przekazono tablicy tworzymy pustą klasę

		if (isset($sql_arr['ID'])) $this->ID = intval($sql_arr['ID']);
		if (isset($sql_arr['MODULE'])) $this->Module = $sql_arr['MODULE'];
		if (isset($sql_arr['Title'])) $this->Title = $sql_arr['Title'];
		if (isset($sql_arr['Category'])) $this->Category = $sql_arr['Category'];
		
		$this->Owner = oUser::CreateBlank();
		if (isset($sql_arr['UserID'])) $this->Owner->ID = $sql_arr['UserID'];
		if (isset($sql_arr['UploaderNick'])) $this->Owner->Nick = $sql_arr['UploaderNick'];

		if (isset($sql_arr['FileUploaded'])) $this->UploadedTime = $sql_arr['FileUploaded'];
		if (isset($sql_arr['Status'])) $this->Status = $sql_arr['Status'];
		if (isset($sql_arr['DownloadCount'])) $this->DownloadCount = $sql_arr['DownloadCount'];
		if (isset($sql_arr['SendTime'])) $this->SendTime = $sql_arr['SendTime'];
		
		if (isset($sql_arr['Files'])){
		
			$f_arr = explode('|', $sql_arr['Files']);
			$f_path = explode('|', $sql_arr['Paths']);
			for ($i=0; $i<count($f_arr); $i++){
				$file = new oPFile($f_arr[$i], $f_path[$i], $this);
				if (!($file->Filename==''))
					array_push($this->Files, $file);
				unset($file);
			}
		
		}

		
		$this->Points  = new oPPoints();
		if (isset($sql_arr['PointsGood'])) $this->Points->Good = $sql_arr['PointsGood'];
		if (isset($sql_arr['PointsBad'])) $this->Points->Bad = $sql_arr['PointsBad'];
	//	if (isset($sql_arr['PointCount'])) $this->PointCount = $sql_arr['PointCount'];
		$this->Points->Bad = intval($this->Points->Bad);
		$this->Points->Good = intval($this->Points->Good);
	//	$this->PointCount = intval($this->PointCount);
		

		if (isset($sql_arr['PointsCost'])) $this->PointsCost = $sql_arr['PointsCost'];
		if (isset($sql_arr['InUserStock'])) $this->InUserStock = $sql_arr['InUserStock'];
		if (isset($sql_arr['UserVoted'])) $this->UserVoted = $sql_arr['UserVoted'];

		if ( ($this->ID==0) || ($this->Module=='') || ($this->Category==0))
		{
			AddToMsgList("Błąd wewnętrzny","BAD");
			unset($this);
		}
		


	}
	
	function GetFileByName($name){
		for ($i=0; $i<count($this->Files); $i++)
			if ($this->Files[$i]->Filename==$name)
				return $this->Files[$i];
		
				
		return false;
	}

	
	function InUserStock(){
		global $User;
		return $this->InUserStock||IsAdmin()||$User->CheckID($this->Owner->ID());
	}
	

	function IncCount(){
		DBquery("UPDATE BrowserPosts SET `DownloadCount`=`DownloadCount`+1 WHERE ID=".$this->ID);
	}
	
	function UpadateProp($Field, $Value){
		$sql = DBquery("UPDATE BrowserPosts SET `$Field`='$Value' WHERE ID=".$this->ID);
		return $sql;
	}


	function Buy()
	{
		global $User;
		if (!IsLogin())
		{
			StrangeEvent("Nie wiadomo jak ale anonim chciał kupić plik", "BUY_FILE", array($User, $_SESSION, $_SERVER));
			return BUY_ERROR;
		}

		if (IsAdmin()) return IN_USER_STOCK;

		if ($this->InUserStock()) return IN_USER_STOCK; // jeśli użytkownik już kupił ten plik;

		if ($this->PointsCost > $User->Points->TotalPoints()) return NOT_ENOUGH_POINTS;

		$Price = $User->Points->CheckCredit($this->PointsCost);
		$sql = $sql2 = TRUE;
		if ($Price['Regular']) // jeśli pobieramy coś z codziennego przydziału
			$sql = DBquery("INSERT INTO Points (`ID`, `UserID`, `Points`, `EntryDate`, `ElementID`, `Source`, `Comment`, `MODULE`, `Cash`)
					VALUES (NULL, ".$User->ID().", ".$Price['Regular']*(-1).", CURRENT_TIMESTAMP(),".$this->ID.",".BROWSER_POINTS.",'Buy post content', '".$this->Module."', ".NORMAL_POINT.")");
		
		if ($Price['Points']) // jeśli pobieramy coś z prywatnych punktów użytkownika
			$sql2 = DBquery("INSERT INTO Points (`ID`, `UserID`, `Points`, `EntryDate`, `ElementID`, `Source`, `Comment`, `MODULE`, `Cash`)
					VALUES (NULL, ".$User->ID().", ".$Price['Points']*(-1).", CURRENT_TIMESTAMP(),".$this->ID.",".BROWSER_POINTS.",'Buy post content', '".$this->Module."',".PRIVATE_POINT.")");
					
					
				if ($sql||$sql2) return PURCHASED;


	}


}

?>