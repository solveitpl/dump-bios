<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!IsLogin()){
	StrangeEvent("Użytkownik niezalogowany próbował załadować plik", "UPLOAD", array($User, $_SESSION, $_SERVER));
	uStop("Błędny strumień wejścia",3);
}


if (!IsAuth()){
	StrangeEvent("Użytkownik nie uprawniony próbował załadować plik", "UPLOAD", array($User, $_SESSION, $_SERVER));
	uStop("Błędny strumień wejścia",3);
}

@set_time_limit(5 * 60);


$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds

//sleep(5);

// jeśli nie ma indexu 'upload' tylko 'files'
if (isset($_FILES['file'])) $_FILES['upload'] = $_FILES['file'];

// Get a file name
if (isset($_REQUEST["name"])) {
	$fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
	$fileName = $_FILES["upload"]["name"];
} else {
	$fileName = uniqid("file_");
}


$file_key = $fileName;

if (!(isset($_POST['division']))) uStop("Błędne wejście strumienia...", 53);
$DIVISION = htmlspecialchars($_POST['division']);

switch ($DIVISION){
	case "DOWNLOAD":
		if (!isset($_SESSION['_FILE'][$file_key]))
			{
			// ustalanie ścieżki i zapisywanie jej do sesji. Pozostałość po starszej wersji
			if (isset($_POST['selCategory']))
			{
				$CategoryID = intval($_POST['selCategory']);
				$CategoryINFO = DBarray(DBquery("SELECT Categories.*, parent.Name AS parent_name FROM Categories INNER JOIN Categories as parent ON parent.ID=Categories.ParentID WHERE Categories.Id=$CategoryID"));
				$Module = htmlspecialchars($_POST['selModule']);
				$parentID = 1;
				$CategoryTree = array();
				$ReqCounter=0;
				$parentID = $CategoryID;
				do 	{
					$row = dbarray(dbquery("SELECT * FROM Categories WHERE Id=$parentID"));
					if (empty($row)) uStop("Błędne drzewo kategorii $CategoryID");
					array_push($CategoryTree, $row);
					$parentID=$row['ParentID'];
					$ReqCounter++;
					if ($ReqCounter>3)	uStop("Błędne drzewo kategorii", 52);
				} while($parentID<>0);
				if ($ReqCounter!=3)	uStop("Błędne drzewo kategorii", 52);
				$targetDir = 'upload/'.$CategoryTree[2]['Name'].'/'.$CategoryTree[1]['Name'].'/'.$CategoryTree[0]['Name'].'/'.$Module;
					
			}
			else // standardowe działanie
			{
				$CategoryID = 0;
				$Module = '';
				$targetDir = 'upload/UP_FILES/SOFTWARE/'.$User->UserNick();
				
			}
			unset($_SESSION['_FILE']);
			
				
			}
		break;
		
	case "Ad":
		//$marker = intval(Decrypt($_POST['marker']));
		
		$path_p = pathinfo($fileName);
		$targetDir = 'upload/tmp/'.$User->UserNick();
		$filename_md5 = md5($fileName).".".$path_p['extension'];
		break;
		
	case "ArticleIMG":
		$CategoryID = intval(Decrypt($_POST['Category']));
		$Module = htmlspecialchars(Decrypt($_POST['Module']));
		if ((!$CategoryID) &&  ($Module==''))
		{
			$targetDir = 'upload/UP_FILES/Articles';
			$CategoryID = 0;
			$Module = '';
		}
		else {
			if (!$CategoryID) uStop('Błędna kategoria...', 54);
			if ($Module=='') uStop('Błędny moduł...', 55);
			print_r($_POST);
			$CategoryTree = GetCategoryTree($CategoryID);
			$targetDir = 'upload/'.$CategoryTree[2]['Name'].'/'.$CategoryTree[1]['Name'].'/'.$CategoryTree[0]['Name'].'/'.$DIVISION;
		}
		
		break;
	case "BrowserFile":
		$CategoryID = intval($_POST['category']);
		$Module = htmlspecialchars($_POST['module']);
		$marker = intval(Decrypt($_POST['marker']));
		if ((time(NULL)-$marker)>2000) uStop("TOKEN_EXPIRED",100);		
		if (!$CategoryID) uStop('Błędna kategoria...', 54);
		if ($Module=='') uStop('Błędny moduł...', 55);
		$CategoryTree = GetCategoryTree($CategoryID);
		$targetDir = 'upload/tmp/'.$User->UserNick();
		//$targetDir = 'upload/'.$CategoryTree[2]['Name'].'/'.$CategoryTree[1]['Name'].'/'.$CategoryTree[0]['Name'].'/'.$Module;
		
	break;
	default: 
		uStop("Błędne wejście...", 53);
		
	
}

//uStop("Pluskwa $targetDir");
// Create target dir
if (!file_exists($targetDir)) {
	mkdir($targetDir, 0777, true);
	chmod($targetDir, 0777);
}

//uStop('Kontrolowane zatrzymanie '.$targetDir);
$old_fileName=$fileName;
$path_p = pathinfo($fileName);
$exists_counter=1;
$filePath = $targetDir . DIRECTORY_SEPARATOR . MakeLink($path_p['filename']).'.'.$path_p['extension'];




