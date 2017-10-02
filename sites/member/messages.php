<?php
if ((!IsLogin()) || ($User->CheckPerm(USER)==false))
{
	_die("Dostęp wzbroniony");
	StrangeEvent("Nieuprawniony dostęp","MESSAGES");
}	

$recipient = oUser::withName(htmlspecialchars($ARG[1]));
if ($recipient==BAD_LOGIN_DATA)
{
	_die("Niepoprawny strumień wejścia");
	StrangeEvent("Nie znaleziono takiego odbiorcy","MESSAGES");
}
?>
<div class="messages_container">
<a id="page_content"></a>
		<input type='hidden' id='user_name' value="<?= $ARG[1] ?>">
        <div class="messages-header">
		<h3>Message to <?= $ARG[1] ?><div id='case_nbr' style='float:right;'></div></h3>
        </div>
		<div id='talk_window'>
		<div id='msg_loading_bar'><img alt="Ładowanie..." src="<?= BDIR ?>images/loading2.gif"></div>
			<?php
				$sql = dbquery("SELECT * FROM (
									SELECT Messages.*, t1.Nick AS AuthorNick FROM Messages 
									INNER JOIN Users AS t1 ON Messages.UserID=t1.ID
									WHERE
									(UserID=".$User->ID()." AND RecipientID=".$recipient->ID().") OR 
									(UserID=".$recipient->ID()." AND RecipientID=".$User->ID().") 
									ORDER BY DateOF DESC LIMIT 10) AS t1
								ORDER BY t1.DateOF ASC");
				DBquery("UPDATE Messages SET Readed = 1 WHERE RecipientID=".$User->ID." AND UserID=".$recipient->ID."");
					
					if ($sql->num_rows==0)
						echo '<p class="nothing_to_see_here">Brak historii konwersacji z tym użytkownikiem</p>';
					else
						while ($row=dbarray($sql))
						{
							if ($User->CheckID($row['UserID'])) $style='i_said_that'; else $style='he_said_that';
				?>
						<div class="talk <?= $style ?>" msg_id='<?= $row['ID'] ?>'><div class="entry_date"><?= $row['AuthorNick'].', '.$row['DateOF']?></div><?= $row['Content'] ?></div>
				<?php } 
				//	dbquery("UPDATE Messages 
				//			SET Readed=1 
				//			WHERE (UserID=".$User->ID()." AND RecipientID=".$recipient->ID().") OR 
				//			(UserID=".$recipient->ID()." AND RecipientID=".$User->ID().") ");
					?>
				
				</div>
				
				<div id='write_answer'>
					<form name="new_msg" id='new_msg' action="" METHOD="post">
					<input type="hidden" name="SEND_CHAT_MSG">
					<input type="hidden" name="msg_pointer" id="msg_pointer">
					<input type="hidden" name="send_data" value="<?= Encrypt($recipient->ID().'|'.time(NULL)) ?>">
					
					<textarea id='answer_content' name='answer_content'></textarea>
					 </form>
				</div>
				
				
			
			
			
			
			
		</div>
	
	
<script>
		$("#talk_window").scrollTop($("#talk_window")[0].scrollHeight);
</script>

