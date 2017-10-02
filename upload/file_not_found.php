<?php
?>
<div style='text-align: center; font-size:25px; font-weight: bold; margin-top:20px;'>
	Niestety, wygląda na to, że link jest nieaktywny<br>
	<img style="width:200px;" src="<?= BDIR ?>images/warning.png"><br>
	Administrator został powiadomiony o tym fakcie. Wybacz, więc :(
</div>
<?php 
$MSG = "Użytkownik napotkał błędny link -> ".$_SERVER['REQUEST_URI'];
DBquery("INSERT INTO PortalError(`ID`,`MODULE`,`MSG`,`Date`,`User`) VALUES(NULL, 'DOWNLOAD', '.$MSG.', ".time(NULL).", ".$User->ID().")");
?>