<?php
/*
 * OPERACJE NA PLIKACH GRAFICZNYCH
 */

// resizer dla kompatybilność z PHP 5.3
function imscale($im, $_height){
	

	// Get new sizes
	$width = imagesx($im);
	$height = imagesy($im);
	
	$prop = $_height/$height;
	//if ($percent>1) $percent = 1;
	$newwidth = $width * $prop;
	$newheight = $_height;

	// Load
	$thumb = imagecreatetruecolor($newwidth, $newheight);
	$source = $im;

	// Resize
	imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	
	return $thumb;
	
}

/*
 * Przygotowanie miniatur obrazków
 */

function CreateSmall($_filename, $_dest, $_height) {

	$im = imagecreatefromfile($_filename);
	$width = imagesx($im);
	$height = imagesy($im);


	//$im = imagescale($im, $_height);
	$im = imscale($im, $_height);

	$stamp = imagecreatefrompng(BDIR.'images/watermark.png');

	// Set the margins for the stamp and get the height/width of the stamp image
	$marge_right = 10;
	$marge_bottom = 10;
	//	$stamp = imagescale($stamp, imagesx($im)*0.4);
	$stamp = imscale($stamp, imagesx($im)*0.15);
	$stamp = imagesetopacity($stamp, 0.5);

	$sx = imagesx($stamp);
	$sy = imagesy($stamp);
	// Copy the stamp image onto our photo using the margin offsets and the photo
	// width to calculate positioning of the stamp.
	imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
	if ($_dest=='') return $im;
	imagepng($im, $_dest);

}


?>