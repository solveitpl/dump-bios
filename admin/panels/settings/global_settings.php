<?php
if (!IsAdmin()) die();


$sql = DBquery("SELECT * FROM Settings");

while ($row = DBarray($sql))
	$SETT[$row['NAME']] = $row['VALUE'];

include SITES.'browser/header.php';

?>

<input id='MarkerToken' type='hidden' value='<?= Encrypt(time(NULL)) ?>'>
<div class="settings_edit_box">
	<div class="SettingsTabs">
			<div class="SettingTab active" tab='settings_main'><div>MAIN</div></div>
			<div class="SettingTab" tab='settings_contents'><div>CONTENT</div></div>
			<div class="SettingTab" tab='settings_points'><div>POINTS</div></div>
			<div class="SettingTab" tab='settings_ads'><div>ADS</div></div>
			<div class="SettingTab" tab='settings_terms'><div>RULES</div></div>
			<div class="SettingTab" tab='settings_user'><div>USERS</div></div>
			
	</div>
	
	<div class="setting_tabs_container_box">
		<img src='<?= BDIR ?>images/loading2.gif' class='load_setting_data'>
		
		
		<!-- ############## MAIN PANEL ############## -->	
		<div class='setting_tab_container' id='settings_main'>
			<div class="SettProp">
				<div class="prop_name">Długość życia tokena[s]</div>
				<div class="prop_value" prop='TOKEN_LIVE_TIME' editable=1><?= $SETT['TOKEN_LIVE_TIME'] ?></div>
				<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
			</div>
			
			<div class="SettProp">
				<div class="prop_name">Limit groźnych zachowań</div>
				<div class="prop_value" prop='SUSPICIOUS_LIMIT' editable=1><?= $SETT['SUSPICIOUS_LIMIT'] ?></div>
				<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
			</div>
			
			<div class="SettProp txtarea">
				<div class="prop_name">Email black list</div><img src="<?= IMAGES ?>ok.png" class="save_sett">
				<div class="prop_tip">Każdy wpis w nowej linii. Przykład: <i>john@microsoft.com</i> lub <i>*@wp.pl</i></div>
				<textarea rows="" cols="" prop='EMAILS_BLACKLIST' mode="ENTER_SPACING"><?= $SETT['EMAILS_BLACKLIST'] ?></textarea>
				
			</div>
			
			<div class="SettProp txtarea">
				<div class="prop_name">Words blacklist</div><img src="<?= IMAGES ?>ok.png" class="save_sett">
				<div class="prop_tip">Słowa rozdzielone średnikami. Przykład: <i>dupa;kupa;</i></div>
				<textarea rows="" cols="" prop='WORDS_BLACKLIST'><?= $SETT['WORDS_BLACKLIST'] ?></textarea>
				
			</div>
			
			<div class="SettProp">
				<div class="prop_name">Zamiennik dla words blacklist</div>
				<div class="prop_value" prop='WORDS_BLACKLIST_KEYWORD' editable=1><?= $SETT['WORDS_BLACKLIST_KEYWORD'] ?></div>
				<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
			</div>
			
				
		
		</div>
		
	
		<!-- ############## CONTENTS PANEL ############## -->		
		<div class='setting_tab_container' style="display:block;" id='settings_contents'>
			<fieldset class='settings_set_fieldset'>
				<legend>ARTICLES</legend>
				<div class="SettProp">
					<div class="prop_name">Próg punktowy do weryfikacji</div>
					<div class="prop_value" prop='ART_VERIFICATED_POINTS' editable=1><?= $SETT['ART_VERIFICATED_POINTS'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Próg punktowy do usunięcia</div>
					<div class="prop_value" prop='ART_TO_TRASH_VOICES' editable=1><?= $SETT['ART_TO_TRASH_VOICES'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Punkty za zwer. art.</div>
					<div class="prop_value" prop='ART_PRIZE_FOR_VERIFICATION' editable=1><?= $SETT['ART_PRIZE_FOR_VERIFICATION'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Domyślny status</div>
					<div class="prop_value" prop='ART_DEFAULT_STATUS' editable=1><?= $SETT['ART_DEFAULT_STATUS'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
			</fieldset>
			
			<fieldset class='settings_set_fieldset'>
				<legend>POSTS</legend>
				<div class="SettProp">
					<div class="prop_name">Próg punktowy do weryfikacji</div>
					<div class="prop_value" prop='POST_VERIFICATED_POINTS' editable=1><?= $SETT['POST_VERIFICATED_POINTS'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Próg punktowy do usunięcia</div>
					<div class="prop_value" prop='POST_TO_TRASH_POINTS' editable=1><?= $SETT['POST_TO_TRASH_POINTS'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Punkty za zwer. post</div>
					<div class="prop_value" prop='POST_PRIZE_FOR_VERIFICATION' editable=1><?= $SETT['POST_PRIZE_FOR_VERIFICATION'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Domyślny status</div>
					<div class="prop_value" prop='POST_DEFAULT_STATUS' editable=1><?= $SETT['POST_DEFAULT_STATUS'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Ilość plików w poście</div>
					<div class="prop_value" prop='MAX_FILES_IN_POST' editable=1><?= $SETT['MAX_FILES_IN_POST'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Maks. rozmiar pliku [MB]</div>
					<div class="prop_value" prop='POST_MAX_FILE_WEIGHT' editable=1><?= $SETT['POST_MAX_FILE_WEIGHT'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
			</fieldset>
			
			<fieldset class='settings_set_fieldset'>
				<legend>SOFTWARE</legend>
				<div class="SettProp">
					<div class="prop_name">Próg punktowy do weryfikacji</div>
					<div class="prop_value" prop='DOWNLOAD_POINT_TO_VERIFI' editable=1><?= $SETT['DOWNLOAD_POINT_TO_VERIFI'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Próg punktowy do usunięcia</div>
					<div class="prop_value" prop='DOWNLOAD_POINT_TO_DELETE'' editable=1><?= $SETT['DOWNLOAD_POINT_TO_DELETE'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Punkty ze zwer. plik</div>
					<div class="prop_value" prop='DOWNLOAD_PRIZE_FOR_VERIFICATION' editable=1><?= $SETT['DOWNLOAD_PRIZE_FOR_VERIFICATION'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Domyślny status</div>
					<div class="prop_value" prop='DOWNLOAD_DEFAULT_STATUS' editable=1><?= $SETT['DOWNLOAD_DEFAULT_STATUS'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
				<div class="SettProp">
					<div class="prop_name">Maks. rozmiar pliku [MB]</div>
					<div class="prop_value" prop='DOWNLOAD_MAX_FILESIZE' editable=1><?= $SETT['DOWNLOAD_MAX_FILESIZE'] ?></div>
					<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
				</div>
				
			</fieldset>
			
		</div>
		
			
		<!-- ############## POINTS PANEL ############## -->	
		<div class='setting_tab_container' id='settings_points'>
			<div class="SettProp">
				<div class="prop_name">Dzienny limit</div>
				<div class="prop_value" prop='NORMAL_POINT' editable=1><?= $SETT['NORMAL_POINT'] ?></div>
				<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
			</div>
			
			<div class="SettProp">
				<div class="prop_name">Dzienny limit [+]</div>
				<div class="prop_value" prop='NORMAL_POINT_PLUS' editable=1><?= $SETT['NORMAL_POINT_PLUS'] ?></div>
				<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
			</div>
			
			<div class="SettProp">
				<div class="prop_name">Punkty za donate [pkt/EUR]</div>
				<div class="prop_value" prop='POINT_PER_EUR' editable=1><?= $SETT['POINT_PER_EUR'] ?></div>
				<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
			</div>
			
			
		</div>
		
		<!-- ############## ADS PANEL ############## -->	
		<div class='setting_tab_container' id='settings_ads'>
			<div class="SettProp txtarea">
				<div class="prop_name">Wymiary reklam na stronie</div><img src="<?= IMAGES ?>ok.png" class="save_sett">
				<div class="prop_tip">Rozdzielone średnikami bez spacji. Przykład: <i>[X_size]</i><b>x</b><i>[Y_size]</i></div>
				<textarea rows="" cols="" prop='ADS_SIZE_XY'><?= $SETT['ADS_SIZE_XY'] ?></textarea>
				
			</div>
			
			<div class="SettProp">
			<div class="prop_name">Abonament [EUR/mies.]</div>
			<div class="prop_value" prop='ADS_MONTH_SUBS' editable=1><?= $SETT['ADS_MONTH_SUBS'] ?></div>
			<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
		</div>
			
		</div>
		
			<!-- ############## Regulamin ############## -->	
		<div class='setting_tab_container' id='settings_terms'>
			<textarea style="width:90%;height:300px" id="TermsEdit" prop="TermsAndConditions"><?= $SETT['TermsAndConditions'] ?></textarea>
		</div>
		
		<!-- ############## USER SETTINGS ############## -->	
		<div class='setting_tab_container' id='settings_user'>
			<div class="SettProp">
				<div class="prop_name">Maks. nieaktywność [dni]</div>
				<div class="prop_value" prop='USER_MAX_ABSENT_TIME' editable=1><?= $SETT['USER_MAX_ABSENT_TIME'] ?></div>
				<img src="<?= IMAGES ?>edit_icon.png" class="edit_sett">
			</div>
			
			
		</div>
		
			
		
		
	</div>
	
</div>

