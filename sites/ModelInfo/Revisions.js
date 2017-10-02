$(window).ready(function(){
	$('#DeleteChecked').click(function(){
		var  ToDelete = $('#rev_list').serialize();
		
		var xhr;
		xhr = $.post(BDIR + 'query/InfoModel',
				'DeleteRevItem=1&'+ToDelete,
				function(data) {
					switch(data.result){
					case "ACCESS_DENIED": ShowDialogBox("ACCESS DENIED", "INFO");break;
					case "SUCCESS": 					
						data.items.forEach(function(item){
							$('tr[rev_id='+item+']').fadeOut();
						});
						console.log(data);
						break;
					
					case "INTERNAL_ERROR": ShowDialogBox("ERROR", "BAD");break;
					
					default:
					  ShowDialogBox("Nieznany błąd. "+data.msg, "BAD");
					}
					
					$('.loading_img').fadeOut();
				
				}, 'json');
		
		
	});
	
	$('#DeleteAllRev').click(function(){
		var  ToDelete = $('#rev_list').serialize();
		
		var xhr;
		xhr = $.post(BDIR + 'query/InfoModel',
				'DeleteAllItems=1&'+ToDelete,
				function(data) {
					switch(data.result){
					case "ACCESS_DENIED": ShowDialogBox("ACCESS DENIED", "INFO");break;
					case "SUCCESS": 					
						$('tr[rev_id]').fadeOut();
						break;
					
					case "INTERNAL_ERROR": ShowDialogBox("ERROR", "BAD");break;
					
					default:
					  ShowDialogBox("Unknown error... "+data.msg, "BAD");
					}
					
					$('.loading_img').fadeOut();
				
				}, 'json');
	});
	
	$('.rev_accept_btn').click(function(){
		if (!confirm("Apply this revision as official ?")) return 0;
		var RevID = $(this).closest('tr').attr('rev_id');
		var marker = encodeURIComponent($('#marker').val());
		var xhr;
		var $this = $(this);
		xhr = $.post(BDIR + 'query/InfoModel',
				'AcceptItem='+RevID+'&marker='+marker,
				function(data) {
					switch(data.result){
					case "ACCESS_DENIED": ShowDialogBox("ACCESS DENIED", "INFO");break;
					case "SUCCESS": 					
						$this.closest('tr').fadeOut();
						location.reload();
						break;
					
					case "INTERNAL_ERROR": ShowDialogBox("ERROR", "BAD");break;
					
					default:
					  ShowDialogBox("Unknown error... "+data.msg, "BAD");
					}
					
					$('.loading_img').fadeOut();
				
				}, 'json');
		
	});
	
	
	
});