<?php
include "header.php";
if ( (!isset($_GET['File'])) ) die();
$PostID = intval($_GET['Post']);
$FileName = htmlspecialchars($_GET['File']);
if (isset($_GET['Mode']))
	$Mode = $_GET['Mode']; // MIN || NORMAL || BIN
else $Mode = 'MIN';

if ($FileName=='package') $DOWNLOAD_ALL = TRUE;
else $DOWNLOAD_ALL = FALSE;
	
//$sql = DBarray(DBquery("SELECT * FROM BrowserFiles WHERE ID=".$FileID));
$Post = oPost::withID($PostID);


switch ($Mode){
	case "NORMAL": // wyświetl w normalnej wielkości
		$File = $Post->GetFileByName($FileName);
		header('Content-Type: image/png');
		$im = CreateSmall($File->Path.$File->Filename, '', IMG_PREVIEW_HEIGHT);
				
		// Copy the stamp image onto our photo using the margin offsets and the photo
		// width to calculate positioning of the stamp.
		imagepng($im);
		imagedestroy($im);
		break;
		
	case "BUY":
		$Buy = $Post->Buy();
		if (($Buy!=IN_USER_STOCK) && ($Buy!=PURCHASED)) _die("Nope.");
	
		switch ($DOWNLOAD_ALL){
			case TRUE: // w przypadku pobierania całego pakietu
				$ZipFileName = MakeLink("DUMP_BIOS_".$Post->Title).".zip";
				if (file_exists("./upload/tmp/".$User->UserNick()."/".$ZipFileName)){
					unlink("./upload/tmp/".$User->UserNick()."/".$ZipFileName);
				}
				
				$zip = new ZipArchive();
				
				if ($zip->open("./upload/tmp/".$User->UserNick()."/".$ZipFileName, ZipArchive::CREATE)!==TRUE) {
					exit("Cannot open <$ZipFileName\n");
				}
				
				foreach ($Post->Files as $FileEl){
					$zip->addFile($FileEl->Path.'/'.$FileEl->Filename, $FileEl->Filename);
					
				}
				
				$zip->close();
				
				header("Content-Transfer-Encoding: Binary");
				header("Content-disposition: attachment; filename=\"" .$ZipFileName."\"");
				readfile("./upload/tmp/".$User->UserNick()."/".$ZipFileName);
				break;
				
			case FALSE: // w przypadku pobierania pojedyńczego pliku 
				$File = $Post->GetFileByName($FileName);
				$pathinfo = pathinfo($File->Filename);
				switch($pathinfo['extension'])
				{
					CASE 'pdf': // plik typu pdf
						header('Content-Type: application/pdf');
						break;
				
					default:
						header('Content-Type: image/png');
				
				}
				
				header("Content-Transfer-Encoding: Binary");
				header("Content-disposition: attachment; filename=\"" .$File->Filename. "\"");
				readfile($File->Path."/".$File->Filename);
				break;
		}
		
	
		
		dbquery("UPDATE BrowserPosts SET `DownloadCount`=`DownloadCount`+1 WHERE ID=".$Post->ID);
		break;
		
	default:
		$File = $Post->GetFileByName($FileName);
		header('Content-Type: image/png');
		$im = CreateSmall($File->Path.$File->Filename, '', IMG_THUMB_HEIGHT);
				
		// Copy the stamp image onto our photo using the margin offsets and the photo
		// width to calculate positioning of the stamp.
		imagepng($im);
		imagedestroy($im);
		break;

}

die();
?>