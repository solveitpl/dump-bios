<?php
if (IsLogin()) _die("Strona nie jest dostępna");

$Modules->LoadMailSender();
if (isset($ARG[1])) $token = htmlspecialchars($ARG[1]);
else {
	StrangeEvent("Anonim used bad token...","PASSWD_REGISTER");
	_die("Bad token...");
}	
$sql = DBquery("SELECT * FROM UserPasswordRestore WHERE token='$token'");

if ($sql->num_rows!=1){ // jeśli brakuje tego tokena lub jest więcej niż jeden (podejrzana sprawa)
	StrangeEvent("Przy pytaniu o token skrypt zwrócił ".$sql->num_rows." wyników. Token: $token","PASSWD_RESTORE");
	_die("Ten token już nie istnieje !<br>Proszę ponownie użyć narzędzia resetowania hasła<br>Za utrudnienia przepraszamy...");
}

$RESET_PASSWORD = DBarray($sql);

if ($RESET_PASSWORD['TokenExpired'] < time(NULL)){ // jeśli token już wygasł
	$sql = DBquery("DELETE FROM UserPasswordRestore WHERE token='$token'");
	_die("Token stracił ważność.<br>Proszę ponowanie użyć narzędzia resetowania hasła.");
	}
	
// #### wysłanie formularza
$Err = 0;	// indeks błędu zgodnie z tablicą $CheckErrors
$Status =0; // Zmienna status. Jeśli 1 - hasło zostało zmienione
if (isset($_POST['RestorePassword']))
	{
	$password = htmlspecialchars($_POST['password']);
	$password_retype = htmlspecialchars($_POST['password_retype']);
	$marker = $_POST['marker'];
	if ($password!=$password_retype) $Err = 1;
	if (strlen($password)<6) $Err = 2;
	if (Decrypt($marker)!=$RESET_PASSWORD['TokenCreated']) $Err = 3;
	
	if ($Err==0) // jeśli nie ma błędu
	{
		$NewPass = md5($password);
		$sql = DBquery("UPDATE Users SET Password='$NewPass' WHERE ID=".$RESET_PASSWORD['UserID']);
		if ($sql==false)
		{
			StrangeEvent("Błąd SQL przy resetowaniu hasła.");
			_die("Błąd wewnętrzny.<br>Proszę spróbować ponownie później");
		}
		else 
		{
			$sql = DBquery("DELETE FROM UserPasswordRestore WHERE token='$token'");
			AddToMsgList("Nowe hasło zostało zapisane !", "GOOD");
			$Status = 1;
		}
	}
	
	
	
	}

$CheckErrors = array(
	0=>'',
	1=>'Hasła się nie zgadzają',
	2=>'Hasło zbyt krótkie',
	3=>'Błąd ogólny'
);

if ($Status==1) echo "<script>window.location.href=BDIR</script>";
else {
?>


<style>

        #password,#password_retype
    {
        width: 65%;
        margin-left: 4%;
        color: #6fd8d4;
        margin-bottom: 10px;
    }
</style>
<form method="post" id="restore_pass_form" action="">
	<div class="restore_pass_form">
		<div id="password_row" class="section">
			<div class="section_header">
			<div class="restore_pass_form_title">PASSWORD RESET</div>
			<span class="restore_pass_form_err"><?= $CheckErrors[$Err] ?></span>
			</div>
            <table>
            <tr>
			<div class="field" sty>
                <div><td><span class="restore_pass_form_title" style="color:#6fd8d4;">NEW PASSWORD:</span></td><td><input type="password" name="password" id="password" VALIDITY="BAD"></td></div>
			</div>
            </tr>
            <tr>
			<div class="field">
				<div><td><span class="restore_pass_form_title" style="color:#6fd8d4;">PASSWORD RETYPE: </span></td><td><input type="password" name="password_retype" id="password_retype" VALIDITY="BAD"></td></div>
			</div>
            </tr>    
            </table>    
			<span id="PassMsg" class="RegisterSideInputMsg"></span>
		</div>
		<input type="hidden" name="marker" value="<?= Encrypt($RESET_PASSWORD['TokenCreated']) ?>">
		<div class="restore_pass_btn">
			<input type="submit" name="RestorePassword" value="RESET PASSWORD">
		</div>
	
	
	</div>
</form>
<?php 
}
?>