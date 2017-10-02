$(window).ready(function(){
	
	$('#delAllLogs').die('click');
	$('#delAllLogs').live('click',function(){
		var marker = encodeURIComponent($('#LogsMarker').val());
		var xhr = $.post(BDIR + 'query/admin',
				"DelAllLogs=1&marker="+marker,
				function(data) {
			ShowDialogBox("Wyczyszczono log", "INFO");
					$('#LogTable tbody').empty();
				}, 'json');
	});
	
	$('.LogEntries').scroll(function() {
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
});