<?php
/*
 #########################################
 #########################################
 ############## matrix.php ###############
 #########################################
 # Główny plik silnika systemu, zawiera  #
 # podstawowe funkcje najniższej warstwy #
 # mechanizmy, includuje potrzebne pliki #
 #########################################
 */
define('KEY', 'SOMEKEY123SOMEKEY1234567');

define("BDIR", "http://dump.all4it.pl/");

global $ARG;
// Obróbka tablicy z argumetnami
define("ARG_START", 1);
$ARG = explode('/', $_SERVER['REQUEST_URI']);
array_splice($ARG, 0, ARG_START);

define("MAINTANACE",FALSE);
date_default_timezone_set('Europe/Warsaw');
include "modules/site.inc.php";

global $SITE;
global $DATA; // zmienna zawiera wybrane dane z linku które są przepisywane z tablicy $ARG
global $_SYSTEM;
global $User;
global $InhibitedARG;

// Czas generowania strony w microsekundach
$_SYSTEM['time_of_start'] = microtime();

// zakazane elementy linku
$InhibitedARG  = array('page','link','_edit', 'item');
$DATA = array();
AnalyzeARG(); // sprawdza czy w linku nie ma danych potrzebnych dla tablicy DATA

// CSS content
putenv("CSS=");
$_ENV['CSS']='';

// JS content
putenv("JS=");
$_ENV['JS']='';

// Tablica plików ładowanch przy początku strony
putenv("HTML=");
$_ENV['HTML']=array();

// Strumień Debug dl aprogramistów
global $Debug;	
$Debug = array();
// Tablica SysInfo. Lista wiadomości dla użytkownika
global $SysInfo;
$SysInfo = array();

// Dodawanie do LOGU
function AddToLog($Source,$Msg){
	global $Debug;
	global $_SYSTEM;
	array_push($Debug, new oDebug($Source, round(microtime() - $_SYSTEM['time_of_start'],6, TRUE)*10^6, $Msg));
	
}

function AddSysInfo($Title, $Msg, $link='', $Module=''){
	global $SysInfo;
	array_push($SysInfo, new oSysInfo($Title, $Msg, $link, $Module));
}


/* Pobiera zawartość pliku */
function GetFileContent($filename, $MOD_NAME=''){
	$Content = '';
	
	if (file_exists($filename))
	{
		$file_size = filesize($filename);
		if ($file_size>0)
		{	
			$Content='';
			if (!($MOD_NAME==''))
				$Content = "\n\n/* $MOD_NAME */\n";
			$Content .= fread(fopen($filename, "r"), $file_size);
			AddToLog("GetFileContent()", "Załadowanie pliku $filename");
		}
	}
	else AddToLog("GetFileContent()", "Plik $filename nie istnieje");
		 
	
	//return trim(preg_replace('/\s+/', ' ', $Content));
	return $Content;
}

/* funkcja die rozszerzona o DEBUG */
function _die($str, $file=""){
	echo "<script>ShowDialogBox('$str'); </script>";
	include SITES.'DieScreen.php';
	die();
}

function utf8_convert($string)
{

			$tekst=iconv(mb_detect_encoding($string),"UTF-8",$string);

            return $tekst;
}

function MakeLink($str)
{	
	$search = array(" ","ą", "ć", "ę", "ł", "ń", "ó", "ś", "ż", "ź",".",",",'#','*','=','[',']','!','/','&','%');
	$rep	= array("_","a", "c", "e", "l", "n", "o", "s", "z", "z","","",'_','_','_','_','_','_','_','_','_');
	return str_replace($search,$rep,$str);
}

function MakeItShort($str, $count=100){
	if (strlen($str)>$count)
		return substr($str,0,strpos($str, " ", $count))." (...)";
	else return $str;
}


function Encrypt($string)
{
	return  base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(KEY), $string, MCRYPT_MODE_CBC, md5(md5(KEY))));
}

function Decrypt($string)
{
	return $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(KEY), base64_decode($string), MCRYPT_MODE_CBC, md5(md5(KEY))), "\0");
}

function _file_exists($file)
{
	$file_headers = @get_headers($file);
	if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		$exists = false;
	}
	else {
		$exists = true;
	}
	
	return $exists;
}

