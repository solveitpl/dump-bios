<?php
$Pointer = htmlspecialchars($ARG[2]);

$sql = DBquery("SELECT Users.ID FROM Users INNER JOIN (SELECT ID, MD5(Email) as Email2 FROM Users) as t2 ON Users.ID = t2.ID WHERE t2.Email2='$Pointer'");

$Arr = DBarray($sql);
if (count($Arr)){
	DBquery("UPDATE Users SET Status = 2 WHERE Status=0 AND ID=".$Arr['ID']);
	echo "Your account has been activated ! You can login and enjoy all of portal content.";
}

?>
