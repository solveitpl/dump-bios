
<?php

switch ($ARG[1]){
	case 'successful':
		include "payment_successful.php";
		break;
		
	case 'cancel':
		include "payment_cancel.php";			
		break;
		
	default:
		include "payment_form.php";
		
}
?>

