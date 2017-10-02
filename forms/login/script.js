 var login = {
    
    ShowForm: function () {
    	
        $(".login_form").show();
        $(".login_form .login_background").fadeIn();
      //  $(".login_form .login_box").slideDown();
        var div = $(".login_form .login_box");
        div.show();
        
    },
 
 	CloseForm: function () {
 		$('.login_password_restore').hide();
        $(".login_form").hide();
    }
    
 
 
}
 
 $(window).ready(function(){
	// $('.login_form').show();
	// $('.login_password_restore').show();
	 
	 $('.restore_password_link').click(function(){
		 $('.login_box').fadeOut();
		 $('.login_password_restore').fadeIn(); 
	 });
	 
	 $("#RESET_PASSWD").click(function(){
		 $('#login_loading').fadeIn();
		 $login_pointer = $("#passwd_reminder_login").val();
		 
		 if ($login_pointer=="") ShowDialogBox("Pole nie może być puste", "BAD");
		 
			xhr = $.post(BDIR + 'query/login',
					'USER_PASSWORD_RESET=' + $login_pointer+"&MARKER="+encodeURIComponent($('#RESET_PASSWD_MARKER').val()),
					
					function(data) {
						if (!(data)) return;	// return jesli brak danych
						$('.notify_msg_element').attr('check', 1);
						switch(data.result){
						case null: ShowDialogBox("...", "INFO");
						case "ACCESS_DENIED": ShowDialogBox("ACCESS DENIED", "INFO"); break;
						case "INTERNAL_ERROR":ShowDialogBox("Nieznany błąd...","BAD"); break;
						case "ERROR": ShowDialogBox(data.msg, "BAD"); break;
						case "SUCCESS": 
								ShowDialogBox("Udało się !<br>Na adres E-mail została wysłana informacja z dalszymi instrukcjami ! " + data.msg,"INFO");
								$('.login_form').fadeOut();
								$('.login_password_restore').hide();
							break;
						
						}
						$('#login_loading').fadeOut();
					}, 'json');
		 
	 });
	
 });