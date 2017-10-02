<?php
/*
#############################################
#############################################
################ bodziX #####################
########### Bogdan Jakubowski ###############
#############################################
#############################################


*/
if (isset($_POST['id'])){
	$sql = DBquery("INSERT INTO Donates(`id`, `operation_number`, `operation_type`, `operation_status`, `UserID`, `LastUpdate`, `RAW`)
					VALUES(NULL, '".$_POST['operation_number']."', '".$_POST['operation_type']."', '".$_POST['operation_status']."', 0, 0, '".print_r($_POST, TRUE)."')");
	echo "OK";
}
else echo "NOT OK";


?>
