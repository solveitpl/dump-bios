<?php
?>

<input id='MarkerToken' type='hidden' value='<?= Encrypt(time(NULL)) ?>'>
<div class="user_edit_box">
	<div class='user_edit_avatar'>
		<img src="">
	</div>
	<input type=hidden value="" id="CurrUserID">
	<div class="DetailTabs">
			<div class="DetailTab active" tab='user_edit_info'><div>Dane</div></div>
			<div class="DetailTab" tab='user_articles'><div>Artykuły</div></div>
			<div class="DetailTab" tab='user_posts'><div>Posty</div></div>
			<div class="DetailTab" tab='user_files'><div>Pliki</div></div>
			<div class="DetailTab" tab='user_points'><div>Punkty</div></div>
			<div class="DetailTab" tab='user_activity'><div>Aktyw.</div></div>
			<div class="DetailTab" tab='user_searched_words'><div>Wyszukane treści.</div></div>
			
			
		</div>
	<div class="detail_tabs_container">
		<img src='<?= BDIR ?>images/loading2.gif' class='load_user_data'>
		<div class='detail_tab_container user_edit_info' style='display:block;' id='user_edit_info'>
			<div class="properties3">
				<div class="prop_name3">Nick:</div>
				<div class="prop_value3" prop='nick' editable=1>None</div>
			</div>	
		
			<div class="properties3">
				<div class="prop_name3">E-mail:</div>
				<div class="prop_value3" prop='email' editable=1>None</div>
			</div>	
			
			<div class="properties3">
				<div class="prop_name3">Miasto:</div>
				<div class="prop_value3" prop='city' editable=1></div>
			</div>
			
			<div class="properties3">
				<div class="prop_name3">Kraj:</div>
				<div class="prop_value3" prop='country' editable=1></div>
			</div>
			
			<div class="properties3">
				<div class="prop_name3">Data urodzin:</div>
				<div class="prop_value3" prop='birthday' editable=1></div>
			</div>
			
			<div class="properties3">
				<div class="prop_name3">Adres IP:</div>
				<div class="prop_value3" prop='last_ip'></div>
			</div>
			
			<div class="properties3">
				<div class="prop_name3">Data rejestracji:</div>
				<div class="prop_value3" prop='register_time'></div>
			</div>
			
			<div class="properties3">
				<div class="prop_name3">Ostatnie logowanie:</div>
				<div class="prop_value3" prop='last_login'></div>
			</div>
			
			<fieldset>
				<legend>Poziom uprawnień</legend>
				<div class='radio_class'>
					<input type="radio" prop="perm_user" name="user_perm" value=5 id="perm_user" class="radio" />
					<label for="perm_user">Użytkownik</label>
				</div>
				
				<div class='radio_class'>
					<input type="radio" prop="perm_user" name="user_perm" value=10 id="perm_mod" class="radio" />
					<label for="perm_mod">Moderator</label>
				</div>
				
				<div class='radio_class'>
					<input type="radio" prop="perm_user" name="user_perm" value=15 id="perm_admin" class="radio" />
					<label for="perm_admin">Administrator</label>
				</div>
	
			</fieldset>
			
			<fieldset>
				<legend>Status</legend>
				
				<div class='radio_class'>
					<input type="radio" prop="user_status" name="user_status" value=-1 id="status_banned" class="radio"/>
					<label for="status_banned">Zablokowany</label>
				</div>
				
				<div class='radio_class'>
					<input type="radio" prop="user_status" name="user_status" value=0 id="status_notconfirmd" class="radio" />
					<label for="status_notconfirmd">Niepotwierdzony</label>
				</div>
				
				<div class='radio_class'>
					<input type="radio" prop="user_status" name="user_status" value=1 id="status_notactive" class="radio" />
					<label for="status_notactive">Nieaktywny</label>
				</div>
				
				<div class='radio_class'>
					<input type="radio" prop="user_status" name="user_status" value=2 id="status_active" class="radio" />
					<label for="status_active">Aktywny</label>
				</div>
				
			</fieldset>
			
			<fieldset>
				<legend>Usuwanie</legend>
				<div class='radio_class'>
					<input type="checkbox" name="del_user" id="del_user_check" class='radio'/>
					<label for="del_user_check">Do usunięcia</label>
				</div>
				
				
				<input type="button" marker='<?= Encrypt(time(NULL)) ?>' id="DelUserBtn" value="Usuń">
			</fieldset>
		
		</div>
		
		<div class='detail_tab_container' id='user_articles'>
			Artykuły
		</div>
		
		<div class='detail_tab_container' id='user_files'>
			Pliki
		</div>
		
		<div class='detail_tab_container' id='user_posts'>
			Posty
		</div>
		
		
		<div class='detail_tab_container' id='user_points'>
			<div class='points_data'></div>
			
			<div class='MenagePoint'>
				<div class="properties2">
					<div class="prop_name2">Dostępnych punktów:</div>
					<div class="prop_value2" prop="aval_points"></div>
				</div>		
				
				<div class="properties2">
					<div class="prop_name2">Zdobytych:</div>
					<div class="prop_value2" prop="EarnPoints"></div>
				</div>
				
				<div class="properties2">
					<div class="prop_name2">Wykorzystanych:</div>
					<div class="prop_value2" prop="SpendPoints"></div>
				</div>
				
				<div class="GivePoints">
					<input type="text" id='PointToGive'> pkt. 
					<input type="button" value="Przydziel"><br>
					Komentarz:<br>
					<textarea rows=""  id='GivePointsComm' cols=""></textarea>
				</div>
				
				 
				
				
				
			</div>
			
			
			
		</div>
		
		<div class='detail_tab_container' id='user_activity'>
			Aktywności
		</div>
		
		<div class='detail_tab_container' id='user_searched_words'>
			Wyszukiwane słowa
		</div>
		
	</div>
	
