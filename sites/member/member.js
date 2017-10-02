$(document).ready(function(){
	$('#user_article_list .div_title').click(function(){

		$content = $(this).next('.div_content');
		var user_name = $('#user_name').val();
		if ($content.text()=='')
			{
				$content.fadeIn();
				$content.css('text-align', 'center');
				$content.html('<img src="'+BDIR+'images/loading2.gif">');
				var str='<table>';
				var xhr;
				xhr = $.post(BDIR + 'query/member',
						'GET_USER_ARTICLE=' + user_name,
						function(data) {
							for (i=0; i<data.length; i++)	
								str+='<tr target="_blank" action="Navigate" arg="'+BDIR+'article/view/'+data[i].link+'" ><td>'+data[i].Title+'</td><td>'
								+data[i].model_name+'</td><td>'+data[i].manu_name+'</td></tr>';
							
							str+='</table>';
							$content.html(str);
					}, 'json');
			}
	});
	
	$('#user_file_list .div_title').click(function(){

		$content = $(this).next('.div_content');
		var user_name = $('#user_name').val();
		if ($content.text()=='')
			{
				$content.fadeIn();
				$content.css('text-align', 'center');
				$content.html('<img src="'+BDIR+'images/loading2.gif">');
				var str='<table>';
				var xhr;
				xhr = $.post(BDIR + 'query/member',
						'GET_USER_FILE=' + user_name,
						function(data) {
							for (i=0; i<data.length; i++)	
								str+='<tr action="Navigate" target="_blank" arg="'+BDIR+'downloads/item/'+data[i].Id+'"><td>'
								+data[i].FileDesc+'</td><td><img src="'+BDIR+'images/downloaded.png">'+data[i].DownloadCount/1+'</td>'+
								'<td><img src="'+BDIR+'images/point_up.png">'+data[i].PointsGood/1+
								' / <img src="'+BDIR+'images/point_down.png">'+data[i].PointsBad/1+'</td></tr>';
							
							str+='</table>';
							$content.html(str);
					}, 'json');
			}
	});
	
	$('#user_posts_list .div_title').click(function(){

		$content = $(this).next('.div_content');
		var user_name = $('#user_name').val();
		if ($content.text()=='')
			{
				$content.fadeIn();
				$content.css('text-align', 'center');
				$content.html('<img src="'+BDIR+'images/loading2.gif">');
				var str='<table>';
				var xhr;
				xhr = $.post(BDIR + 'query/member',
						'GET_USER_POSTS=' + user_name,
						function(data) {
							for (i=0; i<data.length; i++)	
								str+='<tr target="_blank" action="Navigate" arg="'+BDIR+'browser/GoTo/'+data[i].ID+'"><td>'
								+data[i].Title+'</td><td><img src="'+BDIR+'images/downloaded.png">'+data[i].DownloadCount/1+'</td>'+
								'<td><img src="'+BDIR+'images/point_up.png">'+data[i].PointsGood/1+
								' / <img src="'+BDIR+'images/point_down.png">'+data[i].PointsBad/1+'</td></tr>';
							
							str+='</table>';
							$content.html(str);
					}, 'json');
			}
	});
	
	$('#answer_content').keydown(function(e){
		 if (e.keyCode == 13) {
		        if (e.ctrlKey) {
		            var val = this.value;
		            if (typeof this.selectionStart == "number" && typeof this.selectionEnd == "number") {
		                var start = this.selectionStart;
		                this.value = val.slice(0, start) + "\n" + val.slice(this.selectionEnd);
		                this.selectionStart = this.selectionEnd = start + 1;
		              
		            } else if (document.selection && document.selection.createRange) {
		                this.focus();
		                var range = document.selection.createRange();
		                range.text = "\r\n";
		                range.collapse(false);
		                range.select();
		            }
		            return false;
		        }
		        var pointer = Date.now();
		        $('#talk_window').append('<div pointer="'+pointer+'" class="talk i_said_that"><div class="entry_date"><img class="new_msg_load" src='+
		        		BDIR+'images/loading2.gif></div>'+$(this).val()+'</div>');
				$("#talk_window").scrollTop($("#talk_window")[0].scrollHeight);
		        $('#msg_pointer').val(pointer);
		        var xhr;
				xhr = $.post(BDIR + 'query/member',
						$('#new_msg').serialize(),
						function(data) {
							switch(data.result){
								case 'ERROR':
									ShowDialogBox(data.msg, "BAD");
									break;
									
								case 'INTERNAL_ERROR':
									ShowDialogBox(data.msg, "BAD");
									break;
									
								case 'SUCCESS':
									$('div[pointer='+data.msg_pointer+'] > .entry_date').text(data.format_add_time);
									break;
								
							}
							$("#talk_window").scrollTop($("#talk_window")[0].scrollHeight);
					}, 'json');
				
			    $(this).val('');
		        return false;
		    }
		 
	});
	
	$('#talk_window').scroll(function() {
	    var pos = $(this).scrollTop();
	    var UserName = $('#user_name').val();
	    var ID=$('.talk').first().attr('msg_id');
	    
	    if (pos == 0) {
	    	$('#msg_loading_bar').fadeIn();
	    	var xhr = $.post(BDIR + 'query/member',
					"LOAD_MORE_MSGS="+UserName+"&ID="+ID,
					function(data) {
						switch(data.result){
							case 'ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'INTERNAL_ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'GOT_MESSAGE':
								for (var i=0; i<data.msg_count; i++){
									var style = data.msgs[i].AuthorNick==USER.Nick ? 'he_said_that' : 'i_said_that';
									var html_content='<div class="talk '+style+'" msg_id="'+data.msgs[i].ID+'"><div class="entry_date"> '+
													data.msgs[i].AuthorNick+','+ 
									data.msgs[i].DateOF+'</div>'+data.msgs[i].Content+'</div>';
									$(html_content).insertBefore($("#talk_window .talk").first());
								}
								
								
								$("#talk_window").scrollTop(($(".talk[msg_id="+ID+"]").position().top-75));
								break;
							
							
						}
						$('#msg_loading_bar').fadeOut();
						
				}, 'json');
	    }
	});
	
	// edytowanie pola w ustawieniach profilu
	$('.edit_field').live("click",function(){
		$field_obj = $(this).parent();
		$field_value = $field_obj.text();
		$field_obj.empty();
		$field = $field_obj.attr('field');
		if ($field=='') {
			ShowDialogBox("Błąd wewnętrzny", "BAD");
			return 0;
		}
		$field_obj.html('<input type=text origin="'+$field_value+'" class="edited_user_field" value="'+$field_value+'">');
		$input = $field_obj.children('input');
		$input.focus().val("").val($field_value);
	})
	
	$('.edited_user_field').live('focusout keyup',function(e){
		// jeśli zawartość się nie zmieniła
		if (!((typeof e.keyCode == 'undefined') || (e.keyCode==13))) return 0; // jeśli stracił focus lub wciśnięto enter - idź dalej
		$parent = $(this).parent();
		$value = $(this).attr('origin')
		if ($(this).val()==$value) 
			{
				$parent.empty();
				$parent.html($value+"<img src='"+BDIR+"images/edit.png' class='edit_field date'>");
				return 0;
			}
		$input.addClass("loading_input_icon");
		// uzupełniamy
		var xhr = $.post(BDIR + 'query/member',
				"UPDATE_USER_PROFILE="+$field+"&value="+$input.val()+"&code="+encodeURIComponent($('#marker').val()),
				function(data) {
					switch(data.result){
						case 'ERROR':
							ShowDialogBox(data.msg, "BAD");
							break;
							
						case 'INTERNAL_ERROR':
							ShowDialogBox(data.msg, "BAD");
							break;
							
						case 'SUCCESS':
							$parent.html(data.NewValue+"<img src='"+BDIR+"images/edit.png' class='edit_field date'>");
							break;
						
						
					}
					$input.removeClass("loading_input_icon");
					
			}, 'json');
	});
	
	
	$('#want_newsletter').change(function(){
		// zaznaczamy
		$('#checkbox_loading').fadeIn();
		var xhr = $.post(BDIR + 'query/member',
				"UPDATE_USER_PROFILE=WantsNewsletter&value="+$(this).prop('checked')+"&code="+encodeURIComponent($('#marker').val()),
				function(data) {
					switch(data.result){
						case 'ERROR':
							ShowDialogBox(data.msg, "BAD");
							break;
							
						case 'INTERNAL_ERROR':
							ShowDialogBox(data.msg, "BAD");
							break;
							
						case 'SUCCESS':
							break;
						
						
					}
					$('#checkbox_loading').fadeOut();
					
			}, 'json');
	});
	
	$('#pickAvatar').click(function(){
	    $('.avatar-block').css('display','block');
	});
    $('.avatar-block .avatar-header span:contains(X)').click(function() {
        $('.avatar-block').css('display','none');

	});
	$('.lib_avatar').click(function(){
		var src= $(this).attr("src");
		var xhr = $.post(BDIR + 'query/member',
				"PICK_AVATAR="+encodeURIComponent($(this).attr("pointer")),
				function(data) {
					switch(data.result){
						case 'ERROR':
							ShowDialogBox(data.msg, "BAD");
							break;
							
						case 'INTERNAL_ERROR':
							ShowDialogBox(data.msg, "BAD");
							break;
							
						case 'SUCCESS':
							$('.avatar-block').hide();
							$('#user_avatar').attr('src',src);
							ShowDialogBox("Avatars changed", "GOOD");
							break;
						
						
					}
					$('#checkbox_loading').fadeOut();
					
			}, 'json');

	});




	
});
