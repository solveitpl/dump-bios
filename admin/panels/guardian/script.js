$(window).ready(function(){

	function LoadSQLLogs(){
		var Ttime = new Date();
    	var xhr = $.post(BDIR + 'query/admin',
				"LOAD_LOGS="+Ttime.getFullYear()+'-'+(Ttime.getMonth()+1)+'-'+Ttime.getDate()+' '+Ttime.toLocaleTimeString(),
				function(data) {
					switch(data.result){
						case 'ERROR':
							ShowDialogBox(data.msg, "BAD");
							break;
							
						case 'INTERNAL_ERROR':
							ShowDialogBox(data.msg, "BAD");
							break;
							
						case 'SUCCESS':
							var html_content='';
							for (var i=0; i<data.msg_count; i++){
								html_content+='<tr><td>'+data.msgs[i].id+'</td> <td>'+data.msgs[i].date_of+'</td><td>'+data.msgs[i].query+'</td><td>'+data.msgs[i].result+'</td><td><input type="button" class="logs_btn_show_data" value="SHOW" data="'+encodeURI(data.msgs[i].DUMP_DATA)+'" /></td></tr>';
						
							}
			
							$('#TLogs_SQL tbody').empty();
							$('#TLogs_SQL tbody').html(html_content);
							
							break;
						
						
					}
				
					
			}, 'json');
	}
	
	function LoadPHPLogs(){
		var Ttime = new Date();
	
	    	var xhr = $.post(BDIR + 'query/admin',
					"LOAD_PHP_LOGS="+Ttime.getFullYear()+'-'+(Ttime.getMonth()+1)+'-'+Ttime.getDate()+' '+Ttime.toLocaleTimeString(),
					function(data) {
						switch(data.result){
							case 'ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'INTERNAL_ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'SUCCESS':
								var html_content='';
								for (var i=0; i<data.msg_count; i++){
									html_content+='<tr><td>'+data.msgs[i].id+'</td> <td>'+data.msgs[i].date_of+'</td><td>'+data.msgs[i].string+'</td><td>'+data.msgs[i].file+'</td>'+
									'<td>'+data.msgs[i].line+'</td><td>'+data.msgs[i].ErrStr+'</td><td>'+data.msgs[i].UserNick+'</td><td><input type="button" class="logs_btn_show_data" value="SHOW" data="'+encodeURI(data.msgs[i].DUMP_DATA)+'" /></td>'
									'</tr>';
					
								}
								
								$('#TLogs_PHP tbody').empty();
								$('#TLogs_PHP tbody').html(html_content);
								
								break;
							
							
						}
					
						
				}, 'json');
	}
	
	function LoadPortalLogs(){
		var Ttime = new Date();
	    	var xhr = $.post(BDIR + 'query/admin',
				"LOAD_ALERTS="+Ttime.getFullYear()+'-'+(Ttime.getMonth()+1)+'-'+Ttime.getDate()+' '+Ttime.toLocaleTimeString(),
					function(data) {
						switch(data.result){
							case 'ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'INTERNAL_ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'SUCCESS':
								var html_content='';
								for (var i=0; i<data.msg_count; i++){
									html_content+='<tr><td>'+data.msgs[i].ID+'</td> <td>'+data.msgs[i].MSG+'</td><td>'+data.msgs[i].datef+'</td><td>'+data.msgs[i].MODULE+'</td>'+
									'<td>'+data.msgs[i].IP_ADDR+'</td><td>'+data.msgs[i].UserNick+'</td><td><input type="button" class="logs_btn_show_data" value="SHOW" data="'+encodeURI(data.msgs[i].DUMP_DATA)+'" /></td>'+
									'</tr>';
							
								}
							
								$('#AlertTab tbody').empty();
								$('#AlertTab tbody').html(html_content);
								
								break;
							
							
						}
					
						
				}, 'json');
	
		
	}
	
	$('.LogsTab').die('click');
	$('.LogsTab').live('click',function(){
		$('.Logs_tab_container').hide();
		$('.LogsTab').removeClass('active');
		$(this).addClass('active');
		$('#'+$(this).attr('tab')).delay(100).fadeIn();
		var tab = $(this).attr('tab');
		switch(tab) {
		case 'Logs_Guard':
			LoadPortalLogs();
			break;
		case 'Logs_SQL':
			LoadSQLLogs();
			break;
		case 'Logs_PHP':
			LoadPHPLogs();
			break;
			
			
			
		}
		
	});
	

	$('#delAllLogs').die('click');
	$('#delAllLogs').live('click',function(){
		var marker = encodeURIComponent($('#LogsMarker').val());
		var xhr = $.post(BDIR + 'query/admin',
				"DelAllLogs=1&marker="+marker,
				function(data) {
			ShowDialogBox("Wyczyszczono log", "INFO");
					$('#Logs_SQL tbody').empty();
				}, 'json');
	});
	
	$('#delAllPHPLogs').die('click');
	$('#delAllPHPLogs').live('click',function(){
		var marker = encodeURIComponent($('#PHPLogsMarker').val());
		var xhr = $.post(BDIR + 'query/admin',
				"DelAllPHPLogs=1&marker="+marker,
				function(data) {
			ShowDialogBox("Wyczyszczono log", "INFO");
					$('#Logs_PHP tbody').empty();
				}, 'json');
	});

	
	$('#Logs_SQL').scroll(function() {
	    var pos = $(this).scrollTop();
	    var height = $(this)[0].scrollTopMax;
	    var total = height-pos;
	    
	    var ID=$('#LogTable tr:last td:nth-child(2)').text();


	    if (total == 0) { 
	    	var xhr = $.post(BDIR + 'query/admin',
					"LOAD_LOGS="+ID,
					function(data) {
						switch(data.result){
							case 'ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'INTERNAL_ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'SUCCESS':
								console.log(data);
								for (var i=0; i<data.msg_count; i++){
									
									var html_content='<tr><td>'+data.msgs[i].id+'</td> <td>'+data.msgs[i].date_of+'</td><td>'+data.msgs[i].query+'</td><td>'+data.msgs[i].result+'</td></tr>';
									$(html_content).insertAfter($('#LogTable tr:last'));
								}
								
								
								$("#talk_window").scrollTop(($(".talk[msg_id="+ID+"]").position().top-75));
								break;
							
							
						}
					
						
				}, 'json');
	    }
	});
	

	
	$('#delAllAlerts').die('click');
	$('#delAllAlerts').live('click',function(){
		var marker = encodeURIComponent($('#GuardMarker').val());
		var xhr = $.post(BDIR + 'query/admin',
				"DelAllAlerts=1&marker="+marker,
				function(data) {
			ShowDialogBox("Wyczyszczono log", "INFO");
					$('#AlertTab tbody').empty();
				}, 'json');
	});

	
	$(".LogsTab[tab=Logs_Guard]").trigger('click');
	
	$('#Logs_Guard').scroll(function() {
	    var pos = $(this).scrollTop();
	    var height = $(this)[0].scrollTopMax;
	    var total = height-pos;
	    
	    var ID=$('#AlertTab tr:last td:nth-child(3)').text();


	    if (total == 0) { 
	    	var xhr = $.post(BDIR + 'query/admin',
					"LOAD_ALERTS="+ID,
					function(data) {
						switch(data.result){
							case 'ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'INTERNAL_ERROR':
								ShowDialogBox(data.msg, "BAD");
								break;
								
							case 'SUCCESS':
								for (var i=0; i<data.msg_count; i++){
									var html_content='<tr><td>'+data.msgs[i].ID+'</td> <td>'+data.msgs[i].MSG+'</td><td>'+data.msgs[i].datef+'</td><td>'+data.msgs[i].MODULE+'</td>'+
									'<td>'+data.msgs[i].IP_ADDR+'</td><td>'+data.msgs[i].UserNick+'</td>'+
									'</tr>';
									$(html_content).insertAfter($('#AlertTab tr:last'));
								}
								
								
								break;
							
							
						}
					
						
				}, 'json');
	    }
	});
	
	$('.logs_btn_show_data').live('click',function(){
		 if (oDataViewer.FromString(decodeURI($(this).attr('data'))))
		 	oDataViewer.Show();
	});
});
