<?php
?>

<?php 
if (!isset($ARG[1])) $ARG[1]='';

if (IsLogin()) _die("Błędne przekierowanie...");

switch ($ARG[1]){
	case "DoIT":
		require_once 'register_form.php';
		break;
	case "activate":
		require_once 'register_email_activate.php';
		break;
		
	case "Check":
		require_once 'register_check.php';
		break;
	default:	// Domyślnie pokazywany jest regulamin do akceptacji
		$AGREEMENT = GetSettings('TermsAndConditions');
		
?>
<div class="register">
	<div class="registation_header">Registry</div>
    
    <div class="registration_message">Regulations</div>
	<div class="registration_terms">
        <div class="register-agreement">
		<?= $AGREEMENT ?>
        </div>
        <div class="register-scroll">
            <span class="register-scroll-up"> > </span>
            <span class="register-scroll-down"> > </span>
        </div>

	</div>
	<div class="registration_accept_box">
		<form action="<?= BDIR.$ARG[0].'/DoIT' ?>" method="post">
    <span class="TermsAgree">	<input type="checkbox" id="TermsAgree" name="TermsAgree"> I agree</span>
			<input type="submit" id="RegisterMe" value="Register me" disabled>
			
		</form>
	</div>
</div>

<?php }

?>