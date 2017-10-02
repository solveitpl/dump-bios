<?php

header('Content-Type: image/png');

$UserNick = htmlspecialchars($ARG[1]);

if (file_exists('upload/users/'.$UserNick.'/user_avatar.png'))
	$fileName = 'upload/users/'.$UserNick.'/user_avatar.png';
else
	$fileName = 'images/user_img.jpg';

$im = imagecreatefromfile($fileName);

imagepng($im);
imagedestroy($im);
die();
?>