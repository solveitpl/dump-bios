// Inicjalizacja zmiennych
var UserVote=0;
var expand_menu=0;
$(document).ready(function(){



	$('.article_min_degree').click(function(){
		window.location.href=BDIR+'Article/view/'+$(this).closest( ".article_min" ).attr('article_id');
	});
	
	$('.article_voting .vote div').click(function(){
		var article = $('#article_id').val();
		var point = $(this).attr('point');
		$this = $(this);
		var xhr;
		xhr = $.post(BDIR + 'query/article',
				'ARTICLE_VOTE=' + article
				+ '&ARTICLE_POINT=' + point,
				function(data) {
					switch(data.result){
					case "ACCESS_DENIED": ShowDialogBox("Głosować mogą jedynie zalogowani i uprawnieni użytkownicy", "INFO");break;
					case "VOTED_ALREADY": ShowDialogBox("Głos już został oddany na ten artykuł", "INFO");break;
					case "VOTED_SUCCESS": 
						ShowDialogBox("Głos został oddany", "GOOD"); 
						$('#points').text(parseInt($('#points').text())+parseInt(point));
						$('#votes').text(parseInt($('#votes').text())+1);
						$('div[point=1]').next('div').text(data.PointsGood);
						$('div[point=-1]').next('div').text(data.PointsBad);
						
						break;
					case "INTERNAL_ERROR": ShowDialogBox("Błąd wewnętrzny. Powiadomiono administratora. Przepraszamy", "BAD");break;
					default:
					  ShowDialogBox("Nieznany błąd.", "BAD");
					}
				}, 'json');
	
	
	});
	
	$('#AddArticle').click(function(){
		alert('Yo');
	});
	
	$('.votes').mouseenter(function(){
		var article = $('#article_id').val();
		$content = $(this).find('.votes_details');
		$content.html('<img src="'+BDIR+'images/loading2.gif">');
		var xhr;
		xhr = $.post(BDIR + 'query/article',
				'WHO_VOTED=' + article,
				function(data) {
					switch(data.result){
					case "SUCCESS":
						var str = '';
						for (var i=0; i<data.voters; i++)
							str += data.VOTES[i].Nick+'('+data.VOTES[i].Points+'), ';
						$content.text(str);
						break;
					case "INTERNAL_ERROR": ShowDialogBox("Internal error. Admin got sign about this. Sorry", "BAD");break;
					case "ERROR": ShowDialogBox("Error. Admin got ign about this. Sorry", "BAD");break;
					
					default:
					  ShowDialogBox("Unknown error.", "BAD");
					}
				}, 'json');
		
	});

	
	// podświetlamy przycisk głosowania który wybrał użytkownik
	
	//$("img[point="+UserVote+"]").parent('.vote').css({'background-color':'rgba(100,100,100,0.5)'});

	// Ustawianie automatycznego rozwijania menu
		if (expand_menu.length > 0){
			$("[cat_id="+expand_menu[expand_menu.length-1]+"]").attr('next-expand',expand_menu[expand_menu.length-2]);
			$("[cat_id="+expand_menu[expand_menu.length-1]+"]").data('Steps',expand_menu);
			expand_menu.splice(expand_menu.length-1,1);
			}
		
	$('#ShowCommentPanel').click(function(){
		$(this).hide();
		$('.addComent').fadeIn();
	});

	if ($("#ScrollHere").length)
		$('html, body').animate({
			scrollTop: $("#ScrollHere").offset().top
		}, 2000);


    setTimeout(function () {
        $(".FilterInputClass").css('display', 'none');
        $(".levelHolderClass ul").css('top', '35px');
    }, 0);
	
	

		
});