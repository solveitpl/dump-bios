$def_mod_str = 'basic';
var dialog;
$(document).ready(function(){
    $('.btn').prepend('<div class="hover"><span></span><span></span><span></span><span></span><span></span></div>');

    $('.social-btn').prepend('<div class="hover"><span></span><span></span><span></span><span></span></div>');
	
	dialog = $( "#dialog-confirm" ).dialog({
        resizable: false,
        height: "auto",
        autoOpen: false,
        width: 400,
        modal: true,
        buttons: {
          "Delete all items": function() {
            $( this ).dialog( "close" );
          },
          Cancel: function() {
            $( this ).dialog( "close" );
          }
        }
      });
	
	$('.AdminTab').click(function(){
		$('.AdminTab').removeClass('active');
		$(this).addClass('active');
		$tab_name = $(this).attr('tab');
		$tab = $('.tab_container');
		$tab.empty();
		$loading = $('.loading_coat');
		$loading.fadeIn('fast');
		
		xhr = $.post(BDIR + 'query/admin',
				"GetTab="+$tab_name,
				function(data) {
					$def_mod = $def_mod_str.split(',');
					// jeśli styl dla tego panelu nie istnieje to go ładujemy
					$tab.html(data.html);
					// ładujemy zawartość tab
					if (!$('style[panel='+$tab_name+']').length)
						$('head').append( $('<style panel="'+$tab_name+'" type="text/css" />').text(data.css) );
					
					if (!in_array($tab_name, $def_mod))
						{
						$def_mod_str += ','+$tab_name;
						$('head').append( $('<script id="js_'+$tab_name+'" type="text/javascript" />').text(data.js) );
						}
					
					$loading.fadeOut();
				}, 'json');
		
		
		
	});


$('.AdminTab[tab=menu]').click();


});