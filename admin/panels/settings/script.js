$(window).ready(function(){
	
	$('.save_sett').die('click');
	$('.save_sett').live('click',function(){
		$txtarea = $(this).parent().find('textarea');
		$(this).attr('src', BDIR+'images/loading2.gif');
		
		$this = $(this);
		
		xhr = $.post(BDIR + 'query/admin',
				"ChangeSettValue="+$txtarea.attr('prop')+'&val='+$txtarea.val(),
				function(data) {
					switch (data.result) {
						case 'SUCCESS':
							break;
						default:
							ShowDialogBox("Błąd<br>" + data.msg, "BAD");
					}
					$this.attr('src', BDIR+'images/ok.png');
				},'json');
		
	});
	
	$('.edit_sett').die('click');
	$('.edit_sett').live('click',function(){
	 $prop = $(this).prev();
	 $val = $prop.text()
	 $(this).hide();
	 $prop.html('<input type="text" prop="'+$prop.attr('prop')+'" title="'+$prop.attr('tip')+'" class="edit_sett_data_input" value="'+$val+'">')
	});
	
	$('.edit_sett_data_input').die('keypress');
	$('.edit_sett_data_input').live('keypress',function(e){
		if (e.keyCode==13){
			$this = $(this);
			$content = $(this).parent();
			$load_img = $('.load_user_data');
			$load_img.fadeIn();
			xhr = $.post(BDIR + 'query/admin',
					"ChangeSettValue="+$this.attr('prop')+'&val='+$this.val(),
					function(data) {
						switch (data.result) {
							case 'SUCCESS':
								$content.text($this.val());
								$content.next('.edit_sett').show();
								break;
							default:
								ShowDialogBox("Error<br>" + data.msg, "BAD");
						}
						$load_img.fadeOut();
					},'json');
			
		}
		
	});
	
	$('.SettingTab').die('click');
	$('.SettingTab').live('click',function(){
		$('.setting_tab_container').hide();
		$('.SettingTab').removeClass('active');
		$(this).addClass('active');
		$('#'+$(this).attr('tab')).delay(100).fadeIn();
	});
	
	
	$('.SettingTab[tab="settings_terms"]').die('click');
	$('.SettingTab[tab="settings_terms"]').live('click',function(){
		InitializeEditor();
	});
	
	
	
	
	
	  function InitializeEditor(){
		// inicjalizacja CKEditor do edycji regulaminu

		  CKEDITOR.config.height = 300;
		  //CKEDITOR.config.width = 800;
		  var uploadURL = '';
		  

		  editor = CKEDITOR.replace('TermsEdit');
		  
		  editor.addCommand("SaveDoc", { // create named command
			    exec: function(edt) {
			    	$load_img = $('#load_setting_data');

			    	$load_img.fadeIn();
					xhr = $.post(BDIR + 'query/admin',
							"ChangeSettValue=TermsAndConditions&val="+encodeURIComponent(edt.getData().replace(/<(?:.|\n)*?>/gm, '')),
							function(data) {
								switch (data.result) {
									case 'SUCCESS':
										ShowDialogBox("Regulamin został zapisany", "GOOD");
										break;
									default:
										ShowDialogBox("Błąd<br>" + data.msg, "BAD");
								}
								
								$load_img.fadeOut();
							},'json');
			    	
			    	
			    }
			});

		  editor.ui.addButton('SaveBtn', { // add new button and bind our command
			    label: "Zapisz regulamin",
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
