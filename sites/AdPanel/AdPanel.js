$(document).ready(function(){
	$('#NewAdBtn').click(function(){
			if (USER.CheckPerm(PERM.USER)){
				$(this).closest('form').submit();
			}
			else
				ShowDialogBox("Pliki mogą dodawać jedynie zalogowani użytkownicy...", "BAD");
		});
	
	$('#_Mode').change(function(){
		var val = $(this).val();
		$('div[_selVisible]').hide();
		$('div[_selVisible="'+val+'"]').fadeIn();
		
	});
	
	$('#time_limited').datepicker({ 
        inline: true,
        dateFormat: 'yy-mm-dd',
        onSelect: function(dateText) {
        	
        	$('.user_list thead input').first().trigger('keyup');
           }
    });
	
	$('.EditAdBtn').click(function(){
		window.location.href = BDIR + 'AdsPanel/Edit/item/'+$(this).attr('item_id');
	});
	
	$('.DelForm').submit(function(e){
		if (!confirm("Czy usunąć trwale reklamę ?")) return false;
	});
	
});
	