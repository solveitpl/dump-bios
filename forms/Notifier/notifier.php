<?php
if (IsLogin())
{
	$sql = DBquery("SELECT Messages.*, t2.Him, Users.Nick As Nick, Users.Avatar as Avatar FROM Messages
					INNER JOIN (
						SELECT 
							MAX(DateOF) AS MaxDate, LEAST(UserID, RecipientID) AS User1,
							GREATEST(UserID, RecipientID) AS User2, IF(UserID=".$User->ID().", RecipientID, UserID) AS Him 
						FROM Messages GROUP BY User1, User2) AS t2 ON Messages.DateOF = t2.MaxDate 
					INNER JOIN Users ON Users.ID = Him
					WHERE UserID=".$User->ID()." OR RecipientID=".$User->ID()." GROUP BY User1, User2 ORDER BY `DateOF` DESC");
			
			
}


?>
<div class="notify_msg" id="MailBox">
	<input type="hidden" id="last_message_date" value="2016-10-19-00:01:03">
	
		<?php
		while ($row=DBarray($sql))
		{
		?>
		<div class='notify_msg_element' check="<?= $row['Readed'] ?>" user='<?= $row['Nick'] ?>' action="Navigate" arg="member/<?= $row['Nick'] ?>/SendMessage">
            <div class='info_div'><?= $row['DateOF'] ?></div>
            <div class='img_div'><img src='<?= IMAGES.($row['Avatar']=='' ? 'user_img.jpg' : 'avatars/'.$row['Avatar']) ?>'></div>
            <div class='content_div'>
                <div class="msg_user_name"><?= $row['Nick'] ?></div>
                <div class="msg_text"><p><?= $row['Content'] ?></p></div>
            </div>
        </div>
		<?php 
		}
		?>
	
</div>

