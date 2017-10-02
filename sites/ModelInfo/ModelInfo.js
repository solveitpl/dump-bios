$(document).ready(function(){
	$('.edit_btn').click(function(){
		if (USER.CheckPerm(PERM.USER))
			$frm = $(this).closest('form').submit();
		else
			ShowDialogBox("You need to login before edit this content", "BAD");
	});
	
	$('.save_btn').click(function(){
		$frm = $(this).closest('form').submit();
	});
	
	$('#GPUTypeSel').change(function(){
		var ThisVal = parseInt($(this).val());
		
		if (ThisVal==0) $('[HideWhenGPUIntegrated]').fadeOut();
		else $('[HideWhenGPUIntegrated]').fadeIn();
	})
	
	$('.CheckRevisions').click(function(){
		$(".CurrentData").hide();
		$(".RevisionsBlock").show();
	});
	
	$('#CloseRevList').click(function(){
		$(".CurrentData").show();
		$(".RevisionsBlock").hide();
	});
	

});