var usersDef=1;

$(document).ready(function(){
	

	$('.edit_this').die('click');
	$('.edit_this').live('click',function(){
	 $prop = $(this).prev();
	 $val = $prop.text()
	 $(this).hide();
	 $prop.html('<input type="text" prop="'+$prop.attr('prop')+'" class="edit_user_data_input" value="'+$val+'">')
	});

	$('.radio[prop]').live('change',function(){
		$UserID = $('#CurrUserID').val();
		$prop = $(this).attr('prop');
		$val = $(this).val();
		$load_img = $('.load_user_data');
		$load_img.fadeIn();
		xhr = $.post(BDIR + 'query/admin',
				"ChangeUserData="+$UserID+'&field='+$prop+'&val='+$val,
				function(data) {
					switch (data.result) {
						case 'SUCCESS': 
							break;
						default:
							ShowDialogBox("Błąd<br>"+data.msg, "BAD");
					}
					$load_img.fadeOut();
				},'json');
	});
	
	
	$('.edit_user_data_input').die('keypress');
	$('.edit_user_data_input').live('keypress',function(e){
		if (e.keyCode==13){
			$this = $(this);
			$content = $(this).parent();
			$UserID = $('#CurrUserID').val();
			$load_img = $('.load_user_data');
			$load_img.fadeIn();
			xhr = $.post(BDIR + 'query/admin',
					"ChangeUserData="+$user_ID+'&field='+$this.attr('prop')+'&val='+$this.val(),
					function(data) {
						switch (data.result) {
							case 'SUCCESS':
								$content.text($this.val());
								$content.next('.edit_this').show();
								break;
							default:
								ShowDialogBox("Błąd<br>" + data.msg, "BAD");
						}
						$load_img.fadeOut();
					},'json');
			
		}
		
	});
	
	$('.user_list thead input[type2nd!=date]').live('keyup',function(event){
		//alert(event.originalEvent);
		var data = $('#search_user').serialize();
		var str_line = '';
		xhr = $.post(BDIR + 'query/admin',
				"GET_USER_LIST=1&"+data,
				function(data) {
					// ładujemy użytkowników
					$('.user_list tbody').empty();
					for (var i=0; i<data.Users.length; i++)
						{
						str_line += ' <tr user_id='+data.Users[i].ID+'>'+
					      '<td><strong>'+data.Users[i].Nick+'</strong></td>'+
					      '<td>'+data.Users[i].Email+'</td>'+
					      '<td>'+data.Users[i].Status_str+'</td>'+
					      '<td>'+data.Users[i].RegisterTime+'</td>'+
					      '<td>'+data.Users[i].City+'</td>'+
					      '<td>'+data.Users[i].LastLoginTime+'</td>'+
					      '<td>'+data.Users[i].TotalPoints+'</td>'+
					      '</tr>';	
						}
					$('.user_list tbody').html(str_line);
					
				}, 'json');
	});
	
	$('.user_list tr[user_id]').die('click');
	$('.user_list tr[user_id]').live('click',function(){
		
		$('[editable]').each(function(index){
			
			$('<img src="'+BDIR+'/images/edit_icon.png" class="edit_this">').insertAfter(this);
		
		})
		$user_ID = $(this).attr('user_id');
		$loading = $('.loading_coat');
		$loading.fadeIn();
		
		xhr = $.post(BDIR + 'query/admin',
				"UserDetail="+$user_ID,
				function(data) {
					// ładujemy zawartość tab
					$('.user_edit_avatar img').attr('src', BDIR+'User/'+data.user_data.Nick);
					$("#CurrUserID").val(data.user_data.ID);
					$(".prop_value3[prop=nick]").text(data.user_data.Nick);
					$(".prop_value3[prop=email]").text(data.user_data.Email);
					$(".prop_value3[prop=city]").text(data.user_data.City);
					$(".prop_value3[prop=country]").text(data.user_data.Country);
					$(".prop_value3[prop=birthday]").text(data.user_data.Birthday);
					$(".prop_value3[prop=last_ip]").text(data.user_data.LastIP);
					$(".prop_value3[prop=register_time]").text(data.user_data.RegisterDate);
					$(".prop_value3[prop=last_login]").text(data.user_data.LastVisit);
					$(".radio[name=user_perm][value="+data.user_data.PermLevel+"]").prop('checked',true);
					$(".radio[name=user_status][value="+data.user_data.Status+"]").prop('checked',true);
					var TotalPoints = data.user_data.Points.RegularPoints + data.user_data.Points.MainPoints;
					$(".prop_value2[prop=aval_points]").text(TotalPoints);
					$(".prop_value2[prop=EarnPoints]").text(data.user_data.Points.EarnPoints);
					$(".prop_value2[prop=SpendPoints]").text(data.user_data.Points.SpendPoints);
					
					
					$('.user_edit_box').fadeIn();
					
					$loading.fadeOut();
					
				}, 'json');

	});
	
	$('.DetailTab').die('click');
	$('.DetailTab').live('click',function(){
		$('.detail_tab_container').hide();
		$('.DetailTab').removeClass('active');
		$(this).addClass('active');
		$('#'+$(this).attr('tab')).delay(100).fadeIn();
	});
	
	$('.del_item[sub=article]').live('click',function(){
		if (confirm("Usunąć artykuł ?")) {
			$load_img = $('.load_user_data');
			$load_img.fadeIn();
			var marker = encodeURIComponent($('#MarkerToken').val());
			var link = $(this).closest('tr').attr('art_id');
			xhr = $.post(BDIR + 'query/admin',
					'DEL_ARTICLE=' + link+'&marker='+marker,
					function(data) {
						switch(data.result){
							case 'BAD_TOKEN':
								ShowDialogBox('Zły token.', "BAD");
								location.reload();
								break;
							case 'SUCCESS':
								ShowDialogBox('Usunięto artykuł', "GOOD");
								$('div[tab=user_articles]').trigger('click');
								break;
							default:
								ShowDialogBox('Wystąpił błąd.', "BAD");
							
						}
								
						$load_img.fadeOut();
					}, 'json');
			}
	});


	
	$('div[tab=user_articles]').die('click');
	$('div[tab=user_articles]').live('click',function(){
		
		$content = $('#user_articles');
		var user_name = $(".prop_value3[prop=nick]").text();
		
				$content.css('text-align', 'center');
				$content.html('<img src="'+BDIR+'images/loading2.gif">');
				var str='<table><tr><th>Artykuł</th><th>Kategoria</th><th>Data dodania</th><th>Działanie</th></tr>';
				var xhr;
				xhr = $.post(BDIR + 'query/member',
						'GET_USER_ARTICLE=' + user_name,
						function(data) {
							for (i=0; i<data.length; i++)	
								str+='<tr art_id='+data[i].link+'><td action="Navigate" target="_blank" arg="article/view/'+data[i].link+'">'+data[i].Title+'</td><td>'
								+data[i].model_name+'</td><td>'+data[i].AddDateTime+
								'<td class="action_btn"><input type="button" class="item_edit" action="Navigate" target="_blank" arg="Article/edit/'+data[i].link+'" value="Edytuj">'+
								'<input type="button" class="del_item" sub="article" value="Usuń"></td>'+
								
								'</td></tr>';
							 
							str+='</table>';
							$content.html(str);
					}, 'json');
			
	});
	
	$('.del_item[sub=files]').live('click',function(){
		if (confirm("Usunąć plik ?")) {
			$load_img = $('.load_user_data');
			$load_img.fadeIn();
			var marker = encodeURIComponent($('#MarkerToken').val());
			var link = $(this).closest('tr').attr('file_id');
			xhr = $.post(BDIR + 'query/admin',
					'DEL_FILE=' + link+'&marker='+marker,
					function(data) {
						switch(data.result){
							case 'BAD_TOKEN':
								ShowDialogBox('Zły token.', "BAD");
								location.reload();
								break;
							case 'SUCCESS':
								ShowDialogBox('Usunięto plik', "GOOD");
								$('div[tab=user_files]').trigger('click');
								break;
							default:
								ShowDialogBox('Wystąpił błąd.', "BAD");
							
						}
								
						$load_img.fadeOut();
					}, 'json');
			}
	});
	
	$('div[tab=user_posts]').die('click');	
	$('div[tab=user_posts]').live('click',function(){

		$content = $('#user_posts');
		var user_name =  $(".prop_value3[prop=nick]").text();
		

				$content.css('text-align', 'center');
				$content.html('<img src="'+BDIR+'images/loading2.gif">');
				var str='<table><tr><th>Title</th><th>Status</th><th>Downloaded</th><th>Ocena</th><th>Data dodania</th><th>Działanie</th></tr>';
				var xhr;
				xhr = $.post(BDIR + 'query/member',
						'GET_USER_POSTS=' + user_name,
						function(data) {
							for (i=0; i<data.length; i++)	
								str+='<tr file_id='+data[i].ID+'><td action="Navigate" target="_blank" arg="">'
								+data[i].Title+'</td><td>'+data[i].StatusStr+'</td><td>'+data[i].DownloadCount+'</td><td>'+(data[i].PointsGood/1)+' / '+(data[i].PointsBad/1)+'</td><td>'+data[i].SendDate+'</td>'+
								'<td class="action_btn"><input type="button" class="item_edit" action="Navigate" arg="'+BDIR+'Browser/GoTo/'+data[i].ID+'" target="_blank" value="Zobacz">'+
								'<input type="button" class="del_item" sub="files" value="Usuń"></td>'+
								'</tr>';
							
							str+='</table>';
							$content.html(str);
					}, 'json');
			
	});
	
	$('div[tab=user_files]').die('click');	
	$('div[tab=user_files]').live('click',function(){

		$content = $('#user_files');
		var user_name =  $(".prop_value3[prop=nick]").text();
		

				$content.css('text-align', 'center');
				$content.html('<img src="'+BDIR+'images/loading2.gif">');
				var str='<table><tr><th>Nazwa pliku</th><th>Status</th><th>Pobrany</th><th>Ocena</th><th>Data dodania</th><th>Działanie</th></tr>';
				var xhr;
				xhr = $.post(BDIR + 'query/member',
						'GET_USER_FILE=' + user_name,
						function(data) {
							for (i=0; i<data.length; i++)	
								str+='<tr file_id='+data[i].Id+'><td action="Navigate" target="_blank" arg="">'
								+data[i].FileDesc+'</td><td>'+data[i].StatusStr+'</td><td>'+data[i].DownloadCount+'</td><td>'+data[i].PointsGood/1+' / '+data[i].PointsBad/1+'</td><td>'+data[i].upload_time+'</td>'+
								'<td class="action_btn"><input type="button" class="item_edit" action="Navigate" arg="'+BDIR+'downloads/item/'+data[i].Id+'" target="_blank" value="Zobacz">'+
								'<input type="button" class="del_item" sub="files" value="Usuń"></td>'+
								'</tr>';
							
							str+='</table>';
							$content.html(str);
					}, 'json');
			
	});
	
	$('div[tab=user_points]').die('click');	
	$('div[tab=user_points]').live('click',function(){

		$content = $('#user_points .points_data');
		var UserID = $('#CurrUserID').val();
		

				$content.css('text-align', 'center');
				$content.html('<img src="'+BDIR+'images/loading2.gif">');
				var str='<table><tr><th>Data</th><th>Żródło</th><th>Element</th><th>Ilość</th><th>Komentarz</th></tr>';
				var xhr;
				xhr = $.post(BDIR + 'query/member',
						'GET_USER_POINTS_DATA=' + UserID,
						function(data) {
							for (i=0; i<data.length; i++)	
								str+='<tr><td action="Navigate">'+data[i].EntryDate+'</td><td>'+data[i].SrcName+'</td>'+
								'<td class="CheckIt center" style="" source="'+data[i].Source+'" element="'+data[i].ElementID+'">Zobacz</td><td class="center">'
								+data[i].Points+'</td><td>'+data[i].Comment+'</td>'+
								'</tr>';
							
							str+='</table>';
							
							$content.html(str);
					}, 'json');
			
	});
	
	$('[element]').die('click');
	$('[element]').live('click',function(){
		var ElID = $(this).attr('element');
		var Src = parseInt($(this).attr('source'))/1;

		switch (Src) {
		case 1: // Software files
			window.open(BDIR+'download/item/'+ElID,'_blank');
			break;
		case 2: // Browser files
			window.open(BDIR+'browser/GoTo/'+ElID,'_blank');
			break;
		default:
			ShowDialogBox("Nothing to see here", "INFO");
		}
		
		
	});
	
	$('.GivePoints input[type=text]').live('keyup',function(){
		if (!($.isNumeric($(this).val())))
			{
			$(this).val('');
			
			}
			
	});
	
	$('div[tab=user_searched_words]').die('click');	
	$('div[tab=user_searched_words]').live('click',function(){

		$content = $('#user_searched_words');
		var user_name =  $(".prop_value3[prop=nick]").text();
		

				$content.css('text-align', 'center');
				$content.html('<img src="'+BDIR+'images/loading2.gif">');
				var str='<table><tr><th>Wyszukiwane słowo</th><th>Ilość</th></tr>';
				var xhr;
				xhr = $.post(BDIR + 'query/admin',
						'GET_USER_SEARCH_WORDS=' + user_name,
						function(data) {
							for (i=0; i<data.length; i++)	
								str+='<tr><td action="Navigate" target="_blank" arg="">'
								+data[i].KEYWORD+'</td><td>'+data[i].Quantity+'</td>'+
								'</tr>';
							
							str+='</table>';
							$content.html(str);
					}, 'json');
			
	});
	
	
	
	$('.GivePoints input[type=button]').live('click',function(){
		var xhr;
		var UserID = $('#CurrUserID').val();
		$input = $('.GivePoints input[type=text]');
		$comm = $('#GivePointsComm');
		if (confirm('Czy przydzielić '+$input.val()+'pkt użytkownikowi ?'))
			{
			$load_img = $('.load_user_data');
			$load_img.fadeIn();
			xhr = $.post(BDIR + 'query/admin',
					'GIVE_POINTS_TO_USER=' + UserID+'&Points='+$input.val()+'&Comm='+$comm.val(),
					function(data) {
						switch(data.result){
							case 'SUCCESS':
								ShowDialogBox('Punkty zostały przydzielone', "GOOD");
								$('div[tab=user_points]').trigger('click');
								break;
							default:
								ShowDialogBox('Wystąpił błąd.', "BAD");
							
						}
				
						$input.val('');
						$load_img.fadeOut();
					}, 'json');
				
			}
	
		 
	
		
	});
	

	//$( "[type2nd=date]").die('change');
	$( "[type2nd=date]").live('change',function(){
		//alert($(this).val().length);
		if ($(this).val().length>=10)
			{
		//	$('.user_list thead input').trigger('keyup');
			alert($(this).val().length);
			}
	});
	
	

	$("[type2nd=date]").live("click", function(){
		
		if (!$(this).hasClass('hasDatepicker'))
			{
			$this = $(this);
		    $(this).datepicker({ 
		        inline: true,
		        dateFormat: 'yy-mm-dd',
		        onSelect: function(dateText) {
		        	
		        	$('.user_list thead input').first().trigger('keyup');
		           }
		    });
		   
	
		    $this.datepicker('show');
		
		    }
	});
	
	$('#DelUserBtn').die('click');
	$('#DelUserBtn').live('click',function(){
		$RadioBtn = $('#del_user_check');
		var UserID = $('#CurrUserID').val();
		var marker = encodeURIComponent($(this).attr('marker'));
		if ($RadioBtn.prop('checked')) {
			if (confirm('Czy napewno usunąć tego użytkownika ?'))
				{
					var xhr;
					xhr = $.post(BDIR + 'query/admin',
							'DELETE_USER=' + UserID+'&marker='+marker,
							function(data) {
								switch(data.result){
									case 'SUCCESS':
										ShowDialogBox('Użytkownik usunięty', "GOOD");
										$('.AdminTab[tab=users]').trigger('click');
										break;
									default:
										ShowDialogBox('Wystąpił błąd.', "BAD");
									
								}

							}, 'json');
				
				}
		}
		else
			ShowDialogBox("Najpierw zaznacz pole", "INFO");
		
	}
	);


	
});