function StrangeEvent($MSG, $MODULE='', $DUMP_DATA=''){
	global $User;
	
	DBquery("INSERT INTO StrangeEvents (`ID`, `MODULE`, `MSG`, `Date`, `User`, `new`, `DUMP_DATA`, `IP_ADDR`)
			VALUES(NULL, '$MODULE', '".$MSG."', UNIX_TIMESTAMP(), ".$User->ID.", 1, '".json_encode($DUMP_DATA)."', '".$_SERVER['REMOTE_ADDR']."')");
	return '';
}

function GetCategoryTree($CategoryID){
	$parentID = $CategoryID;
	$CategoryTree = array();
	$ReqCounter = 0;
	do 	{
		$row = dbarray(dbquery("SELECT * FROM Categories WHERE Id=$parentID"));
		if (empty($row)) return -1;
			
		array_push($CategoryTree, $row);
		$parentID=$row['ParentID'];
		$ReqCounter++;
		if ($ReqCounter>3) return 0;
			
	} while($parentID<>0);
	if ($ReqCounter!=3)	return 0;

	return $CategoryTree;
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

/*
 * Dobieranie ikony na podstawie rozszerzenia pliku
 */
function GetFileIcon($filename, $TO_STREAM=FALSE,$opt=''){
	$extensions = array('exe', 'pdf', 'deb', 'jpg', 'raw', 'tiff', 'dwg', 'bin', 'rom');
	$ext = pathinfo(strtolower($filename), PATHINFO_EXTENSION);
	if (in_array($ext, $extensions))
		 $result =  "<img src='".BDIR."images/icon/$ext.png' $opt>";
	else $result =  "<img src='".BDIR."images/icon/file.png' $opt>";
	
	if ($TO_STREAM==TRUE) return $result;
	else echo $result;
	
}

/*
 * Dobieranie ikony na podstawie rozszerzenia pliku
 */
function GetIconPath($filename){
	$extensions = array('exe', 'pdf', 'deb', 'jpg', 'raw', 'tiff', 'dwg', 'bin', 'rom');
	$ext = pathinfo(strtolower($filename), PATHINFO_EXTENSION);
	
	if (file_exists("images/icon/$ext.png"))
		return "images/icon/$ext.png";
	
	
	
	if (in_array($ext, $extensions))
		$result =  "<img src='".BDIR."images/icon/$ext.png' $opt>";
		else $result =  "<img src='".BDIR."images/icon/file.png' $opt>";

		if ($TO_STREAM==TRUE) return $result;
		else echo $result;

}

/*
 * Tworzy obraz z różnych typów
 */

function imagecreatefromfile( $filename ) {
	if (!file_exists($filename)) {
		throw new InvalidArgumentException('File "'.$filename.'" not found.');
	}
	switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
		case 'jpeg':
		case 'jpg':
			return imagecreatefromjpeg($filename);
			break;

		case 'png':
			return imagecreatefrompng($filename);
			break;

		case 'gif':
			return imagecreatefromgif($filename);
			break;
			
		case 'pdf':
			$x = new imagick($filename.'[0]');
			$x->setimageformat('png');
			$x = imagecreatefromstring($x);
			return $x;

		default:
			throw new InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
			break;
	}
}

/*
 * Przeźroczystość obrazka
 */

function imagesetopacity( $imageSrc, $opacity )
{
	$width  = imagesx( $imageSrc );
	$height = imagesy( $imageSrc );

	// Duplicate image and convert to TrueColor
	$imageDst = imagecreatetruecolor( $width, $height );
	imagealphablending( $imageDst, false );
	imagefill( $imageDst, 0, 0, imagecolortransparent( $imageDst ) );
	imagecopy( $imageDst, $imageSrc, 0, 0, 0, 0, $width, $height );

	// Set new opacity to each pixel
	for ( $x = 0; $x < $width; ++$x )
		for ( $y = 0; $y < $height; ++$y ) {
			$color = imagecolorat( $imageDst, $x, $y );
			$alpha = 127 - ( ( $color >> 24 ) & 0xFF );
			if ( $alpha > 0 ) {
				$color = ( $color & 0xFFFFFF ) | ( (int)round( 127 - $alpha * $opacity ) << 24 );
				imagesetpixel( $imageDst, $x, $y, $color );
			}
		}

	return $imageDst;
}

/*
 * Zatrzymanie dla uploadu plików
 */

function uStop($str, $error=51)
{
	die('{"jsonrpc" : "2.0", "error" : {"code": '.$error.', "message": "'.$str.'"}, "id" : "id"}');

}

/*
 * Dodawanie pozycji do tablicy na żądane miejsce
 *
 */

function array_insert(&$array, $position, $insert)
{
	if (is_int($position)) {
		array_splice($array, $position, 0, $insert);
	} else {
		$pos   = array_search($position, array_keys($array));
		$array = array_merge(
				array_slice($array, 0, $pos),
				$insert,
				array_slice($array, $pos)
				);
	}
}

function CheckMarker($ToCheck, $Decode=TRUE, $time = 3600){
	if ($Decode) $ToCheck = Decrypt($ToCheck);
	$StampTime = intval($ToCheck);
	if (time(NULL)-$StampTime > $time)
		return false;
	else 
		return true;
}

function FilenameWithSuffix($filename, $suffix){
	$pathInfo = pathinfo($filename);
	return $pathInfo['filename'].$suffix.".".$pathInfo['extension'];
}

function FilenameNewExt($filename, $extension){
	$pathInfo = pathinfo($filename);
	return $pathInfo['filename'].$extension;
}

// ładniejsza forma print_r
function print_pretty($array, $TO_STREAM=false){
	if ($TO_STREAM=TRUE) return "<pre>".json_encode($array, JSON_PRETTY_PRINT)."</pre>";
	else
	echo "<pre>".json_encode($array, JSON_PRETTY_PRINT)."</pre>";
}

function AnalyzeARG(){
	global $ARG;
	global $InhibitedARG;
	global $DATA;
	
	foreach ($InhibitedARG as $c_arr)
	{	
		$index = array_search($c_arr, $ARG);
		if ($index)
		{
			if (isset($ARG[$index+1]))
			{
				$DATA[$c_arr]  = $ARG[$index+1];
				unset($ARG[$index+1]);
			}
			else
				$DATA[$c_arr] = 0;
			
			unset($ARG[$index]);
			array_values($ARG);
		}
	}
	
	
	
	
}

/*
 * Render linku paginacji
 */
function RenderPaginationLink($page,$Link='',$ActivePage=0){

	// jeśli $page jest równy 0 oznacza to zwyły przerywniki
	if (!$page)
		return "<div class='space_dot'>...</div>";

		$active='';
		if ($ActivePage==$page) $active='active';
		return "<a href='".$Link."page/$page'><div class='$active'>".$page."</div></a>";

}

/**
 * Ładowanie ustawień
 */




?>