while (file_exists($filePath))
{
	$fileName=MakeLink($path_p['filename']).'_'.$exists_counter.'.'.$path_p['extension'];
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
	$exists_counter++;
}



// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


// Remove old temp files	
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}	


// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES["upload"]["error"] || !is_uploaded_file($_FILES["upload"]["tmp_name"])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["upload"]["tmp_name"], "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen("php://input", "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

$result = 'INTERNAL_ERROR';
$msg = '';
// Jeśli upload się zakończył
if (!$chunks || $chunk == $chunks - 1) {
	// zmiana nazwy 
	rename("{$filePath}.part", $filePath);
	chmod($filePath, 0755);
	$inserted_id = 0;
	$url ='';
	// Dodajemy do bazy danych
	switch ($DIVISION){
		case "DOWNLOAD":
		// Jeśli jest to dział DOWNLOAD
			if (strtolower($path_p['extension'])!='zip') // jeśli plik nie jest już archiwum
				{
				// kompresja pliku do zip
				$zip = new ZipArchive();
				//$ZipFileName = MakeLink($CategoryINFO['parent_name'].'_'.$CategoryINFO['Name'].'_'.$Module.'_'.$path_p['filename']).'_'.$exists_counter.".zip";
				$ZipFileName = MakeLink($path_p['filename']).'_'.$exists_counter.".zip";
						
				if ($zip->open($targetDir.DIRECTORY_SEPARATOR.$ZipFileName, ZipArchive::CREATE)!==TRUE) {
					exit("cannot open <$ZipFileName\n");
				}
				$zip->addFile($filePath, $fileName);
				$zip->close();
				unlink($filePath);
				}
			else $ZipFileName = $fileName;
			
			$FileDesc = htmlspecialchars(strip_tags($_POST['FileDesc']));
			$FileLicense = htmlspecialchars(strip_tags($_POST['FileLicense']));
			
			if (isset($_POST['OS']))
				$FileOS = htmlspecialchars(implode(',', $_POST['OS']));
			else 
				$FileOS = '';
			
			$FileDescEX = htmlspecialchars($_POST['FileDescExt']);		
			
			$sql = DBquery("INSERT INTO UploadedFile (`ID`, `FileDesc`, `FileDescExt`, `License`, `OS`,`RealFileName`, `ServerFilePath`, 
							`Category`, `UploaderID`, `FileUploaded`, `Status`, `MODULE`, `OSBit`)
							VALUES (NULL, '$FileDesc', '$FileDescEX', '$FileLicense', '$FileOS', '$ZipFileName', '$targetDir', $CategoryID, ".$User->ID().",
									".time(NULL).",0,'".strtoupper($Module)."', '')");
			
					
				

				
			
			if ($sql==false) uStop("Błąd wewnętrzny: 3306");
			unset($_SESSION['_FILE'][$file_key]);
			$inserted_id = dblastid();
			
			$fileName=$ZipFileName;
			
			$result = 'SUCCESS';
		break;
		
		case "ArticleIMG":
			// dodawanie znaku wodnego do obrazka
			// Load the stamp and the photo to apply the watermark to
			$stamp = imagecreatefrompng(BDIR.'images/watermark.png');
			$stamp = imagesetopacity($stamp, 0.5);
			
			$im = imagecreatefromfile($filePath);
			// Set the margins for the stamp and get the height/width of the stamp image
			$marge_right = 10;
			$marge_bottom = 10;
			$stamp = imagescale($stamp, imagesx($im)*0.12);			
			$sx = imagesx($stamp);		
			$sy = imagesy($stamp);
			
			// Copy the stamp image onto our photo using the margin offsets and the photo
			// width to calculate positioning of the stamp.
			imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
			
			// Output and free memory
			imagepng($im, $filePath);
			imagedestroy($im);
			$url = BDIR.$targetDir.'/'.$fileName;
			break;
		
		case "Ad":
			
			$_SESSION['upload_ad'] = array(
					'ServerFilePath' => $targetDir,
					'fileName' => $filename_md5 
			);
			
			$path_p = pathinfo($fileName);
			$fileName = MakeLink($path_p['filename']).'.'.$path_p['extension'];
			
				
			
			rename($targetDir.'/'.$fileName, $targetDir.'/'.$filename_md5);
			$result = 'SUCCESS';
			break;
			
		case "BrowserFile":
			$_SESSION['upload_files'][$fileName] = array(
				'ServerFilePath' => $targetDir
			);
			$result = 'SUCCESS';
		//	$sql = DBquery("INSERT INTO BrowserFiles (`ID`, `RealFileName`, `ServerFilePath`,
			///		`Category`, `UploaderID`, `FileUploaded`, `Status`, `MODULE`)
				//	VALUES (NULL, '$fileName', '$targetDir', $CategoryID, ".$User->ID().",
		//							".time(NULL).",0,'".strtoupper($Module)."')");
		//	$inserted_id = DBlastID();
			break;
	}
}

// Return Success JSON-RPC response
die('{"uploaded" : "1", "result" : "'.$result.'", "id": "'.$inserted_id.'", "id2" : "", 
		"fileName" : "'.$fileName.'", 
		"url":"'.$url.'",
		"msg":"'.$msg.'",
		
		"marker":"bgdfg"
		}');
