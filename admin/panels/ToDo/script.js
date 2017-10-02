var neditor;
$(window).ready(function(){
	
	
	$('.ToDoTab').die('click');
	$('.ToDoTab').live('click',function(){
		$('.ToDo_tab_container').hide();
		$('.ToDoTab').removeClass('active');
		$(this).addClass('active');
		$('#'+$(this).attr('tab')).delay(100).fadeIn();
	});
	
	$('.ToDoTab[tab="ToDo_Newsletter"]').die('click');
	$('.ToDoTab[tab="ToDo_Newsletter"]').live('click', function(){
		InitializeMailEditor();
	});
	
	$('#send_test_newsletter').die('click');
	$('#send_test_newsletter').live('click', function(){
		var MailContent = neditor.getData();
		xhr = $.post(BDIR + 'query/admin',
				{
					"SEND_NEWSTLETTER" : 1,
					"EMAIL_ADDR" : $('#email_for_testing').val(),
					"EMAIL_SUBJECT" : $('#newsletter_subject_field').val(),
					"EMAIL_CONTENT" : MailContent
				},
				function(data) {
					switch (data.result) {
						case 'SUCCESS':
							ShowDialogBox("Newsletter został wysłany", "GOOD");
							break;
						default:
							ShowDialogBox("Błąd<br>" + data.msg, "BAD");
					}
					
	
				},'json');
	
	});
	
	function NewsletterPrg(){

		xhr = $.post(BDIR + 'query/admin',
				{
					"SENDING_NEWSLETTER_CHECK_PRG" : 1,
				},
				function(data) {
					switch (data.result) {
						case 'SUCCESS':
							var prg_percent = Math.round(parseFloat(data.MAIL_TASK.CURRENT_ITERATION) / parseFloat(data.MAIL_TASK.TOTAL_TO_SEND)*100);
							
							$('#current_email_sending').text(data.MAIL_TASK.CURRENT_MAIL);
							$('#progress_bar_sending_mails').find('.prg_bar_pattern').css('width',prg_percent+'%');
							NewsletterPrg();
							break;
						case 'EOF':
							$('#current_email_sending').text('');
							$('#progress_bar_sending_mails').find('.prg_bar_pattern').css('width','0%');
							break;
						default:
							ShowDialogBox("Error<br>" + data.msg, "BAD");
					}
					
		
				},'json');
	}
	
	$('#send_newsletters').die('click');
	$('#send_newsletters').live('click', function(){
		if (!confirm('Are you sure you want to send this newsletter ?')) return 0;
	
		var MailContent = neditor.getData();
		xhr = $.post(BDIR + 'query/admin',
				{
					"SEND_NEWSTLETTER" : 1,
					"EDITED_ID" : $(neditor).attr('edited_id'),
					"EMAIL_SUBJECT" : $('#newsletter_subject_field').val(),
					"EMAIL_CONTENT" : MailContent
				},
				function(data) {
					switch (data.result) {
						case 'SUCCESS':
							ShowDialogBox("Newsletter has been sent", "GOOD");
							break;
						default:
							ShowDialogBox("Error<br>" + data.msg, "BAD");
					}
					
				
				},'json');
		NewsletterPrg();
	
	});
	

	$('[acq="load_mail_s"]').live('click',function(){
		var id = $(this).closest('tr').attr('mail_id');
		
		
		xhr = $.post(BDIR + 'query/admin',
				{
					"LOAD_NEWSLETTER" : id
				},
				function(data) {
					switch (data.result) {
						case 'SUCCESS':
							$('#newsletter_subject_field').val(data.MAIL.Title);
							 neditor.setData(data.MAIL.Content);
							 $(neditor).attr("edited_id",id);
							break;
						default:
							ShowDialogBox("Error<br>" + data.msg, "BAD");
					}
					
		
				},'json');
		
	});
	
	$('[acq="delete_mail_s"]').live('click',function(){
		if (!confirm('Czy napewno usunąć ten newsletter')) return 0;
		
		var id = $(this).closest('tr').attr('mail_id');
		
		xhr = $.post(BDIR + 'query/admin',
				{
					"DELETE_NEWSLETTER" : id
				},
				function(data) {
					switch (data.result) {
						case 'SUCCESS':
							$(this).closest('tr').remove();
							ShowDialogBox("Deleted.", "GOOD");
							break;
						default:
							ShowDialogBox("Error<br>" + data.msg, "BAD");
					}
					
		
				},'json');
		
	});
	
	  function InitializeMailEditor(){
		// inicjalizacja CKEditor do edycji newslettera

		  CKEDITOR.config.height = 300;
		  //CKEDITOR.config.width = 800;
		  var uploadURL = '';
		  

		  neditor = CKEDITOR.replace('MailBody');
		  
		  neditor.addCommand("NewDoc", { // create named command
			    exec: function(edt) {
			    	$('#newsletter_subject_field').val('');
			    	$load_img = $('#load_setting_data');
			    	neditor.setData('');
			    	$(edt).attr('edited_id',0);
			    	
			    	$load_img.fadeIn();
								    	
			    	
			    }
			});
		  
		  neditor.ui.addButton('NewBtn', { // add new button and bind our command
			    label: "Nowy",
			    command: 'NewDoc',
			    icon: BDIR+'images/edit.png'
			});
		  
		  neditor.addCommand("SaveDoc", { // create named command
			    exec: function(edt) {
			    	var nws_id = $(edt).attr('edited_id');
			    	
			    	
			    	$load_img = $('#load_setting_data');
			    	
			    	$load_img.fadeIn();
					xhr = $.post(BDIR + 'query/admin',
					{
						"SAVE_NEWSLETTER":1,
						"NEWSLETTER_ID":nws_id,
						"SUBJECT":	$('#newsletter_subject_field').val(),
						"CONTENT":encodeURIComponent(edt.getData())
					},
							function(data) {
								switch (data.result) {
									case 'SUCCESS':
										ShowDialogBox("Newsletter has been saved", "GOOD");
										break;
									default:
										ShowDialogBox("ERROR<br>" + data.msg, "BAD");
								}
								
								$load_img.fadeOut();
							},'json');
			    	
			    	
			    }
			});

		  neditor.ui.addButton('SaveBtn', { // add new button and bind our command
			    label: "Zapisz",
			    command: 'SaveDoc',
			    icon: BDIR+'images/save_icon.png'
			});
		  
		  CKEDITOR.on('instanceReady', function(e) {
			    // the real listener
			    e.editor.on( 'simpleuploads.startUpload' , function(ev) {
			        var data = ev.data;
			        // the context property provides info about where the upload is being used
			        // var context = data.context;

			        // Check if there's a dialog open:
			        var dialog = CKEDITOR.dialog.getCurrent();
			        if (dialog)  {
			            var name = dialog.getName();
			            if (name == 'image') {
			                // Get the value of our new checkbox and if it's checked add it as a GET parameter to the URL
			                var value = dialog.getValueOf('Upload', 'chkCustom');
			                if (value)
			                    ev.data.url += '&checked=on';
			            }
			        }
			        var extraFields = ev.data.extraFields || {};

			        CKEDITOR.tools.extend(extraFields, {
			            'Action' : CKEDITOR.config.action,
			            'FormID' : CKEDITOR.config.formID
			        });

			        ev.data.extraFields = extraFields;

			        // And send a new custom HTTP header in the request
			        var extraHeaders = {};
			        extraHeaders[ Core.Get('SessionName') ] = Core.Get('SessionID');
			        ev.data.extraHeaders = extraHeaders;
			    
			    });
			});
		  
	  }
		
	
	
	
});
