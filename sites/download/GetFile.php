<?php
include "header.php";

if (!isset($_POST['key'])) die();

$time = $_POST['key'];
$ControlFileName = Decrypt($_POST['key2']);
$FormTime = time(NULL) - Decrypt($time); // czas od wysłania formularza
if ($FormTime>2000) 
{
	AddToMsgList("Too much time for action. Try again.");

}
else
{	
	$File = oDFile::withID(intval($_POST['FileID']));
	
	if ($File->GetRealFileName()!=$ControlFileName) // jeśli zakodowana nazwa pliku niezgadza się z żądaną to mamy podejrzaną sprawę.
	{
		StrangeEvent("Bad name of file", "GETFILE");
		AddToMsgList("Authorize error. It will be report to Admin. We apologize for any inconvenience..");
		header("Location: ".BDIR);
	}
	
	$attachment_location =  $File->GetPath().'/'.$File->GetRealFileName();
	
	switch($File->Buy()){ 	// Kupuj. Funkcja sprawdza czy plik jest już w zasobach.
		
		case NOT_ENOUGH_POINTS: _die("You dont have enough points"); break;
		case BUY_ERROR: _die("ACCESS_DENIED"); break;
		
	}
	$File->IncCount();
	
	// pobranie Marki i modelu w celu nadania nazwy pliku
	$FileCategory = DBarray(DBquery("SELECT Categories.Name AS Model, Parent.Name AS Producer FROM Categories INNER JOIN Categories AS Parent ON Parent.ID=Categories.ParentID WHERE Categories.ID=".$File->GetCategory()));
	//print_r($File);
	
	if (file_exists($attachment_location))
	{
		$sLink = "./upload/files/DUMP_BIOS".$FileCategory['Producer']."_".$FileCategory['Model']."_".$File->GetRealFileName();

		symlink("../../".$attachment_location,$sLink);
		touch($sLink);
		//echo $sLink;
		header('Location: '.$sLink);
		
	}
	else
	{
		AddToMsgList("File do not exists... It will be report to administrator", "BAD");
		StrangeEvent("Użytkownik odkrył, że plik nie istnieje... ID PLIKU: ".$File->ID(), "SOFTWARE");
		header("Location: ".BDIR);
		
	//	$sLink = "./upload/files/".$FileCategory['Producer']."_".$FileCategory['Model']."_".$File->GetRealFileName();
	//	symlink("../../".$attachment_location,$sLink);
	//	echo "2Link:".$sLink;
	//	touch($sLink);
		/*header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="'.BDIR. $sLink.'"');
		header("Content-Type: application/force-download");
		header("Content-Length: " . filesize($sLink));
		header("Connection: close");
		*/
		//echo "Content-Disposition: attachment; filename=\"".BDIR. $attachment_location . "\"";
	}	
}
?>