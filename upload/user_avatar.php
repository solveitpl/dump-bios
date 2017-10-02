<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!IsLogin())
{
	uStop("Błędny strumień wejścia",3);
}

@set_time_limit(5 * 60);


$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds

//uStop(print_r($_FILES,true));
// Get a file name
// Get a file name
if (isset($_REQUEST["name"])) {
	$fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
	$fileName = $_FILES["file"]["name"];
} else {
	$fileName = uniqid("file_");
}


$targetDir = 'upload/users/'.$User->UserNick().'/';


// Create target dir
if (!file_exists($targetDir)) {
	mkdir($targetDir, 0777, true);
	chmod($targetDir, 0777);
}

$old_fileName=$fileName;
$path_p = pathinfo($fileName);

// jeśli nazwa pliku jest niepoprawna
if ((!array_key_exists('filename', $path_p))||(!array_key_exists('extension', $path_p)))
	die('{"jsonrpc" : "2.0", "error" : {"code": 105, "message": "Invalid filename."}, "id" : "'.$path_p['filename'].' '.$path_p['extension'].'", "array":"'.json_encode($path_p).'"}');

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
	if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
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


// Jeśli upload się zakończył
if (!$chunks || $chunk == $chunks - 1) {
	// zmiana nazwy
	rename("{$filePath}.part", $filePath);
	chmod($filePath, 0755);
	$inserted_id = 0;
	// Dodajemy do bazy danych
			$im = imagecreatefromfile($filePath);
			$im = imagescale($im, 100);
			// Set the margins for the stamp and get the height/width of the stamp image		
			imagepng($im, $targetDir . DIRECTORY_SEPARATOR ."user_avatar.png");
			unlink($filePath);
			imagedestroy($im);
	
	
}

// Return Success JSON-RPC response
die('{"uploaded" : "1", "result" : null, "id": "'.$inserted_id.'", "id2" : "'.Encrypt($inserted_id).'",
		"fileName" : "'.$fileName.'",
		"url":"'.BDIR.$targetDir.$fileName.'",
		"marker":"'.Encrypt(time(NULL)).'"
		}');

?>
