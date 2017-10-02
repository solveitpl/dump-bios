
function CheckNewMessage(){
		var xhr;

		if (!USER.CheckPerm(PERM.USER)) return 0;
		var last_message_date = $('#last_message_date').val();
		var ActiveTalk =  $('#user_name').val() || '';

		xhr = $.post(BDIR + 'query/notifier',
				'CHECK_NEW_MESSAGES=' + last_message_date,
				
				function(data) {
					if (!(data)) return;	// return jesli brak danych
					$('#MailBox .notify_msg_element').attr('check', 1);
					switch(data.result){
					case null: ShowDialogBox("...", "INFO");
					case "ACCESS_DENIED": ShowDialogBox("ACCESS DENIED", "INFO");break;
					case "INTERNAL_ERROR": break;
					case "GOT_MESSAGE":

							for (var i=0; i<data.msg_count; i++)
								{
								$msg_element = $('#MailBox .notify_msg_element[user='+data.msgs[i].Nick+']');
								//$msg_element.remove();
								if (!$msg_element.length){
									var html_content="<div class='notify_msg_element' check='0' user='"+data.msgs[i].Nick+"' " +
											"action='Navigate' arg='member/"+data.msgs[i].Nick+"/SendMessage'>" +
											"<div class='info_div'>"+data.msgs[i].DateOF+"</div>" +
											"<div class='img_div'><img src='"+BDIR+"images/user_img.jpg'></div>" +
												"<div class='content_div'>" +
													"<div class='msg_user_name'>"+data.msgs[i].Nick+"</div>"
													"<div class='msg_text'>"+data.msgs[i].Content+"</div>"
												"</div>"+
											"</div>";
									$(html_content).insertBefore($('#MailBox .notify_msg_element:first'));
								}
								
								if ($('#MailBox .notify_msg_element:first').attr('user')!=data.msgs[i].Nick)
									$msg_element.insertBefore($(' #MailBox.notify_msg_element:first'));
									
									
								$msg_element.children('.info_div').html(data.msgs[i].DateOF);
								$msg_element.children('.content_div').children('.msg_text').html(data.msgs[i].Content);
								$msg_element.attr('check', data.msgs[i].Readed);
								
								// jeśli jest otwarte okno konwersacji
								if (ActiveTalk==data.msgs[i].Nick)	{
									var html_content='<div class="talk he_said_that"><div class="entry_date"> '+data.msgs[i].Nick+','+ 
														data.msgs[i].DateOF+'</div>'+data.msgs[i].Content+'</div>';
									$(html_content).insertAfter($("#talk_window > div").last());
									$("#talk_window").scrollTop($("#talk_window")[0].scrollHeight);
									xhr = $.post(BDIR + 'query/notifier','MARK_MSG_AS_READED='+data.msgs[i].client_id,function(){},'json');
									//alert($("#talk_window:last-child").text());
								}
								
								
								}
						break;
					
					}
					
					// ile czasu ma trwać interwał miedzy sprawdzeniem wiadomości. Jeśli jest to panel wiadomości to ma to być odpowiednio szybko
					var TimeOutPeriod = 7000;
					if (ActiveTalk) TimeOutPeriod = 1000;
					
					setTimeout('CheckNewMessage()', TimeOutPeriod);
					
					// jeśli jest nie przeczytana wiadomość, efekt trzęsienia kopertą
					if (data.msg_count)
						// $('#UserMSG').effect('shake', {times:4}, 1000 );
                        /*$("#UserMSG").css('background-image','url("images/blue_envelope.png")');*/
                    $("#UserMSG").attr('src',BDIR + 'images/blue_envelope.png');
				}, 'json');
	}

$(document).ready(function(){
	
	$(document).mouseup(function (e)
			{
			    var container = $('.notify_msg');

			    if (!container.is(e.target) // if the target of the click isn't the container...
			        && container.has(e.target).length === 0) // ... nor a descendant of the container
			    {
			    	container.children('.notify_msg_list').hide();
			    	container.children('.notify_icon').css('filter','');
			    	container.css('background','');
                    /*$("#UserMSG").css('background-image','url("images/koperta8bitwhite.png")');
                    $("#skull_icon").css('background-image','url("images/skull.png")');*/
			    }
			});
	
	$('.notify_msg').click(function(){
		 $('.notify_msg').not(this).children('.notify_msg_list').hide();
		$('.notify_msg').not(this).children('.notify_icon').css('filter','');
		// $('.notify_msg').not(this).css('background','');
		 $(this).children('.notify_msg_list').toggle();

		var display = $(this).children('.notify_msg_list').is(':visible');

		if (display){
			/*if(this.children[0].id == 'skull_icon') $("#skull_icon").css('background-image','url("images/redskull.png")');
			else $("#UserMSG").css('background-image','url("images/koperta_gold.png")');*/
			 $(this).children('.notify_icon').css('filter','invert(0%)');
			/*$(this).css('background','#f8f8f8');*/


		}
		else{
			 $(this).children('.notify_icon').css('filter','');
			// $(this).css('background','');
             /*$("#skull_icon").css('background-image','url("images/skull.png")');
             $("#UserMSG").css('background-image','url("images/koperta8bitwhite.png")');*/


		}
		
	});
	
	// Klikanie wiadomosci systemowych
	$('#SysInfoMsgs .notify_msg_element[internal]').click(function(e){
		var MsgID = parseInt($(this).attr('internal')) || 0;
		$this = $(this);
		if (parseInt(MsgID)>0)
			{
				e.stopPropagation();
				
				xhr = $.post(BDIR + 'query/notifier',
					'ACK_SYSINFO=' + MsgID,
					function(data) {
						$this.removeAttr('internal');
						$this.trigger('click');
					
					}, 'json');
			}
		
	});
	
	
	CheckNewMessage()
	
	// Przepisanie zawartości ukrytego div generowanego na końcu strony.
	//$('#SysInfoMsgs').children('.notify_msg_element').first().insertBefore($('#NNTBS_SysInfo').html());
	$('#NNTBS_SysInfo').insertBefore($('#SysInfoMsgs').children('.notify_msg_element').first());
});

