$(document).ready(function(){
		$('.deleteComm').click(function(){
			if (confirm('Usunąć ?'))
				$(this).parent('form').submit();
		});
});