</div>

<div class="user_list">
	<form id='search_user' method="post">
		<table>
		  <thead>
		
		    <tr class='smooth search_row'>
			      <th><input type=text class='search' name="Nick" field="Nick"></th>
			      <th><input type=text class='search' name="Email" field="Email"></th>
			   	  <th>
			   	  	<select name="status">
			   	  		<option value='100' selected>Wszyscy</option>
			   	  		<option value='<?= USER_BANNED ?>'>Zablokowani</option>
			   	  		<option value='<?= USER_NOT_CONFIRMD ?>'>Niezwryfikowani</option>
			   	  		<option value='<?= USER_NOT_ACTIVATED ?>'>Niezaakceptowani</option>
			   	  		<option value='<?= USER_ACTIVE ?>'>Aktywni</option>
			   	  
			   	  	</select>
			   	  </th>
			      <th>
			      	<input type=text type2nd='date' class='search' name="reg_date_from" field="reg_date_from">
			      	-
			      	<input type=text type2nd='date' class='search' name="reg_date_to" field="reg_date_to">
			      </th>
			      <th><input type=text class='search' name="city" field="city"></th>
			      <th>
			      	<input type=text class='search' type2nd='date' name="last_login_from" field="last_login_from">
			      	-
			      	<input type=text class='search' type2nd='date' name="last_login_to" field="last_login_to">
			      </th>
			      <th></th>
		     
		    </tr>
		    <tr>
		      <th style="width:100px;">Nick</th>
		      <th>e-mail</th>
		      <th>Status</th>
		      <th>registration</th>
		      <th>city</th>
		      <th>last login</th>
		      <th>points</th>
		      
		    </tr>
		 
		  </thead>
		  <tbody>
		  <?php 
		
		  $sql = DBquery("SELECT * FROM Users WHERE ID > 0");
		  while($row=DBarray($sql)){
		  	
		  $_User = new oUser($row);
		  $_User->Points->GetTotalPoint()
		  ?>
		    <tr user_id='<?= $_User->ID() ?>'>
		      <td><strong><?= $_User->UserNick(); ?></strong></td>
		      <td><?= $_User->Email() ?></td>
		      <td><?= $USER_STATUS[$_User->Status()] ?></td>
		      <td><?= $_User->RegisterDate() ?></td>
		      <td><?= $_User->City() ?></td>
		      <td><?= $_User->LastVisit() ?></td>
		      <td><?= $_User->Points->TotalPoints() ?></td>
		      
		    </tr>
		   <?php 
		  }
		   ?>
			
		  </tbody>
		</table>
	</form>
</div>