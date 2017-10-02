$(document).ready(function(){
	$('.debug_btn, .debug_content').click(function(){
		$box = $('.debug_content');
		if ($box.css('right')=='-265px')
			$box.animate({right:'0px'});
		else $box.animate({right:'-265px'});
	});
	
	$('#CloseDebugContent').click(function(){
		$('.debug_content').hide();
	});
	
});