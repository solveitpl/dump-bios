var MessagesList; // Zmienna stanowiąca bufor dla wiadomości
var CurrentMessage=0; //aktualnie wyświetlana wiadomość

// wywoływanie okna dialogowego. Str oznacza tekst, Type - rodzaj grafiki okna. Może być BAD, GOOD, INFO, WARNING
	function ShowDialogBox(str, type, MsgID){
		type = type || "WARNING";
		MsgID = MsgID || "";
		
		$("#MsgID").val(MsgID);
		var IMG = 'info.jpg';
		switch (type){
		case "INFO":
			IMG = 'info.jpg';
			break;
			
		case "BAD":
			IMG = 'error.jpeg';
			break;
			
		case "GOOD":
			IMG = 'ok.png';
			break;
			
		case "WARNING":
			IMG = 'warning.png';
			break;
		}
		
		$(".dialog_box_img img").attr("src", BDIR+'images/'+IMG);
		$(".dialog_box_text").html(str);
		$(".dialog_box_container").fadeIn();
	}

function first(obj) {
    for (var a in obj) return a;
}

$(document).ready(function(){
	
	$("#CloseDialogBox").click(function(){
		if ($("#MsgID").val()!='')
			$.post(BDIR + 'query','ACK_MSG=' + $("#MsgID").val()); // Kasowanie komunikatu z listy
		$(".dialog_box_container").fadeOut();
		// sprawdzenie czy nie zalega jakaś wiadomość do wyświetlenia
		var AllMsg = ObjectLength_Modern(MessagesList);
		if (AllMsg>0)
			setTimeout(function(){
				var a = first(MessagesList);
				// console.log(a);
				$('.dialog_box_msg_counter').text(AllMsg+' message(s)');
				if (AllMsg>3) $("#CloseDialogBoxWithDismiss").show();
				else $("#CloseDialogBoxWithDismiss").hide();
				ShowDialogBox(MessagesList[a].Msg, MessagesList[a].Type, MessagesList[a].Time);		
				delete MessagesList[a];
			},400);
	});
	
	// ACK all message
	$("#CloseDialogBoxWithDismiss").click(function(){
		
		$.post(BDIR + 'query','ACK_MSG=ALL'); // ACK ALL MESSAGES
		delete MessagesList;
		
		$(".dialog_box_container").fadeOut();
	
		
	});
	
	// Get info about system messages
	var xhr;
	
	xhr = $.post(BDIR + 'query',
			'GET_MSG=' + $(this).val(),
			function(data) {
				MessagesList = data;
				var CItems = ObjectLength_Modern(data);
			
				if (CItems>0)
					{
					CurrentMessage = CItems-1;
					var a = first(MessagesList);
					$('.dialog_box_msg_counter').text(CItems+' message(s)');
					
					if (CItems>3) $("#CloseDialogBoxWithDismiss").show();
					else $("#CloseDialogBoxWithDismiss").hide();
					
					ShowDialogBox(MessagesList[a].Msg, MessagesList[a].Type, MessagesList[a].Time);	
					delete MessagesList[a];
					// console.log(a);
					
					}
					
			}, 'json');

});