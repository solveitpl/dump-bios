<?php

?>


<input id='MarkerToken' type='hidden' value='<?= Encrypt(time(NULL)) ?>'>
<div class="ToDo_edit_box">
	<div class="ToDoTabs">
			<div class="ToDoTab active" tab='ToDo_art'><div>ARTICLES</div></div>
			<div class="ToDoTab" tab='ToDo_files'><div>FILES</div></div>
			<div class="ToDoTab" tab='ToDo_posts'><div>POSTS</div></div>
			<div class="ToDoTab" tab='ToDo_Info'><div>INFO</div></div>
			<div class="ToDoTab" tab='ToDo_Newsletter'><div>NEWSLETTER</div></div>
			
			
	</div>
	
	<div class="ToDo_tabs_container_box">
		<img src='<?= BDIR ?>images/loading2.gif' class='load_ToDo_data'>
		
		
		<!-- ############## Articles ############## -->	
		<div  class='ToDo_tab_container'  id='ToDo_art'>
			<?php 
			require SITES.'article/header.php';
			$TabCon =  array();
			$Headers = array(array("ID", "link", "Title", "Date", "Status", "added by", "category"));
			$sql = DBquery("SELECT Articles.ID, link, Title, AddDateTime, Articles.Status, Users.Nick AS UserName, Categories.Name AS CatName FROM Articles
							LEFT JOIN Users ON Users.ID = Articles.AuthorID 
							LEFT JOIN Categories ON Categories.ID = Articles.Category
							WHERE Articles.Status = ".ARTICLE_NEW." OR Articles.Status=".ARTICLE_REJECTED);
			while ($row=DBarray($sql)){
				$row['link'] = "<a href='".BDIR.'article/view/'.$row['link']."' target='_blank'>".$row['link']."</a>"; 
				$row['Status'] = $STATUS_LABEL[$row['Status']];

				array_push($TabCon, $row);
			}
			echo CreateTable($Headers, $TabCon);
			
			
			?>
			
		</div>
		
		<!-- ############## Files ############## -->	
		<div class='ToDo_tab_container' id='ToDo_files'>
			<?php 
			require_once SITES.'download/header.php';
			$TabCon =  array();
			$Headers = array(array("ID", "Title", "file", "Status", "Date", "User", "check"));
			$sql = DBquery("SELECT UploadedFile.ID, FileDesc, RealFileName, UploadedFile.Status, FileUploaded, Users.Nick AS UserName FROM UploadedFile
							LEFT JOIN Users ON Users.ID = UploadedFile.UploaderID 
							WHERE UploadedFile.Status = ".FILE_REJECTED." OR UploadedFile.Status=".FILE_NEW);
			while ($row=DBarray($sql)){
				$row['FileUploaded'] = date("Y-m-d H:i:s",$row['FileUploaded']); 
				$row['Status'] = $STATUS_LABEL[$row['Status']];
				$row['link'] = '<a href="'.BDIR.'downloads/item/'.$row['ID'].'" target="_blank">Zobacz</a>';
				array_push($TabCon, $row);
			}
			echo CreateTable($Headers, $TabCon);
			
			
			?>
		</div>
		
		<!-- ############## Posts ############## -->	
		<div class='ToDo_tab_container' id='ToDo_posts'>
			<?php 
			require_once SITES.'browser/header.php';
			$TabCon =  array();
			$Headers = array(array("ID", "Title", "file", "Status", "Date", "User", "check"));
			$sql = DBquery("SELECT BrowserPosts.ID, Title, Module, BrowserPosts.Status, SendTime, Users.Nick AS UserName FROM BrowserPosts
							LEFT JOIN Users ON Users.ID = BrowserPosts.UserID 
							WHERE BrowserPosts.Status = ".POST_REJECTED." OR BrowserPosts.Status=".POST_NEW);
			while ($row=DBarray($sql)){
				$row['SendTime'] = date("Y-m-d H:i:s",$row['SendTime']); 
				$row['Status'] = $STATUS_LABEL[$row['Status']];
				$row['link'] = '<a href="'.BDIR.'browser/GoTo/'.$row['ID'].'" target="_blank">Zobacz</a>';
				array_push($TabCon, $row);
			}
			echo CreateTable($Headers, $TabCon);
			
			
			?>
		</div>
		
		<!-- ############## ModelInfo ############## -->	
		<div class='ToDo_tab_container' id='ToDo_Info'>
			
			<?php 
			require_once SITES.'left_menu/functions.php';
			$TabCon =  array();
			$Headers = array(array( "changes count", "check"));
			$sql = DBquery("SELECT CategoryID, COUNT(*) AS ChangesQuan FROM ModelInfoChanges
							INNER JOIN Categories ON Categories.ID = ModelInfoChanges.CategoryID
							GROUP BY CategoryID");
			while ($row=DBarray($sql)){
				$CatLink = FindCatLinkByID($row['CategoryID']);
				$row['link'] = '<a href="'.BDIR.'info/'.$CatLink.'" target="_blank">'.$CatLink.'</a>';
				unset($row['CategoryID']);
				array_push($TabCon, $row);
			}
			echo CreateTable($Headers, $TabCon);
			
			
			?>
		</div>
		
		<!-- ############  Newsletter panel############# -->
		<?php 
		
		// Ilość użytkowników z opcją Newslettera
		$UsersCountWithN = dbarray(dbquery("SELECT COUNT(*) as u_count FROM Users WHERE WantsNewsletter=1"))['u_count'];
		
		?>
		<div style="display:block" class='ToDo_tab_container' id='ToDo_Newsletter'>
			<div class="MailEditorDiv">
				<div class="MailEBlock">
						<div class="meb_title">EMAIL TITLE</div>
					<input type="text" style="width:100%" id="newsletter_subject_field">
				</div>
				<textarea style="width:100%;height:300px" id="MailBody" prop="MailBody"></textarea>
			</div>
			
			<fieldset class="MailEditorTools">
				<legend>Narzędzia</legend>
				
				<fieldset>
					<legend>Testowe wysyłanie</legend>
					<div class="MailEBlock">
						<div class="meb_title"></div>
						<input type="text" id="email_for_testing" value="<?= $_SETTINGS['LAST_NEWS_TEST_MAIL'] ?>">
						<input type="button" id="send_test_newsletter" value="Send">
					</div>
				</fieldset>
				
				<fieldset>
					<legend>Send</legend>
					<div class="meb_info_block">
						<div class="info_title">Newsletterów</div>
						<div class="info_value"><?= $UsersCountWithN ?></div>
					</div>
					
					<div class="meb_info_block">
						<div class="info_title">Wysłanych</div>
						<div class="info_value">20</div>
					</div>
					
					<input type="button" id="send_newsletters" value="Send Newsletter">
					<fieldset>
						<legend>Progress</legend>
						<span id="current_email_sending"></span>
						<div id="progress_bar_sending_mails" class="prg_bar"><div class="prg_bar_pattern"></div></div>
					</fieldset>
				</fieldset>
				
				
			</fieldset>
			
			<fieldset class="SavedMailList">
				<legend>Zapisane wiadomości</legend>
				<!-- <img src="<?= BDIR ?>images/loading2.gif"> -->
				<table class="modern_table meb_table">
				<thead><tr><th>Title</th><th>Sended</th><th>Action</th></tr></thead>
				<tbody>
				<?php 
				$action_line = "<img acq='load_mail_s' src='".IMAGES."edit.png'><img acq='delete_mail_s' src='".IMAGES."trash_can2.png'>";
				$sql = DBquery("SELECT * FROM Newsletters ORDER BY SendDateTime DESC");
				while ($row = dbarray($sql)){
					echo "<tr mail_id='".$row['ID']."'><td>".$row['Title']."</td><td>".$row['SendDateTime']."</td><td class='meb_btn_td'>$action_line</td></tr>";
				}
				

				
				?>
				</tbody>
				</table> 
			</fieldset>
			
			
			
			
		</div>
	</div>
		
		
		
		
			
		
		
</div>
	

