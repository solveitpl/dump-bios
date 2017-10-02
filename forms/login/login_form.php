
<div class="login_form">
	<div class="login_box">
		<div class="login_header">
			<span class='close_btn' OnClick="login.CloseForm()">X</span>
		</div>
		  <img src="/images/logowanie.png" id="img_login" alt="login">
		<div class="login_input_box" >
			<form action="<?= BDIR ?>login" method="post">
                
				 <input type='text' placeholder="EMAIL ADRESS" class="login_input" name="login"><br><br>
				<input type="password" placeholder="PASSWORD" name="password"><BR>
                <div class="restore_password_link" >RECOVERING PASSWORD </div>
				<input type="submit"  id="login_button" value="> LOG IN">
			</form>
			
		</div>
	</div>
	
	<div class="login_password_restore">
		<div class="login_header">
			<span class="resetheader">RESET PASSWORD</span>
			<span OnClick="login.CloseForm()" class="restoreX">X</span>
		</div>
		
		<div class="login_input_box">
			
				<input type='text' placeholder="EMAIL ADRESS" id="passwd_reminder_login">
    
            <br><br>
				<input type='hidden' id="RESET_PASSWD_MARKER" value="<?= Encrypt(time(NULL))?>">
				<input type="button" id="RESET_PASSWD" value="RESET PASSWORD"><br>
				<img id="login_loading" src="<?= BDIR ?>images/loading_line.gif">
				
		</div>
	</div>
	
	
	
	<div class="login_background"></div>
	
</div>

