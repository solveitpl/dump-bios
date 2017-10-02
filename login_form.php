<div class="login_form">
	<div class="login_box">
		<div class="login_header">
			Logowanie
			<span class='close_btn' OnClick="login.CloseForm()">X</span>
		</div>
		
		<div class="login_input_box">
			<form action="<?= BDIR ?>login" method="post">
				Login: <input type='text' class="login_input" name="login"><br><br>
				Hasło: <input type="password"  name="password"><BR>
				<input type="submit"  class="login_button" value="Zaloguj">
			</form>
			<div class="restore_password_link" >Odzyskiwanie hasła</div>
		</div>
	</div>
	
	<div class="login_password_restore">
		<div class="login_header">
			Przywracanie hasła
			<span class='close_btn' OnClick="login.CloseForm()">X</span>
		</div>
		
		<div class="login_input_box">
			
				<input type='text' id="passwd_reminder_login"><br><br>
				<input type='hidden' id="RESET_PASSWD_MARKER" value="<?= Encrypt(time(NULL))?>">
				<input type="button" id="RESET_PASSWD" value="Resetuj hasło"><br>
				<img id="login_loading" src="<?= BDIR ?>images/loading_line.gif">
				
		</div>
	</div>
	
	
	
	<div class="login_background"></div>
	
</div>

