$(document).ready(function(){

	$('body').css({
		'background-image': 'url("'+BDIR+'images/background.jpg")',
		'background-size': 'contain'
	});

	$(".scroll_down").click(function(){
		$content = $(this).prev();
	
		$content.animate({ scrollTop: $content.prop("scrollTop")+40}, 500);
	});
});